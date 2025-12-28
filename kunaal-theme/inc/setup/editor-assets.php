<?php
/**
 * Editor Assets
 * 
 * Registers and enqueues assets for the Gutenberg block editor.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
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

