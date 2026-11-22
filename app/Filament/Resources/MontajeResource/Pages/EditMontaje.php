<?php

declare(strict_types=1);

namespace App\Filament\Resources\MontajeResource\Pages;

use App\Filament\Resources\MontajeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMontaje extends EditRecord
{
    protected static string $resource = MontajeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
