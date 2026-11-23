<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Customer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExport
{
    /**
     * Genera una respuesta CSV en streaming compatible con Excel (UTF-8 con BOM y delimitador `;`)
     * neutralizando inyección de fórmulas en cada celda.
     *
     * @param  list<string>  $headers
     * @param  iterable<int, list<mixed>>  $rows
     */
    public static function download(string $filename, array $headers, iterable $rows): StreamedResponse
    {
        $safeFilename = preg_replace('/[^A-Za-z0-9._-]+/', '-', $filename) ?: 'export.csv';

        return response()->streamDownload(static function () use ($headers, $rows): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            // UTF-8 BOM para que Excel abra acentos y eñes correctamente sin configuración manual
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv(
                $handle,
                array_map(static fn (string $header): string => Customer::sanitizeCsvCell($header), $headers),
                ';'
            );

            foreach ($rows as $row) {
                fputcsv(
                    $handle,
                    array_map(static fn (mixed $cell): string => Customer::sanitizeCsvCell($cell), $row),
                    ';'
                );
            }

            fclose($handle);
        }, $safeFilename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
