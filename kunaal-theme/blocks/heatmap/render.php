<?php
/**
 * Heatmap Block - Frontend Render
 *
 * @var array $attributes Block attributes
 * @var string $content Block content
 * @var WP_Block $block Block instance
 */

if (!defined('ABSPATH')) {
    exit;
}

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$row_labels = $attributes['rowLabels'] ?? [];
$column_labels = $attributes['columnLabels'] ?? [];
$data = $attributes['data'] ?? [];
$color_scale = $attributes['colorScale'] ?? 'theme';
$custom_color_low = $attributes['customColorLow'] ?? '#F5F0EB';
$custom_color_high = $attributes['customColorHigh'] ?? '#7D6B5D';
$custom_color_mid = $attributes['customColorMid'] ?? '';
$show_values = $attributes['showValues'] ?? false;
$value_format = $attributes['valueFormat'] ?? 'number';
$show_legend = $attributes['showLegend'] ?? true;
$legend_position = $attributes['legendPosition'] ?? 'bottom';
$cell_size = $attributes['cellSize'] ?? 'auto';
$rotate_column_labels = $attributes['rotateColumnLabels'] ?? false;
$source_note = $attributes['sourceNote'] ?? '';

// Calculate min/max for normalization
$all_values = [];
foreach ($data as $row) {
    if (is_array($row)) {
        foreach ($row as $value) {
            if (is_numeric($value)) {
                $all_values[] = floatval($value);
            }
        }
    }
}
$min_value = !empty($all_values) ? min($all_values) : 0;
$max_value = !empty($all_values) ? max($all_values) : 100;

// Generate unique ID for this block instance
$block_id = 'heatmap-' . wp_unique_id();

// Helper functions are defined in inc/block-helpers.php
?>

<figure class="wp-block-kunaal-heatmap heatmap-<?php echo esc_attr($cell_size); ?>"
        role="img"
        aria-labelledby="<?php echo esc_attr($block_id); ?>-title"
        aria-describedby="<?php echo esc_attr($block_id); ?>-desc">
    
    <?php if ($title || $subtitle) : ?>
    <header class="heatmap-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="heatmap-title">
            <?php echo esc_html($title); ?>
        </h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="heatmap-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <div id="<?php echo esc_attr($block_id); ?>-desc" class="sr-only">
        <?php
        printf(
            esc_html__('Heatmap showing %d rows and %d columns. Values range from %s to %s.', 'kunaal-theme'),
            count($row_labels),
            count($column_labels),
            kunaal_format_heatmap_value($min_value, $value_format),
            kunaal_format_heatmap_value($max_value, $value_format)
        );
        ?>
    </div>
    
    <div class="heatmap-wrapper">
        <table class="heatmap-grid" aria-label="<?php echo esc_attr($title ?: 'Heatmap data'); ?>">
            <thead>
                <tr>
                    <th scope="col" class="heatmap-corner"></th>
                    <?php foreach ($column_labels as $col_label) : ?>
                    <th scope="col" class="heatmap-col-header <?php echo esc_attr($rotate_column_labels ? 'rotated' : ''); ?>">
                        <?php echo esc_html($col_label); ?>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($row_labels as $i => $row_label) : ?>
                <tr>
                    <th scope="row" class="heatmap-row-header"><?php echo esc_html($row_label); ?></th>
                    <?php
                    $row_data = $data[$i] ?? [];
                    foreach ($column_labels as $j => $col_label) :
                        $value = isset($row_data[$j]) && is_numeric($row_data[$j]) ? floatval($row_data[$j]) : 0;
                        $cell_color = kunaal_get_cell_color($value, $min_value, $max_value, $color_scale, $custom_color_low, $custom_color_high, $custom_color_mid);
                        $normalized = $max_value > $min_value ? (($value - $min_value) / ($max_value - $min_value)) : 0;
                    ?>
                    <td class="heatmap-cell"
                        role="gridcell"
                        tabindex="0"
                        style="--cell-value: <?php echo esc_attr($normalized); ?>; background-color: <?php echo esc_attr($cell_color); ?>;"
                        aria-label="<?php printf(esc_attr__('%s, %s: %s', 'kunaal-theme'), esc_attr($row_label), esc_attr($col_label), kunaal_format_heatmap_value($value, $value_format)); ?>">
                        <?php if ($show_values) : ?>
                        <span class="heatmap-cell-value"><?php echo esc_html(kunaal_format_heatmap_value($value, $value_format)); ?></span>
                        <?php endif; ?>
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($show_legend) : ?>
    <footer class="heatmap-footer">
        <div class="heatmap-legend heatmap-legend--<?php echo esc_attr($legend_position); ?>">
            <span class="legend-min"><?php echo esc_html(kunaal_format_heatmap_value($min_value, $value_format)); ?></span>
            <div class="legend-gradient"
                 style="background: linear-gradient(to right,
                    <?php echo esc_attr(kunaal_get_cell_color($min_value, $min_value, $max_value, $color_scale, $custom_color_low, $custom_color_high, $custom_color_mid)); ?>,
                    <?php echo esc_attr(kunaal_get_cell_color($max_value, $min_value, $max_value, $color_scale, $custom_color_low, $custom_color_high, $custom_color_mid)); ?>);">
            </div>
            <span class="legend-max"><?php echo esc_html(kunaal_format_heatmap_value($max_value, $value_format)); ?></span>
        </div>
        <?php if ($source_note) : ?>
        <p class="heatmap-source"><?php echo esc_html($source_note); ?></p>
        <?php endif; ?>
    </footer>
    <?php endif; ?>
    
    <details class="heatmap-data-table">
        <summary><?php esc_html_e('View data table', 'kunaal-theme'); ?></summary>
        <table>
            <caption><?php echo esc_html($title ?: 'Heatmap data'); ?></caption>
            <thead>
                <tr>
                    <th scope="col"></th>
                    <?php foreach ($column_labels as $col_label) : ?>
                    <th scope="col"><?php echo esc_html($col_label); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($row_labels as $i => $row_label) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($row_label); ?></th>
                    <?php
                    $row_data = $data[$i] ?? [];
                    foreach ($column_labels as $j => $col_label) :
                        $value = isset($row_data[$j]) && is_numeric($row_data[$j]) ? floatval($row_data[$j]) : 0;
                    ?>
                    <td><?php echo esc_html(kunaal_format_heatmap_value($value, $value_format)); ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </details>
</figure>

