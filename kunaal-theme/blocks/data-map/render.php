<?php
/**
 * Data Map Block - Frontend Render
 */
if (!defined('ABSPATH')) exit;

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$map_type = $attributes['mapType'] ?? 'choropleth';
$base_map = $attributes['baseMap'] ?? 'world';
$custom_geojson = $attributes['customGeoJSON'] ?? '';
$center_lat = floatval($attributes['centerLat'] ?? 25);
$center_lng = floatval($attributes['centerLng'] ?? 0);
$initial_zoom = intval($attributes['initialZoom'] ?? 2);
$enable_zoom = $attributes['enableZoom'] ?? true;
$enable_pan = $attributes['enablePan'] ?? true;
$show_labels = $attributes['showLabels'] ?? false;
$color_scale = $attributes['colorScale'] ?? 'sequential';
$color_low = $attributes['colorLow'] ?? '#F5F0EB';
$color_high = $attributes['colorHigh'] ?? '#7D6B5D';
$color_mid = $attributes['colorMid'] ?? '#F5F0EB';
$color_negative = $attributes['colorNegative'] ?? '#C9553D';
$dot_size_min = intval($attributes['dotSizeMin'] ?? 4);
$dot_size_max = intval($attributes['dotSizeMax'] ?? 40);
$dot_opacity = floatval($attributes['dotOpacity'] ?? 0.7);
$dot_border_color = $attributes['dotBorderColor'] ?? '#FFFFFF';
$value_label = $attributes['valueLabel'] ?? 'Value';
$value_format = $attributes['valueFormat'] ?? 'number';
$currency_symbol = $attributes['currencySymbol'] ?? '$';
$value_suffix = $attributes['valueSuffix'] ?? '';
$show_legend = $attributes['showLegend'] ?? true;
$legend_position = $attributes['legendPosition'] ?? 'bottom-right';
$legend_title = $attributes['legendTitle'] ?? '';
$height = intval($attributes['height'] ?? 500);
$source_note = $attributes['sourceNote'] ?? '';
$region_data = $attributes['regionData'] ?? [];
$point_data = $attributes['pointData'] ?? [];

function kunaal_format_map_value($value, $format, $currency = '$', $suffix = '') {
    switch ($format) {
        case 'percent': return round($value, 1) . '%';
        case 'currency': return $currency . number_format($value) . ($suffix ? ' ' . $suffix : '');
        case 'compact':
            if ($value >= 1000000) return $currency . round($value / 1000000, 1) . 'M' . ($suffix ? ' ' . $suffix : '');
            if ($value >= 1000) return $currency . round($value / 1000, 1) . 'K' . ($suffix ? ' ' . $suffix : '');
            return $currency . round($value) . ($suffix ? ' ' . $suffix : '');
        case 'decimal1': return number_format($value, 1) . ($suffix ? ' ' . $suffix : '');
        default: return round($value) . ($suffix ? ' ' . $suffix : '');
    }
}

$block_id = 'data-map-' . wp_unique_id();

// Calculate min/max for legend
$all_values = [];
foreach ($region_data as $region) {
    if (isset($region['value']) && is_numeric($region['value'])) {
        $all_values[] = floatval($region['value']);
    }
}
foreach ($point_data as $point) {
    if (isset($point['value']) && is_numeric($point['value'])) {
        $all_values[] = floatval($point['value']);
    }
}
$min_value = !empty($all_values) ? min($all_values) : 0;
$max_value = !empty($all_values) ? max($all_values) : 100;
?>

