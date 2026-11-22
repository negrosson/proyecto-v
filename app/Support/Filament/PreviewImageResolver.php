<?php

declare(strict_types=1);

namespace App\Support\Filament;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

final class PreviewImageResolver
{
    /**
     * Resuelve el estado actual de un FileUpload simple de Filament (TemporaryUploadedFile, array UUID o string guardado)
     * a una URL previsualizable de inmediato en el navegador.
     */
    public static function resolveSingle(mixed $state): ?string
    {
        $urls = self::resolveMultiple($state);

        return $urls[0] ?? null;
    }

    /**
     * Resuelve el estado actual de un FileUpload simple o múltiple de Filament a un arreglo de URLs únicas.
     *
     * @return array<int, string>
     */
    public static function resolveMultiple(mixed $state): array
    {
        if (empty($state)) {
            return [];
        }

        $items = is_array($state) ? array_values($state) : [$state];
        $urls = [];

        foreach ($items as $item) {
            $url = self::resolveItem($item);
            if ($url !== null) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    private static function resolveItem(mixed $item): ?string
    {
        if ($item instanceof TemporaryUploadedFile) {
            try {
                if ($item->isPreviewable()) {
                    return $item->temporaryUrl();
                }
            } catch (Throwable) {
                return null;
            }

            return null;
        }

        if (! is_string($item) || trim($item) === '') {
            return null;
        }

        $clean = trim($item);

        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://') || str_starts_with($clean, 'data:')) {
            return $clean;
        }

        if (str_starts_with($clean, 'images/')) {
            return asset($clean);
        }

        return asset('storage/'.ltrim($clean, '/'));
    }
}
