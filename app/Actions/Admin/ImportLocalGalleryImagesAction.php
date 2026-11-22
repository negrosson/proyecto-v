<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\GalleryImage;
use App\Services\Security\SafeImageSanitizer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Throwable;

class ImportLocalGalleryImagesAction
{
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Importa y sanitiza destructivamente (WebP sin EXIF) las imágenes desde la carpeta local de eventos.
     *
     * @return array{imported: int, skipped: int, failed: int}
     */
    public function execute(?string $directoryPath = null, string $defaultCategory = 'Sin clasificar'): array
    {
        $sourceDir = $directoryPath ?? base_path('Documentos/Vicente eventos');

        if (! is_dir($sourceDir)) {
            return ['imported' => 0, 'skipped' => 0, 'failed' => 0];
        }

        $files = File::files($sourceDir);
        $imported = 0;
        $skipped = 0;
        $failed = 0;

        $nextOrder = ((int) GalleryImage::query()->max('sort_order')) + 1;
        $existingCount = GalleryImage::query()->count();

        foreach ($files as $fileInfo) {
            $ext = strtolower($fileInfo->getExtension());
            if (! in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                continue;
            }

            $realPath = $fileInfo->getRealPath();
            if ($realPath === false || ! is_readable($realPath)) {
                $failed++;

                continue;
            }

            $fileHash = hash_file('sha256', $realPath);
            if ($fileHash !== false && GalleryImage::query()->where('file_hash', $fileHash)->exists()) {
                $skipped++;

                continue;
            }

            try {
                $sanitizedPath = SafeImageSanitizer::sanitize($realPath, 'public', 'galeria');
                $sequenceNumber = str_pad((string) ($existingCount + $imported + 1), 2, '0', STR_PAD_LEFT);

                GalleryImage::query()->create([
                    'title' => "Evento #{$sequenceNumber}",
                    'category' => $defaultCategory,
                    'image_path' => $sanitizedPath,
                    'original_name' => $fileInfo->getFilename(),
                    'file_hash' => $fileHash ?: null,
                    'sort_order' => $nextOrder++,
                    'is_active' => true,
                ]);

                $imported++;
            } catch (Throwable) {
                $failed++;
            }
        }

        Cache::forget('filament_badge_gallery_images_count');

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }
}
