<?php

declare(strict_types=1);

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro de Auditoría Forense con Inmutabilidad Criptográfica (Hash Chaining & HMAC-SHA256).
 *
 * Cualquier intento de actualización o eliminación a través del ORM arroja una DomainException.
 * Si un atacante altera manualmente registros en MySQL/phpMyAdmin, verifyIntegrity() retorna false.
 */
class SecurityAuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'security_audit_logs';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new DomainException('VIOLACIÓN FORENSE: Los registros de auditoría son inmutables.');
        });

        static::deleting(function (): void {
            throw new DomainException('VIOLACIÓN FORENSE: Está prohibido eliminar registros de auditoría.');
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Registra un evento forense en la base de datos con firma criptográfica HMAC encadenada.
     *
     * @param  array<string, mixed>  $context
     */
    public static function record(
        string $eventType,
        string $severity = 'info',
        array $context = [],
        ?int $userId = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): self {
        $userId = $userId ?? auth()->id();
        $ipAddress = $ipAddress ?? request()?->ip() ?? '127.0.0.1';
        $userAgent = $userAgent ?? request()?->userAgent();
        $now = now();
        $createdAtIso = $now->toIso8601String();

        // Obtener el hash del último registro insertado para mantener la cadena inmutable (Blockchain-like)
        /** @var self|null $lastRecord */
        $lastRecord = static::query()->latest('id')->first();
        $previousHash = $lastRecord?->signature_hmac ?? str_repeat('0', 64);

        $signature = static::computeSignature(
            userId: $userId,
            eventType: $eventType,
            severity: $severity,
            ipAddress: $ipAddress,
            context: $context,
            previousHash: $previousHash,
            createdAt: $createdAtIso,
        );

        $log = new static;
        $log->user_id = $userId;
        $log->event_type = $eventType;
        $log->severity = $severity;
        $log->ip_address = $ipAddress;
        $log->user_agent = $userAgent;
        $log->context = $context;
        $log->previous_hash = $previousHash;
        $log->signature_hmac = $signature;
        $log->created_at = $now;
        $log->saveQuietly();

        return $log;
    }

    /**
     * Calcula la firma HMAC-SHA256 determinista sobre el contenido del registro.
     *
     * @param  array<string, mixed>|string|null  $context
     */
    public static function computeSignature(
        ?int $userId,
        string $eventType,
        string $severity,
        string $ipAddress,
        mixed $context,
        ?string $previousHash,
        string $createdAt,
    ): string {
        $normalizedContext = is_array($context)
            ? (string) json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            : (is_string($context) ? $context : (string) json_encode($context));

        $payload = implode('|', [
            (string) ($userId ?? ''),
            $eventType,
            $severity,
            $ipAddress,
            $normalizedContext,
            (string) ($previousHash ?? ''),
            $createdAt,
        ]);

        $appKey = (string) config('app.key');
        if (str_starts_with($appKey, 'base64:')) {
            $decoded = base64_decode(substr($appKey, 7), true);
            if ($decoded !== false) {
                $appKey = $decoded;
            }
        }

        return hash_hmac('sha256', $payload, $appKey);
    }

    /**
     * Verifica la autenticidad e integridad del registro recalculando el HMAC.
     */
    public function verifyIntegrity(): bool
    {
        $createdAtIso = $this->created_at instanceof \DateTimeInterface
            ? $this->created_at->toIso8601String()
            : (string) $this->created_at;

        $computed = static::computeSignature(
            userId: $this->user_id,
            eventType: (string) $this->event_type,
            severity: (string) $this->severity,
            ipAddress: (string) $this->ip_address,
            context: $this->context,
            previousHash: $this->previous_hash,
            createdAt: $createdAtIso,
        );

        return hash_equals((string) $this->signature_hmac, $computed);
    }
}
