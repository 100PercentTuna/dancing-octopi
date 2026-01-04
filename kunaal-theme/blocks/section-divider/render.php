<?php
/**
 * Section Divider Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$variant = isset($attributes['variant']) ? $attributes['variant'] : 'single';
$spacing = isset($attributes['spacing']) ? $attributes['spacing'] : 'medium';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Build classes
$classes = 'wp-block-kunaal-section-divider sectionDivider';
$classes .= ' sectionDivider--' . esc_attr($variant);
$classes .= ' sectionDivider--' . esc_attr($spacing);
$classes .= $class_name;

// Determine ornament character(s)
$ornament = '';
switch ($variant) {
    case 'single':
        $ornament = '<span class="sectionDivider__ornament">✦</span>';
        break;
    case 'triple':
        $ornament = '<span class="sectionDivider__ornament"><span>✦</span><span>✦</span><span>✦</span></span>';
        break;
    case 'fleuron':
        $ornament = '<span class="sectionDivider__ornament">❧</span>';
        break;
}
?>
<div<?php echo $anchor; ?> class="<?php echo esc_attr($classes); ?>" aria-hidden="true">
    <?php echo $ornament; // Already escaped/safe characters ?>
</div>

