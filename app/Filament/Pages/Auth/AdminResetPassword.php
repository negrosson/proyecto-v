<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\SecurityAuditLog;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\PasswordReset\ResetPassword as BaseResetPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * Pantalla de restablecimiento de contraseña endurecida con política de contraseña robusta
 * y registro de auditoría en SecurityAuditLog (mantiene intacto el 2FA si está activo).
 */
class AdminResetPassword extends BaseResetPassword
{
    public function getTitle(): string
    {
        return 'Nueva Contraseña';
    }

    public function getHeading(): string
    {
        return 'Crear nueva contraseña';
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Nueva contraseña')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->maxLength(128)
            ->rule(PasswordRule::min(10)->letters()->mixedCase()->numbers())
            ->same('passwordConfirmation')
            ->helperText('Mínimo 10 caracteres, combinando mayúsculas, minúsculas y números.')
            ->validationAttribute('nueva contraseña');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label('Confirmar nueva contraseña')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->maxLength(128)
            ->dehydrated(false);
    }

    public function resetPassword(): ?PasswordResetResponse
    {
        try {
            $this->rateLimit(3);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title('Demasiados intentos')
                ->body("Espera {$exception->secondsUntilAvailable} segundos antes de volver a intentar.")
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();
        $data['email'] = $this->email;
        $data['token'] = $this->token;

        $ip = (string) (request()->ip() ?: '127.0.0.1');
        $hasPanelAccess = true;

        $status = Password::broker(Filament::getAuthPasswordBroker())->reset(
            $this->getCredentialsFromFormData($data),
            function (CanResetPassword|Model|Authenticatable $user) use ($data, $ip, &$hasPanelAccess): void {
                if (
                    ($user instanceof FilamentUser) &&
                    (! $user->canAccessPanel(Filament::getCurrentPanel()))
                ) {
                    $hasPanelAccess = false;

                    return;
                }

                $user->forceFill([
                    'password' => Hash::make($data['password']),
                    'remember_token' => Str::random(60),
                ])->save();

                SecurityAuditLog::record(
                    eventType: 'auth.password_reset_completed',
                    severity: 'info',
                    context: ['email' => $user->getEmailForPasswordReset()],
                    userId: $user->getKey(),
                    ipAddress: $ip,
                );

                event(new PasswordReset($user));
            },
        );

        if ($hasPanelAccess === false) {
            $status = Password::INVALID_USER;
        }

        if ($status === Password::PASSWORD_RESET) {
            Notification::make()
                ->title('Contraseña actualizada')
                ->body('Ya puedes iniciar sesión con tu nueva contraseña.')
                ->success()
                ->send();

            return app(PasswordResetResponse::class);
        }

        Notification::make()
            ->title('Enlace inválido o expirado')
            ->body('El enlace de recuperación ya expiró o fue utilizado. Solicita uno nuevo desde el inicio de sesión.')
            ->danger()
            ->send();

        return null;
    }
}
