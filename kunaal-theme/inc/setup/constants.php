<?php
/**
 * Theme Constants
 * 
 * Defines all theme constants used throughout the codebase.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// Core theme constants
define('KUNAAL_THEME_VERSION', '4.30.0');
define('KUNAAL_THEME_DIR', get_template_directory());
define('KUNAAL_THEME_URI', get_template_directory_uri());

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

/**
 * Asset version helper (cache-bust on managed hosts/CDNs).
 */
function kunaal_asset_version($relative_path) {
    $relative_path = ltrim((string) $relative_path, '/');
    $full = trailingslashit(KUNAAL_THEME_DIR) . $relative_path;
    if ($relative_path && file_exists($full)) {
        return (string) filemtime($full);
    }
    return KUNAAL_THEME_VERSION;
}

