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
 * @version 4.20.8
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

define('KUNAAL_THEME_VERSION', '4.20.8');
define('KUNAAL_THEME_DIR', get_template_directory());
define('KUNAAL_THEME_URI', get_template_directory_uri());

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

// Block Registration
kunaal_theme_safe_require_once(KUNAAL_THEME_DIR . '/inc/blocks.php');

// Note: Helper functions currently live in this file.
// Do NOT include `inc/helpers.php` here unless the duplicates in this file are removed,
// otherwise it can cause "Cannot redeclare ..." fatal errors on boot.

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

    // Main stylesheet
    wp_enqueue_style(
        'kunaal-theme-style',
        get_stylesheet_uri(),
        array('kunaal-google-fonts'),
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
    
    if ($is_about_page || $is_contact_page) {
        // Cormorant Garamond font
        wp_enqueue_style(
            'kunaal-cormorant-font',
            'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,400;1,500&display=swap',
            array(),
            null
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
        
        // Localize script with places data for map
        $places = kunaal_get_places_v22();
        wp_localize_script('kunaal-about-page-v22', 'kunaalAboutV22', array(
            'places' => $places,
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
        'kunaal-about-page',
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
        <label for="kunaal_subtitle"><strong>Subtitle/Description</strong></label><br>
        <input type="text" id="kunaal_subtitle" name="kunaal_subtitle" value="<?php echo esc_attr($subtitle); ?>" style="width:100%;" />
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
            <img src="<?php echo esc_url($card_image_url); ?>" style="max-width:100%;margin-bottom:10px;" />
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
 * Based on word count at 200 words per minute
 */
function kunaal_calculate_reading_time($post_id) {
    $content = get_post_field('post_content', $post_id);
    
    // Strip shortcodes and HTML tags
    $content = strip_shortcodes($content);
    $content = wp_strip_all_tags($content);
    
    // Count words
    $word_count = str_word_count($content);
    
    // Calculate reading time (200 words per minute)
    $reading_time = max(1, ceil($word_count / 200));
    
    return $reading_time;
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
function kunaal_customize_register($wp_customize) {
    // Author Section
    $wp_customize->add_section('kunaal_author', array(
        'title' => 'Author Info',
        'priority' => 30,
    ));

    // Avatar
    $wp_customize->add_setting('kunaal_avatar', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kunaal_avatar', array(
        'label' => 'Author Avatar',
        'description' => 'Upload a square image (at least 88x88px). If not set, initials will be displayed.',
        'section' => 'kunaal_author',
    )));

    // First Name (live preview)
    $wp_customize->add_setting('kunaal_author_first_name', array(
        'default' => 'Kunaal',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage', // Live preview without refresh
    ));
    $wp_customize->add_control('kunaal_author_first_name', array(
        'label' => 'First Name',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Last Name (live preview)
    $wp_customize->add_setting('kunaal_author_last_name', array(
        'default' => 'Wadhwa',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage', // Live preview without refresh
    ));
    $wp_customize->add_control('kunaal_author_last_name', array(
        'label' => 'Last Name',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Tagline (live preview)
    $wp_customize->add_setting('kunaal_author_tagline', array(
        'default' => 'A slightly alarming curiosity about humans and human collectives.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage', // Live preview without refresh
    ));
    $wp_customize->add_control('kunaal_author_tagline', array(
        'label' => 'Tagline',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Contact Email
    $wp_customize->add_setting('kunaal_contact_email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_contact_email', array(
        'label' => 'Contact Email',
        'description' => 'Displayed in footer with envelope animation',
        'section' => 'kunaal_author',
        'type' => 'email',
    ));

    // Footer Disclaimer
    $wp_customize->add_setting('kunaal_footer_disclaimer', array(
        'default' => 'Personal writing. Independent of my day job.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_footer_disclaimer', array(
        'label' => 'Footer Disclaimer',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));
    
    // ===== SOCIAL SHARING SECTION =====
    $wp_customize->add_section('kunaal_sharing', array(
        'title' => 'Social Sharing',
        'priority' => 35,
        'description' => 'Customize how posts are shared on social media.',
    ));
    
    // LinkedIn Handle
    $wp_customize->add_setting('kunaal_linkedin_handle', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_linkedin_handle', array(
        'label' => 'LinkedIn Profile URL',
        'description' => 'Full LinkedIn profile URL (e.g., https://linkedin.com/in/yourname)',
        'section' => 'kunaal_sharing',
        'type' => 'url',
    ));
    
    // Twitter/X Handle
    $wp_customize->add_setting('kunaal_twitter_handle', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_twitter_handle', array(
        'label' => 'Twitter/X Handle',
        'description' => 'Without @ (e.g., yourhandle)',
        'section' => 'kunaal_sharing',
        'type' => 'text',
    ));
    
    // Default Share Text
    $wp_customize->add_setting('kunaal_share_text', array(
        'default' => 'Check out this article:',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_share_text', array(
        'label' => 'Default Share Text',
        'description' => 'Text that appears before the article title when sharing',
        'section' => 'kunaal_sharing',
        'type' => 'text',
    ));
    
    // ===== SITE IDENTITY ADDITIONS =====
    // Favicon (PNG without border)
    $wp_customize->add_setting('kunaal_favicon', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kunaal_favicon', array(
        'label' => 'Favicon (PNG)',
        'description' => 'Upload a PNG favicon (recommended 32x32 or 180x180 for Apple Touch). No white outline.',
        'section' => 'title_tagline',
        'priority' => 100,
    )));
    
    // =====================================================
    // SUBSCRIBE SECTION SETTINGS
    // =====================================================
    $wp_customize->add_section('kunaal_subscribe', array(
        'title' => 'Subscribe Section',
        'priority' => 45,
    ));
    
    // Enable Subscribe Section
    $wp_customize->add_setting('kunaal_subscribe_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_subscribe_enabled', array(
        'label' => 'Enable Subscribe Section',
        'description' => 'Show email subscribe form on essays and jottings',
        'section' => 'kunaal_subscribe',
        'type' => 'checkbox',
    ));
    
    // Subscribe Display Location
    $wp_customize->add_setting('kunaal_subscribe_location', array(
        'default' => 'both',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_location', array(
        'label' => 'Subscribe Display Location',
        'description' => 'Choose where to show subscribe form',
        'section' => 'kunaal_subscribe',
        'type' => 'radio',
        'choices' => array(
            'dock' => 'Dock only (floating button)',
            'bottom' => 'Bottom section only',
            'both' => 'Both dock and bottom',
            'neither' => 'Disabled',
        ),
    ));
    
    // Subscribe Heading
    $wp_customize->add_setting('kunaal_subscribe_heading', array(
        'default' => 'Stay updated',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_heading', array(
        'label' => 'Subscribe Heading',
        'section' => 'kunaal_subscribe',
        'type' => 'text',
    ));
    
    // Subscribe Description
    $wp_customize->add_setting('kunaal_subscribe_description', array(
        'default' => __('Get notified when new essays and jottings are published.', 'kunaal-theme'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_description', array(
        'label' => 'Subscribe Description',
        'section' => 'kunaal_subscribe',
        'type' => 'textarea',
    ));
    
    // Subscribe Form Action URL
    $wp_customize->add_setting('kunaal_subscribe_form_action', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_subscribe_form_action', array(
        'label' => 'Form Action URL',
        'description' => 'Optional: external provider form action URL (Mailchimp/ConvertKit/etc). If empty, the theme will use its built-in subscribe flow.',
        'section' => 'kunaal_subscribe',
        'type' => 'url',
    ));
    
    // Subscribe notification delay (hours after publish)
    $wp_customize->add_setting('kunaal_subscribe_notify_delay_hours', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_subscribe_notify_delay_hours', array(
        'label' => 'Email Delay (Hours)',
        'description' => 'Send notification emails X hours after a new essay/jotting is published (0 = immediately)',
        'section' => 'kunaal_subscribe',
        'type' => 'number',
        'input_attrs' => array('min' => 0, 'max' => 168, 'step' => 1),
    ));

    // Subscribe mode (built-in vs external)
    $wp_customize->add_setting('kunaal_subscribe_mode', array(
        'default' => 'builtin',
        'sanitize_callback' => function ($value) {
            $allowed = array('builtin', 'external');
            return in_array($value, $allowed, true) ? $value : 'builtin';
        },
    ));
    $wp_customize->add_control('kunaal_subscribe_mode', array(
        'label' => 'Subscribe Mode',
        'description' => 'Built-in: stores subscribers in WordPress (private) and sends confirmation emails. External: posts to your provider form action URL.',
        'section' => 'kunaal_subscribe',
        'type' => 'radio',
        'choices' => array(
            'builtin' => 'Built-in (recommended)',
            'external' => 'External provider (form action URL)',
        ),
    ));

    // Where subscription notifications go (built-in mode)
    $wp_customize->add_setting('kunaal_subscribe_notify_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_subscribe_notify_email', array(
        'label' => 'Subscribe Notifications Email',
        'description' => 'Built-in mode: confirmations and admin notifications use this email.',
        'section' => 'kunaal_subscribe',
        'type' => 'email',
    ));
    
    // Page selection removed - no longer used in customizations

    // =====================================================
    // ABOUT PAGE SETTINGS
    // Note: All About page settings are in the "About Page" panel
    // See: Appearance → Customize → About Page
    // =====================================================
    
    // =====================================================
    // CONTACT PAGE SECTION
    // =====================================================
    $wp_customize->add_section('kunaal_contact_page', array(
        'title' => 'Contact Page',
        'priority' => 51,
        'description' => 'Customize the Contact page. Create a page with the "Contact Page" template.',
    ));

    // Recipient email (where messages are delivered)
    $wp_customize->add_setting('kunaal_contact_recipient_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_contact_recipient_email', array(
        'label' => 'Recipient Email (for Contact form delivery)',
        'description' => 'Messages submitted on the Contact page will be sent to this address.',
        'section' => 'kunaal_contact_page',
        'type' => 'email',
    ));
    
    $wp_customize->add_setting('kunaal_contact_headline', array(
        'default' => 'Get in Touch',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_headline', array(
        'label' => 'Headline',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_contact_placeholder', array(
        'default' => 'Leave a note...',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_placeholder', array(
        'label' => 'Message Placeholder Text',
        'description' => 'Text shown in the message textarea when empty',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_contact_response_time', array(
        'default' => 'I typically respond within 2-3 business days.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_response_time', array(
        'label' => 'Response Time Note',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));

    // =====================================================
    // EMAIL DELIVERY (SMTP)
    // =====================================================
    $wp_customize->add_section('kunaal_email_delivery', array(
        'title' => 'Email Delivery (SMTP)',
        'priority' => 52,
        'description' => 'Configure SMTP so contact + subscribe emails deliver reliably on shared hosts.',
    ));

    $wp_customize->add_setting('kunaal_smtp_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_smtp_enabled', array(
        'label' => 'Enable SMTP',
        'section' => 'kunaal_email_delivery',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('kunaal_smtp_from_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_smtp_from_email', array(
        'label' => 'From Email',
        'description' => 'Use an address that matches your domain if possible.',
        'section' => 'kunaal_email_delivery',
        'type' => 'email',
    ));

    $wp_customize->add_setting('kunaal_smtp_from_name', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_from_name', array(
        'label' => 'From Name',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_smtp_host', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_host', array(
        'label' => 'SMTP Host',
        'description' => 'Example (Brevo): smtp-relay.brevo.com',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_smtp_port', array(
        'default' => 587,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_smtp_port', array(
        'label' => 'SMTP Port',
        'description' => '587 (TLS) is typical; 465 for SSL.',
        'section' => 'kunaal_email_delivery',
        'type' => 'number',
        'input_attrs' => array('min' => 1, 'max' => 65535),
    ));

    $wp_customize->add_setting('kunaal_smtp_encryption', array(
        'default' => 'tls',
        'sanitize_callback' => function ($value) {
            $allowed = array('none', 'tls', 'ssl');
            return in_array($value, $allowed, true) ? $value : 'tls';
        },
    ));
    $wp_customize->add_control('kunaal_smtp_encryption', array(
        'label' => 'Encryption',
        'section' => 'kunaal_email_delivery',
        'type' => 'select',
        'choices' => array(
            'none' => 'None',
            'tls'  => 'TLS',
            'ssl'  => 'SSL',
        ),
    ));

    $wp_customize->add_setting('kunaal_smtp_username', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_username', array(
        'label' => 'SMTP Username',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_smtp_password', array(
        'default' => '',
        'sanitize_callback' => function ($value) {
            // Allow most characters; keep as plain text (shared-host reality). Avoid stripping symbols.
            return is_string($value) ? $value : '';
        },
    ));
    $wp_customize->add_control('kunaal_smtp_password', array(
        'label' => 'SMTP Password / Key',
        'section' => 'kunaal_email_delivery',
        'type' => 'password',
    ));

    // =====================================================
    // CONTACT PAGE - SOCIAL LINKS
    // =====================================================
    // Instagram
    $wp_customize->add_setting('kunaal_contact_instagram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_instagram', array(
        'label' => 'Instagram URL',
        'description' => 'Your Instagram profile URL (e.g., https://instagram.com/yourname)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));
    
    // WhatsApp (public link, no phone number)
    $wp_customize->add_setting('kunaal_contact_whatsapp', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_whatsapp', array(
        'label' => 'WhatsApp Link',
        'description' => 'Public WhatsApp link (e.g., https://wa.me/yourlink - avoid phone numbers)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));
    
    // Viber (public link, no phone number)
    $wp_customize->add_setting('kunaal_contact_viber', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_viber', array(
        'label' => 'Viber Link',
        'description' => 'Public Viber link (e.g., https://chats.viber.com/yourname - avoid phone numbers)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));
    
    // LINE (public link, no phone number)
    $wp_customize->add_setting('kunaal_contact_line', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_line', array(
        'label' => 'LINE Link',
        'description' => 'Public LINE link (e.g., https://line.me/R/ti/p/@yourname - avoid phone numbers)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));
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

/**
 * Helper: Get initials
 */
if (!function_exists('kunaal_get_initials')) {
    function kunaal_get_initials() {
        $first = kunaal_mod('kunaal_author_first_name', 'Kunaal');
        $last = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
        return strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
    }
}

/**
 * Helper: Output Subscribe Section
 */
if (!function_exists('kunaal_subscribe_section')) {
    function kunaal_subscribe_section() {
        if (!kunaal_mod('kunaal_subscribe_enabled', false)) {
            return;
        }
        
        // Check location setting - only show bottom if 'bottom' or 'both'
        $sub_location = kunaal_mod('kunaal_subscribe_location', 'both');
        if (!in_array($sub_location, array('bottom', 'both'))) {
            return;
        }
        
        $heading = kunaal_mod('kunaal_subscribe_heading', 'Stay updated');
        $description = kunaal_mod('kunaal_subscribe_description', 'Get notified when new essays and jottings are published.');
        $form_action = kunaal_mod('kunaal_subscribe_form_action', '');
        $mode = kunaal_mod('kunaal_subscribe_mode', 'builtin');
        
        ?>
    <section class="subscribe-section reveal">
        <h3><?php echo esc_html($heading); ?></h3>
        <p><?php echo esc_html($description); ?></p>
        <form class="subscribe-form" data-subscribe-form="bottom" data-subscribe-mode="<?php echo esc_attr($mode); ?>" action="<?php echo $mode === 'external' ? esc_url($form_action) : ''; ?>" method="post" novalidate>
            <input type="email" name="email" placeholder="Your email address" required />
            <button type="submit">Subscribe</button>
        </form>
        <div class="subscribe-status" aria-live="polite"></div>
    </section>
    <?php
    }
}

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

function kunaal_handle_subscribe() {
    try {
        if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_theme_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh and try again.'));
            wp_die();
        }

        $mode = kunaal_mod('kunaal_subscribe_mode', 'builtin');
        if ($mode === 'external') {
            wp_send_json_error(array('message' => 'Subscribe is configured for an external provider.'));
            wp_die();
        }

        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
            wp_die();
        }

        $email = strtolower(trim($email));
        $existing_id = kunaal_find_subscriber_by_email($email);
        if ($existing_id) {
            $status = get_post_meta($existing_id, 'kunaal_status', true);
            if ($status === 'confirmed') {
                wp_send_json_success(array('message' => 'You are already subscribed.'));
            } else {
                wp_send_json_success(array('message' => 'Check your inbox to confirm your subscription.'));
            }
            wp_die();
        }

        $token = kunaal_generate_subscribe_token();
        $subscriber_id = wp_insert_post(array(
            'post_type' => 'kunaal_subscriber',
            'post_status' => 'private',
            'post_title' => $email,
        ), true);

        if (is_wp_error($subscriber_id) || empty($subscriber_id)) {
            wp_send_json_error(array('message' => 'Unable to create subscription. Please try again.'));
            wp_die();
        }

        update_post_meta($subscriber_id, 'kunaal_email', $email);
        update_post_meta($subscriber_id, 'kunaal_status', 'pending');
        update_post_meta($subscriber_id, 'kunaal_token', $token);
        update_post_meta($subscriber_id, 'kunaal_created_gmt', gmdate('c'));

        $sent = kunaal_send_subscribe_confirmation($email, $token);
        if (!$sent) {
            wp_send_json_error(array('message' => 'Unable to send confirmation email. Please try again later.'));
            wp_die();
        }

        wp_send_json_success(array('message' => 'Check your inbox to confirm your subscription.'));
        wp_die();
    } catch (Exception $e) {
        kunaal_theme_log('Subscribe error', array('error' => $e->getMessage()));
        wp_send_json_error(array('message' => 'An error occurred. Please try again.'));
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

/**
 * Helper: Get all topics with counts
 */
if (!function_exists('kunaal_get_all_topics')) {
    function kunaal_get_all_topics() {
    $topics = get_terms(array(
        'taxonomy' => 'topic',
        'hide_empty' => false,
    ));

    if (is_wp_error($topics) || empty($topics)) {
        return array();
    }

    $result = array();
    foreach ($topics as $topic) {
        $result[] = array(
            'slug' => $topic->slug,
            'name' => $topic->name,
            'count' => $topic->count,
        );
    }
    return $result;
    }
}

/**
 * Helper: Get card image URL
 */
if (!function_exists('kunaal_get_card_image_url')) {
    function kunaal_get_card_image_url($post_id, $size = 'essay-card') {
        $card_image = get_post_meta($post_id, 'kunaal_card_image', true);
        if ($card_image) {
            return wp_get_attachment_image_url($card_image, $size);
        }
        if (has_post_thumbnail($post_id)) {
            return get_the_post_thumbnail_url($post_id, $size);
        }
        return '';
    }
}

/**
 * Helper: Render atmosphere images for About page
 * Moved from page-about.php template to prevent side effects
 */
if (!function_exists('kunaal_render_atmo_images')) {
    function kunaal_render_atmo_images($position, $images) {
        if (empty($images)) {
            return;
        }
        
        foreach ($images as $img) {
            if ($img['position'] !== $position && $img['position'] !== 'auto') {
                continue;
            }
            if ($img['type'] === 'hidden') {
                continue;
            }
            
            $clip_class = '';
            switch ($img['clip']) {
                case 'angle_bottom':
                    $clip_class = 'clip-angle-bottom';
                    break;
                case 'angle_top':
                    $clip_class = 'clip-angle-top';
                    break;
                case 'angle_both':
                    $clip_class = 'clip-angle-both';
                    break;
            }
            
            if ($img['has_quote'] && !empty($img['quote'])) {
                ?>
                <section class="about-quote-image about-layer-image">
                    <div class="about-quote-image-bg parallax-slow <?php echo esc_attr($clip_class); ?>">
                        <img src="<?php echo esc_url($img['image']); ?>" alt="" class="about-image">
                    </div>
                    <div class="about-quote-content reveal-up">
                        <p class="about-quote-text">"<?php echo esc_html($img['quote']); ?>"</p>
                        <?php if (!empty($img['quote_attr'])) : ?>
                        <span class="about-quote-attr">— <?php echo esc_html($img['quote_attr']); ?></span>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
            } else {
                ?>
                <div class="atmo-full <?php echo esc_attr($clip_class); ?> about-layer-image">
                    <img src="<?php echo esc_url($img['image']); ?>" alt="" class="about-image parallax-slow">
                    <?php if (!empty($img['caption'])) : ?>
                    <span class="about-quote-caption"><?php echo esc_html($img['caption']); ?></span>
                    <?php endif; ?>
                </div>
                <?php
            }
        }
    }
}

/**
 * Get all theme mods (cached for request lifetime)
 * 
 * @return array All theme modification values
 */
function kunaal_get_theme_mods() {
    static $mods = null;
    if ($mods === null) {
        $mods = get_theme_mods();
    }
    return $mods;
}

/**
 * Get theme mod with caching
 * 
 * @param string $key Theme mod key
 * @param mixed $default Default value if not set
 * @return mixed Theme mod value or default
 */
function kunaal_mod($key, $default = '') {
    $mods = kunaal_get_theme_mods();
    return isset($mods[$key]) ? $mods[$key] : $default;
}

/**
 * AJAX: Filter content
 */
function kunaal_filter_content() {
    try {
        // Verify nonce for CSRF protection - enforce nonce requirement
        if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_theme_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh the page and try again.'));
            wp_die();
        }
        
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'essay';
        
        // Handle topics - can be string, array, or empty
        $topics = array();
        if (isset($_POST['topics'])) {
            $topics_raw = $_POST['topics'];
            if (is_array($topics_raw)) {
                $topics = array_filter(array_map('sanitize_text_field', $topics_raw));
            } elseif (is_string($topics_raw) && !empty($topics_raw)) {
                $topics = array_filter(array_map('sanitize_text_field', explode(',', $topics_raw)));
            }
        }
        
        $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'new';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 12;
        // Limit per_page to prevent DoS via massive queries
        $per_page = min($per_page, 100);
        
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $per_page,
            'paged' => $page,
            'post_status' => 'publish',
        );
        
        // Topics filter - only if topics selected
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
        
        $query = new WP_Query($args);
        $posts_data = array();
        
        if ($query->have_posts()) {
            // Prime caches to prevent N+1 queries
            // Ensure WordPress functions are available
            if (!function_exists('update_post_meta_cache')) {
                require_once(ABSPATH . 'wp-admin/includes/post.php');
            }
            if (!function_exists('update_object_term_cache')) {
                require_once(ABSPATH . 'wp-includes/taxonomy.php');
            }
            $post_ids = wp_list_pluck($query->posts, 'ID');
            if (function_exists('update_post_meta_cache')) {
                update_post_meta_cache($post_ids);
            }
            if (function_exists('update_object_term_cache')) {
                update_object_term_cache($post_ids, array('essay', 'jotting'));
            }
            
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $topics_list = get_the_terms($post_id, 'topic');
                $tags = array();
                $tag_slugs = array();
                if ($topics_list && !is_wp_error($topics_list)) {
                    foreach ($topics_list as $topic) {
                        $tags[] = $topic->name;
                        $tag_slugs[] = $topic->slug;
                    }
                }
                
                $posts_data[] = array(
                    'id' => $post_id,
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'date' => get_the_date('j F Y'),
                    'dateShort' => get_the_date('j M Y'),
                    'subtitle' => get_post_meta($post_id, 'kunaal_subtitle', true),
                    'readTime' => get_post_meta($post_id, 'kunaal_read_time', true),
                    'image' => kunaal_get_card_image_url($post_id),
                    'tags' => $tags,
                    'tagSlugs' => $tag_slugs,
                );
            }
        }
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'posts' => $posts_data,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'page' => $page,
        ));
        wp_die(); // Prevent code execution after JSON response
    } catch (Exception $e) {
        kunaal_theme_log('AJAX filter error', array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString()));
        wp_send_json_error(array('message' => 'An error occurred. Please try again.'));
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
 * Contact Form AJAX Handler
 */
function kunaal_handle_contact_form() {
    try {
        // Verify nonce
        if (!isset($_POST['kunaal_contact_nonce']) || !wp_verify_nonce($_POST['kunaal_contact_nonce'], 'kunaal_contact_form')) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh and try again.'));
            wp_die();
        }
        
        // Sanitize inputs
        $name = isset($_POST['contact_name']) ? sanitize_text_field($_POST['contact_name']) : '';
        $email = isset($_POST['contact_email']) ? sanitize_email($_POST['contact_email']) : '';
        $message = isset($_POST['contact_message']) ? sanitize_textarea_field($_POST['contact_message']) : '';
        $honeypot = isset($_POST['contact_company']) ? sanitize_text_field($_POST['contact_company']) : '';

        // Honeypot check (bots)
        if (!empty($honeypot)) {
            wp_send_json_error(array('message' => 'Sorry, your message could not be sent.'));
            wp_die();
        }

        // Basic rate limiting by IP (check X-Forwarded-For for proxies)
        $ip = '';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $ip = sanitize_text_field(trim($forwarded[0]));
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }
        $rate_key = 'kunaal_contact_rl_' . wp_hash($ip);
        $count = (int) get_transient($rate_key);
        if ($count >= 5) {
            wp_send_json_error(array('message' => 'Please wait a bit before sending another message.'));
            wp_die();
        }
        set_transient($rate_key, $count + 1, 10 * MINUTE_IN_SECONDS);
        
        // Validate - message is required, name and email are optional
        if (empty($message)) {
            wp_send_json_error(array('message' => 'Please enter a message.'));
            wp_die();
        }
        
        // If email is provided, validate it
        if (!empty($email) && !is_email($email)) {
            wp_send_json_error(array('message' => 'Please enter a valid email address.'));
            wp_die();
        }
        
        // Get recipient email
        $to_email = kunaal_mod('kunaal_contact_recipient_email', get_option('admin_email'));
        if (!is_email($to_email)) {
            $to_email = get_option('admin_email');
        }
        
        // Build email
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
        
        // Send email
        $sent = wp_mail($to_email, $email_subject, $email_body, $headers);
        
        if ($sent) {
            wp_send_json_success(array('message' => 'Thank you! Your message has been sent.'));
            wp_die();
        } else {
            // Log the error for debugging
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
    } catch (Exception $e) {
        kunaal_theme_log('Contact form error', array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString()));
        wp_send_json_error(array('message' => 'An error occurred. Please try again.'));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_contact_form', 'kunaal_handle_contact_form');
add_action('wp_ajax_nopriv_kunaal_contact_form', 'kunaal_handle_contact_form');

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
                        'height' => kunaal_mod("kunaal_about_v22_panorama_{$i}_height", '160'),
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

