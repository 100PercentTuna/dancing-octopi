<?php
/**
 * Takeaway Item Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$heading = isset($attributes['heading']) ? $attributes['heading'] : '';
$description = isset($attributes['description']) ? $attributes['description'] : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<li class="wp-block-kunaal-takeaway-item<?php echo $class_name; ?>">
    <div class="takeaway-content">
        <?php if (!empty($heading)) : ?>
            <h4><?php echo esc_html($heading); ?></h4>
        <?php endif; ?>
        <?php if (!empty($description)) : ?>
            <p><?php echo wp_kses_post($description); ?></p>
        <?php endif; ?>
    </div>
</li>
