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
    <?php if ($title || $subtitle) : ?>
    <header class="slopegraph-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="slopegraph-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="slopegraph-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <div class="slopegraph-chart">
        <div class="slopegraph-columns">
            <span class="slopegraph-column-label slopegraph-column-left"><?php echo esc_html($left_label); ?></span>
            <span class="slopegraph-column-label slopegraph-column-right"><?php echo esc_html($right_label); ?></span>
        </div>
        
        <svg class="slopegraph-lines" viewBox="0 0 600 400" preserveAspectRatio="xMidYMid meet">
            <?php foreach ($data_rows as $i => $row) :
                $label = $row['label'] ?? '';
                $left_val = floatval($row['leftValue'] ?? 0);
                $right_val = floatval($row['rightValue'] ?? 0);
                $change = $right_val - $left_val;
                $pct_change = $left_val != 0 ? ($change / abs($left_val)) * 100 : 0;
                $is_positive = $change > 0.5;
                $is_negative = $change < -0.5;
                $color = $is_positive ? '#7D6B5D' : ($is_negative ? '#C9553D' : '#666');
                $y = 50 + ($i * 60);
            ?>
            <g class="slopegraph-row" data-change="<?php echo $is_positive ? 'positive' : ($is_negative ? 'negative' : 'neutral'); ?>" tabindex="0">
                <circle class="dot dot-left" cx="120" cy="<?php echo $y; ?>" r="5" fill="<?php echo esc_attr($color); ?>"/>
                <line class="slope-line" x1="120" y1="<?php echo $y; ?>" x2="480" y2="<?php echo $y + ($is_positive ? -10 : ($is_negative ? 10 : 0)); ?>"
                      stroke="<?php echo esc_attr($color); ?>" stroke-width="2"/>
                <circle class="dot dot-right" cx="480" cy="<?php echo $y + ($is_positive ? -10 : ($is_negative ? 10 : 0)); ?>" r="5" fill="<?php echo esc_attr($color); ?>"/>
                <text class="label label-left" x="10" y="<?php echo $y + 5; ?>"><?php echo esc_html($label); ?></text>
                <text class="value value-left" x="100" y="<?php echo $y + 5; ?>" text-anchor="end">
                    <?php echo esc_html(kunaal_format_slope_value($left_val, $value_format, $currency_symbol)); ?>
                </text>
                <text class="label label-right" x="490" y="<?php echo $y + ($is_positive ? -5 : ($is_negative ? 15 : 5)); ?>"><?php echo esc_html($label); ?></text>
                <text class="value value-right" x="570" y="<?php echo $y + ($is_positive ? -5 : ($is_negative ? 15 : 5)); ?>" text-anchor="end">
                    <?php echo esc_html(kunaal_format_slope_value($right_val, $value_format, $currency_symbol)); ?>
                    <?php if ($show_pct) : ?>
                    <tspan class="pct-change" fill="<?php echo esc_attr($color); ?>">
                        (<?php echo $change >= 0 ? '+' : ''; ?><?php echo round($pct_change, 1); ?>%)
                    </tspan>
                    <?php endif; ?>
                </text>
            </g>
            <?php endforeach; ?>
        </svg>
    </div>
    
    <?php if ($source_note) : ?>
    <footer class="slopegraph-footer">
        <p class="slopegraph-source"><?php echo esc_html($source_note); ?></p>
    </footer>
    <?php endif; ?>
    
    <details class="slopegraph-data-table">
        <summary><?php esc_html_e('View data table', 'kunaal-theme'); ?></summary>
        <table>
            <thead>
                <tr><th><?php esc_html_e('Label', 'kunaal-theme'); ?></th><th><?php echo esc_html($left_label); ?></th><th><?php echo esc_html($right_label); ?></th><th><?php esc_html_e('Change', 'kunaal-theme'); ?></th></tr>
            </thead>
            <tbody>
                <?php foreach ($data_rows as $row) :
                    $change = floatval($row['rightValue'] ?? 0) - floatval($row['leftValue'] ?? 0);
                ?>
                <tr>
                    <td><?php echo esc_html($row['label'] ?? ''); ?></td>
                    <td><?php echo esc_html(kunaal_format_slope_value($row['leftValue'] ?? 0, $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(kunaal_format_slope_value($row['rightValue'] ?? 0, $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(($change >= 0 ? '+' : '') . kunaal_format_slope_value($change, $value_format, $currency_symbol)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </details>
</figure>

