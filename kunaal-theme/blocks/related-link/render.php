<?php
/**
 * Related Link Block - Render
 */
$url = $attributes['url'] ?? '';
$link_title = $attributes['title'] ?? '';
$source = $attributes['source'] ?? '';
$description = $attributes['description'] ?? '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($link_title)) return;

$tag = $url ? 'a' : 'div';
$link_attrs = $url ? ' href="' . esc_url($url) . '" target="_blank" rel="noopener"' : '';
?>
<<?php echo $tag . $link_attrs; ?> class="wp-block-kunaal-related-link related-link<?php echo $class_name; ?>">
    <span class="related-link-arrow">→</span>
    <div class="related-link-content">
        <span class="related-link-title"><?php echo esc_html($link_title); ?></span>
        <?php if ($source) : ?>
            <span class="related-link-source"><?php echo esc_html($source); ?></span>
        <?php endif; ?>
        <?php if ($description) : ?>
            <p class="related-link-desc"><?php echo wp_kses_post($description); ?></p>
        <?php endif; ?>
    </div>
</<?php echo $tag; ?>>

