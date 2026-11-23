<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteLogResource\Pages;
use App\Models\Customer;
use App\Models\QuoteLog;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuoteLogResource extends Resource
{
    protected static ?string $model = QuoteLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Atención al Cliente';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Cotización WhatsApp';

    protected static ?string $pluralModelLabel = 'Cotizaciones WhatsApp';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('customer');
    }

    public static function getNavigationBadge(): ?string
    {
        $pending = cache()->remember('filament_badge_quotes_pending_count', 30, static fn () => (int) static::getModel()::where('status', 'pendiente')->count());

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

                        Forms\Components\TextInput::make('customer_phone')
                            ->label('WhatsApp / Teléfono (opcional)')
                            ->tel()
                            ->maxLength(30)
                            ->placeholder('+56 9 1234 5678'),

                        Forms\Components\Select::make('customer_id')
                            ->label('Cliente en Cartera (opcional)')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Sin vincular — atención directa por WhatsApp'),

                        Forms\Components\TextInput::make('location')
                            ->label('Comuna / Dirección del Evento')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('event_date')
                            ->label('Fecha del Evento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
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

                        Forms\Components\Textarea::make('internal_notes')
                            ->label('Notas Internas de Seguimiento')
                            ->rows(3)
                            ->placeholder('Ej: Abonó $150.000, despacho viernes 16:00, confirmar acceso.')
                            ->columnSpanFull(),
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
                    ->weight('bold')
                    ->description(function (QuoteLog $record): ?string {
                        if ($record->customer !== null) {
                            return $record->customer->is_frequent
                                ? '★ Cliente Frecuente en Cartera'
                                : '✓ En Cartera de Clientes';
                        }

                        return filled($record->customer_phone) ? (string) $record->customer_phone : null;
                    }),

                Tables\Columns\TextColumn::make('customer_phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Tables\Columns\TextColumn::make('internal_notes')
                    ->label('Notas')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

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

                Tables\Filters\TernaryFilter::make('customer_id')
                    ->label('Respaldo en Cartera')
                    ->nullable()
                    ->trueLabel('Solo Guardados en Cartera')
                    ->falseLabel('Sin Guardar en Cartera'),
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

                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (QuoteLog $record): ?string => $record->whatsappUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (QuoteLog $record): bool => $record->whatsappUrl() !== null),

                Tables\Actions\Action::make('saveAsCustomer')
                    ->label('Guardar Cliente')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (QuoteLog $record): bool => $record->customer_id === null)
                    ->modalHeading('Guardar en Cartera de Clientes')
                    ->modalDescription('Crea un respaldo de este cliente y vincula la cotización. Si ya existe por teléfono o correo, se asocia automáticamente sin duplicar.')
                    ->modalSubmitActionLabel('Guardar y Vincular')
                    ->fillForm(fn (QuoteLog $record): array => [
                        'name' => $record->customer_name,
                        'type' => Customer::TYPE_PARTICULAR,
                        'phone' => $record->customer_phone,
                        'commune' => $record->location,
                        'is_frequent' => false,
                        'mark_confirmed' => true,
                        'notes' => $record->internal_notes,
                    ])
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre o Empresa')
                            ->required()
                            ->maxLength(150),

                        Forms\Components\Select::make('type')
                            ->label('Tipo de Cliente')
                            ->options(Customer::TYPES)
                            ->default(Customer::TYPE_PARTICULAR)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('phone')
                            ->label('WhatsApp / Teléfono (opcional)')
                            ->tel()
                            ->maxLength(30)
                            ->placeholder('+56 9 1234 5678'),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo (opcional)')
                            ->email()
                            ->maxLength(150),

                        Forms\Components\TextInput::make('commune')
                            ->label('Comuna / Sector (opcional)')
                            ->maxLength(120),

                        Forms\Components\Toggle::make('is_frequent')
                            ->label('Marcar como Cliente Frecuente / VIP')
                            ->default(false),

                        Forms\Components\Toggle::make('mark_confirmed')
                            ->label('Marcar esta cotización como Confirmada')
                            ->default(true),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Internas (opcional)')
                            ->rows(3),
                    ])
                    ->action(function (QuoteLog $record, array $data): void {
                        $customer = Customer::findOrCreateFromInquiry([
                            'name' => (string) $data['name'],
                            'type' => (string) ($data['type'] ?? Customer::TYPE_PARTICULAR),
                            'phone' => $data['phone'] ?? null,
                            'email' => $data['email'] ?? null,
                            'commune' => $data['commune'] ?? null,
                            'is_frequent' => (bool) ($data['is_frequent'] ?? false),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        $updates = [
                            'customer_id' => $customer->id,
                        ];

                        if (filled($data['phone'] ?? null)) {
                            $updates['customer_phone'] = trim((string) $data['phone']);
                        }

                        if (filled($data['notes'] ?? null)) {
                            $updates['internal_notes'] = trim((string) $data['notes']);
                        }

                        if (! empty($data['mark_confirmed'])) {
                            $updates['status'] = 'confirmado';
                        }

                        $record->update($updates);

                        Notification::make()
                            ->title('Cliente guardado y vinculado')
                            ->body("«{$customer->name}» quedó respaldado en tu Cartera de Clientes.")
                            ->success()
                            ->send();
                    }),

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
