<?php
/**
 * Statistical Distribution Block - Frontend Render
 */
if (!defined('ABSPATH')) {
    exit;
}

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$chart_type = $attributes['chartType'] ?? 'box';
$orientation = $attributes['orientation'] ?? 'horizontal';
$show_mean = $attributes['showMean'] ?? false;
$show_outliers = $attributes['showOutliers'] ?? true;
$show_data_points = $attributes['showDataPoints'] ?? false;
$show_stats = $attributes['showStatistics'] ?? false;
$value_format = $attributes['valueFormat'] ?? 'number';
$currency_symbol = $attributes['currencySymbol'] ?? '$';
$source_note = $attributes['sourceNote'] ?? '';
$data_groups = $attributes['dataGroups'] ?? [];
$data_mode = $attributes['dataMode'] ?? 'raw';

// Helper functions are defined in inc/block-helpers.php

$block_id = 'stat-dist-' . wp_unique_id();
$stats_data = [];

if ($data_mode === 'raw') {
    foreach ($data_groups as $group) {
        $values = array_map('floatval', $group['values'] ?? []);
        if (!empty($values)) {
            $stats_data[] = array_merge(
                ['label' => $group['label'] ?? ''],
                kunaal_calculate_quartiles($values)
            );
        }
    }
} else {
    $stats_data = $attributes['precomputedStats'] ?? [];
}

// Calculate axis range
$all_values = [];
foreach ($stats_data as $stat) {
    $all_values[] = floatval($stat['min'] ?? 0);
    $all_values[] = floatval($stat['max'] ?? 0);
}
$axis_min = !empty($all_values) ? min($all_values) : 0;
$axis_max = !empty($all_values) ? max($all_values) : 100;
?>

