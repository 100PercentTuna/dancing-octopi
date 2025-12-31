<?php
/**
 * Block Registration and Render Helper Functions
 * 
 * Contains helper functions used by block render.php files.
 * All functions are loaded via require_once in functions.php, so no function_exists guards needed.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validate block.json file
 * 
 * @param string $block_path Path to block directory
 * @param string $block Block name
 * @return array|false Block data array if valid, false otherwise
 */
function kunaal_validate_block_json(string $block_path, string $block): array|false {
    $block_json = $block_path . '/block.json';
    $result = false;
    
    if (file_exists($block_json)) {
        $block_data = json_decode(file_get_contents($block_json), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($block_data)) {
            if (!empty($block_data['name']) && !empty($block_data['title'])) {
                $result = $block_data;
            } else {
                kunaal_theme_log('Block.json missing required fields', array('block' => $block));
            }
        } else {
            kunaal_theme_log('Invalid block.json', array('block' => $block, 'error' => json_last_error_msg()));
        }
    }
    
    return $result;
}

/**
 * Register a single block
 * 
 * @param string $block_path Path to block directory
 * @param string $block Block name
 * @return bool True if registered successfully, false otherwise
 */
function kunaal_register_single_block(string $block_path, string $block): bool {
    $block_data = kunaal_validate_block_json($block_path, $block);
    $result = false;
    
    if ($block_data) {
        try {
            $registered = register_block_type($block_path);
            if ($registered) {
                $result = true;
            } else {
                kunaal_theme_log('Block registration failed', array('block' => $block));
            }
        } catch (\Throwable $e) {
            kunaal_theme_log('Block registration exception', array('block' => $block, 'error' => $e->getMessage()));
        }
    }
    
    return $result;
}

// ========================================
// BLOCK RENDER HELPER FUNCTIONS
// ========================================
// All functions below are used by block render.php files.
// No function_exists guards needed - file is loaded via require_once in functions.php.

/**
 * Format compact number (M/K suffixes)
 * Used by: data-map
 * 
 * @param float $value Value to format
 * @param string $currency Currency symbol
 * @param string $suffix Optional suffix
 * @return string Formatted value
 */
function kunaal_format_compact_value(float $value, string $currency, string $suffix): string {
    if ($value >= 1000000) {
        return $currency . round($value / 1000000, 1) . 'M' . ($suffix ? ' ' . $suffix : '');
    }
    if ($value >= 1000) {
        return $currency . round($value / 1000, 1) . 'K' . ($suffix ? ' ' . $suffix : '');
    }
    return $currency . round($value) . ($suffix ? ' ' . $suffix : '');
}

/**
 * Format map value based on format type
 * Used by: data-map
 * 
 * @param float $value Value to format
 * @param string $format Format type (percent, currency, compact, decimal1, number)
 * @param string $currency Currency symbol
 * @param string $suffix Optional suffix
 * @return string Formatted value
 */
function kunaal_format_map_value(float $value, string $format, string $currency = '$', string $suffix = ''): string {
        $format_handlers = array(
            'percent' => function($v) { return round($v, 1) . '%'; },
            'currency' => function($v) use ($currency, $suffix) { return $currency . number_format($v) . ($suffix ? ' ' . $suffix : ''); },
            'compact' => function($v) use ($currency, $suffix) { return kunaal_format_compact_value($v, $currency, $suffix); },
            'decimal1' => function($v) use ($suffix) { return number_format($v, 1) . ($suffix ? ' ' . $suffix : ''); },
        );
        
        if (isset($format_handlers[$format])) {
            return $format_handlers[$format]($value);
        }
        
        return round($value) . ($suffix ? ' ' . $suffix : '');
}

/**
 * Calculate quartiles for statistical distribution
 * Used by: statistical-distribution
 * 
 * @param array $values Array of numeric values
 * @return array Array with min, q1, median, q3, max, mean, outliers
 */
