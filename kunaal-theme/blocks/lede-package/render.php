<?php
/**
 * Lede Package Block - Render
 */
$media_url = $attributes['mediaUrl'] ?? '';
$headline = $attributes['headline'] ?? '';
$dek = $attributes['dek'] ?? '';
$credit = $attributes['credit'] ?? '';
$layout = $attributes['layout'] ?? 'overlay';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<header<?php echo $anchor; ?> class="wp-block-kunaal-lede-package lede-package lede-<?php echo esc_attr($layout) . $class_name; ?>">
    <?php if ($media_url) : ?>
        <div class="lede-media">
            <img src="<?php echo esc_url($media_url); ?>" alt="" loading="eager" />
        </div>
    <?php endif; ?>
    
    <div class="lede-text">
        <?php if ($headline) : ?>
            <h1 class="lede-headline"><?php echo wp_kses_post($headline); ?></h1>
        <?php endif; ?>
        <?php if ($dek) : ?>
            <p class="lede-dek"><?php echo wp_kses_post($dek); ?></p>
        <?php endif; ?>
    </div>
    
    <?php if ($credit) : ?>
        <span class="lede-credit"><?php echo esc_html($credit); ?></span>
    <?php endif; ?>
</header>

