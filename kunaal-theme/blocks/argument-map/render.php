<?php
/**
 * Argument Map Block - Render
 */
$claim = $attributes['claim'] ?? '';
$supporting = $attributes['supporting'] ?? [];
$opposing = $attributes['opposing'] ?? [];
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($claim) && empty($supporting) && empty($opposing)) return;
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-argument-map argument-map<?php echo $class_name; ?>">
    <?php if ($claim) : ?>
        <div class="argument-claim">
            <span class="argument-label">Main Claim</span>
            <p class="claim-text"><?php echo wp_kses_post($claim); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="argument-columns">
        <?php if (!empty($supporting)) : ?>
            <div class="argument-column supporting">
                <h4 class="column-title">Supporting Evidence</h4>
                <ul class="argument-list">
                    <?php foreach ($supporting as $item) : ?>
                        <?php if ($item) : ?>
                            <li><?php echo wp_kses_post($item); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($opposing)) : ?>
            <div class="argument-column opposing">
                <h4 class="column-title">Counter-Arguments</h4>
                <ul class="argument-list">
                    <?php foreach ($opposing as $item) : ?>
                        <?php if ($item) : ?>
                            <li><?php echo wp_kses_post($item); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

