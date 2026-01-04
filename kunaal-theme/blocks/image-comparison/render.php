<?php
/**
 * Image Comparison Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$before_url = isset($attributes['beforeUrl']) ? $attributes['beforeUrl'] : '';
$before_alt = isset($attributes['beforeAlt']) ? $attributes['beforeAlt'] : '';
$before_label = isset($attributes['beforeLabel']) ? $attributes['beforeLabel'] : __('Before', 'kunaal-theme');
$after_url = isset($attributes['afterUrl']) ? $attributes['afterUrl'] : '';
$after_alt = isset($attributes['afterAlt']) ? $attributes['afterAlt'] : '';
$after_label = isset($attributes['afterLabel']) ? $attributes['afterLabel'] : __('After', 'kunaal-theme');
$caption = isset($attributes['caption']) ? $attributes['caption'] : '';
$initial_position = isset($attributes['initialPosition']) ? $attributes['initialPosition'] : 50;
$orientation = isset($attributes['orientation']) ? $attributes['orientation'] : 'horizontal';
$show_labels = isset($attributes['showLabels']) ? $attributes['showLabels'] : true;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($before_url) || empty($after_url)) {
    return; // Don't render without both images
}

$is_horizontal = $orientation === 'horizontal';
?>
<figure<?php echo $anchor; ?> class="wp-block-kunaal-image-comparison imgcmp imgcmp--<?php echo esc_attr($orientation); ?><?php echo $class_name; ?>" data-initial="<?php echo esc_attr($initial_position); ?>" data-orientation="<?php echo esc_attr($orientation); ?>">
    <div class="imgcmp__container">
        <!-- After image (background) -->
        <div class="imgcmp__after">
            <img src="<?php echo esc_url($after_url); ?>" alt="<?php echo esc_attr($after_alt ?: $after_label); ?>" loading="lazy" draggable="false" />
        </div>
        
        <!-- Before image (clipped) -->
        <div class="imgcmp__before" style="<?php echo $is_horizontal ? 'width:' : 'height:'; ?><?php echo esc_attr($initial_position); ?>%;">
            <img src="<?php echo esc_url($before_url); ?>" alt="<?php echo esc_attr($before_alt ?: $before_label); ?>" loading="lazy" draggable="false" />
        </div>
        
        <!-- Slider handle -->
        <div class="imgcmp__handle" style="<?php echo $is_horizontal ? 'left:' : 'top:'; ?><?php echo esc_attr($initial_position); ?>%;" role="slider" aria-valuenow="<?php echo esc_attr($initial_position); ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php esc_attr_e('Image comparison slider', 'kunaal-theme'); ?>" tabindex="0">
            <div class="imgcmp__handle-line"></div>
            <div class="imgcmp__handle-button">
                <?php if ($is_horizontal) { ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M7 12l-4 4m0-4l4-4m0 4h18M17 12l4-4m0 4l-4 4"/>
                    </svg>
                <?php } else { ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M12 7l-4-4m4 0l4 4m-4-4v18m0-4l-4 4m4 0l4-4"/>
                    </svg>
                <?php } ?>
            </div>
        </div>
        
        <!-- Labels -->
        <?php if ($show_labels) { ?>
            <span class="imgcmp__label imgcmp__label--before"><?php echo esc_html($before_label); ?></span>
            <span class="imgcmp__label imgcmp__label--after"><?php echo esc_html($after_label); ?></span>
        <?php } ?>
    </div>
    
    <?php if (!empty($caption)) { ?>
        <figcaption class="imgcmp__caption"><?php echo wp_kses_post($caption); ?></figcaption>
    <?php } ?>
</figure>

