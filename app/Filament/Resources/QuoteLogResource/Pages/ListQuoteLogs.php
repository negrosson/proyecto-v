<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuoteLogResource\Pages;

use App\Filament\Resources\QuoteLogResource;
use Filament\Resources\Pages\ListRecords;

class ListQuoteLogs extends ListRecords
{
    protected static string $resource = QuoteLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Las cotizaciones provienen del catálogo web
        ];
    }
}
