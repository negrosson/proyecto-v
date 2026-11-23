<?php

declare(strict_types=1);

namespace App\Filament\Resources\GalleryImageResource\Pages;

use App\Actions\Admin\AssignGalleryImagesToMontajeAction;
use App\Actions\Admin\ImportLocalGalleryImagesAction;
use App\Filament\Resources\GalleryImageResource;
use App\Models\GalleryImage;
use App\Models\Montaje;
use App\Services\Security\SafeImageSanitizer;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;

class ListGalleryImages extends ListRecords
{
    protected static string $resource = GalleryImageResource::class;

    /**
     * Pestañas rápidas para filtrar fotos sin clasificar, clasificadas y asignadas a montajes.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas las Fotos')
                ->badge(fn (): int => GalleryImage::query()->count()),

            'unclassified' => Tab::make('Sin Clasificar')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('category', 'Sin clasificar'))
                ->badge(fn (): int => GalleryImage::query()->where('category', 'Sin clasificar')->count())
                ->badgeColor('warning'),

            'classified' => Tab::make('Ya Categorizadas')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('category', '!=', 'Sin clasificar'))
                ->badge(fn (): int => GalleryImage::query()->where('category', '!=', 'Sin clasificar')->count())
                ->badgeColor('success'),

            'unassigned' => Tab::make('Sin Montaje Asignado')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNull('montaje_id'))
                ->badge(fn (): int => GalleryImage::query()->whereNull('montaje_id')->count()),

            'assigned' => Tab::make('En Montajes')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNotNull('montaje_id'))
                ->badge(fn (): int => GalleryImage::query()->whereNotNull('montaje_id')->count())
                ->badgeColor('info'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // 1. Subida Masiva Segura (Drag & Drop de múltiples fotos con sanitización WebP)
            Actions\Action::make('bulkUpload')
                ->label('Subida Masiva de Fotos')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('amber')
                ->modalHeading('Subida Masiva y Segura a la Galería')
                ->modalDescription('Arrastra múltiples fotografías de una sola vez. Cada imagen es inspeccionada contra código malicioso, limpiada de metadatos EXIF y convertida a WebP seguro.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Guardar Fotos en Galería')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('category')
                                ->label('Categoría Inicial para este Lote')
                                ->options(GalleryImage::CATEGORIES)
                                ->default('Sin clasificar')
                                ->required(),

                            Forms\Components\TextInput::make('title_prefix')
                                ->label('Prefijo de Título')
                                ->default('Evento')
                                ->required()
                                ->maxLength(100)
                                ->helperText('Se numerarán automáticamente (ej: Evento #01, Evento #02...).'),
                        ]),

                    Forms\Components\FileUpload::make('images')
                        ->label('Selecciona o Arrastra las Fotografías (Hasta 50 por lote)')
                        ->disk('public')
                        ->directory('galeria')
                        ->visibility('public')
                        ->multiple()
                        ->reorderable()
                        ->maxFiles(50)
                        ->image()
                        ->maxSize(4096)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->saveUploadedFileUsing(fn (UploadedFile $file, BaseFileUpload $component): string => SafeImageSanitizer::sanitizeForFilament($file, 'public', 'galeria', $component->getStatePath()))
                        ->required()
                        ->helperText('Solo JPG, PNG o WebP verificados (máx. 4 MB por foto).')
                        ->columnSpanFull(),
                ])
                ->action(function (array $data): void {
                    $paths = is_array($data['images'] ?? null) ? array_values($data['images']) : [];
                    $category = (string) ($data['category'] ?? 'Sin clasificar');
                    $prefix = trim((string) ($data['title_prefix'] ?? 'Evento')) ?: 'Evento';

                    $nextOrder = ((int) GalleryImage::query()->max('sort_order')) + 1;
                    $existingCount = GalleryImage::query()->count();
                    $created = 0;

                    foreach ($paths as $index => $path) {
                        if (! is_string($path) || $path === '') {
                            continue;
                        }

                        $num = str_pad((string) ($existingCount + $index + 1), 2, '0', STR_PAD_LEFT);

                        GalleryImage::query()->create([
                            'title' => "{$prefix} #{$num}",
                            'category' => $category,
                            'image_path' => $path,
                            'sort_order' => $nextOrder++,
                            'is_active' => true,
                        ]);
                        $created++;
                    }

                    Notification::make()
                        ->title("{$created} fotografía(s) sanitizadas y agregadas a la Galería")
                        ->success()
                        ->send();
                }),

            // 2. Enviar a Montajes filtrando por Categoría (Todas o Selección Manual)
            Actions\Action::make('sendByCategoryToMontaje')
                ->label('Enviar por Categoría a Montaje')
                ->icon('heroicon-o-arrows-right-left')
                ->color('gray')
                ->modalHeading('Enviar Fotos de una Categoría a Montajes & Galerías')
                ->modalDescription('Elige una categoría de tu Galería, decide si enviar todas sus fotos o elegirlas manualmente, y asígnalas a un Montaje.')
                ->modalWidth('2xl')
                ->modalSubmitActionLabel('Enviar a Montaje')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('source_category')
                                ->label('1. Categoría de Galería')
                                ->options(function (): array {
                                    $counts = GalleryImage::query()
                                        ->selectRaw('category, COUNT(*) as total')
                                        ->groupBy('category')
                                        ->pluck('total', 'category')
                                        ->all();

                                    $result = [];
                                    foreach (GalleryImage::CATEGORIES as $key => $label) {
                                        $c = (int) ($counts[$key] ?? 0);
                                        $result[$key] = "{$label} ({$c} fotos)";
                                    }

                                    return $result;
                                })
                                ->live()
                                ->afterStateUpdated(fn (Forms\Set $set) => $set('gallery_image_ids', []))
                                ->required(),

                            Forms\Components\Select::make('target_montaje_id')
                                ->label('2. Montaje de Destino')
                                ->options(fn (): array => Montaje::query()->orderBy('sort_order')->pluck('title', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->required()
                                ->exists('montajes', 'id'),
                        ]),

                    Forms\Components\Radio::make('selection_mode')
                        ->label('3. ¿Qué fotos de esta categoría deseas enviar?')
                        ->options([
                            'all' => 'Enviar TODAS las fotos de esta categoría al Montaje',
                            'manual' => 'Seleccionar manualmente qué fotos de esta categoría enviar',
                        ])
                        ->default('all')
                        ->live()
                        ->required(),

                    Forms\Components\CheckboxList::make('gallery_image_ids')
                        ->label('Marca las fotografías a enviar')
                        ->options(function (Forms\Get $get): array {
                            $cat = (string) $get('source_category');
                            if ($cat === '') {
                                return [];
                            }

                            return GalleryImage::query()
                                ->where('category', $cat)
                                ->orderBy('sort_order')
                                ->get(['id', 'title', 'original_name'])
                                ->mapWithKeys(fn (GalleryImage $img): array => [
                                    $img->id => $img->title.($img->original_name ? " ({$img->original_name})" : ''),
                                ])
                                ->all();
                        })
                        ->searchable()
                        ->bulkToggleable()
                        ->columns(2)
                        ->visible(fn (Forms\Get $get): bool => $get('selection_mode') === 'manual' && filled($get('source_category')))
                        ->required(fn (Forms\Get $get): bool => $get('selection_mode') === 'manual'),

                    Forms\Components\Toggle::make('set_first_as_cover')
                        ->label('Usar la primera foto como Portada Principal del Montaje')
                        ->default(false),
                ])
                ->action(function (array $data, AssignGalleryImagesToMontajeAction $assignAction): void {
                    $category = (string) $data['source_category'];
                    $montajeId = (int) $data['target_montaje_id'];
                    $mode = (string) ($data['selection_mode'] ?? 'all');

                    $ids = $mode === 'all'
                        ? GalleryImage::query()->where('category', $category)->orderBy('sort_order')->pluck('id')->all()
                        : (is_array($data['gallery_image_ids'] ?? null) ? $data['gallery_image_ids'] : []);

                    $count = $assignAction->appendToMontaje(
                        montajeId: $montajeId,
                        galleryImageIds: $ids,
                        setFirstAsCover: (bool) ($data['set_first_as_cover'] ?? false),
                    );

                    if ($count === 0) {
                        Notification::make()
                            ->title('No había fotografías en esa selección para enviar')
                            ->warning()
                            ->send();

                        return;
                    }

                    $montajeTitle = Montaje::query()->whereKey($montajeId)->value('title') ?? 'Montaje';

                    Notification::make()
                        ->title("{$count} foto(s) enviadas al montaje «{$montajeTitle}»")
                        ->success()
                        ->send();
                }),

            // 3. Sincronizar carpeta local Documentos/Vicente eventos
            Actions\Action::make('syncLocalFolder')
                ->label('Importar Carpeta Local')
                ->icon('heroicon-o-folder-arrow-down')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Importar y Sanitizar Fotos de «Documentos/Vicente eventos»')
                ->modalDescription('Se importarán únicamente las fotos nuevas que aún no estén en la Galería (verificadas por hash SHA-256), limpiando metadatos EXIF y convirtiéndolas a WebP seguro.')
                ->modalSubmitActionLabel('Importar Fotos Nuevas')
                ->action(function (ImportLocalGalleryImagesAction $importAction): void {
                    $stats = $importAction->execute();

                    Notification::make()
                        ->title("Importación completada: {$stats['imported']} nuevas, {$stats['skipped']} ya existentes")
                        ->body($stats['failed'] > 0 ? "{$stats['failed']} archivo(s) fueron rechazados por el filtro de seguridad." : 'Todas las imágenes cumplen con la validación criptográfica y WebP.')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('Subir 1 Foto'),
        ];
    }
}
