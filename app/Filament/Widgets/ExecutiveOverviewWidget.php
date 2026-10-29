<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\QuoteLogResource;
use App\Filament\Resources\SiteSettingResource;
use App\Filament\Resources\UserResource;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Product;
use App\Models\QuoteLog;
use Filament\Widgets\Widget;

class ExecutiveOverviewWidget extends Widget
{
    protected static ?int $sort = -10;

    protected int|string|array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.executive-overview-widget';

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        $user = auth()->user();
        $rawName = $user?->name;
        $userName = ($rawName === 'admin' || empty($rawName))
            ? 'Administrador General'
            : $rawName;

        return [
            'totalProducts' => Product::where('is_active', true)->count(),
            'totalStock' => (int) Product::where('is_active', true)->sum('stock'),
            'totalCategories' => Category::count(),
            'totalFaqs' => Faq::where('is_active', true)->count(),
            'totalQuotes' => QuoteLog::count(),
            'pendingQuotes' => QuoteLog::where('status', 'pendiente')->count(),
            'unreadMessages' => ContactMessage::where('is_read', false)->count(),
            'userName' => $userName,
            'createProductUrl' => ProductResource::getUrl('create'),
            'productsUrl' => ProductResource::getUrl('index'),
            'categoriesUrl' => CategoryResource::getUrl('index'),
            'siteSettingsUrl' => SiteSettingResource::getUrl('index'),
            'quotesUrl' => QuoteLogResource::getUrl('index'),
            'messagesUrl' => ContactMessageResource::getUrl('index'),
            'usersUrl' => UserResource::getUrl('index'),
            'catalogUrl' => url('/'),
        ];
    }
}
