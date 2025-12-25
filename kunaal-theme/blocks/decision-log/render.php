<?php
/**
 * Decision Log Block - Render
 */
$title = $attributes['title'] ?? 'Decision Log';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-decision-log decision-log<?php echo $class_name; ?>">
    <h3 class="dl-title"><?php echo esc_html($title); ?></h3>
    <div class="dl-entries">
        <?php echo $content; ?>
    </div>
</div>

