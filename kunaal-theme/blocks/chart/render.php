<?php
/**
 * Chart Block - Render (v2.0)
 * Comprehensive chart rendering with multiple chart types
 */
$chart_type = $attributes['chartType'] ?? 'bar';
$orientation = $attributes['orientation'] ?? 'vertical';
$bar_mode = $attributes['barMode'] ?? 'simple';
$title = $attributes['title'] ?? '';
$data_str = $attributes['data'] ?? '';
$data2_str = $attributes['data2'] ?? '';
$data3_str = $attributes['data3'] ?? '';
$labels_str = $attributes['labels'] ?? '';
$series_labels_str = $attributes['seriesLabels'] ?? '';
$source = $attributes['source'] ?? '';
$caption = $attributes['caption'] ?? '';
$show_legend = $attributes['showLegend'] ?? true;
$show_values = $attributes['showValues'] ?? true;
$show_grid = $attributes['showGrid'] ?? true;
$colors = $attributes['colors'] ?? 'theme';
$start_value = floatval($attributes['startValue'] ?? 0);
$unit = $attributes['unit'] ?? '';
$unit_position = $attributes['unitPosition'] ?? 'suffix';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Parse data arrays - defined once globally
if (!function_exists('kunaal_parse_chart_data')) {
    function kunaal_parse_chart_data($str) {
        if (!$str) return array();
        return array_map('floatval', array_map('trim', explode(',', $str)));
    }
}

// Format value with unit - defined once globally
if (!function_exists('kunaal_format_chart_value')) {
    function kunaal_format_chart_value($val, $unit, $unit_position) {
        $formatted = number_format($val, ($val == floor($val)) ? 0 : 1);
        if ($unit) {
            return $unit_position === 'prefix' ? $unit . $formatted : $formatted . $unit;
        }
        return $formatted;
    }
}

$data = kunaal_parse_chart_data($data_str);
$data2 = kunaal_parse_chart_data($data2_str);
$data3 = kunaal_parse_chart_data($data3_str);
$labels = $labels_str ? array_map('trim', explode(',', $labels_str)) : array();
$series_labels = $series_labels_str ? array_map('trim', explode(',', $series_labels_str)) : array('Series 1', 'Series 2', 'Series 3');

if (empty($data)) return;

// Ensure labels match data length
while (count($labels) < count($data)) {
    $labels[] = 'Item ' . (count($labels) + 1);
}

// Color schemes
$color_schemes = array(
    'theme' => array('#1E5AFF', '#7D6B5D', '#5B7BA8', '#d4c4b5', '#3B82F6', '#B8A99A', '#60A5FA'),
    'blue' => array('#1E5AFF', '#3B82F6', '#60A5FA', '#93C5FD', '#DBEAFE', '#1D4ED8', '#2563EB'),
    'warm' => array('#7D6B5D', '#B8A99A', '#d4c4b5', '#E8DED4', '#C9B8A8', '#8B7355', '#A69080'),
    'green' => array('#059669', '#10B981', '#34D399', '#6EE7B7', '#A7F3D0', '#047857', '#065F46'),
    'mono' => array('#1f2937', '#374151', '#4b5563', '#6b7280', '#9ca3af', '#d1d5db', '#e5e7eb'),
    'rainbow' => array('#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#06B6D4')
);

$chart_colors = $color_schemes[$colors] ?? $color_schemes['theme'];

// Positive/negative colors for waterfall
$positive_color = '#10B981';
$negative_color = '#EF4444';
$total_color = $chart_colors[0];

