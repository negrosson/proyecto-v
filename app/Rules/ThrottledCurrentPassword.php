<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\SecurityAuditLog;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Valida la contraseña actual del usuario autenticado aplicando Rate Limiting por cuenta
 * y registro forense en SecurityAuditLog (evita fuerza bruta sobre sesiones ya abiertas).
 */
class ThrottledCurrentPassword implements ValidationRule
{
    public const MAX_ATTEMPTS = 5;

    public const DECAY_SECONDS = 600;

    public function __construct(
        private readonly string $guard = 'web',
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        /** @var User|null $user */
        $user = auth($this->guard)->user();

        if ($user === null || ! is_string($value) || $value === '' || strlen($value) > 128) {
            $fail('La contraseña actual ingresada es incorrecta.');

            return;
        }

        $key = self::throttleKey((int) $user->id);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);

            SecurityAuditLog::record(
                eventType: 'auth.sudo_rate_limit_exceeded',
                severity: 'critical',
                context: [
                    'user_id' => $user->id,
                    'retry_after_seconds' => $seconds,
                ],
                userId: $user->id,
            );

            $fail("Demasiados intentos fallidos de verificación de contraseña. Espera {$seconds} segundos antes de reintentar.");

            return;
        }

        if (! Hash::check($value, (string) $user->getAuthPassword())) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            SecurityAuditLog::record(
                eventType: 'auth.sudo_failed',
                severity: 'warning',
                context: [
                    'user_id' => $user->id,
                    'attempts' => RateLimiter::attempts($key),
                ],
                userId: $user->id,
            );

            $fail('La contraseña actual ingresada es incorrecta.');

            return;
        }

        RateLimiter::clear($key);
    }

    public static function throttleKey(int $userId): string
    {
        return 'sudo-password:user:'.$userId;
    }
}
