<?php
/**
 * Kunaal Theme Functions
 *
 * Main theme bootstrap file. Loads modular includes in correct order.
 *
 * @package Kunaal_Theme
 * @since 1.0.0
 * @version 4.30.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// PHASE 1: LOGGING (must load first)
// ========================================
require_once get_template_directory() . '/inc/setup/logging.php';

// ========================================
// PHASE 2: CONSTANTS (needed by all modules)
// ========================================
require_once get_template_directory() . '/inc/setup/constants.php';

// ========================================
// PHASE 3: CORE MODULES (load in dependency order)
// ========================================

// Theme setup (image sizes, theme support, etc.)
kunaal_theme_safe_require_once(get_template_directory() . '/inc/setup/theme-setup.php');

// Post types and taxonomies
kunaal_theme_safe_require_once(get_template_directory() . '/inc/post-types/post-types.php');

// Meta boxes and meta field registration
kunaal_theme_safe_require_once(get_template_directory() . '/inc/meta/meta-boxes.php');

// Validation (essay/jotting publish validation)
kunaal_theme_safe_require_once(get_template_directory() . '/inc/validation/validation.php');

// Customizer registration
kunaal_theme_safe_require_once(get_template_directory() . '/inc/setup/customizer.php');

// Editor assets (Gutenberg)
kunaal_theme_safe_require_once(get_template_directory() . '/inc/setup/editor-assets.php');

// ========================================
// PHASE 4: EXISTING MODULAR FILES
// ========================================

// Helper Functions (must load before other modules that depend on them)
kunaal_theme_safe_require_once(get_template_directory() . '/inc/helpers.php');

// Asset Enqueuing Helpers
kunaal_theme_safe_require_once(get_template_directory() . '/inc/enqueue-helpers.php');

// Block Registration
kunaal_theme_safe_require_once(get_template_directory() . '/inc/block-helpers.php');
kunaal_theme_safe_require_once(get_template_directory() . '/inc/blocks.php');

// Customizer Section Helpers
kunaal_theme_safe_require_once(get_template_directory() . '/inc/customizer-sections.php');

// About Page Customizer V22
kunaal_theme_safe_require_once(get_template_directory() . '/inc/about-customizer-v22.php');
kunaal_theme_safe_require_once(get_template_directory() . '/inc/about-customizer-sections.php');
kunaal_theme_safe_require_once(get_template_directory() . '/inc/about-helpers.php');
kunaal_theme_safe_require_once(get_template_directory() . '/inc/about/about-helpers.php');

// PDF Generator
kunaal_theme_safe_require_once(get_template_directory() . '/pdf-generator.php');

// ========================================
// PHASE 5: AJAX & EMAIL HANDLERS
// ========================================

// AJAX handlers (filter, debug log)
kunaal_theme_safe_require_once(get_template_directory() . '/inc/ajax/ajax-handlers.php');

// Email handlers (contact form, subscribe, SMTP)
kunaal_theme_safe_require_once(get_template_directory() . '/inc/email/email-handlers.php');
kunaal_theme_safe_require_once(get_template_directory() . '/inc/email/subscribe-handler.php');
kunaal_theme_safe_require_once(get_template_directory() . '/inc/email/smtp-config.php');

// ========================================
// PHASE 6: SEO & ASSET ENQUEUING
// ========================================

// SEO (Open Graph, Twitter Cards)
kunaal_theme_safe_require_once(get_template_directory() . '/inc/seo/seo.php');

// Main asset enqueuing (must load after all helpers are available)
// This function is defined in inc/enqueue-helpers.php
add_action('wp_enqueue_scripts', 'kunaal_enqueue_assets');

/**
 * Add defer attribute to non-critical scripts for better performance
 */
function kunaal_add_defer_to_scripts($tag, $handle) {
    $defer_scripts = array(
        'kunaal-theme-main',
        'kunaal-theme-controller',
        'kunaal-lazy-blocks',
        'kunaal-lib-loader',
        'gsap-core',
        'gsap-scrolltrigger',
        'kunaal-about-page-v22',
    );
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'kunaal_add_defer_to_scripts', 10, 2);

/**
 * Add a `js` class to <html> early for progressive enhancement.
 * This ensures About page content is visible even if JS fails.
 */
function kunaal_add_js_class() {
    echo "<script>(function(d){d.documentElement.classList.add('js');})(document);</script>\n";
}
add_action('wp_head', 'kunaal_add_js_class', 0);
