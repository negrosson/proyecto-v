<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Filament\Pages\Auth\AdminRequestPasswordReset;
use App\Filament\Pages\Auth\AdminResetPassword;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\AdminResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetAndSocialLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_displays_password_reset_link(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('¿Olvidaste tu contraseña?');
        $response->assertSee('/admin/password-reset/request');
    }

    public function test_request_password_reset_sends_spanish_notification_only_to_active_admin_without_enumerating(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create([
            'email' => 'jefe@arriendomobiliario.cl',
        ]);

        // 1. Admin válido recibe el correo en español y se audita
        Livewire::test(AdminRequestPasswordReset::class)
            ->fillForm(['email' => $admin->email])
            ->call('request')
            ->assertNotified('Solicitud procesada');

        Notification::assertSentTo($admin, AdminResetPasswordNotification::class, function (AdminResetPasswordNotification $notification) use ($admin): bool {
            $mail = $notification->toMail($admin);

            return str_contains((string) $mail->subject, 'Restablecer contraseña')
                && str_contains($notification->url, '/admin/password-reset/reset');
        });

        $this->assertDatabaseHas('security_audit_logs', [
            'event_type' => 'auth.password_reset_link_sent',
            'user_id' => $admin->id,
        ]);

        // 2. Correo inexistente muestra exactamente la misma respuesta neutra (anti-enumeración)
        Livewire::test(AdminRequestPasswordReset::class)
            ->fillForm(['email' => 'no-existe@ejemplo.cl'])
            ->call('request')
            ->assertNotified('Solicitud procesada');
    }

    public function test_admin_can_complete_password_reset_with_strong_password_and_audit_log(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'reset@arriendomobiliario.cl',
            'password' => Hash::make('ClaveAntigua123!'),
        ]);

        $token = Password::broker()->createToken($admin);

        Livewire::test(AdminResetPassword::class, [
            'email' => $admin->email,
            'token' => $token,
        ])
            ->fillForm([
                'password' => 'NuevaClaveSegura2026!',
                'passwordConfirmation' => 'NuevaClaveSegura2026!',
            ])
            ->call('resetPassword')
            ->assertNotified('Contraseña actualizada');

        $admin->refresh();
        $this->assertTrue(Hash::check('NuevaClaveSegura2026!', $admin->password));

        $this->assertDatabaseHas('security_audit_logs', [
            'event_type' => 'auth.password_reset_completed',
            'user_id' => $admin->id,
        ]);
    }

    public function test_footer_and_schema_hide_empty_or_placeholder_social_links(): void
    {
        SiteSetting::query()->delete();
        Cache::forget('site_settings.current');

        $settings = SiteSetting::createDefault();
        $settings->update([
            'instagram_url' => 'https://instagram.com',
            'facebook_url' => '',
            'tiktok_url' => null,
        ]);

        $this->assertSame([], $settings->fresh()->socialLinks());

        $response = $this->get('/');
        $response->assertOk();
        $response->assertDontSee('title="Instagram"', false);
        $response->assertDontSee('title="Facebook"', false);
        $response->assertDontSee('title="TikTok"', false);
        $response->assertSee('Atención Directa');
        $response->assertDontSee('"sameAs"', false);

        // Ahora configurar solo Instagram real: debe aparecer Instagram y seguir ocultos Facebook y TikTok
        $settings->update([
            'instagram_url' => 'https://instagram.com/vys.eventos',
            'facebook_url' => null,
            'tiktok_url' => null,
        ]);

        $responseWithIg = $this->get('/');
        $responseWithIg->assertOk();
        $responseWithIg->assertSee('Síguenos');
        $responseWithIg->assertSee('https://instagram.com/vys.eventos');
        $responseWithIg->assertDontSee('title="Facebook"', false);
        $responseWithIg->assertDontSee('title="TikTok"', false);
    }
}
