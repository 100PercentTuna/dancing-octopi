<?php
/**
 * Rubric Row Block - Render
 */
$criteria = $attributes['criteria'] ?? '';
$levels = $attributes['levels'] ?? ['', '', '', ''];
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($criteria)) return;
?>
<tr class="wp-block-kunaal-rubric-row rubric-row<?php echo $class_name; ?>">
    <td class="rubric-criteria"><?php echo wp_kses_post($criteria); ?></td>
    <?php foreach ($levels as $level) : ?>
        <td class="rubric-level-cell"><?php echo wp_kses_post($level); ?></td>
    <?php endforeach; ?>
</tr>

