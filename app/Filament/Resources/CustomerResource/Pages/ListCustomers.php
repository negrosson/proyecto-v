<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Support\CsvExport;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportCsv')
                ->label('Exportar Excel (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => self::exportCustomersCsv()),

            Actions\CreateAction::make()
                ->label('Nuevo Cliente'),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'todos' => Tab::make('Todos'),
            'frecuentes' => Tab::make('Frecuentes / VIP')
                ->icon('heroicon-m-star')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_frequent', true)),
            'empresas' => Tab::make('Empresas y Productoras')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('type', [
                    Customer::TYPE_EMPRESA,
                    Customer::TYPE_PRODUCTORA,
                ])),
            'particulares' => Tab::make('Particulares')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('type', Customer::TYPE_PARTICULAR)),
        ];
    }

    public static function exportCustomersCsv(): StreamedResponse
    {
        $headers = [
            'Nombre / Empresa',
            'Tipo',
            'Frecuente / VIP',
            'WhatsApp / Teléfono',
            'Correo',
            'RUT',
            'Comuna',
            'Cotizaciones Vinculadas',
            'Total Cotizado (CLP)',
            'Notas Internas',
            'Registrado El',
        ];

        $rows = (static function (): \Generator {
            $query = Customer::query()
                ->withCount('quoteLogs')
                ->withSum('quoteLogs', 'total_amount')
                ->orderByDesc('is_frequent')
                ->orderBy('name');

            foreach ($query->cursor() as $customer) {
                yield [
                    $customer->name,
                    Customer::TYPES[$customer->type] ?? $customer->type,
                    $customer->is_frequent ? 'Sí' : 'No',
                    $customer->phone ?? '',
                    $customer->email ?? '',
                    $customer->rut ?? '',
                    $customer->commune ?? '',
                    (int) ($customer->quote_logs_count ?? 0),
                    (int) ($customer->quote_logs_sum_total_amount ?? 0),
                    $customer->notes ?? '',
                    $customer->created_at?->format('d/m/Y H:i') ?? '',
                ];
            }
        })();

        return CsvExport::download(
            'cartera-clientes-vys-'.now()->format('Y-m-d').'.csv',
            $headers,
            $rows
        );
    }
}
