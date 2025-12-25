<?php
/**
 * Flow Chart Block - Render
 */
$title = $attributes['title'] ?? '';
$orientation = $attributes['orientation'] ?? 'horizontal';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-flowchart flowchart flowchart-<?php echo esc_attr($orientation) . $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="fc-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    <div class="fc-steps">
        <?php echo $content; ?>
    </div>
</div>

