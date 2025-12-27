<?php
/**
 * Flow Diagram Block - Frontend Render
 */
if (!defined('ABSPATH')) exit;

$title = $attributes['title'] ?? '';
$subtitle = $attributes['subtitle'] ?? '';
$diagram_type = $attributes['diagramType'] ?? 'sankey';
$node_width = intval($attributes['nodeWidth'] ?? 20);
$node_padding = intval($attributes['nodePadding'] ?? 8);
$link_color_mode = $attributes['linkColorMode'] ?? 'source';
$single_link_color = $attributes['singleLinkColor'] ?? '#B8A99A';
$show_values = $attributes['showValues'] ?? true;
$value_format = $attributes['valueFormat'] ?? 'number';
$currency_symbol = $attributes['currencySymbol'] ?? '$';
$value_unit = $attributes['valueUnit'] ?? '';
$column_labels = $attributes['columnLabels'] ?? [];
$source_note = $attributes['sourceNote'] ?? '';
$nodes = $attributes['nodes'] ?? [];
$links = $attributes['links'] ?? [];

$block_id = 'flow-' . wp_unique_id();

// Helper functions are defined in inc/block-helpers.php
?>

<figure class="wp-block-kunaal-flow-diagram flow-diagram--<?php echo esc_attr($diagram_type); ?>"
        role="img"
        aria-labelledby="<?php echo esc_attr($block_id); ?>-title"
        data-lazy-block="flow-diagram"
        data-diagram-type="<?php echo esc_attr($diagram_type); ?>"
        data-nodes='<?php echo esc_attr(wp_json_encode($nodes)); ?>'
        data-links='<?php echo esc_attr(wp_json_encode($links)); ?>'
        data-node-width="<?php echo esc_attr($node_width); ?>"
        data-node-padding="<?php echo esc_attr($node_padding); ?>"
        data-link-color-mode="<?php echo esc_attr($link_color_mode); ?>"
        data-single-link-color="<?php echo esc_attr($single_link_color); ?>"
        data-show-values="<?php echo $show_values ? 'true' : 'false'; ?>"
        data-value-format="<?php echo esc_attr($value_format); ?>"
        data-currency-symbol="<?php echo esc_attr($currency_symbol); ?>"
        data-value-unit="<?php echo esc_attr($value_unit); ?>">
    
    <?php if ($title || $subtitle) : ?>
    <header class="flow-header">
        <?php if ($title) : ?>
        <h3 id="<?php echo esc_attr($block_id); ?>-title" class="flow-title"><?php echo esc_html($title); ?></h3>
        <?php endif; ?>
        <?php if ($subtitle) : ?>
        <p class="flow-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
    </header>
    <?php endif; ?>
    
    <div class="flow-chart" style="min-height: 300px;">
        <svg class="flow-svg" viewBox="0 0 800 500" preserveAspectRatio="xMidYMid meet">
            <!-- Will be populated by JavaScript -->
            <text x="400" y="250" text-anchor="middle" fill="var(--muted)" font-family="var(--sans)" font-size="14px">
                Loading diagram...
            </text>
        </svg>
    </div>
    
    <?php if ($source_note) : ?>
    <footer class="flow-footer">
        <p class="flow-source"><?php echo esc_html($source_note); ?></p>
    </footer>
    <?php endif; ?>
    
    <details class="flow-data-table">
        <summary><?php esc_html_e('View flow data', 'kunaal-theme'); ?></summary>
        <table>
            <caption><?php echo esc_html($title ?: 'Flow diagram data'); ?></caption>
            <thead>
                <tr><th><?php esc_html_e('From', 'kunaal-theme'); ?></th><th><?php esc_html_e('To', 'kunaal-theme'); ?></th><th><?php esc_html_e('Value', 'kunaal-theme'); ?></th></tr>
            </thead>
            <tbody>
                <?php
                $node_map = [];
                foreach ($nodes as $node) {
                    $node_map[$node['id']] = $node['label'] ?? $node['id'];
                }
                foreach ($links as $link) :
                ?>
                <tr>
                    <td><?php echo esc_html($node_map[$link['source']] ?? $link['source']); ?></td>
                    <td><?php echo esc_html($node_map[$link['target']] ?? $link['target']); ?></td>
                    <td><?php echo esc_html(kunaal_format_flow_value($link['value'] ?? 0, $value_format, $currency_symbol, $value_unit)); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </details>
</figure>

