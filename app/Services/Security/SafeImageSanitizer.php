<?php

declare(strict_types=1);

namespace App\Services\Security;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Sanitizador Binario de Imágenes Anti-Políglotas y Eliminador Destructivo de EXIF (OWASP A03 / A08).
 *
 * Previene ataques de ejecución remota de código (RCE) y Cross-Site Scripting (XSS)
 * mediante inspección binaria profunda y reconstrucción de píxeles en memoria con GD.
 */
final class SafeImageSanitizer
{
    public const MAX_BYTES = 4 * 1024 * 1024; // 4 MB (4.194.304 bytes)

    private const ALLOWED_IMAGE_TYPES = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_WEBP,
    ];

    private const DANGEROUS_SEQUENCES = [
        '<?php',
        '<?=',
        '<script',
        '<!doctype',
        '<svg',
        'eval(',
        'base64_decode',
    ];

    /**
     * Inspecciona, sanitiza y reconstruye destructivamente la imagen exportándola en WebP limpio.
     *
     * @throws InvalidArgumentException
     */
    public static function sanitize(UploadedFile|string $file, string $disk = 'public', string $directory = 'products'): string
    {
        $realPath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! is_string($realPath) || ! file_exists($realPath) || ! is_readable($realPath)) {
            throw new InvalidArgumentException('El archivo de imagen no existe o no tiene permisos de lectura.');
        }

        // 1. Control estricto de tamaño
        $fileSize = filesize($realPath);
        if ($fileSize === false || $fileSize <= 0 || $fileSize > self::MAX_BYTES) {
            throw new InvalidArgumentException('El archivo supera el tamaño máximo permitido de 4MB o está vacío.');
        }

        // 2. Control de cabecera mágica MIME con getimagesize
        $imageInfo = @getimagesize($realPath);
        if ($imageInfo === false || ! in_array($imageInfo[2], self::ALLOWED_IMAGE_TYPES, true)) {
            throw new InvalidArgumentException('El archivo no corresponde a una imagen válida (solo JPEG, PNG y WEBP son permitidos).');
        }

        // 3. Inspección binaria profunda (Anti-Políglota / Anti-PHP Injection / Anti-XSS)
        $rawBytes = (string) file_get_contents($realPath);
        foreach (self::DANGEROUS_SEQUENCES as $sequence) {
            if (stripos($rawBytes, $sequence) !== false) {
                throw new InvalidArgumentException("Violación de seguridad: Se detectó la secuencia no permitida '{$sequence}' en el archivo binario.");
            }
        }

        // 4. Re-codificación Destructiva con GD (Destrucción total de metadatos EXIF y payloads EOF)
        $sourceImage = @imagecreatefromstring($rawBytes);
        if ($sourceImage === false) {
            throw new InvalidArgumentException('La estructura de la imagen está corrupta o no pudo ser deserializada.');
        }

        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        // Crear un lienzo limpio en memoria sin metadatos
        $cleanCanvas = imagecreatetruecolor($width, $height);
        if ($cleanCanvas === false) {
            imagedestroy($sourceImage);
            throw new InvalidArgumentException('No fue posible inicializar el lienzo limpio en memoria.');
        }

        // Preservar transparencia
        imagealphablending($cleanCanvas, false);
        imagesavealpha($cleanCanvas, true);
        $transparentColor = imagecolorallocatealpha($cleanCanvas, 0, 0, 0, 127);
        if ($transparentColor !== false) {
            imagefilledrectangle($cleanCanvas, 0, 0, $width, $height, $transparentColor);
        }

        imagecopy($cleanCanvas, $sourceImage, 0, 0, 0, 0, $width, $height);

        // Nombre seguro y aleatorio con Str::ulid()
        $fileName = Str::ulid().'.webp';
        $relativeStoragePath = trim($directory, '/').'/'.$fileName;

        Storage::disk($disk)->makeDirectory(trim($directory, '/'));
        $targetFullPath = Storage::disk($disk)->path($relativeStoragePath);

        // Exportar como WebP puro sin EXIF
        $saved = imagewebp($cleanCanvas, $targetFullPath, 85);

        imagedestroy($sourceImage);
        imagedestroy($cleanCanvas);

        if (! $saved || ! file_exists($targetFullPath)) {
            throw new InvalidArgumentException('Ocurrió un error al guardar la imagen sanitizada en el almacenamiento.');
        }

        return $relativeStoragePath;
    }
}
