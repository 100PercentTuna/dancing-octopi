<?php
/**
 * Kunaal Theme Functions
 * 
 * Main theme functions file. This file is organized into sections:
 * 
 * 1. CONSTANTS & INCLUDES
 * 2. THEME SETUP
 * 3. ASSET ENQUEUING
 * 4. CUSTOM POST TYPES & TAXONOMIES
 * 5. META BOXES
 * 6. VALIDATION
 * 7. CUSTOMIZER SETTINGS
 * 8. HELPER FUNCTIONS
 * 9. AJAX HANDLERS
 * 10. SHORTCODES
 *
 * @package Kunaal_Theme
 * @since 1.0.0
 * @version 4.28.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// ========================================
// CRASH-SAFE LOGGING (for managed hosts where debug.log is blocked)
// ========================================

/**
 * Best-effort logger.
 * Writes to:
 * - PHP error log (always attempted)
 * - wp-content/kunaal-theme-debug.log (if writable)
 */
function kunaal_theme_log($message, $context = array()) {
    try {
        $prefix = '[kunaal-theme] ';
        $ts = gmdate('c');
        $ctx = '';
        if (!empty($context)) {
            // Avoid throwing on non-UTF8 / non-serializable values.
            $ctx = ' ' . wp_json_encode($context);
        }
        @error_log($prefix . $ts . ' ' . (string) $message . $ctx);

        if (defined('WP_CONTENT_DIR') && is_dir(WP_CONTENT_DIR) && is_writable(WP_CONTENT_DIR)) {
            $logFile = trailingslashit(WP_CONTENT_DIR) . 'kunaal-theme-debug.log';
            $line = $prefix . $ts . ' ' . (string) $message . $ctx . PHP_EOL;
            @file_put_contents($logFile, $line, FILE_APPEND);
        }
    } catch (\Throwable $e) {
        // Never allow logging to crash the site.
        @error_log('[kunaal-theme] log failure: ' . $e->getMessage());
    }
}

/**
 * Capture fatal errors that result in white-screen/500 without wp-content/debug.log.
 */
function kunaal_theme_register_shutdown_handler() {
    register_shutdown_function(function () {
        $err = error_get_last();
        if (!$err || !isset($err['type'])) {
            return;
        }
        $fatal_types = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
        if (in_array($err['type'], $fatal_types, true)) {
            kunaal_theme_log('FATAL', array(
                'type' => $err['type'],
                'message' => $err['message'] ?? '',
                'file' => $err['file'] ?? '',
                'line' => $err['line'] ?? 0,
                'uri' => $_SERVER['REQUEST_URI'] ?? '',
            ));
        }
    });
}
kunaal_theme_register_shutdown_handler();

/**
 * Crash-safe require_once wrapper.
 * If a file is missing (bad zip / partial deploy), we log and continue.
 */
function kunaal_theme_safe_require_once($absolute_path) {
    if (is_string($absolute_path) && $absolute_path !== '' && file_exists($absolute_path)) {
        require_once $absolute_path;
        return true;
    }
    kunaal_theme_log('Missing required file', array('path' => (string) $absolute_path));
    return false;
}

// ========================================
// 1. CONSTANTS & INCLUDES
// ========================================

define('KUNAAL_THEME_VERSION', '4.28.1');
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

// PDF Generator for essays
kunaal_theme_safe_require_once(KUNAAL_THEME_DIR . '/pdf-generator.php');

// About Page Customizer V22 (new polished design)
kunaal_theme_safe_require_once(KUNAAL_THEME_DIR . '/inc/about-customizer-v22.php');
kunaal_theme_safe_require_once(KUNAAL_THEME_DIR . '/inc/about-helpers.php');

// Block Registration
kunaal_theme_safe_require_once(KUNAAL_THEME_DIR . '/inc/blocks.php');

// Helper Functions
// All helper functions are defined in inc/helpers.php to avoid duplication.
kunaal_theme_safe_require_once(KUNAAL_THEME_DIR . '/inc/helpers.php');

// Customizer Section Helpers
kunaal_theme_safe_require_once(KUNAAL_THEME_DIR . '/inc/customizer-sections.php');

/**
 * Theme Setup
 */
function kunaal_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');
    
    add_image_size('essay-card', 800, 1000, true);
    add_image_size('essay-hero', 1600, 533, true);
    add_image_size('split-image', 600, 750, true);
    
    // Editor styles
    add_editor_style('assets/css/editor-style.css');
}
add_action('after_setup_theme', 'kunaal_theme_setup');

/**
 * Add resource hints for Google Fonts (preconnect for faster DNS)
 */
function kunaal_resource_hints($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = array(
            'href' => 'https://fonts.googleapis.com',
            'crossorigin',
        );
        $urls[] = array(
            'href' => 'https://fonts.gstatic.com',
            'crossorigin',
        );
    }
    return $urls;
}
add_filter('wp_resource_hints', 'kunaal_resource_hints', 10, 2);

/**
 * Enqueue Scripts and Styles
 */
