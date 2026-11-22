<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Pages\TwoFactorSecurityPage;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\GalleryImageResource;
use App\Filament\Resources\MontajeResource;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\SiteSettingResource;
use App\Models\Category;
use App\Models\GalleryImage;
use App\Models\Montaje;
use App\Models\SiteSetting;
use App\Models\User;
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
        /** @var User|null $user */
        $user = auth()->user();
        $rawName = $user?->name;
        $userName = ($rawName === 'admin' || empty($rawName))
            ? 'Administrador General'
            : $rawName;

        $settings = SiteSetting::current();

        return [
            'siteName' => $settings->site_name ?: 'VYS · Productora de Eventos',
            'userName' => $userName,
            'hasTwoFactor' => (bool) $user?->hasEnabledTwoFactor(),
            'totalGalleryPhotos' => GalleryImage::query()->count(),
            'totalMontajes' => Montaje::query()->where('is_active', true)->count(),
            'totalCategories' => Category::query()->count(),
            'createProductUrl' => ProductResource::getUrl('create'),
            'galleryUrl' => GalleryImageResource::getUrl('index'),
            'montajesUrl' => MontajeResource::getUrl('index'),
            'categoriesUrl' => CategoryResource::getUrl('index'),
            'siteSettingsUrl' => SiteSettingResource::getUrl('index'),
            'twoFactorUrl' => TwoFactorSecurityPage::getUrl(),
            'catalogUrl' => url('/'),
        ];
    }
}
