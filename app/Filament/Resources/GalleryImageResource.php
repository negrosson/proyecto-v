<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Actions\Admin\AssignGalleryImagesToMontajeAction;
use App\Filament\Resources\GalleryImageResource\Pages;
use App\Models\GalleryImage;
use App\Models\Montaje;
use App\Services\Security\SafeImageSanitizer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Contenido & Vitrina Web';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Galería';

    protected static ?string $modelLabel = 'Foto de Galería';

    protected static ?string $pluralModelLabel = 'Galería';

    protected static ?string $slug = 'galeria';

    public static function getNavigationBadge(): ?string
    {
        return (string) cache()->remember(
            'filament_badge_gallery_images_count',
            60,
            static fn (): string => (string) static::getModel()::count()
        );
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('montaje');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fotografía & Clasificación')
                    ->description('Todas las imágenes pasan por sanitización binaria anti-políglota, eliminación de metadatos EXIF y conversión segura a WebP.')
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título / Referencia de la Foto')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Ej: Mesa Imperial Carpa Teno'),

                        Forms\Components\Select::make('category')
                            ->label('Categoría de Galería')
                            ->options(GalleryImage::CATEGORIES)
                            ->default('Sin clasificar')
                            ->required()
                            ->searchable(),

                        Forms\Components\FileUpload::make('image_path')
                            ->label('Fotografía (Sanitizada a WebP)')
                            ->disk('public')
                            ->directory('galeria')
                            ->visibility('public')
                            ->image()
                            ->maxSize(4096)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->saveUploadedFileUsing(fn (UploadedFile $file): string => SafeImageSanitizer::sanitize($file, 'public', 'galeria'))
                            ->deleteUploadedFileUsing(function (?string $file): void {
                                if ($file && ! str_starts_with($file, 'images/') && Storage::disk('public')->exists($file)) {
                                    Storage::disk('public')->delete($file);
                                }
                            })
                            ->deletable(true)
                            ->openable()
                            ->downloadable()
                            ->required()
                            ->helperText('Formatos permitidos: JPG, PNG, WebP (máx. 4 MB). Se limpia de EXIF/scripts y se optimiza a WebP automáticamente.')
                            ->columnSpanFull(),

                        Forms\Components\Select::make('montaje_id')
                            ->label('Vinculada al Montaje (Opcional)')
                            ->relationship('montaje', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Indica si esta foto ya forma parte de un Montaje publicado.'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Orden de Visualización')
                            ->numeric()
                            ->integer()
                            ->default(0)
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas / Detalles del Evento (Opcional)')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Vista Previa')
                    ->size(88)
                    ->square(),

                Tables\Columns\TextInputColumn::make('title')
                    ->label('Título / Nombre (Editable en 1 clic)')
                    ->rules(['required', 'string', 'max:150'])
                    ->searchable()
                    ->sortable(),

                Tables\Columns\SelectColumn::make('category')
                    ->label('Categoría (Clasificación Rápida)')
                    ->options(GalleryImage::CATEGORIES)
                    ->selectablePlaceholder(false)
                    ->sortable(),

                Tables\Columns\TextColumn::make('montaje.title')
                    ->label('En Montaje')
                    ->placeholder('Disponible')
                    ->badge()
                    ->color(fn (?string $state): string => filled($state) ? 'success' : 'gray')
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('sort_order')
                    ->label('Orden')
                    ->rules(['required', 'integer', 'min:0', 'max:99999'])
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('original_name')
                    ->label('Archivo Original')
                    ->limit(22)
                    ->color('gray')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Filtrar por Categoría')
                    ->options(GalleryImage::CATEGORIES),

                Tables\Filters\SelectFilter::make('montaje_id')
                    ->label('Filtrar por Montaje')
                    ->relationship('montaje', 'title'),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Ver Grande')
                    ->icon('heroicon-o-magnifying-glass-plus')
                    ->color('amber')
                    ->modalHeading(fn (GalleryImage $record): string => $record->title)
                    ->modalContent(fn (GalleryImage $record) => view('filament.components.gallery-image-preview-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),

                Tables\Actions\Action::make('sendSingleToMontaje')
                    ->label('Enviar a Montaje')
                    ->icon('heroicon-o-camera')
                    ->color('gray')
                    ->form([
                        Forms\Components\Select::make('montaje_id')
                            ->label('Selecciona el Montaje de Destino')
                            ->options(fn (): array => Montaje::query()->orderBy('sort_order')->pluck('title', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->exists('montajes', 'id'),
                        Forms\Components\Toggle::make('set_as_cover')
                            ->label('Establecer también como Foto de Portada Principal del Montaje')
                            ->default(false),
                    ])
                    ->action(function (GalleryImage $record, array $data, AssignGalleryImagesToMontajeAction $assignAction): void {
                        $assignAction->appendToMontaje(
                            montajeId: (int) $data['montaje_id'],
                            galleryImageIds: [$record->id],
                            setFirstAsCover: (bool) ($data['set_as_cover'] ?? false),
                        );

                        Notification::make()
                            ->title('Fotografía enviada al Montaje correctamente')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // 1. Clasificación masiva en lote
                    Tables\Actions\BulkAction::make('bulkAssignCategory')
                        ->label('1. Asignar Categoría a Seleccionadas')
                        ->icon('heroicon-o-tag')
                        ->color('amber')
                        ->form([
                            Forms\Components\Select::make('category')
                                ->label('Nueva Categoría para las Fotos Seleccionadas')
                                ->options(GalleryImage::CATEGORIES)
                                ->required(),
                        ])
                        ->modalHeading('Clasificar fotografías en lote')
                        ->modalSubmitActionLabel('Aplicar Categoría')
                        ->action(function (Collection $records, array $data): void {
                            $category = (string) $data['category'];
                            GalleryImage::query()
                                ->whereIn('id', $records->pluck('id')->all())
                                ->update(['category' => $category]);

                            Notification::make()
                                ->title("{$records->count()} foto(s) clasificadas en «{$category}»")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // 2. Enviar fotos seleccionadas a un Montaje existente
                    Tables\Actions\BulkAction::make('bulkSendToMontaje')
                        ->label('2. Enviar a un Montaje Existente')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('montaje_id')
                                ->label('Montaje de Destino (en Montajes & Galerías)')
                                ->options(fn (): array => Montaje::query()->orderBy('sort_order')->pluck('title', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->exists('montajes', 'id'),
                            Forms\Components\Toggle::make('set_first_as_cover')
                                ->label('Usar la primera foto seleccionada como Portada Principal del Montaje')
                                ->default(false),
                        ])
                        ->modalHeading('Agregar fotografías seleccionadas a un Montaje')
                        ->modalDescription('Las fotos seleccionadas se integrarán a la galería del montaje elegido sin tener que volver a subirlas.')
                        ->modalSubmitActionLabel('Enviar a Montaje')
                        ->action(function (Collection $records, array $data, AssignGalleryImagesToMontajeAction $assignAction): void {
                            $montajeId = (int) $data['montaje_id'];
                            $count = $assignAction->appendToMontaje(
                                montajeId: $montajeId,
                                galleryImageIds: $records->pluck('id')->all(),
                                setFirstAsCover: (bool) ($data['set_first_as_cover'] ?? false),
                            );

                            $montajeTitle = Montaje::query()->whereKey($montajeId)->value('title') ?? 'Montaje';

                            Notification::make()
                                ->title("{$count} foto(s) agregadas a la galería de «{$montajeTitle}»")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // 3. Crear un Nuevo Montaje completo con las fotos seleccionadas
                    Tables\Actions\BulkAction::make('bulkCreateMontaje')
                        ->label('3. Crear Nuevo Montaje con Seleccionadas')
                        ->icon('heroicon-o-sparkles')
                        ->color('info')
                        ->form([
                            Forms\Components\TextInput::make('title')
                                ->label('Título del Nuevo Montaje / Evento')
                                ->required()
                                ->maxLength(150)
                                ->placeholder('Ej: Matrimonio Campestre & Carpa Imperial'),
                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('client')
                                        ->label('Cliente / Empresa')
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
                                        ->default('Matrimonio & Boda')
                                        ->required(),
                                    Forms\Components\TextInput::make('year')
                                        ->label('Año')
                                        ->default(date('Y'))
                                        ->required()
                                        ->maxLength(20),
                                ]),
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('location')
                                        ->label('Lugar o Comuna')
                                        ->default('Santiago')
                                        ->maxLength(150),
                                    Forms\Components\TextInput::make('capacity')
                                        ->label('Capacidad / Personas')
                                        ->placeholder('Ej: 200 personas')
                                        ->maxLength(150),
                                ]),
                            Forms\Components\Textarea::make('summary')
                                ->label('Resumen Corto')
                                ->rows(2)
                                ->maxLength(300),
                            Forms\Components\Textarea::make('description')
                                ->label('Descripción del Montaje')
                                ->rows(3)
                                ->maxLength(2500),
                        ])
                        ->modalHeading('Crear Nuevo Montaje en «Montajes & Galerías»')
                        ->modalDescription('La primera foto seleccionada será la Portada Principal y todas las seleccionadas conformarán la galería del evento.')
                        ->modalWidth('2xl')
                        ->modalSubmitActionLabel('Crear Montaje con estas Fotos')
                        ->action(function (Collection $records, array $data, AssignGalleryImagesToMontajeAction $assignAction): void {
                            $montaje = $assignAction->createMontajeFromImages(
                                galleryImageIds: $records->pluck('id')->all(),
                                montajeData: $data,
                            );

                            Notification::make()
                                ->title("Montaje «{$montaje->title}» creado con {$records->count()} foto(s)")
                                ->body('Ya está disponible y publicado en la sección Montajes & Galerías.')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryImages::route('/'),
            'create' => Pages\CreateGalleryImage::route('/create'),
            'edit' => Pages\EditGalleryImage::route('/{record}/edit'),
        ];
    }
}
