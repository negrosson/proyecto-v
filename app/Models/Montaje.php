<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Media\PublicMediaCleaner;
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

        static::updated(static function (self $montaje): void {
            if ($montaje->wasChanged('cover_image')) {
                $oldCover = $montaje->getOriginal('cover_image');
                PublicMediaCleaner::deleteIfUnreferenced(is_string($oldCover) ? $oldCover : null);
            }

            if ($montaje->wasChanged('gallery')) {
                $oldGallery = $montaje->getOriginal('gallery');
                if (is_string($oldGallery)) {
                    $decoded = json_decode($oldGallery, true);
                    $oldGallery = is_array($decoded) ? $decoded : [];
                }
                $oldPaths = is_array($oldGallery) ? $oldGallery : [];
                $newPaths = is_array($montaje->gallery) ? $montaje->gallery : [];

                foreach (array_diff($oldPaths, $newPaths) as $removedPath) {
                    if (is_string($removedPath)) {
                        PublicMediaCleaner::deleteIfUnreferenced($removedPath);
                    }
                }
            }
        });

        static::deleted(static function (self $montaje): void {
            PublicMediaCleaner::deleteIfUnreferenced($montaje->cover_image);

            if (is_array($montaje->gallery)) {
                foreach ($montaje->gallery as $path) {
                    if (is_string($path)) {
                        PublicMediaCleaner::deleteIfUnreferenced($path);
                    }
                }
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
     * Colección de URLs absolutas únicas para la galería fotográfica (incluyendo portada al inicio sin duplicados).
     *
     * @return array<int, string>
     */
    public function getGalleryUrlsAttribute(): array
    {
        $coverUrl = $this->cover_image_url;
        $gallery = $this->gallery;

        if (! is_array($gallery) || empty($gallery)) {
            return [$coverUrl];
        }

        $urls = array_map(function (string $path): string {
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            if (str_starts_with($path, 'images/')) {
                return asset($path);
            }

            return asset('storage/'.ltrim($path, '/'));
        }, array_filter($gallery, static fn ($item): bool => is_string($item) && $item !== ''));

        return array_values(array_unique([$coverUrl, ...$urls]));
    }
}