// SVG dimensions
$svg_width = 800;
$svg_height = 400;
$margin = array('top' => 40, 'right' => 40, 'bottom' => 60, 'left' => 60);
$chart_width = $svg_width - $margin['left'] - $margin['right'];
$chart_height = $svg_height - $margin['top'] - $margin['bottom'];
?>
<figure<?php echo $anchor; ?> class="wp-block-kunaal-chart chart chart-<?php echo esc_attr($chart_type) . ' chart-' . esc_attr($orientation) . $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="chart-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    
    <div class="chart-container">
        <svg class="chart-svg" viewBox="0 0 <?php echo $svg_width; ?> <?php echo $svg_height; ?>" preserveAspectRatio="xMidYMid meet">
            
            <?php if ($chart_type === 'bar') : ?>
                <?php
                $max_value = max($data);
                $min_value = min(0, min($data));
                $range = $max_value - $min_value;
                $bar_count = count($data);
                
                if ($orientation === 'horizontal') {
                    $bar_height = min(40, ($chart_height - 20) / $bar_count);
                    $bar_gap = $bar_height * 0.3;
                    $total_height = $bar_count * ($bar_height + $bar_gap) - $bar_gap;
                    $start_y = $margin['top'] + ($chart_height - $total_height) / 2;
                    
                    // Grid lines
                    if ($show_grid) {
                        for ($i = 0; $i <= 5; $i++) {
                            $x = $margin['left'] + ($i / 5) * $chart_width;
                            echo '<line x1="' . $x . '" y1="' . $margin['top'] . '" x2="' . $x . '" y2="' . ($svg_height - $margin['bottom']) . '" stroke="rgba(0,0,0,0.05)" stroke-width="1"/>';
                        }
                    }
                    
                    foreach ($data as $index => $value) {
                        $bar_width = ($value / $max_value) * $chart_width;
                        $y = $start_y + $index * ($bar_height + $bar_gap);
                        $color = $chart_colors[$index % count($chart_colors)];
                        
                        echo '<rect x="' . $margin['left'] . '" y="' . $y . '" width="' . max(0, $bar_width) . '" height="' . $bar_height . '" fill="' . $color . '" rx="2"/>';
                        
                        // Label
                        echo '<text x="' . ($margin['left'] - 8) . '" y="' . ($y + $bar_height / 2) . '" dominant-baseline="middle" text-anchor="end" font-size="12" fill="#666" font-family="var(--sans)">' . esc_html($labels[$index]) . '</text>';
                        
                        // Value
                        if ($show_values) {
                            echo '<text x="' . ($margin['left'] + $bar_width + 8) . '" y="' . ($y + $bar_height / 2) . '" dominant-baseline="middle" font-size="12" fill="#333" font-weight="500" font-family="var(--sans)">' . kunaal_format_chart_value($value, $unit, $unit_position) . '</text>';
                        }
                    }
                } else {
                    // Vertical bars
                    $bar_width = min(60, ($chart_width - 40) / $bar_count);
                    $bar_gap = $bar_width * 0.3;
                    $total_width = $bar_count * ($bar_width + $bar_gap) - $bar_gap;
                    $start_x = $margin['left'] + ($chart_width - $total_width) / 2;
                    
                    // Grid lines
                    if ($show_grid) {
                        for ($i = 0; $i <= 5; $i++) {
                            $y = $margin['top'] + ($i / 5) * $chart_height;
                            echo '<line x1="' . $margin['left'] . '" y1="' . $y . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . $y . '" stroke="rgba(0,0,0,0.05)" stroke-width="1"/>';
                        }
                    }
                    
                    // Baseline
                    echo '<line x1="' . $margin['left'] . '" y1="' . ($svg_height - $margin['bottom']) . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . ($svg_height - $margin['bottom']) . '" stroke="rgba(0,0,0,0.1)" stroke-width="1"/>';
                    
                    foreach ($data as $index => $value) {
                        $bar_height_px = ($value / $max_value) * $chart_height;
                        $x = $start_x + $index * ($bar_width + $bar_gap);
                        $y = $margin['top'] + $chart_height - $bar_height_px;
                        $color = $chart_colors[$index % count($chart_colors)];
                        
                        echo '<rect x="' . $x . '" y="' . $y . '" width="' . $bar_width . '" height="' . $bar_height_px . '" fill="' . $color . '" rx="2"/>';
                        
                        // Value on top
                        if ($show_values) {
                            echo '<text x="' . ($x + $bar_width / 2) . '" y="' . ($y - 8) . '" text-anchor="middle" font-size="11" fill="#333" font-weight="500" font-family="var(--sans)">' . kunaal_format_chart_value($value, $unit, $unit_position) . '</text>';
                        }
                        
                        // Label below
                        echo '<text x="' . ($x + $bar_width / 2) . '" y="' . ($svg_height - $margin['bottom'] + 20) . '" text-anchor="middle" font-size="11" fill="#666" font-family="var(--sans)">' . esc_html($labels[$index]) . '</text>';
                    }
                }
                ?>
                
            <?php elseif ($chart_type === 'stacked-bar' || $chart_type === 'clustered-bar') : ?>
                <?php
                $all_series = array($data);
                if (!empty($data2)) $all_series[] = $data2;
                if (!empty($data3)) $all_series[] = $data3;
                $series_count = count($all_series);
                
                if ($chart_type === 'stacked-bar') {
                    // Calculate max stacked value
                    $max_stacked = 0;
                    for ($i = 0; $i < count($data); $i++) {
                        $sum = 0;
                        foreach ($all_series as $series) {
                            $sum += isset($series[$i]) ? $series[$i] : 0;
                        }
                        $max_stacked = max($max_stacked, $sum);
                    }
                    
                    if ($orientation === 'horizontal') {
                        $bar_height = min(35, ($chart_height - 20) / count($data));
                        $bar_gap = $bar_height * 0.4;
                        $total_height = count($data) * ($bar_height + $bar_gap) - $bar_gap;
                        $start_y = $margin['top'] + ($chart_height - $total_height) / 2;
                        
                        foreach ($data as $index => $value) {
                            $y = $start_y + $index * ($bar_height + $bar_gap);
                            $current_x = $margin['left'];
                            
                            foreach ($all_series as $s_index => $series) {
                                $val = isset($series[$index]) ? $series[$index] : 0;
                                $bar_w = ($val / $max_stacked) * $chart_width;
                                $color = $chart_colors[$s_index % count($chart_colors)];
                                
                                echo '<rect x="' . $current_x . '" y="' . $y . '" width="' . $bar_w . '" height="' . $bar_height . '" fill="' . $color . '"/>';
                                $current_x += $bar_w;
                            }
                            
                            echo '<text x="' . ($margin['left'] - 8) . '" y="' . ($y + $bar_height / 2) . '" dominant-baseline="middle" text-anchor="end" font-size="11" fill="#666" font-family="var(--sans)">' . esc_html($labels[$index]) . '</text>';
                        }
                    } else {
                        // Vertical stacked
                        $bar_width = min(50, ($chart_width - 40) / count($data));
                        $bar_gap = $bar_width * 0.4;
                        $total_width = count($data) * ($bar_width + $bar_gap) - $bar_gap;
                        $start_x = $margin['left'] + ($chart_width - $total_width) / 2;
                        
                        // Baseline
                        echo '<line x1="' . $margin['left'] . '" y1="' . ($svg_height - $margin['bottom']) . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . ($svg_height - $margin['bottom']) . '" stroke="rgba(0,0,0,0.1)" stroke-width="1"/>';
                        
                        foreach ($data as $index => $value) {
                            $x = $start_x + $index * ($bar_width + $bar_gap);
                            $current_y = $svg_height - $margin['bottom'];
                            
                            foreach ($all_series as $s_index => $series) {
                                $val = isset($series[$index]) ? $series[$index] : 0;
                                $bar_h = ($val / $max_stacked) * $chart_height;
                                $color = $chart_colors[$s_index % count($chart_colors)];
                                
                                echo '<rect x="' . $x . '" y="' . ($current_y - $bar_h) . '" width="' . $bar_width . '" height="' . $bar_h . '" fill="' . $color . '"/>';
                                $current_y -= $bar_h;
                            }
                            
                            echo '<text x="' . ($x + $bar_width / 2) . '" y="' . ($svg_height - $margin['bottom'] + 18) . '" text-anchor="middle" font-size="10" fill="#666" font-family="var(--sans)">' . esc_html($labels[$index]) . '</text>';
                        }
                    }
                } else {
                    // Clustered bars
                    $max_value = 0;
                    foreach ($all_series as $series) {
                        $max_value = max($max_value, max($series));
                    }
                    
                    if ($orientation === 'horizontal') {
                        $sub_bar_height = 18;
                        $sub_bar_gap = 3;
                        $group_height = $series_count * $sub_bar_height + ($series_count - 1) * $sub_bar_gap;
                        $group_gap = 20;
                        $total_height = count($data) * ($group_height + $group_gap) - $group_gap;
                        $start_y = $margin['top'] + ($chart_height - $total_height) / 2;
                        
                        foreach ($data as $index => $value) {
                            $group_y = $start_y + $index * ($group_height + $group_gap);
                            
                            foreach ($all_series as $s_index => $series) {
                                $val = isset($series[$index]) ? $series[$index] : 0;
                                $bar_w = ($val / $max_value) * $chart_width;
                                $y = $group_y + $s_index * ($sub_bar_height + $sub_bar_gap);
                                $color = $chart_colors[$s_index % count($chart_colors)];
                                
                                echo '<rect x="' . $margin['left'] . '" y="' . $y . '" width="' . $bar_w . '" height="' . $sub_bar_height . '" fill="' . $color . '" rx="2"/>';
                            }
                            
                            echo '<text x="' . ($margin['left'] - 8) . '" y="' . ($group_y + $group_height / 2) . '" dominant-baseline="middle" text-anchor="end" font-size="11" fill="#666" font-family="var(--sans)">' . esc_html($labels[$index]) . '</text>';
                        }
                    } else {
                        // Vertical clustered
                        $sub_bar_width = 20;
                        $sub_bar_gap = 3;
                        $group_width = $series_count * $sub_bar_width + ($series_count - 1) * $sub_bar_gap;
                        $group_gap = 25;
                        $total_width = count($data) * ($group_width + $group_gap) - $group_gap;
                        $start_x = $margin['left'] + ($chart_width - $total_width) / 2;
                        
                        echo '<line x1="' . $margin['left'] . '" y1="' . ($svg_height - $margin['bottom']) . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . ($svg_height - $margin['bottom']) . '" stroke="rgba(0,0,0,0.1)" stroke-width="1"/>';
                        
                        foreach ($data as $index => $value) {
                            $group_x = $start_x + $index * ($group_width + $group_gap);
                            
                            foreach ($all_series as $s_index => $series) {
                                $val = isset($series[$index]) ? $series[$index] : 0;
                                $bar_h = ($val / $max_value) * $chart_height;
                                $x = $group_x + $s_index * ($sub_bar_width + $sub_bar_gap);
                                $y = $margin['top'] + $chart_height - $bar_h;
                                $color = $chart_colors[$s_index % count($chart_colors)];
                                
                                echo '<rect x="' . $x . '" y="' . $y . '" width="' . $sub_bar_width . '" height="' . $bar_h . '" fill="' . $color . '" rx="2"/>';
                            }
                            
                            echo '<text x="' . ($group_x + $group_width / 2) . '" y="' . ($svg_height - $margin['bottom'] + 18) . '" text-anchor="middle" font-size="10" fill="#666" font-family="var(--sans)">' . esc_html($labels[$index]) . '</text>';
                        }
                    }
                }
                
                // Legend for multi-series
                if ($show_legend && $series_count > 1) {
                    $legend_y = $svg_height - 15;
                    $legend_x = $margin['left'];
                    for ($i = 0; $i < $series_count; $i++) {
                        $color = $chart_colors[$i % count($chart_colors)];
                        $label = isset($series_labels[$i]) ? $series_labels[$i] : ('Series ' . ($i + 1));
                        $x = $legend_x + $i * 100;
                        echo '<rect x="' . $x . '" y="' . ($legend_y - 10) . '" width="14" height="14" fill="' . $color . '" rx="2"/>';
                        echo '<text x="' . ($x + 20) . '" y="' . $legend_y . '" font-size="11" fill="#666" font-family="var(--sans)">' . esc_html($label) . '</text>';
                    }
                }
                ?>
                
            <?php elseif ($chart_type === 'line') : ?>
                <?php
                $all_series = array($data);
                if (!empty($data2)) $all_series[] = $data2;
                if (!empty($data3)) $all_series[] = $data3;
                $series_count = count($all_series);
                
                $max_value = 0;
                $min_value = PHP_INT_MAX;
                foreach ($all_series as $series) {
                    $max_value = max($max_value, max($series));
                    $min_value = min($min_value, min($series));
                }
                $min_value = min(0, $min_value);
                $range = $max_value - $min_value;
                
                // Grid lines
                if ($show_grid) {
                    for ($i = 0; $i <= 5; $i++) {
                        $y = $margin['top'] + ($i / 5) * $chart_height;
                        echo '<line x1="' . $margin['left'] . '" y1="' . $y . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . $y . '" stroke="rgba(0,0,0,0.05)" stroke-width="1"/>';
                    }
                }
                
                // Baseline
                echo '<line x1="' . $margin['left'] . '" y1="' . ($svg_height - $margin['bottom']) . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . ($svg_height - $margin['bottom']) . '" stroke="rgba(0,0,0,0.1)" stroke-width="1"/>';
                
                foreach ($all_series as $s_index => $series) {
                    $color = $chart_colors[$s_index % count($chart_colors)];
                    $points = array();
                    
                    foreach ($series as $index => $value) {
                        $x = $margin['left'] + ($index / max(1, count($series) - 1)) * $chart_width;
                        $y = $margin['top'] + $chart_height - (($value - $min_value) / $range) * $chart_height;
                        $points[] = $x . ',' . $y;
                    }
                    
                    // Area fill (only for first series)
                    if ($s_index === 0) {
                        $area_points = $points;
                        $area_points[] = ($margin['left'] + $chart_width) . ',' . ($svg_height - $margin['bottom']);
                        $area_points[] = $margin['left'] . ',' . ($svg_height - $margin['bottom']);
                        echo '<polygon points="' . implode(' ', $area_points) . '" fill="' . $color . '" opacity="0.1"/>';
                    }
                    
                    // Line
                    $path_d = 'M ' . implode(' L ', $points);
                    echo '<path d="' . $path_d . '" fill="none" stroke="' . $color . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>';
                    
                    // Data points
                    foreach ($series as $index => $value) {
                        $x = $margin['left'] + ($index / max(1, count($series) - 1)) * $chart_width;
                        $y = $margin['top'] + $chart_height - (($value - $min_value) / $range) * $chart_height;
                        echo '<circle cx="' . $x . '" cy="' . $y . '" r="4" fill="' . $color . '"/>';
                        
                        // Values
                        if ($show_values && $s_index === 0) {
                            echo '<text x="' . $x . '" y="' . ($y - 10) . '" text-anchor="middle" font-size="10" fill="#333" font-family="var(--sans)">' . kunaal_format_chart_value($value, $unit, $unit_position) . '</text>';
                        }
                    }
                }
                
                // X-axis labels
                foreach ($labels as $index => $label) {
                    $x = $margin['left'] + ($index / max(1, count($labels) - 1)) * $chart_width;
                    echo '<text x="' . $x . '" y="' . ($svg_height - $margin['bottom'] + 20) . '" text-anchor="middle" font-size="11" fill="#666" font-family="var(--sans)">' . esc_html($label) . '</text>';
                }
                
                // Legend
                if ($show_legend && $series_count > 1) {
                    $legend_y = $margin['top'] - 10;
                    for ($i = 0; $i < $series_count; $i++) {
                        $color = $chart_colors[$i % count($chart_colors)];
                        $label = isset($series_labels[$i]) ? $series_labels[$i] : ('Series ' . ($i + 1));
                        $x = $margin['left'] + $i * 100;
                        echo '<line x1="' . $x . '" y1="' . $legend_y . '" x2="' . ($x + 20) . '" y2="' . $legend_y . '" stroke="' . $color . '" stroke-width="2"/>';
                        echo '<text x="' . ($x + 26) . '" y="' . ($legend_y + 4) . '" font-size="11" fill="#666" font-family="var(--sans)">' . esc_html($label) . '</text>';
                    }
                }
                ?>
                
            <?php elseif ($chart_type === 'pie' || $chart_type === 'donut') : ?>
                <?php
                $total = array_sum($data);
                $center_x = $svg_width / 2;
                $center_y = $svg_height / 2;
                $radius = min($chart_width, $chart_height) / 2 - 20;
                $inner_radius = $chart_type === 'donut' ? $radius * 0.55 : 0;
                $current_angle = -90;
                
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
                    
                    if ($chart_type === 'donut') {
                        $ix1 = $center_x + ($inner_radius * cos(deg2rad($current_angle)));
                        $iy1 = $center_y + ($inner_radius * sin(deg2rad($current_angle)));
                        $ix2 = $center_x + ($inner_radius * cos(deg2rad($end_angle)));
                        $iy2 = $center_y + ($inner_radius * sin(deg2rad($end_angle)));
                        
                        echo '<path d="M ' . $x1 . ',' . $y1 . ' A ' . $radius . ',' . $radius . ' 0 ' . $large_arc . ',1 ' . $x2 . ',' . $y2 . ' L ' . $ix2 . ',' . $iy2 . ' A ' . $inner_radius . ',' . $inner_radius . ' 0 ' . $large_arc . ',0 ' . $ix1 . ',' . $iy1 . ' Z" fill="' . $color . '"/>';
                    } else {
                        echo '<path d="M ' . $center_x . ',' . $center_y . ' L ' . $x1 . ',' . $y1 . ' A ' . $radius . ',' . $radius . ' 0 ' . $large_arc . ',1 ' . $x2 . ',' . $y2 . ' Z" fill="' . $color . '"/>';
                    }
                    
                    // Label
                    if ($show_values && $percentage > 5) {
                        $label_angle = $current_angle + ($angle / 2);
                        $label_radius = $chart_type === 'donut' ? ($radius + $inner_radius) / 2 : $radius * 0.65;
                        $label_x = $center_x + ($label_radius * cos(deg2rad($label_angle)));
                        $label_y = $center_y + ($label_radius * sin(deg2rad($label_angle)));
                        echo '<text x="' . $label_x . '" y="' . $label_y . '" text-anchor="middle" dominant-baseline="middle" font-size="12" fill="#fff" font-weight="600" font-family="var(--sans)">' . round($percentage) . '%</text>';
                    }
                    
                    $current_angle = $end_angle;
                }
                
                // Center text for donut
                if ($chart_type === 'donut' && $show_values) {
                    echo '<text x="' . $center_x . '" y="' . ($center_y - 5) . '" text-anchor="middle" font-size="24" font-weight="700" fill="#333" font-family="var(--serif)">' . kunaal_format_chart_value($total, $unit, $unit_position) . '</text>';
                    echo '<text x="' . $center_x . '" y="' . ($center_y + 18) . '" text-anchor="middle" font-size="12" fill="#666" font-family="var(--sans)">Total</text>';
                }
                
                // Legend
                if ($show_legend) {
                    $legend_x = $svg_width - $margin['right'] - 120;
                    foreach ($data as $index => $value) {
                        $color = $chart_colors[$index % count($chart_colors)];
                        $y = $margin['top'] + $index * 24;
                        echo '<rect x="' . $legend_x . '" y="' . ($y - 8) . '" width="14" height="14" fill="' . $color . '" rx="2"/>';
                        echo '<text x="' . ($legend_x + 20) . '" y="' . $y . '" font-size="11" fill="#666" font-family="var(--sans)">' . esc_html($labels[$index]) . '</text>';
                    }
                }
                ?>
                
            <?php elseif ($chart_type === 'waterfall') : ?>
                <?php
                // Calculate cumulative values
                $cumulative = array();
                $running_total = $start_value;
                
                // First value is the starting point
                $cumulative[] = array(
                    'start' => 0,
                    'end' => $start_value,
                    'value' => $start_value,
                    'type' => 'total'
                );
                $running_total = $start_value;
                
                // Middle values are changes
                for ($i = 0; $i < count($data); $i++) {
                    $change = $data[$i];
                    $new_total = $running_total + $change;
                    $cumulative[] = array(
                        'start' => $running_total,
                        'end' => $new_total,
                        'value' => $change,
                        'type' => $change >= 0 ? 'positive' : 'negative'
                    );
                    $running_total = $new_total;
                }
                
                // Last value is the final total
                $cumulative[] = array(
                    'start' => 0,
                    'end' => $running_total,
                    'value' => $running_total,
                    'type' => 'total'
                );
                
                // Determine scale
                $all_values = array();
                foreach ($cumulative as $item) {
                    $all_values[] = $item['start'];
                    $all_values[] = $item['end'];
                }
                $max_val = max($all_values);
                $min_val = min(0, min($all_values));
                $range = $max_val - $min_val;
                
                $bar_count = count($cumulative);
                $bar_width = min(70, ($chart_width - 40) / $bar_count);
                $bar_gap = $bar_width * 0.25;
                $total_width = $bar_count * ($bar_width + $bar_gap) - $bar_gap;
                $start_x = $margin['left'] + ($chart_width - $total_width) / 2;
                
                // Grid and baseline
                echo '<line x1="' . $margin['left'] . '" y1="' . ($svg_height - $margin['bottom']) . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . ($svg_height - $margin['bottom']) . '" stroke="rgba(0,0,0,0.1)" stroke-width="1"/>';
                
                $zero_y = $margin['top'] + (($max_val - 0) / $range) * $chart_height;
                echo '<line x1="' . $margin['left'] . '" y1="' . $zero_y . '" x2="' . ($svg_width - $margin['right']) . '" y2="' . $zero_y . '" stroke="rgba(0,0,0,0.2)" stroke-width="1" stroke-dasharray="4,4"/>';
                
                foreach ($cumulative as $index => $item) {
                    $x = $start_x + $index * ($bar_width + $bar_gap);
                    
                    if ($item['type'] === 'total') {
                        // Full bar from zero
                        $bar_top = $margin['top'] + (($max_val - $item['end']) / $range) * $chart_height;
                        $bar_bottom = $zero_y;
                        $bar_height = abs($bar_bottom - $bar_top);
                        $y = min($bar_top, $bar_bottom);
                        $color = $total_color;
                    } else {
                        // Floating bar
                        $start_y = $margin['top'] + (($max_val - $item['start']) / $range) * $chart_height;
                        $end_y = $margin['top'] + (($max_val - $item['end']) / $range) * $chart_height;
                        $y = min($start_y, $end_y);
                        $bar_height = abs($end_y - $start_y);
                        $color = $item['type'] === 'positive' ? $positive_color : $negative_color;
                    }
                    
                    echo '<rect x="' . $x . '" y="' . $y . '" width="' . $bar_width . '" height="' . max(2, $bar_height) . '" fill="' . $color . '" rx="2"/>';
                    
                    // Connector line (except for last)
                    if ($index < count($cumulative) - 1) {
                        $connect_y = $margin['top'] + (($max_val - $item['end']) / $range) * $chart_height;
                        $next_x = $start_x + ($index + 1) * ($bar_width + $bar_gap);
                        echo '<line x1="' . ($x + $bar_width) . '" y1="' . $connect_y . '" x2="' . $next_x . '" y2="' . $connect_y . '" stroke="#999" stroke-width="1" stroke-dasharray="3,3"/>';
                    }
                    
                    // Value label
                    if ($show_values) {
                        $label_y = $y - 8;
                        $sign = ($item['type'] === 'positive' && $item['value'] > 0) ? '+' : '';
                        echo '<text x="' . ($x + $bar_width / 2) . '" y="' . $label_y . '" text-anchor="middle" font-size="11" fill="#333" font-weight="500" font-family="var(--sans)">' . $sign . kunaal_format_chart_value($item['value'], $unit, $unit_position) . '</text>';
                    }
                    
                    // X-axis label
                    $label_index = $index === 0 ? 0 : ($index === count($cumulative) - 1 ? -1 : $index - 1);
                    $label_text = '';
                    if ($index === 0) {
                        $label_text = isset($labels[0]) ? $labels[0] : 'Start';
                    } elseif ($index === count($cumulative) - 1) {
                        $label_text = 'Total';
                    } else {
                        $label_text = isset($labels[$index]) ? $labels[$index] : ('Step ' . $index);
                    }
                    echo '<text x="' . ($x + $bar_width / 2) . '" y="' . ($svg_height - $margin['bottom'] + 18) . '" text-anchor="middle" font-size="10" fill="#666" font-family="var(--sans)">' . esc_html($label_text) . '</text>';
                }
                
                // Legend
                if ($show_legend) {
                    $legend_y = $svg_height - 15;
                    echo '<rect x="' . $margin['left'] . '" y="' . ($legend_y - 10) . '" width="12" height="12" fill="' . $positive_color . '" rx="2"/>';
                    echo '<text x="' . ($margin['left'] + 18) . '" y="' . $legend_y . '" font-size="10" fill="#666" font-family="var(--sans)">Increase</text>';
                    echo '<rect x="' . ($margin['left'] + 80) . '" y="' . ($legend_y - 10) . '" width="12" height="12" fill="' . $negative_color . '" rx="2"/>';
                    echo '<text x="' . ($margin['left'] + 98) . '" y="' . $legend_y . '" font-size="10" fill="#666" font-family="var(--sans)">Decrease</text>';
                    echo '<rect x="' . ($margin['left'] + 170) . '" y="' . ($legend_y - 10) . '" width="12" height="12" fill="' . $total_color . '" rx="2"/>';
                    echo '<text x="' . ($margin['left'] + 188) . '" y="' . $legend_y . '" font-size="10" fill="#666" font-family="var(--sans)">Total</text>';
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
