<?php

declare(strict_types=1);

namespace Tests\Feature\Seo;

use App\Models\Category;
use App\Models\Faq;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dynamic_sitemap_xml_lists_public_pages_and_active_categories_only(): void
    {
        Cache::forget('seo.sitemap_xml');

        $activeCategory = Category::factory()->create([
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);
        Product::factory()->create([
            'category_id' => $activeCategory->id,
            'is_active' => true,
        ]);

        $emptyCategory = Category::factory()->create([
            'name' => 'Categoría Vacía',
            'slug' => 'categoria-vacia',
        ]);
        Product::factory()->create([
            'category_id' => $emptyCategory->id,
            'is_active' => false,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', (string) $response->headers->get('Content-Type'));

        $xml = simplexml_load_string((string) $response->getContent());
        $this->assertNotFalse($xml);

        $content = (string) $response->getContent();
        $this->assertStringContainsString(route('catalog.index'), $content);
        $this->assertStringContainsString(route('catalog.montajes'), $content);
        $this->assertStringContainsString(route('catalog.como-cotizar'), $content);
        $this->assertStringContainsString(route('catalog.category', 'mobiliario'), $content);
        $this->assertStringNotContainsString(route('catalog.category', 'categoria-vacia'), $content);
    }

    public function test_category_seo_landing_page_renders_dedicated_metadata_canonical_and_filtered_products(): void
    {
        $catMobiliario = Category::factory()->create([
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);
        $catVajilla = Category::factory()->create([
            'name' => 'Vajilla',
            'slug' => 'vajilla',
        ]);

        Product::factory()->create([
            'category_id' => $catMobiliario->id,
            'name' => 'Silla Tiffany Dorada SEO',
            'is_active' => true,
        ]);
        Product::factory()->create([
            'category_id' => $catVajilla->id,
            'name' => 'Plato Fondo Porcelana SEO',
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.category', 'mobiliario'));

        $response->assertOk();
        $response->assertSee('Arriendo de Mobiliario para Eventos en Santiago', false);
        $response->assertSee('<link rel="canonical" href="'.route('catalog.category', 'mobiliario').'">', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('"@type":"ItemList"', false);
        $response->assertSee('"@type":"Product"', false);
        $response->assertSee('"priceCurrency":"CLP"', false);
        $response->assertSee('Arriendo de Mobiliario para Eventos');
        $response->assertSee('Explora otras categorías en arriendo');
        $response->assertSee('Arriendo de Vajilla');
        $response->assertSee('Silla Tiffany Dorada SEO');

        // Landing dedicada de Vajilla debe mostrar copy propio (lavado incluido) y sin duplicar H1 de portada
        $vajillaResponse = $this->get(route('catalog.category', 'vajilla'));
        $vajillaResponse->assertOk();
        $vajillaResponse->assertSee('Arriendo de Vajilla, Platos, Copas y Cubiertos para Eventos en Santiago', false);
        $vajillaResponse->assertSee('Arriendo de Vajilla y Cristalería');
        $vajillaResponse->assertSee('Lavado y sanitización profesional 100% incluidos');

        // Categoría inexistente debe retornar 404 limpio
        $this->get('/arriendo/no-existe-esta-categoria')->assertNotFound();
    }

    public function test_public_pages_include_canonical_es_cl_locale_and_valid_json_ld_schemas(): void
    {
        Faq::query()->create([
            'category' => 'Despacho y Logística',
            'question' => '¿Incluyen despacho en Santiago?',
            'answer' => 'Sí, despachamos en toda la Región Metropolitana.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        // 1. Portada
        $home = $this->get('/?buscar=silla');
        $home->assertOk();
        $home->assertSee('<html lang="es-CL"', false);
        $home->assertSee('<meta property="og:locale" content="es_CL">', false);
        $home->assertSee('<link rel="canonical" href="'.route('catalog.index').'">', false);
        $home->assertSee('"@type":"LocalBusiness"', false);
        $home->assertSee('"@type":"WebSite"', false);
        $home->assertSee('"@type":"FAQPage"', false);

        // 2. Montajes
        $montajes = $this->get(route('catalog.montajes'));
        $montajes->assertOk();
        $montajes->assertSee('<link rel="canonical" href="'.route('catalog.montajes').'">', false);
        $montajes->assertSee('"@type":"BreadcrumbList"', false);

        // 3. Cómo Cotizar
        $guide = $this->get(route('catalog.como-cotizar'));
        $guide->assertOk();
        $guide->assertSee('<link rel="canonical" href="'.route('catalog.como-cotizar').'">', false);
        $guide->assertSee('"@type":"BreadcrumbList"', false);
    }

    public function test_robots_txt_and_indexnow_key_file_exist_and_are_valid(): void
    {
        $robots = (string) file_get_contents(public_path('robots.txt'));
        $this->assertStringContainsString('Sitemap: https://arriendomobiliario.cl/sitemap.xml', $robots);
        $this->assertStringContainsString('Disallow: /catalogo/buscar', $robots);

        $indexNowPath = public_path('a9f4c2e7b1d8405fa3e6c9b2d7148e05.txt');
        $this->assertFileExists($indexNowPath);
        $this->assertSame('a9f4c2e7b1d8405fa3e6c9b2d7148e05', trim((string) file_get_contents($indexNowPath)));
    }
}
