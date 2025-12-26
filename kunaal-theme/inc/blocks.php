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
            if (file_exists($block_path . '/block.json')) {
                register_block_type($block_path);
            }
        }
    }
}
add_action('init', 'kunaal_register_blocks', 10);

// ========================================
// EDITOR ASSETS
// ========================================

/**
 * Enqueue Block Editor Assets
 * 
 * Loads fonts and styles needed for block previews
 * to match the frontend appearance.
 */
function kunaal_enqueue_block_editor_assets() {
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
}
add_action('enqueue_block_editor_assets', 'kunaal_enqueue_block_editor_assets');

// ========================================
// REVEAL ANIMATIONS
// ========================================

/**
 * Add Reveal Class to Core Blocks
 * 
 * Adds scroll-triggered reveal animation class to core blocks
 * when viewing essays or jottings.
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

    if (in_array($block['blockName'], $reveal_blocks)) {
        // Only add if not already wrapped in a reveal container
        if (strpos($block_content, 'reveal') === false) {
            $block_content = preg_replace(
                '/^<(\w+)/',
                '<$1 class="reveal"',
                $block_content,
                1
            );
        }
    }

    return $block_content;
}
add_filter('render_block', 'kunaal_block_wrapper', 10, 2);

// ========================================
// BLOCK PATTERN CATEGORY
// ========================================

/**
 * Register Block Pattern Category
 * 
 * Note: Most patterns have been converted to proper Gutenberg blocks.
 * This category remains for any future quick patterns.
 */
function kunaal_register_block_pattern_categories() {
    if (!function_exists('register_block_pattern_category')) {
        return;
    }

    register_block_pattern_category(
        'kunaal-bespoke',
        array(
            'label' => __("Kunaal's Patterns", 'kunaal-theme'),
        )
    );
}
add_action('init', 'kunaal_register_block_pattern_categories');

// ========================================
// ESSENTIAL PATTERNS ONLY
// ========================================

/**
 * Register Essential Block Patterns
 * 
 * Only patterns that don't have block equivalents.
 * Most patterns have been converted to proper blocks.
 */
function kunaal_register_essential_patterns() {
    if (!function_exists('register_block_pattern')) {
        return;
    }

    // Hero Image Pattern - useful for quick hero setup
    register_block_pattern(
        'kunaal/hero-image',
        array(
            'title' => __('Hero Image (Fades to Background)', 'kunaal-theme'),
            'description' => __('Full-width image that fades into the page', 'kunaal-theme'),
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"heroImage reveal"} -->
<div class="wp-block-group heroImage reveal">
    <!-- wp:image {"sizeSlug":"full"} -->
    <figure class="wp-block-image size-full"><img src="" alt=""/></figure>
    <!-- /wp:image -->
</div>
<!-- /wp:group -->',
        )
    );
    
    // Split Layout Pattern - common layout need
    register_block_pattern(
        'kunaal/split-layout',
        array(
            'title' => __('Split Layout (Image + Text)', 'kunaal-theme'),
            'description' => __('Two-column layout with image and text', 'kunaal-theme'),
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"split reveal"} -->
<div class="wp-block-group split reveal">
    <!-- wp:group {"className":"splitImg reveal-left"} -->
    <div class="wp-block-group splitImg reveal-left">
        <!-- wp:image -->
        <figure class="wp-block-image"><img src="" alt=""/></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:group -->
    <!-- wp:group {"className":"splitText reveal-right"} -->
    <div class="wp-block-group splitText reveal-right">
        <div class="label">LABEL</div>
        <!-- wp:heading {"level":3} -->
        <h3>Heading</h3>
        <!-- /wp:heading -->
        <!-- wp:paragraph -->
        <p>Description text goes here...</p>
        <!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->
</div>
<!-- /wp:group -->',
        )
    );
    
    // Drop Cap Opening - common editorial style
    register_block_pattern(
        'kunaal/drop-cap',
        array(
            'title' => __('Drop Cap Opening', 'kunaal-theme'),
            'description' => __('Classic editorial drop cap for article openers', 'kunaal-theme'),
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:paragraph {"dropCap":true,"className":"article-opener"} -->
<p class="has-drop-cap article-opener">Write your opening paragraph here. The first letter will appear as an elegant drop cap, setting the tone for a thoughtful, magazine-quality article.</p>
<!-- /wp:paragraph -->',
        )
    );
}
add_action('init', 'kunaal_register_essential_patterns');
