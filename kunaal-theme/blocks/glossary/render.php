<?php
/**
 * Glossary Block - Render
 */
$title = $attributes['title'] ?? 'Key Terms';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-glossary glossary<?php echo $class_name; ?>">
    <h3 class="glossary-title"><?php echo esc_html($title); ?></h3>
    <dl class="glossary-list">
        <?php echo $content; ?>
    </dl>
</div>

