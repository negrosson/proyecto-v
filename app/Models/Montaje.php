<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Montaje extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'montajes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'client',
        'type',
        'year',
        'location',
        'capacity',
        'summary',
        'description',
        'cover_image',
        'gallery',
        'items_used',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'items_used' => 'array',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Mutator para asegurar slug limpio si no se provee.
     */
    protected static function booted(): void
    {
        static::saving(function (self $montaje): void {
            if (empty($montaje->slug) && ! empty($montaje->title)) {
                $montaje->slug = Str::slug($montaje->title);
            }
        });
    }

    /**
     * URL absoluta de la imagen de portada.
     */
    public function getCoverImageUrlAttribute(): string
    {
        if (empty($this->cover_image)) {
            return asset('images/montajes/teno-mesa-principal.jpg');
        }

        if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://')) {
            return $this->cover_image;
        }

        if (str_starts_with($this->cover_image, 'images/')) {
            return asset($this->cover_image);
        }

        return asset('storage/'.ltrim($this->cover_image, '/'));
    }

    /**
     * Colección de URLs absolutas para la galería fotográfica.
     *
     * @return array<int, string>
     */
    public function getGalleryUrlsAttribute(): array
    {
        $gallery = $this->gallery;

        if (! is_array($gallery) || empty($gallery)) {
            return [$this->cover_image_url];
        }

        return array_map(function (string $path): string {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            if (str_starts_with($path, 'images/')) {
                return asset($path);
            }

            return asset('storage/'.ltrim($path, '/'));
        }, $gallery);
    }
}
