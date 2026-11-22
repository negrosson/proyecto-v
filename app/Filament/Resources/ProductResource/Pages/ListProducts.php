<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Actions\Admin\MoveProductsToCategoryAction;
use App\Filament\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('moveByCategory')
                ->label('Mover por Categoría')
                ->icon('heroicon-o-arrows-right-left')
                ->color('gray')
                ->modalHeading('Mover Productos entre Categorías')
                ->modalDescription('Elige una categoría de origen y decide si deseas trasladar todos sus productos de una vez o seleccionar manualmente cuáles mover.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Aplicar Traslado')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('source_category_id')
                                ->label('1. Categoría de Origen')
                                ->options(fn (): array => Category::query()
                                    ->withCount('products')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (Category $cat): array => [
                                        $cat->id => "{$cat->name} ({$cat->products_count} productos)",
                                    ])
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(fn (Forms\Set $set) => $set('product_ids', []))
                                ->required()
                                ->exists('categories', 'id'),

                            Forms\Components\Select::make('target_category_id')
                                ->label('2. Categoría de Destino')
                                ->options(fn (Forms\Get $get): array => Category::query()
                                    ->when(
                                        filled($get('source_category_id')),
                                        fn ($query) => $query->where('id', '!=', (int) $get('source_category_id'))
                                    )
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->different('source_category_id')
                                ->exists('categories', 'id'),
                        ]),

                    Forms\Components\Radio::make('selection_mode')
                        ->label('3. ¿Qué productos deseas mover?')
                        ->options([
                            'all' => 'Seleccionar TODOS los productos de la categoría de origen',
                            'manual' => 'Seleccionar manualmente qué productos de esta categoría mover',
                        ])
                        ->default('all')
                        ->live()
                        ->required(),

                    Forms\Components\CheckboxList::make('product_ids')
                        ->label('Marca los productos a trasladar')
                        ->helperText('Puedes usar el buscador o los botones «Seleccionar todos / Desmarcar todos».')
                        ->options(function (Forms\Get $get): array {
                            $sourceId = (int) $get('source_category_id');
                            if ($sourceId <= 0) {
                                return [];
                            }

                            return Product::query()
                                ->where('category_id', $sourceId)
                                ->orderBy('code')
                                ->get(['id', 'code', 'name'])
                                ->mapWithKeys(fn (Product $product): array => [
                                    $product->id => "[Cód. {$product->code}] {$product->name}",
                                ])
                                ->all();
                        })
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(2)
                        ->visible(fn (Forms\Get $get): bool => $get('selection_mode') === 'manual' && filled($get('source_category_id')))
                        ->required(fn (Forms\Get $get): bool => $get('selection_mode') === 'manual'),
                ])
                ->action(function (array $data, MoveProductsToCategoryAction $moveAction): void {
                    $sourceCategoryId = (int) $data['source_category_id'];
                    $targetCategoryId = (int) $data['target_category_id'];
                    $mode = (string) ($data['selection_mode'] ?? 'all');
                    $productIds = is_array($data['product_ids'] ?? null) ? $data['product_ids'] : [];

                    $movedCount = $moveAction->execute(
                        targetCategoryId: $targetCategoryId,
                        sourceCategoryId: $sourceCategoryId,
                        productIds: $productIds,
                        mode: $mode,
                    );

                    $targetName = Category::query()->whereKey($targetCategoryId)->value('name') ?? 'Destino';

                    if ($movedCount === 0) {
                        Notification::make()
                            ->title('No había productos para mover')
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title("{$movedCount} producto(s) movidos exitosamente a «{$targetName}»")
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make(),
        ];
    }
}
