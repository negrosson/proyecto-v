<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Comprueba que las cabeceras de seguridad HTTP (OWASP A05) estén presentes en las respuestas.
     */
    public function test_security_headers_are_present_on_catalog_root(): void
    {
        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $csp = (string) $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString('https://cdn.tailwindcss.com', $csp);
        $this->assertStringContainsString('https://cdn.jsdelivr.net', $csp);
        $this->assertStringContainsString('https://images.unsplash.com', $csp);
    }

    /**
     * 2. Comprueba que búsquedas con comodines SQL (% y _) busquen literalmente sin volcar el catálogo entero.
     */
    public function test_wildcard_search_does_not_dump_entire_catalog(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Silla Chiavari Dorada',
            'is_active' => true,
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Mantel 100% Lino Italiano',
            'is_active' => true,
        ]);

        // La búsqueda del comodín '%' solo debe devolver el que literalmente contiene '%'
        $response = $this->getJson(route('catalog.search', ['q' => '%']));

        $response->assertOk();
        $items = collect($response->json('data'));
        $names = $items->pluck('name');

        $this->assertTrue($names->contains('Mantel 100% Lino Italiano'));
        $this->assertFalse($names->contains('Silla Chiavari Dorada'));
    }

    /**
     * 3. Comprueba que inyecciones de payloads XSS en el formulario de cotización sean rechazadas con HTTP 422.
     */
    public function test_xss_injection_payload_in_quote_submission_is_rejected_with_422(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'stock' => 10,
        ]);

        $xssPayloads = [
            '<script>alert(1)</script>',
            '"><img src=x onerror=alert(1)>',
            'javascript:/*--></title></style></textarea></script></xmp><svg/onload=\'+/"/+/onmouseover=1/+/[*/[]/+alert(1)//\'>',
        ];

        foreach ($xssPayloads as $payload) {
            $response = $this->postJson(route('quote.store'), [
                'customer_name' => $payload,
                'location' => 'Providencia, Santiago',
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
            ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['customer_name']);
        }
    }

    /**
     * 4. Comprueba que peticiones consecutivas al endpoint de búsqueda activen el bloqueo HTTP 429 al superar el Rate Limiter.
     */
    public function test_search_rate_limiter_blocks_consecutive_requests_with_429(): void
    {
        RateLimiter::clear('catalog-search:127.0.0.1');

        for ($i = 0; $i < 45; $i++) {
            $this->getJson(route('catalog.search', ['q' => 'silla']));
        }

        $blockedResponse = $this->getJson(route('catalog.search', ['q' => 'silla']));
        $blockedResponse->assertStatus(429);
    }

    /**
     * 5. Comprueba que un usuario autenticado no administrador reciba HTTP 403 al intentar acceder a /admin.
     */
    public function test_non_admin_authenticated_user_receives_403_on_admin_panel(): void
    {
        $regularUser = User::factory()->create([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($regularUser)->get('/admin');

        $response->assertForbidden();
    }

    /**
     * Comprueba que un usuario con is_admin = true sí pueda acceder al panel de Filament.
     */
    public function test_admin_user_can_access_admin_panel(): void
    {
        $adminUser = User::factory()->admin()->create();

        $response = $this->actingAs($adminUser)->get('/admin');

        $response->assertSuccessful();
    }
}
