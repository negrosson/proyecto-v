<?php

declare(strict_types=1);

namespace App\Services\Security;

use InvalidArgumentException;

/**
 * Codificador autónomo de códigos QR estándar ISO/IEC 18004 (Modo Byte, Nivel de Corrección M).
 *
 * Genera códigos QR 100% reales y escaneables sin enviar jamás secretos 2FA a APIs externas.
 */
final class QrCodeSvgGenerator
{
    /**
     * Tabla de parámetros para versiones 1..10 con corrección de errores Nivel M:
     * [totalCodewords, ecCodewordsPerBlock, numBlocksGroup1, dataCodewordsGroup1, numBlocksGroup2, dataCodewordsGroup2]
     *
     * @var array<int, array{0:int, 1:int, 2:int, 3:int, 4:int, 5:int}>
     */
    private const VERSION_M_SPECS = [
        1 => [26, 10, 1, 16, 0, 0],
        2 => [44, 16, 1, 28, 0, 0],
        3 => [70, 26, 1, 44, 0, 0],
        4 => [100, 18, 2, 32, 0, 0],
        5 => [134, 24, 2, 43, 0, 0],
        6 => [172, 16, 4, 27, 0, 0],
        7 => [196, 18, 4, 31, 0, 0],
        8 => [242, 22, 2, 38, 2, 39],
        9 => [292, 22, 3, 36, 2, 37],
        10 => [346, 26, 4, 43, 1, 44],
    ];

    /**
     * Coordenadas de patrones de alineación para versiones 2..10.
     *
     * @var array<int, array<int, int>>
     */
    private const ALIGNMENT_PATTERNS = [
        1 => [],
        2 => [6, 18],
        3 => [6, 22],
        4 => [6, 26],
        5 => [6, 30],
        6 => [6, 34],
        7 => [6, 22, 38],
        8 => [6, 24, 42],
        9 => [6, 26, 46],
        10 => [6, 28, 50],
    ];

    /**
     * Cadenas de formato BCH(15,5) precomputadas para Nivel M (indicador 00) y máscaras 0..7.
     *
     * @var array<int, int>
     */
    private const FORMAT_BITS_M = [
        0 => 0x5412,
        1 => 0x5125,
        2 => 0x5E7C,
        3 => 0x5B4B,
        4 => 0x45F9,
        5 => 0x40CE,
        6 => 0x4F97,
        7 => 0x4AA0,
    ];

    /**
     * Cadenas de versión BCH(18,6) precomputadas para versiones 7..10.
     *
     * @var array<int, int>
     */
    private const VERSION_BITS = [
        7 => 0x07C94,
        8 => 0x085BC,
        9 => 0x09A99,
        10 => 0x0A4D3,
    ];

