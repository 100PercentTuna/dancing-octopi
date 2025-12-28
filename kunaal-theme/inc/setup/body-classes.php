<?php
/**
 * Stable body classes for page-specific styling.
 *
 * Provides stable, predictable body classes that won't change with WordPress updates.
 * These classes should be used instead of WordPress-generated body classes for CSS scoping.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add stable body classes for key pages.
 *
 * @param string[] $classes Existing body classes.
 * @return string[] Modified body classes.
 */
function kunaal_add_stable_body_classes(array $classes): array {
    // Homepage
    if (is_front_page()) {
        $classes[] = 'kunaal-homepage';
    }

    // About page
    if (is_page('about') || is_page_template('page-about.php')) {
        $classes[] = 'kunaal-about-page';
    }

    // Contact page
    if (is_page('contact') || is_page_template('page-contact.php')) {
        $classes[] = 'kunaal-contact-page';
    }

    // Essay post type
    if (is_post_type_archive('essay') || is_singular('essay')) {
        $classes[] = 'kunaal-essay';
        if (is_post_type_archive('essay')) {
            $classes[] = 'kunaal-essay-archive';
        }
    }

    // Jotting post type
    if (is_post_type_archive('jotting') || is_singular('jotting')) {
        $classes[] = 'kunaal-jotting';
        if (is_post_type_archive('jotting')) {
            $classes[] = 'kunaal-jotting-archive';
        }
    }

    return $classes;
}
add_filter('body_class', 'kunaal_add_stable_body_classes');

