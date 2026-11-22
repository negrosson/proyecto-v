<?php

declare(strict_types=1);

namespace App\Services\Media;

use App\Models\GalleryImage;
use App\Models\Montaje;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Servicio centralizado de integridad y limpieza de archivos multimedia en disco `public`.
 *
 * Garantiza que nunca queden archivos huérfanos ocupando espacio al eliminar o reemplazar
 * imágenes en Productos, Montajes, Galería o Configuración del Sitio, sin romper nunca
 * referencias compartidas ni assets base de `images/`.
 */
final class PublicMediaCleaner
{
    /**
     * Directorios gestionados por subidas de usuarios dentro de `storage/app/public/`.
     *
     * @var array<int, string>
     */
    private const MANAGED_DIRECTORIES = [
        'products',
        'montajes',
        'galeria',
        'site',
    ];

    /**
     * Elimina físicamente una imagen del disco `public` solo si ya ningún modelo la referencia.
     */
    public static function deleteIfUnreferenced(?string $path): bool
    {
        $normalized = self::normalizePath($path);

        if ($normalized === null) {
            return false;
        }

        if (self::isReferencedAnywhere($normalized)) {
            return false;
        }

        if (! Storage::disk('public')->exists($normalized)) {
            return false;
        }

        return Storage::disk('public')->delete($normalized);
    }

    /**
     * Verifica si una ruta está siendo utilizada por algún registro activo en la base de datos.
     */
    public static function isReferencedAnywhere(string $normalizedPath): bool
    {
        if (Schema::hasTable('products') && Product::query()->where('image', $normalizedPath)->exists()) {
            return true;
        }

        if (Schema::hasTable('gallery_images') && GalleryImage::query()->where('image_path', $normalizedPath)->exists()) {
            return true;
        }

        if (Schema::hasTable('site_settings') && SiteSetting::query()
            ->where('logo_path', $normalizedPath)
            ->orWhere('hero_bg_image', $normalizedPath)
            ->exists()) {
            return true;
        }

        if (Schema::hasTable('montajes')) {
            if (Montaje::query()->where('cover_image', $normalizedPath)->exists()) {
                return true;
            }

            $montajes = Montaje::query()->whereNotNull('gallery')->get(['gallery']);
            foreach ($montajes as $montaje) {
                if (is_array($montaje->gallery) && in_array($normalizedPath, $montaje->gallery, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Recolecta todas las rutas actualmente referenciadas en base de datos.
     *
     * @return array<string, true>
     */
    public static function getAllReferencedPaths(): array
    {
        $paths = [];

        if (Schema::hasTable('products')) {
            foreach (Product::query()->whereNotNull('image')->pluck('image') as $p) {
                if (($n = self::normalizePath((string) $p)) !== null) {
                    $paths[$n] = true;
                }
            }
        }

        if (Schema::hasTable('gallery_images')) {
            foreach (GalleryImage::query()->whereNotNull('image_path')->pluck('image_path') as $p) {
                if (($n = self::normalizePath((string) $p)) !== null) {
                    $paths[$n] = true;
                }
            }
        }

        if (Schema::hasTable('site_settings')) {
            foreach (SiteSetting::query()->get(['logo_path', 'hero_bg_image']) as $setting) {
                foreach ([$setting->logo_path, $setting->hero_bg_image] as $p) {
                    if (($n = self::normalizePath($p ? (string) $p : null)) !== null) {
                        $paths[$n] = true;
                    }
                }
            }
        }

        if (Schema::hasTable('montajes')) {
            foreach (Montaje::query()->get(['cover_image', 'gallery']) as $montaje) {
                if (($n = self::normalizePath($montaje->cover_image)) !== null) {
                    $paths[$n] = true;
                }
                if (is_array($montaje->gallery)) {
                    foreach ($montaje->gallery as $item) {
                        if (($n = self::normalizePath(is_string($item) ? $item : null)) !== null) {
                            $paths[$n] = true;
                        }
                    }
                }
            }
        }

        return $paths;
    }

    /**
     * Escanea los directorios gestionados y elimina archivos huérfanos + temporales viejos de Livewire.
     *
     * @return array{deleted: array<int, string>, freed_bytes: int, kept: int, tmp_deleted: int}
     */
    public static function pruneOrphans(bool $dryRun = false): array
    {
        $referenced = self::getAllReferencedPaths();
        $disk = Storage::disk('public');

        $deleted = [];
        $freedBytes = 0;
        $kept = 0;

        foreach (self::MANAGED_DIRECTORIES as $dir) {
            if (! $disk->exists($dir)) {
                continue;
            }

            foreach ($disk->allFiles($dir) as $file) {
                $normalized = str_replace('\\', '/', ltrim($file, '/'));

                if (str_starts_with(basename($normalized), '.')) {
                    continue;
                }

                if (isset($referenced[$normalized])) {
                    $kept++;

                    continue;
                }

                $size = 0;
                try {
                    $size = (int) $disk->size($normalized);
                } catch (\Throwable) {
                }

                if (! $dryRun) {
                    $disk->delete($normalized);
                }

                $deleted[] = $normalized;
                $freedBytes += $size;
            }
        }

        // Limpiar archivos temporales antiguos de Livewire (> 24 horas)
        $tmpDeleted = 0;
        $tmpDirs = [
            storage_path('app/private/livewire-tmp'),
            storage_path('app/livewire-tmp'),
        ];

        $cutoff = now()->subDay()->getTimestamp();

        foreach ($tmpDirs as $tmpDir) {
            if (! is_dir($tmpDir)) {
                continue;
            }

            foreach (File::files($tmpDir) as $tmpFile) {
                if ($tmpFile->getMTime() < $cutoff) {
                    $freedBytes += (int) $tmpFile->getSize();
                    if (! $dryRun) {
                        @unlink($tmpFile->getPathname());
                    }
                    $tmpDeleted++;
                }
            }
        }

        return [
            'deleted' => $deleted,
            'freed_bytes' => $freedBytes,
            'kept' => $kept,
            'tmp_deleted' => $tmpDeleted,
        ];
    }

    /**
     * Normaliza una ruta y retorna null si es un asset protegido o externo que nunca debe borrarse.
     */
    private static function normalizePath(?string $path): ?string
    {
        if ($path === null) {
            return null;
        }

        $clean = str_replace('\\', '/', ltrim(trim($path), '/'));

        if (
            $clean === '' ||
            str_starts_with($clean, 'http://') ||
            str_starts_with($clean, 'https://') ||
            str_starts_with($clean, 'images/') ||
            str_contains($clean, '..')
        ) {
            return null;
        }

        return $clean;
    }
}
