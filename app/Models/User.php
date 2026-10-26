<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo de Usuario protegido contra escalada de privilegios y ataques de asignación masiva (OWASP A01 / A04).
 *
 * Atributos sensibles como is_admin, two_factor_* y bloqueos de seguridad están estrictamente fuera de $fillable.
 */
#[Fillable(['name', 'email', 'password', 'is_active'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_developer' => 'boolean',
            'is_active' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'last_totp_slice' => 'integer',
            'mfa_locked_until' => 'datetime',
        ];
    }

    /**
     * Determina si el usuario actual es el Desarrollador del sistema.
     */
    public function isDeveloper(): bool
    {
        return (bool) ($this->is_developer ?? false) || $this->email === 'dev@catalogo-arriendo.cl';
    }

    /**
     * Controla el acceso estricto al panel administrativo de Filament (OWASP A01).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin === true && ($this->is_active ?? true);
    }

    /**
     * Determina si el usuario tiene activado y confirmado el doble factor de autenticación (TOTP).
     */
    public function hasEnabledTwoFactor(): bool
    {
        return $this->two_factor_confirmed_at !== null && ! empty($this->two_factor_secret);
    }

    /**
     * Comprueba si la cuenta está temporalmente bloqueada por exceso de intentos fallidos de MFA (Circuit Breaker).
     */
    public function isMfaLocked(): bool
    {
        return $this->mfa_locked_until !== null && $this->mfa_locked_until->isFuture();
    }
}
