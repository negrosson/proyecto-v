<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Atención al Cliente';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Mensaje de Contacto';

    protected static ?string $pluralModelLabel = 'Mensajes de Contacto';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('customer');
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = cache()->remember('filament_badge_contact_unread_count', 30, static fn () => (int) static::getModel()::where('is_read', false)->count());

        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del Cliente')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->disabled(),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->disabled(),
                Forms\Components\DatePicker::make('event_date')
                    ->label('Fecha del Evento')
                    ->disabled(),
                Forms\Components\Select::make('customer_id')
                    ->label('Vinculado en Cartera')
                    ->relationship('customer', 'name')
                    ->disabled()
                    ->visible(fn (?ContactMessage $record): bool => $record?->customer_id !== null)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('event_address')
                    ->label('Comuna / Dirección del Evento')
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('message')
                    ->label('Mensaje / Requerimiento')
                    ->rows(5)
                    ->disabled()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_read')
                    ->label('Marcado como Leído'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Leído')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->searchable()
                    ->weight('bold')
                    ->description(function (ContactMessage $record): ?string {
                        if ($record->customer !== null) {
                            return $record->customer->is_frequent
                                ? '★ Cliente Frecuente en Cartera'
                                : '✓ En Cartera de Clientes';
                        }

                        return filled($record->phone) ? (string) $record->phone : null;
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Fecha Evento')
                    ->date('d/m/Y')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_address')
                    ->label('Lugar Evento')
                    ->limit(25)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('unread')
                    ->label('Solo No Leídos')
                    ->query(fn ($query) => $query->where('is_read', false)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
                Tables\Actions\Action::make('whatsapp')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (ContactMessage $record): ?string => $record->whatsappUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (ContactMessage $record): bool => $record->whatsappUrl() !== null),
                Tables\Actions\Action::make('saveAsCustomer')
                    ->label('Guardar Cliente')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->visible(fn (ContactMessage $record): bool => $record->customer_id === null)
                    ->modalHeading('Guardar en Cartera de Clientes')
                    ->modalDescription('Crea un respaldo de este contacto y márcalo como leído. Si ya existe por teléfono o correo, se vincula sin duplicar.')
                    ->modalSubmitActionLabel('Guardar y Vincular')
                    ->fillForm(fn (ContactMessage $record): array => [
                        'name' => $record->name,
                        'type' => Customer::TYPE_PARTICULAR,
                        'phone' => $record->phone,
                        'email' => $record->email,
                        'commune' => $record->event_address,
                        'is_frequent' => false,
                        'notes' => null,
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
                            ->maxLength(30),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo (opcional)')
                            ->email()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('commune')
                            ->label('Comuna / Dirección (opcional)')
                            ->maxLength(120),
                        Forms\Components\Toggle::make('is_frequent')
                            ->label('Marcar como Cliente Frecuente / VIP')
                            ->default(false),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Internas (opcional)')
                            ->rows(3),
                    ])
                    ->action(function (ContactMessage $record, array $data): void {
                        $customer = Customer::findOrCreateFromInquiry([
                            'name' => (string) $data['name'],
                            'type' => (string) ($data['type'] ?? Customer::TYPE_PARTICULAR),
                            'phone' => $data['phone'] ?? null,
                            'email' => $data['email'] ?? null,
                            'commune' => $data['commune'] ?? null,
                            'is_frequent' => (bool) ($data['is_frequent'] ?? false),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        $record->update([
                            'customer_id' => $customer->id,
                            'is_read' => true,
                        ]);

                        Notification::make()
                            ->title('Cliente guardado y vinculado')
                            ->body("«{$customer->name}» quedó respaldado en tu Cartera de Clientes.")
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('markAsRead')
                    ->label('Marcar leído')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (ContactMessage $record): bool => ! $record->is_read)
                    ->action(function (ContactMessage $record): void {
                        $record->update(['is_read' => true]);
                        Notification::make()
                            ->title('Mensaje marcado como leído')
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListContactMessages::route('/'),
        ];
    }
}
