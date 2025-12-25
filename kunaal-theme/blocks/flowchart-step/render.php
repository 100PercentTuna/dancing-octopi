<?php
/**
 * Flow Chart Step Block - Render
 */
$label = $attributes['label'] ?? '';
$description = $attributes['description'] ?? '';
$step_type = $attributes['stepType'] ?? 'process';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($label)) return;
?>
<div class="wp-block-kunaal-flowchart-step flowchart-step step-<?php echo esc_attr($step_type) . $class_name; ?>">
    <div class="fcs-box">
        <span class="fcs-label"><?php echo esc_html($label); ?></span>
        <?php if ($description) : ?>
            <p class="fcs-description"><?php echo wp_kses_post($description); ?></p>
        <?php endif; ?>
    </div>
    <div class="fcs-arrow">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M13 5l7 7-7 7"/>
        </svg>
    </div>
</div>

