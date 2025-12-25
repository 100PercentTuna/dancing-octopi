<?php
/**
 * Accordion Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$summary = isset($attributes['summary']) ? $attributes['summary'] : 'Click to expand';
$start_open = isset($attributes['startOpen']) && $attributes['startOpen'] ? ' open' : '';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($summary)) {
    $summary = 'Click to expand';
}
?>
<details<?php echo $anchor; ?> class="wp-block-kunaal-accordion accordion reveal<?php echo $class_name; ?>"<?php echo $start_open; ?>>
    <summary>
        <span><?php echo esc_html($summary); ?></span>
        <span class="marker">+</span>
    </summary>
    <div class="accBody">
        <?php echo $content; ?>
    </div>
</details>
