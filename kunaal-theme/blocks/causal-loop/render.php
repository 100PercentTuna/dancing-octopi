<?php
/**
 * Causal Loop Diagram Block - Render
 */
$title = $attributes['title'] ?? '';
$nodes = $attributes['nodes'] ?? [];
$description = $attributes['description'] ?? '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($nodes)) return;

// Determine if it's a reinforcing or balancing loop
$negative_count = 0;
foreach ($nodes as $node) {
    if (($node['effect'] ?? 'positive') === 'negative') {
        $negative_count++;
    }
}
$loop_type = ($negative_count % 2 === 0) ? 'reinforcing' : 'balancing';
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-causal-loop causal-loop loop-<?php echo esc_attr($loop_type) . $class_name; ?>">
    <?php if ($title) : ?>
        <h3 class="cl-title"><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    
    <div class="cl-diagram">
        <div class="cl-loop-indicator">
            <span class="cl-loop-type"><?php echo $loop_type === 'reinforcing' ? 'R' : 'B'; ?></span>
            <span class="cl-loop-label"><?php echo $loop_type === 'reinforcing' ? 'Reinforcing' : 'Balancing'; ?></span>
        </div>
        
        <div class="cl-nodes">
            <?php foreach ($nodes as $index => $node) : ?>
                <div class="cl-node">
                    <span class="cl-node-label"><?php echo esc_html($node['label'] ?? ''); ?></span>
                </div>
                <?php if ($index < count($nodes) - 1) : ?>
                    <div class="cl-arrow cl-<?php echo esc_attr($node['effect'] ?? 'positive'); ?>">
                        <span class="cl-effect"><?php echo ($node['effect'] ?? 'positive') === 'positive' ? '+' : '−'; ?></span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M13 5l7 7-7 7"/>
                        </svg>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <!-- Loop-back arrow -->
            <div class="cl-loop-back cl-<?php echo esc_attr($nodes[count($nodes)-1]['effect'] ?? 'positive'); ?>">
                <span class="cl-effect"><?php echo ($nodes[count($nodes)-1]['effect'] ?? 'positive') === 'positive' ? '+' : '−'; ?></span>
            </div>
        </div>
    </div>
    
    <?php if ($description) : ?>
        <p class="cl-description"><?php echo wp_kses_post($description); ?></p>
    <?php endif; ?>
</div>

