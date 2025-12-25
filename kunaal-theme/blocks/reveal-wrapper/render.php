<?php
/**
 * Reveal Wrapper Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$animation_type = isset($attributes['animationType']) ? $attributes['animationType'] : 'fade-up';
$delay = isset($attributes['delay']) ? $attributes['delay'] : 0;
$duration = isset($attributes['duration']) ? $attributes['duration'] : 600;
$threshold = isset($attributes['threshold']) ? $attributes['threshold'] : 20;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

$style = sprintf(
    '--reveal-delay: %dms; --reveal-duration: %dms;',
    (int) $delay,
    (int) $duration
);
?>
<div<?php echo $anchor; ?> 
    class="wp-block-kunaal-reveal-wrapper reveal-wrapper reveal-<?php echo esc_attr($animation_type); ?><?php echo $class_name; ?>"
    style="<?php echo esc_attr($style); ?>"
    data-reveal-threshold="<?php echo esc_attr($threshold); ?>"
>
    <?php echo $content; ?>
</div>

