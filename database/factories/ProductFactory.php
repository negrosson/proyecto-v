<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Product>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            'Silla Chiavari Dorada',
            'Silla Crossback Madera Rústica',
            'Silla Tiffany Blanca',
            'Silla Plegable Acolchada Negra',
            'Mesa Redonda 1.80m (10 personas)',
            'Mesa Tablón Rectangular Madera 2.40m',
            'Mesa de Bar Alta Cocktail con 2 Pisos',
            'Sillón Lounge Modular 2 Cuerpos Blanco',
            'Puff Cuadrado Ecocuero Blanco 40x40',
            'Plato Base Dorado Labrado 33cm',
            'Plato Base Plateado Acero 33cm',
            'Plato Trinche Porcelana Hotelera Blanca 27cm',
            'Plato Hondo para Sopa/Crema 23cm',
            'Plato Postre Porcelana Blanca 19cm',
            'Copa Vino Tinto Cabernet 450ml',
            'Copa Vino Blanco Sauvignon 350ml',
            'Copa Flauta Champaña 210ml',
            'Vaso Old Fashioned Whisky 300ml',
            'Vaso Highball Bebidas y Tragos 350ml',
            'Tenedor Principal Acero Inoxidable 18/10',
            'Cuchillo Principal Sierra Suave Acero Inox',
            'Cuchara Sopera Acero Inoxidable 18/10',
            'Cucharita Postre / Café Acero Inox',
            'Mantel Redondo Brocado Blanco 3.00m',
            'Mantel Rectangular Lino Gris Plomo 2.50x3.00m',
            'Camino de Mesa Gasa Terracota 2.20m',
            'Servilleta de Lino Crudo 45x45cm',
            'Guirnalda de Luces Vintage Cálida 15m',
            'Foco LED RGB Perimetral 50W con Control',
            'Estufa Patio a Gas Exterior Tipo Hongo 13kW',
            'Pista de Baile Ajedrezada Blanca y Negra 4x4m',
            'Barra Móvil Plegable Madera y Acero 1.60m',
            'Chafing Dish Samovar Acero Inox 9 Litros',
            'Hielera con Pinzas de Acero Inoxidable 5L',
            'Candelabro de Mesa Hierro Forjado 5 Velas',
            'Sombrilla Terraza Madera con Base 3m',
        ];

        $descriptions = [
            'Estructura de alta calidad, resistente y elegante. Ideal para matrimonios, recepciones y celebraciones de gala.',
            'Excelente acabado y presentación para banquetería de primer nivel. Se entrega sanitizado y empacado.',
            'Diseño versátil apto para ambientaciones de interior y exterior bajo carpa. Fácil manipulación.',
            'Material duradero y de fácil limpieza. Cumple con los estándares más exigentes para eventos corporativos y bodas.',
            'Elegancia y funcionalidad para montajes de alta concurrencia. Incluye accesorios correspondientes.',
        ];

        // Precios realistas de arriendo unitario en CLP (pesos chilenos)
        $clpPrices = [
            1200, 1500, 1800, 2200, 2500, 3200, 3800, 4500, 5500, 7500,
            9500, 12000, 15000, 18500, 22000, 28000, 35000, 45000, 65000,
        ];

        return [
            'category_id' => Category::factory(),
            'code' => fake()->unique()->numberBetween(100, 99999),
            'name' => fake()->randomElement($products),
            'description' => fake()->randomElement($descriptions),
            'rental_price' => fake()->randomElement($clpPrices),
            'stock' => fake()->numberBetween(10, 250),
            'image' => 'products/'.fake()->slug(2).'.jpg',
            'is_active' => true,
            'is_sale' => false,
            'is_featured' => false,
        ];
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product is inactive in the catalog.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the product is active in the catalog.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the product is for direct sale rather than rental.
     */
    public function forSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sale' => true,
        ]);
    }

    /**
     * Indicate that the product is for rental.
     */
    public function forRental(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sale' => false,
        ]);
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }
}
