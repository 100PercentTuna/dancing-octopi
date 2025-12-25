<?php
/**
 * Footnote Block - Server-side rendering
 * Renders inline reference; footnote content is collected and rendered by footnotes-section
 *
 * @package Kunaal_Theme
 */

$content = isset($attributes['content']) ? $attributes['content'] : '';
$footnote_id = isset($attributes['footnoteId']) ? $attributes['footnoteId'] : 'fn-' . wp_unique_id();
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($content)) {
    return; // Don't render empty footnotes
}

// Store footnote content in global array for later rendering
global $kunaal_footnotes;
if (!is_array($kunaal_footnotes)) {
    $kunaal_footnotes = array();
}

$footnote_number = count($kunaal_footnotes) + 1;
$kunaal_footnotes[] = array(
    'id' => $footnote_id,
    'number' => $footnote_number,
    'content' => $content,
);

// Render inline reference
?>
<sup class="footnote-ref<?php echo $class_name; ?>">
    <a 
        href="#<?php echo esc_attr($footnote_id); ?>" 
        id="<?php echo esc_attr($footnote_id); ?>-ref"
        aria-describedby="<?php echo esc_attr($footnote_id); ?>"
        data-footnote-ref="<?php echo esc_attr($footnote_number); ?>"
    ><?php echo esc_html($footnote_number); ?></a>
</sup>

