<?php

namespace App\Services;

class Barcode128Service
{
    /**
     * Code 128 Patterns (Table B)
     */
    private static array $patterns = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', // 0-9
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', // 10-19
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', // 20-29
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', // 30-39
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', // 40-49
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', // 50-59
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', // 60-69
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', // 70-79
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', // 80-89
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', // 90-99
        '114131', '311141', '411131', '211412', '211214', '211232', '2331112'                               // 100-106
    ];

    /**
     * Render string as SVG Barcode Code 128
     */
    public static function getBarcodeSvg(string $code, int $height = 65, int $barWidth = 2): string
    {
        $code = trim($code);
        if (empty($code)) return '';

        $startCode = 104; // Start B
        $checkSum = $startCode;
        $encodedWeights = [$startCode];

        for ($i = 0; $i < strlen($code); $i++) {
            $char = ord($code[$i]);
            $val = $char - 32;
            if ($val < 0 || $val > 95) $val = 0;
            $encodedWeights[] = $val;
            $checkSum += $val * ($i + 1);
        }

        $checkDigit = $checkSum % 103;
        $encodedWeights[] = $checkDigit;
        $encodedWeights[] = 106; // Stop

        $patternStr = '';
        foreach ($encodedWeights as $val) {
            $patternStr .= self::$patterns[$val] ?? '';
        }

        $totalUnits = 0;
        for ($i = 0; $i < strlen($patternStr); $i++) {
            $totalUnits += (int) $patternStr[$i];
        }

        $svgWidth = $totalUnits * $barWidth + 20; // 10px quiet zone each side
        $svgHeight = $height + 25; // Space for text below

        $svg = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$svgWidth}\" height=\"{$svgHeight}\" viewBox=\"0 0 {$svgWidth} {$svgHeight}\" class=\"mx-auto select-none\">";
        $svg .= "<rect width=\"100%\" height=\"100%\" fill=\"#ffffff\"/>";

        $currentX = 10; // Left quiet zone
        for ($i = 0; $i < strlen($patternStr); $i++) {
            $unitWidth = ((int) $patternStr[$i]) * $barWidth;
            if ($i % 2 === 0) { // Bar (black)
                $svg .= "<rect x=\"{$currentX}\" y=\"5\" width=\"{$unitWidth}\" height=\"{$height}\" fill=\"#000000\"/>";
            }
            $currentX += $unitWidth;
        }

        // Add text label below barcode
        $svg .= "<text x=\"" . ($svgWidth / 2) . "\" y=\"" . ($height + 18) . "\" font-family=\"monospace\" font-size=\"13\" font-weight=\"bold\" fill=\"#0f172a\" text-anchor=\"middle\" letter-spacing=\"2\">" . htmlspecialchars($code) . "</text>";
        $svg .= "</svg>";

        return $svg;
    }
}
