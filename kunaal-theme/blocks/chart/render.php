<?php
/**
 * Chart Block - Render
 */
$chart_type = $attributes['chartType'] ?? 'bar';
$orientation = $attributes['orientation'] ?? 'vertical';
$title = $attributes['title'] ?? '';
$data_str = $attributes['data'] ?? '';
$labels_str = $attributes['labels'] ?? '';
$source = $attributes['source'] ?? '';
$caption = $attributes['caption'] ?? '';
$show_legend = $attributes['showLegend'] ?? true;
$colors = $attributes['colors'] ?? 'theme';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Parse data
$data = array();
if ($data_str) {
    $data = array_map('floatval', array_map('trim', explode(',', $data_str)));
}

// Parse labels
$labels = array();
if ($labels_str) {
    $labels = array_map('trim', explode(',', $labels_str));
}

// Ensure labels match data length
while (count($labels) < count($data)) {
    $labels[] = 'Item ' . (count($labels) + 1);
}

if (empty($data)) return;

// Generate unique ID for this chart
$chart_id = 'chart-' . wp_unique_id();

// Color schemes
$color_schemes = array(
    'theme' => array('#1E5AFF', '#5B7BA8', '#7D6B5D', '#d4c4b5', '#16a34a', '#eab308', '#dc2626'),
    'blue' => array('#1E5AFF', '#3B82F6', '#60A5FA', '#93C5FD', '#DBEAFE'),
    'warm' => array('#7D6B5D', '#d4c4b5', '#F5F3EF', '#F9F7F4', '#FDFCFA'),
    'green' => array('#16a34a', '#22c55e', '#4ade80', '#86efac', '#bbf7d0')
);

