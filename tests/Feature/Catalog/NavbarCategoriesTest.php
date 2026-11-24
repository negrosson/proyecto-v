<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_renders_direct_category_links_on_all_public_pages(): void
    {
        $vajilla = Category::factory()->create([
            'name' => 'Vajilla',
            'slug' => 'vajilla',
        ]);

        $mobiliario = Category::factory()->create([
            'name' => 'Mobiliario',
            'slug' => 'mobiliario',
        ]);

        Product::factory()->count(3)->create([
            'category_id' => $vajilla->id,
            'is_active' => true,
        ]);

        Product::factory()->count(2)->create([
            'category_id' => $mobiliario->id,
            'is_active' => true,
        ]);

        foreach ([
            route('catalog.index'),
            route('catalog.category', $vajilla->slug),
            route('catalog.montajes'),
            route('catalog.como-cotizar'),
        ] as $url) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertSee(route('catalog.category', $vajilla->slug).'#catalogo', false);
            $response->assertSee(route('catalog.category', $mobiliario->slug).'#catalogo', false);
            $response->assertSee('Ver Catálogo Completo');
            $response->assertSee('Vajilla');
            $response->assertSee('Mobiliario');
        }
    }
}
