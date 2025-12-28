<?php
/**
 * Kunaal Theme functions and definitions.
 *
 * Main theme bootstrap file. Loads modular includes in correct order.
 *
 * @package Kunaal_Theme
 * @since 1.0.0
 * @version 4.32.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// CONSTANTS
// ============================================================================

define('KUNAAL_THEME_DIR', get_template_directory());
define('KUNAAL_THEME_URI', get_template_directory_uri());
define('KUNAAL_THEME_VERSION', '4.32.0');

// ============================================================================
// SETUP (Platform)
// ============================================================================

// Logging (must load first)
require_once KUNAAL_THEME_DIR . '/inc/setup/logging.php';

// Constants (needed by all modules)
require_once KUNAAL_THEME_DIR . '/inc/setup/constants.php';

// Body classes (stable page scoping)
require_once KUNAAL_THEME_DIR . '/inc/Setup/body-classes.php';

// Asset enqueuing
require_once KUNAAL_THEME_DIR . '/inc/Setup/enqueue.php';

// Theme setup (image sizes, theme support, etc.)
require_once KUNAAL_THEME_DIR . '/inc/setup/theme-setup.php';

// Customizer registration
require_once KUNAAL_THEME_DIR . '/inc/setup/customizer.php';

// Editor assets (Gutenberg)
require_once KUNAAL_THEME_DIR . '/inc/setup/editor-assets.php';

// ============================================================================
// FEATURES (Site Behavior) — Will be reorganized in Phase 2
// ============================================================================

// Post types and taxonomies
require_once KUNAAL_THEME_DIR . '/inc/post-types/post-types.php';

// Meta boxes and meta field registration
require_once KUNAAL_THEME_DIR . '/inc/meta/meta-boxes.php';

// Validation (essay/jotting publish validation)
require_once KUNAAL_THEME_DIR . '/inc/validation/validation.php';

// AJAX handlers (filter, debug log)
require_once KUNAAL_THEME_DIR . '/inc/ajax/ajax-handlers.php';

// Email handlers (contact form, subscribe, SMTP)
require_once KUNAAL_THEME_DIR . '/inc/email/email-handlers.php';
require_once KUNAAL_THEME_DIR . '/inc/email/subscribe-handler.php';
require_once KUNAAL_THEME_DIR . '/inc/email/smtp-config.php';

// SEO (Open Graph, Twitter Cards)
require_once KUNAAL_THEME_DIR . '/inc/seo/seo.php';

// ============================================================================
// BLOCKS (Content System) — Will be reorganized in Phase 2
// ============================================================================

// Block helpers (must load before block registration)
require_once KUNAAL_THEME_DIR . '/inc/block-helpers.php';

// Block registration
require_once KUNAAL_THEME_DIR . '/inc/blocks.php';

// ============================================================================
// SUPPORT (Utilities) — Will be reorganized in Phase 2
// ============================================================================

// Helper Functions (must load before other modules that depend on them)
require_once KUNAAL_THEME_DIR . '/inc/helpers.php';

// Customizer Section Helpers
require_once KUNAAL_THEME_DIR . '/inc/customizer-sections.php';

// About Page Customizer V22
require_once KUNAAL_THEME_DIR . '/inc/about-customizer-v22.php';
require_once KUNAAL_THEME_DIR . '/inc/about-customizer-sections.php';
require_once KUNAAL_THEME_DIR . '/inc/about-helpers.php';
require_once KUNAAL_THEME_DIR . '/inc/about/about-helpers.php';

// PDF Generator
require_once KUNAAL_THEME_DIR . '/pdf-generator.php';

// ============================================================================
// ASSET ENQUEUING
// ============================================================================

// Main asset enqueuing (must load after all helpers are available)
// This function is defined in inc/Setup/enqueue.php
add_action('wp_enqueue_scripts', 'kunaal_enqueue_assets');
