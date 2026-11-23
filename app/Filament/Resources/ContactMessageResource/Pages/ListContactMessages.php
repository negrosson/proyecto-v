<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            'no_leidos' => Tab::make('No Leídos')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_read', false)),
            'leidos' => Tab::make('Leídos')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_read', true)),
            'guardados' => Tab::make('En Cartera')
                ->icon('heroicon-m-user-group')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNotNull('customer_id')),
        ];
    }
}
