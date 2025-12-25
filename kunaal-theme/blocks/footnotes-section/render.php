<?php
/**
 * Footnotes Section Block - Server-side rendering
 * Renders all collected footnotes
 *
 * @package Kunaal_Theme
 */

global $kunaal_footnotes;

$title = isset($attributes['title']) ? $attributes['title'] : 'Notes';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : ' id="footnotes"';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// If no footnotes, don't render anything
if (empty($kunaal_footnotes) || !is_array($kunaal_footnotes)) {
    return;
}
?>
<aside<?php echo $anchor; ?> class="footnotes-section<?php echo $class_name; ?>" aria-label="<?php echo esc_attr($title); ?>">
    <h4><?php echo esc_html($title); ?></h4>
    <ol class="footnotes-list">
        <?php foreach ($kunaal_footnotes as $footnote) : ?>
            <li id="<?php echo esc_attr($footnote['id']); ?>" class="footnote-item">
                <span class="footnote-number">
                    <a href="#<?php echo esc_attr($footnote['id']); ?>-ref" aria-label="Back to reference <?php echo esc_attr($footnote['number']); ?>">
                        <?php echo esc_html($footnote['number']); ?>.
                    </a>
                </span>
                <span class="footnote-content">
                    <?php echo wp_kses_post($footnote['content']); ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ol>
</aside>
<?php
// Clear footnotes after rendering
$kunaal_footnotes = array();