function kunaal_enqueue_assets() {
    // Google Fonts (including Caveat for sidenotes)
    wp_enqueue_style(
        'kunaal-google-fonts',
        'https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Inter:opsz,wght@14..32,300..700&family=Caveat:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    // Modular CSS files (loaded in order for proper cascade)
    // 1. Variables (must load first)
    wp_enqueue_style(
        'kunaal-theme-variables',
        KUNAAL_THEME_URI . '/assets/css/variables.css',
        array(),
        kunaal_asset_version('assets/css/variables.css')
    );

    // 2. Base styles (resets, typography)
    wp_enqueue_style(
        'kunaal-theme-base',
        KUNAAL_THEME_URI . '/assets/css/base.css',
        array('kunaal-theme-variables'),
        kunaal_asset_version('assets/css/base.css')
    );

    // 3. Dark mode (must load after base)
    wp_enqueue_style(
        'kunaal-theme-dark-mode',
        KUNAAL_THEME_URI . '/assets/css/dark-mode.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/dark-mode.css')
    );

    // 4. Layout
    wp_enqueue_style(
        'kunaal-theme-layout',
        KUNAAL_THEME_URI . '/assets/css/layout.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/layout.css')
    );

    // 5. Header
    wp_enqueue_style(
        'kunaal-theme-header',
        KUNAAL_THEME_URI . '/assets/css/header.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/header.css')
    );

    // 6. Components (cards, buttons, panels, footer)
    wp_enqueue_style(
        'kunaal-theme-components',
        KUNAAL_THEME_URI . '/assets/css/components.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/components.css')
    );

    // 7. Utilities (progress bar, lazy loading, animations)
    wp_enqueue_style(
        'kunaal-theme-utilities',
        KUNAAL_THEME_URI . '/assets/css/utilities.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/utilities.css')
    );

    // 8. Filters/Toolbar
    wp_enqueue_style(
        'kunaal-theme-filters',
        KUNAAL_THEME_URI . '/assets/css/filters.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/filters.css')
    );

    // 9. Sections & Grid
    wp_enqueue_style(
        'kunaal-theme-sections',
        KUNAAL_THEME_URI . '/assets/css/sections.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/sections.css')
    );

    // 10. Pages (archive, article, prose, page utilities)
    wp_enqueue_style(
        'kunaal-theme-pages',
        KUNAAL_THEME_URI . '/assets/css/pages.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/pages.css')
    );

    // 11. Custom Blocks
    wp_enqueue_style(
        'kunaal-theme-blocks',
        KUNAAL_THEME_URI . '/assets/css/blocks.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/blocks.css')
    );

    // 12. WordPress Core Block Overrides
    wp_enqueue_style(
        'kunaal-theme-wordpress-blocks',
        KUNAAL_THEME_URI . '/assets/css/wordpress-blocks.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/wordpress-blocks.css')
    );

    // 13. Motion Primitives
    wp_enqueue_style(
        'kunaal-theme-motion',
        KUNAAL_THEME_URI . '/assets/css/motion.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/motion.css')
    );

    // 14. Compatibility (print, reduced motion, cross-browser, parallax)
    wp_enqueue_style(
        'kunaal-theme-compatibility',
        KUNAAL_THEME_URI . '/assets/css/compatibility.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/compatibility.css')
    );

    // 15. About Page V2
    wp_enqueue_style(
        'kunaal-theme-about-page',
        KUNAAL_THEME_URI . '/assets/css/about-page.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/about-page.css')
    );

    // 16. Main stylesheet (now minimal - only contains theme header and any remaining styles)
    wp_enqueue_style(
        'kunaal-theme-style',
        get_stylesheet_uri(),
        array('kunaal-google-fonts', 'kunaal-theme-variables', 'kunaal-theme-base', 'kunaal-theme-dark-mode', 'kunaal-theme-layout', 'kunaal-theme-header', 'kunaal-theme-components', 'kunaal-theme-utilities', 'kunaal-theme-filters', 'kunaal-theme-sections', 'kunaal-theme-pages', 'kunaal-theme-blocks', 'kunaal-theme-wordpress-blocks', 'kunaal-theme-motion', 'kunaal-theme-compatibility', 'kunaal-theme-about-page'),
        kunaal_asset_version('style.css')
    );
    
    // Sidenote font (Caveat from Google Fonts - already loaded above)
    
    // Print stylesheet
    wp_enqueue_style(
        'kunaal-print-style',
        KUNAAL_THEME_URI . '/assets/css/print.css',
        array('kunaal-theme-style'),
        kunaal_asset_version('assets/css/print.css'),
        'print'
    );

    // Main script (defer for non-blocking)
    wp_enqueue_script(
        'kunaal-theme-main',
        KUNAAL_THEME_URI . '/assets/js/main.js',
        array(),
        kunaal_asset_version('assets/js/main.js'),
        true
    );

    // Theme controller (dark mode) - defer
    wp_enqueue_script(
        'kunaal-theme-controller',
        KUNAAL_THEME_URI . '/assets/js/theme-controller.js',
        array(),
        kunaal_asset_version('assets/js/theme-controller.js'),
        true
    );

    // Lazy loading for heavy blocks - defer
    wp_enqueue_script(
        'kunaal-lazy-blocks',
        KUNAAL_THEME_URI . '/assets/js/lazy-blocks.js',
        array(),
        kunaal_asset_version('assets/js/lazy-blocks.js'),
        true
    );

    // Centralized library loader (prevents duplicate loads) - defer
    wp_enqueue_script(
        'kunaal-lib-loader',
        KUNAAL_THEME_URI . '/assets/js/lib-loader.js',
        array(),
        kunaal_asset_version('assets/js/lib-loader.js'),
        true
    );

    // Localize script with data
    // Always localize to ensure kunaalTheme is available on all pages
    wp_localize_script('kunaal-theme-main', 'kunaalTheme', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kunaal_theme_nonce'),
        'pdfNonce' => wp_create_nonce('kunaal_pdf_nonce'),
        'homeUrl' => home_url('/'),
        'shareText' => kunaal_mod('kunaal_share_text', ''),
        'twitterHandle' => kunaal_mod('kunaal_twitter_handle', ''),
        'linkedinUrl' => kunaal_mod('kunaal_linkedin_handle', ''),
        'authorName' => kunaal_mod('kunaal_author_first_name', 'Kunaal') . ' ' . kunaal_mod('kunaal_author_last_name', 'Wadhwa'),
    ));
    
    // For contact page, also add inline script to ensure kunaalTheme is available
    // even if main.js loads late or fails
    if (is_page_template('page-contact.php') || (is_page() && get_page_template_slug() === 'page-contact.php')) {
        wp_add_inline_script('kunaal-theme-main', 
            'if (typeof kunaalTheme === "undefined") { window.kunaalTheme = { ajaxUrl: "' . esc_js(admin_url('admin-ajax.php')) . '" }; }',
            'before'
        );
    }
    
    // About page and Contact page assets
    // Template detection (page selection removed - no longer used)
    $is_about_page = is_page_template('page-about.php') || is_page('about');
    $is_contact_page = is_page_template('page-contact.php') || is_page('contact');
    
    // About page: Generate CSS variables for category colors (externalized from template)
    if ($is_about_page) {
        $categories = kunaal_get_categories_v22();
        if (!empty($categories)) {
            $css_vars = "body.page-template-page-about {\n";
            foreach ($categories as $slug => $category) {
                $css_vars .= "  --cat-" . esc_attr($slug) . ": " . esc_attr($category['color']) . ";\n";
            }
            $css_vars .= "}";
            wp_add_inline_style('kunaal-about-page-v22-style', $css_vars);
        }
    }
    
    if ($is_about_page || $is_contact_page) {
        // Cormorant Garamond font
        wp_enqueue_style(
            'kunaal-cormorant-font',
            'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500&display=swap',
            array(),
            null
        );
    }
    
    // Contact page specific assets
    if ($is_contact_page) {
        // Contact page CSS
        wp_enqueue_style(
            'kunaal-contact-page',
            KUNAAL_THEME_URI . '/assets/css/contact-page.css',
            array('kunaal-theme-style'),
            kunaal_asset_version('assets/css/contact-page.css')
        );
        
        // Contact page JavaScript
        wp_enqueue_script(
            'kunaal-contact-page',
            KUNAAL_THEME_URI . '/assets/js/contact-page.js',
            array('kunaal-theme-main'),
            kunaal_asset_version('assets/js/contact-page.js'),
            true
        );
    }
    
    // About page specific assets (heavier libraries)
    if ($is_about_page) {
        // About page V22 CSS
        wp_enqueue_style(
            'kunaal-about-page-v22',
            KUNAAL_THEME_URI . '/assets/css/about-page-v22.css',
            array('kunaal-theme-style'),
            kunaal_asset_version('assets/css/about-page-v22.css')
        );
        
        // GSAP Core (required for ScrollTrigger) - Load in footer to avoid blocking render
        wp_enqueue_script(
            'gsap-core',
            'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
            array(),
            '3.12.5',
            true // Load in footer to avoid blocking render
        );
        
        // GSAP ScrollTrigger Plugin
        wp_enqueue_script(
            'gsap-scrolltrigger',
            'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js',
            array('gsap-core'),
            '3.12.5',
            true // Load in footer
        );
        
        // GSAP registration moved to after about-page-v22 loads to ensure proper order
        
        // D3.js for world map
        wp_enqueue_script(
            'd3-js',
            'https://d3js.org/d3.v7.min.js',
            array(),
            '7.0.0',
            true
        );
        
        // TopoJSON for world map
        wp_enqueue_script(
            'topojson-js',
            'https://unpkg.com/topojson-client@3',
            array('d3-js'),
            '3.0.0',
            true
        );
        
        // About page V22 JS (depends on GSAP, D3, TopoJSON)
        wp_enqueue_script(
            'kunaal-about-page-v22',
            KUNAAL_THEME_URI . '/assets/js/about-page-v22.js',
            array('gsap-scrolltrigger', 'd3-js', 'topojson-js'),
            kunaal_asset_version('assets/js/about-page-v22.js'),
            true
        );
        
        // Localize script with places data for map and debug config
        $places = kunaal_get_places_v22();
        wp_localize_script('kunaal-about-page-v22', 'kunaalAboutV22', array(
            'places' => $places,
            'debug' => defined('WP_DEBUG') && WP_DEBUG, // Gate debug logging behind WP_DEBUG
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kunaal_debug_log_nonce'),
        ));
        
        // GSAP registration is handled inside about-page-v22.js after checking if GSAP is available
        // No separate inline script needed - the JS file already has proper GSAP checks
    }
}
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
        'kunaal-about-page-v22', // Fixed: match actual script handle
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

/**
 * Consolidated Block Editor Assets
 * Enqueues all editor assets in one place to avoid conflicts
 */
