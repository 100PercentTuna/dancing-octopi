<?php
/**
 * Scrollytelling Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$sticky_label = isset($attributes['stickyLabel']) ? $attributes['stickyLabel'] : 'SECTION';
$sticky_title = isset($attributes['stickyTitle']) ? $attributes['stickyTitle'] : '';
$sticky_description = isset($attributes['stickyDescription']) ? $attributes['stickyDescription'] : '';
$sticky_position = isset($attributes['stickyPosition']) ? $attributes['stickyPosition'] : 'left';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

$is_right = $sticky_position === 'right';
$inner_class = $is_right ? 'scrolly-inner scrolly-reverse' : 'scrolly-inner';
?>
<section<?php echo $anchor; ?> class="wp-block-kunaal-scrollytelling scrolly<?php echo $class_name; ?>">
    <div class="<?php echo esc_attr($inner_class); ?>">
        <div class="scrolly-sticky">
            <?php if ($sticky_label) : ?>
                <div class="scrolly-label"><?php echo esc_html($sticky_label); ?></div>
            <?php endif; ?>
            <?php if ($sticky_title) : ?>
                <h3 class="scrolly-title" id="scrollyTitle"><?php echo wp_kses_post($sticky_title); ?></h3>
            <?php endif; ?>
            <?php if ($sticky_description) : ?>
                <p class="scrolly-description" id="scrollyNote"><?php echo wp_kses_post($sticky_description); ?></p>
            <?php endif; ?>
        </div>
        <div class="scrolly-steps">
            <?php echo $content; ?>
        </div>
    </div>
</section>

