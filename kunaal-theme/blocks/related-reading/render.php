<?php
/**
 * Related Reading Block - Render
 */
$title = $attributes['title'] ?? 'Further Reading';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<section<?php echo $anchor; ?> class="wp-block-kunaal-related-reading related-reading<?php echo $class_name; ?>">
    <h3 class="related-title"><?php echo esc_html($title); ?></h3>
    <div class="related-list">
        <?php echo $content; ?>
    </div>
</section>

