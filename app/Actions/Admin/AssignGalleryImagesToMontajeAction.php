<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\GalleryImage;
use App\Models\Montaje;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AssignGalleryImagesToMontajeAction
{
    /**
     * Envía las fotografías seleccionadas de la Galería a un Montaje existente.
     *
     * @param  array<int|string>  $galleryImageIds
     */
    public function appendToMontaje(int $montajeId, array $galleryImageIds, bool $setFirstAsCover = false): int
    {
        /** @var Montaje|null $montaje */
        $montaje = Montaje::query()->find($montajeId);
        if ($montaje === null) {
            throw new InvalidArgumentException('El montaje de destino no existe.');
        }

        $cleanIds = array_values(array_unique(array_filter(
            array_map(static fn (mixed $id): int => (int) $id, $galleryImageIds),
            static fn (int $id): bool => $id > 0
        )));

        if ($cleanIds === []) {
            return 0;
        }

        $images = GalleryImage::query()
            ->whereIn('id', $cleanIds)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($images->isEmpty()) {
            return 0;
        }

        $newPaths = $images->pluck('image_path')->filter()->values()->all();
        if ($newPaths === []) {
            return 0;
        }

        DB::transaction(static function () use ($montaje, $images, $newPaths, $setFirstAsCover): void {
            $currentGallery = is_array($montaje->gallery) ? $montaje->gallery : [];
            $mergedGallery = array_values(array_unique(array_merge($currentGallery, $newPaths)));

            if ($setFirstAsCover || empty($montaje->cover_image)) {
                $montaje->cover_image = $newPaths[0];
            }

            $montaje->gallery = $mergedGallery;
            $montaje->save();

            GalleryImage::query()
                ->whereIn('id', $images->pluck('id')->all())
                ->update(['montaje_id' => $montaje->id]);
        });

        return $images->count();
    }

    /**
     * Crea un nuevo Montaje en «Montajes & Galerías» a partir de las fotografías seleccionadas en la Galería.
     *
     * @param  array<int|string>  $galleryImageIds
     * @param  array<string, mixed>  $montajeData
     */
    public function createMontajeFromImages(array $galleryImageIds, array $montajeData): Montaje
    {
        $cleanIds = array_values(array_unique(array_filter(
            array_map(static fn (mixed $id): int => (int) $id, $galleryImageIds),
            static fn (int $id): bool => $id > 0
        )));

        if ($cleanIds === []) {
            throw new InvalidArgumentException('Debes seleccionar al menos una fotografía para crear el montaje.');
        }

        $images = GalleryImage::query()
            ->whereIn('id', $cleanIds)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($images->isEmpty()) {
            throw new InvalidArgumentException('No se encontraron las fotografías seleccionadas.');
        }

        $paths = $images->pluck('image_path')->filter()->values()->all();
        $title = trim((string) ($montajeData['title'] ?? 'Nuevo Montaje de Evento'));
        $baseSlug = Str::slug($title) ?: 'montaje-evento';
        $slug = $baseSlug;
        $counter = 2;

        while (Montaje::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return DB::transaction(static function () use ($images, $paths, $title, $slug, $montajeData): Montaje {
            $montaje = Montaje::query()->create([
                'title' => $title,
                'slug' => $slug,
                'client' => $montajeData['client'] ?? null,
                'type' => (string) ($montajeData['type'] ?? 'Evento Empresa'),
                'year' => (string) ($montajeData['year'] ?? date('Y')),
                'location' => $montajeData['location'] ?? 'Santiago',
                'capacity' => $montajeData['capacity'] ?? null,
                'summary' => $montajeData['summary'] ?? null,
                'description' => $montajeData['description'] ?? null,
                'cover_image' => $paths[0] ?? null,
                'gallery' => $paths,
                'items_used' => is_array($montajeData['items_used'] ?? null) ? $montajeData['items_used'] : [],
                'sort_order' => ((int) Montaje::query()->max('sort_order')) + 1,
                'is_featured' => (bool) ($montajeData['is_featured'] ?? true),
                'is_active' => (bool) ($montajeData['is_active'] ?? true),
            ]);

            GalleryImage::query()
                ->whereIn('id', $images->pluck('id')->all())
                ->update(['montaje_id' => $montaje->id]);

            return $montaje;
        });
    }
}