function kunaal_enqueue_editor_assets() {
    // Editor sidebar (only on essay/jotting edit screens)
    $screen = get_current_screen();
    if ($screen && in_array($screen->post_type, array('essay', 'jotting'))) {
        wp_enqueue_script(
            'kunaal-editor-sidebar',
            KUNAAL_THEME_URI . '/assets/js/editor-sidebar.js',
            array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-block-editor', 'wp-api-fetch'),
            KUNAAL_THEME_VERSION,
            true
        );
        
        // Localize editor sidebar script with constants
        wp_localize_script('kunaal-editor-sidebar', 'kunaalEditor', array(
            'readingSpeedWpm' => defined('KUNAAL_READING_SPEED_WPM') ? KUNAAL_READING_SPEED_WPM : 200,
        ));
        
        // Add some CSS for the sidebar
        wp_add_inline_style('wp-edit-post', '
            .kunaal-field-missing input {
                border-color: #d63638 !important;
                box-shadow: 0 0 0 1px #d63638 !important;
            }
            .kunaal-field-missing-btn {
                background: #d63638 !important;
                border-color: #d63638 !important;
            }
        ');
        
        // Enqueue color picker component
        wp_enqueue_script(
            'kunaal-color-picker',
            KUNAAL_THEME_URI . '/assets/js/components/color-picker.js',
            array('wp-element', 'wp-components', 'wp-i18n'),
            KUNAAL_THEME_VERSION,
            true
        );
        
        wp_enqueue_style(
            'kunaal-color-picker',
            KUNAAL_THEME_URI . '/assets/js/components/color-picker.css',
            array(),
            KUNAAL_THEME_VERSION
        );
        
        // Enqueue presets system
        wp_enqueue_script(
            'kunaal-presets',
            KUNAAL_THEME_URI . '/assets/js/presets.js',
            array(),
            KUNAAL_THEME_VERSION,
            true
        );
    }
    
    // Block editor assets (fonts and styles for all blocks)
    if ($screen && method_exists($screen, 'is_block_editor') && $screen->is_block_editor()) {
        // Caveat font for sidenote block preview
        wp_enqueue_style(
            'kunaal-caveat-editor',
            'https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600&display=swap',
            array(),
            null
        );
        
        // Theme's main stylesheet for block previews
        wp_enqueue_style(
            'kunaal-blocks-editor',
            KUNAAL_THEME_URI . '/style.css',
            array(),
            KUNAAL_THEME_VERSION
        );
        
        // Inline formats
        wp_enqueue_script('kunaal-inline-formats');
        wp_enqueue_style('kunaal-inline-formats-style');
    }
}
add_action('enqueue_block_editor_assets', 'kunaal_enqueue_editor_assets');

/**
 * Register Custom Post Types
 */
function kunaal_register_post_types() {
    // Essay Post Type
    register_post_type('essay', array(
        'labels' => array(
            'name' => 'Essays',
            'singular_name' => 'Essay',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Essay',
            'edit_item' => 'Edit Essay',
            'new_item' => 'New Essay',
            'view_item' => 'View Essay',
            'search_items' => 'Search Essays',
            'not_found' => 'No essays found',
            'not_found_in_trash' => 'No essays found in trash',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'essays', 'with_front' => false),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
        'menu_icon' => 'dashicons-media-document',
        'show_in_rest' => true,
        'template' => array(
            array('core/paragraph', array('placeholder' => 'Start writing your essay...')),
        ),
    ));

    // Jotting Post Type
    register_post_type('jotting', array(
        'labels' => array(
            'name' => 'Jottings',
            'singular_name' => 'Jotting',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Jotting',
            'edit_item' => 'Edit Jotting',
            'new_item' => 'New Jotting',
            'view_item' => 'View Jotting',
            'search_items' => 'Search Jottings',
            'not_found' => 'No jottings found',
            'not_found_in_trash' => 'No jottings found in trash',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'jottings', 'with_front' => false),
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
        'menu_icon' => 'dashicons-edit',
        'show_in_rest' => true,
    ));

    /**
     * Subscriber (private)
     * Built-in subscribe flow stores subscribers as private posts for easy export and admin visibility,
     * without exposing anything publicly.
     */
    register_post_type('kunaal_subscriber', array(
        'labels' => array(
            'name' => 'Subscribers',
            'singular_name' => 'Subscriber',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'options-general.php',
        'supports' => array('title'),
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'rewrite' => false,
        'query_var' => false,
        'show_in_rest' => false,
        'menu_icon' => 'dashicons-email',
    ));

    // Topic Taxonomy
    register_taxonomy('topic', array('essay', 'jotting'), array(
        'labels' => array(
            'name' => 'Topics',
            'singular_name' => 'Topic',
            'search_items' => 'Search Topics',
            'all_items' => 'All Topics',
            'edit_item' => 'Edit Topic',
            'update_item' => 'Update Topic',
            'add_new_item' => 'Add New Topic',
            'new_item_name' => 'New Topic Name',
            'menu_name' => 'Topics',
        ),
        'hierarchical' => false,
        'show_admin_column' => true,
        'rewrite' => array('slug' => 'topic'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'kunaal_register_post_types');

/**
 * Add Meta Boxes for Essays and Jottings
 */
/**
 * Register Meta Boxes for Classic Editor fallback only
 * Gutenberg uses the JavaScript sidebar plugin instead
 */
function kunaal_add_meta_boxes() {
    // Only add meta boxes if NOT using Gutenberg
    if (function_exists('use_block_editor_for_post_type')) {
        $screen = get_current_screen();
        $post_type = ($screen && isset($screen->post_type)) ? $screen->post_type : '';
        if ($post_type && use_block_editor_for_post_type($post_type)) {
            return; // Gutenberg is active, use JS sidebar instead
        }
    }
    
    add_meta_box(
        'kunaal_essay_details',
        'Essay Details (Required)',
        'kunaal_essay_meta_box_callback',
        'essay',
        'side',
        'high'
    );

    add_meta_box(
        'kunaal_jotting_details',
        'Jotting Details',
        'kunaal_jotting_meta_box_callback',
        'jotting',
        'side',
        'high'
    );
    
    add_meta_box(
        'kunaal_card_image',
        'Card Image (Required for Essays)',
        'kunaal_card_image_meta_box_callback',
        'essay',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'kunaal_add_meta_boxes');

/**
 * Essay Meta Box
 */
function kunaal_essay_meta_box_callback($post) {
    wp_nonce_field('kunaal_save_meta', 'kunaal_meta_nonce');
    $subtitle = get_post_meta($post->ID, 'kunaal_subtitle', true);
    $read_time = get_post_meta($post->ID, 'kunaal_read_time', true);
    ?>
    <p>
        <label for="kunaal_subtitle"><strong>Subtitle/Dek</strong></label><br>
        <input type="text" id="kunaal_subtitle" name="kunaal_subtitle" value="<?php echo esc_attr($subtitle); ?>" style="width:100%;" />
        <span style="font-size:11px;color:#666;">Appears below the title on cards and article header</span>
    </p>
    <?php if ($read_time) : ?>
    <p style="background:#e7f5ff;padding:8px;border-left:3px solid #1E5AFF;font-size:12px;">
        <strong>Reading Time:</strong> <?php echo esc_html($read_time); ?> minutes (auto-calculated)
    </p>
    <?php endif; ?>
    <p style="background:#fff3cd;padding:8px;border-left:3px solid #ffc107;font-size:11px;">
        <strong>Note:</strong> Essays require at least one Topic tag and a Card Image to be published. Reading time is calculated automatically based on word count.
    </p>
    <?php
}

/**
 * Jotting Meta Box
 */
function kunaal_jotting_meta_box_callback($post) {
    wp_nonce_field('kunaal_save_meta', 'kunaal_meta_nonce');
    $subtitle = get_post_meta($post->ID, 'kunaal_subtitle', true);
    ?>
    <p>
        <label for="kunaal_jotting_subtitle"><strong>Subtitle/Description</strong></label><br>
        <input type="text" id="kunaal_jotting_subtitle" name="kunaal_subtitle" value="<?php echo esc_attr($subtitle); ?>" style="width:100%;" />
        <span style="font-size:11px;color:#666;">Short description shown in the jottings list</span>
    </p>
    <?php
}

/**
 * Card Image Meta Box
 */
function kunaal_card_image_meta_box_callback($post) {
    $card_image = get_post_meta($post->ID, 'kunaal_card_image', true);
    $card_image_url = $card_image ? wp_get_attachment_image_url($card_image, 'medium') : '';
    ?>
    <div id="kunaal-card-image-container">
        <?php if ($card_image_url) : ?>
            <img src="<?php echo esc_url($card_image_url); ?>" alt="<?php echo esc_attr__('Card preview', 'kunaal-theme'); ?>" style="max-width:100%;margin-bottom:10px;" />
        <?php endif; ?>
    </div>
    <input type="hidden" id="kunaal_card_image" name="kunaal_card_image" value="<?php echo esc_attr($card_image); ?>" />
    <button type="button" class="button" id="kunaal-upload-card-image">Select Card Image</button>
    <?php if ($card_image) : ?>
        <button type="button" class="button" id="kunaal-remove-card-image">Remove</button>
    <?php endif; ?>
    <p style="font-size:11px;color:#666;margin-top:8px;">
        This image appears on the card grid. Recommended: 4:5 aspect ratio (e.g., 800x1000px).
        Can be different from the hero image in the post.
    </p>
    <script>
    jQuery(document).ready(function($) {
        var frame;
        $('#kunaal-upload-card-image').on('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Select Card Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#kunaal_card_image').val(attachment.id);
                $('#kunaal-card-image-container').html('<img src="' + attachment.url + '" style="max-width:100%;margin-bottom:10px;" />');
                if ($('#kunaal-remove-card-image').length === 0) {
                    $('#kunaal-upload-card-image').after('<button type="button" class="button" id="kunaal-remove-card-image">Remove</button>');
                }
            });
            frame.open();
        });
        $(document).on('click', '#kunaal-remove-card-image', function(e) {
            e.preventDefault();
            $('#kunaal_card_image').val('');
            $('#kunaal-card-image-container').html('');
            $(this).remove();
        });
    });
    </script>
    <?php
}

/**
 * Calculate Reading Time Automatically
 * Based on word count at configurable words per minute
 *
 * @param int $post_id Post ID
 * @return int Reading time in minutes
 */
function kunaal_calculate_reading_time($post_id) {
    $content = get_post_field('post_content', $post_id);
    
    // Strip shortcodes and HTML tags
    $content = strip_shortcodes($content);
    $content = wp_strip_all_tags($content);
    
    // Count words
    $word_count = str_word_count($content);
    
    // Calculate reading time using constant
    $wpm = defined('KUNAAL_READING_SPEED_WPM') ? KUNAAL_READING_SPEED_WPM : 200;
    return max(1, ceil($word_count / $wpm));
}

/**
 * Save Meta Box Data
 */
function kunaal_save_meta_box_data($post_id) {
    if (!isset($_POST['kunaal_meta_nonce']) || !wp_verify_nonce($_POST['kunaal_meta_nonce'], 'kunaal_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['kunaal_subtitle'])) {
        update_post_meta($post_id, 'kunaal_subtitle', sanitize_text_field($_POST['kunaal_subtitle']));
    }
    
    // Auto-calculate reading time for essays
    $post_type = get_post_type($post_id);
    if ($post_type === 'essay') {
        $reading_time = kunaal_calculate_reading_time($post_id);
        update_post_meta($post_id, 'kunaal_read_time', $reading_time);
    }
    
    if (isset($_POST['kunaal_card_image'])) {
        update_post_meta($post_id, 'kunaal_card_image', absint($_POST['kunaal_card_image']));
    }
}
add_action('save_post', 'kunaal_save_meta_box_data');

/**
 * Register meta fields for REST API access (needed for Gutenberg)
 */
function kunaal_register_meta_fields() {
    register_post_meta('essay', 'kunaal_read_time', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_post_meta('essay', 'kunaal_subtitle', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_post_meta('essay', 'kunaal_card_image', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_post_meta('essay', 'kunaal_summary', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_post_meta('jotting', 'kunaal_subtitle', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
    
    register_post_meta('jotting', 'kunaal_summary', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        }
    ));
}
add_action('init', 'kunaal_register_meta_fields');

/**
 * Validate Essay Before Publish (REST API compatible for Gutenberg)
 */
function kunaal_validate_essay_rest($prepared_post, $request) {
    // Only validate essays being published
    if ($prepared_post->post_type !== 'essay') {
        return $prepared_post;
    }
    
    if ($prepared_post->post_status !== 'publish') {
        return $prepared_post;
    }
    
    $post_id = isset($prepared_post->ID) ? $prepared_post->ID : 0;
    $errors = array();
    $meta = $request->get_param('meta');
    
    // Check for subtitle (now required)
    $subtitle = null;
    if (isset($meta['kunaal_subtitle']) && !empty($meta['kunaal_subtitle'])) {
        $subtitle = $meta['kunaal_subtitle'];
    } elseif ($post_id) {
        $subtitle = get_post_meta($post_id, 'kunaal_subtitle', true);
    }
    
    if (empty($subtitle)) {
        $errors[] = 'ðŸ“ SUBTITLE/DEK is required â€” Find "Essay Details" in the right sidebar';
    }
    
    // Check for read time
    $read_time = null;
    if (isset($meta['kunaal_read_time']) && !empty($meta['kunaal_read_time'])) {
        $read_time = $meta['kunaal_read_time'];
    } elseif ($post_id) {
        $read_time = get_post_meta($post_id, 'kunaal_read_time', true);
    }
    
    if (empty($read_time)) {
        $errors[] = 'â±ï¸ READ TIME is required â€” Find "Essay Details" in the right sidebar';
    }
    
    // Check for topics
    $topic_terms = $request->get_param('topic');
    $has_topics = false;
    
    if (!empty($topic_terms)) {
        $has_topics = true;
    } elseif ($post_id) {
        $existing_topics = get_the_terms($post_id, 'topic');
        if (!empty($existing_topics) && !is_wp_error($existing_topics)) {
            $has_topics = true;
        }
    }
    
    if (!$has_topics) {
        $errors[] = 'ðŸ·ï¸ At least one TOPIC is required â€” Find "Topics" in the right sidebar';
    }
    
    // Check for card image or featured image
    $featured_media = $request->get_param('featured_media');
    $card_image = null;
    if (isset($meta['kunaal_card_image']) && !empty($meta['kunaal_card_image'])) {
        $card_image = $meta['kunaal_card_image'];
    } elseif ($post_id) {
        $card_image = get_post_meta($post_id, 'kunaal_card_image', true);
    }
    
    $has_image = !empty($card_image) || !empty($featured_media) || ($post_id && has_post_thumbnail($post_id));
    
    if (!$has_image) {
        $errors[] = 'ðŸ–¼ï¸ A CARD IMAGE is required â€” Find "Card Image" or "Featured Image" in the right sidebar';
    }
    
    if (!empty($errors)) {
        return new WP_Error(
            'kunaal_essay_incomplete',
            "ðŸ“ ESSAY CANNOT BE PUBLISHED YET\n\nPlease complete these required fields:\n\n" . implode("\n\n", $errors),
            array('status' => 400)
        );
    }
    
    return $prepared_post;
}
add_filter('rest_pre_insert_essay', 'kunaal_validate_essay_rest', 10, 2);

/**
 * Validate Jotting Before Publish (REST API compatible for Gutenberg)
 */
function kunaal_validate_jotting_rest($prepared_post, $request) {
    // Only validate jottings being published
    if ($prepared_post->post_type !== 'jotting') {
        return $prepared_post;
    }
    
    if ($prepared_post->post_status !== 'publish') {
        return $prepared_post;
    }
    
    $post_id = isset($prepared_post->ID) ? $prepared_post->ID : 0;
    $errors = array();
    $meta = $request->get_param('meta');
    
    // Check for subtitle (required for jottings)
    $subtitle = null;
    if (isset($meta['kunaal_subtitle']) && !empty($meta['kunaal_subtitle'])) {
        $subtitle = $meta['kunaal_subtitle'];
    } elseif ($post_id) {
        $subtitle = get_post_meta($post_id, 'kunaal_subtitle', true);
    }
    
    if (empty($subtitle)) {
        $errors[] = 'ðŸ“ SUBTITLE/DEK is required â€” Find "Jotting Details" in the right sidebar';
    }
    
    if (!empty($errors)) {
        return new WP_Error(
            'kunaal_jotting_incomplete',
            "ðŸ“ JOTTING CANNOT BE PUBLISHED YET\n\nPlease complete these required fields:\n\n" . implode("\n\n", $errors),
            array('status' => 400)
        );
    }
    
    return $prepared_post;
}
add_filter('rest_pre_insert_jotting', 'kunaal_validate_jotting_rest', 10, 2);

/**
 * Also validate on classic editor saves (non-Gutenberg)
 */
function kunaal_validate_essay_classic($data, $postarr) {
    // Skip if this is a REST API request (handled by rest_pre_insert_essay)
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return $data;
    }
    
    if ($data['post_type'] !== 'essay' || $data['post_status'] !== 'publish') {
        return $data;
    }
    
    $post_id = isset($postarr['ID']) ? $postarr['ID'] : 0;
    $errors = array();
    
    // Check read time
    $read_time = isset($_POST['kunaal_read_time']) ? $_POST['kunaal_read_time'] : '';
    if (empty($read_time) && $post_id) {
        $read_time = get_post_meta($post_id, 'kunaal_read_time', true);
    }
    if (empty($read_time)) {
        $errors[] = 'Read Time is required (Essay Details box)';
    }
    
    // Check topics
    $topics = isset($_POST['tax_input']['topic']) ? $_POST['tax_input']['topic'] : array();
    if (empty(array_filter($topics)) && $post_id) {
        $existing = get_the_terms($post_id, 'topic');
        if (empty($existing) || is_wp_error($existing)) {
            $errors[] = 'At least one Topic is required';
        }
    } elseif (empty(array_filter($topics)) && !$post_id) {
        $errors[] = 'At least one Topic is required';
    }
    
    // Check image
    $card_image = isset($_POST['kunaal_card_image']) ? $_POST['kunaal_card_image'] : '';
    if (empty($card_image) && $post_id) {
        $card_image = get_post_meta($post_id, 'kunaal_card_image', true);
    }
    $featured = isset($_POST['_thumbnail_id']) ? $_POST['_thumbnail_id'] : '';
    if (empty($featured) && $post_id) {
        $featured = get_post_thumbnail_id($post_id);
    }
    
    if (empty($card_image) && empty($featured)) {
        $errors[] = 'A Card Image or Featured Image is required';
    }
    
    if (!empty($errors)) {
        // Revert to draft
        $data['post_status'] = 'draft';
        set_transient('kunaal_essay_errors_' . ($post_id ?: 'new'), $errors, 60);
    }
    
    return $data;
}
add_filter('wp_insert_post_data', 'kunaal_validate_essay_classic', 10, 2);

/**
 * Display validation errors in admin (classic editor)
 */
function kunaal_display_essay_errors() {
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'essay') {
        return;
    }
    
    global $post;
    $post_id = $post ? $post->ID : 'new';
    $errors = get_transient('kunaal_essay_errors_' . $post_id);
    
    if ($errors) {
        delete_transient('kunaal_essay_errors_' . $post_id);
        echo '<div class="notice notice-error">';
        echo '<p><strong>âš ï¸ Essay could not be published. Please complete these required fields:</strong></p>';
        echo '<ul style="margin-left:20px;list-style:disc;">';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul></div>';
    }
}
add_action('admin_notices', 'kunaal_display_essay_errors');

/**
 * Customizer Settings
 */
/**
 * Register all Customizer sections and controls
 * Delegates to helper functions to reduce complexity
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register($wp_customize) {
    kunaal_customize_register_author_section($wp_customize);
    kunaal_customize_register_sharing_section($wp_customize);
    kunaal_customize_register_site_identity($wp_customize);
    kunaal_customize_register_subscribe_section($wp_customize);
    kunaal_customize_register_contact_page_section($wp_customize);
    kunaal_customize_register_email_delivery_section($wp_customize);
    kunaal_customize_register_contact_social_links($wp_customize);
}
add_action('customize_register', 'kunaal_customize_register');

/**
 * Enqueue Customizer Preview Script
 * 
 * Loads JavaScript that handles live preview updates without page refresh.
 * Uses debouncing to prevent excessive updates while typing.
 */
function kunaal_customizer_preview_js() {
    wp_enqueue_script(
        'kunaal-customizer-preview',
        KUNAAL_THEME_URI . '/assets/js/customizer-preview.js',
        array('jquery', 'customize-preview'),
        KUNAAL_THEME_VERSION,
        true
    );
}
add_action('customize_preview_init', 'kunaal_customizer_preview_js');

// Removed: kunaal_build_messenger_target_url and kunaal_qr_img_src - no longer used (messenger QR codes removed)

// Removed: /connect/<slug> redirects - no longer used (messenger QR codes removed)

/**
 * Consolidated theme activation handler
 * Runs all activation tasks in proper order
 */
function kunaal_theme_activation_handler() {
    // 1. Register post types and rewrite rules
    kunaal_register_post_types();
    flush_rewrite_rules();
    
    // 2. Create default pages
    if (!get_page_by_path('about')) {
        wp_insert_post(array(
            'post_title' => 'About',
            'post_name' => 'about',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- wp:paragraph --><p>Write about yourself here.</p><!-- /wp:paragraph -->',
        ));
    }
    
    if (!get_page_by_path('contact')) {
        wp_insert_post(array(
            'post_title' => 'Contact',
            'post_name' => 'contact',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- wp:paragraph --><p>Add your contact information here.</p><!-- /wp:paragraph -->',
        ));
    }
    
    // 3. Set reading settings (only if not already set)
    $current_front = get_option('show_on_front');
    if (empty($current_front)) {
        update_option('show_on_front', 'posts');
    }
}
add_action('after_switch_theme', 'kunaal_theme_activation_handler');

/**
 * Clean up transients on theme deactivation
 */
function kunaal_theme_deactivation_handler() {
    global $wpdb;
    
    // Delete all rate limiting transients
    $wpdb->query(
        "DELETE FROM {$wpdb->options} 
         WHERE option_name LIKE '_transient_kunaal_contact_rl_%' 
         OR option_name LIKE '_transient_timeout_kunaal_contact_rl_%'"
    );
    
    // Delete all error transients
    $wpdb->query(
        "DELETE FROM {$wpdb->options} 
         WHERE option_name LIKE '_transient_kunaal_essay_errors_%' 
         OR option_name LIKE '_transient_timeout_kunaal_essay_errors_%'"
    );
}
add_action('switch_theme', 'kunaal_theme_deactivation_handler');

// Helper functions are now defined in inc/helpers.php

/**
 * Email delivery (SMTP)
 * Configures PHPMailer if enabled in Customizer.
 */
function kunaal_smtp_is_enabled() {
    return (bool) kunaal_mod('kunaal_smtp_enabled', false);
}

add_filter('wp_mail_from', function ($from_email) {
    if (!kunaal_smtp_is_enabled()) {
        return $from_email;
    }
    $custom = kunaal_mod('kunaal_smtp_from_email', '');
    return is_email($custom) ? $custom : $from_email;
});

add_filter('wp_mail_from_name', function ($from_name) {
    if (!kunaal_smtp_is_enabled()) {
        return $from_name;
    }
    $custom = kunaal_mod('kunaal_smtp_from_name', '');
    return !empty($custom) ? $custom : $from_name;
});

add_action('phpmailer_init', function ($phpmailer) {
    if (!kunaal_smtp_is_enabled()) {
        return;
    }

    $host = trim((string) kunaal_mod('kunaal_smtp_host', ''));
    $user = (string) kunaal_mod('kunaal_smtp_username', '');
    $pass = (string) kunaal_mod('kunaal_smtp_password', '');
    $port = (int) kunaal_mod('kunaal_smtp_port', 587);
    $enc  = (string) kunaal_mod('kunaal_smtp_encryption', 'tls');

    if (empty($host) || empty($user) || empty($pass) || $port <= 0) {
        // Fail-safe: do not partially configure SMTP.
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host = $host;
    $phpmailer->Port = $port;
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $user;
    $phpmailer->Password = $pass;
    $phpmailer->SMTPAutoTLS = true;

    if ($enc === 'ssl') {
        $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($enc === 'tls') {
        $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $phpmailer->SMTPSecure = '';
    }
});

/**
 * Built-in Subscribe Flow
 * - Stores subscriber as private post
 * - Sends confirmation email with one-time token
 * - Confirmation endpoint: ?kunaal_sub_confirm=<token>
 */
function kunaal_find_subscriber_by_email($email) {
    $email = strtolower(trim($email));
    if (!is_email($email)) {
        return 0;
    }
    $q = new WP_Query(array(
        'post_type' => 'kunaal_subscriber',
        'post_status' => 'private',
        'fields' => 'ids',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'kunaal_email',
                'value' => $email,
                'compare' => '=',
            ),
        ),
        'no_found_rows' => true,
    ));
    return !empty($q->posts) ? (int) $q->posts[0] : 0;
}

function kunaal_generate_subscribe_token() {
    return wp_generate_password(32, false, false);
}

function kunaal_send_subscribe_confirmation($email, $token) {
    $to = $email;
    $site = get_bloginfo('name');
    $confirm_url = add_query_arg(array('kunaal_sub_confirm' => $token), home_url('/'));
    $subject = '[' . $site . '] Confirm your subscription';
    $body = "Hi!\n\nPlease confirm your subscription by clicking the link below:\n\n" . esc_url_raw($confirm_url) . "\n\nIf you didn't request this, you can ignore this email.\n";
    return wp_mail($to, $subject, $body);
}

/**
 * Validate subscribe request nonce and mode
 *
 * @return array|WP_Error Returns error array on failure, null on success
 */
function kunaal_validate_subscribe_request() {
        if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_theme_nonce')) {
        return array('message' => 'Security check failed. Please refresh and try again.');
        }

        $mode = kunaal_mod('kunaal_subscribe_mode', 'builtin');
        if ($mode === 'external') {
        return array('message' => 'Subscribe is configured for an external provider.');
    }

    return null;
}

/**
 * Validate and sanitize email from POST data
 *
 * @return string|WP_Error Valid email address or error
 */
function kunaal_validate_subscribe_email() {
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Please enter a valid email address.');
    }
    return strtolower(trim($email));
}

/**
 * Handle existing subscriber response
 *
 * @param int $subscriber_id Subscriber post ID
 * @return bool True if response sent, false otherwise
 */
function kunaal_handle_existing_subscriber($subscriber_id) {
    $status = get_post_meta($subscriber_id, 'kunaal_status', true);
            if ($status === 'confirmed') {
                wp_send_json_success(array('message' => 'You are already subscribed.'));
            } else {
                wp_send_json_success(array('message' => 'Check your inbox to confirm your subscription.'));
            }
            wp_die();
    return true;
}

/**
 * Create new subscriber post with meta
 *
 * @param string $email Subscriber email
 * @param string $token Confirmation token
 * @return int|WP_Error Subscriber post ID or error
 */
function kunaal_create_subscriber_post($email, $token) {
        $subscriber_id = wp_insert_post(array(
            'post_type' => 'kunaal_subscriber',
            'post_status' => 'private',
            'post_title' => $email,
        ), true);

        if (is_wp_error($subscriber_id) || empty($subscriber_id)) {
        return new WP_Error('create_failed', 'Unable to create subscription. Please try again.');
        }

        update_post_meta($subscriber_id, 'kunaal_email', $email);
        update_post_meta($subscriber_id, 'kunaal_status', 'pending');
        update_post_meta($subscriber_id, 'kunaal_token', $token);
        update_post_meta($subscriber_id, 'kunaal_created_gmt', gmdate('c'));

    return $subscriber_id;
}

/**
 * Main subscribe handler - refactored to reduce cognitive complexity
 */
function kunaal_handle_subscribe() {
    try {
        // Validate request
        $validation_error = kunaal_validate_subscribe_request();
        if ($validation_error) {
            wp_send_json_error($validation_error);
            wp_die();
        }

        // Validate email
        $email = kunaal_validate_subscribe_email();
        if (is_wp_error($email)) {
            wp_send_json_error(array('message' => $email->get_error_message()));
            wp_die();
        }

        // Check for existing subscriber
        $existing_id = kunaal_find_subscriber_by_email($email);
        if ($existing_id) {
            kunaal_handle_existing_subscriber($existing_id);
            return;
        }

        // Create new subscriber
        $token = kunaal_generate_subscribe_token();
        $subscriber_id = kunaal_create_subscriber_post($email, $token);
        if (is_wp_error($subscriber_id)) {
            wp_send_json_error(array('message' => $subscriber_id->get_error_message()));
            wp_die();
        }

        // Send confirmation email
        $sent = kunaal_send_subscribe_confirmation($email, $token);
        if (!$sent) {
            wp_send_json_error(array('message' => 'Unable to send confirmation email. Please try again later.'));
            wp_die();
        }

        wp_send_json_success(array('message' => 'Check your inbox to confirm your subscription.'));
        wp_die();
    } catch (Exception $e) {
        kunaal_theme_log('Subscribe error', array('error' => $e->getMessage()));
        wp_send_json_error(array('message' => KUNAAL_ERROR_MESSAGE_GENERIC));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_subscribe', 'kunaal_handle_subscribe');
add_action('wp_ajax_nopriv_kunaal_subscribe', 'kunaal_handle_subscribe');

function kunaal_handle_subscribe_confirmation_request() {
    if (empty($_GET['kunaal_sub_confirm'])) {
        return;
    }
    $token = sanitize_text_field(wp_unslash($_GET['kunaal_sub_confirm']));
    if (empty($token)) {
        return;
    }

    $q = new WP_Query(array(
        'post_type' => 'kunaal_subscriber',
        'post_status' => 'private',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'meta_query' => array(
            array(
                'key' => 'kunaal_token',
                'value' => $token,
                'compare' => '=',
            ),
        ),
    ));

    if (empty($q->posts)) {
        wp_die('Invalid or expired confirmation link.', 'Subscription', array('response' => 400));
    }

    $post = $q->posts[0];
    update_post_meta($post->ID, 'kunaal_status', 'confirmed');
    delete_post_meta($post->ID, 'kunaal_token');

    // Notify admin (optional)
    $notify = kunaal_mod('kunaal_subscribe_notify_email', get_option('admin_email'));
    if (is_email($notify)) {
        $site = get_bloginfo('name');
        $email = get_post_meta($post->ID, 'kunaal_email', true);
        wp_mail($notify, '[' . $site . '] New subscriber', "New subscriber confirmed:\n\n" . $email . "\n");
    }

    wp_die('Subscription confirmed. Thank you!', 'Subscription', array('response' => 200));
}
add_action('template_redirect', 'kunaal_handle_subscribe_confirmation_request');

// Helper functions are now defined in inc/helpers.php

/**
 * Helper: Validate filter request nonce
 */
function kunaal_validate_filter_request() {
    if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_theme_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed. Please refresh the page and try again.'));
        wp_die();
    }
}

/**
 * Helper: Parse and sanitize topics from POST data
 */
function kunaal_parse_filter_topics() {
    $topics = array();
    if (isset($_POST['topics'])) {
        $topics_raw = $_POST['topics'];
        if (is_array($topics_raw)) {
            $topics = array_filter(array_map('sanitize_text_field', $topics_raw));
        } elseif (is_string($topics_raw) && !empty($topics_raw)) {
            $topics = array_filter(array_map('sanitize_text_field', explode(',', $topics_raw)));
        }
    }
    return $topics;
}

/**
 * Helper: Build WP_Query args for filter
 */
function kunaal_build_filter_query_args($post_type, $topics, $sort, $search, $page, $per_page) {
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => min($per_page, 100), // Limit to prevent DoS
        'paged' => $page,
        'post_status' => 'publish',
    );
    
    // Topics filter
    if (!empty($topics)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'topic',
                'field' => 'slug',
                'terms' => $topics,
            ),
        );
    }
    
    // Sort
    switch ($sort) {
        case 'old':
            $args['orderby'] = 'date';
            $args['order'] = 'ASC';
            break;
        case 'title':
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            break;
        default: // new (newest first)
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }
    
    // Search
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    return $args;
}

