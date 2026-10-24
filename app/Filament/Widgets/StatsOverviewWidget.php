<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProducts = Product::where('is_active', true)->count();
        $totalStock = (int) Product::where('is_active', true)->sum('stock');
        $unreadMessages = ContactMessage::where('is_read', false)->count();
        $totalCategories = Category::count();
        $totalFaqs = Faq::where('is_active', true)->count();

        return [
            Stat::make('Catálogo Activo', number_format($totalProducts, 0, ',', '.'))
                ->description("{$totalStock} unidades en stock total")
                ->descriptionIcon('heroicon-m-cube')
                ->color('amber')
                ->chart([15, 25, 45, 60, 95, 140, $totalProducts]),

            Stat::make('Consultas Recibidas', (string) $unreadMessages)
                ->description($unreadMessages > 0 ? "{$unreadMessages} por responder" : 'Bandeja al día')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($unreadMessages > 0 ? 'warning' : 'success'),

            Stat::make('Categorías de Menaje', (string) $totalCategories)
                ->description('Familias activas en vitrina')
                ->descriptionIcon('heroicon-m-tag')
                ->color('gray'),

            Stat::make('Preguntas Frecuentes', (string) $totalFaqs)
                ->description('Respuestas en el catálogo')
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('gray'),
        ];
    }
}