<figure class="wp-block-kunaal-data-map map--<?php echo esc_attr($map_type); ?>" 
        role="img" 
        aria-labelledby="<?php echo esc_attr($block_id); ?>-title"
        data-lazy-block="data-map"
        data-map-type="<?php echo esc_attr($map_type); ?>"
        data-base-map="<?php echo esc_attr($base_map); ?>"
        data-custom-geojson="<?php echo esc_attr($custom_geojson); ?>"
        data-center-lat="<?php echo esc_attr($center_lat); ?>"
        data-center-lng="<?php echo esc_attr($center_lng); ?>"
        data-initial-zoom="<?php echo esc_attr($initial_zoom); ?>"
        data-enable-zoom="<?php echo $enable_zoom ? 'true' : 'false'; ?>"
        data-enable-pan="<?php echo $enable_pan ? 'true' : 'false'; ?>"
        data-color-scale="<?php echo esc_attr($color_scale); ?>"
        data-color-low="<?php echo esc_attr($color_low); ?>"
        data-color-high="<?php echo esc_attr($color_high); ?>"
        data-color-mid="<?php echo esc_attr($color_mid); ?>"
        data-color-negative="<?php echo esc_attr($color_negative); ?>"
        data-dot-size-min="<?php echo esc_attr($dot_size_min); ?>"
        data-dot-size-max="<?php echo esc_attr($dot_size_max); ?>"
        data-dot-opacity="<?php echo esc_attr($dot_opacity); ?>"
        data-dot-border-color="<?php echo esc_attr($dot_border_color); ?>"
        data-region-data='<?php echo esc_attr(wp_json_encode($region_data)); ?>'
        data-point-data='<?php echo esc_attr(wp_json_encode($point_data)); ?>'
        data-value-format="<?php echo esc_attr($value_format); ?>"
        data-currency-symbol="<?php echo esc_attr($currency_symbol); ?>"
        data-value-suffix="<?php echo esc_attr($value_suffix); ?>">
    
    <?php if ($title || $subtitle) : ?>
    <header class="map-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="map-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="map-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <div class="map-container" style="height: <?php echo esc_attr($height); ?>px;">
        <div id="<?php echo esc_attr($block_id); ?>" 
             class="data-map-visual"
             role="application"
             tabindex="0"
             aria-label="<?php echo esc_attr($title ?: 'Interactive map'); ?>">
            <!-- Map will be rendered by JavaScript -->
            <div class="map-loading">Loading map...</div>
        </div>
        
        <?php if ($show_legend) : ?>
        <div class="map-legend map-legend--<?php echo esc_attr($legend_position); ?>">
            <?php if ($legend_title) : ?>
            <h4 class="legend-title"><?php echo esc_html($legend_title); ?></h4>
            <?php endif; ?>
            <div class="legend-content">
                <span class="legend-min"><?php echo esc_html(kunaal_format_map_value($min_value, $value_format, $currency_symbol, $value_suffix)); ?></span>
                <div class="legend-gradient" 
                     style="background: linear-gradient(to right, <?php echo esc_attr($color_low); ?>, <?php echo esc_attr($color_high); ?>);">
                </div>
                <span class="legend-max"><?php echo esc_html(kunaal_format_map_value($max_value, $value_format, $currency_symbol, $value_suffix)); ?></span>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="map-tooltip" role="tooltip" hidden>
            <h4 class="tooltip-region"></h4>
            <p class="tooltip-value"></p>
        </div>
    </div>
    
    <?php if ($source_note) : ?>
    <footer class="map-footer">
        <p class="map-source"><?php echo esc_html($source_note); ?></p>
    </footer>
    <?php endif; ?>
    
    <details class="map-data-table">
        <summary><?php esc_html_e('View data table', 'kunaal-theme'); ?></summary>
        <table>
            <caption><?php echo esc_html($title ?: 'Map data'); ?></caption>
            <?php if ($map_type === 'choropleth' || $map_type === 'combined') : ?>
            <thead>
                <tr><th><?php esc_html_e('Region', 'kunaal-theme'); ?></th><th><?php echo esc_html($value_label); ?></th></tr>
            </thead>
            <tbody>
                <?php foreach ($region_data as $region) : ?>
                <tr>
                    <td><?php echo esc_html($region['label'] ?? $region['code'] ?? ''); ?></td>
                    <td><?php echo esc_html(kunaal_format_map_value($region['value'] ?? 0, $value_format, $currency_symbol, $value_suffix)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <?php endif; ?>
            <?php if ($map_type === 'dots' || $map_type === 'gradient-dots' || $map_type === 'combined') : ?>
            <thead>
                <tr><th><?php esc_html_e('Location', 'kunaal-theme'); ?></th><th><?php echo esc_html($value_label); ?></th></tr>
            </thead>
            <tbody>
                <?php foreach ($point_data as $point) : ?>
                <tr>
                    <td><?php echo esc_html($point['label'] ?? $point['lat'] . ', ' . $point['lng']); ?></td>
                    <td><?php echo esc_html(kunaal_format_map_value($point['value'] ?? 0, $value_format, $currency_symbol, $value_suffix)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <?php endif; ?>
        </table>
    </details>
</figure>

