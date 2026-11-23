<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class QuoteLog extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('filament_badge_quotes_pending_count');
        });

        static::deleted(static function (): void {
            Cache::forget('filament_badge_quotes_pending_count');
        });
    }

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'event_date',
        'location',
        'total_amount',
        'items_count',
        'items_payload',
        'status',
        'internal_notes',
        'ip_address',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'total_amount' => 'integer',
            'items_count' => 'integer',
            'items_payload' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function resolvedPhone(): ?string
    {
        if (filled($this->customer_phone)) {
            return (string) $this->customer_phone;
        }

        if ($this->relationLoaded('customer') && filled($this->customer?->phone)) {
            return (string) $this->customer->phone;
        }

        if ($this->customer_id !== null) {
            $phone = $this->customer()->value('phone');

            return filled($phone) ? (string) $phone : null;
        }

        return null;
    }

    public function whatsappUrl(): ?string
    {
        $dateText = $this->event_date ? ' para el '.$this->event_date->format('d/m/Y') : '';
        $greeting = "Hola {$this->customer_name}, te escribimos de VYS Productora de Eventos por tu cotización{$dateText} ({$this->location}).";

        return Customer::buildWhatsappUrl($this->resolvedPhone(), $greeting);
    }
}
