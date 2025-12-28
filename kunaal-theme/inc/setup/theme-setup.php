<?php
/**
 * Theme Setup
 * 
 * Registers theme support features, image sizes, and editor styles.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

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
 * Add a `js` class to <html> early for progressive enhancement.
 * This ensures About page content is visible even if JS fails.
 */
function kunaal_add_js_class() {
    echo "<script>(function(d){d.documentElement.classList.add('js');})(document);</script>\n";
}
add_action('wp_head', 'kunaal_add_js_class', 0);

/**
 * Add custom body class for About V22 page
 * Provides stable scoping for About page CSS
 */
function kunaal_add_about_v22_body_class($classes) {
    if (is_page_template('page-about.php') || is_page('about')) {
        $classes[] = 'kunaal-about-v22';
    }
    return $classes;
}
add_filter('body_class', 'kunaal_add_about_v22_body_class');

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
 * Uses WordPress API instead of direct database queries
 */
function kunaal_theme_deactivation_handler() {
    global $wpdb;
    
    // Get all transients with our prefix
    $transients = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            $wpdb->esc_like('_transient_kunaal_') . '%'
        )
    );
    
    foreach ($transients as $transient) {
        $transient_name = str_replace('_transient_', '', $transient);
        delete_transient($transient_name);
    }
    
    // Also clean up transient timeouts
    $timeouts = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
            $wpdb->esc_like('_transient_timeout_kunaal_') . '%'
        )
    );
    
    foreach ($timeouts as $timeout) {
        $timeout_name = str_replace('_transient_timeout_', '', $timeout);
        delete_transient($timeout_name);
    }
}
add_action('switch_theme', 'kunaal_theme_deactivation_handler');

