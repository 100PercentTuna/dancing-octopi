<?php
/**
 * Citation Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$quote = isset($attributes['quote']) ? $attributes['quote'] : '';
$author = isset($attributes['author']) ? $attributes['author'] : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($quote)) {
    return; // Don't render empty citations
}
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-citation citation reveal<?php echo $class_name; ?>">
    <blockquote class="wp-block-quote">
        <p><?php echo wp_kses_post($quote); ?></p>
    </blockquote>
    <?php if (!empty($author)) : ?>
        <div class="author">— <?php echo esc_html($author); ?></div>
    <?php endif; ?>
</div>

