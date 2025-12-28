<?php
/**
 * Assumptions Register Block - Render
 */
$title = $attributes['title'] ?? 'Key Assumptions';
$assumptions = $attributes['assumptions'] ?? [];
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($assumptions)) return;

$confidence_labels = ['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
$status_labels = ['untested' => 'Untested', 'validated' => 'Validated', 'invalidated' => 'Invalidated', 'partial' => 'Partial'];
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-assumptions-register assumptions-register<?php echo $class_name; ?>">
    <h3 class="ar-title"><?php echo esc_html($title); ?></h3>
    <table class="ar-table">
        <thead>
            <tr>
                <th scope="col">Assumption</th>
                <th scope="col">Confidence</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($assumptions as $item) : ?>
                <?php if (!empty($item['text'])) : ?>
                    <tr class="ar-row status-<?php echo esc_attr($item['status'] ?? 'untested'); ?>">
                        <td class="ar-assumption"><?php echo wp_kses_post($item['text']); ?></td>
                        <td class="ar-confidence confidence-<?php echo esc_attr($item['confidence'] ?? 'medium'); ?>">
                            <span class="confidence-indicator"></span>
                            <?php echo esc_html($confidence_labels[$item['confidence'] ?? 'medium']); ?>
                        </td>
                        <td class="ar-status">
                            <span class="status-badge status-<?php echo esc_attr($item['status'] ?? 'untested'); ?>">
                                <?php echo esc_html($status_labels[$item['status'] ?? 'untested']); ?>
                            </span>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

