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
 *     @type string $title     Section title (required)
 *     @type string $count     Optional count display (e.g., "6 essays")
 *     @type string $more_url  Optional URL for "more" link
 *     @type string $more_text Optional text for "more" link (default: "more →")
 *     @type string $id        Optional section ID for anchor links
 *     @type string $class     Optional additional CSS classes
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get args with defaults
$title     = $args['title'] ?? '';
$count     = $args['count'] ?? '';
$more_url  = $args['more_url'] ?? '';
$more_text = $args['more_text'] ?? __('more', 'kunaal-theme') . ' &rarr;';
$id        = $args['id'] ?? '';
$class     = $args['class'] ?? '';

if (empty($title)) {
    return;
}

$section_classes = 'sectionHead';
if (!empty($class)) {
    $section_classes .= ' ' . esc_attr($class);
}
?>
<div class="<?php echo esc_attr($section_classes); ?>"<?php echo $id ? ' id="' . esc_attr($id) . '"' : ''; ?>>
    <h2 class="u-section-underline"><?php echo esc_html($title); ?></h2>
    <?php if (!empty($count) || !empty($more_url)) : ?>
    <span class="sectionCount">
        <?php if (!empty($count)) : ?>
            <?php echo esc_html($count); ?>
        <?php endif; ?>
        <?php if (!empty($more_url)) : ?>
            <?php if (!empty($count)) : ?>&nbsp;&middot;&nbsp;<?php endif; ?>
            <a href="<?php echo esc_url($more_url); ?>" class="u-underline-double"><?php echo wp_kses_post($more_text); ?></a>
        <?php endif; ?>
    </span>
    <?php endif; ?>
</div>

