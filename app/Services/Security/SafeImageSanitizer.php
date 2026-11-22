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

    public const MAX_DIMENSION = 6000; // Máximo 6000px por lado (Anti-Decompression Bomb)

    public const MAX_PIXELS = 25_000_000; // Máximo 25 Megapíxeles en memoria

    public const MAX_OUTPUT_DIMENSION = 1600; // Redimensionado proporcional óptimo para web/móvil

    private const ALLOWED_IMAGE_TYPES = [
        IMAGETYPE_JPEG,
        IMAGETYPE_PNG,
        IMAGETYPE_WEBP,
    ];

    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const DANGEROUS_SEQUENCES = [
        '<?php',
        '<?=',
        '<script',
        '<!doctype',
        '<html',
        '<svg',
        'eval(',
        'base64_decode',
        'phar://',
        'javascript:',
    ];

    /**
     * Inspecciona, sanitiza y reconstruye destructivamente la imagen exportándola en WebP limpio.
     *
     * @throws InvalidArgumentException
     */
    public static function sanitize(UploadedFile|string $file, string $disk = 'public', string $directory = 'products'): string
    {
        if (preg_match('/\A[a-zA-Z0-9_\/-]+\z/', $directory) !== 1 || str_contains($directory, '..')) {
            throw new InvalidArgumentException('Directorio de destino inválido o inseguro.');
        }

        $realPath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (! is_string($realPath) || ! file_exists($realPath) || ! is_readable($realPath)) {
            throw new InvalidArgumentException('El archivo de imagen no existe o no tiene permisos de lectura.');
        }

        // 1. Control estricto de tamaño
        $fileSize = filesize($realPath);
        if ($fileSize === false || $fileSize <= 0 || $fileSize > self::MAX_BYTES) {
            throw new InvalidArgumentException('El archivo supera el tamaño máximo permitido de 4MB o está vacío.');
        }

        // 2. Control de cabecera mágica MIME con finfo y getimagesize
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($realPath);
        if (! is_string($detectedMime) || ! in_array($detectedMime, self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidArgumentException('El tipo MIME real del archivo no corresponde a una imagen permitida (JPEG, PNG o WEBP).');
        }

        $imageInfo = @getimagesize($realPath);
        if ($imageInfo === false || ! in_array($imageInfo[2], self::ALLOWED_IMAGE_TYPES, true)) {
            throw new InvalidArgumentException('El archivo no corresponde a una imagen válida (solo JPEG, PNG y WEBP son permitidos).');
        }

        // 2.1 Mitigación contra Pixel Flood / Decompression Bombs (OWASP DoS)
        $imgWidth = (int) ($imageInfo[0] ?? 0);
        $imgHeight = (int) ($imageInfo[1] ?? 0);
        if (
            $imgWidth <= 0 ||
            $imgHeight <= 0 ||
            $imgWidth > self::MAX_DIMENSION ||
            $imgHeight > self::MAX_DIMENSION ||
            ($imgWidth * $imgHeight) > self::MAX_PIXELS
        ) {
            throw new InvalidArgumentException('Las dimensiones de la imagen exceden los límites de seguridad permitidos.');
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

        // Redimensionar proporcionalmente si supera MAX_OUTPUT_DIMENSION (1600px) para ahorrar espacio y acelerar 4G
        $targetWidth = $width;
        $targetHeight = $height;
        if ($width > self::MAX_OUTPUT_DIMENSION || $height > self::MAX_OUTPUT_DIMENSION) {
            $scale = min(self::MAX_OUTPUT_DIMENSION / $width, self::MAX_OUTPUT_DIMENSION / $height);
            $targetWidth = max(1, (int) round($width * $scale));
            $targetHeight = max(1, (int) round($height * $scale));
        }

        // Crear un lienzo limpio en memoria sin metadatos
        $cleanCanvas = imagecreatetruecolor($targetWidth, $targetHeight);
        if ($cleanCanvas === false) {
            imagedestroy($sourceImage);
            throw new InvalidArgumentException('No fue posible inicializar el lienzo limpio en memoria.');
        }

        // Preservar transparencia
        imagealphablending($cleanCanvas, false);
        imagesavealpha($cleanCanvas, true);
        $transparentColor = imagecolorallocatealpha($cleanCanvas, 0, 0, 0, 127);
        if ($transparentColor !== false) {
            imagefilledrectangle($cleanCanvas, 0, 0, $targetWidth, $targetHeight, $transparentColor);
        }

        imagecopyresampled($cleanCanvas, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        // Nombre seguro y aleatorio con Str::ulid()
        $fileName = Str::ulid().'.webp';
        $relativeStoragePath = trim($directory, '/').'/'.$fileName;

        Storage::disk($disk)->makeDirectory(trim($directory, '/'));
        $targetFullPath = Storage::disk($disk)->path($relativeStoragePath);

        // Exportar como WebP puro sin EXIF optimizado
        $saved = imagewebp($cleanCanvas, $targetFullPath, 82);

        imagedestroy($sourceImage);
        imagedestroy($cleanCanvas);

        if (! $saved || ! file_exists($targetFullPath)) {
            throw new InvalidArgumentException('Ocurrió un error al guardar la imagen sanitizada en el almacenamiento.');
        }

        return $relativeStoragePath;
    }
}