/**
 * Helper: Prime caches to prevent N+1 queries
 */
function kunaal_prime_post_caches($post_ids) {
    if (empty($post_ids)) {
        return;
    }
    
    // Ensure WordPress functions are available
    if (!function_exists('update_post_meta_cache')) {
        require_once(ABSPATH . 'wp-admin/includes/post.php');
    }
    if (!function_exists('update_object_term_cache')) {
        require_once(ABSPATH . 'wp-includes/taxonomy.php');
    }
    
    if (function_exists('update_post_meta_cache')) {
        update_post_meta_cache($post_ids);
    }
    if (function_exists('update_object_term_cache')) {
        update_object_term_cache($post_ids, array('essay', 'jotting'));
    }
}

/**
 * Helper: Extract topics from post
 */
function kunaal_extract_post_topics($post_id) {
    $topics_list = get_the_terms($post_id, 'topic');
    $tags = array();
    $tag_slugs = array();
    
    if ($topics_list && !is_wp_error($topics_list)) {
        foreach ($topics_list as $topic) {
            $tags[] = $topic->name;
            $tag_slugs[] = $topic->slug;
        }
    }
    
    return array('tags' => $tags, 'tagSlugs' => $tag_slugs);
}

/**
 * Helper: Build post data array for JSON response
 */
