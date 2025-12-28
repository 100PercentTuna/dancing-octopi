<?php
/**
 * Gutenberg Block Registration
 *
 * Registers all custom blocks, block categories, and editor assets.
 * Patterns have been removed in favor of proper Gutenberg blocks
 * which provide a better editing experience.
 *
 * @package Kunaal_Theme
 * @since 4.11.2
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Compatibility guard:
 * Some WordPress installs may be behind on block APIs. If core block
 * registration functions don't exist, we should no-op instead of fataling.
 */
function kunaal_blocks_api_available() {
    return function_exists('register_block_type');
}

// ========================================
// BLOCK CATEGORIES
// ========================================

/**
 * Register Custom Block Categories
 *
 * These categories organize blocks in the editor inserter.
 * Categories are ordered by usage frequency.
 */
function kunaal_register_block_categories($categories) {
    return array_merge(
        array(
            array(
                'slug'  => 'kunaal-editorial',
                'title' => __('Editorial', 'kunaal-theme'),
                'icon'  => 'edit',
            ),
            array(
                'slug'  => 'kunaal-data',
                'title' => __('Data & Charts', 'kunaal-theme'),
                'icon'  => 'chart-line',
            ),
            array(
                'slug'  => 'kunaal-analysis',
                'title' => __('Analysis', 'kunaal-theme'),
                'icon'  => 'chart-bar',
            ),
            array(
                'slug'  => 'kunaal-interactive',
                'title' => __('Interactive', 'kunaal-theme'),
                'icon'  => 'controls-play',
            ),
        ),
        $categories
    );
}
add_filter('block_categories_all', 'kunaal_register_block_categories', 10, 1);

// ========================================
// CORE BLOCK OVERRIDES
// ========================================

/**
 * Unregister Core Blocks We Replace
 *
 * We have custom versions of these blocks that better match
 * the theme's design language.
 */
function kunaal_unregister_core_blocks() {
    if (!kunaal_blocks_api_available()) {
        return;
    }

    if (class_exists('WP_Block_Type_Registry') && function_exists('unregister_block_type')) {
        $registry = WP_Block_Type_Registry::get_instance();
        
        // Unregister core pullquote - we have kunaal/pullquote
        if ($registry->is_registered('core/pullquote')) {
            unregister_block_type('core/pullquote');
        }
    }
}
add_action('init', 'kunaal_unregister_core_blocks', 100);

// ========================================
// BLOCK DEFINITIONS
// ========================================

/**
 * All available blocks organized by category.
 *
 * Each block requires:
 * - block.json (metadata)
 * - edit.js (editor component)
 * - render.php (frontend output)
 * - style.css (styles)
 *
 * Some blocks also have:
 * - view.js (frontend JavaScript)
 */
function kunaal_get_block_definitions() {
    return array(
        // Editorial blocks - text and content formatting
        'editorial' => array(
            'insight',
            'pullquote',
            'accordion',
            'sidenote',
            'section-header',
            'takeaways',
            'takeaway-item',
            'citation',
            'aside',
            'footnote',
            'footnotes-section',
            'magazine-figure',
            'timeline',
            'timeline-item',
            'glossary',
            'glossary-term',
            'annotation',
            'source-excerpt',
            'context-panel',
            'related-reading',
            'related-link',
            'lede-package',
        ),
        
        // Analysis blocks - for making arguments and analysis
        'analysis' => array(
            'argument-map',
            'know-dont-know',
            'assumptions-register',
            'confidence-meter',
            'scenario-compare',
            'decision-log',
            'decision-entry',
            'framework-matrix',
            'causal-loop',
            'rubric',
            'rubric-row',
            'debate',
            'debate-side',
        ),
        
        // Data blocks - tables, charts, flows
        'data' => array(
            'pub-table',
            'flowchart',
            'flowchart-step',
            'chart',
            'heatmap',
            'dumbbell-chart',
            'slopegraph',
            'small-multiples',
            'statistical-distribution',
            'flow-diagram',
            'network-graph',
            'data-map',
        ),
        
        // Interactive blocks - scrollytelling and reveals
        'interactive' => array(
            'parallax-section',
            'scrollytelling',
            'scrolly-step',
            'reveal-wrapper',
        ),
    );
}

/**
 * Blocks that have frontend view scripts
 */
function kunaal_get_view_script_blocks() {
    return array(
        'sidenote',
        'annotation',
        'footnote',
        'parallax-section',
        'scrollytelling',
        'reveal-wrapper',
        'heatmap',
        'dumbbell-chart',
        'slopegraph',
        'small-multiples',
        'statistical-distribution',
        'flow-diagram',
        'network-graph',
        'data-map',
    );
}

// ========================================
// SCRIPT REGISTRATION
// ========================================

