<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteLogResource\Pages;
use App\Models\QuoteLog;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteLogResource extends Resource
{
    protected static ?string $model = QuoteLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Atención al Cliente';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Cotización WhatsApp';

    protected static ?string $pluralModelLabel = 'Cotizaciones WhatsApp';

    public static function getNavigationBadge(): ?string
    {
        $pending = (int) static::getModel()::where('status', 'pendiente')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles del Cliente y Evento')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nombre del Cliente')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('location')
                            ->label('Comuna / Dirección del Evento')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('event_date')
                            ->label('Fecha del Evento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(now()->subMonths(6))
                            ->maxDate(now()->addYears(2)),

                        Forms\Components\Select::make('status')
                            ->label('Estado de la Cotización')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'confirmado' => 'Confirmado',
                                'descartado' => 'Descartado',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('total_amount')
                            ->label('Monto Total Estimado (CLP)')
                            ->numeric()
                            ->prefix('$')
                            ->required(),

                        Forms\Components\TextInput::make('items_count')
                            ->label('Total Piezas Cotizadas')
                            ->numeric()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Fecha Evento')
                    ->date('d/m/Y')
                    ->badge()
                    ->color(function (?QuoteLog $record): string {
                        if (! $record || ! $record->event_date) {
                            return 'gray';
                        }
                        $diffInDays = now()->startOfDay()->diffInDays(Carbon::parse($record->event_date)->startOfDay(), false);

                        if ($diffInDays >= 0 && $diffInDays <= 7) {
                            return 'danger';
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
                    ->placeholder('Por definir')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->label('Comuna / Lugar')
                    ->searchable()
                    ->limit(25)
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
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\SelectColumn::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'descartado' => 'Descartado',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Generado')
                    ->dateTime('d/m/Y H:i')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Filtrar por Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'descartado' => 'Descartado',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('viewItems')
                    ->label('Desglose')
                    ->icon('heroicon-o-shopping-bag')
                    ->color('amber')
                    ->modalHeading(fn (QuoteLog $record): string => "Detalle de Cotización: {$record->customer_name}")
                    ->modalDescription(fn (QuoteLog $record): string => "Lugar: {$record->location} • Total: {$record->items_count} piezas")
                    ->modalContent(fn (QuoteLog $record) => view('filament.components.quote-items-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteLogs::route('/'),
        ];
    }
}
