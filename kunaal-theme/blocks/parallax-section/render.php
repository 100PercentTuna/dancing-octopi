<?php
/**
 * Parallax Section Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$media_url = isset($attributes['mediaUrl']) ? $attributes['mediaUrl'] : '';
$min_height = isset($attributes['minHeight']) ? $attributes['minHeight'] : '60vh';
$overlay_opacity = isset($attributes['overlayOpacity']) ? $attributes['overlayOpacity'] / 100 : 0.5;
$parallax_intensity = isset($attributes['parallaxIntensity']) ? $attributes['parallaxIntensity'] : 30;
$content_alignment = isset($attributes['contentAlignment']) ? $attributes['contentAlignment'] : 'center';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
$align_class = isset($attributes['align']) ? ' align' . esc_attr($attributes['align']) : '';

$wrapper_style = sprintf(
    'min-height: %s;',
    esc_attr($min_height)
);

$bg_style = '';
if ($media_url) {
    $bg_style = sprintf(
        'background-image: url(%s);',
        esc_url($media_url)
    );
}

$overlay_style = sprintf(
    'background: rgba(11,18,32,%s);',
    esc_attr($overlay_opacity)
);

$content_style = sprintf(
    'text-align: %s;',
    esc_attr($content_alignment)
);
?>
<section<?php echo $anchor; ?>
    class="wp-block-kunaal-parallax-section parallax-section<?php echo $align_class . $class_name; ?>"
    style="<?php echo $wrapper_style; ?>"
    data-parallax-intensity="<?php echo esc_attr($parallax_intensity); ?>"
>
    <div class="parallax-bg" style="<?php echo $bg_style; ?>"></div>
    <div class="parallax-overlay" style="<?php echo $overlay_style; ?>"></div>
    <div class="parallax-content" style="<?php echo $content_style; ?>">
        <?php echo $content; ?>
    </div>
</section>