/**
 * Register Block Editor Scripts
 *
 * Registers edit.js for each block and view.js for blocks
 * that need frontend JavaScript.
 */
function kunaal_register_block_scripts() {
    if (!kunaal_blocks_api_available()) {
        return;
    }

    $blocks_dir = KUNAAL_THEME_DIR . '/blocks';
    $blocks_uri = KUNAAL_THEME_URI . '/blocks';
    $version = KUNAAL_THEME_VERSION;
    
    // WordPress dependencies for block editor scripts
    $editor_deps = array(
        'wp-blocks',
        'wp-element',
        'wp-block-editor',
        'wp-components',
        'wp-i18n',
    );
    
    // Get all block folders
    $block_definitions = kunaal_get_block_definitions();
    $all_blocks = array();
    foreach ($block_definitions as $category => $blocks) {
        $all_blocks = array_merge($all_blocks, $blocks);
    }
    
    // Register editor scripts for each block
    foreach ($all_blocks as $block) {
        $script_path = $blocks_dir . '/' . $block . '/edit.js';
        if (file_exists($script_path)) {
            wp_register_script(
                'kunaal-' . $block . '-editor',
                $blocks_uri . '/' . $block . '/edit.js',
                $editor_deps,
                $version,
                true
            );
        }
    }
    
    // Register view scripts for blocks with frontend JS
    $view_blocks = kunaal_get_view_script_blocks();
    foreach ($view_blocks as $block) {
        $view_path = $blocks_dir . '/' . $block . '/view.js';
        if (file_exists($view_path)) {
            wp_register_script(
                'kunaal-' . $block . '-view',
                $blocks_uri . '/' . $block . '/view.js',
                array(),
                $version,
                true
            );
        }
    }
}
add_action('init', 'kunaal_register_block_scripts', 5);

// ========================================
// BLOCK REGISTRATION
// ========================================

/**
 * Register Custom Gutenberg Blocks
 *
 * Registers all blocks from the /blocks directory.
 * Each block must have a valid block.json file.
 * 
 * Delegates to helper functions to reduce cognitive complexity.
 */
function kunaal_register_blocks() {
    if (!kunaal_blocks_api_available()) {
        return;
    }

    $blocks_dir = KUNAAL_THEME_DIR . '/blocks';
    
    // Get all block folders
    $block_definitions = kunaal_get_block_definitions();
    
    foreach ($block_definitions as $category => $blocks) {
        foreach ($blocks as $block) {
            $block_path = $blocks_dir . '/' . $block;
            kunaal_register_single_block($block_path, $block);
        }
    }
}
add_action('init', 'kunaal_register_blocks', 10);

// ========================================
// EDITOR ASSETS
// ========================================

// Block editor assets are now enqueued in functions.php via kunaal_enqueue_editor_assets()
// This consolidation avoids conflicts and centralizes asset management

// ========================================
// REVEAL ANIMATIONS
// ========================================

/**
 * Add Reveal Class to Core Blocks
 *
 * Adds scroll-triggered reveal animation class to core blocks
 * when viewing essays or jottings.
 * Uses WP_HTML_Tag_Processor for safe HTML manipulation (WordPress 6.2+).
 */
function kunaal_block_wrapper($block_content, $block) {
    // Only add to singular post types
    if (!is_singular(array('essay', 'jotting'))) {
        return $block_content;
    }
    
    // Blocks that should have reveal animation
    $reveal_blocks = array(
        'core/paragraph',
        'core/heading',
        'core/image',
        'core/quote',
        'core/list',
    );

    if (!in_array($block['blockName'], $reveal_blocks)) {
        return $block_content;
    }

    // Only add if not already wrapped in a reveal container
    if (strpos($block_content, 'reveal') !== false) {
        return $block_content;
    }

    // Use WP_HTML_Tag_Processor if available (WordPress 6.2+)
    if (class_exists('WP_HTML_Tag_Processor')) {
        $processor = new WP_HTML_Tag_Processor($block_content);
        if ($processor->next_tag()) {
            $existing_class = $processor->get_attribute('class');
            $new_class = $existing_class ? $existing_class . ' reveal' : 'reveal';
            $processor->set_attribute('class', $new_class);
            return $processor->get_updated_html();
        }
    }

    // Fallback for older WordPress versions: use regex (less safe but necessary)
    // Only match the first tag to avoid modifying nested elements
    $block_content = preg_replace(
        '/^<(\w+)(\s+[^>]*)?>/',
        '<$1$2 class="reveal">',
        $block_content,
        1
    );

    return $block_content;
}
add_filter('render_block', 'kunaal_block_wrapper', 10, 2);

// Block patterns removed - all patterns have been converted to proper Gutenberg blocks
