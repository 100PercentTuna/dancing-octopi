<?php
/**
 * Glossary Term Block - Render
 */
$term = $attributes['term'] ?? '';
$definition = $attributes['definition'] ?? '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($term) && empty($definition)) return;
?>
<div class="wp-block-kunaal-glossary-term glossary-term<?php echo $class_name; ?>">
    <dt class="glossary-term-title"><?php echo esc_html($term); ?></dt>
    <dd class="glossary-term-definition"><?php echo wp_kses_post($definition); ?></dd>
</div>

