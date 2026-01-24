<?php
/**
 * AJAX: Filter Content
 * 
 * Handles content filtering requests for essays and jottings.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper: Validate filter request
 */
function kunaal_validate_filter_request(): bool {
    if (empty($_POST['nonce'])) {
        return false;
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified below
    $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
    if (!wp_verify_nonce($nonce, 'kunaal_theme_nonce')) {
        return false;
    }
    return true;
}

/**
 * Helper: Parse and sanitize topics from POST data
 */
function kunaal_parse_filter_topics(): array {
    $topics = array();
    if (isset($_POST['topics'])) {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below
        $topics_raw = wp_unslash($_POST['topics']);
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
function kunaal_build_filter_query_args(string $post_type, array $topics, string $sort, string $search, int $page, int $per_page): array {
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
        case 'popular':
            // Popular: order by pageviews meta (DESC), fallback to date for ties
            // Custom posts_orderby filter handles NULL/missing values as 0
            $args['meta_key'] = 'kunaal_pageviews';
            $args['orderby'] = array(
                'meta_value_num' => 'DESC',
                'date' => 'DESC',
            );
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
function kunaal_prime_post_caches(array $post_ids): void {
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
function kunaal_extract_post_topics(int $post_id): array {
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
function kunaal_build_post_data(int $post_id): array {
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
function kunaal_filter_content(): void {
    try {
        if (!kunaal_validate_filter_request()) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh the page and try again.'));
            wp_die();
        }
        
        // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- all values sanitized below
        $post_type = isset($_POST['post_type']) ? sanitize_text_field(wp_unslash($_POST['post_type'])) : 'essay';
        $topics = kunaal_parse_filter_topics();
        $sort = isset($_POST['sort']) ? sanitize_text_field(wp_unslash($_POST['sort'])) : 'new';
        $search = isset($_POST['search']) ? sanitize_text_field(wp_unslash($_POST['search'])) : '';
        $page = isset($_POST['page']) ? absint(wp_unslash($_POST['page'])) : 1;
        $per_page = isset($_POST['per_page']) ? absint(wp_unslash($_POST['per_page'])) : 12;
        // phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        
        $args = kunaal_build_filter_query_args($post_type, $topics, $sort, $search, $page, $per_page);
        $query = new WP_Query($args);
        $posts_data = array();
        
        // If popular sort returns no results (all pageviews are 0 or missing), fall back to date
        if ($sort === 'popular' && !$query->have_posts() && $query->found_posts === 0) {
            // Retry with date ordering as fallback
            $args['meta_query'] = array();
            unset($args['meta_key']);
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            $query = new WP_Query($args);
        }
        
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
    } catch (\Throwable $e) {
        kunaal_theme_log('AJAX filter error', array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString()));
        wp_send_json_error(array('message' => KUNAAL_ERROR_MESSAGE_GENERIC));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_filter', 'kunaal_filter_content');
add_action('wp_ajax_nopriv_kunaal_filter', 'kunaal_filter_content');

