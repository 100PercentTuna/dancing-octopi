<?php
/**
 * Custom Post Types and Taxonomies
 * 
 * Registers essay, jotting, and subscriber post types, and topic taxonomy.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Custom Post Types
 */
function kunaal_register_post_types(): void {
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
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'page-attributes'),
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

    /**
     * Subscriber (private)
     * Built-in subscribe flow stores subscribers as private posts for easy export and admin visibility,
     * without exposing anything publicly.
     */
    register_post_type('kunaal_subscriber', array(
        'labels' => array(
            'name' => 'Subscribers',
            'singular_name' => 'Subscriber',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'options-general.php',
        'supports' => array('title'),
        'capability_type' => 'post',
        'map_meta_cap' => true,
        'rewrite' => false,
        'query_var' => false,
        'show_in_rest' => false,
        'menu_icon' => 'dashicons-email',
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

