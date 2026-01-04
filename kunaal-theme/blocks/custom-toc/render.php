<?php
/**
 * Custom TOC Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$title = isset($attributes['title']) ? $attributes['title'] : 'Contents';
$items = isset($attributes['items']) ? $attributes['items'] : array();
$sticky = isset($attributes['sticky']) ? $attributes['sticky'] : true;
$highlight_active = isset($attributes['highlightActive']) ? $attributes['highlightActive'] : true;
$show_numbers = isset($attributes['showNumbers']) ? $attributes['showNumbers'] : false;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

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
?>
<nav<?php echo $anchor; ?> class="<?php echo esc_attr($classes); ?>" data-highlight="<?php echo $highlight_active ? 'true' : 'false'; ?>">
    <?php if ($title) : ?>
        <h4 class="customToc__title"><?php echo esc_html($title); ?></h4>
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

