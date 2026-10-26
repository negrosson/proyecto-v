<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicSettingsAndThemeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que al cambiar el número de WhatsApp en SiteSetting se actualice de forma dinámica e instantánea la URL de cotización.
     */
    public function test_changing_whatsapp_number_in_site_settings_dynamically_updates_quote_url(): void
    {
        $settings = SiteSetting::current();
        $settings->whatsapp_phone = '+56 9 9887 7665';
        $settings->save();

        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'code' => 101,
            'name' => 'Silla Tiffany Blanco Puro',
            'rental_price' => 2500,
            'stock' => 50,
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Valentina Soto',
            'event_date' => '2026-12-15',
            'location' => 'Las Condes, Santiago',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                ],
            ],
        ];

        $response = $this->postJson(route('quote.store'), $payload);

        $response->assertOk();
        $whatsappUrl = $response->json('whatsapp_url');
        $this->assertIsString($whatsappUrl);
        $this->assertStringStartsWith('https://wa.me/56998877665?text=', $whatsappUrl);

        // Actualizar SiteSetting a otro número
        $settings->whatsapp_phone = '+56 9 1122 3344';
        $settings->save();

        $response2 = $this->postJson(route('quote.store'), $payload);

        $response2->assertOk();
        $whatsappUrl2 = $response2->json('whatsapp_url');
        $this->assertIsString($whatsappUrl2);
        $this->assertStringStartsWith('https://wa.me/56911223344?text=', $whatsappUrl2);
    }

    /**
     * Verifica que el catálogo público renderice los teléfonos limpios y que el enlace de FAQ esté en el footer.
     */
    public function test_public_catalog_renders_updated_contact_phones_and_faq_link_in_footer(): void
    {
        $settings = SiteSetting::current();
        $settings->site_name = 'Arriendos Exclusivos';
        $settings->whatsapp_phone = '+56 9 9887 7665';
        $settings->company_phone = '+56 2 2334 5566';
        $settings->save();

        $response = $this->get(route('catalog.index'));

        $response->assertOk();

        // Verifica que los enlaces wa.me/56998877665 aparezcan en el HTML renderizado
        $response->assertSee('wa.me/56998877665');

        // Verifica que el enlace a #preguntas-frecuentes se encuentre dentro del <footer>
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('<footer', $content);

        $footerPart = substr($content, strpos($content, '<footer'));
        $footerEndPos = strpos($footerPart, '</footer>');
        $this->assertNotFalse($footerEndPos);
        $footerHtml = substr($footerPart, 0, $footerEndPos + 9);

        $this->assertStringContainsString('#preguntas-frecuentes', $footerHtml);
    }
}
