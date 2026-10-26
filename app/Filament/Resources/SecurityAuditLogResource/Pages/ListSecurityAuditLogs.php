<?php

declare(strict_types=1);

namespace App\Filament\Resources\SecurityAuditLogResource\Pages;

use App\Filament\Resources\SecurityAuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListSecurityAuditLogs extends ListRecords
{
    protected static string $resource = SecurityAuditLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
