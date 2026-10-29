<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Filament\Pages\TwoFactorChallengePage;
use App\Filament\Resources\SecurityAuditLogResource;
use App\Models\SecurityAuditLog;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\Security\SafeImageSanitizer;
use App\Services\Security\TotpService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Livewire\Livewire;
use Tests\TestCase;

class RedTeamSecurityTest extends TestCase
{
    use RefreshDatabase;

    private TotpService $totp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->totp = app(TotpService::class);
    }

    /**
     * ESCENARIO 1: Mitigación de TOTP Replay Attacks dentro de la misma ventana de 30s.
     */
    public function test_totp_replay_attack_is_blocked_within_same_30_second_window(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $secret = $this->totp->generateSecret();

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        // Calcular el código válido para el slice actual
        $slice = (int) floor(time() / 30);
        $timeBytes = pack('N*', 0).pack('N*', $slice);
        $secretBytes = $this->totp->base32Decode($secret);
        $hash = hash_hmac('sha1', $timeBytes, $secretBytes, true);
        $offset = ord($hash[19]) & 0x0F;
        $binary = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);
        $code = str_pad((string) ($binary % 1000000), 6, '0', STR_PAD_LEFT);

        // Primer intento legítimo: consume exitosamente el slice
        $this->assertTrue($this->totp->verifyAndConsume($user, $code));
        $this->assertEquals($slice, $user->fresh()->last_totp_slice);

        // Intento de Replay Attack con el mismo código interceptado: debe ser rechazado
        $this->assertFalse($this->totp->verifyAndConsume($user, $code));
    }

    /**
     * ESCENARIO 2: Códigos de recuperación de un solo uso con resistencia a reutilización.
     */
    public function test_recovery_code_can_only_be_used_once_and_resists_reuse(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $codes = $this->totp->generateRecoveryCodes(4);

        $user->forceFill([
            'two_factor_recovery_codes' => $codes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $codeToUse = $codes[0];

        // Primer uso: éxito
        $this->assertTrue($this->totp->consumeRecoveryCode($user, $codeToUse));

        $remaining = $user->fresh()->two_factor_recovery_codes;
        $this->assertCount(3, $remaining);
        $this->assertNotContains($codeToUse, $remaining);

        // Segundo uso del mismo código consumido: debe fallar
        $this->assertFalse($this->totp->consumeRecoveryCode($user, $codeToUse));
    }

    /**
     * ESCENARIO 3: Bloqueo de cuenta temporal por 15 minutos (Circuit Breaker) tras 5 intentos fallidos de 2FA.
     */
    public function test_mfa_brute_force_triggers_15_minute_account_lockout_and_audit_log(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
        $secret = $this->totp->generateSecret();

        $admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        // Enviar 5 códigos erróneos
        $component = Livewire::actingAs($admin)->test(TwoFactorChallengePage::class);

        for ($i = 1; $i <= 5; $i++) {
            $component->set('code', '00000'.$i)->call('submitChallenge');
        }

        $admin->refresh();
        $this->assertNotNull($admin->mfa_locked_until);
        $this->assertTrue($admin->mfa_locked_until->isFuture());
        $this->assertTrue($admin->isMfaLocked());

        // Comprobar que se registró la alerta crítica en auditoría forense
        $this->assertTrue(
            SecurityAuditLog::query()
                ->where('event_type', 'mfa.circuit_breaker_triggered')
                ->where('severity', 'critical')
                ->where('user_id', $admin->id)
                ->exists()
        );

        // Intento #6: Incluso con el código correcto, debe ser rechazado mientras dure el bloqueo
        $slice = (int) floor(time() / 30);
        $timeBytes = pack('N*', 0).pack('N*', $slice);
        $secretBytes = $this->totp->base32Decode($secret);
        $hash = hash_hmac('sha1', $timeBytes, $secretBytes, true);
        $offset = ord($hash[19]) & 0x0F;
        $binary = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);
        $validCode = str_pad((string) ($binary % 1000000), 6, '0', STR_PAD_LEFT);

        $this->assertFalse($this->totp->verifyAndConsume($admin, $validCode));
    }

    /**
     * ESCENARIO 4: Archivo políglota con payload PHP inyectado en EXIF/EOF es bloqueado o destruido.
     */
    public function test_polyglot_image_with_embedded_php_payload_is_rejected_or_stripped(): void
    {
        $im = imagecreatetruecolor(40, 40);
        $tmpFile = tempnam(sys_get_temp_dir(), 'polyglot_test_').'.jpg';
        imagejpeg($im, $tmpFile);
        imagedestroy($im);

        // Inyectar código PHP malicioso en los bytes crudos (EOF injection)
        file_put_contents($tmpFile, "\n<?php system('whoami'); ?>", FILE_APPEND);

        try {
            $this->expectException(InvalidArgumentException::class);
            SafeImageSanitizer::sanitize($tmpFile, 'public', 'test');
        } finally {
            if (file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
        }
    }

    /**
     * ESCENARIO 4.1: Control estricto de tamaño máximo de 4MB en subida de imágenes.
     */
    public function test_image_sanitizer_enforces_4mb_maximum_size_limit(): void
    {
        $im = imagecreatetruecolor(10, 10);
        $tmpFile = tempnam(sys_get_temp_dir(), 'size_test_').'.jpg';
        imagejpeg($im, $tmpFile);
        imagedestroy($im);

        // Agrandar artificialmente el archivo superando 4MB (4MB + 10KB)
        $padding = str_repeat('A', 4 * 1024 * 1024 + 10240);
        file_put_contents($tmpFile, $padding, FILE_APPEND);

        try {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage('4MB');
            SafeImageSanitizer::sanitize($tmpFile, 'public', 'test');
        } finally {
            if (file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
        }
    }

    /**
     * ESCENARIO 5: Inmutabilidad forense de SecurityAuditLog y detección de adulteración en MySQL.
     */
    public function test_security_audit_log_is_immutable_and_detects_database_tampering(): void
    {
        $log = SecurityAuditLog::record(
            eventType: 'admin.privileged_action',
            severity: 'warning',
            context: ['action' => 'export_database'],
            ipAddress: '192.168.1.50'
        );

        $this->assertTrue($log->verifyIntegrity());

        // Comprobar que update() arroja DomainException
        try {
            $log->update(['severity' => 'info']);
            $this->fail('Se esperaba DomainException al intentar actualizar un registro de auditoría.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('inmutables', $e->getMessage());
        }

        // Comprobar que delete() arroja DomainException
        try {
            $log->delete();
            $this->fail('Se esperaba DomainException al intentar eliminar un registro de auditoría.');
        } catch (DomainException $e) {
            $this->assertStringContainsString('prohibido', $e->getMessage());
        }

        // Simular a un atacante adulterando la fila directamente en MySQL sin pasar por el modelo
        DB::table('security_audit_logs')
            ->where('id', $log->id)
            ->update(['ip_address' => '10.0.0.99']);

        // El recálculo del HMAC debe fallar demostrando la adulteración
        $this->assertFalse($log->fresh()->verifyIntegrity());
    }

    /**
     * ESCENARIO 6: Asignación masiva no puede escalar a un usuario a administrador ni alterar su MFA.
     */
    public function test_mass_assignment_cannot_escalate_user_to_admin_or_alter_mfa(): void
    {
        $user = User::create([
            'name' => 'Attacker',
            'email' => 'attacker@evil.com',
            'password' => 'Secret123!456',
            'is_admin' => true, // Intento de escalada
            'two_factor_secret' => 'malicious_secret',
            'last_totp_slice' => 999999,
        ]);

        $this->assertFalse((bool) $user->is_admin);
        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->last_totp_slice);

        // Intento mediante update()
        $user->update([
            'is_admin' => true,
            'two_factor_secret' => 'compromised',
        ]);

        $user->refresh();
        $this->assertFalse((bool) $user->is_admin);
        $this->assertNull($user->two_factor_secret);
    }

    /**
     * ESCENARIO 7: Bloqueo de inyecciones de saltos de línea (\n) y URLs javascript: en el mapa iframe.
     */
    public function test_site_setting_blocks_newline_bypass_and_javascript_uri_in_map_iframe(): void
    {
        $setting = SiteSetting::current();

        $this->expectException(ValidationException::class);

        // Intento de evasión con salto de línea y protocolo peligroso
        $setting->map_embed_url = "https://www.google.com/maps/embed?pb=123\njavascript:alert(document.cookie)";
        $setting->save();
    }

    /**
     * ESCENARIO 8: Administrador con 2FA sin verificar no puede acceder directamente a recursos de Filament.
     */
    public function test_unverified_mfa_admin_cannot_access_filament_resources_directly(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
        $secret = $this->totp->generateSecret();

        $admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        // Sin sesión verificada de MFA, intentar acceder a recursos
        $responseProducts = $this->actingAs($admin)->get('/admin/products');
        $responseProducts->assertRedirect(TwoFactorChallengePage::getUrl());

        $responseUsers = $this->actingAs($admin)->get('/admin/users');
        $responseUsers->assertRedirect(TwoFactorChallengePage::getUrl());
    }

    /**
     * ESCENARIO 9: El desarrollador ve la etiqueta 'Auditoría' mientras los demás ven 'Auditoría Forense & Logs'.
     */
    public function test_developer_sees_auditoria_label_while_others_see_auditoria_forense_and_logs(): void
    {
        /** @var User $developer */
        $developer = User::factory()->create([
            'email' => 'dev@catalogo-arriendo.cl',
            'is_admin' => true,
            'is_active' => true,
        ]);
        $developer->forceFill(['is_developer' => true])->save();

        /** @var User $standardAdmin */
        $standardAdmin = User::factory()->create([
            'email' => 'admin@arriendoseventos.cl',
            'is_admin' => true,
            'is_active' => true,
        ]);
        $standardAdmin->forceFill(['is_developer' => false])->save();

        // Autenticado como desarrollador
        $this->actingAs($developer);
        $this->assertTrue(SecurityAuditLogResource::canAccess());
        $this->assertTrue(SecurityAuditLogResource::shouldRegisterNavigation());
        $this->assertSame('Auditoría', SecurityAuditLogResource::getNavigationLabel());
        $this->assertSame('Auditoría', SecurityAuditLogResource::getPluralModelLabel());

        // Autenticado como administrador estándar (no desarrollador)
        $this->actingAs($standardAdmin);
        $this->assertFalse(SecurityAuditLogResource::canAccess());
        $this->assertFalse(SecurityAuditLogResource::shouldRegisterNavigation());
        $this->assertSame('Auditoría Forense & Logs', SecurityAuditLogResource::getNavigationLabel());
        $this->assertSame('Auditoría Forense & Logs', SecurityAuditLogResource::getPluralModelLabel());
    }
}
