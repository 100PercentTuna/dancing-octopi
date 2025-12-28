<?php
/**
 * Framework Matrix Block - Render
 */
$title = $attributes['title'] ?? '';
$size = $attributes['size'] ?? '2x2';
$x_label = $attributes['xAxisLabel'] ?? 'X Axis';
$y_label = $attributes['yAxisLabel'] ?? 'Y Axis';
$cells = $attributes['cells'] ?? [];
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

$grid_size = $size === '3x3' ? 3 : 2;
$total_cells = $grid_size * $grid_size;

if (empty($cells)) {
    return;
}
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-framework-matrix framework-matrix matrix-<?php echo esc_attr($size) . $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="fm-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    
    <div class="fm-container">
        <div class="fm-y-label"><?php echo esc_html($y_label); ?></div>
        <div class="fm-grid" style="grid-template-columns: repeat(<?php echo esc_attr($grid_size); ?>, 1fr);">
            <?php for ($i = 0; $i < $total_cells; $i++) : ?>
                <?php $cell = isset($cells[$i]) ? $cells[$i] : ['label' => '', 'content' => '']; ?>
                <div class="fm-cell">
                    <?php if (!empty($cell['label'])) : ?>
                        <span class="fm-cell-label"><?php echo esc_html($cell['label']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($cell['content'])) : ?>
                        <p class="fm-cell-content"><?php echo wp_kses_post($cell['content']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
        <div class="fm-x-label"><?php echo esc_html($x_label); ?></div>
    </div>
</div>

