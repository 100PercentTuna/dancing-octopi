<?php
/**
 * AJAX Handlers
 * 
 * Handles all AJAX requests: filter content, subscribe, contact form, debug log.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper: Validate filter request
 */
function kunaal_validate_filter_request() {
    if (empty($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_theme_nonce')) {
        return false;
    }
    return true;
}

/**
 * Helper: Parse and sanitize topics from POST data
 */
function kunaal_parse_filter_topics() {
    $topics = array();
    if (isset($_POST['topics'])) {
        $topics_raw = $_POST['topics'];
        if (is_array($topics_raw)) {
            $topics = array_filter(array_map('sanitize_text_field', $topics_raw));
        } elseif (is_string($topics_raw) && !empty($topics_raw)) {
            $topics = array_filter(array_map('sanitize_text_field', explode(',', $topics_raw)));
        }
    }
    return $topics;
}

/**
 * Helper: Build WP_Query args for filter
 */
function kunaal_build_filter_query_args($post_type, $topics, $sort, $search, $page, $per_page) {
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => min($per_page, 100), // Limit to prevent DoS
        'paged' => $page,
        'post_status' => 'publish',
    );
    
    // Topics filter
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
    
    return $args;
}

/**
 * Helper: Prime caches to prevent N+1 queries
 */
function kunaal_prime_post_caches($post_ids) {
    if (empty($post_ids)) {
        return;
    }
    
    // Ensure WordPress functions are available
    if (!function_exists('update_post_meta_cache')) {
        require_once ABSPATH . 'wp-admin/includes/post.php';
    }
    if (!function_exists('update_object_term_cache')) {
        require_once ABSPATH . 'wp-includes/taxonomy.php';
    }
    
    if (function_exists('update_post_meta_cache')) {
        update_post_meta_cache($post_ids);
    }
    if (function_exists('update_object_term_cache')) {
        update_object_term_cache($post_ids, array('essay', 'jotting'));
    }
}

/**
 * Helper: Extract topics from post
 */
function kunaal_extract_post_topics($post_id) {
    $topics_list = get_the_terms($post_id, 'topic');
    $tags = array();
    $tag_slugs = array();
    
    if ($topics_list && !is_wp_error($topics_list)) {
        foreach ($topics_list as $topic) {
            $tags[] = $topic->name;
            $tag_slugs[] = $topic->slug;
        }
    }
    
    return array('tags' => $tags, 'tagSlugs' => $tag_slugs);
}

/**
 * Helper: Build post data array for JSON response
 */
function kunaal_build_post_data($post_id) {
    $topics = kunaal_extract_post_topics($post_id);
    
    return array(
        'id' => $post_id,
        'title' => get_the_title(),
        'url' => get_permalink(),
        'date' => get_the_date('j F Y'),
        'dateShort' => get_the_date('j M Y'),
        'subtitle' => get_post_meta($post_id, 'kunaal_subtitle', true),
        'readTime' => get_post_meta($post_id, 'kunaal_read_time', true),
        'image' => kunaal_get_card_image_url($post_id),
        'tags' => $topics['tags'],
        'tagSlugs' => $topics['tagSlugs'],
    );
}

/**
 * AJAX: Filter content
 */
function kunaal_filter_content() {
    try {
        if (!kunaal_validate_filter_request()) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh the page and try again.'));
            wp_die();
        }
        
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'essay';
        $topics = kunaal_parse_filter_topics();
        $sort = isset($_POST['sort']) ? sanitize_text_field($_POST['sort']) : 'new';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $page = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 12;
        
        $args = kunaal_build_filter_query_args($post_type, $topics, $sort, $search, $page, $per_page);
        $query = new WP_Query($args);
        $posts_data = array();
        
        if ($query->have_posts()) {
            $post_ids = wp_list_pluck($query->posts, 'ID');
            kunaal_prime_post_caches($post_ids);
            
            while ($query->have_posts()) {
                $query->the_post();
                $posts_data[] = kunaal_build_post_data(get_the_ID());
            }
        }
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'posts' => $posts_data,
            'total' => $query->found_posts,
            'pages' => $query->max_num_pages,
            'page' => $page,
        ));
        wp_die();
    } catch (Exception $e) {
        kunaal_theme_log('AJAX filter error', array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString()));
        wp_send_json_error(array('message' => KUNAAL_ERROR_MESSAGE_GENERIC));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_filter', 'kunaal_filter_content');
add_action('wp_ajax_nopriv_kunaal_filter', 'kunaal_filter_content');

/**
 * Helper: Validate debug log request
 *
 * @return array Array with 'valid' (bool) and 'error' (string) keys
 */
function kunaal_validate_debug_log_request() {
    $result = array('valid' => true, 'error' => '');
    
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        $result = array('valid' => false, 'error' => 'Debug logging disabled');
    } elseif (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_debug_log_nonce')) {
        $result = array('valid' => false, 'error' => 'Invalid nonce');
    } elseif (!current_user_can('edit_posts')) {
        $result = array('valid' => false, 'error' => 'Insufficient permissions');
    }
    
    return $result;
}

/**
 * Helper: Get log data from POST
 */
function kunaal_get_debug_log_data() {
    $log_json = isset($_POST['log_data']) ? stripslashes($_POST['log_data']) : '';
    if (empty($log_json)) {
        $raw_input = file_get_contents('php://input');
        if (!empty($raw_input)) {
            $log_json = $raw_input;
        }
    }
    return $log_json;
}

/**
 * Helper: Validate log data structure
 *
 * @param mixed $log_data Log data to validate
 * @return bool True if valid, false otherwise
 */
function kunaal_validate_debug_log_data($log_data) {
    if (!$log_data || !isset($log_data['location']) || !isset($log_data['message'])) {
        return false;
    }
    return true;
}

/**
 * Helper: Write log to file
 */
function kunaal_write_debug_log($log_data) {
    $log_file = get_template_directory() . '/debug.log';
    $log_line = json_encode($log_data) . "\n";
    @file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

/**
 * Debug log handler - receives logs from JavaScript and writes to theme debug.log
 * Only active during development/debugging (WP_DEBUG must be true)
 * Nonce-protected and capability-checked for security
 */
function kunaal_handle_debug_log() {
    $validation = kunaal_validate_debug_log_request();
    if (!$validation['valid']) {
        wp_send_json_error(array('message' => $validation['error']));
        wp_die();
    }
    
    $log_json = kunaal_get_debug_log_data();
    $log_data = json_decode($log_json, true);
    
    if (!kunaal_validate_debug_log_data($log_data)) {
        wp_send_json_error(array('message' => 'Invalid log data'));
        wp_die();
    }
    
    kunaal_write_debug_log($log_data);
    
    wp_send_json_success(array('logged' => true));
    wp_die();
}
// Only register handlers if WP_DEBUG is enabled
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('wp_ajax_kunaal_debug_log', 'kunaal_handle_debug_log');
    // Note: Removed nopriv handler - debug logging requires authentication
}

