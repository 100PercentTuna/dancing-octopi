<?php
/**
 * Debate Block - Render
 */
$title = $attributes['title'] ?? '';
$question = $attributes['question'] ?? '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-debate debate<?php echo $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="debate-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    <?php if ($question) : ?>
        <p class="debate-question"><?php echo wp_kses_post($question); ?></p>
    <?php endif; ?>
    <div class="debate-sides">
        <?php echo $content; ?>
    </div>
</div>

