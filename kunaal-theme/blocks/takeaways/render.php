<?php
/**
 * Takeaways Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$title = isset($attributes['title']) ? $attributes['title'] : 'Takeaways';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-takeaways takeaways reveal<?php echo $class_name; ?>">
    <h2><?php echo esc_html($title); ?></h2>
    <ol class="takeList">
        <?php echo $content; ?>
    </ol>
</div>

