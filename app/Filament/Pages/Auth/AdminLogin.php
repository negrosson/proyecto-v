<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\SecurityAuditLog;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Pantalla de inicio de sesión endurecida contra fuerza bruta, credential stuffing,
 * enumeración de cuentas, ataques de tiempo (timing attacks), DoS de bcrypt y session fixation.
 */
class AdminLogin extends BaseLogin
{
    /**
     * Hash bcrypt dummy precalculado para igualar el tiempo de respuesta cuando un correo no existe.
     */
    private const DUMMY_BCRYPT_HASH = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

    public function getHeading(): string
    {
        return 'Acceso Administrativo';
    }

    /**
     * Omite la casilla «Recuérdame» para evitar cookies persistentes de larga duración en el panel.
     *
     * @return array<int|string, string|Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Correo electrónico')
            ->email()
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->maxLength(120)
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Contraseña')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->maxLength(128)
            ->extraInputAttributes(['tabindex' => 2]);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $email = mb_strtolower(trim((string) ($data['email'] ?? '')));
        $password = (string) ($data['password'] ?? '');
        $ip = (string) (request()->ip() ?: '127.0.0.1');

        $accountKey = 'admin-login:account:'.sha1($email.'|'.$ip);
        $globalAccountKey = 'admin-login:global-account:'.sha1($email);
        $ipKey = 'admin-login:ip:'.sha1($ip);

        // 1. Triple límite de tasa: 5/5min por cuenta+IP, 15/15min por IP y 15/15min global por cuenta (anti-botnet)
        if (
            RateLimiter::tooManyAttempts($accountKey, 5) ||
            RateLimiter::tooManyAttempts($ipKey, 15) ||
            ($email !== '' && RateLimiter::tooManyAttempts($globalAccountKey, 15))
        ) {
            $seconds = max(
                RateLimiter::availableIn($accountKey),
                RateLimiter::availableIn($ipKey),
                $email !== '' ? RateLimiter::availableIn($globalAccountKey) : 0,
            );

            SecurityAuditLog::record(
                eventType: 'auth.rate_limit_exceeded',
                severity: 'critical',
                context: [
                    'email' => $email,
                    'retry_after_seconds' => $seconds,
                ],
                ipAddress: $ip,
            );

            throw ValidationException::withMessages([
                'data.email' => "Demasiados intentos fallidos. Por seguridad, espera {$seconds} segundos antes de volver a intentar.",
            ]);
        }

        // 2. Protección anti-DoS sobre bcrypt (limitar longitud de contraseña)
        if ($email === '' || $password === '' || strlen($password) > 128) {
            RateLimiter::hit($accountKey, 300);
            RateLimiter::hit($ipKey, 900);
            if ($email !== '') {
                RateLimiter::hit($globalAccountKey, 900);
            }
            $this->throwFailureValidationException();
        }

        /** @var User|null $candidate */
        $candidate = User::query()->where('email', $email)->first();

        // 3. Anti-Timing Attack: si el correo no existe, ejecutar Hash::check dummy para igualar latencia
        if ($candidate === null) {
            Hash::check($password, self::DUMMY_BCRYPT_HASH);
            RateLimiter::hit($accountKey, 300);
            RateLimiter::hit($ipKey, 900);
            RateLimiter::hit($globalAccountKey, 900);

            SecurityAuditLog::record(
                eventType: 'auth.login_failed',
                severity: 'warning',
                context: ['email' => $email, 'reason' => 'invalid_credentials'],
                ipAddress: $ip,
            );

            $this->throwFailureValidationException();
        }

        // 4. Validar contraseña + permisos de panel (sin cookie persistente remember_web)
        if (! Filament::auth()->attempt([
            'email' => $email,
            'password' => $password,
        ], false)) {
            RateLimiter::hit($accountKey, 300);
            RateLimiter::hit($ipKey, 900);
            RateLimiter::hit($globalAccountKey, 900);

            SecurityAuditLog::record(
                eventType: 'auth.login_failed',
                severity: 'warning',
                context: ['email' => $email, 'reason' => 'invalid_password'],
                userId: $candidate->id,
                ipAddress: $ip,
            );

            $this->throwFailureValidationException();
        }

        /** @var User|null $user */
        $user = Filament::auth()->user();

        if (
            $user === null ||
            ($user instanceof FilamentUser && ! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            RateLimiter::hit($accountKey, 300);
            RateLimiter::hit($ipKey, 900);
            RateLimiter::hit($globalAccountKey, 900);

            SecurityAuditLog::record(
                eventType: 'auth.login_blocked',
                severity: 'critical',
                context: ['email' => $email, 'reason' => 'panel_access_denied'],
                userId: $candidate->id,
                ipAddress: $ip,
            );

            $this->throwFailureValidationException();
        }

        // 5. Éxito: limpiar contadores de cuenta, regenerar sesión (anti-Session Fixation) y auditar
        RateLimiter::clear($accountKey);
        RateLimiter::clear($globalAccountKey);
        session()->regenerate();

        SecurityAuditLog::record(
            eventType: 'auth.login_success',
            severity: 'info',
            context: ['email' => $user->email, 'mfa_enabled' => $user->hasEnabledTwoFactor()],
            userId: $user->id,
            ipAddress: $ip,
        );

        return app(LoginResponse::class);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => 'Las credenciales ingresadas no son válidas.',
        ]);
    }
}
