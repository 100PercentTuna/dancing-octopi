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
 * WordPress serves a virtual robots.txt at /robots.txt by default.
 *
 * @param string $output
 * @param bool   $public
 * @return string
 */
function kunaal_seo_robots_txt(string $output, bool $public): string {
    if (kunaal_seo_is_yoast_active()) {
        return $output;
    }

    // If the site is set to discourage search engines, don't advertise sitemaps.
    if (!$public) {
        return $output;
    }

    if (stripos($output, 'sitemap:') !== false) {
        return $output;
    }

    return rtrim($output) . "\nSitemap: " . home_url('/sitemap.xml') . "\n";
}
add_filter('robots_txt', 'kunaal_seo_robots_txt', 10, 2);