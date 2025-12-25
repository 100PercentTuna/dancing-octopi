<?php
/**
 * Gutenberg Block Registration
 *
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Block Categories
 */
function kunaal_register_block_categories($categories) {
    return array_merge(
        array(
            array(
                'slug'  => 'kunaal-editorial',
                'title' => __('Kunaal — Editorial', 'kunaal-theme'),
                'icon'  => 'edit',
            ),
            array(
                'slug'  => 'kunaal-analysis',
                'title' => __('Kunaal — Analysis', 'kunaal-theme'),
                'icon'  => 'chart-bar',
            ),
            array(
                'slug'  => 'kunaal-data',
                'title' => __('Kunaal — Data', 'kunaal-theme'),
                'icon'  => 'chart-line',
            ),
            array(
                'slug'  => 'kunaal-interactive',
                'title' => __('Kunaal — Interactive', 'kunaal-theme'),
                'icon'  => 'controls-play',
            ),
            array(
                'slug'  => 'kunaal-jottings',
                'title' => __('Kunaal — Jottings', 'kunaal-theme'),
                'icon'  => 'edit-page',
            ),
        ),
        $categories
    );
}
add_filter('block_categories_all', 'kunaal_register_block_categories', 10, 1);

/**
 * Unregister duplicate core blocks (we have our own versions)
 */
function kunaal_unregister_core_blocks() {
    // Unregister core pullquote - we have kunaal/pullquote
    // Check if block registry exists and block is registered before unregistering
    if (class_exists('WP_Block_Type_Registry')) {
        $registry = WP_Block_Type_Registry::get_instance();
        if ($registry->is_registered('core/pullquote')) {
            unregister_block_type('core/pullquote');
        }
    }
}
add_action('init', 'kunaal_unregister_core_blocks', 100);

/**
 * Register block editor scripts
 */
function kunaal_register_block_scripts() {
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
    
    // Register editor scripts for each block
    $blocks = array(
        'insight'           => 'kunaal-insight-editor',
        'pullquote'         => 'kunaal-pullquote-editor',
        'accordion'         => 'kunaal-accordion-editor',
        'sidenote'          => 'kunaal-sidenote-editor',
        'section-header'    => 'kunaal-section-header-editor',
        'takeaways'         => 'kunaal-takeaways-editor',
        'takeaway-item'     => 'kunaal-takeaway-item-editor',
        'citation'          => 'kunaal-citation-editor',
        'aside'             => 'kunaal-aside-editor',
        'footnote'          => 'kunaal-footnote-editor',
        'footnotes-section' => 'kunaal-footnotes-section-editor',
        'parallax-section'  => 'kunaal-parallax-section-editor',
        'scrollytelling'    => 'kunaal-scrollytelling-editor',
        'scrolly-step'      => 'kunaal-scrolly-step-editor',
        'reveal-wrapper'    => 'kunaal-reveal-wrapper-editor',
        // Epic 2 - Editorial blocks
        'magazine-figure'   => 'kunaal-magazine-figure-editor',
        'timeline'          => 'kunaal-timeline-editor',
        'timeline-item'     => 'kunaal-timeline-item-editor',
        'glossary'          => 'kunaal-glossary-editor',
        'glossary-term'     => 'kunaal-glossary-term-editor',
        'annotation'        => 'kunaal-annotation-editor',
        'argument-map'      => 'kunaal-argument-map-editor',
        'know-dont-know'    => 'kunaal-know-dont-know-editor',
        'source-excerpt'    => 'kunaal-source-excerpt-editor',
        'context-panel'     => 'kunaal-context-panel-editor',
        'related-reading'   => 'kunaal-related-reading-editor',
        'related-link'      => 'kunaal-related-link-editor',
        'lede-package'      => 'kunaal-lede-package-editor',
        // Epic 3 - Analysis blocks
        'assumptions-register' => 'kunaal-assumptions-register-editor',
        'confidence-meter'  => 'kunaal-confidence-meter-editor',
        'scenario-compare'  => 'kunaal-scenario-compare-editor',
        'decision-log'      => 'kunaal-decision-log-editor',
        'decision-entry'    => 'kunaal-decision-entry-editor',
        'framework-matrix'  => 'kunaal-framework-matrix-editor',
        'causal-loop'       => 'kunaal-causal-loop-editor',
        'rubric'            => 'kunaal-rubric-editor',
        'rubric-row'        => 'kunaal-rubric-row-editor',
        'debate'            => 'kunaal-debate-editor',
        'debate-side'       => 'kunaal-debate-side-editor',
        // Epic 4 - Data blocks
        'pub-table'         => 'kunaal-pub-table-editor',
        'flowchart'         => 'kunaal-flowchart-editor',
        'flowchart-step'    => 'kunaal-flowchart-step-editor',
        'chart'             => 'kunaal-chart-editor',
    );
    
    foreach ($blocks as $folder => $handle) {
        $script_path = $blocks_dir . '/' . $folder . '/edit.js';
        if (file_exists($script_path)) {
            wp_register_script(
                $handle,
                $blocks_uri . '/' . $folder . '/edit.js',
                $editor_deps,
                $version,
                true
            );
        }
    }
    
    // Register sidenote view script (frontend)
    $sidenote_view = $blocks_dir . '/sidenote/view.js';
    if (file_exists($sidenote_view)) {
        wp_register_script(
            'kunaal-sidenote-view',
            $blocks_uri . '/sidenote/view.js',
            array(),
            $version,
            true
        );
    }
    
    // Register annotation view script (frontend)
    $annotation_view = $blocks_dir . '/annotation/view.js';
    if (file_exists($annotation_view)) {
        wp_register_script(
            'kunaal-annotation-view',
            $blocks_uri . '/annotation/view.js',
            array(),
            $version,
            true
        );
    }
}
add_action('init', 'kunaal_register_block_scripts', 5);

/**
 * Register Custom Gutenberg Blocks
 */
function kunaal_register_blocks() {
    $blocks_dir = KUNAAL_THEME_DIR . '/blocks';
    
    // Block directories to register
    $block_folders = array(
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
        'parallax-section',
        'scrollytelling',
        'scrolly-step',
        'reveal-wrapper',
        // Epic 2 - Editorial blocks
        'magazine-figure',
        'timeline',
        'timeline-item',
        'glossary',
        'glossary-term',
        'annotation',
        'argument-map',
        'know-dont-know',
        'source-excerpt',
        'context-panel',
        'related-reading',
        'related-link',
        'lede-package',
        // Epic 3 - Analysis blocks
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
        // Epic 4 - Data blocks
        'pub-table',
        'flowchart',
        'flowchart-step',
        'chart',
    );
    
    foreach ($block_folders as $block) {
        $block_path = $blocks_dir . '/' . $block;
        if (file_exists($block_path . '/block.json')) {
            register_block_type($block_path);
        }
    }
}
add_action('init', 'kunaal_register_blocks', 10);

/**
 * Enqueue block editor assets
 */
function kunaal_enqueue_block_editor_assets() {
    // Enqueue Caveat font for sidenote block preview
    wp_enqueue_style(
        'kunaal-caveat-editor',
        'https://fonts.googleapis.com/css2?family=Caveat:wght@400;500;600&display=swap',
        array(),
        null
    );
    
    // Enqueue theme's main stylesheet for block previews
    wp_enqueue_style(
        'kunaal-blocks-editor',
        KUNAAL_THEME_URI . '/style.css',
        array(),
        KUNAAL_THEME_VERSION
    );
}
add_action('enqueue_block_editor_assets', 'kunaal_enqueue_block_editor_assets');

/**
 * Register Custom Inline Formats
 * Sidenote, Highlight, Definition, Key Term, Data Reference
 */
function kunaal_register_inline_formats() {
    $formats_dir = KUNAAL_THEME_DIR . '/blocks/inline-formats';
    $formats_uri = KUNAAL_THEME_URI . '/blocks/inline-formats';
    
    // Only register if file exists
    if (!file_exists($formats_dir . '/index.js')) {
        return;
    }
    
    // Register editor script for inline formats
    // Note: wp-dom-ready is needed for proper initialization
    wp_register_script(
        'kunaal-inline-formats',
        $formats_uri . '/index.js',
        array('wp-rich-text', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-dom-ready'),
        KUNAAL_THEME_VERSION,
        true
    );
    
    // Register styles for inline formats (both editor and frontend)
    wp_register_style(
        'kunaal-inline-formats-style',
        $formats_uri . '/style.css',
        array(),
        KUNAAL_THEME_VERSION
    );
}
add_action('init', 'kunaal_register_inline_formats', 5);

/**
 * Enqueue Inline Formats in Editor
 */
function kunaal_enqueue_inline_formats_editor() {
    // Only run in admin
    if (!is_admin()) {
        return;
    }
    
    // Only enqueue in block editor context
    if (!function_exists('get_current_screen')) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen) {
        return;
    }
    
    // Check if we're editing a supported post type
    $supported_types = array('essay', 'jotting', 'page', 'post');
    if (!isset($screen->post_type) || !in_array($screen->post_type, $supported_types)) {
        return;
    }
    
    // Only enqueue if script is registered (file exists)
    if (wp_script_is('kunaal-inline-formats', 'registered')) {
        wp_enqueue_script('kunaal-inline-formats');
    }
    if (wp_style_is('kunaal-inline-formats-style', 'registered')) {
        wp_enqueue_style('kunaal-inline-formats-style');
    }
}
add_action('enqueue_block_editor_assets', 'kunaal_enqueue_inline_formats_editor');

/**
 * Enqueue Inline Formats Styles on Frontend
 */
function kunaal_enqueue_inline_formats_frontend() {
    if (is_singular(array('essay', 'jotting', 'page'))) {
        if (wp_style_is('kunaal-inline-formats-style', 'registered')) {
            wp_enqueue_style('kunaal-inline-formats-style');
        }
    }
}
add_action('wp_enqueue_scripts', 'kunaal_enqueue_inline_formats_frontend');

/**
 * Register Custom Block Patterns
 */
