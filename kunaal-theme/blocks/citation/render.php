<?php
/**
 * Citation Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$quote = isset($attributes['quote']) ? $attributes['quote'] : '';
$author = isset($attributes['author']) ? $attributes['author'] : '';
$source_text = isset($attributes['sourceText']) ? $attributes['sourceText'] : '';
$source_url = isset($attributes['sourceUrl']) ? $attributes['sourceUrl'] : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($quote)) {
    return; // Don't render empty citations
}
?>
<blockquote<?php echo $anchor; ?> class="wp-block-kunaal-citation<?php echo $class_name; ?>">
    <p class="citation-quote"><?php echo wp_kses_post($quote); ?></p>
    <?php if ($author || $source_text) : ?>
        <footer class="citation-footer">
            <?php if ($author) : ?>
                <cite class="citation-author"><?php echo esc_html($author); ?></cite>
            <?php endif; ?>
            <?php if ($source_text) : ?>
                <span class="citation-source">
                    <?php if ($source_url) : ?>
                        <a href="<?php echo esc_url($source_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($source_text); ?></a>
                    <?php else : ?>
                        <?php echo esc_html($source_text); ?>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </footer>
    <?php endif; ?>
</blockquote>
