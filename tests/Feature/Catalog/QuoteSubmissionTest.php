<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuoteSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Comprueba que un usuario envíe una cotización válida y reciba la URL de WhatsApp en JSON (HTTP 200).
     */
    public function test_user_can_submit_quote_and_receives_whatsapp_url(): void
    {
        $category = Category::factory()->create();

        $chair = Product::factory()->create([
            'category_id' => $category->id,
            'code' => 1010,
            'name' => 'Silla Chiavari Dorada',
            'rental_price' => 3500,
            'stock' => 50,
            'is_active' => true,
        ]);

        $table = Product::factory()->create([
            'category_id' => $category->id,
            'code' => 2020,
            'name' => 'Mesa Redonda 1.80m',
            'rental_price' => 12000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Carla Pizarro',
            'event_date' => '2026-11-28',
            'location' => 'La Reina, Santiago',
            'items' => [
                [
                    'product_id' => $chair->id,
                    'quantity' => 20,
                ],
                [
                    'product_id' => $table->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson(route('quote.store'), $payload);

        $response->assertOk();
        $response->assertJsonStructure(['whatsapp_url']);

        $whatsappUrl = $response->json('whatsapp_url');
        $this->assertIsString($whatsappUrl);
        $this->assertStringStartsWith('https://wa.me/', $whatsappUrl);

        $decodedMessage = rawurldecode(parse_url($whatsappUrl, PHP_URL_QUERY) ?? '');
        $this->assertStringContainsString('Carla Pizarro', $decodedMessage);
        $this->assertStringContainsString('La Reina, Santiago', $decodedMessage);
        $this->assertStringContainsString('2026-11-28', $decodedMessage);
        $this->assertStringContainsString('20x Silla Chiavari Dorada ($70.000)', $decodedMessage);
        $this->assertStringContainsString('2x Mesa Redonda 1.80m ($24.000)', $decodedMessage);
        $this->assertStringContainsString('*TOTAL ESTIMADO:* $94.000', $decodedMessage);
    }

    /**
     * Comprueba que fallen con HTTP 422 peticiones a las que les falte nombre o la lista de productos.
     */
    public function test_quote_submission_fails_if_required_fields_are_missing(): void
    {
        // 1. Petición sin customer_name
        $responseNoName = $this->postJson(route('quote.store'), [
            'location' => 'Providencia',
            'items' => [
                ['product_id' => 1, 'quantity' => 1],
            ],
        ]);

        $responseNoName->assertStatus(422);
        $responseNoName->assertJsonValidationErrors(['customer_name']);

        // 2. Petición sin items
        $responseNoItems = $this->postJson(route('quote.store'), [
            'customer_name' => 'Juan Carlos',
            'location' => 'Las Condes',
            'items' => [],
        ]);

        $responseNoItems->assertStatus(422);
        $responseNoItems->assertJsonValidationErrors(['items']);

        // 3. Petición sin location
        $responseNoLocation = $this->postJson(route('quote.store'), [
            'customer_name' => 'Juan Carlos',
            'items' => [
                ['product_id' => 1, 'quantity' => 1],
            ],
        ]);

        $responseNoLocation->assertStatus(422);
        $responseNoLocation->assertJsonValidationErrors(['location']);
    }

    /**
     * Comprueba que retorne error de validación HTTP 422 si la cantidad solicitada supera el stock disponible.
     */
    public function test_quote_submission_fails_if_quantity_exceeds_available_stock(): void
    {
        $category = Category::factory()->create();

        $limitedStockProduct = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Estufa Exterior a Gas',
            'stock' => 3,
            'rental_price' => 45000,
        ]);

        $payload = [
            'customer_name' => 'Andrés Bello',
            'event_date' => '2026-12-10',
            'location' => 'Chicureo',
            'items' => [
                [
                    'product_id' => $limitedStockProduct->id,
                    'quantity' => 10, // Stock real es 3
                ],
            ],
        ];

        $response = $this->postJson(route('quote.store'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items.0.quantity']);
    }
}