function kunaal_register_block_patterns() {
    // Pull Quote Pattern
    register_block_pattern(
        'kunaal/pullquote',
        array(
            'title' => 'Pull Quote (Blue)',
            'description' => 'A highlighted quote with blue accent',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"pullquote"} -->
<div class="wp-block-group pullquote">
    <!-- wp:quote -->
    <blockquote class="wp-block-quote"><p>Your quote text here...</p><cite>— Author Name</cite></blockquote>
    <!-- /wp:quote -->
</div>
<!-- /wp:group -->',
        )
    );

    // Key Insight Pattern
    register_block_pattern(
        'kunaal/insight',
        array(
            'title' => 'Key Insight Box',
            'description' => 'A warm-colored insight callout',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"insightBox"} -->
<div class="wp-block-group insightBox">
    <div class="label">KEY INSIGHT</div>
    <!-- wp:paragraph -->
    <p>Your insight text here. Use <strong>bold</strong> for emphasis.</p>
    <!-- /wp:paragraph -->
</div>
<!-- /wp:group -->',
        )
    );

    // Section Header Pattern
    register_block_pattern(
        'kunaal/section-header',
        array(
            'title' => 'Section Header with Number',
            'description' => 'A numbered section heading',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"sectionHead reveal"} -->
<div class="wp-block-group sectionHead reveal">
    <!-- wp:heading {"anchor":"section-1"} -->
    <h2 class="wp-block-heading" id="section-1">Section Title</h2>
    <!-- /wp:heading -->
    <span class="sectionNum">01</span>
</div>
<!-- /wp:group -->',
        )
    );

    // Accordion Pattern
    register_block_pattern(
        'kunaal/accordion',
        array(
            'title' => 'Accordion',
            'description' => 'Expandable content section',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<details class="accordion reveal">
  <summary><span>Click to expand</span><span class="marker">+</span></summary>
  <div class="accBody">Your expandable content here...</div>
</details>
<!-- /wp:html -->',
        )
    );

    // Split Layout (Image + Text) Pattern
    register_block_pattern(
        'kunaal/split-layout',
        array(
            'title' => 'Split Layout (Image + Text)',
            'description' => 'Two-column layout with image and text',
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

    // Split Reverse Pattern
    register_block_pattern(
        'kunaal/split-layout-reverse',
        array(
            'title' => 'Split Layout Reverse (Text + Image)',
            'description' => 'Two-column layout with text first, then image',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"split reverse reveal"} -->
<div class="wp-block-group split reverse reveal">
    <!-- wp:group {"className":"splitImg reveal-right"} -->
    <div class="wp-block-group splitImg reveal-right">
        <!-- wp:image -->
        <figure class="wp-block-image"><img src="" alt=""/></figure>
        <!-- /wp:image -->
    </div>
    <!-- /wp:group -->
    <!-- wp:group {"className":"splitText reveal-left"} -->
    <div class="wp-block-group splitText reveal-left">
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

    // Code Block Pattern
    register_block_pattern(
        'kunaal/code-block',
        array(
            'title' => 'Code Block with Copy',
            'description' => 'Styled code block with copy button',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="codeBlock reveal">
  <div class="codeHead">
    <span>JavaScript</span>
    <button onclick="navigator.clipboard.writeText(this.closest(\'.codeBlock\').querySelector(\'pre\').textContent);this.textContent=\'Copied!\';setTimeout(()=>this.textContent=\'Copy\',1500)">Copy</button>
  </div>
  <pre><code>// Your code here
const example = "Hello World";
console.log(example);</code></pre>
</div>
<!-- /wp:html -->',
        )
    );

    // Chart Pattern
    register_block_pattern(
        'kunaal/chart',
        array(
            'title' => 'Chart Container',
            'description' => 'Container for SVG charts',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"chartWrap reveal"} -->
<div class="wp-block-group chartWrap reveal">
    <div class="chartCaption">CHART TITLE</div>
    <div class="chartBody">
        <!-- wp:html -->
        <svg viewBox="0 0 400 200">
            <!-- Add your SVG chart here -->
            <rect x="50" y="50" width="60" height="100" fill="#1E5AFF" opacity="0.7"/>
            <rect x="130" y="80" width="60" height="70" fill="#B8A99A"/>
            <rect x="210" y="60" width="60" height="90" fill="#1E5AFF" opacity="0.7"/>
            <rect x="290" y="100" width="60" height="50" fill="#B8A99A"/>
        </svg>
        <!-- /wp:html -->
    </div>
    <div class="chartNote">Chart description or legend</div>
</div>
<!-- /wp:group -->',
        )
    );

    // Aside / Case Study Pattern
    register_block_pattern(
        'kunaal/aside',
        array(
            'title' => 'Aside / Case Study',
            'description' => 'Warm background callout box',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"aside reveal"} -->
<div class="wp-block-group aside reveal">
    <!-- wp:paragraph -->
    <p>First paragraph of the aside...</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>Second paragraph...</p>
    <!-- /wp:paragraph -->
    <div class="outcome">Result: <strong>Key outcome here</strong></div>
</div>
<!-- /wp:group -->',
        )
    );

    // Citation Pattern
    register_block_pattern(
        'kunaal/citation',
        array(
            'title' => 'Centered Citation',
            'description' => 'A centered quote with author',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"citation reveal"} -->
<div class="wp-block-group citation reveal">
    <!-- wp:quote -->
    <blockquote class="wp-block-quote"><p>"Your centered quote here..."</p></blockquote>
    <!-- /wp:quote -->
    <div class="author">— Author Name</div>
</div>
<!-- /wp:group -->',
        )
    );

    // Takeaways Pattern
    register_block_pattern(
        'kunaal/takeaways',
        array(
            'title' => 'Takeaways Section',
            'description' => 'Numbered takeaways list',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:group {"className":"takeaways reveal","anchor":"takeaways"} -->
<div class="wp-block-group takeaways reveal" id="takeaways">
    <!-- wp:heading -->
    <h2>Takeaways</h2>
    <!-- /wp:heading -->
    <!-- wp:html -->
    <ol class="takeList">
        <li class="reveal">
            <div>
                <h4>First Takeaway</h4>
                <p>Description of the first takeaway.</p>
            </div>
        </li>
        <li class="reveal">
            <div>
                <h4>Second Takeaway</h4>
                <p>Description of the second takeaway.</p>
            </div>
        </li>
        <li class="reveal">
            <div>
                <h4>Third Takeaway</h4>
                <p>Description of the third takeaway.</p>
            </div>
        </li>
    </ol>
    <!-- /wp:html -->
</div>
<!-- /wp:group -->',
        )
    );

    // Scrolly Section Pattern
    register_block_pattern(
        'kunaal/scrolly',
        array(
            'title' => 'Scrolly / Interactive Steps',
            'description' => 'Sticky sidebar with scrolling steps',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<section class="scrolly">
  <div class="scrollyInner">
    <div class="scrollySticky">
      <div class="label">SECTION LABEL</div>
      <h3 id="scrollyTitle">Dynamic Title</h3>
      <p id="scrollyNote">This text updates as you scroll through the steps.</p>
    </div>
    <div class="scrollySteps">
      <div class="step active" data-title="First Step Title" data-note="First step description.">
        <h4>Step 1 Heading</h4>
        <p>Detailed content for step 1.</p>
      </div>
      <div class="step" data-title="Second Step Title" data-note="Second step description.">
        <h4>Step 2 Heading</h4>
        <p>Detailed content for step 2.</p>
      </div>
      <div class="step" data-title="Third Step Title" data-note="Third step description.">
        <h4>Step 3 Heading</h4>
        <p>Detailed content for step 3.</p>
      </div>
    </div>
  </div>
</section>
<!-- /wp:html -->',
        )
    );

    // Hero Image Pattern
    register_block_pattern(
        'kunaal/hero-image',
        array(
            'title' => 'Hero Image (Fades to Background)',
            'description' => 'Full-width image that fades into the page',
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

    // Canvas / Visualization Pattern
    register_block_pattern(
        'kunaal/canvas',
        array(
            'title' => 'Interactive Canvas',
            'description' => 'Container for JavaScript visualizations',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="canvasWrap reveal">
  <canvas id="myVisualization"></canvas>
  <div class="canvasCaption">Interactive: Visualization Title</div>
</div>
<script>
// Add your visualization code here
(function() {
  const canvas = document.getElementById("myVisualization");
  if (!canvas) return;
  const ctx = canvas.getContext("2d");
  // Your visualization code...
})();
</script>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // PARALLAX & IMMERSIVE PATTERNS
    // =====================================================
    
    // Parallax Hero Section
    register_block_pattern(
        'kunaal/parallax-hero',
        array(
            'title' => 'Parallax Hero Section',
            'description' => 'Full-screen parallax image with text overlay',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<section class="parallax-hero" style="background-image: url(\'/wp-content/uploads/your-image.jpg\');">
  <div class="parallax-content">
    <h2 class="parallax-title">Your Headline</h2>
    <p class="parallax-subtitle">A compelling subtitle that draws readers in</p>
  </div>
</section>
<style>
.parallax-hero {
  height: 80vh;
  min-height: 500px;
  background-attachment: fixed;
  background-position: center;
  background-size: cover;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  margin: 3rem -50vw;
  margin-left: calc(-50vw + 50%);
  margin-right: calc(-50vw + 50%);
  width: 100vw;
}
.parallax-hero::before {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, transparent 0%, rgba(11,18,32,0.7) 100%);
}
.parallax-content {
  position: relative;
  z-index: 1;
  text-align: center;
  color: white;
  max-width: 700px;
  padding: 2rem;
}
.parallax-title {
  font-family: var(--serif);
  font-size: clamp(2rem, 5vw, 3.5rem);
  font-weight: 600;
  margin: 0 0 1rem;
  text-shadow: 0 2px 20px rgba(0,0,0,0.4);
}
.parallax-subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
  text-shadow: 0 1px 10px rgba(0,0,0,0.4);
}
@media (max-width: 768px) {
  .parallax-hero { background-attachment: scroll; height: 60vh; }
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Parallax Split Section
    register_block_pattern(
        'kunaal/parallax-split',
        array(
            'title' => 'Parallax Split Panel',
            'description' => 'Side-by-side parallax with text',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<section class="parallax-split">
  <div class="parallax-split-image" style="background-image: url(\'/wp-content/uploads/your-image.jpg\');"></div>
  <div class="parallax-split-text">
    <span class="split-label">Chapter one</span>
    <h3>Section title here</h3>
    <p>Your narrative text goes here. This creates an immersive reading experience with the image scrolling at a different rate than the text.</p>
  </div>
</section>
<style>
.parallax-split {
  display: grid;
  grid-template-columns: 1fr 1fr;
  min-height: 70vh;
  margin: 3rem 0;
}
.parallax-split-image {
  background-attachment: fixed;
  background-position: center;
  background-size: cover;
}
.parallax-split-text {
  padding: 4rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background: var(--bgWarm, #F9F7F4);
}
.split-label {
  font-size: 11px;
  letter-spacing: 0.1em;
  color: var(--warm, #7D6B5D);
  margin-bottom: 1rem;
}
.parallax-split-text h3 {
  font-family: var(--serif);
  font-size: 1.75rem;
  margin: 0 0 1rem;
}
.parallax-split-text p {
  color: var(--muted);
  line-height: 1.7;
}
@media (max-width: 768px) {
  .parallax-split { grid-template-columns: 1fr; }
  .parallax-split-image { height: 50vh; background-attachment: scroll; }
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // EDITORIAL PATTERNS (NYT/Atlantic Style)
    // =====================================================
    
    // Drop Cap Paragraph
    register_block_pattern(
        'kunaal/drop-cap',
        array(
            'title' => 'Drop Cap Opening',
            'description' => 'Classic editorial drop cap for article openers',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:paragraph {"dropCap":true,"className":"article-opener"} -->
<p class="has-drop-cap article-opener">Write your opening paragraph here. The first letter will appear as an elegant drop cap, setting the tone for a thoughtful, magazine-quality article. This style is commonly used in publications like The New Yorker and The Atlantic.</p>
<!-- /wp:paragraph -->',
        )
    );
    
    // Sidenote Pattern
    register_block_pattern(
        'kunaal/sidenote',
        array(
            'title' => 'Sidenote (Tufte Style)',
            'description' => 'Marginal note for supplementary information',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<span class="sidenote-ref">[1]</span>
<aside class="sidenote">
  <span class="sidenote-num">1.</span>
  Your sidenote content here. These appear in the margin on desktop and inline on mobile.
</aside>
<style>
.sidenote-ref {
  font-size: 0.75em;
  vertical-align: super;
  color: var(--blue);
  cursor: help;
}
.sidenote {
  float: right;
  clear: right;
  margin-right: -220px;
  width: 180px;
  font-size: 13px;
  color: var(--muted);
  line-height: 1.5;
  position: relative;
}
.sidenote-num {
  color: var(--blue);
  font-weight: 500;
}
@media (max-width: 1100px) {
  .sidenote {
    float: none;
    margin: 1rem 0;
    width: 100%;
    padding: 1rem;
    background: var(--bgWarm);
    border-left: 2px solid var(--blue);
  }
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Pull Quote Large
    register_block_pattern(
        'kunaal/pullquote-large',
        array(
            'title' => 'Pull Quote (Large Center)',
            'description' => 'Large centered pull quote for emphasis',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<blockquote class="pullquote-large reveal">
  <p>"Your powerful quote goes here—something that captures the essence of your argument."</p>
</blockquote>
<style>
.pullquote-large {
  font-family: var(--serif);
  font-size: clamp(1.5rem, 3vw, 2rem);
  font-weight: 400;
  font-style: italic;
  text-align: center;
  color: var(--ink);
  margin: 3rem auto;
  padding: 2rem 0;
  max-width: 700px;
  border-top: 1px solid var(--hair);
  border-bottom: 1px solid var(--hair);
}
.pullquote-large p { margin: 0; }
</style>
<!-- /wp:html -->',
        )
    );
    
    // Author Attribution Box
    register_block_pattern(
        'kunaal/author-box',
        array(
            'title' => 'Author Attribution',
            'description' => 'Author bio box for end of articles',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="author-box reveal">
  <div class="author-avatar">KW</div>
  <div class="author-info">
    <span class="author-label">About the Author</span>
    <strong class="author-name">Author Name</strong>
    <p class="author-bio">A brief bio about the author. What they do, their interests, and why they write about these topics.</p>
  </div>
</div>
<style>
.author-box {
  display: flex;
  gap: 1.5rem;
  padding: 1.5rem;
  background: var(--bgWarm);
  border-radius: 4px;
  margin: 3rem 0;
}
.author-avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--warm);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  flex-shrink: 0;
}
.author-label {
  font-size: 11px;
  
  letter-spacing: 0.08em;
  color: var(--warm);
}
.author-name {
  display: block;
  margin: 0.25rem 0 0.5rem;
  font-family: var(--serif);
}
.author-bio {
  font-size: 14px;
  color: var(--muted);
  margin: 0;
  line-height: 1.6;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // CHART PATTERNS
    // =====================================================
    
    // Bar Chart
    register_block_pattern(
        'kunaal/chart-bar',
        array(
            'title' => 'Chart: Bar Chart',
            'description' => 'Horizontal or vertical bar chart',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Chart Title: What This Shows</figcaption>
  <div class="barChart" id="barChart1">
    <div class="bar" style="--value: 85%;" data-label="Category A" data-value="85%"></div>
    <div class="bar" style="--value: 62%;" data-label="Category B" data-value="62%"></div>
    <div class="bar" style="--value: 91%;" data-label="Category C" data-value="91%"></div>
    <div class="bar" style="--value: 48%;" data-label="Category D" data-value="48%"></div>
  </div>
  <p class="chartSource">Source: Your Data Source</p>
</figure>
<style>
.barChart {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 1rem 0;
}
.barChart .bar {
  display: flex;
  align-items: center;
  gap: 12px;
}
.barChart .bar::before {
  content: attr(data-label);
  width: 100px;
  font-size: 13px;
  color: var(--muted);
  flex-shrink: 0;
}
.barChart .bar::after {
  content: "";
  height: 24px;
  width: var(--value);
  background: var(--blue);
  border-radius: 2px;
  transition: width 1s ease-out;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Vertical Bar Chart
    register_block_pattern(
        'kunaal/chart-bar-vertical',
        array(
            'title' => 'Chart: Vertical Bar Chart',
            'description' => 'Vertical column chart',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Chart Title: What This Shows</figcaption>
  <div class="barChartVertical" id="barChartV1">
    <div class="barV" style="--value: 85%;" data-label="Q1" data-value="85%"></div>
    <div class="barV" style="--value: 62%;" data-label="Q2" data-value="62%"></div>
    <div class="barV" style="--value: 91%;" data-label="Q3" data-value="91%"></div>
    <div class="barV" style="--value: 48%;" data-label="Q4" data-value="48%"></div>
  </div>
  <p class="chartSource">Source: Your Data Source</p>
</figure>
<style>
.barChartVertical {
  display: flex;
  align-items: flex-end;
  justify-content: space-around;
  gap: 16px;
  padding: 1rem 0;
  height: 300px;
}
.barChartVertical .barV {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  flex: 1;
  max-width: 80px;
}
.barChartVertical .barV::before {
  content: "";
  width: 100%;
  height: var(--value);
  background: var(--blue);
  border-radius: 2px 2px 0 0;
  transition: height 1s ease-out;
  order: -1;
}
.barChartVertical .barV::after {
  content: attr(data-label);
  font-size: 13px;
  color: var(--muted);
  text-align: center;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Donut Chart
    register_block_pattern(
        'kunaal/chart-donut',
        array(
            'title' => 'Chart: Donut Chart',
            'description' => 'Circular percentage chart',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Percentage Breakdown</figcaption>
  <div class="donutChart">
    <svg viewBox="0 0 100 100">
      <circle cx="50" cy="50" r="40" class="donut-bg"/>
      <circle cx="50" cy="50" r="40" class="donut-segment" style="--percent: 65; --color: var(--blue);" stroke-dasharray="163.36 251.2"/>
      <circle cx="50" cy="50" r="40" class="donut-segment" style="--percent: 25; --color: var(--warm);" stroke-dasharray="62.83 251.2" stroke-dashoffset="-163.36"/>
      <circle cx="50" cy="50" r="40" class="donut-segment" style="--percent: 10; --color: var(--warmLight);" stroke-dasharray="25.13 251.2" stroke-dashoffset="-226.19"/>
    </svg>
    <div class="donut-center">
      <strong>65%</strong>
      <span>Primary</span>
    </div>
  </div>
  <div class="donut-legend">
    <span><i style="background: var(--blue);"></i> Category A (65%)</span>
    <span><i style="background: var(--warm);"></i> Category B (25%)</span>
    <span><i style="background: var(--warmLight);"></i> Category C (10%)</span>
  </div>
</figure>
<style>
.donutChart {
  width: 200px;
  height: 200px;
  margin: 1rem auto;
  position: relative;
}
.donutChart svg { transform: rotate(-90deg); }
.donut-bg { fill: none; stroke: var(--hair); stroke-width: 12; }
.donut-segment { fill: none; stroke: var(--color); stroke-width: 12; }
.donut-center {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.donut-center strong { font-size: 2rem; font-family: var(--serif); }
.donut-center span { font-size: 12px; color: var(--muted); }
.donut-legend {
  display: flex;
  justify-content: center;
  gap: 1rem;
  flex-wrap: wrap;
  font-size: 12px;
}
.donut-legend i {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 2px;
  margin-right: 4px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Line Chart
    register_block_pattern(
        'kunaal/chart-line',
        array(
            'title' => 'Chart: Line/Area Chart',
            'description' => 'Trend line visualization',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Trend Over Time</figcaption>
  <div class="lineChart">
    <svg viewBox="0 0 400 150" preserveAspectRatio="none">
      <defs>
        <linearGradient id="lineGrad" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" style="stop-color: var(--blue); stop-opacity: 0.3"/>
          <stop offset="100%" style="stop-color: var(--blue); stop-opacity: 0"/>
        </linearGradient>
      </defs>
      <!-- Area fill -->
      <path d="M0,120 L50,100 L100,80 L150,90 L200,60 L250,40 L300,50 L350,30 L400,20 L400,150 L0,150 Z" fill="url(#lineGrad)"/>
      <!-- Line -->
      <path d="M0,120 L50,100 L100,80 L150,90 L200,60 L250,40 L300,50 L350,30 L400,20" fill="none" stroke="var(--blue)" stroke-width="2"/>
      <!-- Data points -->
      <circle cx="0" cy="120" r="4" fill="var(--blue)"/>
      <circle cx="100" cy="80" r="4" fill="var(--blue)"/>
      <circle cx="200" cy="60" r="4" fill="var(--blue)"/>
      <circle cx="300" cy="50" r="4" fill="var(--blue)"/>
      <circle cx="400" cy="20" r="4" fill="var(--blue)"/>
    </svg>
    <div class="lineChart-labels">
      <span>2020</span><span>2021</span><span>2022</span><span>2023</span><span>2024</span>
    </div>
  </div>
  <p class="chartSource">Source: Your Data Source</p>
</figure>
<style>
.lineChart { padding: 1rem 0; }
.lineChart svg { width: 100%; height: 150px; }
.lineChart-labels {
  display: flex;
  justify-content: space-between;
  font-size: 11px;
  color: var(--muted);
  margin-top: 8px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Comparison Table
    register_block_pattern(
        'kunaal/comparison-table',
        array(
            'title' => 'Comparison Table',
            'description' => 'Clean data comparison table',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="tableWrap reveal">
  <figcaption class="tableCaption">Table 1: Comparison of Key Metrics</figcaption>
  <table class="dataTable">
    <thead>
      <tr>
        <th>Metric</th>
        <th>Option A</th>
        <th>Option B</th>
        <th>Option C</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Performance</td>
        <td><span class="metric high">92%</span></td>
        <td><span class="metric medium">78%</span></td>
        <td><span class="metric low">45%</span></td>
      </tr>
      <tr>
        <td>Reliability</td>
        <td><span class="metric high">88%</span></td>
        <td><span class="metric high">91%</span></td>
        <td><span class="metric medium">72%</span></td>
      </tr>
      <tr>
        <td>Cost Index</td>
        <td>$120</td>
        <td>$85</td>
        <td>$150</td>
      </tr>
    </tbody>
  </table>
</figure>
<style>
.tableWrap { margin: 2rem 0; overflow-x: auto; }
.tableCaption { font-size: 13px; font-weight: 500; margin-bottom: 0.75rem; }
.dataTable {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}
.dataTable th, .dataTable td {
  padding: 12px 16px;
  text-align: left;
  border-bottom: 1px solid var(--hair);
}
.dataTable th {
  font-size: 11px;
  
  letter-spacing: 0.05em;
  color: var(--warm);
  background: var(--bgWarm);
}
.dataTable tbody tr:hover { background: var(--blueTint); }
.metric {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 3px;
  font-weight: 500;
  font-size: 12px;
}
.metric.high { background: rgba(30, 90, 255, 0.1); color: var(--blue); }
.metric.medium { background: rgba(125, 107, 93, 0.15); color: var(--warm); }
.metric.low { background: rgba(200, 100, 100, 0.15); color: #b35050; }
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // JOTTING-SPECIFIC PATTERNS
    // =====================================================
    
    // Quick Thought
    register_block_pattern(
        'kunaal/quick-thought',
        array(
            'title' => 'Quick Thought (Jotting)',
            'description' => 'Brief observation or idea',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:paragraph {"className":"jotting-thought"} -->
<p class="jotting-thought">A fleeting observation, a half-formed idea, or a moment of clarity worth capturing before it fades...</p>
<!-- /wp:paragraph -->',
        )
    );
    
    // Reading Note
    register_block_pattern(
        'kunaal/reading-note',
        array(
            'title' => 'Reading Note (Jotting)',
            'description' => 'Note from something you read',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="reading-note reveal">
  <div class="reading-source">
    <span class="source-type">📚 FROM</span>
    <cite>Book or Article Title</cite>
    <span class="source-author">by Author Name</span>
  </div>
  <blockquote class="reading-quote">
    "The passage or quote that struck you..."
  </blockquote>
  <div class="reading-reflection">
    <span class="reflection-label">My Reflection</span>
    <p>Your thoughts on why this resonated or what it made you think about...</p>
  </div>
</div>
<style>
.reading-note {
  background: var(--bgWarm);
  padding: 1.5rem;
  border-radius: 4px;
  margin: 1.5rem 0;
}
.reading-source { margin-bottom: 1rem; }
.source-type {
  display: block;
  font-size: 10px;
  letter-spacing: 0.1em;
  color: var(--warm);
  margin-bottom: 4px;
}
.reading-source cite {
  font-family: var(--serif);
  font-style: normal;
  font-weight: 500;
}
.source-author {
  display: block;
  font-size: 13px;
  color: var(--muted);
}
.reading-quote {
  font-family: var(--serif);
  font-style: italic;
  border-left: 2px solid var(--blue);
  padding-left: 1rem;
  margin: 1rem 0;
}
.reflection-label {
  font-size: 11px;
  
  letter-spacing: 0.08em;
  color: var(--warm);
}
.reading-reflection p {
  margin: 0.5rem 0 0;
  font-size: 14px;
  color: var(--muted);
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Link Collection
    register_block_pattern(
        'kunaal/link-collection',
        array(
            'title' => 'Link Collection (Jotting)',
            'description' => 'Curated links with notes',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="link-collection reveal">
  <h4 class="links-heading">Worth Reading</h4>
  <div class="link-item">
    <a href="#" class="link-title">Article or Resource Title →</a>
    <p class="link-note">A brief note on why this is worth checking out.</p>
  </div>
  <div class="link-item">
    <a href="#" class="link-title">Another Interesting Link →</a>
    <p class="link-note">Your commentary or context for this link.</p>
  </div>
</div>
<style>
.link-collection { margin: 1.5rem 0; }
.links-heading {
  font-size: 13px;
  
  letter-spacing: 0.08em;
  color: var(--warm);
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--hair);
}
.link-item { margin-bottom: 1rem; }
.link-title {
  font-weight: 500;
  color: var(--ink);
  text-decoration: none;
}
.link-title:hover { color: var(--blue); }
.link-note {
  font-size: 13px;
  color: var(--muted);
  margin: 4px 0 0;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // SCROLL ANIMATION PATTERNS
    // =====================================================
    
    // Slide In From Left
    register_block_pattern(
        'kunaal/scroll-slide-left',
        array(
            'title' => 'Scroll Animation: Slide from Left',
            'description' => 'Content slides in from the left as you scroll',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="scroll-animate slide-left" data-animate="slide-left">
  <div class="animate-content">
    <h3>Your Heading</h3>
    <p>Content that slides in from the left as you scroll down the page. Replace with your actual content.</p>
  </div>
</div>
<style>
.scroll-animate {
  opacity: 0;
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}
.scroll-animate.slide-left {
  transform: translateX(-60px);
}
.scroll-animate.slide-right {
  transform: translateX(60px);
}
.scroll-animate.visible {
  opacity: 1;
  transform: translateX(0);
}
.animate-content {
  padding: 2rem;
  background: var(--bgWarm);
  border-left: 3px solid var(--blue);
  border-radius: 0 4px 4px 0;
}
</style>
<script>
(function() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("visible");
      }
    });
  }, { threshold: 0.2 });
  document.querySelectorAll(".scroll-animate").forEach(el => observer.observe(el));
})();
</script>
<!-- /wp:html -->',
        )
    );
    
    // Slide In From Right
    register_block_pattern(
        'kunaal/scroll-slide-right',
        array(
            'title' => 'Scroll Animation: Slide from Right',
            'description' => 'Content slides in from the right as you scroll',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="scroll-animate slide-right" data-animate="slide-right">
  <div class="animate-content right-aligned">
    <h3>Your Heading</h3>
    <p>Content that slides in from the right as you scroll down the page.</p>
  </div>
</div>
<style>
.animate-content.right-aligned {
  border-left: none;
  border-right: 3px solid var(--blue);
  border-radius: 4px 0 0 4px;
  text-align: right;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Fade and Scale
    register_block_pattern(
        'kunaal/scroll-fade-scale',
        array(
            'title' => 'Scroll Animation: Fade & Scale',
            'description' => 'Content fades in and scales up',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="scroll-animate fade-scale">
  <div class="scale-content">
    <h3>Featured Content</h3>
    <p>This content fades in and scales up elegantly as you scroll.</p>
  </div>
</div>
<style>
.scroll-animate.fade-scale {
  transform: scale(0.9);
}
.scroll-animate.fade-scale.visible {
  transform: scale(1);
}
.scale-content {
  padding: 2rem;
  background: var(--bg);
  border: 1px solid var(--hair);
  border-radius: 8px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // ADVANCED CHART PATTERNS
    // =====================================================
    
    // Clustered Bar Chart
    register_block_pattern(
        'kunaal/chart-clustered-bar',
        array(
            'title' => 'Chart: Clustered Bar Chart',
            'description' => 'Compare multiple series side by side',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Clustered Bar Comparison</figcaption>
  <div class="clusteredChart">
    <div class="cluster-group">
      <span class="cluster-label">Q1</span>
      <div class="cluster-bars">
        <div class="cbar series-a" style="--h: 70%"></div>
        <div class="cbar series-b" style="--h: 55%"></div>
      </div>
    </div>
    <div class="cluster-group">
      <span class="cluster-label">Q2</span>
      <div class="cluster-bars">
        <div class="cbar series-a" style="--h: 85%"></div>
        <div class="cbar series-b" style="--h: 60%"></div>
      </div>
    </div>
    <div class="cluster-group">
      <span class="cluster-label">Q3</span>
      <div class="cluster-bars">
        <div class="cbar series-a" style="--h: 65%"></div>
        <div class="cbar series-b" style="--h: 90%"></div>
      </div>
    </div>
    <div class="cluster-group">
      <span class="cluster-label">Q4</span>
      <div class="cluster-bars">
        <div class="cbar series-a" style="--h: 95%"></div>
        <div class="cbar series-b" style="--h: 75%"></div>
      </div>
    </div>
  </div>
  <div class="chart-legend">
    <span class="legend-item"><span class="legend-color series-a"></span>Series A</span>
    <span class="legend-item"><span class="legend-color series-b"></span>Series B</span>
  </div>
  <p class="chartSource">Source: Your Data Source</p>
</figure>
<style>
.clusteredChart {
  display: flex;
  justify-content: space-around;
  align-items: flex-end;
  height: 200px;
  padding: 1rem 0;
  border-bottom: 1px solid var(--hair);
}
.cluster-group { text-align: center; }
.cluster-label {
  font-size: 12px;
  color: var(--muted);
  margin-top: 8px;
  display: block;
}
.cluster-bars {
  display: flex;
  gap: 4px;
  align-items: flex-end;
  height: 160px;
}
.cbar {
  width: 24px;
  height: var(--h);
  border-radius: 2px 2px 0 0;
  transition: height 0.6s ease;
}
.cbar.series-a { background: var(--blue); }
.cbar.series-b { background: var(--warm); }
.chart-legend {
  display: flex;
  gap: 1.5rem;
  justify-content: center;
  margin: 1rem 0;
  font-size: 12px;
}
.legend-item { display: flex; align-items: center; gap: 6px; }
.legend-color {
  width: 12px;
  height: 12px;
  border-radius: 2px;
}
.legend-color.series-a { background: var(--blue); }
.legend-color.series-b { background: var(--warm); }
</style>
<!-- /wp:html -->',
        )
    );
    
    // Waterfall Chart (Build-Up)
    register_block_pattern(
        'kunaal/chart-waterfall',
        array(
            'title' => 'Chart: Waterfall (Build-Up)',
            'description' => 'Show cumulative growth with floating bars',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Revenue build-up: $50M to $85M</figcaption>
  <div class="waterfallChart">
    <svg viewBox="0 0 700 240" preserveAspectRatio="xMidYMid meet">
      <!-- Y-axis baseline -->
      <line x1="40" y1="200" x2="660" y2="200" stroke="#ddd" stroke-width="2"/>
      
      <!-- Y-axis labels -->
      <text x="25" y="205" text-anchor="end" font-size="11" fill="var(--muted)">$0</text>
      <text x="25" y="125" text-anchor="end" font-size="11" fill="var(--muted)">$50M</text>
      <text x="25" y="50" text-anchor="end" font-size="11" fill="var(--muted)">$100M</text>
      
      <!-- Bar 1: Start $50M - Full column -->
      <rect x="80" y="125" width="80" height="75" fill="var(--chart-blue)" opacity="0.9"/>
      <text x="120" y="110" text-anchor="middle" font-size="14" font-weight="600" fill="var(--ink)">$50M</text>
      <text x="120" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Start</text>
      
      <!-- Connector from top of Start to bottom of Increase 1 -->
      <line x1="160" y1="125" x2="200" y2="125" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 2: +$30M FLOATS above current 50M level -->
      <!-- From y=95 (80M) to y=125 (50M) = 30px representing 30M increase -->
      <rect x="200" y="95" width="80" height="30" fill="var(--chart-green)" opacity="0.85"/>
      <text x="240" y="87" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-green)">+$30M</text>
      <text x="240" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Revenue</text>
      
      <!-- Connector from top of Increase 1 to bottom of Increase 2 -->
      <line x1="280" y1="95" x2="320" y2="95" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 3: +$20M FLOATS from 80M to 100M -->
      <rect x="320" y="65" width="80" height="30" fill="var(--chart-green)" opacity="0.85"/>
      <text x="360" y="57" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-green)">+$20M</text>
      <text x="360" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Gains</text>
      
      <!-- Connector from top of Increase 2 to top of Decrease -->
      <line x1="400" y1="65" x2="440" y2="65" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 4: -$15M decrease from 100M down to 85M -->
      <rect x="440" y="65" width="80" height="22.5" fill="var(--chart-accent)" opacity="0.85"/>
      <text x="480" y="58" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-accent)">-$15M</text>
      <text x="480" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Costs</text>
      
      <!-- Connector to Final -->
      <line x1="520" y1="87.5" x2="560" y2="87.5" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 5: Final $85M - Full column -->
      <rect x="560" y="87.5" width="80" height="112.5" fill="var(--chart-warm)" opacity="0.9"/>
      <text x="600" y="72" text-anchor="middle" font-size="14" font-weight="600" fill="var(--ink)">$85M</text>
      <text x="600" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Total</text>
    </svg>
  </div>
  <p class="chartSource">Source: Financial Analysis 2024</p>
</figure>
<style>
.waterfallChart { 
  padding: 1rem 0; 
  background: var(--bgAlt);
  border-radius: 4px;
}
.waterfallChart svg { 
  width: 100%; 
  height: auto; 
  max-height: 280px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Bubble Chart
    register_block_pattern(
        'kunaal/chart-bubble',
        array(
            'title' => 'Chart: Bubble Chart',
            'description' => 'Show three dimensions with bubble size',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Bubble Chart: 3-Dimensional View</figcaption>
  <div class="bubbleChart">
    <svg viewBox="0 0 400 300">
      <!-- Grid lines -->
      <line x1="50" y1="250" x2="380" y2="250" stroke="#eee" stroke-width="1"/>
      <line x1="50" y1="200" x2="380" y2="200" stroke="#eee" stroke-width="1"/>
      <line x1="50" y1="150" x2="380" y2="150" stroke="#eee" stroke-width="1"/>
      <line x1="50" y1="100" x2="380" y2="100" stroke="#eee" stroke-width="1"/>
      <line x1="50" y1="50" x2="380" y2="50" stroke="#eee" stroke-width="1"/>
      <!-- Bubbles (x, y position + r for size) -->
      <circle cx="100" cy="180" r="25" fill="#1E5AFF" opacity="0.6"/>
      <circle cx="180" cy="120" r="40" fill="#1E5AFF" opacity="0.6"/>
      <circle cx="260" cy="160" r="18" fill="#7D6B5D" opacity="0.6"/>
      <circle cx="320" cy="80" r="35" fill="#1E5AFF" opacity="0.6"/>
      <circle cx="150" cy="220" r="15" fill="#7D6B5D" opacity="0.6"/>
      <!-- Axis labels -->
      <text x="215" y="290" text-anchor="middle" font-size="11" fill="#666">X Axis Label →</text>
      <text x="20" y="150" text-anchor="middle" font-size="11" fill="#666" transform="rotate(-90, 20, 150)">Y Axis →</text>
    </svg>
  </div>
  <div class="chart-legend">
    <span class="legend-item"><span class="legend-color" style="background: #1E5AFF"></span>Category A</span>
    <span class="legend-item"><span class="legend-color" style="background: #7D6B5D"></span>Category B</span>
  </div>
  <p class="chartNote">Bubble size represents third dimension (e.g., revenue)</p>
</figure>
<style>
.bubbleChart svg {
  width: 100%;
  max-width: 500px;
  display: block;
  margin: 0 auto;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Variwide Chart
    register_block_pattern(
        'kunaal/chart-variwide',
        array(
            'title' => 'Chart: Variwide Chart',
            'description' => 'Bar width represents another dimension',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Variwide: Width = Market Size, Height = Growth</figcaption>
  <div class="variwideChart">
    <div class="vw-bar" style="--w: 30%; --h: 80%">
      <span class="vw-label">Segment A</span>
    </div>
    <div class="vw-bar" style="--w: 20%; --h: 60%">
      <span class="vw-label">Segment B</span>
    </div>
    <div class="vw-bar" style="--w: 35%; --h: 45%">
      <span class="vw-label">Segment C</span>
    </div>
    <div class="vw-bar" style="--w: 15%; --h: 90%">
      <span class="vw-label">Segment D</span>
    </div>
  </div>
  <p class="chartNote">Width = market size, Height = growth rate</p>
</figure>
<style>
.variwideChart {
  display: flex;
  align-items: flex-end;
  height: 180px;
  border-bottom: 1px solid var(--hair);
  padding-bottom: 0.5rem;
}
.vw-bar {
  width: var(--w);
  height: var(--h);
  background: linear-gradient(to top, var(--blue), rgba(30,90,255,0.6));
  position: relative;
  margin-right: 2px;
}
.vw-bar:nth-child(even) {
  background: linear-gradient(to top, var(--warm), rgba(125,107,93,0.6));
}
.vw-label {
  position: absolute;
  bottom: -24px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 10px;
  color: var(--muted);
  white-space: nowrap;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // SUBSCRIBE SECTION
    // =====================================================
    
    register_block_pattern(
        'kunaal/subscribe-section',
        array(
            'title' => 'Subscribe Section',
            'description' => 'Email subscription form',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<section class="subscribe-section reveal">
  <h3>Subscribe</h3>
  <p>Get notified when new essays and jottings are published.</p>
  <form class="subscribe-form" action="#" method="post">
    <input type="email" name="email" placeholder="Your email address" required />
    <button type="submit">Subscribe</button>
  </form>
</section>
<style>
.subscribe-section {
  background: var(--bgWarm);
  padding: var(--space-6) var(--space-4);
  border-radius: 8px;
  text-align: center;
  margin: var(--space-8) 0;
}
.subscribe-section h3 {
  font-family: var(--serif);
  font-size: 22px;
  margin: 0 0 8px;
}
.subscribe-section > p {
  color: var(--muted);
  font-size: 14px;
  margin: 0 0 1.5rem;
}
.subscribe-form {
  display: flex;
  gap: 8px;
  max-width: 420px;
  margin: 0 auto;
}
.subscribe-form input[type="email"] {
  flex: 1;
  padding: 14px 18px;
  border: 1px solid var(--hair);
  border-radius: 4px;
  font-size: 14px;
  background: var(--bg);
}
.subscribe-form input:focus {
  outline: none;
  border-color: var(--blue);
}
.subscribe-form button {
  padding: 14px 24px;
  background: var(--blue);
  color: white;
  border: none;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  transition: background 200ms ease;
}
.subscribe-form button:hover {
  background: var(--ink);
}
.subscribe-note {
  font-size: 12px;
  color: var(--muted2);
  margin: 1rem 0 0;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // CITATION / FOOTNOTE PATTERN
    // =====================================================
    
    register_block_pattern(
        'kunaal/footnote',
        array(
            'title' => 'Footnote Reference',
            'description' => 'Add numbered footnote references',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<sup class="footnote-ref"><a href="#fn1" id="fnref1">[1]</a></sup>

<!-- Add this at the end of your article: -->
<hr class="footnotes-sep" />
<section class="footnotes">
  <ol class="footnotes-list">
    <li id="fn1" class="footnote-item">
      <p>Your footnote text here. <a href="#fnref1" class="footnote-backref">↩</a></p>
    </li>
  </ol>
</section>
<style>
.footnote-ref a {
  color: var(--blue);
  text-decoration: none;
  font-size: 0.75em;
  vertical-align: super;
}
.footnotes-sep {
  margin: var(--space-8) 0 var(--space-4);
}
.footnotes {
  font-size: 13px;
  color: var(--muted);
}
.footnotes-list {
  padding-left: 1.5rem;
}
.footnote-item {
  margin-bottom: 0.5rem;
}
.footnote-backref {
  color: var(--blue);
  text-decoration: none;
  margin-left: 4px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // =====================================================
    // DATA TABLE PATTERN
    // =====================================================
    
    register_block_pattern(
        'kunaal/data-table',
        array(
            'title' => 'Data Table (Styled)',
            'description' => 'A beautifully styled data table',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="data-table-wrap reveal">
  <figcaption class="table-caption">Table Title: What This Data Shows</figcaption>
  <table class="data-table">
    <thead>
      <tr>
        <th>Category</th>
        <th>Metric A</th>
        <th>Metric B</th>
        <th>Change</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Row 1</td>
        <td>1,234</td>
        <td>5,678</td>
        <td class="positive">+12.5%</td>
      </tr>
      <tr>
        <td>Row 2</td>
        <td>2,345</td>
        <td>4,567</td>
        <td class="negative">-3.2%</td>
      </tr>
      <tr>
        <td>Row 3</td>
        <td>3,456</td>
        <td>6,789</td>
        <td class="positive">+8.7%</td>
      </tr>
    </tbody>
  </table>
  <p class="table-source">Source: Your Data Source</p>
</figure>
<style>
.data-table-wrap {
  margin: var(--space-4) 0;
  overflow-x: auto;
}
.table-caption {
  font-size: 13px;
  font-weight: 600;
  
  letter-spacing: 0.04em;
  color: var(--warm);
  margin-bottom: 1rem;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}
.data-table th {
  background: var(--ink);
  color: white;
  font-size: 11px;
  font-weight: 600;
  
  letter-spacing: 0.05em;
  padding: 14px 18px;
  text-align: left;
}
.data-table td {
  padding: 14px 18px;
  border-bottom: 1px solid var(--hair);
}
.data-table tbody tr:hover {
  background: var(--blueTint);
}
.data-table .positive { color: #2e7d32; font-weight: 500; }
.data-table .negative { color: #c62828; font-weight: 500; }
.table-source {
  font-size: 11px;
  color: var(--muted2);
  margin-top: 8px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Stacked Bar Chart
    register_block_pattern(
        'kunaal/chart-stacked-bar',
        array(
            'title' => 'Chart: Stacked Bar Chart',
            'description' => 'Horizontal stacked bars showing composition',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Stacked Composition Analysis</figcaption>
  <div class="stackedBarChart">
    <div class="stacked-bar">
      <div class="bar-label">Category A</div>
      <div class="bar-segments">
        <div class="segment" style="width: 40%; background: #1E5AFF;" title="Segment 1: 40%"></div>
        <div class="segment" style="width: 35%; background: #5B7BA8;" title="Segment 2: 35%"></div>
        <div class="segment" style="width: 25%; background: #7D6B5D;" title="Segment 3: 25%"></div>
      </div>
    </div>
    <div class="stacked-bar">
      <div class="bar-label">Category B</div>
      <div class="bar-segments">
        <div class="segment" style="width: 30%; background: #1E5AFF;" title="Segment 1: 30%"></div>
        <div class="segment" style="width: 45%; background: #5B7BA8;" title="Segment 2: 45%"></div>
        <div class="segment" style="width: 25%; background: #7D6B5D;" title="Segment 3: 25%"></div>
      </div>
    </div>
    <div class="stacked-bar">
      <div class="bar-label">Category C</div>
      <div class="bar-segments">
        <div class="segment" style="width: 50%; background: #1E5AFF;" title="Segment 1: 50%"></div>
        <div class="segment" style="width: 30%; background: #5B7BA8;" title="Segment 2: 30%"></div>
        <div class="segment" style="width: 20%; background: #7D6B5D;" title="Segment 3: 20%"></div>
      </div>
    </div>
  </div>
  <div class="chart-legend">
    <span><i style="background: #1E5AFF;"></i> Segment 1</span>
    <span><i style="background: #5B7BA8;"></i> Segment 2</span>
    <span><i style="background: #7D6B5D;"></i> Segment 3</span>
  </div>
  <p class="chartSource">Source: Your Data Source</p>
</figure>
<style>
.stackedBarChart { padding: 1rem 0; }
.stacked-bar {
  display: grid;
  grid-template-columns: 120px 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
  align-items: center;
}
.bar-label {
  font-size: 13px;
  font-weight: 500;
  color: var(--ink);
  text-align: right;
}
.bar-segments {
  display: flex;
  height: 32px;
  border-radius: 4px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.bar-segments .segment {
  transition: opacity 200ms ease;
  cursor: help;
}
.bar-segments .segment:hover {
  opacity: 0.8;
}
.chart-legend {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 1rem;
  font-size: 12px;
}
.chart-legend i {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 2px;
  margin-right: 4px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Combination Chart (Line + Bar)
    register_block_pattern(
        'kunaal/chart-combination',
        array(
            'title' => 'Chart: Combination (Line + Bar)',
            'description' => 'Combined line and bar chart for dual metrics',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Revenue vs Growth Rate</figcaption>
  <div class="comboChart">
    <svg viewBox="0 0 500 200" preserveAspectRatio="xMidYMid meet">
      <!-- Grid lines -->
      <line x1="50" y1="20" x2="450" y2="20" stroke="var(--hair2)" stroke-width="1"/>
      <line x1="50" y1="60" x2="450" y2="60" stroke="var(--hair2)" stroke-width="1"/>
      <line x1="50" y1="100" x2="450" y2="100" stroke="var(--hair2)" stroke-width="1"/>
      <line x1="50" y1="140" x2="450" y2="140" stroke="var(--hair2)" stroke-width="1"/>
      
      <!-- Bars (Revenue) -->
      <rect x="70" y="60" width="60" height="80" fill="var(--blueTint)" stroke="var(--blue)" stroke-width="2"/>
      <rect x="170" y="40" width="60" height="100" fill="var(--blueTint)" stroke="var(--blue)" stroke-width="2"/>
      <rect x="270" y="50" width="60" height="90" fill="var(--blueTint)" stroke="var(--blue)" stroke-width="2"/>
      <rect x="370" y="30" width="60" height="110" fill="var(--blueTint)" stroke="var(--blue)" stroke-width="2"/>
      
      <!-- Line (Growth Rate) -->
      <path d="M 100 100 L 200 80 L 300 90 L 400 70" 
            fill="none" stroke="var(--warm)" stroke-width="3"/>
      <circle cx="100" cy="100" r="5" fill="var(--warm)"/>
      <circle cx="200" cy="80" r="5" fill="var(--warm)"/>
      <circle cx="300" cy="90" r="5" fill="var(--warm)"/>
      <circle cx="400" cy="70" r="5" fill="var(--warm)"/>
      
      <!-- X-axis labels -->
      <text x="100" y="165" text-anchor="middle" font-size="11" fill="var(--muted)">Q1</text>
      <text x="200" y="165" text-anchor="middle" font-size="11" fill="var(--muted)">Q2</text>
      <text x="300" y="165" text-anchor="middle" font-size="11" fill="var(--muted)">Q3</text>
      <text x="400" y="165" text-anchor="middle" font-size="11" fill="var(--muted)">Q4</text>
    </svg>
  </div>
  <div class="chart-legend">
    <span><i style="background: var(--blue);"></i> Revenue ($M)</span>
    <span><i style="background: var(--warm);"></i> Growth Rate (%)</span>
  </div>
  <p class="chartSource">Source: Your Data Source</p>
</figure>
<style>
.comboChart { padding: 1rem 0; }
.comboChart svg { width: 100%; height: auto; max-height: 250px; }
</style>
<!-- /wp:html -->',
        )
    );
    
    // Waterfall Chart (Build-Down)
    register_block_pattern(
        'kunaal/chart-waterfall-down',
        array(
            'title' => 'Chart: Waterfall (Build-Down)',
            'description' => 'Waterfall chart showing decremental changes',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Revenue waterfall: $100M to $78M</figcaption>
  <div class="waterfallChart">
    <svg viewBox="0 0 700 240" preserveAspectRatio="xMidYMid meet">
      <!-- Y-axis baseline -->
      <line x1="40" y1="200" x2="660" y2="200" stroke="#ddd" stroke-width="2"/>
      
      <!-- Y-axis labels -->
      <text x="25" y="205" text-anchor="end" font-size="11" fill="var(--muted)">$0</text>
      <text x="25" y="125" text-anchor="end" font-size="11" fill="var(--muted)">$50M</text>
      <text x="25" y="50" text-anchor="end" font-size="11" fill="var(--muted)">$100M</text>
      
      <!-- Bar 1: Starting value $100M - Full column from baseline -->
      <rect x="80" y="50" width="80" height="150" fill="var(--chart-blue)" opacity="0.9"/>
      <text x="120" y="35" text-anchor="middle" font-size="14" font-weight="600" fill="var(--ink)">$100M</text>
      <text x="120" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Start</text>
      
      <!-- Connector: From top of Start (y=50) to top of Decrease 1 -->
      <line x1="160" y1="50" x2="200" y2="50" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 2: -$10M decrease FLOATS at 100M position, goes down to 90M -->
      <!-- Positioned from y=50 (100M) down 15px (representing 10M) -->
      <rect x="200" y="50" width="80" height="15" fill="var(--chart-accent)" opacity="0.85"/>
      <text x="240" y="42" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-accent)">-$10M</text>
      <text x="240" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Cost A</text>
      
      <!-- Connector: From bottom of Decrease 1 (y=65, representing 90M) to top of Decrease 2 -->
      <line x1="280" y1="65" x2="320" y2="65" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 3: -$7M decrease FLOATS at 90M position -->
      <!-- From y=65 (90M) down 10.5px (representing 7M to reach 83M) -->
      <rect x="320" y="65" width="80" height="10.5" fill="var(--chart-accent)" opacity="0.85"/>
      <text x="360" y="58" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-accent)">-$7M</text>
      <text x="360" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Cost B</text>
      
      <!-- Connector: From bottom of Decrease 2 (y=75.5, representing 83M) to top of Decrease 3 -->
      <line x1="400" y1="75.5" x2="440" y2="75.5" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 4: -$5M decrease FLOATS at 83M position -->
      <!-- From y=75.5 (83M) down 7.5px (representing 5M to reach 78M) -->
      <rect x="440" y="75.5" width="80" height="7.5" fill="var(--chart-accent)" opacity="0.85"/>
      <text x="480" y="69" text-anchor="middle" font-size="12" font-weight="600" fill="var(--chart-accent)">-$5M</text>
      <text x="480" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Cost C</text>
      
      <!-- Connector: From bottom of Decrease 3 (y=83, representing 78M) to top of Final -->
      <line x1="520" y1="83" x2="560" y2="83" stroke="#999" stroke-width="1.5" stroke-dasharray="4,4"/>
      
      <!-- Bar 5: Final value $78M - Full column from baseline -->
      <!-- From y=200 (baseline) up to y=83 (78M position) = 117px -->
      <rect x="560" y="83" width="80" height="117" fill="var(--chart-warm)" opacity="0.9"/>
      <text x="600" y="68" text-anchor="middle" font-size="14" font-weight="600" fill="var(--ink)">$78M</text>
      <text x="600" y="220" text-anchor="middle" font-size="12" fill="var(--muted)">Final</text>
    </svg>
  </div>
  <p class="chartSource">Source: Financial Analysis 2024</p>
</figure>
<style>
.waterfallChart { 
  padding: 1rem 0; 
  background: var(--bgAlt);
  border-radius: 4px;
}
.waterfallChart svg { 
  width: 100%; 
  height: auto; 
  max-height: 280px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Flow Chart
    register_block_pattern(
        'kunaal/flowchart',
        array(
            'title' => 'Flow Chart',
            'description' => 'Process flow diagram with arrows',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Process Flow Diagram</figcaption>
  <div class="flowChart">
    <div class="flow-step">
      <div class="flow-box start">Start</div>
      <div class="flow-arrow">↓</div>
    </div>
    <div class="flow-step">
      <div class="flow-box process">Process Input</div>
      <div class="flow-arrow">↓</div>
    </div>
    <div class="flow-decision">
      <div class="flow-box decision">Decision Point?</div>
      <div class="flow-branches">
        <div class="flow-branch">
          <div class="flow-arrow">→ Yes</div>
          <div class="flow-box process">Action A</div>
        </div>
        <div class="flow-branch">
          <div class="flow-arrow">→ No</div>
          <div class="flow-box process">Action B</div>
        </div>
      </div>
    </div>
    <div class="flow-step">
      <div class="flow-arrow">↓</div>
      <div class="flow-box end">End Result</div>
    </div>
  </div>
</figure>
<style>
.flowChart {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0;
}
.flow-step {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.flow-box {
  padding: 12px 24px;
  border: 2px solid var(--blue);
  border-radius: 8px;
  background: var(--bg);
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  min-width: 150px;
}
.flow-box.start, .flow-box.end {
  border-radius: 24px;
  background: var(--blueTint);
}
.flow-box.decision {
  transform: rotate(45deg);
  padding: 20px;
  min-width: 120px;
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.flow-box.decision > * {
  transform: rotate(-45deg);
}
.flow-arrow {
  font-size: 24px;
  color: var(--blue);
  padding: 8px 0;
  font-weight: bold;
}
.flow-decision {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}
.flow-branches {
  display: flex;
  gap: 2rem;
  margin-top: 1rem;
}
.flow-branch {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Chevron/Funnel Chart
    register_block_pattern(
        'kunaal/chart-chevron',
        array(
            'title' => 'Chart: Chevron/Funnel',
            'description' => 'Funnel or process chevron chart',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="chartWrap reveal">
  <figcaption class="chartCaption">Conversion Funnel</figcaption>
  <div class="chevronChart">
    <div class="chevron" style="width: 100%;">
      <span class="chevron-label">Awareness</span>
      <span class="chevron-value">10,000</span>
    </div>
    <div class="chevron" style="width: 80%;">
      <span class="chevron-label">Interest</span>
      <span class="chevron-value">8,000</span>
    </div>
    <div class="chevron" style="width: 60%;">
      <span class="chevron-label">Consideration</span>
      <span class="chevron-value">6,000</span>
    </div>
    <div class="chevron" style="width: 40%;">
      <span class="chevron-label">Intent</span>
      <span class="chevron-value">4,000</span>
    </div>
    <div class="chevron" style="width: 25%;">
      <span class="chevron-label">Purchase</span>
      <span class="chevron-value">2,500</span>
    </div>
  </div>
  <p class="chartSource">Source: Your Data Source</p>
</figure>
<style>
.chevronChart {
  padding: 1rem 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}
.chevron {
  position: relative;
  height: 50px;
  background: linear-gradient(135deg, var(--blue) 0%, var(--blueMuted) 100%);
  -webkit-clip-path: polygon(0% 0%, calc(100% - 20px) 0%, 100% 50%, calc(100% - 20px) 100%, 0% 100%, 20px 50%);
  clip-path: polygon(0% 0%, calc(100% - 20px) 0%, 100% 50%, calc(100% - 20px) 100%, 0% 100%, 20px 50%);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 30px;
  color: white;
  font-weight: 500;
  transition: all 300ms ease;
}
.chevron:hover {
  transform: translateX(8px);
  filter: brightness(1.1);
}
.chevron-label {
  font-size: 14px;
}
.chevron-value {
  font-family: var(--mono);
  font-size: 13px;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Citation Block
    register_block_pattern(
        'kunaal/citation',
        array(
            'title' => 'Citation/Reference',
            'description' => 'Formatted citation or reference',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="citation-block reveal">
  <div class="citation-number">[1]</div>
  <div class="citation-content">
    <p class="citation-text">Author, A. A., Author, B. B., & Author, C. C. (Year). Title of article. <em>Title of Journal, Volume</em>(Issue), pages. https://doi.org/xx.xxxx</p>
  </div>
</div>
<style>
.citation-block {
  display: flex;
  gap: 1rem;
  padding: var(--space-3);
  margin: var(--space-4) 0;
  background: var(--bgAlt);
  border-left: 2px solid var(--blueMuted);
  font-size: 13px;
  line-height: 1.7;
}
.citation-number {
  font-family: var(--mono);
  font-weight: 600;
  color: var(--blue);
  flex-shrink: 0;
  width: 32px;
}
.citation-content {
  flex: 1;
}
.citation-text {
  margin: 0;
  color: var(--muted);
}
.citation-text em {
  font-style: italic;
}
.citation-text a {
  color: var(--blue);
  text-decoration: none;
  border-bottom: 1px solid var(--blueStroke);
}
.citation-text a:hover {
  border-bottom-color: var(--blue);
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Footnote Reference (inline)
    register_block_pattern(
        'kunaal/footnote-ref',
        array(
            'title' => 'Footnote Reference (Inline)',
            'description' => 'Inline footnote reference number',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<sup class="footnote-ref"><a href="#fn-1" id="fnref-1">[1]</a></sup>
<style>
.footnote-ref {
  font-size: 0.75em;
  font-weight: 600;
  line-height: 0;
  position: relative;
  vertical-align: baseline;
  top: -0.5em;
}
.footnote-ref a {
  color: var(--blue);
  text-decoration: none;
  padding: 2px 4px;
  border-radius: 3px;
  transition: background 200ms ease;
}
.footnote-ref a:hover {
  background: var(--blueTint);
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Footnotes Section
    register_block_pattern(
        'kunaal/footnotes-section',
        array(
            'title' => 'Footnotes Section',
            'description' => 'Container for end-of-article footnotes',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="footnotes-section reveal">
  <h4 class="footnotes-heading">Notes & References</h4>
  <ol class="footnotes-list">
    <li id="fn-1">
      <p>Your footnote content here. <a href="#fnref-1" class="footnote-backref">↩</a></p>
    </li>
    <li id="fn-2">
      <p>Another footnote. <a href="#fnref-2" class="footnote-backref">↩</a></p>
    </li>
  </ol>
</div>
<style>
.footnotes-section {
  margin-top: var(--space-12);
  padding-top: var(--space-6);
  border-top: 2px solid var(--hair2);
}
.footnotes-heading {
  font-family: var(--sans);
  font-size: 16px;
  font-weight: 600;
  margin-bottom: var(--space-4);
  color: var(--ink);
  letter-spacing: -.01em;
}
.footnotes-list {
  list-style: none;
  counter-reset: footnote-counter;
  padding-left: 0;
}
.footnotes-list li {
  counter-increment: footnote-counter;
  display: flex;
  gap: 1rem;
  margin-bottom: var(--space-3);
  font-size: 13px;
  line-height: 1.7;
  color: var(--muted);
}
.footnotes-list li::before {
  content: "[" counter(footnote-counter) "]";
  font-family: var(--mono);
  font-weight: 600;
  color: var(--blue);
  flex-shrink: 0;
  min-width: 32px;
}
.footnotes-list li p {
  margin: 0;
}
.footnote-backref {
  color: var(--blue);
  text-decoration: none;
  font-size: 14px;
  margin-left: 8px;
  padding: 2px 6px;
  border-radius: 3px;
  transition: background 200ms ease;
}
.footnote-backref:hover {
  background: var(--blueTint);
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Read More / Continue Reading Block
    register_block_pattern(
        'kunaal/read-more',
        array(
            'title' => 'Read More Separator',
            'description' => 'Elegant content separator with continue reading link',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="read-more-block reveal">
  <div class="read-more-line"></div>
  <button class="read-more-toggle" onclick="this.parentElement.classList.toggle(\'expanded\'); this.textContent = this.parentElement.classList.contains(\'expanded\') ? \'Show Less ↑\' : \'Continue Reading →\';">Continue Reading →</button>
  <div class="read-more-line"></div>
</div>
<style>
.read-more-block {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  margin: var(--space-8) 0;
  opacity: 0.7;
  transition: opacity 300ms ease;
}
.read-more-block:hover {
  opacity: 1;
}
.read-more-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(to right, transparent, var(--blue), transparent);
}
.read-more-toggle {
  font-family: var(--sans);
  font-size: 13px;
  font-weight: 500;
  color: var(--blue);
  background: var(--blueTint);
  border: 1px solid var(--blueStroke);
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  transition: all 200ms ease;
  white-space: nowrap;
}
.read-more-toggle:hover {
  background: var(--blue);
  color: var(--bg);
  border-color: var(--blue);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px var(--blueShadow);
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Timeline Vertical
    register_block_pattern(
        'kunaal/timeline',
        array(
            'title' => 'Timeline (Vertical)',
            'description' => 'Chronological timeline with markers',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="timeline-vertical">
  <div class="timeline-item">
    <div class="timeline-marker"></div>
    <div class="timeline-content">
      <span class="timeline-date">January 2024</span>
      <h4>First Event</h4>
      <p>Description of what happened.</p>
    </div>
  </div>
  <div class="timeline-item">
    <div class="timeline-marker"></div>
    <div class="timeline-content">
      <span class="timeline-date">March 2024</span>
      <h4>Key Development</h4>
      <p>The story continues.</p>
    </div>
  </div>
</div>
<style>
.timeline-vertical {
  position: relative;
  padding: 2rem 0 2rem 3rem;
}
.timeline-vertical::before {
  content: "";
  position: absolute;
  left: 11px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: var(--blue);
}
.timeline-item {
  position: relative;
  margin-bottom: 2rem;
  padding-left: 2rem;
}
.timeline-marker {
  position: absolute;
  left: 0;
  top: 4px;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--bg);
  border: 3px solid var(--blue);
}
.timeline-date {
  font-family: var(--mono);
  font-size: 11px;
  color: var(--blue);
  font-weight: 600;
  display: block;
  margin-bottom: 0.5rem;
}
.timeline-content h4 {
  font-family: var(--serif);
  font-size: 17px;
  margin: 0 0 0.5rem;
}
.timeline-content p {
  font-size: 14px;
  line-height: 1.6;
  color: var(--muted);
  margin: 0;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Statistical Callout
    register_block_pattern(
        'kunaal/stat-callout',
        array(
            'title' => 'Statistical Callout',
            'description' => 'Highlight key statistics',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<div class="stat-callout reveal">
  <div class="stat-number">67%</div>
  <div class="stat-label">of respondents agreed</div>
  <p class="stat-context">This represents a significant finding in our analysis.</p>
</div>
<style>
.stat-callout {
  text-align: center;
  padding: 2.5rem 2rem;
  margin: 3rem 0;
  background: var(--blueTint);
  border-left: 4px solid var(--blue);
  border-radius: 4px;
}
.stat-number {
  font-family: var(--serif);
  font-size: 3.5rem;
  font-weight: 700;
  color: var(--blue);
  line-height: 1;
  margin-bottom: 0.5rem;
}
.stat-label {
  font-family: var(--sans);
  font-size: 14px;
  font-weight: 600;
  color: var(--ink);
  margin-bottom: 1rem;
}
.stat-context {
  font-size: 13px;
  line-height: 1.6;
  color: var(--muted);
  max-width: 50ch;
  margin: 0 auto;
}
</style>
<!-- /wp:html -->',
        )
    );
    
    // Data Table Enhanced
    register_block_pattern(
        'kunaal/data-table',
        array(
            'title' => 'Data Table (Enhanced)',
            'description' => 'Professional data table with highlights',
            'categories' => array('kunaal-bespoke'),
            'content' => '<!-- wp:html -->
<figure class="data-table-wrap">
  <figcaption class="table-caption">Table 1: Comparative Data</figcaption>
  <table class="data-table">
    <thead>
      <tr>
        <th>Category</th>
        <th>Value A</th>
        <th>Value B</th>
        <th>Change</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Metric 1</td>
        <td>85%</td>
        <td>92%</td>
        <td class="positive">+7%</td>
      </tr>
      <tr class="highlight">
        <td><strong>Key Finding</strong></td>
        <td><strong>45%</strong></td>
        <td><strong>78%</strong></td>
        <td class="positive"><strong>+33%</strong></td>
      </tr>
      <tr>
        <td>Metric 3</td>
        <td>67%</td>
        <td>64%</td>
        <td class="negative">-3%</td>
      </tr>
    </tbody>
  </table>
  <p class="table-note">Source: Research Data 2024</p>
</figure>
<style>
.data-table-wrap {
  margin: 2rem 0;
}
.table-caption {
  font-family: var(--mono);
  font-size: 11px;
  color: var(--warm);
  margin-bottom: 1rem;
  font-weight: 600;
}
.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 13px;
}
.data-table thead {
  background: var(--bgWarm);
  border-bottom: 2px solid var(--blue);
}
.data-table th {
  padding: 0.75rem 1rem;
  text-align: left;
  font-weight: 600;
  font-size: 12px;
}
.data-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid var(--hair2);
  color: var(--muted);
}
.data-table .highlight {
  background: var(--blueTint);
  border-left: 3px solid var(--blue);
}
.data-table .positive {
  color: #2e7d32;
  font-weight: 600;
}
.data-table .negative {
  color: #c62828;
  font-weight: 600;
}
.table-note {
  font-size: 11px;
  color: var(--muted2);
  margin-top: 0.5rem;
  font-style: italic;
}
</style>
<!-- /wp:html -->',
        )
    );
}
add_action('init', 'kunaal_register_block_patterns');

/**
 * Unregister patterns that now have proper Gutenberg block equivalents
 * These blocks are in /blocks/ directory with full editor controls
 */
function kunaal_unregister_deprecated_patterns() {
    // Patterns that now have proper block equivalents
    $deprecated_patterns = array(
        // Editorial blocks
        'kunaal/pullquote',      // Use kunaal/pullquote block
        'kunaal/insight',        // Use kunaal/insight block
        'kunaal/section-header', // Use kunaal/section-header block
        'kunaal/accordion',      // Use kunaal/accordion block
        'kunaal/aside',          // Use kunaal/aside block
        'kunaal/citation',       // Use kunaal/citation block
        'kunaal/takeaways',      // Use kunaal/takeaways block
        'kunaal/sidenote',       // Use kunaal/sidenote block
        'kunaal/timeline',       // Use kunaal/timeline block
        
        // Parallax & scrollytelling
        'kunaal/scrolly',        // Use kunaal/scrollytelling block
        'kunaal/parallax-hero',  // Use kunaal/parallax-section block
        'kunaal/parallax-split', // Use kunaal/parallax-section block
        
        // Footnotes
        'kunaal/footnote',       // Use kunaal/footnote block
        'kunaal/footnote-ref',   // Use kunaal/footnote block
        'kunaal/footnotes-section', // Use kunaal/footnotes-section block
        
        // Data/charts - use kunaal/chart block
        'kunaal/chart',          // Use kunaal/chart block
        'kunaal/chart-bar',      // Use kunaal/chart block
        'kunaal/chart-bar-vertical', // Use kunaal/chart block
        'kunaal/chart-line',     // Use kunaal/chart block
        'kunaal/chart-donut',    // Use kunaal/chart block
        'kunaal/chart-waterfall', // Use kunaal/chart block
        'kunaal/chart-waterfall-down', // Use kunaal/chart block
        'kunaal/chart-clustered-bar', // Use kunaal/chart block
        'kunaal/chart-stacked-bar', // Use kunaal/chart block
        'kunaal/chart-combination', // Use kunaal/chart block
        'kunaal/chart-bubble',   // Use kunaal/chart block
        'kunaal/chart-variwide', // Use kunaal/chart block
        'kunaal/chart-chevron',  // Use kunaal/flowchart block
        'kunaal/flowchart',      // Use kunaal/flowchart block
        'kunaal/data-table',     // Use kunaal/pub-table block
        'kunaal/comparison-table', // Use kunaal/pub-table block
        
        // Core overrides
        'core/pullquote',        // Use kunaal/pullquote block instead
    );
    
    foreach ($deprecated_patterns as $pattern) {
        unregister_block_pattern($pattern);
    }
}
add_action('init', 'kunaal_unregister_deprecated_patterns', 20); // Run after patterns are registered

/**
 * Register Block Pattern Category
 */
function kunaal_register_block_pattern_categories() {
    register_block_pattern_category(
        'kunaal-bespoke',
        array(
            'label' => "Kunaal's Bespoke Block Patterns",
        )
    );
}
add_action('init', 'kunaal_register_block_pattern_categories');

/**
 * Add custom classes to core blocks
 */
function kunaal_block_wrapper($block_content, $block) {
    // Add reveal class to certain blocks
    $reveal_blocks = array(
        'core/paragraph',
        'core/heading',
        'core/image',
        'core/quote',
        'core/list',
    );

    if (in_array($block['blockName'], $reveal_blocks) && is_singular(array('essay', 'jotting'))) {
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

/**
 * Enqueue block editor styles
 */
function kunaal_block_editor_styles() {
    wp_enqueue_style(
        'kunaal-editor-styles',
        get_template_directory_uri() . '/style.css',
        array(),
        KUNAAL_THEME_VERSION
    );
}
add_action('enqueue_block_editor_assets', 'kunaal_block_editor_styles');
