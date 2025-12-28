<?php
/**
 * Primary Source Excerpt Block - Render
 */
$excerpt_content = $attributes['content'] ?? '';
$source = $attributes['source'] ?? '';
$source_type = $attributes['sourceType'] ?? 'document';
$date = $attributes['date'] ?? '';
$source_url = $attributes['sourceUrl'] ?? '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($excerpt_content)) {
    return;
}
?>
<blockquote<?php echo $anchor; ?> class="wp-block-kunaal-source-excerpt source-excerpt source-<?php echo esc_attr($source_type) . $class_name; ?>">
    <div class="source-type-label"><?php echo esc_html(strtoupper($source_type)); ?></div>
    <div class="source-content"><?php echo wp_kses_post($excerpt_content); ?></div>
    <?php if ($source || $date) : ?>
        <footer class="source-attribution">
            <?php if ($source) : ?>
                <?php if ($source_url) : ?>
                    <cite><a href="<?php echo esc_url($source_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($source); ?></a></cite>
                <?php else : ?>
                    <cite><?php echo esc_html($source); ?></cite>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($date) : ?>
                <span class="source-date"><?php echo esc_html($date); ?></span>
            <?php endif; ?>
        </footer>
    <?php endif; ?>
</blockquote>