function kunaal_build_post_data($post_id) {
    $topics = kunaal_extract_post_topics($post_id);
    
    return array(
        'id' => $post_id,
        'title' => get_the_title(),
        'url' => get_permalink(),
        'date' => get_the_date('j F Y'),
        'dateShort' => get_the_date('j M Y'),
        'subtitle' => get_post_meta($post_id, 'kunaal_subtitle', true),
        'readTime' => get_post_meta($post_id, 'kunaal_read_time', true),
        'image' => kunaal_get_card_image_url($post_id),
        'tags' => $topics['tags'],
        'tagSlugs' => $topics['tagSlugs'],
    );
}

/**
 * AJAX: Filter content
 */
function kunaal_filter_content() {
    try {
        kunaal_validate_filter_request();
        
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'essay';
        $topics = kunaal_parse_filter_topics();
        $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'new';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 12;
        
        $args = kunaal_build_filter_query_args($post_type, $topics, $sort, $search, $page, $per_page);
        $query = new WP_Query($args);
        $posts_data = array();
        
        if ($query->have_posts()) {
            $post_ids = wp_list_pluck($query->posts, 'ID');
            kunaal_prime_post_caches($post_ids);
            
            while ($query->have_posts()) {
                $query->the_post();
                $posts_data[] = kunaal_build_post_data(get_the_ID());
            }
        }
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'posts' => $posts_data,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'page' => $page,
        ));
        wp_die();
    } catch (Exception $e) {
        kunaal_theme_log('AJAX filter error', array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString()));
        wp_send_json_error(array('message' => KUNAAL_ERROR_MESSAGE_GENERIC));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_filter', 'kunaal_filter_content');
