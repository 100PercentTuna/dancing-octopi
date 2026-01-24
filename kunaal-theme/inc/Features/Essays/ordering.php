<?php
/**
 * Essay Ordering System
 * 
 * Handles default ordering for essays (manual, popularity, date, title).
 * Allows site owner to set default order while visitors can override via filter.
 *
 * @package Kunaal_Theme
 * @since 4.44.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get default essay order setting
 * 
 * @return string Order mode: 'manual', 'popular', 'date', or 'title'
 */
function kunaal_get_essay_default_order(): string {
    $order = kunaal_mod('kunaal_essay_default_order', 'date');
    $allowed = array('manual', 'popular', 'date', 'title');
    return in_array($order, $allowed, true) ? $order : 'date';
}

/**
 * Apply essay ordering to WP_Query args
 * 
 * @param array $args WP_Query arguments
 * @param string $order_mode Order mode: 'manual', 'popular', 'date', 'title', or 'default' (uses setting)
 * @return array Modified query args
 */
function kunaal_apply_essay_order(array $args, string $order_mode = 'default'): array {
    // If 'default', use the Customizer setting
    if ($order_mode === 'default') {
        $order_mode = kunaal_get_essay_default_order();
    }

    switch ($order_mode) {
        case 'manual':
            // Manual order: essays with menu_order > 0 first (ASC), then rest by date DESC
            // Use a custom orderby filter to ensure menu_order > 0 posts appear first
            // Set a flag so the filter can detect this and apply custom SQL
            $args['orderby'] = 'menu_order';
            $args['order'] = 'ASC';
            $args['kunaal_manual_order'] = true;
            break;

        case 'popular':
            // Popular: order by pageviews meta (DESC), fallback to date for ties
            // Use meta_query with EXISTS to include posts with 0 or missing pageviews
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => 'kunaal_pageviews',
                    'compare' => 'EXISTS',
                ),
                array(
                    'key' => 'kunaal_pageviews',
                    'compare' => 'NOT EXISTS',
                ),
            );
            $args['orderby'] = array(
                'meta_value_num' => 'DESC',
                'date' => 'DESC',
            );
            $args['meta_key'] = 'kunaal_pageviews';
            break;

        case 'title':
            // Alphabetical
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            break;

        case 'date':
        default:
            // Date (newest first) - default behavior
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
    }

    return $args;
}

/**
 * Modify archive queries to respect default order
 * 
 * Only applies when no explicit sort is requested (visitor hasn't chosen a sort option).
 */
function kunaal_essay_archive_order(WP_Query $query): void {
    // Only on frontend, main query, essay archive
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if (!is_post_type_archive('essay')) {
        return;
    }

    // Don't override if visitor has explicitly chosen a sort (handled by filter system)
    // The filter system will set orderby via query vars, so we only apply default if not set
    if (!empty($query->get('orderby'))) {
        return;
    }

    // Apply default order
    $default_order = kunaal_get_essay_default_order();
    $query_args = kunaal_apply_essay_order(array(), $default_order);
    
    foreach ($query_args as $key => $value) {
        $query->set($key, $value);
    }
}
add_action('pre_get_posts', 'kunaal_essay_archive_order');

/**
 * Custom orderby for manual ordering: posts with menu_order > 0 first
 * 
 * Uses SQL CASE to ensure posts with menu_order > 0 appear before posts with menu_order = 0
 */
function kunaal_essay_manual_orderby(string $orderby, WP_Query $query): string {
    // Only apply if this is a manual order query
    if (empty($query->get('kunaal_manual_order'))) {
        return $orderby;
    }

    global $wpdb;
    
    // Custom SQL: posts with menu_order > 0 get priority 0, others get priority 1
    // Then sort by menu_order ASC, then date DESC
    // This ensures manually ordered posts (menu_order > 0) appear first
    $orderby = "CASE WHEN {$wpdb->posts}.menu_order > 0 THEN 0 ELSE 1 END ASC, "
             . "{$wpdb->posts}.menu_order ASC, "
             . "{$wpdb->posts}.post_date DESC";
    
    return $orderby;
}
add_filter('posts_orderby', 'kunaal_essay_manual_orderby', 10, 2);
