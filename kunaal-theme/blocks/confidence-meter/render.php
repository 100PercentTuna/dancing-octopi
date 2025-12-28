<?php
/**
 * Confidence Meter Block - Render
 */
$label = $attributes['label'] ?? '';
$level = $attributes['level'] ?? 50;
$description = $attributes['description'] ?? '';
$show_percentage = $attributes['showPercentage'] ?? true;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($label)) {
    return;
}

// Determine color based on level
$color = '#eab308'; // default yellow
if ($level >= 70) {
    $color = '#16a34a'; // green
} elseif ($level < 40) {
    $color = '#dc2626'; // red
}

if ($level >= 70) {
    $level_class = 'high';
} elseif ($level >= 40) {
    $level_class = 'medium';
} else {
    $level_class = 'low';
}
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-confidence-meter confidence-meter level-<?php echo esc_attr($level_class) . $class_name; ?>">
    <div class="cm-header">
        <span class="cm-label"><?php echo wp_kses_post($label); ?></span>
        <?php if ($show_percentage) { ?>
            <span class="cm-percentage" style="color: <?php echo esc_attr($color); ?>"><?php echo esc_html($level); ?>%</span>
        <?php } ?>
    </div>
    <div class="cm-bar-container">
        <div class="cm-bar" style="width: <?php echo esc_attr($level); ?>%; background: <?php echo esc_attr($color); ?>"></div>
    </div>
    <?php if ($description) { ?>
        <p class="cm-description"><?php echo wp_kses_post($description); ?></p>
    <?php } ?>
</div>

