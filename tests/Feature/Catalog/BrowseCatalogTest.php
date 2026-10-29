<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrowseCatalogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Comprueba que un visitante vea productos activos y reciba status 200.
     */
    public function test_can_browse_active_products_in_catalog(): void
    {
        $category = Category::factory()->create([
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Silla Chiavari Dorada',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mesa Redonda 1.80m',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertViewIs('catalog.index');
        $response->assertViewHas('products');
        $response->assertViewHas('categories');
        $response->assertSee('Silla Chiavari Dorada');
        $response->assertSee('Mesa Redonda 1.80m');
    }

    /**
     * Comprueba que productos con is_active = false nunca aparezcan en la vista.
     */
    public function test_inactive_products_are_hidden_from_catalog(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Silla Activa Visible',
            'is_active' => true,
        ]);

        Product::factory()->inactive()->create([
            'category_id' => $category->id,
            'name' => 'Silla Descontinuada Inactiva',
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertSee('Silla Activa Visible');
        $response->assertDontSee('Silla Descontinuada Inactiva');
    }

    /**
     * Comprueba que al pasar ?categoria=slug solo se rendericen los productos de esa categoría.
     */
    public function test_can_filter_products_by_category_slug(): void
    {
        $furnitureCategory = Category::factory()->create([
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);

        $glasswareCategory = Category::factory()->create([
            'name' => 'Cristalería',
            'slug' => 'cristaleria',
        ]);

        Product::factory()->create([
            'category_id' => $furnitureCategory->id,
            'name' => 'Silla Crossback Madera',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $glasswareCategory->id,
            'name' => 'Copa Flauta Champaña 210ml',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.index', ['categoria' => 'mobiliario']));

        $response->assertOk();
        $response->assertSee('Silla Crossback Madera');
        $response->assertDontSee('Copa Flauta Champaña 210ml');
    }

    /**
     * Comprueba la búsqueda por coincidencia parcial de texto.
     */
    public function test_can_search_products_by_name(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Silla Tiffany Blanca',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mesa Tablón Madera 2.40m',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mantel Brocado Blanco 3m',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.index', ['buscar' => 'Tiffany']));

        $response->assertOk();
        $response->assertSee('Silla Tiffany Blanca');
        $response->assertDontSee('Mesa Tablón Madera 2.40m');
        $response->assertDontSee('Mantel Brocado Blanco 3m');
    }

    /**
     * Comprueba la búsqueda por el código numérico del producto.
     */
    public function test_can_search_products_by_exact_code(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'code' => 1030,
            'name' => 'Silla Modelo Especial',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'code' => 2050,
            'name' => 'Mesa Modelo Especial',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.index', ['buscar' => '1030']));

        $response->assertOk();
        $response->assertSee('Silla Modelo Especial');
        $response->assertDontSee('Mesa Modelo Especial');
    }

    /**
     * Comprueba que búsquedas con comillas o caracteres raros no rompan la consulta.
     */
    public function test_search_sanitizes_or_escapes_special_characters(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Producto Prueba Especial',
            'is_active' => true,
        ]);

        // Intentos de inyección SQL o comodines de consulta LIKE
        $maliciousSearches = [
            "' OR '1'='1",
            "%' OR ''='",
            '%',
            '_',
            '\\',
            '"><script>alert("xss")</script>',
            '--',
            '; DROP TABLE products; --',
        ];

        foreach ($maliciousSearches as $search) {
            $response = $this->get(route('catalog.index', ['buscar' => $search]));

            $response->assertOk();
            $response->assertDontSee('SQLSTATE');
            $response->assertViewHas('products');
        }
    }

    /**
     * Comprueba que la vista principal cargue productos para el carrusel y la sección de montajes.
     */
    public function test_catalog_homepage_loads_featured_and_showcase_collections(): void
    {
        $furnitureCategory = Category::factory()->create([
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);

        $featuredProduct = Product::factory()->featured()->create([
            'category_id' => $furnitureCategory->id,
            'name' => 'Silla Chiavari Imperial Dorada',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertViewHas('featuredProducts');
        $response->assertViewHas('showcaseProducts');
        $response->assertViewHas('carouselProducts');
        $response->assertViewHas('montajes');
        $response->assertSee('Silla Chiavari Imperial Dorada');
        $response->assertSee('Vitrina en Carrusel');
        $response->assertSee('T E N O');
        $response->assertSee('Montaje de Eventos en Santiago');
        $response->assertSee(route('catalog.montajes'));
    }

    /**
     * Comprueba que la vista dedicada de montajes cargue correctamente con el portafolio de trabajos.
     */
    public function test_can_browse_dedicated_montajes_page(): void
    {
        $response = $this->get(route('catalog.montajes'));

        $response->assertOk();
        $response->assertViewIs('catalog.montajes');
        $response->assertViewHas('montajes');
        $response->assertSee('T E N O   2 0 2 3');
        $response->assertSee('Agrozzi');
        $response->assertSee('Montaje de Eventos en Santiago');
        $response->assertSee('Gala Nocturna &amp; Ambientación Dorada', false);
    }
}