    /**
     * Genera un Data URI `data:image/svg+xml;base64,...` de un código QR real ISO/IEC 18004.
     */
    public static function generateDataUri(string $payload, int $quietZone = 4): string
    {
        $svg = self::generateSvg($payload, $quietZone);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Genera el documento SVG vectorizado de un código QR real ISO/IEC 18004.
     */
    public static function generateSvg(string $payload, int $quietZone = 4): string
    {
        $matrix = self::encodeMatrix($payload);
        $size = count($matrix);
        $fullSize = $size + ($quietZone * 2);

        $pathParts = [];
        for ($y = 0; $y < $size; $y++) {
            $runStart = null;
            for ($x = 0; $x <= $size; $x++) {
                $isDark = $x < $size && $matrix[$y][$x] === 1;
                if ($isDark && $runStart === null) {
                    $runStart = $x;
                } elseif (! $isDark && $runStart !== null) {
                    $runLen = $x - $runStart;
                    $px = $runStart + $quietZone;
                    $py = $y + $quietZone;
                    $pathParts[] = "M{$px} {$py}h{$runLen}v1h-{$runLen}z";
                    $runStart = null;
                }
            }
        }

        $pathData = implode('', $pathParts);

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$fullSize.' '.$fullSize.'" width="240" height="240" shape-rendering="crispEdges">'
            .'<rect width="'.$fullSize.'" height="'.$fullSize.'" fill="#ffffff"/>'
            .'<path d="'.$pathData.'" fill="#09090b"/>'
            .'</svg>';
    }

    /**
     * Construye la matriz binaria NxN (0 = blanco, 1 = negro) del código QR.
     *
     * @return array<int, array<int, int>>
     */
    public static function encodeMatrix(string $payload): array
    {
        $bytes = array_values(unpack('C*', $payload) ?: []);
        $byteCount = count($bytes);
        $version = self::selectVersion($byteCount);
        $spec = self::VERSION_M_SPECS[$version];
        $size = 17 + (4 * $version);

        $dataCapacity = ($spec[2] * $spec[3]) + ($spec[4] * $spec[5]);
        $dataCodewords = self::buildDataCodewords($bytes, $version, $dataCapacity);
        $finalCodewords = self::interleaveWithErrorCorrection($dataCodewords, $spec);

        [$baseMatrix, $isFunction] = self::initFunctionPatterns($version, $size);
        self::placeDataBits($baseMatrix, $isFunction, $finalCodewords, $size);

        $bestMask = 0;
        $bestScore = PHP_INT_MAX;
        $bestMatrix = $baseMatrix;

        for ($mask = 0; $mask < 8; $mask++) {
            $candidate = self::applyMask($baseMatrix, $isFunction, $mask, $size);
            self::drawFormatAndVersionBits($candidate, $version, $mask, $size);
            $score = self::evaluatePenalty($candidate, $size);

            if ($score < $bestScore) {
                $bestScore = $score;
                $bestMask = $mask;
                $bestMatrix = $candidate;
            }
        }

        self::drawFormatAndVersionBits($bestMatrix, $version, $bestMask, $size);

        return $bestMatrix;
    }

    private static function selectVersion(int $byteCount): int
    {
        foreach (self::VERSION_M_SPECS as $version => $spec) {
            $dataCapacity = ($spec[2] * $spec[3]) + ($spec[4] * $spec[5]);
            $headerBits = $version <= 9 ? 12 : 20; // 4 bits modo Byte + 8 ó 16 bits longitud
            $maxBytes = intdiv(($dataCapacity * 8) - $headerBits, 8);

            if ($byteCount <= $maxBytes) {
                return $version;
            }
        }

        throw new InvalidArgumentException('El contenido excede la capacidad máxima soportada (versión 10 nivel M).');
    }

    /**
     * @param  array<int, int>  $bytes
     * @return array<int, int>
     */
    private static function buildDataCodewords(array $bytes, int $version, int $dataCapacity): array
    {
        $bits = [];
        // Modo Byte (0100)
        self::appendBits($bits, 0b0100, 4);
        // Contador de caracteres (8 bits para v1..v9, 16 bits para v10+)
        self::appendBits($bits, count($bytes), $version <= 9 ? 8 : 16);

        foreach ($bytes as $b) {
            self::appendBits($bits, $b, 8);
        }

        $capacityBits = $dataCapacity * 8;
        // Terminador de hasta 4 bits en cero
        $terminatorLen = min(4, $capacityBits - count($bits));
        self::appendBits($bits, 0, $terminatorLen);

        // Alinear a múltiplo de 8
        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }

        $codewords = [];
        $bitCount = count($bits);
        for ($i = 0; $i < $bitCount; $i += 8) {
            $val = 0;
            for ($j = 0; $j < 8; $j++) {
                $val = ($val << 1) | $bits[$i + $j];
            }
            $codewords[] = $val;
        }

        // Bytes de relleno alternos 0xEC y 0x11
        $padBytes = [0xEC, 0x11];
        $padIdx = 0;
        while (count($codewords) < $dataCapacity) {
            $codewords[] = $padBytes[$padIdx % 2];
            $padIdx++;
        }

        return $codewords;
    }

    /**
     * @param  array<int, int>  $bits
     */
    private static function appendBits(array &$bits, int $value, int $length): void
    {
        for ($i = $length - 1; $i >= 0; $i--) {
            $bits[] = ($value >> $i) & 1;
        }
    }

