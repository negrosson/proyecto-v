<?php

declare(strict_types=1);

namespace App\Services\Security;

use Filament\Notifications\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Throwable;

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
     * Inspecciona, sanitiza y reconstruye destructivamente la imagen exportándola en WebP limpio
     * (con fallback seguro a PNG/JPEG si el servidor no tiene GD compilado con soporte WebP).
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

        // 2. Control de cabecera mágica MIME con finfo (si está disponible) y getimagesize
        $imageInfo = @getimagesize($realPath);
        if ($imageInfo === false || ! in_array($imageInfo[2], self::ALLOWED_IMAGE_TYPES, true)) {
            throw new InvalidArgumentException('El archivo no corresponde a una imagen válida (solo JPEG, PNG y WEBP son permitidos).');
        }

        $detectedMime = null;
        if (class_exists(\finfo::class)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->file($realPath);
        } else {
            $detectedMime = $imageInfo['mime'] ?? null;
        }

        if (! is_string($detectedMime) || ! in_array($detectedMime, self::ALLOWED_MIME_TYPES, true)) {
            throw new InvalidArgumentException('El tipo MIME real del archivo no corresponde a una imagen permitida (JPEG, PNG o WEBP).');
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
            throw new InvalidArgumentException('Las dimensiones de la imagen exceden los límites de seguridad permitidos (máximo 6000px o 25 megapíxeles).');
        }

        // 3. Inspección binaria profunda (Anti-Políglota / Anti-PHP Injection / Anti-XSS)
        $rawBytes = (string) file_get_contents($realPath);
        foreach (self::DANGEROUS_SEQUENCES as $sequence) {
            if (stripos($rawBytes, $sequence) !== false) {
                throw new InvalidArgumentException("Violación de seguridad: Se detectó la secuencia no permitida '{$sequence}' en el archivo binario.");
            }
        }

        // Detección específica de etiqueta corta PHP (<?= ...) evitando falsos positivos en entropía binaria JPEG
        if (preg_match('/<\?=\s*[\$a-zA-Z_(\'"`]/', $rawBytes) === 1) {
            throw new InvalidArgumentException("Violación de seguridad: Se detectó la secuencia no permitida '<?=' en el archivo binario.");
        }

        // 4. Re-codificación Destructiva con GD (Destrucción total de metadatos EXIF y payloads EOF)
        if (! extension_loaded('gd') || ! function_exists('imagecreatefromstring')) {
            throw new InvalidArgumentException('La extensión PHP GD no está habilitada en el servidor. Actívala en cPanel (Select PHP Version → gd).');
        }

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

        $cleanDirectory = trim($directory, '/');
        Storage::disk($disk)->makeDirectory($cleanDirectory);

        $extension = 'webp';
        if (! function_exists('imagewebp')) {
            $extension = $imageInfo[2] === IMAGETYPE_PNG && function_exists('imagepng') ? 'png' : 'jpg';
        }

        // Nombre seguro y aleatorio con Str::ulid()
        $fileName = Str::ulid().'.'.$extension;
        $relativeStoragePath = $cleanDirectory.'/'.$fileName;
        $targetFullPath = Storage::disk($disk)->path($relativeStoragePath);

        // Exportar imagen limpia sin EXIF (WebP prioritario; fallback PNG/JPEG si el hosting no compila GD con WebP)
        $saved = false;
        if ($extension === 'webp') {
            $saved = @imagewebp($cleanCanvas, $targetFullPath, 82);
        } elseif ($extension === 'png') {
            $saved = @imagepng($cleanCanvas, $targetFullPath, 6);
        } elseif (function_exists('imagejpeg')) {
            $jpegCanvas = imagecreatetruecolor($targetWidth, $targetHeight);
            if ($jpegCanvas !== false) {
                $white = imagecolorallocate($jpegCanvas, 255, 255, 255);
                if ($white !== false) {
                    imagefilledrectangle($jpegCanvas, 0, 0, $targetWidth, $targetHeight, $white);
                }
                imagecopy($jpegCanvas, $cleanCanvas, 0, 0, 0, 0, $targetWidth, $targetHeight);
                $saved = @imagejpeg($jpegCanvas, $targetFullPath, 85);
                imagedestroy($jpegCanvas);
            }
        }

        imagedestroy($sourceImage);
        imagedestroy($cleanCanvas);

        if (! $saved || ! file_exists($targetFullPath)) {
            throw new InvalidArgumentException('Ocurrió un error al guardar la imagen sanitizada en el almacenamiento. Verifica los permisos de escritura en storage/app/public.');
        }

        return $relativeStoragePath;
    }

    /**
     * Envoltorio para componentes FileUpload de Filament: convierte fallos de sanitización
     * en errores de validación visibles en el formulario en lugar de devolver HTTP 500.
     *
     * @throws ValidationException
     */
    public static function sanitizeForFilament(
        UploadedFile $file,
        string $disk = 'public',
        string $directory = 'products',
        string $statePath = 'data.image',
    ): string {
        try {
            return self::sanitize($file, $disk, $directory);
        } catch (InvalidArgumentException $e) {
            Log::warning('Imagen rechazada por SafeImageSanitizer', [
                'directory' => $directory,
                'original_name' => $file->getClientOriginalName(),
                'reason' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('No se pudo guardar la imagen')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                $statePath => [$e->getMessage()],
            ]);
        } catch (Throwable $e) {
            Log::error('Error inesperado al procesar imagen en SafeImageSanitizer', [
                'directory' => $directory,
                'original_name' => $file->getClientOriginalName(),
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            $friendlyMessage = 'No se pudo procesar la imagen en el servidor. Intenta con otra imagen JPG, PNG o WebP menor a 4 MB.';

            Notification::make()
                ->title('Error al procesar la imagen')
                ->body($friendlyMessage)
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                $statePath => [$friendlyMessage],
            ]);
        }
    }
}
