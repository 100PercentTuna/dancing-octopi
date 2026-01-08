<?php
/**
 * Custom TOC Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$title = isset($attributes['title']) ? $attributes['title'] : 'Contents';
$show_eyebrow = isset($attributes['showEyebrow']) ? (bool) $attributes['showEyebrow'] : true;
$eyebrow_text = isset($attributes['eyebrowText']) ? (string) $attributes['eyebrowText'] : 'IN THIS ESSAY';
$items = isset($attributes['items']) ? $attributes['items'] : array();
$sticky = isset($attributes['sticky']) ? $attributes['sticky'] : true;
$highlight_active = isset($attributes['highlightActive']) ? $attributes['highlightActive'] : true;
$show_numbers = isset($attributes['showNumbers']) ? $attributes['showNumbers'] : false;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Check Customizer setting for hiding on mobile
$hide_mobile = get_theme_mod('kunaal_custom_toc_hide_mobile', false);

// Build classes
$classes = 'wp-block-kunaal-custom-toc customToc';
if ($sticky) {
    $classes .= ' customToc--sticky';
}
if ($highlight_active) {
    $classes .= ' customToc--highlight';
}
if ($show_numbers) {
    $classes .= ' customToc--numbered';
}
$classes .= $class_name;

// Don't render if no items
if (empty($items)) {
    return;
}

// Build data attributes
$data_attrs = 'data-highlight="' . ($highlight_active ? 'true' : 'false') . '"';
if ($hide_mobile) {
    $data_attrs .= ' data-hide-mobile="true"';
}
?>
<nav<?php echo $anchor; ?> class="<?php echo esc_attr($classes); ?>" <?php echo $data_attrs; ?>>
    <?php if ($title) : ?>
        <h4 class="customToc__title">
            <?php if ($show_eyebrow && $eyebrow_text) : ?>
                <span class="customToc__eyebrow"><?php echo esc_html($eyebrow_text); ?></span>
            <?php endif; ?>
            <span class="customToc__titleText"><?php echo esc_html($title); ?></span>
            <span class="customToc__toggleIcon" aria-hidden="true"></span>
        </h4>
    <?php endif; ?>
    
    <ul class="customToc__list">
        <?php foreach ($items as $index => $item) : 
            if (empty($item['label']) || empty($item['anchorId'])) continue;
        ?>
            <li class="customToc__item">
                <?php if ($show_numbers) : ?>
                    <span class="customToc__number"><?php echo esc_html($index + 1); ?></span>
                <?php endif; ?>
                <a href="#<?php echo esc_attr($item['anchorId']); ?>" class="customToc__link" data-anchor="<?php echo esc_attr($item['anchorId']); ?>">
                    <?php echo esc_html($item['label']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
