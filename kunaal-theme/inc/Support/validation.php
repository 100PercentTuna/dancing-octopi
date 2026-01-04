<?php
/**
 * Post Type Validation
 * 
 * Validates essays and jottings before publishing (REST API and classic editor).
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get meta value from request or post meta
 * 
 * @param array|null $meta Request meta array (nullable for REST API safety)
 * @param string $key Meta key
 * @param int $post_id Post ID
 * @return mixed Meta value or null
 */
function kunaal_get_meta_value(?array $meta, string $key, int $post_id = 0): mixed {
    // Handle null meta from REST requests
    if ($meta === null) {
        $meta = array();
    }
    if (isset($meta[$key]) && !empty($meta[$key])) {
        return $meta[$key];
    }
    if ($post_id) {
        return get_post_meta($post_id, $key, true);
    }
    return null;
}

/**
 * Check if essay has topics
 * 
 * @param WP_REST_Request $request Request object
 * @param int $post_id Post ID
 * @return bool True if has topics
 */
function kunaal_essay_has_topics(WP_REST_Request $request, int $post_id = 0): bool {
    $topic_terms = $request->get_param('topic');
    if (!empty($topic_terms)) {
        return true;
    }
    if ($post_id) {
        $existing_topics = get_the_terms($post_id, 'topic');
        return !empty($existing_topics) && !is_wp_error($existing_topics);
    }
    return false;
}

/**
 * Check if essay has image
 * 
 * @param WP_REST_Request $request Request object
 * @param array|null $meta Request meta array (nullable for REST API safety)
 * @param int $post_id Post ID
 * @return bool True if has image
 */
function kunaal_essay_has_image(WP_REST_Request $request, ?array $meta, int $post_id = 0): bool {
    $featured_media = $request->get_param('featured_media');
    $card_image = kunaal_get_meta_value($meta, 'kunaal_card_image', $post_id);
    return !empty($card_image) || !empty($featured_media) || ($post_id && has_post_thumbnail($post_id));
}

/**
 * Validate Essay Before Publish (REST API compatible for Gutenberg)
 * 
 * Wrapped in try-catch per architecture rule 6.3 to prevent uncaught
 * exceptions from causing 500 errors in the REST API.
 */
