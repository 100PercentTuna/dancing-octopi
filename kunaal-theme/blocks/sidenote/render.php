<?php
/**
 * Sidenote Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$marker = isset($attributes['marker']) ? $attributes['marker'] : '*';
$content = isset($attributes['content']) ? $attributes['content'] : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($content)) {
    return; // Don't render empty sidenotes
}

// Enqueue the sidenote font only when this block is present
wp_enqueue_style(
    'kunaal-caveat-font',
    'https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600&display=swap',
    array(),
    null
);

// Generate unique ID for accessibility
$sidenote_id = 'sidenote-' . wp_unique_id();
?>
<span class="wp-block-kunaal-sidenote sidenote-wrapper<?php echo $class_name; ?>">
    <button 
        type="button"
        class="sidenote-ref" 
        aria-describedby="<?php echo esc_attr($sidenote_id); ?>"
        aria-expanded="false"
    ><?php echo esc_html($marker); ?></button>
    <span<?php echo $anchor; ?> id="<?php echo esc_attr($sidenote_id); ?>" class="sidenote" role="note">
        <?php echo wp_kses_post($content); ?>
    </span>
</span>

