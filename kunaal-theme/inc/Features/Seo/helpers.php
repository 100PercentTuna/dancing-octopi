<?php
/**
 * SEO Helpers (Theme-Owned)
 *
 * Centralizes SEO-derived values (title/description/image/canonical) and guards
 * against duplication when an SEO plugin (Yoast) is active.
 *
 * @package Kunaal_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Determine if Yoast SEO is active (theme must not duplicate tags).
 */
function kunaal_seo_is_yoast_active(): bool {
    return defined('WPSEO_VERSION') || class_exists('WPSEO_Frontend');
}

/**
 * Get theme SEO settings (Settings API).
 *
 * @return array<string, mixed>
 */
function kunaal_seo_get_settings(): array {
    $raw = get_option('kunaal_seo_settings', array());
    return is_array($raw) ? $raw : array();
}

/**
 * Get a setting with fallback.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function kunaal_seo_setting(string $key, $default = null) {
    $settings = kunaal_seo_get_settings();
    return array_key_exists($key, $settings) ? $settings[$key] : $default;
}

/**
 * Build the SEO title (for <title>, OG, schema).
 */
function kunaal_seo_get_title(): string {
    if (is_singular()) {
        $post_id = get_queried_object_id();
        $custom = trim((string) get_post_meta($post_id, 'kunaal_seo_title', true));
        if ($custom !== '') {
            return $custom;
        }
        return (string) get_the_title($post_id);
    }

    if (is_post_type_archive()) {
        $pt = get_query_var('post_type');
        $pt = is_array($pt) ? reset($pt) : $pt;
        $obj = $pt ? get_post_type_object((string) $pt) : null;
        return $obj && !empty($obj->labels->name) ? (string) $obj->labels->name : (string) get_bloginfo('name');
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            return (string) $term->name;
        }
    }

    if (is_home() || is_front_page()) {
        return (string) get_bloginfo('name');
    }

    return (string) wp_get_document_title();
}

/**
 * Get the best meta description for the current view.
 */
function kunaal_seo_get_description(): string {
    if (is_singular()) {
        $post_id = get_queried_object_id();

        $custom = trim((string) get_post_meta($post_id, 'kunaal_seo_description', true));
        if ($custom !== '') {
            return $custom;
        }

        $subtitle = trim((string) get_post_meta($post_id, 'kunaal_subtitle', true));
        if ($subtitle !== '') {
            return $subtitle;
        }

        if (has_excerpt($post_id)) {
            return (string) get_the_excerpt($post_id);
        }

        $post = get_post($post_id);
        if ($post) {
            return (string) wp_trim_words(wp_strip_all_tags((string) $post->post_content), 30);
        }
    }

    if (is_post_type_archive('essay')) {
        return (string) kunaal_seo_setting('archive_essay_description', kunaal_seo_setting('default_description', ''));
    }
    if (is_post_type_archive('jotting')) {
        return (string) kunaal_seo_setting('archive_jotting_description', kunaal_seo_setting('default_description', ''));
    }
    if (is_tax('topic')) {
        return (string) kunaal_seo_setting('archive_topic_description', kunaal_seo_setting('default_description', ''));
    }

    return (string) kunaal_seo_setting('default_description', '');
}

/**
 * Determine whether the current view should be noindexed (theme policy).
 */
function kunaal_seo_should_noindex(): bool {
    if (is_404()) {
        return true;
    }

    if (is_search() && (bool) kunaal_seo_setting('noindex_search', true)) {
        return true;
    }

    $paged = (int) get_query_var('paged');
    if ($paged > 1 && (bool) kunaal_seo_setting('noindex_paged_archives', false)) {
        return true;
    }

    if (is_singular()) {
        $post_id = get_queried_object_id();
        return (bool) get_post_meta($post_id, 'kunaal_seo_noindex', true);
    }

    return false;
}

/**
 * Get canonical URL for the current view.
 */
function kunaal_seo_get_canonical_url(): string {
    if (is_singular()) {
        return (string) get_permalink(get_queried_object_id());
    }

    if (is_post_type_archive()) {
        $pt = get_query_var('post_type');
        $pt = is_array($pt) ? reset($pt) : $pt;
        $base = $pt ? get_post_type_archive_link((string) $pt) : home_url('/');
        return kunaal_seo_apply_pagination((string) $base);
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        $base = ($term instanceof WP_Term) ? get_term_link($term) : home_url('/');
        if (is_wp_error($base)) {
            $base = home_url('/');
        }
        return kunaal_seo_apply_pagination((string) $base);
    }

    if (is_front_page()) {
        return home_url('/');
    }
    if (is_home()) {
        $page_for_posts = (int) get_option('page_for_posts');
        $base = $page_for_posts ? get_permalink($page_for_posts) : home_url('/');
        return kunaal_seo_apply_pagination((string) $base);
    }

    return home_url('/');
}

/**
 * Apply /page/N canonicalization to an archive-like URL.
 */
function kunaal_seo_apply_pagination(string $base_url): string {
    $paged = (int) get_query_var('paged');
    if ($paged <= 1) {
        return $base_url;
    }
    return (string) get_pagenum_link($paged, false);
}

/**
 * Get an absolute share image URL for OG/Twitter/Schema.
 */
function kunaal_seo_get_share_image_url(): string {
    if (is_singular()) {
        $post_id = get_queried_object_id();
        $seo_img = absint(get_post_meta($post_id, 'kunaal_seo_og_image_id', true));
        if ($seo_img) {
            $url = wp_get_attachment_image_url($seo_img, 'large');
            if ($url) return (string) $url;
        }

        $card_image = absint(get_post_meta($post_id, 'kunaal_card_image', true));
        if ($card_image) {
            $url = wp_get_attachment_image_url($card_image, 'large');
            if ($url) return (string) $url;
        }

        if (has_post_thumbnail($post_id)) {
            $url = get_the_post_thumbnail_url($post_id, 'large');
            if ($url) return (string) $url;
        }
    }

    $default_id = absint(kunaal_seo_setting('default_share_image_id', 0));
    if ($default_id) {
        $url = wp_get_attachment_image_url($default_id, 'large');
        if ($url) return (string) $url;
    }

    return '';
}