function kunaal_validate_essay_rest(WP_Post $prepared_post, WP_REST_Request $request): WP_Post|WP_Error {
    // Only validate essays being published
    if ($prepared_post->post_type !== 'essay' || $prepared_post->post_status !== 'publish') {
        return $prepared_post;
    }
    
    try {
        $post_id = isset($prepared_post->ID) ? $prepared_post->ID : 0;
        $errors = array();
        // Null coalesce to empty array - REST requests may not include meta
        $meta = $request->get_param('meta') ?? array();
        
        // Check for subtitle (now required)
        $subtitle = kunaal_get_meta_value($meta, 'kunaal_subtitle', $post_id);
        if (empty($subtitle)) {
            $errors[] = '📝 SUBTITLE/DEK is required — Find "Essay Details" in the right sidebar';
        }
        
        // Check for read time
        $read_time = kunaal_get_meta_value($meta, 'kunaal_read_time', $post_id);
        if (empty($read_time)) {
            $errors[] = '⏱️ READ TIME is required — Find "Essay Details" in the right sidebar';
        }
        
        // Check for topics
        if (!kunaal_essay_has_topics($request, $post_id)) {
            $errors[] = '🏷️ At least one TOPIC is required — Find "Topics" in the right sidebar';
        }
        
        // Check for card image or featured image
        if (!kunaal_essay_has_image($request, $meta, $post_id)) {
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
    } catch (\Throwable $e) {
        // Log the error for debugging
        if (function_exists('kunaal_log')) {
            kunaal_log('validation_error', 'Essay validation failed: ' . $e->getMessage());
        }
        return new WP_Error(
            'validation_error',
            'An unexpected error occurred during validation. Please try again.',
            array('status' => 500)
        );
    }
}
add_filter('rest_pre_insert_essay', 'kunaal_validate_essay_rest', 10, 2);

/**
 * Validate Jotting Before Publish (REST API compatible for Gutenberg)
 * 
 * Wrapped in try-catch per architecture rule 6.3 to prevent uncaught
 * exceptions from causing 500 errors in the REST API.
 */
function kunaal_validate_jotting_rest(WP_Post $prepared_post, WP_REST_Request $request): WP_Post|WP_Error {
    // Only validate jottings being published
    if ($prepared_post->post_type !== 'jotting' || $prepared_post->post_status !== 'publish') {
        return $prepared_post;
    }
    
    try {
        $post_id = isset($prepared_post->ID) ? $prepared_post->ID : 0;
        $errors = array();
        // Null coalesce to empty array - REST requests may not include meta
        $meta = $request->get_param('meta') ?? array();
        
        // Check for subtitle (required for jottings)
        $subtitle = kunaal_get_meta_value($meta, 'kunaal_subtitle', $post_id);
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
    } catch (\Throwable $e) {
        // Log the error for debugging
        if (function_exists('kunaal_log')) {
            kunaal_log('validation_error', 'Jotting validation failed: ' . $e->getMessage());
        }
        return new WP_Error(
            'validation_error',
            'An unexpected error occurred during validation. Please try again.',
            array('status' => 500)
        );
    }
}
add_filter('rest_pre_insert_jotting', 'kunaal_validate_jotting_rest', 10, 2);

/**
 * Get classic editor meta value
 * 
 * @param string $key Meta key
 * @param int $post_id Post ID
 * @return mixed Meta value or empty string
 */
function kunaal_get_classic_meta(string $key, int $post_id = 0): mixed {
    $value = '';
    if (isset($_POST[$key])) {
        // Sanitize based on expected type
        if (is_array($_POST[$key])) {
            $value = array_map('sanitize_text_field', $_POST[$key]);
        } else {
            $value = sanitize_text_field($_POST[$key]);
        }
    }
    if (empty($value) && $post_id) {
        $value = get_post_meta($post_id, $key, true);
    }
    return $value;
}

/**
 * Check if classic editor essay has topics
 * 
 * @param int $post_id Post ID
 * @return bool True if has topics
 */
function kunaal_classic_essay_has_topics(int $post_id = 0): bool {
    $topics = array();
    if (isset($_POST['tax_input']['topic']) && is_array($_POST['tax_input']['topic'])) {
        $topics = array_map('absint', $_POST['tax_input']['topic']);
    }
    if (!empty(array_filter($topics))) {
        return true;
    }
    if ($post_id) {
        $existing = get_the_terms($post_id, 'topic');
        return !empty($existing) && !is_wp_error($existing);
    }
    return false;
}

/**
 * Check if classic editor essay has image
 * 
 * @param int $post_id Post ID
 * @return bool True if has image
 */
function kunaal_classic_essay_has_image(int $post_id = 0): bool {
    $card_image = kunaal_get_classic_meta('kunaal_card_image', $post_id);
    $featured = isset($_POST['_thumbnail_id']) ? absint($_POST['_thumbnail_id']) : 0;
    if (empty($featured) && $post_id) {
        $featured = get_post_thumbnail_id($post_id);
    }
    return !empty($card_image) || !empty($featured);
}

/**
 * Also validate on classic editor saves (non-Gutenberg)
 */
function kunaal_validate_essay_classic(array $data, array $postarr): array {
    // Skip if this is a REST API request (handled by rest_pre_insert_essay)
    if ((defined('REST_REQUEST') && REST_REQUEST) || 
        $data['post_type'] !== 'essay' || 
        $data['post_status'] !== 'publish') {
        return $data;
    }
    
    $post_id = isset($postarr['ID']) ? $postarr['ID'] : 0;
    $errors = array();
    
    // Check read time
    $read_time = kunaal_get_classic_meta('kunaal_read_time', $post_id);
    if (empty($read_time)) {
        $errors[] = 'Read Time is required (Essay Details box)';
    }
    
    // Check topics
    if (!kunaal_classic_essay_has_topics($post_id)) {
        $errors[] = 'At least one Topic is required';
    }
    
    // Check image
    if (!kunaal_classic_essay_has_image($post_id)) {
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
function kunaal_display_essay_errors(): void {
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

