<?php
/**
 * Section Header Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$title = isset($attributes['title']) ? $attributes['title'] : '';
$number = isset($attributes['number']) ? $attributes['number'] : '01';
$level = isset($attributes['level']) ? $attributes['level'] : 2;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($title)) {
    return; // Don't render empty headers
}

$heading_tag = 'h' . min(max((int)$level, 1), 6);
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-section-header sectionHead reveal<?php echo $class_name; ?>">
    <<?php echo $heading_tag; ?> class="wp-block-heading"><?php echo esc_html($title); ?></<?php echo $heading_tag; ?>>
    <span class="sectionNum"><?php echo esc_html($number); ?></span>
</div>

