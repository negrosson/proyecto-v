<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Actions\Admin\MoveProductsToCategoryAction;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions\MountableAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catálogo & Inventario';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Categoría';

    protected static ?string $pluralModelLabel = 'Categorías';

    public static function getNavigationBadge(): ?string
    {
        return (string) cache()->remember('filament_badge_categories_count', 60, static fn () => (string) static::getModel()::count());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('products');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Categoría')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la Categoría')
                            ->required()
                            ->string()
                            ->minLength(2)
                            ->maxLength(80)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Forms\Set $set): void {
                                if ($operation === 'create' && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->required()
                            ->string()
                            ->minLength(2)
                            ->maxLength(80)
                            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->unique(ignoreRecord: true)
                            ->helperText('Solo minúsculas, números y guiones (ej: vajilla, manteleria, sillas)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Categoría')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->label('Productos Vinculados')
                    ->counts('products')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('moveProducts')
                    ->label('Mover productos')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('amber')
                    ->visible(fn (Category $record): bool => ($record->products_count ?? $record->products()->count()) > 0)
                    ->modalHeading(fn (Category $record): string => "Mover productos de «{$record->name}»")
                    ->modalDescription('Puedes trasladar todos los productos de esta categoría a otra o elegir manualmente cuáles mover.')
                    ->modalWidth('2xl')
                    ->modalSubmitActionLabel('Mover Productos')
                    ->form([
                        Forms\Components\Select::make('target_category_id')
                            ->label('Categoría de Destino')
                            ->options(fn (Category $record): array => Category::query()
                                ->where('id', '!=', $record->id)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->exists('categories', 'id'),

                        Forms\Components\Radio::make('selection_mode')
                            ->label('¿Qué productos deseas mover?')
                            ->options([
                                'all' => 'Mover TODOS los productos de esta categoría',
                                'manual' => 'Seleccionar manualmente los productos a mover',
                            ])
                            ->default('all')
                            ->live()
                            ->required(),

                        Forms\Components\CheckboxList::make('product_ids')
                            ->label('Selecciona los productos a trasladar')
                            ->helperText('Usa el buscador o haz clic en «Seleccionar todos / Desmarcar todos».')
                            ->options(fn (Category $record): array => Product::query()
                                ->where('category_id', $record->id)
                                ->orderBy('code')
                                ->get(['id', 'code', 'name'])
                                ->mapWithKeys(fn (Product $product): array => [
                                    $product->id => "[Cód. {$product->code}] {$product->name}",
                                ])
                                ->all())
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->visible(fn (Forms\Get $get): bool => $get('selection_mode') === 'manual')
                            ->required(fn (Forms\Get $get): bool => $get('selection_mode') === 'manual'),
                    ])
                    ->action(function (Category $record, array $data, MoveProductsToCategoryAction $moveAction): void {
                        $targetCategoryId = (int) $data['target_category_id'];
                        $mode = (string) ($data['selection_mode'] ?? 'all');
                        $productIds = is_array($data['product_ids'] ?? null) ? $data['product_ids'] : [];

                        $movedCount = $moveAction->execute(
                            targetCategoryId: $targetCategoryId,
                            sourceCategoryId: $record->id,
                            productIds: $productIds,
                            mode: $mode,
                        );

                        $targetName = Category::query()->whereKey($targetCategoryId)->value('name') ?? 'Destino';

                        Notification::make()
                            ->title("{$movedCount} producto(s) movidos de «{$record->name}» a «{$targetName}»")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Category $record, Tables\Actions\DeleteAction $action): void {
                        static::haltIfCategoryHasProducts($record, $action);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function (Collection $records): void {
                            $deletableCategories = $records->filter(fn (Category $category): bool => ! $category->products()->exists());
                            $skippedCount = $records->count() - $deletableCategories->count();

                            $deletableCategories->each->delete();

                            if ($skippedCount > 0) {
                                Notification::make()
                                    ->title("{$skippedCount} categoría(s) no se eliminaron porque tienen productos asociados")
                                    ->body('Mueve o elimina primero sus productos.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }

    /**
     * Evita borrar una categoría con productos: la llave foránea en cascada eliminaría todos sus productos.
     */
    public static function haltIfCategoryHasProducts(Category $category, MountableAction $action): void
    {
        $productsCount = $category->products()->count();

        if ($productsCount === 0) {
            return;
        }

        Notification::make()
            ->title('No se puede eliminar la categoría')
            ->body("Tiene {$productsCount} producto(s) asociados. Muévelos a otra categoría antes de eliminarla.")
            ->danger()
            ->send();

        $action->cancel();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
