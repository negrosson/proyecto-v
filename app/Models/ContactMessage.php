<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
}
