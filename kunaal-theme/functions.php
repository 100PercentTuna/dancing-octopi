<?php
/**
 * Kunaal Theme functions and definitions.
 *
 * Main theme bootstrap file. Loads modular includes in correct order.
 *
 * @package Kunaal_Theme
 * @since 1.0.0
 * @version 4.43.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// CONSTANTS
// ============================================================================

if (!defined('KUNAAL_THEME_DIR')) {
    define('KUNAAL_THEME_DIR', get_template_directory());
}
if (!defined('KUNAAL_THEME_URI')) {
    define('KUNAAL_THEME_URI', get_template_directory_uri());
}
if (!defined('KUNAAL_THEME_VERSION')) {
    // Single source of truth: derived from style.css header via wp_get_theme().
    $theme = wp_get_theme();
    define('KUNAAL_THEME_VERSION', $theme->get('Version'));
}

// ============================================================================
// SETUP (Platform)
// ============================================================================

// Logging (must load first)
require_once KUNAAL_THEME_DIR . '/inc/Setup/logging.php';

// Constants (needed by all modules)
require_once KUNAAL_THEME_DIR . '/inc/Setup/constants.php';

// Body classes (stable page scoping)
require_once KUNAAL_THEME_DIR . '/inc/Setup/body-classes.php';

// Asset enqueuing
require_once KUNAAL_THEME_DIR . '/inc/Setup/enqueue.php';

// Theme setup (image sizes, theme support, etc.)
require_once KUNAAL_THEME_DIR . '/inc/Setup/theme-setup.php';

// Customizer registration
require_once KUNAAL_THEME_DIR . '/inc/Setup/customizer.php';

// Editor assets (Gutenberg)
require_once KUNAAL_THEME_DIR . '/inc/Setup/editor-assets.php';

// ============================================================================
// FEATURES (Site Behavior)
// ============================================================================

// Post types and taxonomies
require_once KUNAAL_THEME_DIR . '/inc/Features/PostTypes/post-types.php';

// Meta boxes and meta field registration
require_once KUNAAL_THEME_DIR . '/inc/meta/meta-boxes.php';

// Validation (essay/jotting publish validation)
require_once KUNAAL_THEME_DIR . '/inc/Support/validation.php';

// AJAX handlers
require_once KUNAAL_THEME_DIR . '/inc/Features/Ajax/filter-content.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Ajax/debug-log.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Ajax/embed-card.php';

// Email handlers (contact form, subscribe, SMTP)
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/email-handlers.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/db.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/subscribe-handler.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/admin-pages.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/templates.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/queue.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/blast-admin.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/tracking.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/smtp-config.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/smtp-diagnostics.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/smtp-admin-page.php';

// SEO (theme-owned; avoids duplication when Yoast is active)
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/helpers.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/meta.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/robots.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/schema.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/open-graph.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/admin-settings.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/meta-box.php';

// ============================================================================
// BLOCKS (Content System)
// ============================================================================

// Block helpers (must load before block registration)
require_once KUNAAL_THEME_DIR . '/inc/Blocks/helpers.php';

// Block registration
require_once KUNAAL_THEME_DIR . '/inc/Blocks/register.php';

// Block styles
require_once KUNAAL_THEME_DIR . '/inc/Blocks/styles.php';

// ============================================================================
// SUPPORT (Utilities)
// ============================================================================

// Helper Functions (must load before other modules that depend on them)
require_once KUNAAL_THEME_DIR . '/inc/Support/helpers.php';

// Customizer Section Helpers
require_once KUNAAL_THEME_DIR . '/inc/customizer-sections.php';

// About Page Feature Module
require_once KUNAAL_THEME_DIR . '/inc/Features/About/data.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/About/render.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/About/customizer.php';

// PDF Generator
require_once KUNAAL_THEME_DIR . '/pdf-generator.php';

// ============================================================================
// ASSET ENQUEUING
// ============================================================================

// Main asset enqueuing (must load after all helpers are available)
// This function is defined in inc/Setup/enqueue.php
add_action('wp_enqueue_scripts', 'kunaal_enqueue_assets');