<figure class="wp-block-kunaal-statistical-distribution stat-dist--<?php echo esc_attr($chart_type); ?> stat-dist--<?php echo esc_attr($orientation); ?>"
        role="img" aria-labelledby="<?php echo esc_attr($block_id); ?>-title">
    
    <?php if ($title || $subtitle) : ?>
    <header class="stat-dist-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="stat-dist-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="stat-dist-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <div class="stat-dist-chart">
        <svg class="stat-dist-visual" viewBox="0 0 800 400" preserveAspectRatio="xMidYMid meet">
            <?php foreach ($stats_data as $i => $stat) :
                $y = 50 + ($i * 60);
                $min_x = 140 + (($stat['min'] - $axis_min) / ($axis_max - $axis_min)) * 560;
                $q1_x = 140 + (($stat['q1'] - $axis_min) / ($axis_max - $axis_min)) * 560;
                $median_x = 140 + (($stat['median'] - $axis_min) / ($axis_max - $axis_min)) * 560;
                $q3_x = 140 + (($stat['q3'] - $axis_min) / ($axis_max - $axis_min)) * 560;
                $max_x = 140 + (($stat['max'] - $axis_min) / ($axis_max - $axis_min)) * 560;
            ?>
            <g class="stat-group" data-group="<?php echo esc_attr($stat['label']); ?>" transform="translate(0, <?php echo $y; ?>)">
                <text class="stat-label" x="10" y="25"><?php echo esc_html($stat['label']); ?></text>
                
                <?php if ($chart_type === 'box' || $chart_type === 'combo') : ?>
                <!-- Whiskers -->
                <line class="stat-whisker stat-whisker-left" x1="<?php echo $min_x; ?>" y1="20" x2="<?php echo $q1_x; ?>" y2="20" stroke="var(--muted)" stroke-width="2"/>
                <line class="stat-whisker stat-whisker-right" x1="<?php echo $q3_x; ?>" y1="20" x2="<?php echo $max_x; ?>" y2="20" stroke="var(--muted)" stroke-width="2"/>
                <line class="stat-whisker-connector" x1="<?php echo $min_x; ?>" y1="20" x2="<?php echo $max_x; ?>" y2="20" stroke="var(--muted)" stroke-width="1" stroke-dasharray="2,2"/>
                
                <!-- Box -->
                <rect class="stat-box" x="<?php echo $q1_x; ?>" y="10" width="<?php echo $q3_x - $q1_x; ?>" height="20"
                      fill="var(--warmLight)" fill-opacity="0.6" stroke="var(--warm)" stroke-width="2" rx="3"/>
                
                <!-- Median -->
                <line class="stat-median" x1="<?php echo $median_x; ?>" y1="8" x2="<?php echo $median_x; ?>" y2="32"
                      stroke="var(--ink)" stroke-width="2"/>
                
                <?php if ($show_mean) : ?>
                <polygon class="stat-mean" points="<?php echo $stat['mean'] ? ($median_x + 30) : $median_x; ?>,20 <?php echo $stat['mean'] ? ($median_x + 36) : ($median_x + 6); ?>,14 <?php echo $stat['mean'] ? ($median_x + 42) : ($median_x + 12); ?>,20 <?php echo $stat['mean'] ? ($median_x + 36) : ($median_x + 6); ?>,26"
                         fill="var(--blue)"/>
                <?php endif; ?>
                
                <?php if ($show_outliers && !empty($stat['outliers'])) :
                    foreach ($stat['outliers'] as $outlier) :
                        $outlier_x = 140 + (($outlier - $axis_min) / ($axis_max - $axis_min)) * 560;
                ?>
                <circle class="stat-outlier" cx="<?php echo $outlier_x; ?>" cy="20" r="4" fill="var(--terracotta)"/>
                <?php endforeach; endif; ?>
                <?php endif; ?>
            </g>
            <?php endforeach; ?>
            
            <!-- Axis -->
            <g class="stat-axis stat-axis-x">
                <line x1="140" y1="380" x2="700" y2="380" stroke="var(--muted)" stroke-width="1"/>
                <?php for ($i = 0; $i <= 4; $i++) :
                    $x = 140 + ($i / 4) * 560;
                    $val = $axis_min + ($i / 4) * ($axis_max - $axis_min);
                ?>
                <line x1="<?php echo $x; ?>" y1="375" x2="<?php echo $x; ?>" y2="380" stroke="var(--muted)" stroke-width="1"/>
                <text x="<?php echo $x; ?>" y="395" text-anchor="middle" class="axis-label">
                    <?php echo esc_html(kunaal_format_stat_value($val, $value_format, $currency_symbol)); ?>
                </text>
                <?php endfor; ?>
            </g>
        </svg>
    </div>
    
    <?php if ($show_stats) : ?>
    <div class="stat-summary">
        <table role="table">
            <caption class="sr-only"><?php esc_html_e('Summary statistics', 'kunaal-theme'); ?></caption>
            <thead>
                <tr>
                    <th><?php esc_html_e('Group', 'kunaal-theme'); ?></th>
                    <th><?php esc_html_e('Min', 'kunaal-theme'); ?></th>
                    <th><?php esc_html_e('Q1', 'kunaal-theme'); ?></th>
                    <th><?php esc_html_e('Median', 'kunaal-theme'); ?></th>
                    <th><?php esc_html_e('Q3', 'kunaal-theme'); ?></th>
                    <th><?php esc_html_e('Max', 'kunaal-theme'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats_data as $stat) : ?>
                <tr>
                    <td><?php echo esc_html($stat['label']); ?></td>
                    <td><?php echo esc_html(kunaal_format_stat_value($stat['min'], $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(kunaal_format_stat_value($stat['q1'], $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(kunaal_format_stat_value($stat['median'], $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(kunaal_format_stat_value($stat['q3'], $value_format, $currency_symbol)); ?></td>
                    <td><?php echo esc_html(kunaal_format_stat_value($stat['max'], $value_format, $currency_symbol)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <?php if ($source_note) : ?>
    <footer class="stat-dist-footer">
        <p class="stat-dist-source"><?php echo esc_html($source_note); ?></p>
    </footer>
    <?php endif; ?>
</figure>

