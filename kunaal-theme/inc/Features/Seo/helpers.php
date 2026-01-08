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

// ============================================================================
// SITEMAP.XML (Theme-owned alias to WP core sitemaps)
// ============================================================================

/**
 * Add /sitemap.xml endpoint.
 *
 * Preferred behavior: render the WordPress core sitemap index at /sitemap.xml
 * (keeps a stable, conventional URL without relying on redirects).
 */
function kunaal_seo_register_sitemap_xml_endpoint(): void {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?kunaal_sitemap=1', 'top');
}
add_action('init', 'kunaal_seo_register_sitemap_xml_endpoint');

/**
 * @param array<int, string> $vars
 * @return array<int, string>
 */
function kunaal_seo_add_sitemap_query_var(array $vars): array {
    $vars[] = 'kunaal_sitemap';
    return $vars;
}
add_filter('query_vars', 'kunaal_seo_add_sitemap_query_var');

/**
 * Flush rewrite rules once after deployment so /sitemap.xml begins working.
 * Safe: runs only for admins, only once.
 */
function kunaal_seo_maybe_flush_sitemap_rewrite(): void {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    if (get_option('kunaal_seo_sitemap_rewrite_flushed')) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('kunaal_seo_sitemap_rewrite_flushed', gmdate('c'));
}
add_action('admin_init', 'kunaal_seo_maybe_flush_sitemap_rewrite');

/**
 * Render sitemap for /sitemap.xml requests.
 *
 * Since WordPress core sitemaps are available at /wp-sitemap.xml (WP 5.5+),
 * we redirect /sitemap.xml to the core sitemap for consistency and completeness.
 * This ensures all post types, taxonomies, and pages are included automatically.
 */
function kunaal_seo_maybe_render_sitemap_xml(): void {
    if ((int) get_query_var('kunaal_sitemap') !== 1) {
        return;
    }

    // Avoid duplication if Yoast is active; Yoast handles /sitemap_index.xml.
    if (kunaal_seo_is_yoast_active()) {
        wp_redirect(home_url('/wp-sitemap.xml'), 301);
        exit;
    }

    // WordPress core sitemaps are available in WP 5.5+ at /wp-sitemap.xml.
    // Redirect /sitemap.xml to the core sitemap for a single, canonical source.
    // Use 301 (permanent) redirect so search engines update their index.
    wp_redirect(home_url('/wp-sitemap.xml'), 301);
    exit;
}
add_action('template_redirect', 'kunaal_seo_maybe_render_sitemap_xml', 0);

function kunaal_seo_render_simple_sitemap_xml(): void {
    status_header(200);
    header('Content-Type: application/xml; charset=' . get_option('blog_charset'), true);
    nocache_headers();

    $urls = array();

    $urls[] = array('loc' => home_url('/'), 'lastmod' => gmdate('c'));

    foreach (array('about', 'contact') as $slug) {
        $page = get_page_by_path($slug);
        if ($page) {
            $urls[] = array(
                'loc' => (string) get_permalink($page),
                'lastmod' => (string) get_post_modified_time('c', true, $page),
            );
        }
    }

    $post_types = array('essay', 'jotting');
    foreach ($post_types as $pt) {
        $ids = get_posts(
            array(
                'post_type' => $pt,
                'post_status' => 'publish',
                'fields' => 'ids',
                'posts_per_page' => 5000,
                'no_found_rows' => true,
                'orderby' => 'modified',
                'order' => 'DESC',
            )
        );

        foreach ($ids as $post_id) {
            $urls[] = array(
                'loc' => (string) get_permalink((int) $post_id),
                'lastmod' => (string) get_post_modified_time('c', true, (int) $post_id),
            );
        }
    }

    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($urls as $item) {
        $loc = isset($item['loc']) ? (string) $item['loc'] : '';
        $lastmod = isset($item['lastmod']) ? (string) $item['lastmod'] : '';
        if ($loc === '') {
            continue;
        }
        echo "  <url>\n";
        echo "    <loc>" . esc_url($loc) . "</loc>\n";
        if ($lastmod !== '') {
            echo "    <lastmod>" . esc_html($lastmod) . "</lastmod>\n";
        }
        echo "  </url>\n";
    }
    echo "</urlset>\n";
}

/**
 * Build the SEO title (for <title>, OG, schema).
 */
