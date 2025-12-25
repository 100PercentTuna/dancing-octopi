<?php
/**
 * Context Panel Block - Render
 */
$label = $attributes['label'] ?? 'Context';
$title = $attributes['title'] ?? '';
$display_label = ($label === 'custom' && $title) ? $title : $label;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<aside<?php echo $anchor; ?> class="wp-block-kunaal-context-panel context-panel<?php echo $class_name; ?>">
    <div class="context-label"><?php echo esc_html($display_label); ?></div>
    <div class="context-content">
        <?php echo $content; ?>
    </div>
</aside>

