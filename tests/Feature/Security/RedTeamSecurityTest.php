<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Filament\Pages\Auth\AdminLogin;
use App\Filament\Pages\TwoFactorChallengePage;
use App\Filament\Pages\TwoFactorSecurityPage;
use App\Filament\Resources\SecurityAuditLogResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\Category;
use App\Models\GalleryImage;
use App\Models\Montaje;
use App\Models\Product;
use App\Models\SecurityAuditLog;
use App\Models\SiteSetting;
use App\Models\User;
use App\Rules\ThrottledCurrentPassword;
use App\Services\Media\PublicMediaCleaner;
use App\Services\Security\QrCodeSvgGenerator;
use App\Services\Security\SafeImageSanitizer;
use App\Services\Security\TotpService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
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
     * ESCENARIO 4.2: Bloqueo estricto de Path Traversal en el directorio destino de subida de imágenes.
     */
    public function test_image_sanitizer_blocks_path_traversal_in_target_directory(): void
    {
        $im = imagecreatetruecolor(10, 10);
        $tmpFile = tempnam(sys_get_temp_dir(), 'traversal_test_').'.jpg';
        imagejpeg($im, $tmpFile);
        imagedestroy($im);

        try {
            $this->expectException(InvalidArgumentException::class);
            SafeImageSanitizer::sanitize($tmpFile, 'public', '../outside');
        } finally {
            if (file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
        }
    }

    /**
     * ESCENARIO 4.3: Redimensionamiento proporcional a máximo 1600px y eliminación limpia sin huérfanos en disco.
     */
    public function test_image_sanitizer_resizes_large_images_and_cleaner_removes_unreferenced_files(): void
    {
        Storage::fake('public');

        $im = imagecreatetruecolor(2400, 1200);
        $tmpFile = tempnam(sys_get_temp_dir(), 'resize_test_').'.jpg';
        imagejpeg($im, $tmpFile, 80);
        imagedestroy($im);

        try {
            $storedPath = SafeImageSanitizer::sanitize($tmpFile, 'public', 'galeria');
            Storage::disk('public')->assertExists($storedPath);

            $savedInfo = getimagesize(Storage::disk('public')->path($storedPath));
            $this->assertIsArray($savedInfo);
            $this->assertSame(1600, $savedInfo[0]);
            $this->assertSame(800, $savedInfo[1]);

            $galleryImage = GalleryImage::factory()->create(['image_path' => $storedPath]);
            $montaje = Montaje::query()->create([
                'title' => 'Evento Cleanup Test',
                'slug' => 'evento-cleanup-test',
                'type' => 'Evento Empresa',
                'year' => '2026',
                'cover_image' => $storedPath,
                'gallery' => [$storedPath],
                'sort_order' => 1,
                'is_active' => true,
            ]);

            // Al borrar de Galería pero seguir en el Montaje, el archivo físico debe conservarse
            $galleryImage->delete();
            Storage::disk('public')->assertExists($storedPath);

            // Al borrar también el Montaje, ya no hay referencias y debe eliminarse del disco
            $montaje->delete();
            Storage::disk('public')->assertMissing($storedPath);

            // Crear un archivo huérfano directo en disco y comprobar que pruneOrphans lo elimina
            Storage::disk('public')->put('montajes/orphan_test.webp', 'fake-orphan-bytes');
            $result = PublicMediaCleaner::pruneOrphans();
            $this->assertContains('montajes/orphan_test.webp', $result['deleted']);
            Storage::disk('public')->assertMissing('montajes/orphan_test.webp');
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
     * ESCENARIO 8.1: Un atacante NO puede evadir el 2FA falsificando la cabecera HTTP Referer.
     */
    public function test_mfa_cannot_be_bypassed_by_forged_referer_header(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $admin->forceFill([
            'two_factor_secret' => $this->totp->generateSecret(),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $forgedReferer = TwoFactorChallengePage::getUrl();

        $this->actingAs($admin)
            ->withHeader('Referer', $forgedReferer)
            ->get('/admin/products')
            ->assertRedirect(TwoFactorChallengePage::getUrl());

        $this->actingAs($admin)
            ->withHeader('Referer', $forgedReferer)
            ->get('/admin/users')
            ->assertRedirect(TwoFactorChallengePage::getUrl());
    }

    /**
     * ESCENARIO 8.2: El comando de retención purga auditorías antiguas (>180 días) preservando las recientes.
     */
    public function test_audit_log_pruner_respects_retention_window(): void
    {
        $recentLog = SecurityAuditLog::record(eventType: 'auth.login_success', severity: 'info');
        $oldLog = SecurityAuditLog::record(eventType: 'auth.legacy_event', severity: 'info');

        DB::table('security_audit_logs')
            ->where('id', $oldLog->id)
            ->update(['created_at' => now()->subDays(200)]);

        $this->artisan('security:prune-audit-logs', ['--days' => 180])
            ->assertSuccessful();

        $this->assertDatabaseHas('security_audit_logs', ['id' => $recentLog->id]);
        $this->assertDatabaseMissing('security_audit_logs', ['id' => $oldLog->id]);
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

    /**
     * ESCENARIO 10: La cuenta técnica dev@catalogo-arriendo.cl permanece oculta en UserResource
     * y cambiar contraseñas exige Modo Sudo (current_password) + confirmación.
     */
    public function test_developer_account_is_hidden_and_password_reset_requires_sudo_password(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => 'admin@arriendoseventos.cl',
            'password' => bcrypt('AdminActual2026!#'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        /** @var User $developer */
        $developer = User::factory()->create([
            'email' => 'dev@catalogo-arriendo.cl',
            'is_admin' => true,
            'is_active' => true,
        ]);
        $developer->forceFill(['is_developer' => true])->save();

        // 1. La cuenta dev@catalogo-arriendo.cl no aparece en el query del recurso ni permite acceso por URL
        $visibleEmails = UserResource::getEloquentQuery()->pluck('email')->all();
        $this->assertContains('admin@arriendoseventos.cl', $visibleEmails);
        $this->assertNotContains('dev@catalogo-arriendo.cl', $visibleEmails);

        $this->actingAs($admin)
            ->get("/admin/users/{$developer->id}/edit")
            ->assertNotFound();

        // 2. Intentar cambiar contraseña con Modo Sudo incorrecto debe fallar y no alterar el hash
        $originalHash = $admin->password;

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callTableAction('resetPassword', $admin, data: [
                'sudo_password' => 'ClaveIncorrecta99!',
                'new_password' => 'NuevaClaveFuerte2026!#',
                'new_password_confirmation' => 'NuevaClaveFuerte2026!#',
                'revoke_sessions' => true,
            ])
            ->assertHasTableActionErrors(['sudo_password']);

        $this->assertSame($originalHash, $admin->fresh()->password);

        // 3. Con Modo Sudo correcto + confirmación, actualiza la clave y registra auditoría crítica
        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callTableAction('resetPassword', $admin, data: [
                'sudo_password' => 'AdminActual2026!#',
                'new_password' => 'NuevaClaveFuerte2026!#',
                'new_password_confirmation' => 'NuevaClaveFuerte2026!#',
                'revoke_sessions' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertTrue(Hash::check('NuevaClaveFuerte2026!#', (string) $admin->fresh()->password));
        $this->assertTrue(
            SecurityAuditLog::query()
                ->where('event_type', 'user.password_reset')
                ->where('severity', 'critical')
                ->where('user_id', $admin->id)
                ->exists()
        );
    }

    /**
     * ESCENARIO 11: El generador QR TOTP produce una matriz ISO/IEC 18004 real y escaneable (no un placeholder).
     */
    public function test_totp_qr_generator_produces_valid_iso_18004_matrix_and_svg(): void
    {
        $secret = $this->totp->generateSecret();
        $otpauth = $this->totp->getOtpAuthUri('VYS Eventos', 'admin@arriendoseventos.cl', $secret);
        $matrix = QrCodeSvgGenerator::encodeMatrix($otpauth);

        $size = count($matrix);
        $this->assertGreaterThanOrEqual(33, $size);
        $this->assertSame(1, ($size - 17) % 4 === 0 ? 1 : 0);

        // Verificar los 3 patrones de búsqueda (Finder Patterns 7x7) en esquinas sup-izq, sup-der e inf-izq
        foreach ([[0, 0], [0, $size - 7], [$size - 7, 0]] as [$oy, $ox]) {
            $this->assertSame(1, $matrix[$oy][$ox]);
            $this->assertSame(1, $matrix[$oy + 3][$ox + 3]);
            $this->assertSame(0, $matrix[$oy + 1][$ox + 1]);
            $this->assertSame(1, $matrix[$oy + 6][$ox + 6]);
        }

        // Módulo oscuro fijo ISO/IEC 18004 y patrón de temporización alterno
        $this->assertSame(1, $matrix[$size - 8][8]);
        $this->assertSame(1, $matrix[6][8]);
        $this->assertSame(0, $matrix[6][9]);
        $this->assertSame(1, $matrix[6][10]);

        // Verificar Data URI SVG base64 limpio sin el viejo placeholder amarillo
        $dataUri = $this->totp->generateQrSvgDataUri('VYS Eventos', 'admin@arriendoseventos.cl', $secret);
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);
        $decodedSvg = base64_decode(substr($dataUri, strlen('data:image/svg+xml;base64,')), true);
        $this->assertIsString($decodedSvg);
        $this->assertStringContainsString('<path d="M', $decodedSvg);
        $this->assertStringNotContainsString('#f59e0b', $decodedSvg);
    }

    /**
     * ESCENARIO 12: AdminLogin bloquea fuerza bruta, cuentas suspendidas sin enumeración y audita todos los eventos.
     */
    public function test_admin_login_blocks_brute_force_suspended_accounts_and_audits_attempts(): void
    {
        $suspended = User::factory()->create([
            'email' => 'suspendido@vys.cl',
            'password' => Hash::make('ClaveValida2026!#'),
            'is_admin' => true,
            'is_active' => false,
        ]);

        // Limpiar rate limiters previos del entorno de pruebas
        $ip = '127.0.0.1';
        RateLimiter::clear('admin-login:ip:'.sha1($ip));
        RateLimiter::clear('admin-login:account:'.sha1('suspendido@vys.cl|'.$ip));
        RateLimiter::clear('admin-login:account:'.sha1('atacante@vys.cl|'.$ip));

        // 1. Cuenta suspendida con contraseña correcta: rechazada con mensaje genérico + logueada
        Livewire::test(AdminLogin::class)
            ->fillForm([
                'email' => 'suspendido@vys.cl',
                'password' => 'ClaveValida2026!#',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest();
        $this->assertTrue(
            SecurityAuditLog::query()
                ->where('event_type', 'auth.login_blocked')
                ->where('user_id', $suspended->id)
                ->exists()
        );

        // 2. Fuerza bruta: 5 intentos fallidos + 6to bloqueado por Rate Limiter con auditoría crítica
        for ($i = 1; $i <= 5; $i++) {
            Livewire::test(AdminLogin::class)
                ->fillForm([
                    'email' => 'atacante@vys.cl',
                    'password' => 'IntentoFallido'.$i.'!',
                ])
                ->call('authenticate')
                ->assertHasFormErrors(['email']);
        }

        Livewire::test(AdminLogin::class)
            ->fillForm([
                'email' => 'atacante@vys.cl',
                'password' => 'IntentoBloqueado6!',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertTrue(
            SecurityAuditLog::query()
                ->where('event_type', 'auth.rate_limit_exceeded')
                ->where('severity', 'critical')
                ->exists()
        );
    }

    /**
     * ESCENARIO 13: El asistente de activación 2FA (TwoFactorSecurityPage) valida el código TOTP
     * contra la semilla en memoria y persiste secreto, códigos de respaldo y last_totp_slice.
     */
    public function test_two_factor_security_page_can_enable_2fa_with_valid_totp_code(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'email' => 'activar2fa@vys.cl',
            'password' => Hash::make('AdminClave2026!#'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($admin)
            ->test(TwoFactorSecurityPage::class)
            ->call('startConfiguration');

        $secret = (string) $component->get('setupSecret');
        $this->assertNotEmpty($secret);

        // Calcular código TOTP válido actual para esa semilla recién generada
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

        $component
            ->set('currentPassword', 'AdminClave2026!#')
            ->set('confirmationCode', $validCode)
            ->call('confirmConfiguration')
            ->assertHasNoErrors();

        $admin->refresh();
        $this->assertTrue($admin->hasEnabledTwoFactor());
        $this->assertSame($secret, $admin->two_factor_secret);
        $this->assertSame($slice, $admin->last_totp_slice);
        $this->assertCount(8, $admin->two_factor_recovery_codes);
    }

    /**
     * ESCENARIO 14: Falsificar X-Forwarded-For desde un cliente no confiable NO evade el Rate Limiter.
     */
    public function test_spoofed_x_forwarded_for_header_cannot_bypass_rate_limiting(): void
    {
        RateLimiter::clear('contact-submission:127.0.0.1');

        for ($i = 1; $i <= 3; $i++) {
            $this->withHeaders(['X-Forwarded-For' => "203.0.113.{$i}"])
                ->postJson(route('contact.store'), [
                    'name' => 'Cliente Real',
                    'email' => "cliente{$i}@ejemplo.cl",
                    'event_address' => 'Providencia 1234',
                    'event_date' => now()->addDays(10)->format('Y-m-d'),
                    'message' => 'Consulta de disponibilidad para matrimonio en Santiago.',
                ])
                ->assertOk();
        }

        // El 4to intento con una IP falsa distinta debe ser bloqueado igual con HTTP 429
        $this->withHeaders(['X-Forwarded-For' => '198.51.100.99'])
            ->postJson(route('contact.store'), [
                'name' => 'Cliente Real',
                'email' => 'cliente4@ejemplo.cl',
                'event_address' => 'Providencia 1234',
                'event_date' => now()->addDays(10)->format('Y-m-d'),
                'message' => 'Intento extra intentando rotar X-Forwarded-For.',
            ])
            ->assertStatus(429);
    }

    /**
     * ESCENARIO 15: El Circuit Breaker de 2FA persiste aunque se reinicie la sesión tras 4 intentos fallidos.
     */
    public function test_mfa_circuit_breaker_persists_across_session_resets(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $admin->forceFill([
            'two_factor_secret' => $this->totp->generateSecret(),
            'two_factor_confirmed_at' => now(),
        ])->save();

        RateLimiter::clear('mfa-challenge:user:'.$admin->id);

        // 4 intentos fallidos en la sesión 1
        $sessionOne = Livewire::actingAs($admin)->test(TwoFactorChallengePage::class);
        for ($i = 1; $i <= 4; $i++) {
            $sessionOne->set('code', '11111'.$i)->call('submitChallenge');
        }
        $this->assertFalse($admin->fresh()->isMfaLocked());

        // Simular reinicio completo de cookies/sesión e intento #5 en una sesión nueva
        session()->flush();

        Livewire::actingAs($admin)
            ->test(TwoFactorChallengePage::class)
            ->set('code', '111115')
            ->call('submitChallenge');

        $this->assertTrue($admin->fresh()->isMfaLocked());
    }

    /**
     * ESCENARIO 16: Bloqueo global por cuenta en AdminLogin (anti-ataque distribuido) y sin cookie remember.
     */
    public function test_admin_login_enforces_global_account_throttle_and_disables_remember_me(): void
    {
        $email = 'objetivo-global@vys.cl';
        $globalKey = 'admin-login:global-account:'.sha1($email);
        $ipKey = 'admin-login:ip:'.sha1('127.0.0.1');
        $accountIpKey = 'admin-login:account:'.sha1($email.'|127.0.0.1');

        RateLimiter::clear($globalKey);
        RateLimiter::clear($ipKey);
        RateLimiter::clear($accountIpKey);

        // Simular 15 intentos previos provenientes de múltiples IPs distintas
        for ($i = 0; $i < 15; $i++) {
            RateLimiter::hit($globalKey, 900);
        }

        // Aunque la IP actual (127.0.0.1) jamás haya fallado, el bloqueo global por cuenta debe rechazarla
        Livewire::test(AdminLogin::class)
            ->fillForm([
                'email' => $email,
                'password' => 'CualquierClave2026!',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertTrue(
            SecurityAuditLog::query()
                ->where('event_type', 'auth.rate_limit_exceeded')
                ->where('severity', 'critical')
                ->exists()
        );

        RateLimiter::clear($globalKey);

        // Verificar además que un inicio de sesión exitoso no genera remember_token persistente
        $admin = User::factory()->create([
            'email' => 'sinremember@vys.cl',
            'password' => Hash::make('AdminSeguro2026!#'),
            'is_admin' => true,
            'is_active' => true,
            'remember_token' => null,
        ]);

        RateLimiter::clear('admin-login:account:'.sha1($admin->email.'|127.0.0.1'));
        RateLimiter::clear('admin-login:global-account:'.sha1($admin->email));

        Livewire::test(AdminLogin::class)
            ->fillForm([
                'email' => $admin->email,
                'password' => 'AdminSeguro2026!#',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($admin);
        $this->assertNull($admin->fresh()->remember_token);
    }

    /**
     * ESCENARIO 17: La validación de contraseña actual en acciones críticas del panel bloquea fuerza bruta.
     */
    public function test_sudo_password_checks_inside_admin_panel_are_rate_limited(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create([
            'password' => Hash::make('AdminClaveReal2026!#'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        RateLimiter::clear(ThrottledCurrentPassword::throttleKey($admin->id));

        for ($i = 1; $i <= 5; $i++) {
            Livewire::actingAs($admin)
                ->test(TwoFactorSecurityPage::class)
                ->set('disablePassword', 'ClaveErronea'.$i.'!')
                ->call('disableTwoFactor')
                ->assertHasErrors(['disablePassword']);
        }

        // Al 6to intento (incluso con la contraseña real) debe permanecer bloqueado temporalmente
        Livewire::actingAs($admin)
            ->test(TwoFactorSecurityPage::class)
            ->set('disablePassword', 'AdminClaveReal2026!#')
            ->call('disableTwoFactor')
            ->assertHasErrors(['disablePassword']);

        $this->assertTrue(
            SecurityAuditLog::query()
                ->where('event_type', 'auth.sudo_rate_limit_exceeded')
                ->where('severity', 'critical')
                ->where('user_id', $admin->id)
                ->exists()
        );
    }

    /**
     * ESCENARIO 18: Integridad de cotizaciones y telemetría (productos inactivos, IDs duplicados y honeypot).
     */
    public function test_quote_and_click_endpoints_block_inactive_items_duplicate_stock_bypass_and_bots(): void
    {
        $category = Category::factory()->create();

        $activeProduct = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mesa Imperial',
            'rental_price' => 15000,
            'stock' => 5,
            'quoted_count' => 0,
            'units_quoted_count' => 0,
            'is_active' => true,
        ]);

        $inactiveProduct = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Artículo Retirado',
            'rental_price' => 9000,
            'stock' => 20,
            'is_active' => false,
        ]);

        // 1. Producto inactivo no se puede clickear (404) ni cotizar (422)
        $this->postJson(route('catalog.track-click', $inactiveProduct))
            ->assertNotFound();

        $this->postJson(route('quote.store'), [
            'customer_name' => 'María Torres',
            'location' => 'Vitacura',
            'items' => [
                ['product_id' => $inactiveProduct->id, 'quantity' => 1],
            ],
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.product_id']);

        // 2. Enviar el mismo product_id duplicado para superar stock acumulado (4 + 4 > 5) debe fallar con 422
        $this->postJson(route('quote.store'), [
            'customer_name' => 'María Torres',
            'location' => 'Vitacura',
            'items' => [
                ['product_id' => $activeProduct->id, 'quantity' => 4],
                ['product_id' => $activeProduct->id, 'quantity' => 4],
            ],
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);

        // 3. Honeypot relleno rechaza el envío como bot (422)
        $this->postJson(route('quote.store'), [
            'website_hp' => 'https://spam-bot.example',
            'customer_name' => 'Bot Spam',
            'location' => 'Santiago',
            'items' => [
                ['product_id' => $activeProduct->id, 'quantity' => 1],
            ],
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['website_hp']);

        // 4. Duplicados válidos dentro del stock (2 + 2 <= 5) se consolidan en 1 solo incremento de quoted_count
        $this->postJson(route('quote.store'), [
            'customer_name' => 'María Torres',
            'location' => 'Vitacura',
            'items' => [
                ['product_id' => $activeProduct->id, 'quantity' => 2],
                ['product_id' => $activeProduct->id, 'quantity' => 2],
            ],
        ])->assertOk();

        $activeProduct->refresh();
        $this->assertSame(1, (int) $activeProduct->quoted_count);
        $this->assertSame(4, (int) $activeProduct->units_quoted_count);
    }
}
