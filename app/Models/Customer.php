<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Customer extends Model
{
    use HasFactory;

    public const TYPE_PARTICULAR = 'particular';

    public const TYPE_EMPRESA = 'empresa';

    public const TYPE_PRODUCTORA = 'productora';

    /**
     * @var array<string, string>
     */
    public const TYPES = [
        self::TYPE_PARTICULAR => 'Particular',
        self::TYPE_EMPRESA => 'Empresa / Banquetera',
        self::TYPE_PRODUCTORA => 'Productora / Organizador',
    ];

    protected $fillable = [
        'name',
        'type',
        'rut',
        'phone',
        'email',
        'commune',
        'is_frequent',
        'notes',
    ];

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('filament_badge_customers_count');
        });

        static::deleted(static function (): void {
            Cache::forget('filament_badge_customers_count');
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_frequent' => 'boolean',
        ];
    }

    /**
     * @return HasMany<QuoteLog, $this>
     */
    public function quoteLogs(): HasMany
    {
        return $this->hasMany(QuoteLog::class);
    }

    /**
     * @return HasMany<ContactMessage, $this>
     */
    public function contactMessages(): HasMany
    {
        return $this->hasMany(ContactMessage::class);
    }

    /**
     * Normaliza un teléfono chileno o internacional a dígitos limpios aptos para WhatsApp (wa.me).
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || trim($phone) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if ($digits === '' || strlen($digits) < 8) {
            return null;
        }

        // Móvil chileno ingresado como 9XXXXXXXX -> 569XXXXXXXX
        if (strlen($digits) === 9 && str_starts_with($digits, '9')) {
            return '56'.$digits;
        }

        // Móvil chileno ingresado sin 9 ni 56 (8 dígitos) -> 569XXXXXXXX
        if (strlen($digits) === 8) {
            return '569'.$digits;
        }

        return $digits;
    }

    /**
     * Construye un enlace directo a WhatsApp Web / App con mensaje opcional.
     */
    public static function buildWhatsappUrl(?string $phone, ?string $message = null): ?string
    {
        $normalized = self::normalizePhone($phone);

        if ($normalized === null) {
            return null;
        }

        $url = 'https://wa.me/'.$normalized;

        if ($message !== null && trim($message) !== '') {
            $url .= '?text='.rawurlencode(trim($message));
        }

        return $url;
    }

    public function whatsappUrl(?string $message = null): ?string
    {
        return self::buildWhatsappUrl($this->phone, $message);
    }

    /**
     * Busca un cliente existente por teléfono normalizado o correo para evitar duplicados;
     * si no existe, lo crea. Si existe, completa los campos que estén vacíos.
     *
     * @param  array{
     *     name: string,
     *     type?: string|null,
     *     rut?: string|null,
     *     phone?: string|null,
     *     email?: string|null,
     *     commune?: string|null,
     *     is_frequent?: bool,
     *     notes?: string|null,
     * }  $attributes
     */
    public static function findOrCreateFromInquiry(array $attributes): self
    {
        $name = trim((string) ($attributes['name'] ?? ''));
        $phone = isset($attributes['phone']) && trim((string) $attributes['phone']) !== ''
            ? trim((string) $attributes['phone'])
            : null;
        $email = isset($attributes['email']) && trim((string) $attributes['email']) !== ''
            ? mb_strtolower(trim((string) $attributes['email']))
            : null;
        $commune = isset($attributes['commune']) && trim((string) $attributes['commune']) !== ''
            ? trim((string) $attributes['commune'])
            : null;
        $rut = isset($attributes['rut']) && trim((string) $attributes['rut']) !== ''
            ? trim((string) $attributes['rut'])
            : null;
        $notes = isset($attributes['notes']) && trim((string) $attributes['notes']) !== ''
            ? trim((string) $attributes['notes'])
            : null;
        $type = array_key_exists((string) ($attributes['type'] ?? ''), self::TYPES)
            ? (string) $attributes['type']
            : self::TYPE_PARTICULAR;
        $isFrequent = (bool) ($attributes['is_frequent'] ?? false);

        $existing = null;

        $normalizedPhone = self::normalizePhone($phone);
        if ($normalizedPhone !== null) {
            $suffix = substr($normalizedPhone, -8);
            $candidates = self::query()
                ->whereNotNull('phone')
                ->whereRaw(
                    "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', ''), '(', ''), ')', ''), '.', '') LIKE ?",
                    ['%'.$suffix]
                )
                ->get();

            foreach ($candidates as $candidate) {
                if (self::normalizePhone($candidate->phone) === $normalizedPhone) {
                    $existing = $candidate;
                    break;
                }
            }
        }

        if ($existing === null && $email !== null) {
            $existing = self::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();
        }

        if ($existing !== null) {
            $updates = [];

            if (blank($existing->phone) && $phone !== null) {
                $updates['phone'] = $phone;
            }

            if (blank($existing->email) && $email !== null) {
                $updates['email'] = $email;
            }

            if (blank($existing->commune) && $commune !== null) {
                $updates['commune'] = $commune;
            }

            if (blank($existing->rut) && $rut !== null) {
                $updates['rut'] = $rut;
            }

            if ($existing->type === self::TYPE_PARTICULAR && $type !== self::TYPE_PARTICULAR) {
                $updates['type'] = $type;
            }

            if ($isFrequent && ! $existing->is_frequent) {
                $updates['is_frequent'] = true;
            }

            if ($notes !== null && ! str_contains((string) $existing->notes, $notes)) {
                $updates['notes'] = filled($existing->notes)
                    ? trim((string) $existing->notes)."\n".$notes
                    : $notes;
            }

            if ($updates !== []) {
                $existing->update($updates);
            }

            return $existing;
        }

        return self::create([
            'name' => $name !== '' ? $name : 'Cliente sin nombre',
            'type' => $type,
            'rut' => $rut,
            'phone' => $phone,
            'email' => $email,
            'commune' => $commune,
            'is_frequent' => $isFrequent,
            'notes' => $notes,
        ]);
    }

    /**
     * Neutraliza inyección de fórmulas en archivos CSV para Excel / LibreOffice (OWASP).
     */
    public static function sanitizeCsvCell(mixed $value): string
    {
        $text = trim((string) ($value ?? ''));

        if ($text === '') {
            return '';
        }

        $firstChar = $text[0];

        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$text;
        }

        return $text;
    }
}
