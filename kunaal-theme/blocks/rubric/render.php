<?php
/**
 * Evaluation Rubric Block - Render
 */
$title = $attributes['title'] ?? 'Evaluation Criteria';
$columns = $attributes['columns'] ?? ['Poor', 'Fair', 'Good', 'Excellent'];
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-rubric rubric<?php echo $class_name; ?>">
    <h3 class="rubric-title"><?php echo esc_html($title); ?></h3>
    <div class="rubric-table-wrapper">
        <table class="rubric-table">
            <thead>
                <tr>
                    <th scope="col" class="rubric-criteria-header">Criteria</th>
                    <?php foreach ($columns as $index => $col) : ?>
                        <th scope="col" class="rubric-level rubric-level-<?php echo $index; ?>"><?php echo esc_html($col); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php echo $content; ?>
            </tbody>
        </table>
    </div>
</div>

