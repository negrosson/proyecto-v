<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'customer_name',
        'event_date',
        'location',
        'total_amount',
        'items_count',
        'items_payload',
        'status',
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
}
