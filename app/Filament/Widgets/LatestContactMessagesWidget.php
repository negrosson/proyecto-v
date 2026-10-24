<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestContactMessagesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Últimas Solicitudes y Cotizaciones de Clientes';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContactMessage::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Cliente')
                    ->weight('bold')
                    ->description(fn (ContactMessage $record): string => $record->email),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Fecha del Evento')
                    ->date('d/m/Y')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('event_address')
                    ->label('Ubicación')
                    ->limit(30)
                    ->placeholder('No especificada'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recibido')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsRead')
                    ->label('Leído')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->visible(fn (ContactMessage $record): bool => ! $record->is_read)
                    ->action(function (ContactMessage $record): void {
                        $record->update(['is_read' => true]);
                        Notification::make()
                            ->title('Mensaje marcado como leído')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('viewInResource')
                    ->label('Ver Todos')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (): string => ContactMessageResource::getUrl('index')),
            ])
            ->paginated(false);
    }
}
