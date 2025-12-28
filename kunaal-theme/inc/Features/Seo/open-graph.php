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
    $author_first = kunaal_mod('kunaal_author_first_name', 'Kunaal');
    $author_last = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
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
    $twitter_handle = kunaal_mod('kunaal_twitter_handle', '');
    
    ?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="article" />
    <meta property="og:title" content="<?php echo esc_attr($title); ?>" />
    <meta property="og:description" content="<?php echo esc_attr($description); ?>" />
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

