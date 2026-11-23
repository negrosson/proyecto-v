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
     * Archivos de estudio con fondo blanco puro (#FFFFFF) que deben mostrarse con object-contain.
     *
     * @var list<string>
     */
    private const WHITE_STUDIO_PACKSHOTS = [
        'default-platos.webp',
        'default-plato-base.webp',
        'default-vajilla.webp',
        'default-cubiertos.webp',
        'default-cubiertos-dorados.webp',
        'default-copas.webp',
        'default-vasos.webp',
        'default-manteleria.webp',
        'default-mobiliario.webp',
        'default-bar-decoracion.webp',
        'default-arrocera.webp',
        'copa-vino.jpg',
        'silla-chiavari-dorada.jpg',
    ];

    /**
     * Obtiene la URL completa de la fotografía o render del producto.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        $normalizedName = Str::upper(Str::ascii((string) $this->name));

        // Sobrescribe referencias heredadas de seeders cuando existe una foto más específica para esa pieza.
        if ($this->image === 'images/products/copa-vino.jpg' && str_contains($normalizedName, 'BRINDIS')) {
            return asset('images/products/catalog-copa-brindis.webp');
        }

        if ($this->image === 'images/products/mesa-banquete.jpg' && str_contains($normalizedName, 'NOVIOS')) {
            return asset('images/products/catalog-mesa-novios.webp');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://') || str_starts_with($this->image, 'images/')) {
            return asset($this->image);
        }

        return asset('storage/'.$this->image);
    }

    /**
     * Indica si el producto utiliza una miniatura por defecto (sin fotografía propia subida).
     */
    public function usesFallbackImage(): bool
    {
        return empty($this->image);
    }

    /**
     * Indica si la imagen final es un packshot recortado sobre fondo blanco puro (#FFFFFF).
     */
    public function usesWhiteStudioBackground(): bool
    {
        $filename = basename((string) parse_url($this->display_image_url, PHP_URL_PATH));

        return in_array($filename, self::WHITE_STUDIO_PACKSHOTS, true);
    }

    /**
     * Resuelve una fotografía coherente según el tipo específico de artículo y su categoría.
     */
    public function resolveFallbackImageUrl(): string
    {
        $normalizedName = Str::upper(Str::ascii((string) $this->name));
        $categorySlug = $this->category?->slug ?? '';

        // 1. Vajilla, Cristalería y Cubertería
        if (str_contains($normalizedName, 'ALCUZA')) {
            return asset('images/products/articulo-alcuza-completa.webp');
        }

        if (str_contains($normalizedName, 'TAZA')) {
            return asset('images/products/articulo-taza-cafe.webp');
        }

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
                ? asset('images/products/articulo-cubiertos-dorados.webp')
                : asset('images/products/default-cubiertos.webp');
        }

        if (str_contains($normalizedName, 'COPA') && str_contains($normalizedName, 'BRINDIS')) {
            return asset('images/products/catalog-copa-brindis.webp');
        }

        if (str_contains($normalizedName, 'COPA')) {
            return asset('images/products/default-copas.webp');
        }

        if (str_contains($normalizedName, 'VASO') || str_contains($normalizedName, 'SHOT')) {
            return asset('images/products/articulo-vaso-vidrio.webp');
        }

        if ($categorySlug === 'vajilla'
            && (str_contains($normalizedName, 'PLATO')
                || str_contains($normalizedName, 'POCILLO')
                || str_contains($normalizedName, 'ENSALADERA')
                || str_contains($normalizedName, 'REMEKEN')
                || str_contains($normalizedName, 'AZUCARERO')
                || str_contains($normalizedName, 'CAFETERO')
                || str_contains($normalizedName, 'SALERO')
                || str_contains($normalizedName, 'PIMENTERO')
                || str_contains($normalizedName, 'PANERA'))) {
            return asset('images/products/default-platos.webp');
        }

        // 2. Equipamiento de Cocina & Banquetería Industrial
        if ($categorySlug === 'equipamiento-cocina') {
            if (str_contains($normalizedName, 'ARROCERA')) {
                return asset('images/products/articulo-arrocera-15kg.webp');
            }

            if (str_contains($normalizedName, 'FONDO GRANDE')) {
                return asset('images/products/default-arrocera.webp');
            }

            if (str_contains($normalizedName, 'BANDEJA HORNO')
                || str_contains($normalizedName, 'BUDINERA')
                || str_contains($normalizedName, 'TABLA PICAR')) {
                return asset('images/products/catalog-bandeja-horno.webp');
            }

            if (str_contains($normalizedName, 'HORNO')) {
                return asset('images/products/catalog-horno-industrial.webp');
            }

            if (str_contains($normalizedName, 'JUGUERA') || str_contains($normalizedName, 'PICADORA DE HIELO')) {
                return asset('images/products/catalog-juguera.webp');
            }

            if (str_contains($normalizedName, 'PERCOLADORA')
                || str_contains($normalizedName, 'TERMO HERVIDOR')
                || str_contains($normalizedName, 'DISPENSADOR')
                || str_contains($normalizedName, 'CAMBRO')) {
                return asset('images/products/catalog-percoladora.webp');
            }

            if (str_contains($normalizedName, 'COOLER') || str_contains($normalizedName, 'HIELERA')) {
                return asset('images/products/catalog-cooler.webp');
            }

            return asset('images/products/catalog-chaffer.webp');
        }

        // 3. Bar, Ambientación y Decoración
        if ($categorySlug === 'bar-decoracion') {
            if (str_contains($normalizedName, 'ATRIL')) {
                return asset('images/products/articulo-atril-porta-letrero.webp');
            }

            if (str_contains($normalizedName, 'BANDEJA PLAQUET')) {
                return asset('images/products/articulo-bandeja-plaquet-tapa-vidrio.webp');
            }

            if (str_contains($normalizedName, 'BASURERO')) {
                return asset('images/products/articulo-basurero.webp');
            }

            if (str_contains($normalizedName, 'COCTELERA')) {
                return asset('images/products/articulo-coctelera.webp');
            }

            if (str_contains($normalizedName, 'DECANTADOR')) {
                return asset('images/products/articulo-decantador-vino.webp');
            }

            if (str_contains($normalizedName, 'FLORERO 20 CM CUADRADO')) {
                return asset('images/products/articulo-florero-20cm-cuadrado.webp');
            }

            if (str_contains($normalizedName, 'PORTA CERVEZA')) {
                return asset('images/products/articulo-porta-cerveza.webp');
            }

            if (str_contains($normalizedName, 'ARCO DE NOVIO') || str_contains($normalizedName, 'CORTINAJE')) {
                return asset('images/products/catalog-arco-novio.webp');
            }

            if (str_contains($normalizedName, 'CALEFACTOR')
                || str_contains($normalizedName, 'ESTUFA')
                || str_contains($normalizedName, 'PARAFINA')) {
                return asset('images/products/calefactor-gas.jpg');
            }

            if (str_contains($normalizedName, 'FLOR')
                || str_contains($normalizedName, 'PORTA VELA')
                || str_contains($normalizedName, 'PESCERA')
                || str_contains($normalizedName, 'PEDESTAL')
                || str_contains($normalizedName, 'JAULA DORADA')
                || str_contains($normalizedName, 'PLATO MADERA')
                || str_contains($normalizedName, 'PASTO ARTIFICIAL')) {
                return asset('images/products/catalog-decoracion-floral.webp');
            }

            if (str_contains($normalizedName, 'LUCES')
                || str_contains($normalizedName, 'MAQUINA HUMO')
                || str_contains($normalizedName, 'ENCHUFE')) {
                return asset('images/products/catalog-iluminacion.webp');
            }

            if (str_contains($normalizedName, 'BARTENDER')) {
                return asset('images/products/catalog-bartender.webp');
            }

            return asset('images/products/default-bar-decoracion.webp');
        }

        // 4. Mantelería, Caminos, Carpetas, Cubre Sillas y Lazos
        if ($categorySlug === 'manteleria') {
            if (str_contains($normalizedName, 'CAMINO')) {
                return asset('images/products/catalog-camino-mesa.webp');
            }

            if (str_contains($normalizedName, 'CARPETA')) {
                return asset('images/products/catalog-carpeta-mantel.webp');
            }

            if (str_contains($normalizedName, 'CUBRE SILLA') || str_contains($normalizedName, 'COJIN')) {
                return asset('images/products/catalog-cubre-silla.webp');
            }

            if (str_contains($normalizedName, 'LAZO')) {
                return asset('images/products/catalog-lazos-silla.webp');
            }

            return asset('images/products/default-manteleria.webp');
        }

        // 5. Mobiliario, Carpas y Lounge
        if ($categorySlug === 'mobiliario') {
            if (str_contains($normalizedName, 'ALFOMBRA')) {
                return asset('images/products/articulo-alfombra-roja.webp');
            }

            if (str_contains($normalizedName, 'CARPA')) {
                return asset('images/products/catalog-carpa-evento.webp');
            }

            if (str_contains($normalizedName, 'PUFF')) {
                return asset('images/products/catalog-puff-lounge.webp');
            }

            if (str_contains($normalizedName, 'NOVIOS')
                || str_contains($normalizedName, 'MESON BAR')
                || str_contains($normalizedName, 'TRIO MESA')
                || str_contains($normalizedName, 'MESA DE APOYO')) {
                return asset('images/products/catalog-mesa-novios.webp');
            }

            return asset('images/products/default-mobiliario.webp');
        }

        return match ($categorySlug) {
            'vajilla' => asset('images/products/default-platos.webp'),
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
