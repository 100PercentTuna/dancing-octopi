<?php
/**
 * Magazine Figure Block - Render
 */
$media_url = $attributes['mediaUrl'] ?? '';
$alt = $attributes['alt'] ?? '';
$caption = $attributes['caption'] ?? '';
$credit = $attributes['credit'] ?? '';
$size = $attributes['size'] ?? 'default';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($media_url)) return;

$size_class = $size !== 'default' ? ' align' . $size : '';
?>
<figure<?php echo $anchor; ?> class="wp-block-kunaal-magazine-figure magazine-figure<?php echo $size_class . $class_name; ?>">
    <img src="<?php echo esc_url($media_url); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" />
    <?php if ($caption || $credit) : ?>
        <figcaption class="figure-caption">
            <?php if ($caption) : ?>
                <span class="caption-text"><?php echo wp_kses_post($caption); ?></span>
            <?php endif; ?>
            <?php if ($credit) : ?>
                <span class="figure-credit"><?php echo esc_html($credit); ?></span>
            <?php endif; ?>
        </figcaption>
    <?php endif; ?>
</figure>

