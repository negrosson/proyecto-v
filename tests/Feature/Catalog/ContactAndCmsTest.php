<?php

declare(strict_types=1);

namespace Tests\Feature\Catalog;

use App\Models\SiteSetting;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactAndCmsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Envía datos válidos a POST /contacto y comprueba HTTP 200 y el registro en contact_messages.
     */
    public function test_contact_form_stores_valid_message_in_database(): void
    {
        $payload = [
            'name' => 'Camila Henríquez',
            'email' => 'camila@eventosvip.cl',
            'phone' => '+56987654321',
            'event_address' => 'Av. El Bosque Norte 500, Las Condes',
            'event_date' => now()->addDays(15)->format('Y-m-d'),
            'message' => 'Necesito cotizar vajilla para 120 personas y carpa 10x10.',
            'website_hp' => '',
        ];

        $response = $this->postJson(route('contact.store'), $payload);

        $response->assertOk()
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Camila Henríquez',
            'email' => 'camila@eventosvip.cl',
            'phone' => '+56987654321',
            'event_address' => 'Av. El Bosque Norte 500, Las Condes',
            'is_read' => false,
        ]);
    }

    /**
     * 2. Verifica que si website_hp trae datos o el nombre tiene <script>, retorne error 422.
     */
    public function test_contact_form_rejects_xss_and_honeypot_bots(): void
    {
        // Caso A: Bot llena campo Honeypot
        $honeypotPayload = [
            'name' => 'Spam Bot',
            'email' => 'bot@spammer.org',
            'event_address' => 'Calle Falsa 123',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'message' => 'Buy cheap watches now!',
            'website_hp' => 'https://spam-bot.xyz',
        ];

        $honeypotResponse = $this->postJson(route('contact.store'), $honeypotPayload);
        $honeypotResponse->assertStatus(422)
            ->assertJsonValidationErrors(['website_hp']);

        // Caso B: Intento de XSS en el nombre
        $xssPayload = [
            'name' => '<script>alert("hack")</script>',
            'email' => 'hacker@target.com',
            'event_address' => 'Av. Providencia 1234',
            'event_date' => now()->addDays(5)->format('Y-m-d'),
            'message' => 'Intento de inyección de script malicioso.',
            'website_hp' => '',
        ];

        $xssResponse = $this->postJson(route('contact.store'), $xssPayload);
        $xssResponse->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        $this->assertDatabaseCount('contact_messages', 0);
    }

    /**
     * 3. Verifica que la 4ta petición consecutiva en un minuto retorne HTTP 429 (Rate Limiter).
     */
    public function test_contact_form_rate_limiter_blocks_spam(): void
    {
        $payload = [
            'name' => 'Usuario Legítimo',
            'email' => 'contacto@cliente.cl',
            'phone' => '+56911223344',
            'event_address' => 'Av. Vitacura 2000',
            'event_date' => now()->addDays(7)->format('Y-m-d'),
            'message' => 'Consulta rápida sobre disponibilidad de mesas y sillas.',
            'website_hp' => '',
        ];

        // Primeras 3 peticiones: permitidas (HTTP 200)
        for ($i = 0; $i < 3; $i++) {
            $response = $this->postJson(route('contact.store'), $payload);
            $response->assertOk();
        }

        // 4ta petición en el mismo minuto: bloqueada por el rate limiter (HTTP 429)
        $fourthResponse = $this->postJson(route('contact.store'), $payload);
        $fourthResponse->assertStatus(429);
    }

    /**
     * 4. Ejecuta SiteContentSeeder, hace GET / y comprueba que se muestren las FAQs, políticas de envío y datos de contacto.
     */
    public function test_catalog_renders_dynamic_cms_settings_and_faqs(): void
    {
        $this->seed(SiteContentSeeder::class);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();

        // FAQs de vajilla y garantía
        $response->assertSee('¿La vajilla y cristalería se devuelve limpia o sucia?');
        $response->assertSee('¿Cómo funciona la garantía en caso de roturas, pérdidas o daños?');
        $response->assertSee('Vajilla y Lavado');
        $response->assertSee('Garantías y Roturas');

        // Políticas de envíos y logística
        $response->assertSee('Políticas de Envíos, Montaje y Logística');
        $response->assertSee('Despachamos a todas las comunas del Gran Santiago');
        $response->assertSee('Carpas estructurales 10x10 y 10x13');

        // Datos oficiales de la empresa
        $response->assertSee('Av. El Parque 4120, Ciudad Empresarial');
        $response->assertSee('contacto@arriendoseventos.cl');
        $response->assertSee('Lunes a Viernes de 09:00 a 19:00 hrs');
    }

    /**
     * 5. Modifica site_name en SiteSetting y verifica que SiteSetting::current()->site_name refleje el cambio inmediatamente.
     */
    public function test_updating_site_settings_flushes_cache_automatically(): void
    {
        $this->seed(SiteContentSeeder::class);

        // Se cachea el valor inicial
        $initialSetting = SiteSetting::current();
        $this->assertSame('Arriendos & Eventos', $initialSetting->site_name);

        // Modificamos el registro en la base de datos
        $setting = SiteSetting::firstOrFail();
        $setting->update([
            'site_name' => 'Banquetería Imperial Gourmet',
        ]);

        // Verificamos que el método current() refleje el cambio inmediatamente por el cache flush automático
        $freshSetting = SiteSetting::current();
        $this->assertSame('Banquetería Imperial Gourmet', $freshSetting->site_name);
    }

    /**
     * 6. Verifica que no se puedan agendar eventos en el pasado ni con más de 2 años en el futuro.
     */
    public function test_contact_form_rejects_past_or_incoherent_event_dates(): void
    {
        // Caso A: Fecha en el pasado (ayer)
        $pastPayload = [
            'name' => 'Felipe Morales',
            'email' => 'felipe@empresa.cl',
            'phone' => '+56977112233',
            'event_address' => 'Santiago Centro',
            'event_date' => now()->subDay()->format('Y-m-d'),
            'message' => 'Intento de agendar con fecha pasada.',
            'website_hp' => '',
        ];

        $pastResponse = $this->postJson(route('contact.store'), $pastPayload);
        $pastResponse->assertStatus(422)
            ->assertJsonValidationErrors(['event_date']);

        $this->assertStringContainsString(
            'no puede ser en el pasado',
            $pastResponse->json('errors.event_date.0')
        );

        // Caso B: Fecha incoherentemente lejana (3 años en el futuro)
        $futurePayload = [
            'name' => 'Felipe Morales',
            'email' => 'felipe@empresa.cl',
            'phone' => '+56977112233',
            'event_address' => 'Santiago Centro',
            'event_date' => now()->addYears(3)->format('Y-m-d'),
            'message' => 'Intento de agendar a 3 años futuro.',
            'website_hp' => '',
        ];

        $futureResponse = $this->postJson(route('contact.store'), $futurePayload);
        $futureResponse->assertStatus(422)
            ->assertJsonValidationErrors(['event_date']);

        // Caso C: Fecha de hoy (válida para arriendos inmediatos)
        $todayPayload = [
            'name' => 'Felipe Morales',
            'email' => 'felipe@empresa.cl',
            'phone' => '+56977112233',
            'event_address' => 'Santiago Centro',
            'event_date' => now()->format('Y-m-d'),
            'message' => 'Arriendo urgente para evento de hoy.',
            'website_hp' => '',
        ];

        $todayResponse = $this->postJson(route('contact.store'), $todayPayload);
        $todayResponse->assertOk();
    }
}
