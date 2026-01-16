<?php
/**
 * Asset Enqueuing
 * 
 * Handles all CSS and JavaScript asset registration and enqueuing.
 * Extracted from kunaal_enqueue_assets() to reduce cognitive complexity.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get AJAX URL with forced HTTPS if site is HTTPS
 * Prevents mixed content errors on mobile browsers and reverse proxies
 *
 * @return string AJAX URL with correct protocol
 */
function kunaal_get_ajax_url(): string {
    $url = admin_url('admin-ajax.php');
    // Force HTTPS if site is HTTPS (handles reverse proxies like Cloudflare)
    if (is_ssl() || 
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
        $url = str_replace('http://', 'https://', $url);
    }
    return $url;
}

/**
 * Enqueue Google Fonts
 */
function kunaal_enqueue_google_fonts(): void {
    wp_enqueue_style(
        'kunaal-google-fonts',
        'https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Inter:opsz,wght@14..32,300..700&family=Caveat:wght@400;500;600;700&display=swap',
        array(),
        null
    );
}

/**
 * Enqueue core CSS modules (tokens, variables, base, dark mode, layout, header)
 */
function kunaal_enqueue_core_css(): void {
    // 1. Design tokens (must load first - single source of truth)
    wp_enqueue_style(
        'kunaal-theme-tokens',
        KUNAAL_THEME_URI . '/assets/css/tokens.css',
        array(),
        kunaal_asset_version('assets/css/tokens.css')
    );

    // 2. Variables (legacy mappings and chart colors)
    wp_enqueue_style(
        'kunaal-theme-variables',
        KUNAAL_THEME_URI . '/assets/css/variables.css',
        array('kunaal-theme-tokens'),
        kunaal_asset_version('assets/css/variables.css')
    );

    // 3. Base styles (resets, typography)
    wp_enqueue_style(
        'kunaal-theme-base',
        KUNAAL_THEME_URI . '/assets/css/base.css',
        array('kunaal-theme-tokens', 'kunaal-theme-variables'),
        kunaal_asset_version('assets/css/base.css')
    );

    // 4. Dark mode (must load after base)
    wp_enqueue_style(
        'kunaal-theme-dark-mode',
        KUNAAL_THEME_URI . '/assets/css/dark-mode.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/dark-mode.css')
    );

    // 5. Layout
    wp_enqueue_style(
        'kunaal-theme-layout',
        KUNAAL_THEME_URI . '/assets/css/layout.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/layout.css')
    );

    // 6. Header
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
function kunaal_enqueue_component_css(): void {
    // 7. Components (cards, buttons, panels, footer)
    wp_enqueue_style(
        'kunaal-theme-components',
        KUNAAL_THEME_URI . '/assets/css/components.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/components.css')
    );

    // 8. Utilities (progress bar, lazy loading, animations)
    wp_enqueue_style(
        'kunaal-theme-utilities',
        KUNAAL_THEME_URI . '/assets/css/utilities.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/utilities.css')
    );

    // 9. Filters/Toolbar
    wp_enqueue_style(
        'kunaal-theme-filters',
        KUNAAL_THEME_URI . '/assets/css/filters.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/filters.css')
    );

    // 10. Sections & Grid
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
function kunaal_enqueue_page_css(): void {
    // 11. Pages (archive, article, prose, page utilities)
    wp_enqueue_style(
        'kunaal-theme-pages',
        KUNAAL_THEME_URI . '/assets/css/pages.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/pages.css')
    );

    // 12. Custom Blocks
    wp_enqueue_style(
        'kunaal-theme-blocks',
        KUNAAL_THEME_URI . '/assets/css/blocks.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/blocks.css')
    );

    // 13. WordPress Core Block Overrides
    wp_enqueue_style(
        'kunaal-theme-wordpress-blocks',
        KUNAAL_THEME_URI . '/assets/css/wordpress-blocks.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/wordpress-blocks.css')
    );

    // 14. Motion Primitives
    wp_enqueue_style(
        'kunaal-theme-motion',
        KUNAAL_THEME_URI . '/assets/css/motion.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/motion.css')
    );

    // 15. Compatibility (print, reduced motion, cross-browser)
    wp_enqueue_style(
        'kunaal-theme-compatibility',
        KUNAAL_THEME_URI . '/assets/css/compatibility.css',
        array('kunaal-theme-base'),
        kunaal_asset_version('assets/css/compatibility.css')
    );

    // About page CSS is enqueued conditionally in kunaal_enqueue_page_specific_assets()
}

/**
 * Enqueue main stylesheet and print styles
 */
