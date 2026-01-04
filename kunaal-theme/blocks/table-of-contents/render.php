<?php
/**
 * Table of Contents Block - Server-side rendering
 * Extracts headings from post content and generates navigation
 *
 * @package Kunaal_Theme
 */

$title = isset($attributes['title']) ? $attributes['title'] : __('On this page', 'kunaal-theme');
$heading_levels = isset($attributes['headingLevels']) ? $attributes['headingLevels'] : array('h2', 'h3');
$style = isset($attributes['style']) ? $attributes['style'] : 'numbered';
$collapsible = isset($attributes['collapsible']) ? $attributes['collapsible'] : true;
$default_open = isset($attributes['defaultOpen']) ? $attributes['defaultOpen'] : true;
$highlight_active = isset($attributes['highlightActive']) ? $attributes['highlightActive'] : true;
$smooth_scroll = isset($attributes['smoothScroll']) ? $attributes['smoothScroll'] : true;
$sticky = isset($attributes['sticky']) ? $attributes['sticky'] : false;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Get the current post content
global $post;
if (!$post) {
    return;
}

$content = $post->post_content;

// Parse headings from content
$headings = array();
$heading_pattern = '/<(h[2-6])([^>]*)>(.*?)<\/\1>/is';

if (preg_match_all($heading_pattern, $content, $matches, PREG_SET_ORDER)) {
    $counter = 0;
    foreach ($matches as $match) {
        $tag = strtolower($match[1]);
        
        // Only include specified heading levels
        if (!in_array($tag, $heading_levels, true)) {
            continue;
        }
        
        $attrs = $match[2];
        $text = wp_strip_all_tags($match[3]);
        
        if (empty($text)) {
            continue;
        }
        
        // Extract existing ID or generate one
        $id = '';
        if (preg_match('/id=["\']([^"\']+)["\']/i', $attrs, $id_match)) {
            $id = $id_match[1];
        } else {
            // Generate ID from text (slugify)
            $id = 'heading-' . sanitize_title($text);
        }
        
        $level = (int) substr($tag, 1);
        
        $headings[] = array(
            'id'    => $id,
            'text'  => $text,
            'level' => $level,
        );
        
        $counter++;
    }
}

// Also check for section-header blocks
if (preg_match_all('/<!-- wp:kunaal\/section-header\s+({[^}]+})\s*-->/s', $content, $block_matches, PREG_SET_ORDER)) {
    foreach ($block_matches as $block_match) {
        $attrs_json = $block_match[1];
        $block_attrs = json_decode($attrs_json, true);
        
        if (!empty($block_attrs['title']) && in_array('h2', $heading_levels, true)) {
            $id = !empty($block_attrs['anchor']) ? $block_attrs['anchor'] : 'heading-' . sanitize_title($block_attrs['title']);
            
            $headings[] = array(
                'id'    => $id,
                'text'  => $block_attrs['title'],
                'level' => 2,
            );
        }
    }
}

// Sort headings by their position in content (approximate by finding their position)
// This is a simplified approach; for perfect ordering, would need DOM parsing

if (empty($headings)) {
    return; // Don't render if no headings found
}

// Build data attributes for JS
$data_attrs = array(
    'data-toc-highlight="' . ($highlight_active ? 'true' : 'false') . '"',
    'data-toc-smooth="' . ($smooth_scroll ? 'true' : 'false') . '"',
);

$sticky_class = $sticky ? ' toc--sticky' : '';
$list_tag = $style === 'numbered' ? 'ol' : 'ul';
?>

<?php if ($collapsible) { ?>
<details<?php echo $anchor; ?> class="wp-block-kunaal-table-of-contents toc toc--<?php echo esc_attr($style); ?> toc--collapsible<?php echo $sticky_class . $class_name; ?>"<?php echo $default_open ? ' open' : ''; ?> <?php echo implode(' ', $data_attrs); ?>>
    <summary class="toc__header">
        <?php if (!empty($title)) { ?>
            <h4 class="toc__title"><?php echo esc_html($title); ?></h4>
        <?php } ?>
        <span class="toc__toggle" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
        </span>
    </summary>
    <<?php echo $list_tag; ?> class="toc__list" role="list">
        <?php foreach ($headings as $heading) { ?>
            <li class="toc__item toc__item--level-<?php echo esc_attr($heading['level']); ?>">
                <a href="#<?php echo esc_attr($heading['id']); ?>" class="toc__link">
                    <?php echo esc_html($heading['text']); ?>
                </a>
            </li>
        <?php } ?>
    </<?php echo $list_tag; ?>>
</details>
<?php } else { ?>
<nav<?php echo $anchor; ?> class="wp-block-kunaal-table-of-contents toc toc--<?php echo esc_attr($style); ?><?php echo $sticky_class . $class_name; ?>" aria-label="<?php esc_attr_e('Table of contents', 'kunaal-theme'); ?>" <?php echo implode(' ', $data_attrs); ?>>
    <?php if (!empty($title)) { ?>
        <h4 class="toc__title"><?php echo esc_html($title); ?></h4>
    <?php } ?>
    <<?php echo $list_tag; ?> class="toc__list" role="list">
        <?php foreach ($headings as $heading) { ?>
            <li class="toc__item toc__item--level-<?php echo esc_attr($heading['level']); ?>">
                <a href="#<?php echo esc_attr($heading['id']); ?>" class="toc__link">
                    <?php echo esc_html($heading['text']); ?>
                </a>
            </li>
        <?php } ?>
    </<?php echo $list_tag; ?>>
</nav>
<?php } ?>

