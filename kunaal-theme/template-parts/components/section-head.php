<?php
/**
 * Component: Section Head
 *
 * Canonical section header markup with gray line + blue segment rule.
 * Use this component for ALL section headers site-wide.
 *
 * @package Kunaal_Theme
 * @since 4.31.0
 *
 * @param array $args {
 *     @type string $title      Section title (required)
 *     @type int|string $count  Optional count number (e.g., 6)
 *     @type string $count_label Optional count label (e.g., "essays")
 *     @type string $count_id   Optional ID for count element (e.g., "essayCountShown")
 *     @type string $label_id   Optional ID for label element (e.g., "essayLabel")
 *     @type string $more_link  Optional URL for "more" link
 *     @type string $more_text  Optional text for "more" link (default: "more →")
 *     @type string $id         Optional section ID for anchor links
 *     @type string $class      Optional additional CSS classes
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get args with defaults
$title      = $args['title'] ?? '';
$count      = $args['count'] ?? '';
$count_label = $args['count_label'] ?? '';
$count_id   = $args['count_id'] ?? '';
$label_id   = $args['label_id'] ?? '';
$more_link  = $args['more_link'] ?? '';
$more_text  = $args['more_text'] ?? __('more', 'kunaal-theme') . ' &rarr;';
$id         = $args['id'] ?? '';
$class      = $args['class'] ?? '';

if (empty($title)) {
    return;
}

$section_classes = 'sectionHead';
if (!empty($class)) {
    $section_classes .= ' ' . esc_attr($class);
}

$has_count = !empty($count) || !empty($count_label);
$has_more = !empty($more_link);
?>
<div class="<?php echo esc_attr($section_classes); ?>"<?php echo $id ? ' id="' . esc_attr($id) . '"' : ''; ?>>
    <h2 class="u-section-underline"><?php echo esc_html($title); ?></h2>
    <?php if ($has_count || $has_more) : ?>
    <span class="sectionCount">
        <?php if ($has_count) : ?>
            <?php if ($count_id) : ?>
                <span id="<?php echo esc_attr($count_id); ?>"><?php echo esc_html($count); ?></span>
            <?php else : ?>
                <?php echo esc_html($count); ?>
            <?php endif; ?>
            <?php if ($label_id) : ?>
                <span id="<?php echo esc_attr($label_id); ?>"><?php echo esc_html($count_label); ?></span>
            <?php elseif ($count_label) : ?>
                <?php echo esc_html($count_label); ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($has_more) : ?>
            <?php if ($has_count) : ?>&nbsp;&middot;&nbsp;<?php endif; ?>
            <a href="<?php echo esc_url($more_link); ?>" class="u-underline-double"><?php echo wp_kses_post($more_text); ?></a>
        <?php endif; ?>
    </span>
    <?php endif; ?>
</div>

