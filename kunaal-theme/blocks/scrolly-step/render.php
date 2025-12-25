<?php
/**
 * Scrolly Step Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$step_number = isset($attributes['stepNumber']) ? $attributes['stepNumber'] : 1;
$sticky_title = isset($attributes['stickyTitle']) ? $attributes['stickyTitle'] : '';
$sticky_description = isset($attributes['stickyDescription']) ? $attributes['stickyDescription'] : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

$data_attrs = '';
if ($sticky_title) {
    $data_attrs .= ' data-sticky-title="' . esc_attr($sticky_title) . '"';
}
if ($sticky_description) {
    $data_attrs .= ' data-sticky-description="' . esc_attr($sticky_description) . '"';
}
?>
<div class="scrolly-step<?php echo $class_name; ?>"<?php echo $data_attrs; ?>>
    <?php echo $content; ?>
</div>

