<?php
/**
 * What We Know / Don't Know Block - Render
 */
$know_items = $attributes['knowItems'] ?? [];
$dont_know_items = $attributes['dontKnowItems'] ?? [];
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($know_items) && empty($dont_know_items)) {
    return;
}
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-know-dont-know know-dont-know<?php echo $class_name; ?>">
    <div class="kdk-columns">
        <div class="kdk-column know">
            <h4 class="kdk-title"><span class="kdk-icon">✓</span> What We Know</h4>
            <?php if (!empty($know_items)) : ?>
                <ul class="kdk-list">
                    <?php foreach ($know_items as $item) : ?>
                        <?php if ($item) : ?>
                            <li><?php echo wp_kses_post($item); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <div class="kdk-column dont-know">
            <h4 class="kdk-title"><span class="kdk-icon">?</span> What We Don't Know</h4>
            <?php if (!empty($dont_know_items)) : ?>
                <ul class="kdk-list">
                    <?php foreach ($dont_know_items as $item) : ?>
                        <?php if ($item) : ?>
                            <li><?php echo wp_kses_post($item); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

