<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int|string|array
    {
        return 2;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('current_date')
                ->label(now()->translatedFormat('l, j \d\e F Y'))
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->extraAttributes([
                    'class' => 'dashboard-header-date-badge',
                ]),
        ];
    }
}
