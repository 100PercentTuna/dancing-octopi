<?php
/**
 * Kunaal Theme Functions
 *
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KUNAAL_THEME_VERSION', '4.9.2');
define('KUNAAL_THEME_DIR', get_template_directory());
define('KUNAAL_THEME_URI', get_template_directory_uri());

// Include custom PDF generator
require_once KUNAAL_THEME_DIR . '/pdf-generator.php';

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
        KUNAAL_THEME_VERSION
    );
    
    // Sidenote font (Caveat from Google Fonts - already loaded above)
    
    // Print stylesheet
    wp_enqueue_style(
        'kunaal-print-style',
        KUNAAL_THEME_URI . '/assets/css/print.css',
        array('kunaal-theme-style'),
        KUNAAL_THEME_VERSION,
        'print'
    );

    // Main script
    wp_enqueue_script(
        'kunaal-theme-main',
        KUNAAL_THEME_URI . '/assets/js/main.js',
        array(),
        KUNAAL_THEME_VERSION,
        true
    );

    // Localize script with data
    wp_localize_script('kunaal-theme-main', 'kunaalTheme', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kunaal_theme_nonce'),
        'homeUrl' => home_url('/'),
        'shareText' => get_theme_mod('kunaal_share_text', ''),
        'twitterHandle' => get_theme_mod('kunaal_twitter_handle', ''),
        'linkedinUrl' => get_theme_mod('kunaal_linkedin_handle', ''),
        'authorName' => get_theme_mod('kunaal_author_first_name', 'Kunaal') . ' ' . get_theme_mod('kunaal_author_last_name', 'Wadhwa'),
    ));
    
    // About page and Contact page assets
    // Template detection + explicit page selection to avoid slug brittleness
    $about_page_id = (int) get_theme_mod('kunaal_about_page_id', 0);
    $is_about_page = is_page_template('page-about.php') || ($about_page_id && is_page($about_page_id)) || is_page('about');
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
        // Leaflet CSS
        wp_enqueue_style(
            'leaflet-css',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            array(),
            '1.9.4'
        );
        
        // About page CSS
        wp_enqueue_style(
            'kunaal-about-page',
            KUNAAL_THEME_URI . '/assets/css/about-page.css',
            array('kunaal-theme-style'),
            KUNAAL_THEME_VERSION
        );
        
        // Scrollama (from CDN)
        wp_enqueue_script(
            'scrollama',
            'https://unpkg.com/scrollama@3.2.0/build/scrollama.min.js',
            array(),
            '3.2.0',
            true
        );
        
        // Leaflet JS
        wp_enqueue_script(
            'leaflet-js',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            array(),
            '1.9.4',
            true
        );
        
        // About page JS
        wp_enqueue_script(
            'kunaal-about-page',
            KUNAAL_THEME_URI . '/assets/js/about-page.js',
            array('scrollama', 'leaflet-js'),
            KUNAAL_THEME_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'kunaal_enqueue_assets');

/**
 * Add a `js` class to <html> early for progressive enhancement.
 * This ensures About page content is visible even if JS fails.
 */
function kunaal_add_js_class() {
    echo "<script>(function(d){d.documentElement.classList.add('js');})(document);</script>\n";
}
add_action('wp_head', 'kunaal_add_js_class', 0);

/**
 * Enqueue Gutenberg Editor Sidebar Script
 */
