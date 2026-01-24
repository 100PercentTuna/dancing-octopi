<?php
/**
 * Essay Admin Columns
 * 
 * Adds pageviews and order columns to the Essays admin list.
 * Enables drag-and-drop sorting via page-attributes support.
 *
 * @package Kunaal_Theme
 * @since 4.44.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom columns to Essays admin list
 * 
 * @param array $columns Existing columns
 * @return array Modified columns
 */
function kunaal_essay_admin_columns(array $columns): array {
    // Insert pageviews after title, order before date
    $new_columns = array();
    foreach ($columns as $key => $title) {
        $new_columns[$key] = $title;
        if ($key === 'title') {
            $new_columns['kunaal_pageviews'] = __('Views', 'kunaal-theme');
        }
        if ($key === 'date') {
            $new_columns['menu_order'] = __('Order', 'kunaal-theme');
        }
    }
    return $new_columns;
}
add_filter('manage_essay_posts_columns', 'kunaal_essay_admin_columns');

/**
 * Display custom column content
 * 
 * @param string $column Column name
 * @param int    $post_id Post ID
 */
function kunaal_essay_admin_column_content(string $column, int $post_id): void {
    if ($column === 'kunaal_pageviews') {
        $count = (int) get_post_meta($post_id, 'kunaal_pageviews', true);
        echo esc_html(number_format_i18n($count));
    } elseif ($column === 'menu_order') {
        $order = (int) get_post($post_id)->menu_order;
        echo esc_html($order > 0 ? $order : '—');
    }
}
add_action('manage_essay_posts_custom_column', 'kunaal_essay_admin_column_content', 10, 2);

/**
 * Make order column sortable
 * 
 * @param array $columns Sortable columns
 * @return array Modified sortable columns
 */
function kunaal_essay_admin_sortable_columns(array $columns): array {
    $columns['menu_order'] = 'menu_order';
    $columns['kunaal_pageviews'] = 'kunaal_pageviews';
    return $columns;
}
add_filter('manage_edit-essay_sortable_columns', 'kunaal_essay_admin_sortable_columns');

/**
 * Handle sorting by pageviews
 * 
 * @param WP_Query $query Query object
 */
function kunaal_essay_admin_sort_by_pageviews(WP_Query $query): void {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $orderby = $query->get('orderby');
    if ($orderby === 'kunaal_pageviews') {
        $query->set('meta_key', 'kunaal_pageviews');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'kunaal_essay_admin_sort_by_pageviews');