    /**
     * @param  array<int, int>  $dataCodewords
     * @param  array{0:int, 1:int, 2:int, 3:int, 4:int, 5:int}  $spec
     * @return array<int, int>
     */
    private static function interleaveWithErrorCorrection(array $dataCodewords, array $spec): array
    {
        [, $ecPerBlock, $g1Blocks, $g1DataLen, $g2Blocks, $g2DataLen] = $spec;
        $generator = self::reedSolomonGenerator($ecPerBlock);

        $dataBlocks = [];
        $ecBlocks = [];
        $offset = 0;

        for ($i = 0; $i < $g1Blocks; $i++) {
            $block = array_slice($dataCodewords, $offset, $g1DataLen);
            $offset += $g1DataLen;
            $dataBlocks[] = $block;
            $ecBlocks[] = self::reedSolomonComputeRemainder($block, $generator);
        }

        for ($i = 0; $i < $g2Blocks; $i++) {
            $block = array_slice($dataCodewords, $offset, $g2DataLen);
            $offset += $g2DataLen;
            $dataBlocks[] = $block;
            $ecBlocks[] = self::reedSolomonComputeRemainder($block, $generator);
        }

        $result = [];
        $maxDataLen = max($g1DataLen, $g2DataLen);
        $totalBlocks = count($dataBlocks);

        for ($i = 0; $i < $maxDataLen; $i++) {
            for ($b = 0; $b < $totalBlocks; $b++) {
                if (isset($dataBlocks[$b][$i])) {
                    $result[] = $dataBlocks[$b][$i];
                }
            }
        }

        for ($i = 0; $i < $ecPerBlock; $i++) {
            for ($b = 0; $b < $totalBlocks; $b++) {
                $result[] = $ecBlocks[$b][$i];
            }
        }

        return $result;
    }

    /**
     * @return array<int, int>
     */
    private static function reedSolomonGenerator(int $degree): array
    {
        $poly = array_fill(0, $degree, 0);
        $poly[$degree - 1] = 1;
        $root = 1;

        for ($i = 0; $i < $degree; $i++) {
            for ($j = 0; $j < $degree; $j++) {
                $poly[$j] = self::gfMultiply($poly[$j], $root);
                if ($j + 1 < $degree) {
                    $poly[$j] ^= $poly[$j + 1];
                }
            }
            $root = self::gfMultiply($root, 0x02);
        }

        return $poly;
    }

    /**
     * @param  array<int, int>  $data
     * @param  array<int, int>  $generator
     * @return array<int, int>
     */
    private static function reedSolomonComputeRemainder(array $data, array $generator): array
    {
        $degree = count($generator);
        $remainder = array_fill(0, $degree, 0);

        foreach ($data as $byte) {
            $factor = $byte ^ array_shift($remainder);
            $remainder[] = 0;
            for ($i = 0; $i < $degree; $i++) {
                $remainder[$i] ^= self::gfMultiply($generator[$i], $factor);
            }
        }

        return $remainder;
    }

    /**
     * Multiplicación en el campo de Galois GF(256) con polinomio primitivo 0x11D.
     */
    private static function gfMultiply(int $x, int $y): int
    {
        $z = 0;
        for ($i = 7; $i >= 0; $i--) {
            $z = ($z << 1) ^ (($z >> 7) * 0x11D);
            $z ^= (($y >> $i) & 1) * $x;
        }

        return $z;
    }

