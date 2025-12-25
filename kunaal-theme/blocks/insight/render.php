<?php
/**
 * Insight Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$label = isset($attributes['label']) ? $attributes['label'] : 'Key insight';
$content = isset($attributes['content']) ? $attributes['content'] : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-insight insightBox reveal<?php echo $class_name; ?>">
    <div class="label"><?php echo esc_html($label); ?></div>
    <div class="insightContent">
        <?php echo wp_kses_post($content); ?>
    </div>
</div>

