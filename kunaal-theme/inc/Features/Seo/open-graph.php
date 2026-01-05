<?php
/**
 * SEO Functions
 * 
 * Handles Open Graph meta tags, Twitter Cards, and other SEO-related functionality.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Open Graph Meta Tags for Social Sharing (LinkedIn, X/Twitter, Facebook)
 */
function kunaal_add_open_graph_tags(): void {
    if (kunaal_seo_is_yoast_active()) {
        return;
    }

    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    if (!is_singular(array('essay', 'jotting', 'post', 'page')) && !is_home() && !is_front_page() && !is_post_type_archive() && !is_tax('topic')) {
        return;
    }

    $title = kunaal_seo_get_title();
    $url = kunaal_seo_get_canonical_url();
    $site_name = get_bloginfo('name');
    $author_first = kunaal_mod('kunaal_author_first_name', 'Kunaal');
    $author_last = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
    $author_name = $author_first . ' ' . $author_last;
    
    // Get description
    $description = kunaal_seo_get_description();
    
    // Get image
    $image = kunaal_seo_get_share_image_url();
    
    // Twitter handle
    $twitter_handle = (string) kunaal_mod('kunaal_twitter_handle', '');
    $twitter_handle = ltrim(trim($twitter_handle), '@');
    $type = is_singular() ? 'article' : 'website';
    
    ?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="<?php echo esc_attr($type); ?>" />
    <meta property="og:title" content="<?php echo esc_attr($title); ?>" />
    <meta property="og:description" content="<?php echo esc_attr($description); ?>" />
    <meta property="og:url" content="<?php echo esc_url($url); ?>" />
    <meta property="og:site_name" content="<?php echo esc_attr($site_name); ?>" />
    <?php if ($image) : ?>
    <meta property="og:image" content="<?php echo esc_url($image); ?>" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <?php endif; ?>
    <?php if (is_singular()) : ?>
    <meta property="article:author" content="<?php echo esc_attr($author_name); ?>" />
    <meta property="article:published_time" content="<?php echo esc_attr(get_the_date('c', get_queried_object_id())); ?>" />
    <meta property="article:modified_time" content="<?php echo esc_attr(get_the_modified_date('c', get_queried_object_id())); ?>" />
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo esc_attr($title); ?>" />
    <meta name="twitter:description" content="<?php echo esc_attr($description); ?>" />
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

