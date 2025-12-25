<?php
/**
 * Aside Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$outcome = isset($attributes['outcome']) ? $attributes['outcome'] : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Get inner blocks content
$inner_content = '';
if (!empty($content)) {
    $inner_content = $content;
}
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-aside aside reveal<?php echo $class_name; ?>">
    <?php echo $inner_content; ?>
    <?php if (!empty($outcome)) : ?>
        <div class="outcome">Result: <strong><?php echo esc_html($outcome); ?></strong></div>
    <?php endif; ?>
</div>

