<?php
/**
 * Scenario Comparison Block - Render
 */
$title = $attributes['title'] ?? '';
$scenarios = $attributes['scenarios'] ?? [];
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($scenarios)) return;

$probability_labels = ['high' => 'High Likelihood', 'medium' => 'Medium', 'low' => 'Low Likelihood'];
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-scenario-compare scenario-compare<?php echo $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="sc-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    <div class="sc-grid" style="grid-template-columns: repeat(<?php echo esc_attr(count($scenarios)); ?>, 1fr);">
        <?php foreach ($scenarios as $scenario) : ?>
            <div class="sc-card sc-<?php echo esc_attr($scenario['probability'] ?? 'medium'); ?>">
                <div class="sc-card-header">
                    <span class="sc-name"><?php echo esc_html($scenario['name'] ?? ''); ?></span>
                    <span class="sc-probability-badge"><?php echo esc_html($probability_labels[$scenario['probability'] ?? 'medium']); ?></span>
                </div>
                <?php if (!empty($scenario['description'])) : ?>
                    <p class="sc-description"><?php echo wp_kses_post($scenario['description']); ?></p>
                <?php endif; ?>
                <?php if (!empty($scenario['outcome'])) : ?>
                    <div class="sc-outcome-section">
                        <span class="sc-outcome-label">Outcome</span>
                        <p class="sc-outcome"><?php echo wp_kses_post($scenario['outcome']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

