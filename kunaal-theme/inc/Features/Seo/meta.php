<?php
/**
 * Meta tags (theme-owned SEO).
 *
 * Outputs meta description + canonical link. Avoids duplication with Yoast.
 *
 * @package Kunaal_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function kunaal_seo_print_meta_tags(): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    if (kunaal_seo_is_yoast_active()) {
        return;
    }

    $description = trim(kunaal_seo_get_description());
    $canonical = kunaal_seo_get_canonical_url();

    if ($description !== '') {
        echo "<meta name=\"description\" content=\"" . esc_attr($description) . "\" />\n";
    }

    if ($canonical !== '') {
        echo "<link rel=\"canonical\" href=\"" . esc_url($canonical) . "\" />\n";
    }
}
add_action('wp_head', 'kunaal_seo_print_meta_tags', 4);

function kunaal_seo_remove_core_canonical(): void {
    if (kunaal_seo_is_yoast_active()) {
        return;
    }
    remove_action('wp_head', 'rel_canonical');
}
add_action('wp', 'kunaal_seo_remove_core_canonical');

/**
 * Optional: allow per-post SEO title to control <title> (no change unless set).
 *
 * @param array<string, string> $parts
 * @return array<string, string>
 */
function kunaal_seo_filter_document_title_parts(array $parts): array {
    if (kunaal_seo_is_yoast_active()) {
        return $parts;
    }
    if (!is_singular()) {
        return $parts;
    }
    $post_id = get_queried_object_id();
    $custom = trim((string) get_post_meta($post_id, 'kunaal_seo_title', true));
    if ($custom === '') {
        return $parts;
    }
    $parts['title'] = $custom;
    return $parts;
}
add_filter('document_title_parts', 'kunaal_seo_filter_document_title_parts');
