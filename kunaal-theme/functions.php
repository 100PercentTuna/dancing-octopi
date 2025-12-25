<?php
/**
 * Kunaal Theme Functions
 *
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

define('KUNAAL_THEME_VERSION', '3.8.0');
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
    // Google Fonts
    wp_enqueue_style(
        'kunaal-google-fonts',
        'https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600;6..72,700&family=Inter:opsz,wght@14..32,300..700&display=swap',
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
}
add_action('wp_enqueue_scripts', 'kunaal_enqueue_assets');

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
        $post_type = get_current_screen()->post_type ?? '';
        if (use_block_editor_for_post_type($post_type)) {
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
}
add_action('customize_register', 'kunaal_customize_register');

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
