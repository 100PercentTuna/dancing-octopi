<?php
/**
 * Debate Side Block - Render
 */
$position = $attributes['position'] ?? 'for';
$label = $attributes['label'] ?? 'For';
$argument = $attributes['argument'] ?? '';
$points = $attributes['points'] ?? [];
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div class="wp-block-kunaal-debate-side debate-side side-<?php echo esc_attr($position) . $class_name; ?>">
    <span class="ds-label"><?php echo esc_html($label); ?></span>
    <?php if ($argument) : ?>
        <p class="ds-argument"><?php echo wp_kses_post($argument); ?></p>
    <?php endif; ?>
    <?php if (!empty($points)) : ?>
        <ul class="ds-points">
            <?php foreach ($points as $point) : ?>
                <?php if ($point) : ?>
                    <li><?php echo wp_kses_post($point); ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

