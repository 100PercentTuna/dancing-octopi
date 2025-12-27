<?php
/**
 * Post Type Validation
 * 
 * Validates essays and jottings before publishing (REST API and classic editor).
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

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
        echo '<div class="notice notice-error"><p><strong>Essay cannot be published:</strong></p><ul>';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul></div>';
        delete_transient('kunaal_essay_errors_' . $post_id);
    }
}
add_action('admin_notices', 'kunaal_display_essay_errors');

