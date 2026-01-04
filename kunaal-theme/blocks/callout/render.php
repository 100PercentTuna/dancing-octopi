<?php
/**
 * Callout Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$type = isset($attributes['type']) ? $attributes['type'] : 'info';
$title = isset($attributes['title']) ? $attributes['title'] : '';
$content = isset($attributes['content']) ? $attributes['content'] : '';
$show_icon = isset($attributes['showIcon']) ? $attributes['showIcon'] : true;
$collapsible = isset($attributes['collapsible']) ? $attributes['collapsible'] : false;
$default_open = isset($attributes['defaultOpen']) ? $attributes['defaultOpen'] : true;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($content)) {
    return;
}

// Icons for each type (using SVG for better rendering)
$icons = array(
    'info'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
    'warning' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>',
    'success' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>',
    'danger'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>',
    'note'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
    'tip'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18h6M10 22h4M12 2v1M12 22v-4a4 4 0 004-4 6 6 0 10-8 0 4 4 0 004 4z"/></svg>',
);

$icon_svg = isset($icons[$type]) ? $icons[$type] : $icons['info'];

// Default titles if none provided
$default_titles = array(
    'info'    => 'Information',
    'warning' => 'Warning',
    'success' => 'Success',
    'danger'  => 'Important',
    'note'    => 'Note',
    'tip'     => 'Tip',
);

$aria_label = !empty($title) ? $title : $default_titles[$type];

if ($collapsible) {
    // Collapsible version using <details>
    ?>
    <details<?php echo $anchor; ?> class="wp-block-kunaal-callout callout callout--<?php echo esc_attr($type); ?> callout--collapsible<?php echo $class_name; ?>"<?php echo $default_open ? ' open' : ''; ?>>
        <summary class="callout__header">
            <?php if ($show_icon) { ?>
                <span class="callout__icon" aria-hidden="true"><?php echo $icon_svg; ?></span>
            <?php } ?>
            <span class="callout__title"><?php echo esc_html(!empty($title) ? $title : $default_titles[$type]); ?></span>
            <span class="callout__toggle" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </span>
        </summary>
        <div class="callout__content">
            <?php echo wp_kses_post($content); ?>
        </div>
    </details>
    <?php
} else {
    // Standard version
    ?>
    <div<?php echo $anchor; ?> class="wp-block-kunaal-callout callout callout--<?php echo esc_attr($type); ?><?php echo $class_name; ?>" role="note" aria-label="<?php echo esc_attr($aria_label); ?>">
        <?php if ($show_icon || !empty($title)) { ?>
            <div class="callout__header">
                <?php if ($show_icon) { ?>
                    <span class="callout__icon" aria-hidden="true"><?php echo $icon_svg; ?></span>
                <?php } ?>
                <?php if (!empty($title)) { ?>
                    <span class="callout__title"><?php echo esc_html($title); ?></span>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="callout__content">
            <?php echo wp_kses_post($content); ?>
        </div>
    </div>
    <?php
}

