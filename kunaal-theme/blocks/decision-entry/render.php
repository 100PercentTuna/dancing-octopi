<?php
/**
 * Decision Entry Block - Render
 */
$decision = $attributes['decision'] ?? '';
$date = $attributes['date'] ?? '';
$rationale = $attributes['rationale'] ?? '';
$status = $attributes['status'] ?? 'active';
$outcome = $attributes['outcome'] ?? '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($decision)) return;

$status_labels = ['active' => 'Active', 'superseded' => 'Superseded', 'reversed' => 'Reversed'];
?>
<div class="wp-block-kunaal-decision-entry decision-entry de-<?php echo esc_attr($status) . $class_name; ?>">
    <div class="de-marker"></div>
    <div class="de-content">
        <div class="de-header">
            <h4 class="de-decision"><?php echo wp_kses_post($decision); ?></h4>
            <div class="de-meta">
                <?php if ($date) : ?>
                    <span class="de-date"><?php echo esc_html($date); ?></span>
                <?php endif; ?>
                <span class="de-status"><?php echo esc_html($status_labels[$status]); ?></span>
            </div>
        </div>
        <?php if ($rationale) : ?>
            <p class="de-rationale"><?php echo wp_kses_post($rationale); ?></p>
        <?php endif; ?>
        <?php if ($outcome) : ?>
            <div class="de-outcome">
                <span class="de-outcome-label">Outcome:</span>
                <?php echo wp_kses_post($outcome); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

