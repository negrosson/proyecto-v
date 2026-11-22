<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\QuoteLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStatsOverview extends BaseWidget
{
    protected static ?int $sort = -5;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $productStats = Product::query()
            ->selectRaw('
                COALESCE(SUM(clicks_count), 0) as total_clicks,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_products_count,
                COALESCE(SUM(CASE WHEN is_active = 1 THEN stock ELSE 0 END), 0) as total_stock,
                SUM(CASE WHEN is_active = 1 AND clicks_count >= 5 THEN 1 ELSE 0 END) as high_demand_count
            ')
            ->first();

        $totalClicks = (int) ($productStats->total_clicks ?? 0);
        $activeProductsCount = (int) ($productStats->active_products_count ?? 0);
        $totalStock = (int) ($productStats->total_stock ?? 0);
        $highDemandCount = (int) ($productStats->high_demand_count ?? 0);

        $topClickedProduct = Product::query()
            ->where('is_active', true)
            ->where('clicks_count', '>', 0)
            ->orderByDesc('clicks_count')
            ->first();

        $quoteStats = QuoteLog::query()
            ->selectRaw('COUNT(*) as total_quotes, COALESCE(SUM(total_amount), 0) as total_quoted_amount')
            ->first();

        $totalQuotes = (int) ($quoteStats->total_quotes ?? 0);
        $totalQuotedAmount = (int) ($quoteStats->total_quoted_amount ?? 0);
        $unreadMessages = ContactMessage::where('is_read', false)->count();

        $conversionRate = $totalClicks > 0
            ? round(($totalQuotes / $totalClicks) * 100, 1)
            : 0;

        $formattedQuotedAmount = '$ '.number_format($totalQuotedAmount, 0, ',', '.');
        $formattedStock = number_format($totalStock, 0, ',', '.');

        $clicksChart = match (true) {
            $totalClicks >= 20 => [
                (int) round($totalClicks * 0.25),
                (int) round($totalClicks * 0.45),
                (int) round($totalClicks * 0.65),
                (int) round($totalClicks * 0.85),
                $totalClicks,
            ],
            $totalClicks > 0 => [0, 0, 0, 1, $totalClicks],
            default => [0, 0, 0, 0, 0],
        };

        $quotesChart = match (true) {
            $totalQuotes >= 10 => [
                (int) round($totalQuotes * 0.3),
                (int) round($totalQuotes * 0.5),
                (int) round($totalQuotes * 0.75),
                (int) round($totalQuotes * 0.9),
                $totalQuotes,
            ],
            $totalQuotes > 0 => [0, 0, 1, 1, $totalQuotes],
            default => [0, 0, 0, 0, 0],
        };

        return [
            Stat::make('Interés en Vitrina (Clics)', number_format($totalClicks, 0, ',', '.'))
                ->description($topClickedProduct
                    ? "Más consultado: {$topClickedProduct->name} ({$topClickedProduct->clicks_count} interacciones)"
                    : 'Registrando interacciones en catálogo')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('amber')
                ->chart($clicksChart),

            Stat::make('Cotizaciones WhatsApp', (string) $totalQuotes)
                ->description($totalQuotes > 0 ? "{$formattedQuotedAmount} CLP en arriendos cotizados" : 'Bandeja de cotizaciones al día')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('success')
                ->chart($quotesChart),

            Stat::make('Tasa de Conversión a Cotización', "{$conversionRate}%")
                ->description($unreadMessages > 0
                    ? "{$unreadMessages} consultas de clientes por responder"
                    : 'Todas las consultas web al día')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($conversionRate > 0 ? 'amber' : 'gray'),

            Stat::make('Catálogo Activo de Menaje', "{$activeProductsCount} artículos")
                ->description("{$formattedStock} piezas en inventario ({$highDemandCount} con alta demanda)")
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('gray'),
        ];
    }
}