function kunaal_calculate_quartiles(array $values): array {
        $sorted = $values;
        sort($sorted);
        $n = count($sorted);
        if ($n === 0) {
            return array('min' => 0, 'q1' => 0, 'median' => 0, 'q3' => 0, 'max' => 0, 'mean' => 0, 'outliers' => array());
        }
        
        $median = $n % 2 === 0
            ? ($sorted[$n/2 - 1] + $sorted[$n/2]) / 2
            : $sorted[floor($n/2)];
        
        $q1 = $n % 4 === 0
            ? ($sorted[$n/4 - 1] + $sorted[$n/4]) / 2
            : $sorted[floor($n/4)];
        
        $q3 = $n % 4 === 0
            ? ($sorted[3*$n/4 - 1] + $sorted[3*$n/4]) / 2
            : $sorted[floor(3*$n/4)];
        
        $iqr = $q3 - $q1;
        $lower_fence = $q1 - 1.5 * $iqr;
        $upper_fence = $q3 + 1.5 * $iqr;
        
        // Clamp min/max to fences (outliers are handled separately)
        $min = $sorted[0];
        $max = $sorted[$n-1];
        
        $outliers = array_filter($sorted, function($v) use ($lower_fence, $upper_fence) {
            return $v < $lower_fence || $v > $upper_fence;
        });
        
        $mean = array_sum($sorted) / $n;
        
        return array(
            'min' => $min,
            'q1' => $q1,
            'median' => $median,
            'q3' => $q3,
            'max' => $max,
            'mean' => $mean,
            'outliers' => array_values($outliers)
        );
}

/**
 * Format statistical distribution value
 * Used by: statistical-distribution
 * 
 * @param float $value Value to format
 * @param string $format Format type (currency, percent, decimal1, decimal2, number)
 * @param string $currency Currency symbol
 * @return string Formatted value
 */
function kunaal_format_stat_value(float $value, string $format, string $currency = '$'): string {
        $result = '';
        switch ($format) {
            case 'currency':
                $result = $currency . number_format($value);
                break;
            case 'percent':
                $result = round($value, 1) . '%';
                break;
            case 'decimal1':
                $result = number_format($value, 1);
                break;
            case 'decimal2':
                $result = number_format($value, 2);
                break;
            default:
                $result = round($value);
                break;
        }
        return $result;
}

/**
 * Format slopegraph value
 * Used by: slopegraph
 * 
 * @param float $value Value to format
 * @param string $format Format type (percent, currency, decimal1, decimal2, number)
 * @param string $currency Currency symbol
 * @return string Formatted value
 */
function kunaal_format_slope_value(float $value, string $format, string $currency = '$'): string {
    $result = '';
    switch ($format) {
        case 'percent':
            $result = round($value, 1) . '%';
            break;
        case 'currency':
            $result = $currency . number_format($value);
            break;
        case 'decimal1':
            $result = number_format($value, 1);
            break;
        case 'decimal2':
            $result = number_format($value, 2);
            break;
        default:
            $result = round($value);
            break;
    }
    return $result;
}

/**
 * Format flow diagram value
 * Used by: flow-diagram
 * 
 * @param float $value Value to format
 * @param string $format Format type (percent, currency, compact, number)
 * @param string $currency Currency symbol
 * @param string $unit Optional unit
 * @return string Formatted value
 */
function kunaal_format_flow_value(float $value, string $format, string $currency = '$', string $unit = ''): string {
        $result = '';
        $unit_suffix = $unit ? ' ' . $unit : '';
        switch ($format) {
            case 'percent':
                $result = round($value, 1) . '%';
                break;
            case 'currency':
                $result = $currency . number_format($value) . $unit_suffix;
                break;
            case 'compact':
                if ($value >= 1000000) {
                    $result = $currency . round($value / 1000000, 1) . 'M' . $unit_suffix;
                } elseif ($value >= 1000) {
                    $result = $currency . round($value / 1000, 1) . 'K' . $unit_suffix;
                } else {
                    $result = $currency . round($value) . $unit_suffix;
                }
                break;
            default:
                $result = round($value) . $unit_suffix;
                break;
        }
        return $result;
}

/**
 * Format dumbbell chart value
 * Used by: dumbbell-chart
 * 
 * @param float $value Value to format
 * @param string $format Format type (percent, currency, compact, number)
 * @param string $currency Currency symbol
 * @return string Formatted value
 */
function kunaal_format_dumbbell_value(float $value, string $format, string $currency = '$'): string {
    $result = '';
    switch ($format) {
        case 'percent':
            $result = round($value, 1) . '%';
            break;
        case 'currency':
            $result = $currency . number_format($value);
            break;
        case 'compact':
            if ($value >= 1000000) {
                $result = $currency . round($value / 1000000, 1) . 'M';
            } elseif ($value >= 1000) {
                $result = $currency . round($value / 1000, 1) . 'K';
            } else {
                $result = $currency . round($value);
            }
            break;
        default:
            $result = round($value);
            break;
    }
    return $result;
}

/**
 * Interpolate color between two hex colors
 * Used by: heatmap
 * 
 * @param string $color1 First color (hex)
 * @param string $color2 Second color (hex)
 * @param float $t Interpolation factor (0-1)
 * @return string RGB color string
 */
