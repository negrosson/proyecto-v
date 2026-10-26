<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\SiteSettingResource;
use App\Filament\Resources\UserResource;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\QuoteLog;
use Filament\Widgets\Widget;

class ExecutiveOverviewWidget extends Widget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.executive-overview-widget';

    public function getViewData(): array
    {
        return [
            'totalProducts' => Product::where('is_active', true)->count(),
            'totalStock' => (int) Product::where('is_active', true)->sum('stock'),
            'totalQuotes' => QuoteLog::count(),
            'pendingQuotes' => QuoteLog::where('status', 'pendiente')->count(),
            'unreadMessages' => ContactMessage::where('is_read', false)->count(),
            'userName' => auth()->user()?->name ?? 'Administrador',
            'createProductUrl' => ProductResource::getUrl('create'),
            'siteSettingsUrl' => SiteSettingResource::getUrl('index'),
            'usersUrl' => UserResource::getUrl('index'),
            'catalogUrl' => url('/'),
        ];
    }
}
