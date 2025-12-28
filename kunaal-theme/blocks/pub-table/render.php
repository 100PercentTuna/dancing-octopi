<?php
/**
 * Publication Table Block - Render
 */
$title = $attributes['title'] ?? '';
$source = $attributes['source'] ?? '';
$caption = $attributes['caption'] ?? '';
$headers = $attributes['headers'] ?? [];
$rows = $attributes['rows'] ?? [];
$highlight_first = $attributes['highlightFirst'] ?? true;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
$highlight_class = $highlight_first ? ' highlight-first' : '';

if (empty($rows)) {
    return;
}
?>
<figure<?php echo $anchor; ?> class="wp-block-kunaal-pub-table pub-table<?php echo $highlight_class . $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="pt-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    
    <div class="pt-wrapper">
        <table class="pt-table">
            <thead>
                <tr>
                    <?php foreach ($headers as $index => $header) : ?>
                        <th scope="col"><?php echo esc_html($header); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row) : ?>
                    <tr>
                        <?php foreach ($row as $cell) : ?>
                            <td><?php echo wp_kses_post($cell); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($caption || $source) : ?>
        <figcaption class="pt-caption">
            <?php if ($caption) : ?>
                <span class="pt-caption-text"><?php echo wp_kses_post($caption); ?></span>
            <?php endif; ?>
            <?php if ($source) : ?>
                <span class="pt-source">Source: <?php echo esc_html($source); ?></span>
            <?php endif; ?>
        </figcaption>
    <?php endif; ?>
</figure>