function kunaal_enqueue_main_styles(): void {
    // 16. Main stylesheet (loads after all modules - only contains theme header)
    wp_enqueue_style(
        'kunaal-theme-style',
        get_stylesheet_uri(),
        array('kunaal-google-fonts', 'kunaal-theme-tokens', 'kunaal-theme-variables', 'kunaal-theme-base', 'kunaal-theme-dark-mode', 'kunaal-theme-layout', 'kunaal-theme-header', 'kunaal-theme-components', 'kunaal-theme-utilities', 'kunaal-theme-filters', 'kunaal-theme-sections', 'kunaal-theme-pages', 'kunaal-theme-blocks', 'kunaal-theme-wordpress-blocks', 'kunaal-theme-motion', 'kunaal-theme-compatibility'),
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

/**
 * Enqueue page-specific assets (About page, Contact page)
 * Must be called after main styles are enqueued
 */
function kunaal_enqueue_page_specific_assets(): void {
    // Template detection
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
        // About page CSS (must be enqueued before wp_add_inline_style)
        wp_enqueue_style(
            'kunaal-about-page',
            KUNAAL_THEME_URI . '/assets/css/about-page.css',
            array('kunaal-theme-style'),
            kunaal_asset_version('assets/css/about-page.css')
        );
        
        // About page: Generate CSS variables for category colors (must be AFTER enqueue)
        $categories = kunaal_get_categories();
        if (!empty($categories)) {
            $css_vars = ".kunaal-about-page {\n";
            foreach ($categories as $slug => $category) {
                $css_vars .= "  --cat-" . esc_attr($slug) . ": " . esc_attr($category['color']) . ";\n";
            }
            $css_vars .= "}\n";
            // Add slug-scoped selectors for capsule dots and legend dots
            foreach ($categories as $slug => $category) {
                $css_vars .= ".kunaal-about-page .capsule[data-cat=\"" . esc_attr($slug) . "\"] .capsule-dot {\n";
                $css_vars .= "  background: var(--cat-" . esc_attr($slug) . ");\n";
                $css_vars .= "}\n";
                $css_vars .= ".kunaal-about-page .legend-dot[data-cat=\"" . esc_attr($slug) . "\"] {\n";
                $css_vars .= "  background: var(--cat-" . esc_attr($slug) . ");\n";
                $css_vars .= "}\n";
            }
            wp_add_inline_style('kunaal-about-page', $css_vars);
        }
        
        // GSAP Core (required for ScrollTrigger) - Load in footer to avoid blocking render
        // Bundled locally per architecture standards (assets/libs/)
        wp_enqueue_script(
            'gsap-core',
            KUNAAL_THEME_URI . '/assets/libs/gsap.min.js',
            array(),
            '3.12.5',
            true // Load in footer to avoid blocking render
        );
        
        // GSAP ScrollTrigger Plugin - Bundled locally
        wp_enqueue_script(
            'gsap-scrolltrigger',
            KUNAAL_THEME_URI . '/assets/libs/ScrollTrigger.min.js',
            array('gsap-core'),
            '3.12.5',
            true // Load in footer
        );
        
        // D3.js for world map - Bundled locally
        wp_enqueue_script(
            'd3-js',
            KUNAAL_THEME_URI . '/assets/libs/d3.v7.min.js',
            array(),
            '7.0.0',
            true
        );
        
        // TopoJSON for world map - Bundled locally
        wp_enqueue_script(
            'topojson-js',
            KUNAAL_THEME_URI . '/assets/libs/topojson-client.min.js',
            array('d3-js'),
            '3.0.0',
            true
        );
        
        // About page JS (depends on GSAP, D3, TopoJSON)
        wp_enqueue_script(
            'kunaal-about-page',
            KUNAAL_THEME_URI . '/assets/js/about-page.js',
            array('gsap-scrolltrigger', 'd3-js', 'topojson-js'),
            kunaal_asset_version('assets/js/about-page.js'),
            true
        );
        
        // Localize script with places data for map and debug config
        $places = kunaal_get_places();
        wp_localize_script('kunaal-about-page', 'kunaalAbout', array(
            'places' => $places,
            'debug' => defined('WP_DEBUG') && WP_DEBUG, // Gate debug logging behind WP_DEBUG
            'ajaxUrl' => kunaal_get_ajax_url(),
            'nonce' => wp_create_nonce('kunaal_debug_log_nonce'),
        ));
    }
}

/**
 * Main asset enqueuing function
 * Delegates to helper functions to reduce cognitive complexity.
 */
function kunaal_enqueue_assets(): void {
    kunaal_enqueue_google_fonts();
    kunaal_enqueue_core_css();
    kunaal_enqueue_component_css();
    kunaal_enqueue_page_css();
    kunaal_enqueue_main_styles();

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
    
    // Configure lib-loader to use locally bundled libraries (before lib-loader runs)
    wp_add_inline_script(
        'kunaal-lib-loader',
        'window.kunaalLibConfig = {
            d3Src: "' . esc_js(KUNAAL_THEME_URI . '/assets/libs/d3.v7.min.js') . '",
            leafletJs: "https://unpkg.com/leaflet@1.9.4/dist/leaflet.js",
            leafletCss: "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css",
            worldAtlasUrl: "' . esc_js(KUNAAL_THEME_URI . '/assets/data/countries-110m.json') . '"
        };',
        'before'
    );

    // Localize script with data
    // Always localize to ensure kunaalTheme is available on all pages
    wp_localize_script('kunaal-theme-main', 'kunaalTheme', array(
        'ajaxUrl' => kunaal_get_ajax_url(),
        'nonce' => wp_create_nonce('kunaal_theme_nonce'),
        'pdfNonce' => wp_create_nonce('kunaal_pdf_nonce'),
        'homeUrl' => home_url('/'),
        'shareText' => kunaal_mod('kunaal_share_text', ''),
        'twitterHandle' => kunaal_mod('kunaal_twitter_handle', ''),
        'linkedinUrl' => kunaal_mod('kunaal_linkedin_handle', ''),
        'authorName' => kunaal_mod('kunaal_author_first_name', 'Kunaal') . ' ' . kunaal_mod('kunaal_author_last_name', 'Wadhwa'),
        'debug' => defined('WP_DEBUG') && WP_DEBUG,
        // Content labels for filtering (singular/plural from Customizer)
        'essayLabelSingular' => kunaal_mod('kunaal_essay_label_singular', 'long one'),
        'essayLabelPlural' => kunaal_mod('kunaal_essay_label_plural', 'long ones'),
        'jottingLabelSingular' => kunaal_mod('kunaal_jotting_label_singular', 'short one'),
        'jottingLabelPlural' => kunaal_mod('kunaal_jotting_label_plural', 'short ones'),
    ));
    
    // For contact page, also add inline script to ensure kunaalTheme is available
    // even if main.js loads late or fails
    if (is_page_template('page-contact.php') || (is_page() && get_page_template_slug() === 'page-contact.php')) {
        wp_add_inline_script('kunaal-theme-main',
            'if (typeof kunaalTheme === "undefined") { window.kunaalTheme = { ajaxUrl: "' . esc_js(kunaal_get_ajax_url()) . '" }; }',
            'before'
        );
    }
    
    // Page-specific assets (About page, Contact page)
    kunaal_enqueue_page_specific_assets();
}

