<?php
/**
 * Dumbbell Chart Block - Frontend Render
 */
if (!defined('ABSPATH')) exit;

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$start_label = $attributes['startLabel'] ?? 'Start';
$end_label = $attributes['endLabel'] ?? 'End';
$show_gap = $attributes['showGapAnnotation'] ?? true;
$gap_prefix = $attributes['gapPrefix'] ?? '';
$gap_suffix = $attributes['gapSuffix'] ?? '';
$show_axis = $attributes['showAxis'] ?? true;
$value_format = $attributes['valueFormat'] ?? 'number';
$currency_symbol = $attributes['currencySymbol'] ?? '$';
$row_height = $attributes['rowHeight'] ?? 'normal';
$color_mode = $attributes['colorMode'] ?? 'theme';
$start_color = $attributes['startColor'] ?? '#7D6B5D';
$end_color = $attributes['endColor'] ?? '#B8A99A';
$show_legend = $attributes['showLegend'] ?? true;
$source_note = $attributes['sourceNote'] ?? '';
$data_rows = $attributes['dataRows'] ?? [];

// Calculate axis range
$all_values = [];
foreach ($data_rows as $row) {
    $all_values[] = floatval($row['startValue'] ?? 0);
    $all_values[] = floatval($row['endValue'] ?? 0);
}
$min_val = !empty($all_values) ? min($all_values) : 0;
$max_val = !empty($all_values) ? max($all_values) : 100;
$axis_min = $attributes['axisMin'] ?? $min_val;
$axis_max = $attributes['axisMax'] ?? $max_val;

function kunaal_format_dumbbell_value($value, $format, $currency = '$') {
    switch ($format) {
        case 'percent': return round($value, 1) . '%';
        case 'currency': return $currency . number_format($value);
        case 'compact':
            if ($value >= 1000000) return $currency . round($value / 1000000, 1) . 'M';
            if ($value >= 1000) return $currency . round($value / 1000, 1) . 'K';
            return $currency . round($value);
        default: return round($value);
    }
}

$block_id = 'dumbbell-' . wp_unique_id();
?>

