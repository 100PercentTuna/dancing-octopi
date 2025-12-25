<?php
/**
 * Takeaways Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$title = isset($attributes['title']) ? $attributes['title'] : 'Key Takeaways';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : ' id="takeaways"';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';
?>
<section<?php echo $anchor; ?> class="wp-block-kunaal-takeaways<?php echo $class_name; ?>">
    <h2><?php echo esc_html($title); ?></h2>
    <ol class="takeaways-list">
        <?php echo $content; ?>
    </ol>
</section>