add_action('wp_ajax_nopriv_kunaal_filter', 'kunaal_filter_content');

/**
 * Enqueue media uploader in admin
 */
function kunaal_admin_scripts($hook) {
    if ('post.php' === $hook || 'post-new.php' === $hook) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'kunaal_admin_scripts');

// Theme activation tasks consolidated into kunaal_theme_activation_handler above

// Note: Block registration is now in inc/blocks.php (included at top of file)

/**
 * DK PDF functionality has been removed - using custom PDF generator instead
 * All DK PDF related code has been cleaned up
 */

/**
 * Add Open Graph Meta Tags for Social Sharing (LinkedIn, X/Twitter, Facebook)
 */
function kunaal_add_open_graph_tags() {
    if (!is_singular(array('essay', 'jotting', 'post', 'page'))) {
        return;
    }
    
    global $post;
    if (!$post) {
        return;
    }
    
    $title = get_the_title($post->ID);
    $url = get_permalink($post->ID);
    $site_name = get_bloginfo('name');
    $author_first = kunaal_mod('kunaal_author_first_name', 'Kunaal');
    $author_last = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
    $author_name = $author_first . ' ' . $author_last;
    
    // Get description
    $description = get_post_meta($post->ID, 'kunaal_subtitle', true);
    if (empty($description)) {
        $description = has_excerpt($post->ID) ? get_the_excerpt($post->ID) : wp_trim_words(strip_tags($post->post_content), 30);
    }
    $description = esc_attr($description);
    
    // Get image
    $image = '';
    $card_image = get_post_meta($post->ID, 'kunaal_card_image', true);
    if ($card_image) {
        $image = wp_get_attachment_image_url($card_image, 'large');
    } elseif (has_post_thumbnail($post->ID)) {
        $image = get_the_post_thumbnail_url($post->ID, 'large');
    }
    
    // Twitter handle
    $twitter_handle = kunaal_mod('kunaal_twitter_handle', '');
    
    ?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo esc_attr($title); ?>" />
    <meta property="og:description" content="<?php echo esc_attr($description); ?>" />
    <meta property="og:url" content="<?php echo esc_url($url); ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>" />
    <?php if ($image) : ?>
    <meta property="og:image" content="<?php echo esc_url($image); ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <?php endif; ?>
    <meta property="article:author" content="<?php echo esc_attr($author_name); ?>" />
    <meta property="article:published_time" content="<?php echo get_the_date('c', $post->ID); ?>" />
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>" />
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>" />
    <?php if ($image) : ?>
    <meta name="twitter:image" content="<?php echo esc_url($image); ?>" />
    <?php endif; ?>
    <?php if ($twitter_handle) : ?>
    <meta name="twitter:site" content="@<?php echo esc_attr($twitter_handle); ?>" />
    <meta name="twitter:creator" content="@<?php echo esc_attr($twitter_handle); ?>" />
    <?php endif; ?>
    
    <!-- LinkedIn-specific -->
    <meta property="og:locale" content="en_US" />
    <?php
}
add_action('wp_head', 'kunaal_add_open_graph_tags', 5);

