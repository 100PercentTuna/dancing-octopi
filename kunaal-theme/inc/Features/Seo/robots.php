<?php
/**
 * Robots directives (theme-owned SEO).
 *
 * @package Kunaal_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add robots directives via wp_robots.
 *
 * @param array<string, bool|string|int> $robots
 * @return array<string, bool|string|int>
 */
function kunaal_seo_wp_robots(array $robots): array {
    if (kunaal_seo_is_yoast_active()) {
        return $robots;
    }

    if (kunaal_seo_should_noindex()) {
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }

    // Discoverability-friendly defaults (Google-supported; harmless elsewhere).
    $robots['max-snippet'] = -1;
    $robots['max-image-preview'] = 'large';
    $robots['max-video-preview'] = -1;

    return $robots;
}
add_filter('wp_robots', 'kunaal_seo_wp_robots');

/**
 * Ensure robots.txt includes a Sitemap directive pointing at /sitemap.xml.
 *
 * WordPress core automatically adds /wp-sitemap.xml to robots.txt when core
 * sitemaps are enabled. We replace it with /sitemap.xml (which redirects to
 * /wp-sitemap.xml) for the conventional URL.
 *
 * @param string $output
 * @param bool   $public
 * @return string
 */
function kunaal_seo_robots_txt(string $output, bool $public): string {
    if (kunaal_seo_is_yoast_active()) {
        return $output;
    }

    if (!$public) {
        return $output;
    }

    $out = $output;
    $sitemap_url = home_url('/sitemap.xml');
    $sitemap_line = "\nSitemap: " . $sitemap_url . "\n";

    // Remove any existing Sitemap lines (WordPress core adds /wp-sitemap.xml).
    // Use regex to match case-insensitive "Sitemap:" followed by any URL.
    $out = preg_replace('/\n[Ss]itemap:\s*[^\n]*\n/i', '', $out);

    // Add our sitemap line.
    $out = rtrim($out) . $sitemap_line;

    return $out;
}
add_filter('robots_txt', 'kunaal_seo_robots_txt', 10, 2);