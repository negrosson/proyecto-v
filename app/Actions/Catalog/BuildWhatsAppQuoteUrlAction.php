<?php

declare(strict_types=1);

namespace App\Actions\Catalog;

use App\DTOs\QuoteRequestData;
use App\Models\SiteSetting;

final class BuildWhatsAppQuoteUrlAction
{
    private ?string $explicitPhoneNumber;

    /**
     * @param  string|null  $phoneNumber  Número de teléfono de contacto (opcional para override en pruebas)
     */
    public function __construct(?string $phoneNumber = null)
    {
        $this->explicitPhoneNumber = $phoneNumber !== null ? preg_replace('/\D+/', '', $phoneNumber) : null;
    }

    /**
     * Construye y retorna la URL directa de WhatsApp con el mensaje estructurado de cotización.
     */
    public function execute(QuoteRequestData $quote, ?string $overridePhone = null): string
    {
        $phone = $overridePhone !== null
            ? (preg_replace('/\D+/', '', $overridePhone) ?? '56900000000')
            : ($this->explicitPhoneNumber ?? SiteSetting::current()->cleanWhatsappPhone());
        $customerName = $this->sanitize($quote->customerName);
        $location = $this->sanitize($quote->location);
        $eventDate = $quote->eventDate !== null && trim($quote->eventDate) !== ''
            ? $this->sanitize($quote->eventDate)
            : 'Por definir';

        $lines = [
            '*SOLICITUD DE COTIZACIÓN - ARRIENDO*',
            '────────────────────────────',
            "👤 *Cliente:* {$customerName}",
            "📅 *Fecha Evento:* {$eventDate}",
            "📍 *Lugar / Comuna:* {$location}",
            '────────────────────────────',
            '*DETALLE DE ARTÍCULOS:*',
        ];

        if (empty($quote->items)) {
            $lines[] = '• Sin artículos seleccionados';
        } else {
            foreach ($quote->items as $item) {
                $itemName = $this->sanitize($item->name);
                $subtotal = $this->formatCurrency($item->subtotal());
                $lines[] = "• [Cód {$item->code}] {$item->quantity}x {$itemName} ({$subtotal})";
            }
        }

        $totalFormatted = $this->formatCurrency($quote->total());

        $lines[] = '────────────────────────────';
        $lines[] = "*TOTAL ESTIMADO:* {$totalFormatted}";
        $lines[] = '────────────────────────────';
        $lines[] = '';
        $lines[] = '_Hola, me gustaría saber si tienen disponibilidad para esta fecha y cuál sería el valor del flete._';

        $message = implode("\r\n", $lines);

        return sprintf('https://wa.me/%s?text=%s', $phone, rawurlencode($message));
    }

    /**
     * Sanitiza cadenas de texto para evitar roturas o inyecciones de código.
     */
    private function sanitize(string $value): string
    {
        $clean = strip_tags($value);
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $clean) ?? '';
        $clean = str_replace(["\r\n", "\r"], "\n", $clean);

        return trim($clean);
    }

    /**
     * Formatea un entero a formato de moneda chilena (CLP, ej: $180.000).
     */
    private function formatCurrency(int $amount): string
    {
        return '$'.number_format($amount, 0, ',', '.');
    }
}