/**
 * Helper: Validate contact form request
 */
function kunaal_validate_contact_request() {
    if (!isset($_POST['kunaal_contact_nonce']) || !wp_verify_nonce($_POST['kunaal_contact_nonce'], 'kunaal_contact_form')) {
        wp_send_json_error(array('message' => 'Security check failed. Please refresh and try again.'));
        wp_die();
    }
}

/**
 * Helper: Sanitize contact form inputs
 */
function kunaal_sanitize_contact_inputs() {
    return array(
        'name' => isset($_POST['contact_name']) ? sanitize_text_field($_POST['contact_name']) : '',
        'email' => isset($_POST['contact_email']) ? sanitize_email($_POST['contact_email']) : '',
        'message' => isset($_POST['contact_message']) ? sanitize_textarea_field($_POST['contact_message']) : '',
        'honeypot' => isset($_POST['contact_company']) ? sanitize_text_field($_POST['contact_company']) : '',
    );
}

/**
 * Helper: Check honeypot (bot detection)
 */
function kunaal_check_contact_honeypot($honeypot) {
    if (!empty($honeypot)) {
        wp_send_json_error(array('message' => 'Sorry, your message could not be sent.'));
        wp_die();
    }
}

/**
 * Helper: Get client IP address
 */
function kunaal_get_client_ip() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return sanitize_text_field(trim($forwarded[0]));
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        return sanitize_text_field($_SERVER['REMOTE_ADDR']);
    }
    return '';
}

/**
 * Helper: Check rate limit for contact form
 */
function kunaal_check_contact_rate_limit() {
    $ip = kunaal_get_client_ip();
    if (empty($ip)) {
        return; // Can't rate limit without IP
    }
    
    $rate_key = 'kunaal_contact_rl_' . wp_hash($ip);
    $count = (int) get_transient($rate_key);
    if ($count >= 5) {
        wp_send_json_error(array('message' => 'Please wait a bit before sending another message.'));
        wp_die();
    }
    set_transient($rate_key, $count + 1, 10 * MINUTE_IN_SECONDS);
}

/**
 * Helper: Validate contact form data
 */
function kunaal_validate_contact_data($message, $email) {
    if (empty($message)) {
        wp_send_json_error(array('message' => 'Please enter a message.'));
        wp_die();
    }
    
    if (!empty($email) && !is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
        wp_die();
    }
}

/**
 * Helper: Get contact form recipient email
 */
function kunaal_get_contact_recipient() {
    $to_email = kunaal_mod('kunaal_contact_recipient_email', get_option('admin_email'));
    if (!is_email($to_email)) {
        $to_email = get_option('admin_email');
    }
    return $to_email;
}

/**
 * Helper: Build contact form email
 */
function kunaal_build_contact_email($name, $email, $message) {
    $site_name = get_bloginfo('name');
    $sender_name = !empty($name) ? $name : 'Anonymous';
    $email_subject = '[' . $site_name . '] New note' . (!empty($name) ? ' from ' . $name : '');
    
    $email_body = "You received a new message from your site contact form.\n\n";
    if (!empty($name)) {
        $email_body .= "Name: {$name}\n";
    }
    if (!empty($email)) {
        $email_body .= "Email: {$email}\n";
    }
    $email_body .= "Page: " . esc_url_raw(wp_get_referer()) . "\n";
    $email_body .= "Time: " . gmdate('c') . " (UTC)\n\n";
    $email_body .= "Message:\n{$message}\n";
    
    $headers = array(
        'From: ' . $site_name . ' <' . get_option('admin_email') . '>',
    );
    if (!empty($email)) {
        $headers[] = 'Reply-To: ' . $sender_name . ' <' . $email . '>';
    }
    
    return array(
        'to' => kunaal_get_contact_recipient(),
        'subject' => $email_subject,
        'body' => $email_body,
        'headers' => $headers,
    );
}

/**
 * Helper: Handle contact form email error
 */
function kunaal_handle_contact_email_error($to_email, $email_subject) {
    global $phpmailer;
    $error_message = 'Sorry, there was an error sending your message.';
    
    if (isset($phpmailer) && is_object($phpmailer) && isset($phpmailer->ErrorInfo)) {
        kunaal_theme_log('Contact form wp_mail error', array('error' => $phpmailer->ErrorInfo, 'to' => $to_email));
        $error_message .= ' Please check your email configuration or try emailing directly.';
    } else {
        kunaal_theme_log('Contact form wp_mail failed', array('to' => $to_email, 'subject' => $email_subject));
    }
    
    wp_send_json_error(array('message' => $error_message));
    wp_die();
}

/**
 * Contact Form AJAX Handler
 */
function kunaal_handle_contact_form() {
    try {
        kunaal_validate_contact_request();
        
        $inputs = kunaal_sanitize_contact_inputs();
        kunaal_check_contact_honeypot($inputs['honeypot']);
        kunaal_check_contact_rate_limit();
        kunaal_validate_contact_data($inputs['message'], $inputs['email']);
        
        $email_data = kunaal_build_contact_email($inputs['name'], $inputs['email'], $inputs['message']);
        $sent = wp_mail($email_data['to'], $email_data['subject'], $email_data['body'], $email_data['headers']);
        
        if ($sent) {
            wp_send_json_success(array('message' => 'Thank you! Your message has been sent.'));
            wp_die();
        } else {
            kunaal_handle_contact_email_error($email_data['to'], $email_data['subject']);
        }
    } catch (Exception $e) {
        kunaal_theme_log('Contact form error', array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString()));
        wp_send_json_error(array('message' => KUNAAL_ERROR_MESSAGE_GENERIC));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_contact_form', 'kunaal_handle_contact_form');
add_action('wp_ajax_nopriv_kunaal_contact_form', 'kunaal_handle_contact_form');

/**
 * Helper: Validate debug log request
 */
function kunaal_validate_debug_log_request() {
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        wp_send_json_error(array('message' => 'Debug logging disabled'));
        wp_die();
    }
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_debug_log_nonce')) {
        wp_send_json_error(array('message' => 'Invalid nonce'));
        wp_die();
    }
    
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(array('message' => 'Insufficient permissions'));
        wp_die();
    }
}

/**
 * Helper: Get log data from POST
 */
function kunaal_get_debug_log_data() {
    $log_json = isset($_POST['log_data']) ? stripslashes($_POST['log_data']) : '';
    if (empty($log_json)) {
        $raw_input = file_get_contents('php://input');
        if (!empty($raw_input)) {
            $log_json = $raw_input;
        }
    }
    return $log_json;
}

/**
 * Helper: Validate log data structure
 */
function kunaal_validate_debug_log_data($log_data) {
    if (!$log_data || !isset($log_data['location']) || !isset($log_data['message'])) {
        wp_send_json_error(array('message' => 'Invalid log data'));
        wp_die();
    }
}

/**
 * Helper: Write log to file
 */
function kunaal_write_debug_log($log_data) {
    $log_file = get_template_directory() . '/debug.log';
    $log_line = json_encode($log_data) . "\n";
    @file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

/**
 * Debug log handler - receives logs from JavaScript and writes to theme debug.log
 * Only active during development/debugging (WP_DEBUG must be true)
 * Nonce-protected and capability-checked for security
 */
function kunaal_handle_debug_log() {
    kunaal_validate_debug_log_request();
    
    $log_json = kunaal_get_debug_log_data();
    $log_data = json_decode($log_json, true);
    
    kunaal_validate_debug_log_data($log_data);
    kunaal_write_debug_log($log_data);
    
    wp_send_json_success(array('logged' => true));
    wp_die();
}
// Only register handlers if WP_DEBUG is enabled
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('wp_ajax_kunaal_debug_log', 'kunaal_handle_debug_log');
    // Note: Removed nopriv handler - debug logging requires authentication
}

/**
 * Register Inline Formats for Gutenberg Editor
 */
function kunaal_register_inline_formats() {
    $formats_dir = KUNAAL_THEME_DIR . '/blocks/inline-formats';
    
    if (!file_exists($formats_dir . '/index.js')) {
        return;
    }
    
    wp_register_script(
        'kunaal-inline-formats',
        KUNAAL_THEME_URI . '/blocks/inline-formats/index.js',
        array('wp-rich-text', 'wp-block-editor', 'wp-element', 'wp-components'),
        KUNAAL_THEME_VERSION,
        true
    );
    
    wp_register_style(
        'kunaal-inline-formats-style',
        KUNAAL_THEME_URI . '/blocks/inline-formats/style.css',
        array(),
        KUNAAL_THEME_VERSION
    );
}
add_action('init', 'kunaal_register_inline_formats');

