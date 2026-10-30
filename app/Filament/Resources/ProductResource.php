<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Services\Security\SafeImageSanitizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catálogo & Inventario';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralModelLabel = 'Productos y Menaje';

    public static function getNavigationBadge(): ?string
    {
        return (string) cache()->remember('filament_badge_products_count', 60, static fn () => (string) static::getModel()::where('is_active', true)->count());
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('category');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Producto')
                    ->description('Detalles principales para el catálogo y cálculo de cotizaciones')
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Categoría')
                            ->relationship('category', 'name')
                            ->preload()
                            ->searchable()
                            ->required()
                            ->exists('categories', 'id')
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre de la Categoría')
                                    ->required()
                                    ->string()
                                    ->minLength(2)
                                    ->maxLength(80)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => filled($state) ? $set('slug', Str::slug($state)) : null),

                                Forms\Components\TextInput::make('slug')
                                    ->label('Slug URL')
                                    ->required()
                                    ->string()
                                    ->minLength(2)
                                    ->maxLength(80)
                                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                                    ->unique(Category::class, 'slug')
                                    ->helperText('Solo minúsculas, números y guiones (ej: manteleria)'),
                            ])
                            ->editOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nombre de la Categoría')
                                    ->required()
                                    ->string()
                                    ->minLength(2)
                                    ->maxLength(80)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state, ?Category $record): void {
                                        if ($record && filled($state)) {
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
                                    ->unique(Category::class, 'slug', ignoreRecord: true)
                                    ->helperText('Solo minúsculas, números y guiones (ej: manteleria)'),
                            ]),

                        Forms\Components\TextInput::make('code')
                            ->label('Código de Referencia')
                            ->helperText('Código único numérico (ej: 10, 220, 1030)')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(99999999)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Producto')
                            ->required()
                            ->string()
                            ->minLength(2)
                            ->maxLength(120)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción / Medidas / Material')
                            ->nullable()
                            ->string()
                            ->maxLength(800)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Precios, Disponibilidad e Imagen')
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('rental_price')
                            ->label('Precio Arriendo (CLP)')
                            ->prefix('$')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(50000000),

                        Forms\Components\TextInput::make('stock')
                            ->label('Stock Disponible (Unidades)')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->maxValue(1000000)
                            ->default(0),

                        Forms\Components\FileUpload::make('image')
                            ->label('Fotografía de Producto')
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->image()
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->deletable(true)
                            ->openable()
                            ->downloadable()
                            ->nullable()
                            ->helperText('Formatos permitidos: JPG, PNG, WebP. Tamaño máximo: 4 MB. Puedes quitar la foto con el botón de papelera o subir una nueva.')
                            ->saveUploadedFileUsing(function (UploadedFile $file): string {
                                return SafeImageSanitizer::sanitize($file, 'public', 'products');
                            })
                            ->deleteUploadedFileUsing(function (?string $file): void {
                                if ($file && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Visible en Catálogo')
                            ->helperText('Determina si los clientes pueden ver y cotizar este artículo')
                            ->default(true),

                        Forms\Components\Toggle::make('is_featured')
                            ->label('Producto Destacado')
                            ->helperText('Se muestra con prioridad en la vitrina de la página principal')
                            ->default(false),

                        Forms\Components\Toggle::make('is_sale')
                            ->label('Disponible para Venta Directa')
                            ->default(false),
                    ]),

                Forms\Components\Section::make('Estadísticas')
                    ->collapsible()
                    ->collapsed()
                    ->columns(['default' => 1, 'sm' => 3])
                    ->schema([
                        Forms\Components\TextInput::make('clicks_count')
                            ->label('Clics / Interés')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->prefixIcon('heroicon-m-cursor-arrow-rays'),

                        Forms\Components\TextInput::make('quoted_count')
                            ->label('Veces Cotizado')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->prefixIcon('heroicon-m-chat-bubble-left'),

                        Forms\Components\TextInput::make('units_quoted_count')
                            ->label('Total Piezas Cotizadas')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->prefixIcon('heroicon-m-shopping-bag'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="%2371717a"><path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm1 2v10h14V7H5zm2 8l3-4 2.5 3 2-2.5 3.5 4.5H7z"/></svg>'),

                Tables\Columns\TextColumn::make('code')
                    ->label('Cód.')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Producto')
                    ->searchable()
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rental_price')
                    ->label('Arriendo')
                    ->formatStateUsing(fn ($state): string => '$ '.number_format((float) $state, 0, ',', '.'))
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    })
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('clicks_count')
                    ->label('Clics')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-cursor-arrow-rays')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('quoted_count')
                    ->label('Cotizado')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-chat-bubble-left-right')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('code', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Filtrar por Categoría')
                    ->relationship('category', 'name'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado Activo'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacados'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
