<?php
/**
 * Timeline Item Block - Render
 */
$date = $attributes['date'] ?? '';
$item_title = $attributes['title'] ?? '';
$description = $attributes['description'] ?? '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div class="wp-block-kunaal-timeline-item timeline-item<?php echo $class_name; ?>">
    <div class="timeline-marker"></div>
    <div class="timeline-content">
        <?php if ($date) : ?>
            <span class="timeline-date"><?php echo esc_html($date); ?></span>
        <?php endif; ?>
        <?php if ($item_title) : ?>
            <h4 class="timeline-event-title"><?php echo esc_html($item_title); ?></h4>
        <?php endif; ?>
        <?php if ($description) : ?>
            <p class="timeline-description"><?php echo wp_kses_post($description); ?></p>
        <?php endif; ?>
    </div>
</div>

