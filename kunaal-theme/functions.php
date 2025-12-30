<?php
/**
 * Kunaal Theme functions and definitions.
 *
 * Main theme bootstrap file. Loads modular includes in correct order.
 *
 * @package Kunaal_Theme
 * @since 1.0.0
 * @version 4.40.3
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// CONSTANTS
// ============================================================================

define('KUNAAL_THEME_DIR', get_template_directory());
define('KUNAAL_THEME_URI', get_template_directory_uri());
define('KUNAAL_THEME_VERSION', '4.40.4');

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

// Email handlers (contact form, subscribe, SMTP)
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/email-handlers.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/subscribe-handler.php';
require_once KUNAAL_THEME_DIR . '/inc/Features/Email/smtp-config.php';

// SEO (Open Graph, Twitter Cards)
require_once KUNAAL_THEME_DIR . '/inc/Features/Seo/open-graph.php';

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
