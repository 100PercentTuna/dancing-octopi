<?php
/**
 * Pullquote Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$quote = isset($attributes['quote']) ? $attributes['quote'] : '';
$citation = isset($attributes['citation']) ? $attributes['citation'] : '';
$size = isset($attributes['size']) ? $attributes['size'] : 'normal';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
$size_class = $size === 'large' ? ' pullquote--large' : '';

if (empty($quote)) {
    return; // Don't render empty quotes
}
?>
<blockquote<?php echo $anchor; ?> class="wp-block-kunaal-pullquote pullquote reveal<?php echo $size_class . $class_name; ?>">
    <p><?php echo wp_kses_post($quote); ?></p>
    <?php if (!empty($citation)) : ?>
        <cite>— <?php echo esc_html($citation); ?></cite>
    <?php endif; ?>
</blockquote>

