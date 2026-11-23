<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Filament\Resources\CustomerResource\Pages\CreateCustomer;
use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\QuoteLogResource\Pages\ListQuoteLogs;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\QuoteLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerCrmTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_and_guest_cannot_access_customers_panel(): void
    {
        $this->get('/admin/customers')->assertRedirect('/admin/login');

        $regularUser = User::factory()->create([
            'is_admin' => false,
            'is_active' => true,
        ]);

        $this->actingAs($regularUser)
            ->get('/admin/customers')
            ->assertForbidden();
    }

    public function test_admin_can_access_customer_quote_and_contact_resources(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/customers')->assertOk();
        $this->actingAs($admin)->get('/admin/quote-logs')->assertOk();
        $this->actingAs($admin)->get('/admin/contact-messages')->assertOk();
    }

    public function test_admin_can_create_customer_and_filter_with_tabs(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateCustomer::class)
            ->fillForm([
                'name' => 'Banquetería Los Robles',
                'type' => Customer::TYPE_EMPRESA,
                'phone' => '+56 9 8888 7777',
                'email' => 'eventos@losrobles.cl',
                'rut' => '76.123.456-7',
                'commune' => 'Las Condes',
                'is_frequent' => true,
                'notes' => 'Siempre solicita sillas Chiavari doradas.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $frequentCompany = Customer::where('name', 'Banquetería Los Robles')->firstOrFail();
        $regularParticular = Customer::factory()->create([
            'name' => 'Pedro Soto',
            'type' => Customer::TYPE_PARTICULAR,
            'is_frequent' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ListCustomers::class)
            ->set('activeTab', 'frecuentes')
            ->assertCanSeeTableRecords([$frequentCompany])
            ->assertCanNotSeeTableRecords([$regularParticular])
            ->set('activeTab', 'empresas')
            ->assertCanSeeTableRecords([$frequentCompany])
            ->assertCanNotSeeTableRecords([$regularParticular])
            ->set('activeTab', 'particulares')
            ->assertCanSeeTableRecords([$regularParticular])
            ->assertCanNotSeeTableRecords([$frequentCompany]);
    }

    public function test_saving_quote_and_contact_as_customer_links_and_deduplicates_without_creating_duplicates(): void
    {
        $admin = User::factory()->admin()->create();

        $quote = QuoteLog::create([
            'customer_name' => 'Camila Herrera',
            'event_date' => now()->addDays(5)->toDateString(),
            'location' => 'Providencia',
            'total_amount' => 350000,
            'items_count' => 80,
            'items_payload' => [
                ['id' => 1, 'name' => 'Silla Chiavari Dorada', 'qty' => 80, 'price' => 4000, 'subtotal' => 320000],
            ],
            'status' => 'pendiente',
        ]);

        Livewire::actingAs($admin)
            ->test(ListQuoteLogs::class)
            ->callTableAction('saveAsCustomer', $quote, data: [
                'name' => 'Camila Herrera',
                'type' => Customer::TYPE_PRODUCTORA,
                'phone' => '+56 9 8765 4321',
                'email' => '',
                'commune' => 'Providencia',
                'is_frequent' => true,
                'mark_confirmed' => true,
                'notes' => 'Abonó 50% por transferencia.',
            ])
            ->assertHasNoTableActionErrors();

        $quote->refresh();
        $this->assertNotNull($quote->customer_id);
        $this->assertSame('confirmado', $quote->status);
        $this->assertSame('+56 9 8765 4321', $quote->customer_phone);
        $this->assertSame('Abonó 50% por transferencia.', $quote->internal_notes);
        $this->assertSame(1, Customer::count());

        // Ahora llega un mensaje web del mismo número en formato corto (987654321) y con correo
        $contact = ContactMessage::create([
            'name' => 'Camila Herrera',
            'email' => 'camila@productora.cl',
            'phone' => '987654321',
            'event_address' => 'Vitacura',
            'event_date' => now()->addDays(15)->toDateString(),
            'message' => 'Necesitamos nueva cotización para gala.',
            'is_read' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(ListContactMessages::class)
            ->callTableAction('saveAsCustomer', $contact, data: [
                'name' => 'Camila Herrera',
                'type' => Customer::TYPE_PARTICULAR,
                'phone' => '987654321',
                'email' => 'camila@productora.cl',
                'commune' => 'Vitacura',
                'is_frequent' => false,
                'notes' => 'Segunda consulta vía web.',
            ])
            ->assertHasNoTableActionErrors();

        $contact->refresh();
        $customer = Customer::firstOrFail();

        $this->assertSame(1, Customer::count());
        $this->assertSame($customer->id, $contact->customer_id);
        $this->assertTrue($contact->is_read);
        $this->assertSame('camila@productora.cl', $customer->email);
        $this->assertSame(Customer::TYPE_PRODUCTORA, $customer->type);
        $this->assertTrue($customer->is_frequent);
    }

    public function test_quote_log_tabs_filter_upcoming_7_days_and_status(): void
    {
        $admin = User::factory()->admin()->create();

        $upcomingPending = QuoteLog::create([
            'customer_name' => 'Evento Cercano',
            'event_date' => now()->addDays(3)->toDateString(),
            'location' => 'Ñuñoa',
            'total_amount' => 120000,
            'items_count' => 20,
            'items_payload' => [],
            'status' => 'pendiente',
        ]);

        $farConfirmed = QuoteLog::create([
            'customer_name' => 'Evento Lejano',
            'event_date' => now()->addDays(30)->toDateString(),
            'location' => 'Las Condes',
            'total_amount' => 500000,
            'items_count' => 100,
            'items_payload' => [],
            'status' => 'confirmado',
        ]);

        Livewire::actingAs($admin)
            ->test(ListQuoteLogs::class)
            ->set('activeTab', 'proximos_7_dias')
            ->assertCanSeeTableRecords([$upcomingPending])
            ->assertCanNotSeeTableRecords([$farConfirmed])
            ->set('activeTab', 'confirmadas')
            ->assertCanSeeTableRecords([$farConfirmed])
            ->assertCanNotSeeTableRecords([$upcomingPending]);
    }

    public function test_phone_normalization_and_whatsapp_urls_work_for_chilean_numbers(): void
    {
        $this->assertSame('56912345678', Customer::normalizePhone('+56 9 1234 5678'));
        $this->assertSame('56912345678', Customer::normalizePhone('912345678'));
        $this->assertSame('56912345678', Customer::normalizePhone('1234-5678'));
        $this->assertNull(Customer::normalizePhone('123'));

        $url = Customer::buildWhatsappUrl('9 8765 4321', 'Hola cliente');
        $this->assertSame('https://wa.me/56987654321?text=Hola%20cliente', $url);
    }

    public function test_csv_exports_include_utf8_bom_and_neutralize_excel_formula_injection(): void
    {
        $customer = Customer::factory()->frequent()->create([
            'name' => '=HYPERLINK("http://evil.test","Click")',
            'phone' => '+56911112222',
            'notes' => '@SUM(1,2)',
        ]);

        QuoteLog::create([
            'customer_id' => $customer->id,
            'customer_name' => '-cmd|\' /C calc\'!A0',
            'customer_phone' => '+56911112222',
            'event_date' => now()->addDays(4)->toDateString(),
            'location' => 'Santiago',
            'total_amount' => 180000,
            'items_count' => 30,
            'items_payload' => [],
            'status' => 'confirmado',
            'internal_notes' => '=1+1',
        ]);

        $customersResponse = ListCustomers::exportCustomersCsv();
        $this->assertSame(200, $customersResponse->getStatusCode());
        $this->assertSame('text/csv; charset=UTF-8', $customersResponse->headers->get('Content-Type'));

        ob_start();
        $customersResponse->sendContent();
        $customersCsv = (string) ob_get_clean();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $customersCsv);
        $this->assertStringContainsString("'=HYPERLINK(", $customersCsv);
        $this->assertStringContainsString("'+56911112222", $customersCsv);
        $this->assertStringContainsString("'@SUM(1,2)", $customersCsv);

        $quotesResponse = ListQuoteLogs::exportQuoteLogsCsv();
        $this->assertSame(200, $quotesResponse->getStatusCode());

        ob_start();
        $quotesResponse->sendContent();
        $quotesCsv = (string) ob_get_clean();

        $this->assertStringStartsWith("\xEF\xBB\xBF", $quotesCsv);
        $this->assertStringContainsString("'-cmd|", $quotesCsv);
        $this->assertStringContainsString("'=1+1", $quotesCsv);
    }
}