function kunaal_enqueue_editor_assets() {
    // Only load on post edit screens
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->post_type, array('essay', 'jotting'))) {
        return;
    }
    
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
        $errors[] = '📝 SUBTITLE/DEK is required — Find "Essay Details" in the right sidebar';
    }
    
    // Check for read time
    $read_time = null;
    if (isset($meta['kunaal_read_time']) && !empty($meta['kunaal_read_time'])) {
        $read_time = $meta['kunaal_read_time'];
    } elseif ($post_id) {
        $read_time = get_post_meta($post_id, 'kunaal_read_time', true);
    }
    
    if (empty($read_time)) {
        $errors[] = '⏱️ READ TIME is required — Find "Essay Details" in the right sidebar';
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
        $errors[] = '🏷️ At least one TOPIC is required — Find "Topics" in the right sidebar';
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
        $errors[] = '🖼️ A CARD IMAGE is required — Find "Card Image" or "Featured Image" in the right sidebar';
    }
    
    if (!empty($errors)) {
        return new WP_Error(
            'kunaal_essay_incomplete',
            "📝 ESSAY CANNOT BE PUBLISHED YET\n\nPlease complete these required fields:\n\n" . implode("\n\n", $errors),
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
        $errors[] = '📝 SUBTITLE/DEK is required — Find "Jotting Details" in the right sidebar';
    }
    
    if (!empty($errors)) {
        return new WP_Error(
            'kunaal_jotting_incomplete',
            "📝 JOTTING CANNOT BE PUBLISHED YET\n\nPlease complete these required fields:\n\n" . implode("\n\n", $errors),
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
        echo '<p><strong>⚠️ Essay could not be published. Please complete these required fields:</strong></p>';
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

    // First Name
    $wp_customize->add_setting('kunaal_author_first_name', array(
        'default' => 'Kunaal',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_author_first_name', array(
        'label' => 'First Name',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Last Name
    $wp_customize->add_setting('kunaal_author_last_name', array(
        'default' => 'Wadhwa',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_author_last_name', array(
        'label' => 'Last Name',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Tagline
    $wp_customize->add_setting('kunaal_author_tagline', array(
        'default' => 'A slightly alarming curiosity about humans and human collectives.',
        'sanitize_callback' => 'sanitize_text_field',
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
        'default' => 'Get notified when new essays and jottings are published.',
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
        'description' => 'Enter Mailchimp, ConvertKit, or other form action URL',
        'section' => 'kunaal_subscribe',
        'type' => 'url',
    ));
    
    // =====================================================
    // ABOUT PAGE - PAGE SELECTION (Reliable Enqueue)
    // =====================================================
    $wp_customize->add_section('kunaal_about_page', array(
        'title' => 'About: Page Selection',
        'priority' => 49,
        'description' => 'Select which page is your About page so enhancements load reliably even if the slug changes.',
    ));
    
    $wp_customize->add_setting('kunaal_about_page_id', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_about_page_id', array(
        'label' => 'About Page',
        'section' => 'kunaal_about_page',
        'type' => 'dropdown-pages',
    ));

    // =====================================================
    // ABOUT PAGE - HERO SECTION
    // =====================================================
    $wp_customize->add_section('kunaal_about_hero', array(
        'title' => 'About: Hero',
        'priority' => 50,
        'description' => 'Hero section at top of About page.',
    ));
    
    $wp_customize->add_setting('kunaal_about_greeting', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_greeting', array(
        'label' => 'Greeting',
        'description' => 'e.g., "Hi, I\'m Kunaal." Leave blank for default.',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_photo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kunaal_about_photo', array(
        'label' => 'Hero Photo',
        'description' => 'Large photo for hero (falls back to Avatar)',
        'section' => 'kunaal_about_hero',
    )));
    
    // =====================================================
    // ABOUT PAGE - BIO SECTION
    // =====================================================
    $wp_customize->add_section('kunaal_about_bio', array(
        'title' => 'About: Bio',
        'priority' => 51,
        'description' => 'Your personal bio/story. Use the page editor for the main content.',
    ));
    
    $wp_customize->add_setting('kunaal_about_show_bio', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_show_bio', array(
        'label' => 'Show Bio Section',
        'section' => 'kunaal_about_bio',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_bio_title', array(
        'default' => 'About',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_bio_title', array(
        'label' => 'Section Title',
        'section' => 'kunaal_about_bio',
        'type' => 'text',
    ));
    
    // =====================================================
    // ABOUT PAGE - WORLD MAP
    // =====================================================
    $wp_customize->add_section('kunaal_about_map', array(
        'title' => 'About: World Map',
        'priority' => 52,
        'description' => 'Interactive map showing places you\'ve been.',
    ));
    
    $wp_customize->add_setting('kunaal_about_show_map', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_show_map', array(
        'label' => 'Show World Map',
        'section' => 'kunaal_about_map',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_map_title', array(
        'default' => 'Places',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_title', array(
        'label' => 'Section Title',
        'section' => 'kunaal_about_map',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_map_visited', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_visited', array(
        'label' => 'Countries Visited',
        'description' => 'Comma-separated ISO codes: US,UK,FR,JP,DE',
        'section' => 'kunaal_about_map',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('kunaal_about_map_lived', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_lived', array(
        'label' => 'Countries Lived In',
        'description' => 'Comma-separated ISO codes: US,IN',
        'section' => 'kunaal_about_map',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('kunaal_about_map_notes', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('kunaal_about_map_notes', array(
        'label' => 'Country Notes (JSON)',
        'description' => '{"US": "Where I grew up", "IN": "My roots"}',
        'section' => 'kunaal_about_map',
        'type' => 'textarea',
    ));
    
    // =====================================================
    // ABOUT PAGE - BOOKSHELF
    // =====================================================
    $wp_customize->add_section('kunaal_about_books', array(
        'title' => 'About: Bookshelf',
        'priority' => 53,
        'description' => 'Currently reading / favorite books.',
    ));
    
    $wp_customize->add_setting('kunaal_about_show_books', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_show_books', array(
        'label' => 'Show Bookshelf',
        'section' => 'kunaal_about_books',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_books_title', array(
        'default' => 'Currently Reading',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_books_title', array(
        'label' => 'Section Title',
        'section' => 'kunaal_about_books',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_books_data', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('kunaal_about_books_data', array(
        'label' => 'Books (JSON)',
        'description' => '[{"title":"Book","author":"Author","cover":"url","link":"url"}]',
        'section' => 'kunaal_about_books',
        'type' => 'textarea',
    ));
    
    // =====================================================
    // ABOUT PAGE - INTERESTS CLOUD
    // =====================================================
    $wp_customize->add_section('kunaal_about_interests', array(
        'title' => 'About: Interests',
        'priority' => 54,
        'description' => 'Things you love - displayed as a beautiful tag cloud.',
    ));
    
    $wp_customize->add_setting('kunaal_about_show_interests', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_show_interests', array(
        'label' => 'Show Interests',
        'section' => 'kunaal_about_interests',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_interests_title', array(
        'default' => 'Things I Love',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_interests_title', array(
        'label' => 'Section Title',
        'section' => 'kunaal_about_interests',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_interests_list', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('kunaal_about_interests_list', array(
        'label' => 'Interests (one per line)',
        'description' => 'Add :2 or :3 for larger size. E.g., "ramen" or "geopolitics:3"',
        'section' => 'kunaal_about_interests',
        'type' => 'textarea',
    ));
    
    // =====================================================
    // ABOUT PAGE - INSPIRATIONS
    // =====================================================
    $wp_customize->add_section('kunaal_about_inspirations', array(
        'title' => 'About: Inspirations',
        'priority' => 55,
        'description' => 'People whose work inspires you.',
    ));
    
    $wp_customize->add_setting('kunaal_about_show_inspirations', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_show_inspirations', array(
        'label' => 'Show Inspirations',
        'section' => 'kunaal_about_inspirations',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_inspirations_title', array(
        'default' => 'People Who Inspire Me',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_title', array(
        'label' => 'Section Title',
        'section' => 'kunaal_about_inspirations',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_inspirations_data', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_data', array(
        'label' => 'Inspirations (JSON)',
        'description' => '[{"name":"Name","role":"Description","photo":"url","link":"url"}]',
        'section' => 'kunaal_about_inspirations',
        'type' => 'textarea',
    ));
    
    // =====================================================
    // ABOUT PAGE - FUN STATS
    // =====================================================
    $wp_customize->add_section('kunaal_about_stats', array(
        'title' => 'About: Fun Stats',
        'priority' => 56,
        'description' => 'Animated number counters.',
    ));
    
    $wp_customize->add_setting('kunaal_about_show_stats', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_show_stats', array(
        'label' => 'Show Stats',
        'section' => 'kunaal_about_stats',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_stats_title', array(
        'default' => 'By the Numbers',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_stats_title', array(
        'label' => 'Section Title',
        'section' => 'kunaal_about_stats',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_stats_data', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('kunaal_about_stats_data', array(
        'label' => 'Stats (JSON)',
        'description' => '[{"number":"30+","label":"Countries visited"},{"number":"500+","label":"Tacos eaten"}]',
        'section' => 'kunaal_about_stats',
        'type' => 'textarea',
    ));
    
    // =====================================================
    // ABOUT PAGE - CONNECT
    // =====================================================
    $wp_customize->add_section('kunaal_about_connect', array(
        'title' => 'About: Connect',
        'priority' => 57,
        'description' => 'Social links section.',
    ));
    
    $wp_customize->add_setting('kunaal_about_show_connect', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_show_connect', array(
        'label' => 'Show Connect Section',
        'section' => 'kunaal_about_connect',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_connect_title', array(
        'default' => 'Want to connect?',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_connect_title', array(
        'label' => 'Section Title',
        'section' => 'kunaal_about_connect',
        'type' => 'text',
    ));
    
    // =====================================================
    // ABOUT PAGE - INTERSTITIAL IMAGE
    // =====================================================
    $wp_customize->add_section('kunaal_about_interstitial', array(
        'title' => 'About: Interstitial Image',
        'priority' => 58,
        'description' => 'Full-bleed parallax image break.',
    ));
    
    $wp_customize->add_setting('kunaal_about_interstitial_image', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kunaal_about_interstitial_image', array(
        'label' => 'Interstitial Image',
        'description' => 'Full-bleed image for visual break. Leave empty to skip.',
        'section' => 'kunaal_about_interstitial',
    )));
    
    $wp_customize->add_setting('kunaal_about_interstitial_caption', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_interstitial_caption', array(
        'label' => 'Caption',
        'section' => 'kunaal_about_interstitial',
        'type' => 'text',
    ));
    
    // =====================================================
    // ABOUT PAGE - MAP PLACES (NEW FORMAT)
    // =====================================================
    $wp_customize->add_setting('kunaal_about_map_intro', array(
        'default' => 'The places that have shaped who I am.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_intro', array(
        'label' => 'Map Introduction',
        'section' => 'kunaal_about_map',
        'type' => 'textarea',
    ));
    
    $wp_customize->add_setting('kunaal_about_map_places', array(
        'default' => '',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('kunaal_about_map_places', array(
        'label' => 'Places (JSON)',
        'description' => '[{"name":"City, Country","lat":0.00,"lng":0.00,"type":"lived|visited","years":"2015-2020","note":"Your story"}]',
        'section' => 'kunaal_about_map',
        'type' => 'textarea',
    ));
    
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
    
    $wp_customize->add_setting('kunaal_contact_intro', array(
        'default' => 'Have a question, idea, or just want to say hello? I\'d love to hear from you.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_intro', array(
        'label' => 'Introduction',
        'section' => 'kunaal_contact_page',
        'type' => 'textarea',
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
    // CONTACT PAGE - MESSENGER QR TILES
    // =====================================================
    $wp_customize->add_setting('kunaal_contact_messenger_telegram_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_telegram_enabled', array(
        'label' => 'Enable Telegram QR',
        'section' => 'kunaal_contact_page',
        'type' => 'checkbox',
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_telegram_mode', array(
        'default' => 'redirect',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_telegram_mode', array(
        'label' => 'Telegram QR Mode',
        'description' => 'Redirect mode encodes a site URL (privacy). Direct mode encodes your link/handle.',
        'section' => 'kunaal_contact_page',
        'type' => 'select',
        'choices' => array(
            'redirect' => 'Site redirect (recommended)',
            'direct' => 'Direct link/handle',
        ),
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_telegram_target', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_telegram_target', array(
        'label' => 'Telegram Target (URL or @handle)',
        'description' => 'Examples: https://t.me/yourname OR @yourname (needed for redirect and for direct mode).',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_telegram_redirect_slug', array(
        'default' => 'telegram',
        'sanitize_callback' => 'sanitize_title',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_telegram_redirect_slug', array(
        'label' => 'Telegram Redirect Slug',
        'description' => 'Used for site redirect URLs like /connect/telegram',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_contact_messenger_line_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_line_enabled', array(
        'label' => 'Enable LINE QR',
        'section' => 'kunaal_contact_page',
        'type' => 'checkbox',
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_line_mode', array(
        'default' => 'redirect',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_line_mode', array(
        'label' => 'LINE QR Mode',
        'description' => 'Redirect mode encodes a site URL (privacy). Direct mode encodes your link.',
        'section' => 'kunaal_contact_page',
        'type' => 'select',
        'choices' => array(
            'redirect' => 'Site redirect (recommended)',
            'direct' => 'Direct link',
        ),
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_line_target', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_line_target', array(
        'label' => 'LINE Target (URL)',
        'description' => 'Example: https://line.me/R/ti/p/@yourname (needed for redirect and direct mode).',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_line_redirect_slug', array(
        'default' => 'line',
        'sanitize_callback' => 'sanitize_title',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_line_redirect_slug', array(
        'label' => 'LINE Redirect Slug',
        'description' => 'Used for site redirect URLs like /connect/line',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_contact_messenger_viber_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_viber_enabled', array(
        'label' => 'Enable Viber QR',
        'section' => 'kunaal_contact_page',
        'type' => 'checkbox',
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_viber_mode', array(
        'default' => 'redirect',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_viber_mode', array(
        'label' => 'Viber QR Mode',
        'description' => 'Redirect mode encodes a site URL (privacy). Direct mode encodes your link.',
        'section' => 'kunaal_contact_page',
        'type' => 'select',
        'choices' => array(
            'redirect' => 'Site redirect (recommended)',
            'direct' => 'Direct link',
        ),
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_viber_target', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_viber_target', array(
        'label' => 'Viber Target (URL)',
        'description' => 'Provide a public Viber link (avoid phone numbers). Used for redirect and direct mode.',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));
    $wp_customize->add_setting('kunaal_contact_messenger_viber_redirect_slug', array(
        'default' => 'viber',
        'sanitize_callback' => 'sanitize_title',
    ));
    $wp_customize->add_control('kunaal_contact_messenger_viber_redirect_slug', array(
        'label' => 'Viber Redirect Slug',
        'description' => 'Used for site redirect URLs like /connect/viber',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));
}
add_action('customize_register', 'kunaal_customize_register');

/**
 * Build a messenger URL from a platform + target (URL or handle).
 */
function kunaal_build_messenger_target_url($platform, $raw_target) {
    $raw_target = is_string($raw_target) ? trim($raw_target) : '';
    if ($raw_target === '') return '';

    // If looks like a URL/protocol, trust it (we'll validate protocol later when redirecting)
    if (preg_match('#^[a-zA-Z][a-zA-Z0-9+.-]*://#', $raw_target)) {
        return $raw_target;
    }

    // Handle-based convenience (Telegram only)
    if ($platform === 'telegram') {
        $handle = ltrim($raw_target, '@');
        return $handle ? ('https://t.me/' . $handle) : '';
    }

    // For LINE/Viber, require an explicit URL for safety/clarity
    return '';
}

/**
 * QR image URL (Google Chart fallback).
 */
function kunaal_qr_img_src($text, $size = 220) {
    $size = max(120, min(512, (int) $size));
    return 'https://chart.googleapis.com/chart?cht=qr&chs=' . $size . 'x' . $size . '&chld=M|0&chl=' . rawurlencode($text);
}

/**
 * /connect/<slug> redirects (privacy-friendly QR codes)
 */
function kunaal_register_connect_rewrite() {
    add_rewrite_rule('^connect/([^/]+)/?$', 'index.php?kunaal_connect=$matches[1]', 'top');
}
add_action('init', 'kunaal_register_connect_rewrite');

function kunaal_connect_query_vars($vars) {
    $vars[] = 'kunaal_connect';
    return $vars;
}
add_filter('query_vars', 'kunaal_connect_query_vars');

function kunaal_connect_template_redirect() {
    $slug = get_query_var('kunaal_connect');
    if (!$slug) return;

    $slug = sanitize_title($slug);

    $platforms = array(
        'telegram' => array(
            'enabled' => (bool) get_theme_mod('kunaal_contact_messenger_telegram_enabled', false),
            'mode' => get_theme_mod('kunaal_contact_messenger_telegram_mode', 'redirect'),
            'target' => get_theme_mod('kunaal_contact_messenger_telegram_target', ''),
            'slug' => sanitize_title(get_theme_mod('kunaal_contact_messenger_telegram_redirect_slug', 'telegram')),
        ),
        'line' => array(
            'enabled' => (bool) get_theme_mod('kunaal_contact_messenger_line_enabled', false),
            'mode' => get_theme_mod('kunaal_contact_messenger_line_mode', 'redirect'),
            'target' => get_theme_mod('kunaal_contact_messenger_line_target', ''),
            'slug' => sanitize_title(get_theme_mod('kunaal_contact_messenger_line_redirect_slug', 'line')),
        ),
        'viber' => array(
            'enabled' => (bool) get_theme_mod('kunaal_contact_messenger_viber_enabled', false),
            'mode' => get_theme_mod('kunaal_contact_messenger_viber_mode', 'redirect'),
            'target' => get_theme_mod('kunaal_contact_messenger_viber_target', ''),
            'slug' => sanitize_title(get_theme_mod('kunaal_contact_messenger_viber_redirect_slug', 'viber')),
        ),
    );

    $platform_key = '';
    foreach ($platforms as $key => $cfg) {
        if ($cfg['slug'] === $slug) {
            $platform_key = $key;
            break;
        }
    }
    if (!$platform_key) {
        status_header(404);
        exit;
    }

    $cfg = $platforms[$platform_key];
    if (empty($cfg['enabled'])) {
        status_header(404);
        exit;
    }

    $target = kunaal_build_messenger_target_url($platform_key, $cfg['target']);
    if (!$target) {
        status_header(404);
        exit;
    }

    // Only allow safe protocols for redirect
    $allowed = array('https', 'http', 'tg', 'line', 'viber');
    $scheme = wp_parse_url($target, PHP_URL_SCHEME);
    if (!$scheme || !in_array($scheme, $allowed, true)) {
        status_header(404);
        exit;
    }

    wp_redirect($target, 302);
    exit;
}
add_action('template_redirect', 'kunaal_connect_template_redirect');

function kunaal_flush_rewrite_on_switch() {
    kunaal_register_connect_rewrite();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'kunaal_flush_rewrite_on_switch');

/**
 * Helper: Get initials
 */
function kunaal_get_initials() {
    $first = get_theme_mod('kunaal_author_first_name', 'Kunaal');
    $last = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
    return strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
}

/**
 * Helper: Output Subscribe Section
 */
function kunaal_subscribe_section() {
    if (!get_theme_mod('kunaal_subscribe_enabled', false)) {
        return;
    }
    
    // Check location setting - only show bottom if 'bottom' or 'both'
    $sub_location = get_theme_mod('kunaal_subscribe_location', 'both');
    if (!in_array($sub_location, array('bottom', 'both'))) {
        return;
    }
    
    $heading = get_theme_mod('kunaal_subscribe_heading', 'Stay updated');
    $description = get_theme_mod('kunaal_subscribe_description', 'Get notified when new essays and jottings are published.');
    $form_action = get_theme_mod('kunaal_subscribe_form_action', '');
    
    ?>
    <section class="subscribe-section reveal">
        <h3><?php echo esc_html($heading); ?></h3>
        <p><?php echo esc_html($description); ?></p>
        <form class="subscribe-form" action="<?php echo esc_url($form_action); ?>" method="post">
            <input type="email" name="email" placeholder="Your email address" required />
            <button type="submit">Subscribe</button>
        </form>
    </section>
    <?php
}

/**
 * Helper: Get all topics with counts
 */
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

/**
 * Helper: Get card image URL
 */
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

/**
 * AJAX: Filter content
 */
function kunaal_filter_content() {
    // Don't die on nonce failure - just log and continue for public pages
    $nonce_valid = isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'kunaal_theme_nonce');
    
    // For logged-in admin users, we might want stricter checking
    // But for public filtering, allow it to work
    
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

/**
 * Flush rewrite rules on theme switch
 */
function kunaal_theme_activation() {
    kunaal_register_post_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'kunaal_theme_activation');

/**
 * Create default pages on theme activation
 */
function kunaal_create_default_pages() {
    // About page
    if (!get_page_by_path('about')) {
        wp_insert_post(array(
            'post_title' => 'About',
            'post_name' => 'about',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- wp:paragraph --><p>Write about yourself here.</p><!-- /wp:paragraph -->',
        ));
    }
    
    // Contact page
    if (!get_page_by_path('contact')) {
        wp_insert_post(array(
            'post_title' => 'Contact',
            'post_name' => 'contact',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '<!-- wp:paragraph --><p>Add your contact information here.</p><!-- /wp:paragraph -->',
        ));
    }
}
add_action('after_switch_theme', 'kunaal_create_default_pages');

/**
 * Set reading settings on activation
 */
function kunaal_set_reading_settings() {
    update_option('show_on_front', 'posts');
}
add_action('after_switch_theme', 'kunaal_set_reading_settings');

/**
 * Include block patterns and Gutenberg enhancements
 */
require_once KUNAAL_THEME_DIR . '/inc/blocks.php';

/**
 * DK PDF Customization - Improve PDF output
 */
function kunaal_customize_dkpdf() {
    // Add custom CSS to PDFs to match theme styling
    ?>
    <style>
        /* PDF Styling to match theme */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #0b1220;
            background: #FDFCFA;
            line-height: 1.6;
            font-size: 14px;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            font-weight: 600;
            color: #0b1220;
            margin-top: 1.5em;
            margin-bottom: 0.5em;
        }
        
        h1 { font-size: 24px; }
        h2 { font-size: 20px; border-bottom: 1px solid #e5e5e5; padding-bottom: 8px; }
        h3 { font-size: 16px; }
        
        p {
            margin: 1em 0;
            line-height: 1.7;
        }
        
        a {
            color: #1E5AFF;
            text-decoration: none;
        }
        
        code {
            background: #f5f3ef;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: "Courier New", monospace;
            font-size: 13px;
        }
        
        pre {
            background: #f5f3ef;
            padding: 16px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 1.5em 0;
        }
        
        blockquote {
            margin: 1.5em 0;
            padding: 12px 16px;
            border-left: 3px solid #1E5AFF;
            background: rgba(125,107,93,0.08);
            font-size: 15px;
        }
        
        img {
            max-width: 100%;
            height: auto;
            margin: 1.5em 0;
        }
        
        ul, ol {
            margin: 1em 0;
            padding-left: 2em;
        }
        
        li {
            margin: 0.5em 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5em 0;
        }
        
        th, td {
            border: 1px solid #e5e5e5;
            padding: 8px 12px;
            text-align: left;
        }
        
        th {
            background: #f5f3ef;
            font-weight: 600;
        }
        
        .insightBox, .wp-block-kunaal-insight {
            background: #f9f7f4;
            padding: 16px;
            border-left: 2px solid #7D6B5D;
            margin: 1.5em 0;
        }
        
        .chartWrap {
            margin: 2em 0;
            page-break-inside: avoid;
        }
        
        .chartCaption {
            font-family: monospace;
            font-size: 11px;
            color: #7D6B5D;
            margin-bottom: 8px;
        }
        
        /* Hide elements that don't make sense in PDF */
        .shareDock, .printDock, .subscribeDock,
        .shareItem, .shareToggle,
        nav, .mast, .progress,
        button:not(.accordion summary),
        .subscribe-section {
            display: none !important;
        }
    </style>
    <?php
}
add_action('dkpdf_head_content', 'kunaal_customize_dkpdf');

/**
 * Disable DK PDF auto-button insertion
 * We use our own PDF system via the action dock
 */
function kunaal_disable_dkpdf_button() {
    // Remove DK PDF button from content
    if (class_exists('DKPDF_Admin')) {
        remove_filter('the_content', array('DKPDF_Admin', 'button_position'));
    }
    // Also try alternative hook names
    remove_all_filters('dkpdf_button_position');
}
add_action('init', 'kunaal_disable_dkpdf_button', 99);

// Disable DK PDF button via settings filter
function kunaal_dkpdf_button_position($position) {
    return 'none'; // Don't show button
}
add_filter('dkpdf_button_position', 'kunaal_dkpdf_button_position');

// Hide button via attributes
function kunaal_dkpdf_button_attributes() {
    return array(
        'text' => '',
        'class' => 'dkpdf-hidden',
        'style' => 'display:none !important;'
    );
}
add_filter('dkpdf_button_attributes', 'kunaal_dkpdf_button_attributes');

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
    $author_first = get_theme_mod('kunaal_author_first_name', 'Kunaal');
    $author_last = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
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
    $twitter_handle = get_theme_mod('kunaal_twitter_handle', '');
    
    ?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo esc_attr($title); ?>" />
    <meta property="og:description" content="<?php echo $description; ?>" />
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
    <meta name="twitter:description" content="<?php echo $description; ?>" />
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
    // Verify nonce
    if (!isset($_POST['kunaal_contact_nonce']) || !wp_verify_nonce($_POST['kunaal_contact_nonce'], 'kunaal_contact_form')) {
        wp_send_json_error(array('message' => 'Security check failed. Please refresh and try again.'));
    }
    
    // Sanitize inputs
    $name = isset($_POST['contact_name']) ? sanitize_text_field($_POST['contact_name']) : '';
    $email = isset($_POST['contact_email']) ? sanitize_email($_POST['contact_email']) : '';
    $message = isset($_POST['contact_message']) ? sanitize_textarea_field($_POST['contact_message']) : '';
    $honeypot = isset($_POST['contact_company']) ? sanitize_text_field($_POST['contact_company']) : '';

    // Honeypot check (bots)
    if (!empty($honeypot)) {
        wp_send_json_error(array('message' => 'Sorry, your message could not be sent.'));
    }

    // Basic rate limiting by IP
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
    $rate_key = 'kunaal_contact_rl_' . md5($ip);
    $count = (int) get_transient($rate_key);
    if ($count >= 5) {
        wp_send_json_error(array('message' => 'Please wait a bit before sending another message.'));
    }
    set_transient($rate_key, $count + 1, 10 * MINUTE_IN_SECONDS);
    
    // Validate
    if (empty($name) || empty($email) || empty($message)) {
        wp_send_json_error(array('message' => 'Please fill in all fields.'));
    }
    
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
    }
    
    // Get recipient email
    $to_email = get_theme_mod('kunaal_contact_recipient_email', get_option('admin_email'));
    if (!is_email($to_email)) {
        $to_email = get_option('admin_email');
    }
    
    // Build email
    $site_name = get_bloginfo('name');
    $email_subject = '[' . $site_name . '] New note from ' . $name;
    $email_body = "You received a new message from your site contact form.\n\n";
    $email_body .= "Name: {$name}\n";
    $email_body .= "Email: {$email}\n";
    $email_body .= "Page: " . esc_url_raw(wp_get_referer()) . "\n";
    $email_body .= "Time: " . gmdate('c') . " (UTC)\n\n";
    $email_body .= "Message:\n{$message}\n";
    
    $headers = array(
        'From: ' . $site_name . ' <' . get_option('admin_email') . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    );
    
    // Send email
    $sent = wp_mail($to_email, $email_subject, $email_body, $headers);
    
    if ($sent) {
        wp_send_json_success(array('message' => 'Thank you! Your message has been sent.'));
    } else {
        wp_send_json_error(array('message' => 'Sorry, there was an error sending your message. Please try emailing directly.'));
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

/**
 * Enqueue Inline Formats in Editor
 */
function kunaal_enqueue_inline_formats_editor() {
    // Only in admin and block editor context
    if (!is_admin()) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || !$screen->is_block_editor()) {
        return;
    }
    
    wp_enqueue_script('kunaal-inline-formats');
    wp_enqueue_style('kunaal-inline-formats-style');
}
add_action('enqueue_block_editor_assets', 'kunaal_enqueue_inline_formats_editor');

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
