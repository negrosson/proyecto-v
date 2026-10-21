<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\ProductCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Comprueba que el seeder pueble el catálogo completo de forma consistente,
     * sin corrupción de datos, con corrección de columnas invertidas e idempotencia.
     */
    public function test_seeder_populates_catalog_without_data_corruption(): void
    {
        // 1. Ejecución inicial del seeder
        $this->seed(ProductCatalogSeeder::class);

        $productCount = Product::count();

        // 2. Verificar que existan más de 100 productos creados
        $this->assertGreaterThan(100, $productCount);

        // 3. Verificar que ningún producto tenga rental_price <= 0 ni stock < 0
        $invalidPriceCount = Product::where('rental_price', '<=', 0)->count();
        $this->assertSame(0, $invalidPriceCount, 'Existen productos con rental_price menor o igual a 0');

        $invalidStockCount = Product::where('stock', '<', 0)->count();
        $this->assertSame(0, $invalidStockCount, 'Existen productos con stock negativo');

        // 4. Verificar la corrección de columnas invertidas en el producto código 1160 (Picadora de hielo)
        $iceCrusher = Product::where('code', 1160)->first();
        $this->assertNotNull($iceCrusher, 'El producto código 1160 no fue encontrado');
        $this->assertLessThanOrEqual(5, $iceCrusher->stock);
        $this->assertSame(1, $iceCrusher->stock);
        $this->assertGreaterThanOrEqual(10000, $iceCrusher->rental_price);
        $this->assertSame(10000, $iceCrusher->rental_price);

        // Verificar correcciones críticas adicionales (códigos 1260, 1285 y 1289)
        $industrialOven = Product::where('code', 1260)->first();
        $this->assertNotNull($industrialOven);
        $this->assertSame(2, $industrialOven->stock);
        $this->assertSame(30000, $industrialOven->rental_price);

        $tentRental = Product::where('code', 1285)->first();
        $this->assertNotNull($tentRental);
        $this->assertSame(2, $tentRental->stock);
        $this->assertSame(400000, $tentRental->rental_price);

        $tentSale = Product::where('code', 1289)->first();
        $this->assertNotNull($tentSale);
        $this->assertSame(1, $tentSale->stock);
        $this->assertSame(4000000, $tentSale->rental_price);
        $this->assertTrue($tentSale->is_sale);

        // 5. Verificar que todos los productos tengan asociada una categoría válida
        $productsWithoutCategory = Product::whereNull('category_id')->count();
        $this->assertSame(0, $productsWithoutCategory, 'Existen productos sin category_id');

        $validCategoryIds = Category::pluck('id')->all();
        $productsWithInvalidCategory = Product::whereNotIn('category_id', $validCategoryIds)->count();
        $this->assertSame(0, $productsWithInvalidCategory, 'Existen productos con categoría inexistente');

        // Comprobar las 5 categorías canónicas
        $canonicalSlugs = ['vajilla', 'manteleria', 'mobiliario', 'equipamiento-cocina', 'bar-decoracion'];
        foreach ($canonicalSlugs as $slug) {
            $this->assertDatabaseHas('categories', ['slug' => $slug]);
        }

        // 6. Verificar idempotencia: correr el seeder dos veces consecutivas no duplica registros
        $this->seed(ProductCatalogSeeder::class);

        $this->assertSame($productCount, Product::count(), 'El seeder duplicó registros al ejecutarse una segunda vez');
        $this->assertSame(5, Category::count(), 'El seeder duplicó categorías canónicas');
    }
}
