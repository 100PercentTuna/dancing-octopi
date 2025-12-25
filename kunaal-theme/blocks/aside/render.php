<?php
/**
 * Aside Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$label = isset($attributes['label']) ? $attributes['label'] : '';
$label_type = isset($attributes['labelType']) ? $attributes['labelType'] : 'none';
$outcome = isset($attributes['outcome']) ? $attributes['outcome'] : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Determine display label
$label_texts = array(
    'case-study' => 'Case Study',
    'example' => 'Example',
    'note' => 'Note',
    'sidebar' => 'Sidebar',
    'definition' => 'Definition',
    'warning' => 'Warning',
);

$display_label = '';
if ($label_type === 'custom' && $label) {
    $display_label = $label;
} elseif (isset($label_texts[$label_type])) {
    $display_label = $label_texts[$label_type];
}

// Add label type class for potential styling variations
$type_class = $label_type !== 'none' ? ' aside-' . esc_attr($label_type) : '';
?>
<aside<?php echo $anchor; ?> class="wp-block-kunaal-aside aside<?php echo $type_class . $class_name; ?>">
    <?php if ($display_label) : ?>
        <div class="aside-label"><?php echo esc_html($display_label); ?></div>
    <?php endif; ?>
    <div class="aside-content">
        <?php echo $content; ?>
    </div>
    <?php if ($outcome) : ?>
        <div class="aside-outcome">Result: <strong><?php echo esc_html($outcome); ?></strong></div>
    <?php endif; ?>
</aside>
