<?php
/**
 * Small Multiples Block - Frontend Render
 */
if (!defined('ABSPATH')) exit;

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$chart_type = $attributes['chartType'] ?? 'line';
$columns = intval($attributes['columns'] ?? 4);
$aspect_ratio = $attributes['cellAspectRatio'] ?? '4:3';
$show_axes = $attributes['showAxes'] ?? false;
$shared_y = $attributes['sharedYScale'] ?? true;
$source_note = $attributes['sourceNote'] ?? '';
$data_rows = $attributes['dataRows'] ?? [];
$x_labels = $attributes['xLabels'] ?? [];

$block_id = 'sm-' . wp_unique_id();
?>

<figure class="wp-block-kunaal-small-multiples" role="img" aria-labelledby="<?php echo esc_attr($block_id); ?>-title">
    <?php if ($title || $subtitle) : ?>
    <header class="small-multiples-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="small-multiples-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="small-multiples-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <ul class="small-multiples-grid" style="--columns: <?php echo esc_attr($columns); ?>; --aspect-ratio: <?php echo esc_attr($aspect_ratio); ?>;">
        <?php foreach ($data_rows as $i => $row) :
            $label = $row['label'] ?? '';
            $values = $row['values'] ?? [];
        ?>
        <li class="small-multiples-cell" aria-label="<?php echo esc_attr($label); ?>">
            <div class="cell-chart" data-chart-type="<?php echo esc_attr($chart_type); ?>" data-values="<?php echo esc_attr(implode(',', $values)); ?>">
                <canvas></canvas>
            </div>
            <p class="cell-label"><?php echo esc_html($label); ?></p>
        </li>
        <?php endforeach; ?>
    </ul>
    
    <?php if ($source_note) : ?>
    <footer class="small-multiples-footer">
        <p class="small-multiples-source"><?php echo esc_html($source_note); ?></p>
    </footer>
    <?php endif; ?>
</figure>

