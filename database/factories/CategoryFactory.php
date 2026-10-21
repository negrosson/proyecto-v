<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Category>
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array{name: string, slug: string}
     */
    public function definition(): array
    {
        $categories = [
            'Mobiliario y Livings',
            'Vajilla y Porcelana',
            'Cristalería y Copas',
            'Cuchillería y Cubiertos',
            'Mantelería y Caminos',
            'Iluminación y Guirnaldas',
            'Carpas y Estructuras',
            'Pistas de Baile y Tarimas',
            'Barras y Coctelería',
            'Calefacción para Terrazas',
            'Decoración y Centros de Mesa',
            'Equipos de Buffet y Samovares',
            'Sillas Chiavari y Crossback',
            'Mesas Redondas y Tablones',
            'Sillones y Juegos de Terraza',
            'Copas de Vino y Champaña',
            'Cuchillería Acero Inoxidable',
            'Manteles de Lino y Terciopelo',
            'Focos LED y Efectos Vintage',
            'Toldos Plegables y Sombrillas',
            'Pérgolas y Toldos Beduinos',
            'Chafing Dish y Calentadores',
            'Hieleras y Enfriadores',
            'Candelabros y Velas',
            'Pistas de Baile LED',
        ];

        $name = fake()->unique()->randomElement($categories);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }

    /**
     * State for furniture category.
     */
    public function mobiliario(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);
    }

    /**
     * State for tableware category.
     */
    public function vajilla(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Vajilla',
            'slug' => 'vajilla',
        ]);
    }

    /**
     * State for tablecloth category.
     */
    public function manteleria(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Mantelería',
            'slug' => 'manteleria',
        ]);
    }

    /**
     * State for glassware category.
     */
    public function cristaleria(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cristalería',
            'slug' => 'cristaleria',
        ]);
    }
}
