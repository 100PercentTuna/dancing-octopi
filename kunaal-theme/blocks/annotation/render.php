<?php
/**
 * Inline Annotation Block - Render
 */
$text = $attributes['text'] ?? '';
$note = $attributes['note'] ?? '';
$highlight_color = $attributes['highlightColor'] ?? 'yellow';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($text)) return;

$unique_id = 'annotation-' . wp_unique_id();
?>
<span class="wp-block-kunaal-annotation annotation annotation-<?php echo esc_attr($highlight_color) . $class_name; ?>" id="<?php echo esc_attr($unique_id); ?>">
    <mark class="annotation-text" tabindex="0" aria-describedby="<?php echo esc_attr($unique_id); ?>-note"><?php echo wp_kses_post($text); ?></mark>
    <?php if ($note) : ?>
        <span class="annotation-note" id="<?php echo esc_attr($unique_id); ?>-note" role="tooltip"><?php echo esc_html($note); ?></span>
    <?php endif; ?>
</span>

