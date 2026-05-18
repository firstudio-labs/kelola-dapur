<?php

if (!function_exists('formatIndonesianNumber')) {
    /**
     * Format numbers to Indonesian standard:
     * - Thousands separator: dot (.)
     * - Decimal separator: comma (,)
     * - Remove trailing zeroes after decimals (e.g. 10.00 -> 10, 10.50 -> 10,5)
     */
    function formatIndonesianNumber($value) {
        if ($value === null || $value === '') return '0';
        if (!is_numeric($value)) return $value;

        $value = (float) $value;
        
        // Use sprintf to format with a high decimal precision without scientific notation
        $numStr = sprintf('%.4f', $value);
        $parts = explode('.', $numStr);
        $integerPart = number_format((float)$parts[0], 0, '', '.');

        if (isset($parts[1])) {
            $decimalPart = rtrim($parts[1], '0');
            if ($decimalPart !== '') {
                return $integerPart . ',' . $decimalPart;
            }
        }

        return $integerPart;
    }
}

if (!function_exists('parseIndonesianNumber')) {
    /**
     * Parse Indonesian formatted number string back to float:
     * - Removes dot (.) as thousands separator
     * - Replaces comma (,) with dot (.) as decimal separator
     */
    function parseIndonesianNumber($value) {
        if ($value === null || $value === '') return 0;
        if (is_numeric($value)) return (float) $value;
        
        $cleaned = str_replace('.', '', (string)$value);
        $cleaned = str_replace(',', '.', $cleaned);
        
        return (float) $cleaned;
    }
}
