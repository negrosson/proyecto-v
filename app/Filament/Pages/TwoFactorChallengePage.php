<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Services\Security\TotpService;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;

/**
 * Pantalla de Desafío de Doble Factor de Autenticación (MFA) para Filament v3.
 *
 * Aplica el atributo #[Locked] a propiedades sensibles y cuenta con Circuit Breaker anti-fuerza bruta.
 */
class TwoFactorChallengePage extends Page
{
    protected static string $view = 'filament.pages.two-factor-challenge';

    protected static ?string $title = 'Verificación de Seguridad (2FA)';

    protected static ?string $slug = 'two-factor-challenge';

    protected static bool $shouldRegisterNavigation = false;

    #[Locked]
    public ?int $userId = null;

    public string $code = '';

    public bool $useRecoveryCode = false;

    public string $recovery_code = '';

    public function mount(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user === null) {
            redirect()->to(Filament::getLoginUrl());

            return;
        }

        // Si el usuario no tiene 2FA configurado, redirigir al panel principal
        if (! $user->hasEnabledTwoFactor()) {
            redirect()->to(Filament::getUrl());

            return;
        }

        // Si ya está verificado en la sesión actual, no requerir re-autenticación
        if (session('mfa_verified_for_user_id') === $user->id && session('mfa_verified_ip') === request()->ip()) {
            redirect()->to(Filament::getUrl());

            return;
        }

        $this->userId = (int) $user->id;
    }

    /**
     * Valida el código TOTP o de recuperación y establece la sesión autenticada.
     */
    public function submitChallenge(TotpService $totp): mixed
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user === null) {
            return redirect()->to(Filament::getLoginUrl());
        }

        // Circuit Breaker: Bloqueo activo
        if ($user->isMfaLocked()) {
            $this->addError('code', 'Tu cuenta se encuentra temporalmente bloqueada por 15 minutos debido a múltiples intentos fallidos de 2FA.');

            return null;
        }

        if ($this->useRecoveryCode) {
            $this->validate([
                'recovery_code' => ['required', 'string', 'min:8', 'max:40'],
            ]);

            $valid = $totp->consumeRecoveryCode($user, $this->recovery_code);
        } else {
            $this->validate([
                'code' => ['required', 'string', 'size:6'],
            ]);

            $valid = $totp->verifyAndConsume($user, $this->code);
        }

        if ($valid) {
            session()->forget('mfa_failed_attempts');

            // Mitigación estricta de Session Fixation
            if (request()->hasSession()) {
                request()->session()->regenerate();
            }

            session([
                'mfa_verified_for_user_id' => $user->id,
                'mfa_verified_ip' => request()->ip(),
            ]);

            SecurityAuditLog::record(
                eventType: 'mfa.challenge_success',
                severity: 'info',
                context: [
                    'user_id' => $user->id,
                    'auth_method' => $this->useRecoveryCode ? 'recovery_code' : 'totp',
                ]
            );

            Notification::make()
                ->title('Identidad confirmada')
                ->success()
                ->send();

            return redirect()->to(Filament::getUrl());
        }

        // Incremento de intentos fallidos
        $attempts = (int) session('mfa_failed_attempts', 0) + 1;
        session(['mfa_failed_attempts' => $attempts]);

        // Disparo de Circuit Breaker tras 5 intentos
        if ($attempts >= 5) {
            $user->forceFill([
                'mfa_locked_until' => now()->addMinutes(15),
            ])->save();

            SecurityAuditLog::record(
                eventType: 'mfa.circuit_breaker_triggered',
                severity: 'critical',
                context: [
                    'user_id' => $user->id,
                    'attempts' => $attempts,
                    'locked_until' => now()->addMinutes(15)->toIso8601String(),
                ]
            );

            Auth::logout();
            if (request()->hasSession()) {
                request()->session()->invalidate();
                request()->session()->regenerateToken();
            }

            return redirect()->to(Filament::getLoginUrl())->with('error', 'Tu cuenta ha sido bloqueada temporalmente por 15 minutos debido a reiterados intentos fallidos.');
        }

        SecurityAuditLog::record(
            eventType: 'mfa.challenge_failed',
            severity: 'warning',
            context: [
                'user_id' => $user->id,
                'attempt' => $attempts,
                'auth_method' => $this->useRecoveryCode ? 'recovery_code' : 'totp',
            ]
        );

        $errorField = $this->useRecoveryCode ? 'recovery_code' : 'code';
        $this->addError($errorField, 'El código ingresado es incorrecto o ha expirado. Intentos restantes: '.(5 - $attempts));

        return null;
    }
}