$chart_colors = $color_schemes[$colors] ?? $color_schemes['theme'];
?>
<figure<?php echo $anchor; ?> class="wp-block-kunaal-chart chart chart-<?php echo esc_attr($chart_type) . ' chart-' . esc_attr($orientation) . $class_name; ?>" data-chart-id="<?php echo esc_attr($chart_id); ?>">
    <?php if ($title) : ?>
        <h3 class="chart-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    
    <div class="chart-container">
        <svg class="chart-svg" viewBox="0 0 800 400" preserveAspectRatio="xMidYMid meet">
            <?php if ($chart_type === 'bar') : ?>
                <?php
                $max_value = max($data);
                $bar_width = 800 / (count($data) * 1.5);
                $bar_spacing = $bar_width * 0.3;
                $chart_height = 350;
                $chart_width = 750;
                $start_x = 50;
                $start_y = 50;
                
                if ($orientation === 'horizontal') {
                    // Horizontal bars
                    foreach ($data as $index => $value) {
                        $bar_height = ($value / $max_value) * $chart_width;
                        $y = $start_y + ($index * ($bar_width + $bar_spacing));
                        $color = $chart_colors[$index % count($chart_colors)];
                        
                        echo '<rect x="' . $start_x . '" y="' . $y . '" width="' . $bar_height . '" height="' . $bar_width . '" fill="' . $color . '" />';
                        echo '<text x="' . ($start_x + $bar_height + 10) . '" y="' . ($y + $bar_width / 2) . '" dominant-baseline="middle" font-size="14" fill="#333">' . esc_html($value) . '</text>';
                        echo '<text x="' . ($start_x - 5) . '" y="' . ($y + $bar_width / 2) . '" dominant-baseline="middle" text-anchor="end" font-size="12" fill="#666">' . esc_html($labels[$index]) . '</text>';
                    }
                } else {
                    // Vertical bars
                    foreach ($data as $index => $value) {
                        $bar_height = ($value / $max_value) * $chart_height;
                        $x = $start_x + ($index * ($bar_width + $bar_spacing));
                        $y = $start_y + $chart_height - $bar_height;
                        $color = $chart_colors[$index % count($chart_colors)];
                        
                        echo '<rect x="' . $x . '" y="' . $y . '" width="' . $bar_width . '" height="' . $bar_height . '" fill="' . $color . '" />';
                        echo '<text x="' . ($x + $bar_width / 2) . '" y="' . ($y - 5) . '" text-anchor="middle" font-size="12" fill="#333">' . esc_html($value) . '</text>';
                        echo '<text x="' . ($x + $bar_width / 2) . '" y="' . ($start_y + $chart_height + 20) . '" text-anchor="middle" font-size="11" fill="#666">' . esc_html($labels[$index]) . '</text>';
                    }
                }
                ?>
            <?php elseif ($chart_type === 'line') : ?>
                <?php
                $max_value = max($data);
                $chart_height = 350;
                $chart_width = 700;
                $start_x = 80;
                $start_y = 50;
                $points = array();
                
                foreach ($data as $index => $value) {
                    $x = $start_x + (($index / (count($data) - 1)) * $chart_width);
                    $y = $start_y + $chart_height - (($value / $max_value) * $chart_height);
                    $points[] = $x . ',' . $y;
                }
                
                $path_d = 'M ' . implode(' L ', $points);
                echo '<path d="' . $path_d . '" fill="none" stroke="' . $chart_colors[0] . '" stroke-width="3" />';
                
                foreach ($data as $index => $value) {
                    $x = $start_x + (($index / (count($data) - 1)) * $chart_width);
                    $y = $start_y + $chart_height - (($value / $max_value) * $chart_height);
                    echo '<circle cx="' . $x . '" cy="' . $y . '" r="5" fill="' . $chart_colors[0] . '" />';
                    echo '<text x="' . $x . '" y="' . ($y - 10) . '" text-anchor="middle" font-size="11" fill="#333">' . esc_html($value) . '</text>';
                    echo '<text x="' . $x . '" y="' . ($start_y + $chart_height + 20) . '" text-anchor="middle" font-size="11" fill="#666">' . esc_html($labels[$index]) . '</text>';
                }
                ?>
            <?php elseif ($chart_type === 'pie') : ?>
                <?php
                $total = array_sum($data);
                $center_x = 400;
                $center_y = 200;
                $radius = 150;
                $current_angle = -90; // Start at top
                
                foreach ($data as $index => $value) {
                    $percentage = ($value / $total) * 100;
                    $angle = ($value / $total) * 360;
                    $end_angle = $current_angle + $angle;
                    
                    $x1 = $center_x + ($radius * cos(deg2rad($current_angle)));
                    $y1 = $center_y + ($radius * sin(deg2rad($current_angle)));
                    $x2 = $center_x + ($radius * cos(deg2rad($end_angle)));
                    $y2 = $center_y + ($radius * sin(deg2rad($end_angle)));
                    
                    $large_arc = $angle > 180 ? 1 : 0;
                    
                    $color = $chart_colors[$index % count($chart_colors)];
                    
                    echo '<path d="M ' . $center_x . ',' . $center_y . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ',' . $radius . ' 0 ' . $large_arc . ',1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $color . '" />';
                    
                    // Label
                    $label_angle = $current_angle + ($angle / 2);
                    $label_radius = $radius * 0.7;
                    $label_x = $center_x + ($label_radius * cos(deg2rad($label_angle)));
                    $label_y = $center_y + ($label_radius * sin(deg2rad($label_angle)));
                    echo '<text x="' . $label_x . '" y="' . $label_y . '" text-anchor="middle" dominant-baseline="middle" font-size="12" fill="#fff" font-weight="600">' . esc_html(round($percentage, 1)) . '%</text>';
                    
                    $current_angle = $end_angle;
                }
                
                // Legend
                if ($show_legend) {
                    $legend_y = 350;
                    $legend_x = 100;
                    foreach ($data as $index => $value) {
                        $color = $chart_colors[$index % count($chart_colors)];
                        $x = $legend_x + (($index % 3) * 200);
                        $y = $legend_y + (floor($index / 3) * 30);
                        echo '<rect x="' . $x . '" y="' . ($y - 8) . '" width="16" height="16" fill="' . $color . '" />';
                        echo '<text x="' . ($x + 22) . '" y="' . $y . '" font-size="12" fill="#333">' . esc_html($labels[$index]) . ' (' . esc_html($value) . ')</text>';
                    }
                }
                ?>
            <?php endif; ?>
        </svg>
    </div>
    
    <?php if ($caption || $source) : ?>
        <figcaption class="chart-caption">
            <?php if ($caption) : ?>
                <span class="chart-caption-text"><?php echo wp_kses_post($caption); ?></span>
            <?php endif; ?>
            <?php if ($source) : ?>
                <span class="chart-source">Source: <?php echo esc_html($source); ?></span>
            <?php endif; ?>
        </figcaption>
    <?php endif; ?>
</figure>