// Inline formats now enqueued in consolidated kunaal_enqueue_editor_assets above

/**
 * Enqueue Inline Format Styles on Frontend
 */
function kunaal_enqueue_inline_formats_frontend() {
    if (is_singular(array('essay', 'jotting', 'post', 'page'))) {
        wp_enqueue_style(
            'kunaal-inline-formats-style',
            KUNAAL_THEME_URI . '/blocks/inline-formats/style.css',
            array(),
            KUNAAL_THEME_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'kunaal_enqueue_inline_formats_frontend');

// Defer function consolidated above - removed duplicate

/**
 * ========================================
 * ABOUT PAGE V22 HELPER FUNCTIONS
 * ========================================
 */

/**
 * Get hero photos for v22 About page
 * Returns array of up to 10 photo URLs
 */
function kunaal_get_hero_photos_v22() {
    $photos = array();
    for ($i = 1; $i <= 10; $i++) {
        $photo_id = kunaal_mod("kunaal_about_v22_hero_photo_{$i}", 0);
        if ($photo_id) {
            $photo_url = wp_get_attachment_image_url($photo_id, 'large');
            if ($photo_url) {
                $photos[] = $photo_url;
            }
        }
    }
    return $photos;
}

/**
 * Get numbers data for v22 About page
 * Returns array of number items (up to 8) plus infinity option
 */
function kunaal_get_numbers_v22() {
    $numbers = array();
    for ($i = 1; $i <= 8; $i++) {
        $value = kunaal_mod("kunaal_about_v22_number_{$i}_value", '');
        $label = kunaal_mod("kunaal_about_v22_number_{$i}_label", '');
        if (!empty($value) && !empty($label)) {
            $numbers[] = array(
                'value' => $value,
                'suffix' => kunaal_mod("kunaal_about_v22_number_{$i}_suffix", ''),
                'label' => $label,
            );
        }
    }
    
    // Add infinity if enabled
    if (kunaal_mod('kunaal_about_v22_numbers_infinity_show', true)) {
        $infinity_label = kunaal_mod('kunaal_about_v22_numbers_infinity_label', 'Rabbit holes');
        if (!empty($infinity_label)) {
            $numbers[] = array(
                'value' => 'infinity',
                'suffix' => '',
                'label' => $infinity_label,
            );
        }
    }
    
    return $numbers;
}

/**
 * Get categories for v22 About page
 * Returns array of category definitions (up to 12)
 */
function kunaal_get_categories_v22() {
    $categories = array();
    for ($i = 1; $i <= 12; $i++) {
        $name = kunaal_mod("kunaal_about_v22_category_{$i}_name", '');
        if (!empty($name)) {
            $slug = sanitize_title($name);
            $categories[$slug] = array(
                'name' => $name,
                'color' => kunaal_mod("kunaal_about_v22_category_{$i}_color", '#7D6B5D'),
            );
        }
    }
    return $categories;
}

/**
 * Get rabbit holes for v22 About page
 * Returns array of rabbit hole items (up to 200)
 */
function kunaal_get_rabbit_holes_v22() {
    $rabbit_holes = array();
    for ($i = 1; $i <= 200; $i++) {
        $text = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_text", '');
        if (!empty($text)) {
            $image_id = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_image", 0);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
            $category = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_category", '');
            $url = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_url", '');
            
            $rabbit_holes[] = array(
                'image' => $image_url,
                'text' => $text,
                'category' => $category,
                'url' => $url,
            );
        }
    }
    return $rabbit_holes;
}

/**
 * Get panoramas for v22 About page
 * Returns array organized by position
 */
function kunaal_get_panoramas_v22() {
    $panoramas_by_position = array(
        'after_hero' => array(),
        'after_numbers' => array(),
        'after_rabbit_holes' => array(),
        'after_media' => array(),
        'after_map' => array(),
        'after_inspirations' => array(),
    );
    
    for ($i = 1; $i <= 10; $i++) {
        $position = kunaal_mod("kunaal_about_v22_panorama_{$i}_position", 'none');
        if ($position !== 'none' && isset($panoramas_by_position[$position])) {
            $image_id = kunaal_mod("kunaal_about_v22_panorama_{$i}_image", 0);
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                if ($image_url) {
                    $panoramas_by_position[$position][] = array(
                        'image' => $image_url,
                        'height' => kunaal_mod("kunaal_about_v22_panorama_{$i}_height", '140'),
                        'cut' => kunaal_mod("kunaal_about_v22_panorama_{$i}_cut", 'none'),
                        'bg' => kunaal_mod("kunaal_about_v22_panorama_{$i}_bg", 'default'),
                        'speed' => kunaal_mod("kunaal_about_v22_panorama_{$i}_speed", '2.0'),
                    );
                }
            }
        }
    }
    
    return $panoramas_by_position;
}

/**
 * Get books for v22 About page
 * Returns array of book items (up to 6)
 */
function kunaal_get_books_v22() {
    $books = array();
    for ($i = 1; $i <= 6; $i++) {
        $title = kunaal_mod("kunaal_about_v22_book_{$i}_title", '');
        if (!empty($title)) {
            $cover_id = kunaal_mod("kunaal_about_v22_book_{$i}_cover", 0);
            $cover_url = $cover_id ? wp_get_attachment_image_url($cover_id, 'medium') : '';
            
            $books[] = array(
                'cover' => $cover_url,
                'title' => $title,
                'author' => kunaal_mod("kunaal_about_v22_book_{$i}_author", ''),
                'url' => kunaal_mod("kunaal_about_v22_book_{$i}_url", ''),
            );
        }
    }
    return $books;
}

/**
 * Get digital media for v22 About page
 * Returns array of digital items (up to 6)
 */
function kunaal_get_digital_media_v22() {
    $digital = array();
    for ($i = 1; $i <= 6; $i++) {
        $title = kunaal_mod("kunaal_about_v22_digital_{$i}_title", '');
        if (!empty($title)) {
            $cover_id = kunaal_mod("kunaal_about_v22_digital_{$i}_cover", 0);
            $cover_url = $cover_id ? wp_get_attachment_image_url($cover_id, 'medium') : '';
            $link_type = kunaal_mod("kunaal_about_v22_digital_{$i}_link_type", 'spotify');
            
            $digital[] = array(
                'cover' => $cover_url,
                'title' => $title,
                'artist' => kunaal_mod("kunaal_about_v22_digital_{$i}_artist", ''),
                'link_type' => $link_type,
                'url' => kunaal_mod("kunaal_about_v22_digital_{$i}_url", ''),
            );
        }
    }
    return $digital;
}

/**
 * Get places data for v22 About page
 * Returns array with lived, visited, and current location ISO codes
 */
function kunaal_get_places_v22() {
    $lived_str = kunaal_mod('kunaal_about_v22_places_lived', '');
    $visited_str = kunaal_mod('kunaal_about_v22_places_visited', '');
    $current = kunaal_mod('kunaal_about_v22_places_current', '');
    
    $lived = array();
    if (!empty($lived_str)) {
        $lived = array_map('trim', array_map('strtoupper', explode(',', $lived_str)));
        $lived = array_filter($lived);
    }
    
    $visited = array();
    if (!empty($visited_str)) {
        $visited = array_map('trim', array_map('strtoupper', explode(',', $visited_str)));
        $visited = array_filter($visited);
    }
    
    // Ensure current is an array (even if empty)
    $current_array = array();
    if (!empty($current)) {
        $current_trimmed = strtoupper(trim($current));
        if (!empty($current_trimmed)) {
            $current_array = array($current_trimmed);
        }
    }
    
    return array(
        'lived' => $lived,
        'visited' => $visited,
        'current' => $current_array,
    );
}

/**
 * Get inspirations for v22 About page
 * Returns array of inspiration items (up to 10)
 */
function kunaal_get_inspirations_v22() {
    $inspirations = array();
    for ($i = 1; $i <= 10; $i++) {
        $name = kunaal_mod("kunaal_about_v22_inspiration_{$i}_name", '');
        if (!empty($name)) {
            $photo_id = kunaal_mod("kunaal_about_v22_inspiration_{$i}_photo", 0);
            $photo_url = $photo_id ? wp_get_attachment_image_url($photo_id, 'medium') : '';
            
            $inspirations[] = array(
                'photo' => $photo_url,
                'name' => $name,
                'role' => kunaal_mod("kunaal_about_v22_inspiration_{$i}_role", ''),
                'note' => kunaal_mod("kunaal_about_v22_inspiration_{$i}_note", ''),
                'url' => kunaal_mod("kunaal_about_v22_inspiration_{$i}_url", ''),
            );
        }
    }
    return $inspirations;
}

