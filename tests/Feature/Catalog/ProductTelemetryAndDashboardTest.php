<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Filament\Widgets\BusinessStatsOverview;
use App\Filament\Widgets\ExecutiveOverviewWidget;
use App\Filament\Widgets\LatestQuotesWidget;
use App\Filament\Widgets\ProductInterestRankingWidget;
use App\Models\Category;
use App\Models\Product;
use App\Models\QuoteLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTelemetryAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Comprueba que hacer POST a /catalogo/productos/{product}/click incremente clicks_count en 1 y retorne HTTP 200.
     */
    public function test_it_tracks_product_click_and_increments_counter(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'clicks_count' => 5,
            'is_active' => true,
        ]);

        $response = $this->postJson(route('catalog.track-click', $product));

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'clicks_count' => 6,
        ]);

        $this->assertSame(6, (int) $product->fresh()->clicks_count);
    }

    /**
     * 2. Comprueba que enviar una cotización válida a POST /cotizacion cree un registro en quote_logs
     * con el monto exacto en CLP e incremente quoted_count y units_quoted_count en los productos.
     */
    public function test_quote_submission_records_quote_log_and_increments_product_telemetry(): void
    {
        $category = Category::factory()->create();

        $chair = Product::factory()->create([
            'category_id' => $category->id,
            'code' => 1010,
            'name' => 'Silla Chiavari Dorada',
            'rental_price' => 3500,
            'stock' => 50,
            'quoted_count' => 0,
            'units_quoted_count' => 0,
            'is_active' => true,
        ]);

        $table = Product::factory()->create([
            'category_id' => $category->id,
            'code' => 2020,
            'name' => 'Mesa Redonda 1.80m',
            'rental_price' => 12000,
            'stock' => 10,
            'quoted_count' => 2,
            'units_quoted_count' => 4,
            'is_active' => true,
        ]);

        $payload = [
            'customer_name' => 'Valentina Rossi',
            'event_date' => '2026-12-15',
            'location' => 'Las Condes, Santiago',
            'items' => [
                [
                    'product_id' => $chair->id,
                    'quantity' => 10, // 10 * 3.500 = 35.000
                ],
                [
                    'product_id' => $table->id,
                    'quantity' => 2,  // 2 * 12.000 = 24.000
                ],
            ],
        ];

        // Total esperado: 35.000 + 24.000 = 59.000 CLP, 12 piezas en total
        $response = $this->postJson(route('quote.store'), $payload);

        $response->assertOk();

        // Verificar registro en quote_logs
        $this->assertDatabaseHas('quote_logs', [
            'customer_name' => 'Valentina Rossi',
            'location' => 'Las Condes, Santiago',
            'total_amount' => 59000,
            'items_count' => 12,
            'status' => 'pendiente',
        ]);

        $quoteLog = QuoteLog::query()->where('customer_name', 'Valentina Rossi')->first();
        $this->assertNotNull($quoteLog);
        $this->assertSame('2026-12-15', $quoteLog->event_date?->format('Y-m-d'));
        $this->assertCount(2, $quoteLog->items_payload);
        $this->assertSame(1010, $quoteLog->items_payload[0]['code']);
        $this->assertSame(35000, $quoteLog->items_payload[0]['subtotal']);

        // Verificar contadores atómicos en los productos
        $chair->refresh();
        $this->assertSame(1, (int) $chair->quoted_count);
        $this->assertSame(10, (int) $chair->units_quoted_count);

        $table->refresh();
        $this->assertSame(3, (int) $table->quoted_count);
        $this->assertSame(6, (int) $table->units_quoted_count);
    }

    /**
     * 3. Comprueba que el Rate Limiter bloquee intentos masivos de inflar clics artificialmente con HTTP 429.
     */
    public function test_product_telemetry_rate_limiting(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        // Rate limiter configurado para 60 peticiones por minuto
        for ($i = 0; $i < 60; $i++) {
            $response = $this->postJson(route('catalog.track-click', $product));
            $response->assertOk();
        }

        // La petición 61 debe ser bloqueada con HTTP 429
        $throttledResponse = $this->postJson(route('catalog.track-click', $product));
        $throttledResponse->assertStatus(429);
    }

    /**
     * 4. Comprueba que el panel administrativo y los recursos de cotizaciones carguen correctamente para administradores.
     */
    public function test_admin_can_access_quote_logs_resource(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/quote-logs');
        $response->assertOk();
    }

    /**
     * 5. Comprueba que el Dashboard de Filament cargue sin errores y renderice todos los widgets de telemetría.
     */
    public function test_admin_can_access_dashboard_with_all_telemetry_widgets(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin');
        $response->assertOk();
        $response->assertSeeLivewire(ExecutiveOverviewWidget::class);
        $response->assertSeeLivewire(BusinessStatsOverview::class);
        $response->assertSeeLivewire(ProductInterestRankingWidget::class);
        $response->assertSeeLivewire(LatestQuotesWidget::class);
    }
}
