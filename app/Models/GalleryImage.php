<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Media\PublicMediaCleaner;
use Database\Factories\GalleryImageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class GalleryImage extends Model
{
    /** @use HasFactory<GalleryImageFactory> */
    use HasFactory;

    public const CATEGORIES = [
        'Sin clasificar' => 'Sin clasificar',
        'Matrimonios & Bodas' => 'Matrimonios & Bodas',
        'Eventos Empresa' => 'Eventos Empresa',
        'Gala & Banquete' => 'Gala & Banquete',
        'Carpas & Exterior' => 'Carpas & Exterior',
        'Mobiliario & Sillas' => 'Mobiliario & Sillas',
        'Vajilla & Cristalería' => 'Vajilla & Cristalería',
        'Decoración & Ambientación' => 'Decoración & Ambientación',
    ];

    protected $fillable = [
        'title',
        'category',
        'image_path',
        'original_name',
        'file_hash',
        'montaje_id',
        'notes',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'montaje_id' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('filament_badge_gallery_images_count');
        });

        static::updated(static function (self $image): void {
            if ($image->wasChanged('image_path')) {
                $oldPath = $image->getOriginal('image_path');
                PublicMediaCleaner::deleteIfUnreferenced(is_string($oldPath) ? $oldPath : null);
            }
        });

        static::deleted(static function (self $image): void {
            Cache::forget('filament_badge_gallery_images_count');
            PublicMediaCleaner::deleteIfUnreferenced($image->image_path);
        });
    }

    public function montaje(): BelongsTo
    {
        return $this->belongsTo(Montaje::class);
    }

    /**
     * Obtiene la URL pública de la fotografía sanitizada.
     */
    public function getImageUrlAttribute(): string
    {
        if (empty($this->image_path)) {
            return asset('images/montajes/teno-mesa-principal.jpg');
        }

        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        if (str_starts_with($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        return asset('storage/'.ltrim($this->image_path, '/'));
    }
}
