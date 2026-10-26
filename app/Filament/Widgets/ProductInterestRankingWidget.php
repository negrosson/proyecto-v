<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ProductInterestRankingWidget extends BaseWidget
{
    protected static ?string $heading = 'Artículos de Menaje con Mayor Interés & Demanda en Vitrina';

    protected static ?int $sort = -2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('is_active', true)
                    ->with('category')
                    ->orderByDesc('clicks_count')
                    ->orderByDesc('quoted_count')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl('https://placehold.co/80x80/FAF8F5/1C1814?text=Item'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Cód.')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->weight('bold')
                    ->wrap()
                    ->description(fn (Product $record): string => $record->category?->name ?? 'Catálogo'),

                Tables\Columns\TextColumn::make('rental_price')
                    ->label('Precio Arriendo')
                    ->formatStateUsing(fn ($state): string => '$ '.number_format((float) $state, 0, ',', '.').' CLP')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock Actual')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'gray',
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clics de Interés')
                    ->badge()
                    ->color('amber')
                    ->icon('heroicon-m-cursor-arrow-rays')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('quoted_count')
                    ->label('Veces Cotizado')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-chat-bubble-left')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('units_quoted_count')
                    ->label('Unidades Pedidas')
                    ->badge()
                    ->color('gray')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('conversion')
                    ->label('Conversión')
                    ->state(function (Product $record): string {
                        $clicks = max((int) $record->clicks_count, 1);
                        $rate = round(((int) $record->quoted_count / $clicks) * 100, 1);

                        return "{$rate}%";
                    })
                    ->badge()
                    ->color(function (Product $record): string {
                        $clicks = max((int) $record->clicks_count, 1);
                        $rate = ((int) $record->quoted_count / $clicks) * 100;

                        return $rate >= 20 ? 'success' : ($rate > 0 ? 'amber' : 'gray');
                    })
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->paginated(false);
    }
}
