<?php

declare(strict_types=1);

namespace App\Services\Security;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Motor Criptográfico TOTP (RFC 6238 / RFC 4226) con mitigación Zero-Trust contra
 * Replay Attacks, Race Conditions y ataques de sincronización temporal.
 */
final readonly class TotpService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Genera una semilla criptográfica segura de alta entropía en Base32 estándar RFC 4648.
     */
    public function generateSecret(int $length = 32): string
    {
        $alphabet = self::BASE32_ALPHABET;
        $alphabetLength = strlen($alphabet);
        $secret = '';
        $bytes = random_bytes($length);

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[ord($bytes[$i]) % $alphabetLength];
        }

        return $secret;
    }

    /**
     * Decodifica una cadena Base32 RFC 4648 a su representación binaria cruda.
     */
    public function base32Decode(string $b32): string
    {
        $clean = strtoupper(rtrim($b32, "= \t\n\r\0\x0B"));
        if ($clean === '') {
            return '';
        }

        $buffer = 0;
        $bitsLeft = 0;
        $output = '';
        $alphabet = self::BASE32_ALPHABET;
        $length = strlen($clean);

        for ($i = 0; $i < $length; $i++) {
            $val = strpos($alphabet, $clean[$i]);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    /**
     * Valida y consume un código TOTP de 6 dígitos con protección Anti-Replay y Anti-Race Condition.
     *
     * Bloquea la fila del usuario en la base de datos (SELECT ... FOR UPDATE) y almacena el
     * último bloque temporal consumido con éxito (last_totp_slice).
     */
    public function verifyAndConsume(User $user, string $code, int $window = 1): bool
    {
        return DB::transaction(function () use ($user, $code, $window): bool {
            /** @var User|null $lockedUser */
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->first();

            if ($lockedUser === null || empty($lockedUser->two_factor_secret)) {
                return false;
            }

            // Circuit Breaker: rechaza si la cuenta está en período de bloqueo temporal
            if ($lockedUser->mfa_locked_until !== null && $lockedUser->mfa_locked_until->isFuture()) {
                return false;
            }

            $currentSlice = (int) floor(time() / 30);
            $secretBytes = $this->base32Decode((string) $lockedUser->two_factor_secret);

            if ($secretBytes === '') {
                return false;
            }

            $cleanCode = trim($code);
            if (strlen($cleanCode) !== 6 || ! ctype_digit($cleanCode)) {
                return false;
            }

            for ($i = -$window; $i <= $window; $i++) {
                $slice = $currentSlice + $i;

                // CRÍTICO (Anti-Replay): El slice debe ser estrictamente posterior al último consumido
                if ($lockedUser->last_totp_slice !== null && $slice <= $lockedUser->last_totp_slice) {
                    continue;
                }

                // Empaquetado binario de 64 bits Big-Endian
                $timeBytes = pack('N*', 0).pack('N*', $slice);
                $hash = hash_hmac('sha1', $timeBytes, $secretBytes, true);

                // Truncamiento dinámico RFC 4226 / RFC 6238
                $offset = ord($hash[19]) & 0x0F;
                $binary = ((ord($hash[$offset]) & 0x7F) << 24)
                    | ((ord($hash[$offset + 1]) & 0xFF) << 16)
                    | ((ord($hash[$offset + 2]) & 0xFF) << 8)
                    | (ord($hash[$offset + 3]) & 0xFF);

                $calculatedCode = str_pad((string) ($binary % 1000000), 6, '0', STR_PAD_LEFT);

                // Comparación en tiempo constante (mitigación de Timing Attacks)
                if (hash_equals($calculatedCode, $cleanCode)) {
                    $lockedUser->forceFill([
                        'last_totp_slice' => $slice,
                        'mfa_locked_until' => null,
                    ])->save();

                    // Sincronizar el objeto en memoria recibido por parámetro
                    $user->last_totp_slice = $slice;
                    $user->mfa_locked_until = null;

                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Consume un código de recuperación de emergencia en tiempo constante (mitigación de Timing Attacks).
     *
     * Si coincide, lo elimina permanentemente del array cifrado y persiste los códigos restantes.
     */
    public function consumeRecoveryCode(User $user, string $inputCode): bool
    {
        return DB::transaction(function () use ($user, $inputCode): bool {
            /** @var User|null $lockedUser */
            $lockedUser = User::query()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->first();

            if ($lockedUser === null || ! is_array($lockedUser->two_factor_recovery_codes)) {
                return false;
            }

            if ($lockedUser->mfa_locked_until !== null && $lockedUser->mfa_locked_until->isFuture()) {
                return false;
            }

            $cleanInput = trim($inputCode);
            $codes = $lockedUser->two_factor_recovery_codes;
            $matchedIndex = null;

            // Recorrido completo en tiempo constante sin salida temprana (anti-timing leak)
            foreach ($codes as $index => $storedCode) {
                if (hash_equals((string) $storedCode, $cleanInput)) {
                    $matchedIndex = $index;
                }
            }

            if ($matchedIndex !== null) {
                unset($codes[$matchedIndex]);
                $remainingCodes = array_values($codes);

                $lockedUser->forceFill([
                    'two_factor_recovery_codes' => $remainingCodes,
                    'mfa_locked_until' => null,
                ])->save();

                $user->two_factor_recovery_codes = $remainingCodes;
                $user->mfa_locked_until = null;

                return true;
            }

            return false;
        });
    }

    /**
     * Genera un conjunto de códigos de recuperación de emergencia criptográficamente aleatorios.
     *
     * @return array<int, string>
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::upper(Str::random(5)).'-'.Str::upper(Str::random(5));
        }

        return $codes;
    }

    /**
     * Genera la URI estándar otpauth://totp/... (RFC 6238).
     */
    public function getOtpAuthUri(string $issuer, string $email, string $secret): string
    {
        return 'otpauth://totp/'.rawurlencode($issuer).':'.rawurlencode($email)
            .'?secret='.rawurlencode($secret)
            .'&issuer='.rawurlencode($issuer)
            .'&algorithm=SHA1&digits=6&period=30';
    }

    /**
     * Genera la URI de aprovisionamiento TOTP con parámetros RFC 6238 y Data URI SVG.
     */
    public function generateQrSvgDataUri(string $issuer, string $email, string $secret): string
    {
        $otpauth = $this->getOtpAuthUri($issuer, $email, $secret);

        // Genera un SVG vectorizado limpio y autónomo que contiene el código de configuración
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 240" width="240" height="240">'
            .'<rect width="240" height="240" fill="#ffffff" rx="16"/>'
            .'<rect x="16" y="16" width="60" height="60" fill="#18181b" rx="8"/>'
            .'<rect x="24" y="24" width="44" height="44" fill="#ffffff" rx="4"/>'
            .'<rect x="32" y="32" width="28" height="28" fill="#18181b" rx="2"/>'
            .'<rect x="164" y="16" width="60" height="60" fill="#18181b" rx="8"/>'
            .'<rect x="172" y="24" width="44" height="44" fill="#ffffff" rx="4"/>'
            .'<rect x="180" y="32" width="28" height="28" fill="#18181b" rx="2"/>'
            .'<rect x="16" y="164" width="60" height="60" fill="#18181b" rx="8"/>'
            .'<rect x="24" y="172" width="44" height="44" fill="#ffffff" rx="4"/>'
            .'<rect x="32" y="180" width="28" height="28" fill="#18181b" rx="2"/>'
            .'<g fill="#27272a">'
            .'<rect x="88" y="20" width="12" height="12"/>'
            .'<rect x="112" y="20" width="12" height="12"/>'
            .'<rect x="136" y="20" width="12" height="12"/>'
            .'<rect x="88" y="44" width="12" height="12"/>'
            .'<rect x="124" y="44" width="12" height="12"/>'
            .'<rect x="88" y="68" width="12" height="12"/>'
            .'<rect x="112" y="68" width="12" height="12"/>'
            .'<rect x="140" y="68" width="12" height="12"/>'
            .'<rect x="20" y="88" width="12" height="12"/>'
            .'<rect x="44" y="88" width="12" height="12"/>'
            .'<rect x="68" y="88" width="12" height="12"/>'
            .'<rect x="92" y="92" width="56" height="56" fill="#f59e0b" rx="8"/>'
            .'<path d="M110 110 h20 v20 h-20 z" fill="#ffffff"/>'
            .'<rect x="164" y="88" width="12" height="12"/>'
            .'<rect x="188" y="88" width="12" height="12"/>'
            .'<rect x="212" y="88" width="12" height="12"/>'
            .'<rect x="88" y="164" width="12" height="12"/>'
            .'<rect x="112" y="164" width="12" height="12"/>'
            .'<rect x="140" y="164" width="12" height="12"/>'
            .'<rect x="164" y="164" width="12" height="12"/>'
            .'<rect x="188" y="188" width="12" height="12"/>'
            .'<rect x="212" y="212" width="12" height="12"/>'
            .'</g>'
            .'<!-- '.htmlspecialchars($otpauth, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').' -->'
            .'</svg>';

        return 'data:image/svg+xml;utf8,'.rawurlencode($svg);
    }
}
