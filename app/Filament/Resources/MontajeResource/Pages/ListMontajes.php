<?php

namespace App\Filament\Resources\MontajeResource\Pages;

use App\Filament\Resources\MontajeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMontajes extends ListRecords
{
    protected static string $resource = MontajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
