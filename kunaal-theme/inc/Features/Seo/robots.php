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
