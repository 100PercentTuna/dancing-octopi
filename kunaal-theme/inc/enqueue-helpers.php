<?php
/**
 * Asset Enqueuing Helper Functions
 * 
 * Extracted from kunaal_enqueue_assets() to reduce cognitive complexity.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue Google Fonts
 */
function kunaal_enqueue_google_fonts() {
    wp_enqueue_style(
        'kunaal-google-fonts',
        'https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Inter:opsz,wght@14..32,300..700&family=Caveat:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}

/**
 * Enqueue core CSS modules (variables, base, dark mode, layout, header)
 */
function kunaal_enqueue_core_css() {
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
}

/**
 * Enqueue component CSS modules
 */
function kunaal_enqueue_component_css() {
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
}

/**
 * Enqueue page-specific CSS modules
 */
function kunaal_enqueue_page_css() {
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
}

/**
 * Enqueue main stylesheet and print styles
 */
function kunaal_enqueue_main_styles() {
    // 16. Main stylesheet (now minimal - only contains theme header and any remaining styles)
    wp_enqueue_style(
        'kunaal-theme-style',
        get_stylesheet_uri(),
        array('kunaal-google-fonts', 'kunaal-theme-variables', 'kunaal-theme-base', 'kunaal-theme-dark-mode', 'kunaal-theme-layout', 'kunaal-theme-header', 'kunaal-theme-components', 'kunaal-theme-utilities', 'kunaal-theme-filters', 'kunaal-theme-sections', 'kunaal-theme-pages', 'kunaal-theme-blocks', 'kunaal-theme-wordpress-blocks', 'kunaal-theme-motion', 'kunaal-theme-compatibility', 'kunaal-theme-about-page'),
        kunaal_asset_version('style.css')
    );
    
    // Print stylesheet
    wp_enqueue_style(
        'kunaal-print-style',
        KUNAAL_THEME_URI . '/assets/css/print.css',
        array('kunaal-theme-style'),
        kunaal_asset_version('assets/css/print.css'),
        'print'
    );
}

