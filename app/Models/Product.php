<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

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
}
