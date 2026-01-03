<?php
/**
 * Theme Constants
 * 
 * Defines configurable theme constants used throughout the codebase.
 * Core constants (KUNAAL_THEME_VERSION, KUNAAL_THEME_DIR, KUNAAL_THEME_URI)
 * are defined in functions.php as the single source of truth.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Theme constants for configurable values
if (!defined('KUNAAL_READING_SPEED_WPM')) {
    define('KUNAAL_READING_SPEED_WPM', 200); // Words per minute for read time calculation
}
if (!defined('KUNAAL_HOME_POSTS_LIMIT')) {
    define('KUNAAL_HOME_POSTS_LIMIT', 6); // Default number of posts to show on home page
}

// Panorama constants for About page
if (!defined('PANORAMA_CUT_PREFIX')) {
    define('PANORAMA_CUT_PREFIX', ' cut-'); // CSS class prefix for panorama cut styles (with leading space)
}
if (!defined('PANORAMA_BG_WARM')) {
    define('PANORAMA_BG_WARM', ' bg-warm'); // CSS class for warm background panorama (with leading space)
}
if (!defined('KUNAAL_ERROR_MESSAGE_GENERIC')) {
    define('KUNAAL_ERROR_MESSAGE_GENERIC', 'An error occurred. Please try again.'); // Generic error message for AJAX responses
}

// Navigation constants
if (!defined('KUNAAL_NAV_CURRENT_CLASS')) {
    define('KUNAAL_NAV_CURRENT_CLASS', ' current'); // CSS class for current navigation item
}

/**
 * Asset version helper (cache-bust on managed hosts/CDNs).
 * 
 * Cache-bust suffix: Increment this when CSS changes need to bypass stale page caches.
 * The filemtime alone isn't enough when WordPress page cache stores old timestamps.
 */
define('KUNAAL_ASSET_CACHE_BUST', '3');

function kunaal_asset_version(string $relative_path): string {
    $relative_path = ltrim((string) $relative_path, '/');
    $full = trailingslashit(KUNAAL_THEME_DIR) . $relative_path;
    if ($relative_path && file_exists($full)) {
        return filemtime($full) . '.' . KUNAAL_ASSET_CACHE_BUST;
    }
    return KUNAAL_THEME_VERSION . '.' . KUNAAL_ASSET_CACHE_BUST;
}

