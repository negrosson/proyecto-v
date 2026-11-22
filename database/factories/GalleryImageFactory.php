<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryImage>
 */
class GalleryImageFactory extends Factory
{
    protected $model = GalleryImage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Fotografía de Evento '.fake()->numberBetween(1, 500),
            'category' => 'Sin clasificar',
            'image_path' => 'images/montajes/teno-mesa-principal.jpg',
            'original_name' => 'sample.jpg',
            'file_hash' => fake()->sha256(),
            'montaje_id' => null,
            'notes' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