function kunaal_interpolate_color(string $color1, string $color2, float $t): string {
    $c1 = kunaal_hex_to_rgb($color1);
    $c2 = kunaal_hex_to_rgb($color2);
    $r = round($c1['r'] + ($c2['r'] - $c1['r']) * $t);
    $g = round($c1['g'] + ($c2['g'] - $c1['g']) * $t);
    $b = round($c1['b'] + ($c2['b'] - $c1['b']) * $t);
    return "rgb($r, $g, $b)";
}

/**
 * Convert hex color to RGB array
 * Used by: heatmap
 * 
 * @param string $hex Hex color string
 * @return array Array with r, g, b keys
 */
function kunaal_hex_to_rgb(string $hex): array {
    $hex = ltrim($hex, '#');
    return array(
        'r' => hexdec(substr($hex, 0, 2)),
        'g' => hexdec(substr($hex, 2, 2)),
        'b' => hexdec(substr($hex, 4, 2))
    );
}

/**
 * Get theme color for normalized value
 * Used by: heatmap
 * 
 * @param float $normalized Normalized value (0-1)
 * @return string Hex color
 */
function kunaal_get_theme_color(float $normalized): string {
    $colors = array(
        '#F5F0EB', '#E8DFD5', '#D4C4B5', '#B8A99A',
        '#8B7355', '#7D6B5D', '#5C4A3D'
    );
    $index = min(floor($normalized * (count($colors) - 1)), count($colors) - 1);
    return $colors[$index];
}

/**
 * Format heatmap value
 * Used by: heatmap
 * 
 * @param float $value Value to format
 * @param string $format Format type (percent, decimal1, decimal2, number)
 * @return string Formatted value
 */
function kunaal_format_heatmap_value(float $value, string $format): string {
    $result = '';
    switch ($format) {
        case 'percent':
            $result = round($value, 1) . '%';
            break;
        case 'decimal1':
            $result = number_format($value, 1);
            break;
        case 'decimal2':
            $result = number_format($value, 2);
            break;
        default:
            $result = round($value);
            break;
    }
    return $result;
}

/**
 * Get cell background color for heatmap
 * Used by: heatmap
 * 
 * @param float $value Cell value
 * @param float $min Minimum value
 * @param float $max Maximum value
 * @param string $scale Color scale (theme, diverging, custom)
 * @param string $low Low color (for custom)
 * @param string $high High color (for custom)
 * @param string $mid Mid color (for diverging)
 * @return string RGB color string
 */
/**
 * Get normalized value for color calculation
 * 
 * @param float $value Cell value
 * @param float $min Minimum value
 * @param float $max Maximum value
 * @return float Normalized value (0-1)
 */
function kunaal_normalize_value(float $value, float $min, float $max): float {
    if ($max <= $min) {
        return 0.5;
    }
    return ($value - $min) / ($max - $min);
}

/**
 * Get diverging color scale
 * 
 * @param float $normalized Normalized value (0-1)
 * @param string $low Low color
 * @param string $mid Mid color
 * @param string $high High color
 * @return string RGB color string
 */
function kunaal_get_diverging_color(float $normalized, string $low, string $mid, string $high): string {
    if ($normalized < 0.5) {
        return kunaal_interpolate_color($low, $mid, $normalized * 2);
    }
    return kunaal_interpolate_color($mid, $high, ($normalized - 0.5) * 2);
}

function kunaal_get_cell_color(float $value, float $min, float $max, string $scale, string $low, string $high, string $mid = ''): string {
    $normalized = kunaal_normalize_value($value, $min, $max);
    
    if ($scale === 'custom') {
        return kunaal_interpolate_color($low, $high, $normalized);
    }
    if ($scale === 'diverging' && $mid) {
        return kunaal_get_diverging_color($normalized, $low, $mid, $high);
    }
    return kunaal_get_theme_color($normalized);
}

/**
 * Parse data string into array
 * Used by: chart
 * 
 * @param string $str Comma-separated values
 * @return array Array of floats
 */
function kunaal_parse_data(string $str): array {
    if (!$str) {
        return array();
    }
    return array_map('floatval', array_map('trim', explode(',', $str)));
}

/**
 * Format chart value with unit
 * Used by: chart
 * 
 * @param float $val Value to format
 * @param string $unit Unit string
 * @param string $unit_position Unit position (prefix or suffix)
 * @return string Formatted value
 */
function kunaal_format_chart_value(float $val, string $unit, string $unit_position): string {
    $formatted = number_format($val, ($val == floor($val)) ? 0 : 1);
    if ($unit) {
        return $unit_position === 'prefix' ? $unit . $formatted : $formatted . $unit;
    }
    return $formatted;
}
