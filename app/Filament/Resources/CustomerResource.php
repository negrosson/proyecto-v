<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Atención al Cliente';

    protected static ?int $navigationSort = 0;

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Cartera de Clientes';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount(['quoteLogs', 'contactMessages'])
            ->withSum('quoteLogs', 'total_amount');
    }

    public static function getNavigationBadge(): ?string
    {
        $count = cache()->remember(
            'filament_badge_customers_count',
            60,
            static fn (): int => (int) static::getModel()::count()
        );

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ficha del Cliente')
                    ->description('Respaldo rápido de tus clientes habituales y contactos clave. Solo el nombre es obligatorio.')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre o Empresa')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Ej: Banquetería Los Robles / María González'),

                        Forms\Components\Select::make('type')
                            ->label('Tipo de Cliente')
                            ->options(Customer::TYPES)
                            ->default(Customer::TYPE_PARTICULAR)
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('phone')
                            ->label('WhatsApp / Teléfono')
                            ->tel()
                            ->maxLength(30)
                            ->placeholder('+56 9 1234 5678'),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(150)
                            ->placeholder('cliente@correo.cl'),

                        Forms\Components\TextInput::make('rut')
                            ->label('RUT (opcional)')
                            ->maxLength(25)
                            ->placeholder('12.345.678-9'),

                        Forms\Components\TextInput::make('commune')
                            ->label('Comuna / Sector Habitual')
                            ->maxLength(120)
                            ->placeholder('Ej: Las Condes, Providencia'),

                        Forms\Components\Toggle::make('is_frequent')
                            ->label('Cliente Frecuente / VIP')
                            ->helperText('Destaca este contacto en la pestaña de Clientes Frecuentes para ubicarlo al instante.')
                            ->default(false)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Internas / Preferencias')
                            ->rows(4)
                            ->placeholder('Ej: Siempre arrienda sillas Chiavari doradas, prefiere despacho día anterior, paga 50% al confirmar.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente / Empresa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(function (Customer $record): ?string {
                        $parts = array_filter([$record->commune, $record->rut]);

                        return $parts !== [] ? implode(' · ', $parts) : null;
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Customer::TYPES[$state] ?? ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        Customer::TYPE_EMPRESA => 'info',
                        Customer::TYPE_PRODUCTORA => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_frequent')
                    ->label('Frecuente')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Teléfono copiado')
                    ->placeholder('Sin teléfono'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('quote_logs_count')
                    ->label('Cotizaciones')
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'amber' : 'gray')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('quote_logs_sum_total_amount')
                    ->label('Historial Cotizado')
                    ->formatStateUsing(fn ($state): string => '$ '.number_format((float) ($state ?? 0), 0, ',', '.').' CLP')
                    ->weight('bold')
                    ->alignEnd()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('is_frequent', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo de Cliente')
                    ->options(Customer::TYPES),

                Tables\Filters\TernaryFilter::make('is_frequent')
                    ->label('Clientes Frecuentes')
                    ->trueLabel('Solo Frecuentes / VIP')
                    ->falseLabel('No Frecuentes'),
            ])
            ->actions([
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (Customer $record): ?string => $record->whatsappUrl("Hola {$record->name}, te escribimos de VYS Productora de Eventos."))
                    ->openUrlInNewTab()
                    ->visible(fn (Customer $record): bool => $record->whatsappUrl() !== null),

                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Aún no tienes clientes guardados')
            ->emptyStateDescription('Puedes seguir atendiendo todo por WhatsApp y respaldar aquí solo tus clientes frecuentes, o guardarlos con 1 clic desde Cotizaciones y Mensajes.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
