<?php
/**
 * Slopegraph Block - Frontend Render
 */
if (!defined('ABSPATH')) {
    exit;
}

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$left_label = $attributes['leftColumnLabel'] ?? 'Before';
$right_label = $attributes['rightColumnLabel'] ?? 'After';
$row_height = $attributes['rowHeight'] ?? 'normal';
$show_pct = $attributes['showPercentChange'] ?? true;
$show_arrows = $attributes['showDirectionArrows'] ?? false;
$value_format = $attributes['valueFormat'] ?? 'number';
$currency_symbol = $attributes['currencySymbol'] ?? '$';
$source_note = $attributes['sourceNote'] ?? '';
$data_rows = $attributes['dataRows'] ?? [];

// Helper functions are defined in inc/block-helpers.php

$block_id = 'slope-' . wp_unique_id();
?>

<figure class="wp-block-kunaal-slopegraph slope-<?php echo esc_attr($row_height); ?>" role="img" aria-labelledby="<?php echo esc_attr($block_id); ?>-title">
    <?php if ($title || $subtitle) { ?>
    <header class="slopegraph-header">
        <?php if ($title) { ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="slopegraph-title"><?php echo esc_html($title); ?></h3>
        <?php } ?>
        <?php if ($subtitle) { ?>
        <p class="slopegraph-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php } ?>
    </header>
    <?php } ?>
    
    <div class="slopegraph-chart">
        <div class="slopegraph-columns">
            <span class="slopegraph-column-label slopegraph-column-left"><?php echo esc_html($left_label); ?></span>
            <span class="slopegraph-column-label slopegraph-column-right"><?php echo esc_html($right_label); ?></span>
        </div>
        
        <svg class="slopegraph-lines" viewBox="0 0 600 400" preserveAspectRatio="xMidYMid meet">
            <?php
            foreach ($data_rows as $i => $row) {
                $label = $row['label'] ?? '';
                $left_val = floatval($row['leftValue'] ?? 0);
                $right_val = floatval($row['rightValue'] ?? 0);
                $change = $right_val - $left_val;
                $pct_change = $left_val != 0 ? ($change / abs($left_val)) * 100 : 0;
                $is_positive = $change > 0.5;
                $is_negative = $change < -0.5;
                
                // Determine color based on change direction
                if ($is_positive) {
                    $color = '#7D6B5D';
                } elseif ($is_negative) {
                    $color = '#C9553D';
                } else {
                    $color = '#666';
                }
                
                // Determine change type for data attribute
                if ($is_positive) {
                    $change_type = 'positive';
                } elseif ($is_negative) {
                    $change_type = 'negative';
                } else {
                    $change_type = 'neutral';
                }
                
                // Calculate vertical offset for right side
                if ($is_positive) {
                    $right_offset = -10;
                } elseif ($is_negative) {
                    $right_offset = 10;
                } else {
                    $right_offset = 0;
                }
                
                // Calculate text offset for right labels
                if ($is_positive) {
                    $text_offset = -5;
                } elseif ($is_negative) {
                    $text_offset = 15;
                } else {
                    $text_offset = 5;
                }
                
                $y = 50 + ($i * 60);
                ?>
            <g class="slopegraph-row" data-change="<?php echo esc_attr($change_type); ?>" tabindex="0">
                <circle class="dot dot-left" cx="120" cy="<?php echo esc_attr($y); ?>" r="5" fill="<?php echo esc_attr($color); ?>"/>
                <line class="slope-line" x1="120" y1="<?php echo esc_attr($y); ?>" x2="480" y2="<?php echo esc_attr($y + $right_offset); ?>"
                      stroke="<?php echo esc_attr($color); ?>" stroke-width="2"/>
                <circle class="dot dot-right" cx="480" cy="<?php echo esc_attr($y + $right_offset); ?>" r="5" fill="<?php echo esc_attr($color); ?>"/>
                <text class="label label-left" x="10" y="<?php echo esc_attr($y + 5); ?>"><?php echo esc_html($label); ?></text>
                <text class="value value-left" x="100" y="<?php echo esc_attr($y + 5); ?>" text-anchor="end">
                    <?php echo esc_html(kunaal_format_slope_value($left_val, $value_format, $currency_symbol)); ?>
                </text>
                <text class="label label-right" x="490" y="<?php echo esc_attr($y + $text_offset); ?>"><?php echo esc_html($label); ?></text>
                <text class="value value-right" x="570" y="<?php echo esc_attr($y + $text_offset); ?>" text-anchor="end">
                    <?php echo esc_html(kunaal_format_slope_value($right_val, $value_format, $currency_symbol)); ?>
                    <?php if ($show_pct) { ?>
                    <tspan class="pct-change" fill="<?php echo esc_attr($color); ?>">
                        (<?php echo esc_html($change >= 0 ? '+' : ''); ?><?php echo esc_html(round($pct_change, 1)); ?>%)
                    </tspan>
                    <?php } ?>
                </text>
            </g>
            <?php
            }
            ?>
        </svg>
    </div>
    
    <?php if ($source_note) { ?>
    <footer class="slopegraph-footer">
        <p class="slopegraph-source"><?php echo esc_html($source_note); ?></p>
    </footer>
    <?php } ?>
    
    <details class="slopegraph-data-table">
        <summary><?php esc_html_e('View data table', 'kunaal-theme'); ?></summary>
        <table>
            <thead>
                <tr><th scope="col"><?php esc_html_e('Label', 'kunaal-theme'); ?></th><th scope="col"><?php echo esc_html($left_label); ?></th><th scope="col"><?php echo esc_html($right_label); ?></th><th scope="col"><?php esc_html_e('Change', 'kunaal-theme'); ?></th></tr>
            </thead>
            <tbody>
                <?php
                foreach ($data_rows as $row) {
                    $change = floatval($row['rightValue'] ?? 0) - floatval($row['leftValue'] ?? 0);
                    ?>
                <tr>
                    <td><?php echo esc_html($row['label'] ?? ''); ?></td>
                    <td><?php echo esc_html(kunaal_format_slope_value($row['leftValue'] ?? 0, $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(kunaal_format_slope_value($row['rightValue'] ?? 0, $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(($change >= 0 ? '+' : '') . kunaal_format_slope_value($change, $value_format, $currency_symbol)); ?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </details>
</figure>

