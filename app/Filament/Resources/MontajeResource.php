<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MontajeResource\Pages;
use App\Models\Montaje;
use App\Services\Security\SafeImageSanitizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MontajeResource extends Resource
{
    protected static ?string $model = Montaje::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'Contenido & Vitrina Web';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Montaje de Evento';

    protected static ?string $pluralModelLabel = 'Montajes & Galerías';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Montaje_Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Textos & Detalles del Evento')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Título del Montaje / Evento')
                                            ->required()
                                            ->maxLength(150)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                                                if (empty($get('slug')) && ! empty($state)) {
                                                    $set('slug', Str::slug($state));
                                                }
                                            }),
                                        Forms\Components\TextInput::make('slug')
                                            ->label('Identificador Web (Slug)')
                                            ->required()
                                            ->unique(Montaje::class, 'slug', ignoreRecord: true)
                                            ->maxLength(150),
                                    ]),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('client')
                                            ->label('Cliente / Empresa')
                                            ->placeholder('Ej: Agrozzi, Matrimonio Privado...')
                                            ->maxLength(150),
                                        Forms\Components\Select::make('type')
                                            ->label('Tipo de Evento')
                                            ->options([
                                                'Evento Empresa' => 'Evento Empresa',
                                                'Gala & Cena de Fin de Año' => 'Gala & Cena de Fin de Año',
                                                'Matrimonio & Boda' => 'Matrimonio & Boda',
                                                'Boda & Banquetería' => 'Boda & Banquetería',
                                                'Graduación & Gala' => 'Graduación & Gala',
                                                'Aniversario & Fiesta' => 'Aniversario & Fiesta',
                                                'Lanzamiento de Marca' => 'Lanzamiento de Marca',
                                            ])
                                            ->default('Evento Empresa')
                                            ->required(),
                                        Forms\Components\TextInput::make('year')
                                            ->label('Año de Realización')
                                            ->default(date('Y'))
                                            ->maxLength(20)
                                            ->required(),
                                    ]),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('location')
                                            ->label('Lugar o Comuna')
                                            ->placeholder('Ej: Teno, Curicó / Las Condes, Santiago')
                                            ->maxLength(150),
                                        Forms\Components\TextInput::make('capacity')
                                            ->label('Capacidad / Personas')
                                            ->placeholder('Ej: Montaje completo para 250 personas')
                                            ->maxLength(150),
                                    ]),
                                Forms\Components\Textarea::make('summary')
                                    ->label('Resumen Corto (Para tarjetas de presentación)')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Descripción & Historia Completa del Montaje')
                                    ->rows(5)
                                    ->maxLength(2500)
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('sort_order')
                                            ->label('Orden de Visualización')
                                            ->numeric()
                                            ->default(0)
                                            ->required(),
                                        Forms\Components\Toggle::make('is_featured')
                                            ->label('Destacar en Página de Inicio')
                                            ->default(true),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Publicado / Activo en la Web')
                                            ->default(true),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Sección de Fotos & Galería')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('cover_image')
                                    ->label('Foto de Portada Principal')
                                    ->disk('public')
                                    ->directory('montajes')
                                    ->visibility('public')
                                    ->image()
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->saveUploadedFileUsing(fn (UploadedFile $file): string => SafeImageSanitizer::sanitize($file, 'public', 'montajes'))
                                    ->deleteUploadedFileUsing(function (?string $file): void {
                                        if ($file && Storage::disk('public')->exists($file)) {
                                            Storage::disk('public')->delete($file);
                                        }
                                    })
                                    ->deletable(true)
                                    ->openable()
                                    ->downloadable()
                                    ->nullable()
                                    ->helperText('Foto representativa principal. Máx 4 MB. Formato JPG, PNG o WebP. Puedes quitar la foto actual con el botón de papelera o subir una nueva.')
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('gallery')
                                    ->label('Galería Fotográfica del Evento (Múltiples Fotos)')
                                    ->disk('public')
                                    ->directory('montajes/galeria')
                                    ->visibility('public')
                                    ->multiple()
                                    ->reorderable()
                                    ->deletable(true)
                                    ->openable()
                                    ->downloadable()
                                    ->nullable()
                                    ->image()
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->saveUploadedFileUsing(fn (UploadedFile $file): string => SafeImageSanitizer::sanitize($file, 'public', 'montajes/galeria'))
                                    ->deleteUploadedFileUsing(function (?string $file): void {
                                        if ($file && Storage::disk('public')->exists($file)) {
                                            Storage::disk('public')->delete($file);
                                        }
                                    })
                                    ->helperText('Sube múltiples fotos para mostrar todos los ángulos del montaje. Puedes quitar cualquiera individualmente con el botón de papelera o reorganizarlas.')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Mobiliario & Artículos Utilizados')
                            ->icon('heroicon-o-tag')
                            ->schema([
                                Forms\Components\TagsInput::make('items_used')
                                    ->label('Artículos y Mobiliario Clave del Montaje')
                                    ->placeholder('Escribe un artículo y presiona Enter (ej: 250x Sillas Tiffany Blancas)')
                                    ->helperText('Permite a los clientes ver qué artículos específicos de nuestro catálogo componen este montaje.')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image_url')
                    ->label('Portada')
                    ->circular(false)
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título del Montaje')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('client')
                    ->label('Cliente / Empresa')
                    ->searchable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Año')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destacado')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado de Publicación'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destacados en Inicio'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListMontajes::route('/'),
            'create' => Pages\CreateMontaje::route('/create'),
            'edit' => Pages\EditMontaje::route('/{record}/edit'),
        ];
    }
}
