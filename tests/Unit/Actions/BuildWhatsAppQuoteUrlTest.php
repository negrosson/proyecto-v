<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\Catalog\BuildWhatsAppQuoteUrlAction;
use App\DTOs\CartItemData;
use App\DTOs\QuoteRequestData;
use Tests\TestCase;

class BuildWhatsAppQuoteUrlTest extends TestCase
{
    /**
     * Valida que la URL resultante comience por https://wa.me/ y contenga el número esperado.
     */
    public function test_url_starts_with_wa_me_and_contains_expected_phone_number(): void
    {
        $action = new BuildWhatsAppQuoteUrlAction('+56 9 8765 4321');

        $quote = new QuoteRequestData(
            customerName: 'Juan Pérez',
            eventDate: '2026-11-20',
            location: 'Las Condes, Santiago',
            items: [
                new CartItemData(code: 101, name: 'Silla Chiavari', unitPrice: 3500, quantity: 10),
            ]
        );

        $url = $action->execute($quote);

        $this->assertStringStartsWith('https://wa.me/56987654321?text=', $url);
    }

    /**
     * Valida que el cálculo de subtotales y el gran total sean matemáticamente exactos.
     */
    public function test_subtotals_and_grand_total_are_mathematically_exact(): void
    {
        $item1 = new CartItemData(code: 1010, name: 'Silla Chiavari Dorada', unitPrice: 3500, quantity: 10);
        $item2 = new CartItemData(code: 2020, name: 'Mesa Redonda 1.80m', unitPrice: 12000, quantity: 5);
        $item3 = new CartItemData(code: 3030, name: 'Copa Vino Tinto Cabernet', unitPrice: 1800, quantity: 50);

        // Validación matemática a nivel de DTO
        $this->assertSame(35000, $item1->subtotal());
        $this->assertSame(60000, $item2->subtotal());
        $this->assertSame(90000, $item3->subtotal());

        $quote = new QuoteRequestData(
            customerName: 'Constanza Morales',
            eventDate: '2026-12-05',
            location: 'Providencia',
            items: [$item1, $item2, $item3]
        );

        $expectedGrandTotal = 35000 + 60000 + 90000; // 185.000
        $this->assertSame(185000, $quote->total());
        $this->assertSame($expectedGrandTotal, $quote->total());

        $action = new BuildWhatsAppQuoteUrlAction('56912345678');
        $url = $action->execute($quote);

        // Extraer y decodificar el mensaje para verificar formato de moneda CLP
        $queryString = parse_url($url, PHP_URL_QUERY);
        $this->assertNotNull($queryString);
        parse_str($queryString, $queryParams);
        $decodedMessage = $queryParams['text'] ?? '';

        $this->assertStringContainsString('• [Cód 1010] 10x Silla Chiavari Dorada ($35.000)', $decodedMessage);
        $this->assertStringContainsString('• [Cód 2020] 5x Mesa Redonda 1.80m ($60.000)', $decodedMessage);
        $this->assertStringContainsString('• [Cód 3030] 50x Copa Vino Tinto Cabernet ($90.000)', $decodedMessage);
        $this->assertStringContainsString('*TOTAL ESTIMADO:* $185.000', $decodedMessage);
    }

    /**
     * Valida que incluya el nombre del cliente, la comuna/lugar y la fecha del evento.
     */
    public function test_includes_customer_name_location_and_event_date(): void
    {
        $action = new BuildWhatsAppQuoteUrlAction('56912345678');

        $quote = new QuoteRequestData(
            customerName: 'Rodrigo Echeverría',
            eventDate: '2026-10-31',
            location: 'Centro de Eventos Los Nogales, Huechuraba',
            items: [
                new CartItemData(code: 4040, name: 'Estufa Patio a Gas', unitPrice: 25000, quantity: 2),
            ]
        );

        $url = $action->execute($quote);
        $decodedMessage = rawurldecode(parse_url($url, PHP_URL_QUERY) ?? '');

        $this->assertStringContainsString('👤 *Cliente:* Rodrigo Echeverría', $decodedMessage);
        $this->assertStringContainsString('📅 *Fecha Evento:* 2026-10-31', $decodedMessage);
        $this->assertStringContainsString('📍 *Lugar / Comuna:* Centro de Eventos Los Nogales, Huechuraba', $decodedMessage);
    }

    /**
     * Valida que si la fecha del evento es null o vacía, se indique "Por definir".
     */
    public function test_handles_null_or_empty_event_date(): void
    {
        $action = new BuildWhatsAppQuoteUrlAction('56912345678');

        $quote = new QuoteRequestData(
            customerName: 'María José',
            eventDate: null,
            location: 'Ñuñoa',
            items: []
        );

        $url = $action->execute($quote);
        $decodedMessage = rawurldecode(parse_url($url, PHP_URL_QUERY) ?? '');

        $this->assertStringContainsString('📅 *Fecha Evento:* Por definir', $decodedMessage);
    }

    /**
     * Valida que caracteres especiales (tildes, comillas, ampersands) queden codificados correctamente sin romper la URL.
     */
    public function test_encodes_special_characters_properly_without_breaking_url(): void
    {
        $action = new BuildWhatsAppQuoteUrlAction('56912345678');

        $quote = new QuoteRequestData(
            customerName: 'Matías & "Peña" Producciones <script>alert("hack")</script>',
            eventDate: '2026-11-15',
            location: 'Viña del Mar & Reñaca "Sector 5"',
            items: [
                new CartItemData(code: 5050, name: 'Mantelería & Cristalería "Chic" en Ñuñoa', unitPrice: 15000, quantity: 3),
            ]
        );

        $url = $action->execute($quote);

        // La URL completa debe ser sintácticamente válida
        $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL));

        // El ampersand del contenido debe estar codificado como %26 para no crear parámetros de consulta espurios
        $this->assertStringNotContainsString('text=Matías &', $url);
        $this->assertStringContainsString('%26', $url);

        // Las etiquetas script deben ser eliminadas por sanitización
        $this->assertStringNotContainsString('<script>', $url);
        $this->assertStringNotContainsString('&lt;script&gt;', $url);

        // Al decodificar con rawurldecode, los caracteres legítimos deben preservarse sin inyecciones
        $queryString = (string) parse_url($url, PHP_URL_QUERY);
        $encodedText = str_replace('text=', '', $queryString);
        $decodedText = rawurldecode($encodedText);

        $this->assertStringContainsString('Matías & "Peña" Producciones alert("hack")', $decodedText);
        $this->assertStringContainsString('Viña del Mar & Reñaca "Sector 5"', $decodedText);
        $this->assertStringContainsString('Mantelería & Cristalería "Chic" en Ñuñoa', $decodedText);
    }
}
