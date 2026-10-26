<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\QuoteLog;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotesWidget extends BaseWidget
{
    protected static ?string $heading = 'Últimas Solicitudes de Cotización (WhatsApp)';

    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                QuoteLog::query()->latest()->limit(8)
            )
            ->emptyStateHeading('Sin cotizaciones registradas aún')
            ->emptyStateDescription('Cuando los clientes generen una cotización hacia WhatsApp en el catálogo web, se registrará aquí con su telemetría e ítems.')
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text')
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Fecha del Evento')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(function (?QuoteLog $record): string {
                        if (! $record || ! $record->event_date) {
                            return 'gray';
                        }
                        $diffInDays = now()->startOfDay()->diffInDays(Carbon::parse($record->event_date)->startOfDay(), false);

                        if ($diffInDays >= 0 && $diffInDays <= 7) {
                            return 'danger'; // ¡Próximo evento urgente!
                        }

                        return $diffInDays > 7 ? 'warning' : 'gray';
                    })
                    ->icon(function (?QuoteLog $record): ?string {
                        if (! $record || ! $record->event_date) {
                            return null;
                        }
                        $diffInDays = now()->startOfDay()->diffInDays(Carbon::parse($record->event_date)->startOfDay(), false);

                        return ($diffInDays >= 0 && $diffInDays <= 7) ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-calendar';
                    })
                    ->placeholder('Por definir'),

                Tables\Columns\TextColumn::make('location')
                    ->label('Comuna / Lugar')
                    ->limit(25)
                    ->placeholder('No indicada')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Piezas')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Estimado')
                    ->formatStateUsing(fn ($state): string => '$ '.number_format((float) $state, 0, ',', '.').' CLP')
                    ->weight('extrabold')
                    ->alignEnd(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'descartado' => 'Descartado',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('viewItems')
                    ->label('Desglose')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('amber')
                    ->button()
                    ->size('sm')
                    ->modalHeading(fn (QuoteLog $record): string => "Detalle de Cotización: {$record->customer_name}")
                    ->modalDescription(fn (QuoteLog $record): string => "Lugar: {$record->location} • Cantidad total: {$record->items_count} piezas")
                    ->modalContent(fn (QuoteLog $record) => view('filament.components.quote-items-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ])
            ->paginated(false);
    }
}
