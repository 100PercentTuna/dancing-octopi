<?php
/**
 * Network Graph Block - Frontend Render
 */
if (!defined('ABSPATH')) {
    exit;
}

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$layout = $attributes['layout'] ?? 'force';
$show_labels = $attributes['showLabels'] ?? true;
$enable_zoom = $attributes['enableZoom'] ?? true;
$enable_drag = $attributes['enableDrag'] ?? true;
$enable_physics = $attributes['enablePhysics'] ?? true;
$charge_strength = intval($attributes['chargeStrength'] ?? -300);
$link_distance = intval($attributes['linkDistance'] ?? 100);
$color_by_group = $attributes['colorByGroup'] ?? true;
$group_colors = $attributes['groupColors'] ?? [];
$show_legend = $attributes['showLegend'] ?? true;
$height = intval($attributes['height'] ?? 500);
$source_note = $attributes['sourceNote'] ?? '';
$nodes = $attributes['nodes'] ?? [];
$edges = $attributes['edges'] ?? [];

$block_id = 'network-' . wp_unique_id();
?>

<figure class="wp-block-kunaal-network-graph"
        role="img"
        aria-labelledby="<?php echo esc_attr($block_id); ?>-title"
        data-lazy-block="network-graph"
        data-layout="<?php echo esc_attr($layout); ?>"
        data-nodes='<?php echo esc_attr(wp_json_encode($nodes)); ?>'
        data-edges='<?php echo esc_attr(wp_json_encode($edges)); ?>'
        data-show-labels="<?php echo $show_labels ? 'true' : 'false'; ?>"
        data-enable-zoom="<?php echo $enable_zoom ? 'true' : 'false'; ?>"
        data-enable-drag="<?php echo $enable_drag ? 'true' : 'false'; ?>"
        data-enable-physics="<?php echo $enable_physics ? 'true' : 'false'; ?>"
        data-charge-strength="<?php echo esc_attr($charge_strength); ?>"
        data-link-distance="<?php echo esc_attr($link_distance); ?>"
        data-color-by-group="<?php echo $color_by_group ? 'true' : 'false'; ?>"
        data-group-colors='<?php echo esc_attr(wp_json_encode($group_colors)); ?>'>
    
    <?php if ($title || $subtitle) : ?>
    <header class="network-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="network-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="network-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <div class="network-container" style="height: <?php echo esc_attr($height); ?>px;">
        <svg class="network-svg" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid meet">
            <text x="400" y="250" text-anchor="middle" fill="var(--muted)" font-family="var(--sans)" font-size="14px">
                Loading network...
            </text>
        </svg>
        
        <?php if ($enable_zoom) : ?>
        <div class="network-controls">
            <button class="network-zoom-in" aria-label="<?php esc_attr_e('Zoom in', 'kunaal-theme'); ?>">+</button>
            <button class="network-zoom-out" aria-label="<?php esc_attr_e('Zoom out', 'kunaal-theme'); ?>">−</button>
            <button class="network-reset" aria-label="<?php esc_attr_e('Reset view', 'kunaal-theme'); ?>">⟲</button>
        </div>
        <?php endif; ?>
        
        <div class="network-tooltip" role="tooltip" hidden>
            <h4 class="tooltip-title"></h4>
            <p class="tooltip-group"></p>
            <p class="tooltip-description"></p>
            <ul class="tooltip-connections"></ul>
        </div>
    </div>
    
    <?php if ($show_legend && $color_by_group) :
        $groups = array_unique(array_column($nodes, 'group'));
        $theme_colors = ['#7D6B5D', '#B8A99A', '#C9553D', '#8B7355', '#D4C4B5', '#6B5B4F', '#A08B7A'];
    ?>
    <footer class="network-footer">
        <div class="network-legend">
            <?php foreach ($groups as $i => $group) :
                if (empty($group)) {
                    continue;
                }
                $color = $group_colors[$group] ?? $theme_colors[$i % count($theme_colors)];
            ?>
            <span class="legend-item">
                <span class="legend-dot" style="background: <?php echo esc_attr($color); ?>"></span>
                <?php echo esc_html($group); ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php if ($source_note) : ?>
        <p class="network-source"><?php echo esc_html($source_note); ?></p>
        <?php endif; ?>
    </footer>
    <?php endif; ?>
    
    <details class="network-list-fallback">
        <summary><?php esc_html_e('View as list', 'kunaal-theme'); ?></summary>
        <ul>
            <?php
            $node_map = [];
            foreach ($nodes as $node) {
                $node_map[$node['id']] = $node;
            }
            foreach ($nodes as $node) :
                $connections = array_filter($edges, function($e) use ($node) {
                    return $e['source'] === $node['id'] || $e['target'] === $node['id'];
                });
            ?>
            <li>
                <strong><?php echo esc_html($node['label'] ?? $node['id']); ?></strong>
                <?php if (!empty($node['group'])) : ?>
                (<?php echo esc_html($node['group']); ?>)
                <?php endif; ?>
                <?php if (!empty($connections)) : ?>
                <ul>
                    <?php foreach ($connections as $edge) :
                        $other_id = $edge['source'] === $node['id'] ? $edge['target'] : $edge['source'];
                        $other_node = $node_map[$other_id] ?? null;
                    ?>
                    <li>
                        <?php esc_html_e('Connected to:', 'kunaal-theme'); ?>
                        <?php echo esc_html($other_node['label'] ?? $other_id); ?>
                        <?php if (!empty($edge['label'])) : ?>
                        (<?php echo esc_html($edge['label']); ?>)
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </details>
</figure>

