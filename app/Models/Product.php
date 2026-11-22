<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Media\PublicMediaCleaner;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saved(static function (): void {
            Cache::forget('filament_badge_products_count');
        });

        static::updated(static function (self $product): void {
            if ($product->wasChanged('image')) {
                $oldImage = $product->getOriginal('image');
                PublicMediaCleaner::deleteIfUnreferenced(is_string($oldImage) ? $oldImage : null);
            }
        });

        static::deleted(static function (self $product): void {
            Cache::forget('filament_badge_products_count');
            PublicMediaCleaner::deleteIfUnreferenced($product->image);
        });
    }

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'rental_price',
        'stock',
        'image',
        'is_active',
        'is_sale',
        'is_featured',
        'clicks_count',
        'quoted_count',
        'units_quoted_count',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'code' => 'integer',
            'rental_price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_sale' => 'boolean',
            'is_featured' => 'boolean',
            'clicks_count' => 'integer',
            'quoted_count' => 'integer',
            'units_quoted_count' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Obtiene la URL completa de la fotografía o render 3D del producto.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        return asset('storage/'.$this->image);
    }

    /**
     * Indica si el producto utiliza una miniatura comercial de estudio por defecto.
     */
    public function usesFallbackImage(): bool
    {
        return empty($this->image);
    }

    /**
     * Resuelve la miniatura comercial de estudio (fondo blanco hotelero) según el tipo de artículo y categoría.
     */
    public function resolveFallbackImageUrl(): string
    {
        $normalizedName = Str::upper(Str::ascii((string) $this->name));
        $categorySlug = $this->category?->slug ?? '';

        if (str_contains($normalizedName, 'PLATO BASE')
            || str_contains($normalizedName, 'PLATO DORADO')
            || str_contains($normalizedName, 'PLATO PLAQUET')) {
            return asset('images/products/default-plato-base.webp');
        }

        if (str_contains($normalizedName, 'TENEDOR')
            || str_contains($normalizedName, 'CUCHILLO')
            || str_contains($normalizedName, 'CUCHARA')
            || str_contains($normalizedName, 'PALETA DE TORTA')
            || str_contains($normalizedName, 'PALETA PARA ENSALADAS')
            || str_contains($normalizedName, 'PINZA METAL')) {
            return str_contains($normalizedName, 'DORAD')
                ? asset('images/products/default-cubiertos-dorados.webp')
                : asset('images/products/default-cubiertos.webp');
        }

        if (str_contains($normalizedName, 'COPA')) {
            return asset('images/products/default-copas.webp');
        }

        if (str_contains($normalizedName, 'VASO') || str_contains($normalizedName, 'SHOT')) {
            return asset('images/products/default-vasos.webp');
        }

        if ($categorySlug === 'vajilla'
            && (str_contains($normalizedName, 'PLATO')
                || str_contains($normalizedName, 'POCILLO')
                || str_contains($normalizedName, 'ENSALADERA')
                || str_contains($normalizedName, 'REMEKEN')
                || str_contains($normalizedName, 'TAZA')
                || str_contains($normalizedName, 'AZUCARERO')
                || str_contains($normalizedName, 'CAFETERO')
                || str_contains($normalizedName, 'SALERO')
                || str_contains($normalizedName, 'PIMENTERO')
                || str_contains($normalizedName, 'ALCUZA')
                || str_contains($normalizedName, 'PANERA'))) {
            return asset('images/products/default-platos.webp');
        }

        return match ($categorySlug) {
            'vajilla' => asset('images/products/default-platos.webp'),
            'manteleria' => asset('images/products/default-manteleria.webp'),
            'mobiliario' => asset('images/products/default-mobiliario.webp'),
            'equipamiento-cocina' => asset('images/products/default-equipamiento-cocina.webp'),
            'bar-decoracion' => asset('images/products/default-bar-decoracion.webp'),
            default => asset('images/products/default-platos.webp'),
        };
    }

    /**
     * Obtiene la URL final para mostrar en catálogo y paneles (foto propia o miniatura comercial de estudio).
     */
    public function getDisplayImageUrlAttribute(): string
    {
        return $this->image_url ?? $this->resolveFallbackImageUrl();
    }
}