<figure class="wp-block-kunaal-dumbbell-chart dumbbell-<?php echo esc_attr($row_height); ?>"
        role="img"
        aria-labelledby="<?php echo esc_attr($block_id); ?>-title">
    
    <?php if ($title || $subtitle) : ?>
    <header class="dumbbell-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="dumbbell-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="dumbbell-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <div class="dumbbell-chart">
        <svg class="dumbbell-visual" viewBox="0 0 800 400" preserveAspectRatio="xMidYMid meet">
            <defs>
                <linearGradient id="dumbbell-gradient-<?php echo esc_attr($block_id); ?>" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:<?php echo esc_attr($start_color); ?>;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:<?php echo esc_attr($end_color); ?>;stop-opacity:1" />
                </linearGradient>
            </defs>
            
            <?php if ($show_axis) : ?>
            <g class="dumbbell-axis">
                <line x1="140" y1="380" x2="700" y2="380" stroke="var(--muted)" stroke-width="1"/>
                <?php for ($i = 0; $i <= 4; $i++) :
                    $x = 140 + ($i / 4) * 560;
                    $val = $axis_min + ($i / 4) * ($axis_max - $axis_min);
                ?>
                <line x1="<?php echo $x; ?>" y1="375" x2="<?php echo $x; ?>" y2="380" stroke="var(--muted)" stroke-width="1"/>
                <text x="<?php echo $x; ?>" y="395" text-anchor="middle" class="axis-label">
                    <?php echo esc_html(kunaal_format_dumbbell_value($val, $value_format, $currency_symbol)); ?>
                </text>
                <?php endfor; ?>
            </g>
            <?php endif; ?>
            
            <g class="dumbbell-rows">
                <?php foreach ($data_rows as $i => $row) :
                    $category = $row['category'] ?? '';
                    $start_val = floatval($row['startValue'] ?? 0);
                    $end_val = floatval($row['endValue'] ?? 0);
                    $gap = $end_val - $start_val;
                    
                    // Normalize positions
                    $start_x = 140 + (($start_val - $axis_min) / ($axis_max - $axis_min)) * 560;
                    $end_x = 140 + (($end_val - $axis_min) / ($axis_max - $axis_min)) * 560;
                    $y = 50 + ($i * 60);
                ?>
                <g class="dumbbell-row" data-category="<?php echo esc_attr($category); ?>" tabindex="0">
                    <text class="dumbbell-label" x="10" y="<?php echo $y + 5; ?>"><?php echo esc_html($category); ?></text>
                    <circle class="dumbbell-dot dumbbell-dot-start" cx="<?php echo $start_x; ?>" cy="<?php echo $y; ?>" r="8" fill="<?php echo esc_attr($start_color); ?>"/>
                    <line class="dumbbell-connector"
                          x1="<?php echo $start_x; ?>" y1="<?php echo $y; ?>"
                          x2="<?php echo $end_x; ?>" y2="<?php echo $y; ?>"
                          stroke="url(#dumbbell-gradient-<?php echo esc_attr($block_id); ?>)"
                          stroke-width="4"/>
                    <circle class="dumbbell-dot dumbbell-dot-end" cx="<?php echo $end_x; ?>" cy="<?php echo $y; ?>" r="8" fill="<?php echo esc_attr($end_color); ?>"/>
                    <text class="dumbbell-value dumbbell-value-start" x="<?php echo $start_x; ?>" y="<?php echo $y + 20; ?>" text-anchor="middle">
                        <?php echo esc_html(kunaal_format_dumbbell_value($start_val, $value_format, $currency_symbol)); ?>
                    </text>
                    <text class="dumbbell-value dumbbell-value-end" x="<?php echo $end_x; ?>" y="<?php echo $y + 20; ?>" text-anchor="middle">
                        <?php echo esc_html(kunaal_format_dumbbell_value($end_val, $value_format, $currency_symbol)); ?>
                    </text>
                    <?php if ($show_gap) : ?>
                    <text class="dumbbell-gap" x="720" y="<?php echo $y + 5; ?>" text-anchor="end">
                        <?php echo esc_html($gap_prefix . kunaal_format_dumbbell_value($gap, $value_format, $currency_symbol) . $gap_suffix); ?>
                    </text>
                    <?php endif; ?>
                </g>
                <?php endforeach; ?>
            </g>
        </svg>
    </div>
    
    <?php if ($show_legend) : ?>
    <footer class="dumbbell-footer">
        <div class="dumbbell-legend">
            <span class="legend-item">
                <span class="legend-dot legend-dot-start" style="background: <?php echo esc_attr($start_color); ?>"></span>
                <?php echo esc_html($start_label); ?>
            </span>
            <span class="legend-item">
                <span class="legend-dot legend-dot-end" style="background: <?php echo esc_attr($end_color); ?>"></span>
                <?php echo esc_html($end_label); ?>
            </span>
        </div>
        <?php if ($source_note) : ?>
        <p class="dumbbell-source"><?php echo esc_html($source_note); ?></p>
        <?php endif; ?>
    </footer>
    <?php endif; ?>
    
    <details class="dumbbell-data-table">
        <summary><?php esc_html_e('View data table', 'kunaal-theme'); ?></summary>
        <table>
            <thead>
                <tr><th><?php esc_html_e('Category', 'kunaal-theme'); ?></th><th><?php echo esc_html($start_label); ?></th><th><?php echo esc_html($end_label); ?></th><th><?php esc_html_e('Gap', 'kunaal-theme'); ?></th></tr>
            </thead>
            <tbody>
                <?php foreach ($data_rows as $row) :
                    $gap = floatval($row['endValue'] ?? 0) - floatval($row['startValue'] ?? 0);
                ?>
                <tr>
                    <td><?php echo esc_html($row['category'] ?? ''); ?></td>
                    <td><?php echo esc_html(kunaal_format_dumbbell_value($row['startValue'] ?? 0, $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(kunaal_format_dumbbell_value($row['endValue'] ?? 0, $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html($gap_prefix . kunaal_format_dumbbell_value($gap, $value_format, $currency_symbol) . $gap_suffix); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </details>
</figure>

