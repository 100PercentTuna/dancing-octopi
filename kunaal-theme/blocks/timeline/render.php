<?php
/**
 * Timeline Block - Render
 */
$title = $attributes['title'] ?? '';
$orientation = $attributes['orientation'] ?? 'vertical';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-timeline timeline timeline-<?php echo esc_attr($orientation) . $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="timeline-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    <div class="timeline-events">
        <?php echo $content; ?>
    </div>
</div>