function kunaal_seo_get_title(): string {
    if (is_singular()) {
        return kunaal_seo_get_singular_title();
    }

    $archive_title = kunaal_seo_get_archive_title();
    if ($archive_title !== '') {
        return $archive_title;
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
        return kunaal_seo_get_singular_description();
    }

    $archive_desc = kunaal_seo_get_archive_description();
    if ($archive_desc !== '') {
        return $archive_desc;
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

    $archive_base = kunaal_seo_get_archive_canonical_base();
    if ($archive_base !== '') {
        return kunaal_seo_apply_pagination($archive_base);
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
    $url = '';

    if (is_singular()) {
        $url = kunaal_seo_get_singular_share_image_url();
    }

    if ($url === '') {
        $url = kunaal_seo_get_default_share_image_url();
    }

    return $url;
}

function kunaal_seo_get_singular_title(): string {
    $post_id = get_queried_object_id();
    $custom = trim((string) get_post_meta($post_id, 'kunaal_seo_title', true));
    return ($custom !== '') ? $custom : (string) get_the_title($post_id);
}

function kunaal_seo_get_archive_title(): string {
    if (is_post_type_archive()) {
        $pt = get_query_var('post_type');
        $pt = is_array($pt) ? reset($pt) : $pt;
        $obj = $pt ? get_post_type_object((string) $pt) : null;
        return ($obj && !empty($obj->labels->name)) ? (string) $obj->labels->name : (string) get_bloginfo('name');
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            return (string) $term->name;
        }
    }

    return '';
}

function kunaal_seo_get_singular_description(): string {
    $post_id = get_queried_object_id();

    $desc = '';

    $custom = trim((string) get_post_meta($post_id, 'kunaal_seo_description', true));
    if ($custom !== '') {
        $desc = $custom;
    }

    if ($desc === '') {
        $subtitle = trim((string) get_post_meta($post_id, 'kunaal_subtitle', true));
        if ($subtitle !== '') {
            $desc = $subtitle;
        }
    }

    if ($desc === '' && has_excerpt($post_id)) {
        $desc = (string) get_the_excerpt($post_id);
    }

    if ($desc === '') {
        $post = get_post($post_id);
        if ($post) {
            $desc = (string) wp_trim_words(wp_strip_all_tags((string) $post->post_content), 30);
        }
    }

    return $desc;
}

function kunaal_seo_get_archive_description(): string {
    $fallback = (string) kunaal_seo_setting('default_description', '');

    $desc = '';
    if (is_post_type_archive('essay')) {
        $desc = (string) kunaal_seo_setting('archive_essay_description', $fallback);
    }
    if ($desc === '' && is_post_type_archive('jotting')) {
        $desc = (string) kunaal_seo_setting('archive_jotting_description', $fallback);
    }
    if ($desc === '' && is_tax('topic')) {
        $desc = (string) kunaal_seo_setting('archive_topic_description', $fallback);
    }

    return $desc;
}

function kunaal_seo_get_archive_canonical_base(): string {
    $base = '';

    if (is_post_type_archive()) {
        $pt = get_query_var('post_type');
        $pt = is_array($pt) ? reset($pt) : $pt;
        $base = $pt ? (string) get_post_type_archive_link((string) $pt) : home_url('/');
    }

    if ($base === '' && (is_tax() || is_category() || is_tag())) {
        $term = get_queried_object();
        $term_link = ($term instanceof WP_Term) ? get_term_link($term) : home_url('/');
        if (is_wp_error($term_link)) {
            $base = home_url('/');
        } else {
            $base = (string) $term_link;
        }
    }

    return $base;
}

function kunaal_seo_get_singular_share_image_url(): string {
    $post_id = get_queried_object_id();

    $url = '';

    $seo_img = absint(get_post_meta($post_id, 'kunaal_seo_og_image_id', true));
    if ($seo_img) {
        $found = wp_get_attachment_image_url($seo_img, 'large');
        if ($found) {
            $url = (string) $found;
        }
    }

    if ($url === '') {
        $card_image = absint(get_post_meta($post_id, 'kunaal_card_image', true));
        if ($card_image) {
            $found = wp_get_attachment_image_url($card_image, 'large');
            if ($found) {
                $url = (string) $found;
            }
        }
    }

    if ($url === '' && has_post_thumbnail($post_id)) {
        $found = get_the_post_thumbnail_url($post_id, 'large');
        if ($found) {
            $url = (string) $found;
        }
    }

    return $url;
}

function kunaal_seo_get_default_share_image_url(): string {
    $default_id = absint(kunaal_seo_setting('default_share_image_id', 0));
    if (!$default_id) {
        return '';
    }

    $url = wp_get_attachment_image_url($default_id, 'large');
    return $url ? (string) $url : '';
}