    /**
     * @return array{0: array<int, array<int, int>>, 1: array<int, array<int, bool>>}
     */
    private static function initFunctionPatterns(int $version, int $size): array
    {
        $matrix = array_fill(0, $size, array_fill(0, $size, 0));
        $isFunc = array_fill(0, $size, array_fill(0, $size, false));

        // 1. Patrones de temporización (Timing patterns)
        for ($i = 0; $i < $size; $i++) {
            $bit = ($i % 2 === 0) ? 1 : 0;
            $matrix[6][$i] = $bit;
            $isFunc[6][$i] = true;
            $matrix[$i][6] = $bit;
            $isFunc[$i][6] = true;
        }

        // 2. Tres patrones de búsqueda (Finder patterns + separadores)
        self::drawFinderPattern($matrix, $isFunc, 3, 3, $size);
        self::drawFinderPattern($matrix, $isFunc, $size - 4, 3, $size);
        self::drawFinderPattern($matrix, $isFunc, 3, $size - 4, $size);

        // 3. Patrones de alineación
        $alignCoords = self::ALIGNMENT_PATTERNS[$version];
        $n = count($alignCoords);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if (($i === 0 && $j === 0) || ($i === 0 && $j === $n - 1) || ($i === $n - 1 && $j === 0)) {
                    continue;
                }
                self::drawAlignmentPattern($matrix, $isFunc, $alignCoords[$i], $alignCoords[$j]);
            }
        }

        // 4. Reservar zonas de bits de formato (y módulo oscuro fijo)
        for ($i = 0; $i <= 8; $i++) {
            $isFunc[8][$i] = true;
            $isFunc[$i][8] = true;
        }
        for ($i = $size - 8; $i < $size; $i++) {
            $isFunc[8][$i] = true;
            $isFunc[$i][8] = true;
        }
        $matrix[$size - 8][8] = 1;
        $isFunc[$size - 8][8] = true;

        // 5. Reservar zonas de bits de versión (v7+)
        if ($version >= 7) {
            for ($i = 0; $i < 6; $i++) {
                for ($j = 0; $j < 3; $j++) {
                    $isFunc[$i][$size - 11 + $j] = true;
                    $isFunc[$size - 11 + $j][$i] = true;
                }
            }
        }

        return [$matrix, $isFunc];
    }

    /**
     * @param  array<int, array<int, int>>  $matrix
     * @param  array<int, array<int, bool>>  $isFunc
     */
    private static function drawFinderPattern(array &$matrix, array &$isFunc, int $cx, int $cy, int $size): void
    {
        for ($dy = -4; $dy <= 4; $dy++) {
            for ($dx = -4; $dx <= 4; $dx++) {
                $x = $cx + $dx;
                $y = $cy + $dy;
                if ($x < 0 || $x >= $size || $y < 0 || $y >= $size) {
                    continue;
                }
                $dist = max(abs($dx), abs($dy));
                $matrix[$y][$x] = ($dist !== 2 && $dist !== 4) ? 1 : 0;
                $isFunc[$y][$x] = true;
            }
        }
    }

    /**
     * @param  array<int, array<int, int>>  $matrix
     * @param  array<int, array<int, bool>>  $isFunc
     */
    private static function drawAlignmentPattern(array &$matrix, array &$isFunc, int $cx, int $cy): void
    {
        for ($dy = -2; $dy <= 2; $dy++) {
            for ($dx = -2; $dx <= 2; $dx++) {
                $dist = max(abs($dx), abs($dy));
                $matrix[$cy + $dy][$cx + $dx] = ($dist !== 1) ? 1 : 0;
                $isFunc[$cy + $dy][$cx + $dx] = true;
            }
        }
    }

    /**
     * @param  array<int, array<int, int>>  $matrix
     * @param  array<int, array<int, bool>>  $isFunc
     * @param  array<int, int>  $codewords
     */
    private static function placeDataBits(array &$matrix, array $isFunc, array $codewords, int $size): void
    {
        $bitIdx = 0;
        $totalBits = count($codewords) * 8;
        $upward = true;

        for ($right = $size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }

            for ($vert = 0; $vert < $size; $vert++) {
                $y = $upward ? ($size - 1 - $vert) : $vert;
                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    if (! $isFunc[$y][$x]) {
                        $bit = 0;
                        if ($bitIdx < $totalBits) {
                            $byte = $codewords[$bitIdx >> 3];
                            $bit = ($byte >> (7 - ($bitIdx & 7))) & 1;
                            $bitIdx++;
                        }
                        $matrix[$y][$x] = $bit;
                    }
                }
            }
            $upward = ! $upward;
        }
    }

    /**
     * @param  array<int, array<int, int>>  $matrix
     * @param  array<int, array<int, bool>>  $isFunc
     * @return array<int, array<int, int>>
     */
    private static function applyMask(array $matrix, array $isFunc, int $mask, int $size): array
    {
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($isFunc[$y][$x]) {
                    continue;
                }
                $invert = match ($mask) {
                    0 => ($x + $y) % 2 === 0,
                    1 => $y % 2 === 0,
                    2 => $x % 3 === 0,
                    3 => ($x + $y) % 3 === 0,
                    4 => (intdiv($y, 2) + intdiv($x, 3)) % 2 === 0,
                    5 => (($x * $y) % 2) + (($x * $y) % 3) === 0,
                    6 => ((($x * $y) % 2) + (($x * $y) % 3)) % 2 === 0,
                    7 => ((($x + $y) % 2) + (($x * $y) % 3)) % 2 === 0,
                    default => false,
                };
                if ($invert) {
                    $matrix[$y][$x] ^= 1;
                }
            }
        }

        return $matrix;
    }

    /**
     * @param  array<int, array<int, int>>  $matrix
     */
    private static function drawFormatAndVersionBits(array &$matrix, int $version, int $mask, int $size): void
    {
        $bits = self::FORMAT_BITS_M[$mask];

        // Copia 1 (alrededor del finder superior izquierdo)
        for ($i = 0; $i <= 5; $i++) {
            $matrix[$i][8] = ($bits >> $i) & 1;
        }
        $matrix[7][8] = ($bits >> 6) & 1;
        $matrix[8][8] = ($bits >> 7) & 1;
        $matrix[8][7] = ($bits >> 8) & 1;
        for ($i = 9; $i < 15; $i++) {
            $matrix[8][14 - $i] = ($bits >> $i) & 1;
        }

        // Copia 2 (superior derecho e inferior izquierdo)
        for ($i = 0; $i < 8; $i++) {
            $matrix[8][$size - 1 - $i] = ($bits >> $i) & 1;
        }
        for ($i = 8; $i < 15; $i++) {
            $matrix[$size - 15 + $i][8] = ($bits >> $i) & 1;
        }
        $matrix[$size - 8][8] = 1;

        // Bits de versión para v7..v10
        if ($version >= 7 && isset(self::VERSION_BITS[$version])) {
            $vBits = self::VERSION_BITS[$version];
            for ($i = 0; $i < 18; $i++) {
                $bit = ($vBits >> $i) & 1;
                $a = intdiv($i, 3);
                $b = $size - 11 + ($i % 3);
                $matrix[$a][$b] = $bit;
                $matrix[$b][$a] = $bit;
            }
        }
    }

    /**
     * @param  array<int, array<int, int>>  $matrix
     */
    private static function evaluatePenalty(array $matrix, int $size): int
    {
        $penalty = 0;

        // Regla 1: corridas consecutivas de >= 5 módulos del mismo color en filas y columnas
        for ($y = 0; $y < $size; $y++) {
            $runColor = $matrix[$y][0];
            $runLen = 1;
            for ($x = 1; $x < $size; $x++) {
                if ($matrix[$y][$x] === $runColor) {
                    $runLen++;
                    if ($runLen === 5) {
                        $penalty += 3;
                    } elseif ($runLen > 5) {
                        $penalty += 1;
                    }
                } else {
                    $runColor = $matrix[$y][$x];
                    $runLen = 1;
                }
            }
        }

        for ($x = 0; $x < $size; $x++) {
            $runColor = $matrix[0][$x];
            $runLen = 1;
            for ($y = 1; $y < $size; $y++) {
                if ($matrix[$y][$x] === $runColor) {
                    $runLen++;
                    if ($runLen === 5) {
                        $penalty += 3;
                    } elseif ($runLen > 5) {
                        $penalty += 1;
                    }
                } else {
                    $runColor = $matrix[$y][$x];
                    $runLen = 1;
                }
            }
        }

        // Regla 2: bloques 2x2 del mismo color
        for ($y = 0; $y < $size - 1; $y++) {
            for ($x = 0; $x < $size - 1; $x++) {
                $c = $matrix[$y][$x];
                if ($c === $matrix[$y][$x + 1] && $c === $matrix[$y + 1][$x] && $c === $matrix[$y + 1][$x + 1]) {
                    $penalty += 3;
                }
            }
        }

        // Regla 4: proporción de módulos oscuros cercana al 50%
        $dark = 0;
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $dark += $matrix[$y][$x];
            }
        }
        $total = $size * $size;
        $k = (int) floor(abs(($dark * 20) - ($total * 10)) / $total);
        $penalty += $k * 10;

        return $penalty;
    }
}