/**
 * Add small, targeted CSS overrides AFTER WordPress global styles.
 * This is necessary when WP's `global-styles` output overrides theme CSS even in later layers.
 */
function kunaal_add_global_styles_overrides(): void {
    // Ensure the handle exists on modern WP
    if (!wp_style_is('global-styles', 'enqueued') && !wp_style_is('global-styles', 'done')) {
        return;
    }

    // Dark mode: WP global styles may override our typography colors after theme CSS loads.
    // Attach overrides to `global-styles` so they appear after WP's own global styles output.
    $css = ''
        // Title
        . ':root[data-theme="dark"] body.wp-theme-kunaal-theme.kunaal-essay .articleHeader h1.articleTitle{color:var(--k-color-ink);}'
        // Meta tags
        . ':root[data-theme="dark"] body.wp-theme-kunaal-theme.kunaal-essay .articleMeta__tags{color:var(--k-color-muted);}'
        . ':root[data-theme="dark"] body.wp-theme-kunaal-theme.kunaal-essay .articleMeta__tags a{color:var(--k-color-muted);}'
        . ':root[data-theme="dark"] body.wp-theme-kunaal-theme.kunaal-essay .articleMeta__tags a:hover{color:var(--k-color-ink);}';
    wp_add_inline_style('global-styles', $css);
}
add_action('wp_enqueue_scripts', 'kunaal_add_global_styles_overrides', 100);

/**
 * Add defer attribute to non-critical scripts for better performance
 * 
 * Moved from functions.php to keep functions.php bootstrap-only.
 */
function kunaal_add_defer_to_scripts(string $tag, string $handle): string {
    $defer_scripts = array(
        'kunaal-theme-main',
        'kunaal-theme-controller',
        'kunaal-lazy-blocks',
        'kunaal-lib-loader',
        'gsap-core',
        'gsap-scrolltrigger',
        'kunaal-about-page',
        'd3-js',
        'topojson-js',
    );
    
    $result = $tag;
    if (in_array($handle, $defer_scripts)) {
        $result = str_replace(' src', ' defer src', $tag);
    }
    
    return $result;
}
add_filter('script_loader_tag', 'kunaal_add_defer_to_scripts', 10, 2);
