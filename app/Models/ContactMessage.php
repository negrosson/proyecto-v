<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ContactMessage extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('filament_badge_contact_unread_count');
        });

        static::deleted(static function (): void {
            Cache::forget('filament_badge_contact_unread_count');
        });
    }

    protected $fillable = [
        'customer_id',
        'name',
        'email',
        'phone',
        'event_address',
        'event_date',
        'message',
        'ip_address',
        'is_read',
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
            'is_read' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function whatsappUrl(): ?string
    {
        $phone = filled($this->phone)
            ? (string) $this->phone
            : ($this->relationLoaded('customer') ? $this->customer?->phone : null);

        $greeting = "Hola {$this->name}, te escribimos de VYS Productora de Eventos en respuesta a tu consulta web.";

        return Customer::buildWhatsappUrl($phone, $greeting);
    }
}
