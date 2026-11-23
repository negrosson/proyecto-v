<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\SecurityAuditLog;
use App\Notifications\AdminResetPasswordNotification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Illuminate\Auth\Events\PasswordResetLinkSent;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

/**
 * Solicitud de restablecimiento de contraseña endurecida contra enumeración de cuentas (OWASP A07)
 * con notificación en español y registro inmutable en auditoría de seguridad.
 */
class AdminRequestPasswordReset extends BaseRequestPasswordReset
{
    public function getTitle(): string
    {
        return 'Recuperar Contraseña';
    }

    public function getHeading(): string
    {
        return '¿Olvidaste tu contraseña?';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Correo electrónico de administrador')
            ->email()
            ->required()
            ->maxLength(120)
            ->autocomplete('username')
            ->autofocus();
    }

    public function request(): void
    {
        try {
            $this->rateLimit(3);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title('Demasiadas solicitudes')
                ->body("Por seguridad, espera {$exception->secondsUntilAvailable} segundos antes de volver a solicitar un enlace.")
                ->danger()
                ->send();

            return;
        }

        $ip = (string) (request()->ip() ?: '127.0.0.1');
        $ipKey = 'admin-pwd-reset:ip:'.sha1($ip);

        if (RateLimiter::tooManyAttempts($ipKey, 6)) {
            $seconds = RateLimiter::availableIn($ipKey);

            SecurityAuditLog::record(
                eventType: 'auth.password_reset_throttled',
                severity: 'warning',
                context: ['retry_after_seconds' => $seconds],
                ipAddress: $ip,
            );

            Notification::make()
                ->title('Límite de solicitudes alcanzado')
                ->body("Por seguridad, espera {$seconds} segundos antes de intentarlo nuevamente.")
                ->danger()
                ->send();

            return;
        }

        RateLimiter::hit($ipKey, 600);

        $data = $this->form->getState();
        $email = mb_strtolower(trim((string) ($data['email'] ?? '')));

        $mailError = false;

        try {
            Password::broker(Filament::getAuthPasswordBroker())->sendResetLink(
                ['email' => $email],
                function (CanResetPassword $user, string $token) use ($ip): void {
                    if (
                        ($user instanceof FilamentUser) &&
                        (! $user->canAccessPanel(Filament::getCurrentPanel()))
                    ) {
                        return;
                    }

                    $notification = new AdminResetPasswordNotification($token);
                    $notification->url = Filament::getResetPasswordUrl($token, $user);

                    if (method_exists($user, 'notify')) {
                        $user->notify($notification);
                    }

                    SecurityAuditLog::record(
                        eventType: 'auth.password_reset_link_sent',
                        severity: 'info',
                        context: ['email' => $user->getEmailForPasswordReset()],
                        userId: $user->getKey(),
                        ipAddress: $ip,
                    );

                    if (class_exists(PasswordResetLinkSent::class)) {
                        event(new PasswordResetLinkSent($user));
                    }
                },
            );
        } catch (Throwable $exception) {
            $mailError = true;
            report($exception);

            SecurityAuditLog::record(
                eventType: 'auth.password_reset_mail_failed',
                severity: 'critical',
                context: [
                    'email' => $email,
                    'error' => mb_substr($exception->getMessage(), 0, 200),
                ],
                ipAddress: $ip,
            );
        }

        if ($mailError) {
            Notification::make()
                ->title('No se pudo enviar el correo')
                ->body('El servicio de correo SMTP del servidor no respondió. Verifica la configuración MAIL_* en el servidor o contacta al soporte técnico.')
                ->danger()
                ->send();

            return;
        }

        // Respuesta neutra anti-enumeración: siempre idéntica exista o no el correo.
        Notification::make()
            ->title('Solicitud procesada')
            ->body('Si el correo ingresado corresponde a una cuenta activa del panel, recibirás un enlace de recuperación en los próximos minutos (revisa también Spam).')
            ->success()
            ->send();

        $this->form->fill();
    }
}
