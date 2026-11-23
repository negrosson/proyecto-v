<?php

declare(strict_types=1);

namespace App\Filament\Resources\QuoteLogResource\Pages;

use App\Filament\Resources\QuoteLogResource;
use App\Models\QuoteLog;
use App\Support\CsvExport;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListQuoteLogs extends ListRecords
{
    protected static string $resource = QuoteLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label('Exportar Excel (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => self::exportQuoteLogsCsv()),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'todas' => Tab::make('Todas'),
            'pendientes' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'pendiente')),
            'proximos_7_dias' => Tab::make('Próximos 7 días')
                ->icon('heroicon-m-fire')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereNotNull('event_date')
                    ->whereBetween('event_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])),
            'confirmadas' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'confirmado')),
            'descartadas' => Tab::make('Descartadas')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', 'descartado')),
        ];
    }

    public static function exportQuoteLogsCsv(): StreamedResponse
    {
        $headers = [
            'ID',
            'Cliente',
            'En Cartera',
            'WhatsApp / Teléfono',
            'Comuna / Lugar',
            'Fecha Evento',
            'Piezas',
            'Total Estimado (CLP)',
            'Estado',
            'Notas Internas',
            'Generado El',
        ];

        $rows = (static function (): \Generator {
            $query = QuoteLog::query()
                ->with('customer:id,name,phone')
                ->orderByDesc('created_at');

            foreach ($query->cursor() as $quote) {
                yield [
                    $quote->id,
                    $quote->customer_name,
                    $quote->customer ? 'Sí ('.$quote->customer->name.')' : 'No',
                    $quote->customer_phone ?? $quote->customer?->phone ?? '',
                    $quote->location,
                    $quote->event_date?->format('d/m/Y') ?? 'Por definir',
                    $quote->items_count,
                    $quote->total_amount,
                    ucfirst((string) $quote->status),
                    $quote->internal_notes ?? '',
                    $quote->created_at?->format('d/m/Y H:i') ?? '',
                ];
            }
        })();

        return CsvExport::download(
            'cotizaciones-whatsapp-vys-'.now()->format('Y-m-d').'.csv',
            $headers,
            $rows
        );
    }
}
