<?php
/**
 * Sidenote Block - Server-side rendering
 * True Tufte-style margin notes with auto-numbering
 *
 * @package Kunaal_Theme
 */

$content = isset($attributes['content']) ? $attributes['content'] : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($content)) {
    return; // Don't render empty sidenotes
}

// Generate unique ID for accessibility
$sidenote_id = 'sn-' . wp_unique_id();
?>
<span class="sidenote-wrapper<?php echo $class_name; ?>">
    <label 
        for="<?php echo esc_attr($sidenote_id); ?>" 
        class="sidenote-number"
        aria-describedby="<?php echo esc_attr($sidenote_id); ?>-content"
    ></label>
    <input type="checkbox" id="<?php echo esc_attr($sidenote_id); ?>" class="sidenote-toggle" />
    <span<?php echo $anchor; ?> id="<?php echo esc_attr($sidenote_id); ?>-content" class="sidenote" role="note">
        <?php echo wp_kses_post($content); ?>
    </span>
</span>
