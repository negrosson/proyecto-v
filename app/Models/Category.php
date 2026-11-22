<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('filament_badge_categories_count');
        });

        static::deleted(static function (): void {
            Cache::forget('filament_badge_categories_count');
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
