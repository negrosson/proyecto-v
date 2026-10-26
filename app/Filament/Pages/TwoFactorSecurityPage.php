<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Services\Security\TotpService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Locked;

/**
 * Página de Gestión y Aprovisionamiento del Segundo Factor de Autenticación (MFA).
 *
 * Exige Modo Sudo (current_password:web) para cualquier cambio y bloquea manipulación con #[Locked].
 */
class TwoFactorSecurityPage extends Page
{
    protected static string $view = 'filament.pages.two-factor-security-page';

    protected static ?string $title = 'Doble Factor de Autenticación (2FA)';

    protected static ?string $navigationLabel = 'Doble Factor (2FA)';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Seguridad & Accesos';

    protected static ?int $navigationSort = 3;

    #[Locked]
    public bool $isEnabled = false;

    #[Locked]
    public bool $isConfiguring = false;

    #[Locked]
    public ?string $setupSecret = null;

    #[Locked]
    public ?string $setupQrUri = null;

    #[Locked]
    public ?string $otpauthUri = null;

    #[Locked]
    public array $recoveryCodes = [];

    public string $confirmationCode = '';

    public string $currentPassword = '';

    public string $disablePassword = '';

    public string $regeneratePassword = '';

    public function mount(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user !== null) {
            $this->isEnabled = $user->hasEnabledTwoFactor();
        }
    }

    /**
     * Inicia el flujo de configuración generando una nueva semilla en memoria.
     */
    public function startConfiguration(TotpService $totp): void
    {
        /** @var User $user */
        $user = auth()->user();

        $secret = $totp->generateSecret();
        $this->setupSecret = $secret;
        $this->setupQrUri = $totp->generateQrSvgDataUri('Arriendos & Eventos', $user->email, $secret);
        $this->otpauthUri = $totp->getOtpAuthUri('Arriendos & Eventos', $user->email, $secret);
        $this->isConfiguring = true;
        $this->confirmationCode = '';
        $this->currentPassword = '';
    }

    /**
     * Cancela el asistente de configuración.
     */
    public function cancelConfiguration(): void
    {
        $this->isConfiguring = false;
        $this->setupSecret = null;
        $this->setupQrUri = null;
        $this->otpauthUri = null;
        $this->confirmationCode = '';
        $this->currentPassword = '';
    }

    /**
     * Confirma la activación del 2FA validando la contraseña actual (Sudo) y el código dinámico.
     */
    public function confirmConfiguration(TotpService $totp): void
    {
        $this->validate([
            'currentPassword' => ['required', 'current_password:web'],
            'confirmationCode' => ['required', 'string', 'size:6'],
        ]);

        /** @var User $user */
        $user = auth()->user();

        if (empty($this->setupSecret)) {
            $this->addError('confirmationCode', 'La sesión de configuración expiró. Por favor reinicia el proceso.');

            return;
        }

        // Asignar temporalmente el secreto para validar el código contra el método oficial
        $user->two_factor_secret = $this->setupSecret;

        if (! $totp->verifyAndConsume($user, $this->confirmationCode)) {
            $this->addError('confirmationCode', 'El código de 6 dígitos no coincide o ha expirado. Verifica la hora en tu dispositivo.');

            return;
        }

        $recoveryCodes = $totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_secret' => $this->setupSecret,
            'two_factor_recovery_codes' => $recoveryCodes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        session([
            'mfa_verified_for_user_id' => $user->id,
            'mfa_verified_ip' => request()->ip(),
        ]);

        $this->recoveryCodes = $recoveryCodes;
        $this->isEnabled = true;
        $this->isConfiguring = false;
        $this->currentPassword = '';
        $this->confirmationCode = '';

        SecurityAuditLog::record(
            eventType: 'mfa.enabled',
            severity: 'warning',
            context: [
                'user_id' => $user->id,
                'email' => $user->email,
            ]
        );

        Notification::make()
            ->title('Doble factor de autenticación activado')
            ->success()
            ->body('Tu cuenta ahora cuenta con protección criptográfica de grado Zero-Trust.')
            ->send();
    }

    /**
     * Regenera un nuevo lote de códigos de emergencia en Modo Sudo.
     */
    public function regenerateRecoveryCodes(TotpService $totp): void
    {
        $this->validate([
            'regeneratePassword' => ['required', 'current_password:web'],
        ]);

        /** @var User $user */
        $user = auth()->user();

        $newCodes = $totp->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => $newCodes,
        ])->save();

        $this->recoveryCodes = $newCodes;
        $this->regeneratePassword = '';

        SecurityAuditLog::record(
            eventType: 'mfa.recovery_codes_regenerated',
            severity: 'warning',
            context: [
                'user_id' => $user->id,
            ]
        );

        Notification::make()
            ->title('Códigos de emergencia regenerados')
            ->success()
            ->body('Los códigos anteriores han sido revocados inmediatamente.')
            ->send();
    }

    /**
     * Desactiva el 2FA en Modo Sudo.
     */
    public function disableTwoFactor(): void
    {
        $this->validate([
            'disablePassword' => ['required', 'current_password:web'],
        ]);

        /** @var User $user */
        $user = auth()->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'last_totp_slice' => null,
        ])->save();

        session()->forget(['mfa_verified_for_user_id', 'mfa_verified_ip']);

        $this->isEnabled = false;
        $this->disablePassword = '';
        $this->recoveryCodes = [];

        SecurityAuditLog::record(
            eventType: 'mfa.disabled',
            severity: 'critical',
            context: [
                'user_id' => $user->id,
                'email' => $user->email,
            ]
        );

        Notification::make()
            ->title('Doble factor desactivado')
            ->warning()
            ->send();
    }
}
