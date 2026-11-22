<?php

declare(strict_types=1);

namespace App\Filament\Resources\MontajeResource\Pages;

use App\Filament\Resources\MontajeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMontaje extends CreateRecord
{
    protected static string $resource = MontajeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
