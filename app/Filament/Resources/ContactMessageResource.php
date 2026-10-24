<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Atención al Cliente';

    protected static ?string $modelLabel = 'Mensaje de Contacto';

    protected static ?string $pluralModelLabel = 'Mensajes de Contacto';

    public static function getNavigationBadge(): ?string
    {
        $unread = (int) static::getModel()::where('is_read', false)->count();

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
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Fecha Evento')
                    ->date('d/m/Y')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('event_address')
                    ->label('Lugar Evento')
                    ->limit(25),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibido')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('unread')
                    ->label('Solo No Leídos')
                    ->query(fn ($query) => $query->where('is_read', false)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
