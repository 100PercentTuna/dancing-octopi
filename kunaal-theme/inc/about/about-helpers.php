<?php
/**
 * About Page Helper Functions (V22)
 * 
 * Helper functions for the About page v22 template.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get hero photos for v22 About page
 * Returns array keyed by slot number 1..10 (stable slots, no reindex shifting)
 * 
 * @return array<int, string> Array keyed by slot number (1-10) => photo URL
 */
function kunaal_get_hero_photos_v22(): array {
    $photos = array();
    for ($i = 1; $i <= 10; $i++) {
        $photo_id = (int) kunaal_mod("kunaal_about_v22_hero_photo_{$i}", 0);
        if ($photo_id) {
            $photo_url = wp_get_attachment_image_url($photo_id, 'full');
            if ($photo_url) {
                $photos[$i] = $photo_url;
            }
        }
    }
    return $photos;
}

/**
 * Get numbers data for v22 About page
 * Returns array of number items (up to 8) plus infinity option
 */
function kunaal_get_numbers_v22(): array {
    $numbers = array();
    for ($i = 1; $i <= 8; $i++) {
        $value = kunaal_mod("kunaal_about_v22_number_{$i}_value", '');
        $label = kunaal_mod("kunaal_about_v22_number_{$i}_label", '');
        if (!empty($value) && !empty($label)) {
            $numbers[] = array(
                'value' => $value,
                'suffix' => kunaal_mod("kunaal_about_v22_number_{$i}_suffix", ''),
                'label' => $label,
            );
        }
    }
    
    // Add infinity if enabled
    if (kunaal_mod('kunaal_about_v22_numbers_infinity_show', true)) {
        $infinity_label = kunaal_mod('kunaal_about_v22_numbers_infinity_label', 'Rabbit holes');
        if (!empty($infinity_label)) {
            $numbers[] = array(
                'value' => 'infinity',
                'suffix' => '',
                'label' => $infinity_label,
            );
        }
    }
    
    return $numbers;
}

/**
 * Get categories for v22 About page
 * Returns array of category definitions (up to 12)
 */
function kunaal_get_categories_v22(): array {
    $categories = array();
    for ($i = 1; $i <= 12; $i++) {
        $name = kunaal_mod("kunaal_about_v22_category_{$i}_name", '');
        if (!empty($name)) {
            $slug = sanitize_title($name);
            $categories[$slug] = array(
                'name' => $name,
                'color' => kunaal_mod("kunaal_about_v22_category_{$i}_color", '#7D6B5D'),
            );
        }
    }
    return $categories;
}

/**
 * Get rabbit holes for v22 About page
 * Returns array of rabbit hole items (up to 200)
 */
function kunaal_get_rabbit_holes_v22(): array {
    $rabbit_holes = array();
    for ($i = 1; $i <= 200; $i++) {
        $text = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_text", '');
        if (!empty($text)) {
            $image_id = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_image", 0);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
            $category = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_category", '');
            $url = kunaal_mod("kunaal_about_v22_rabbit_hole_{$i}_url", '');
            
            $rabbit_holes[] = array(
                'image' => $image_url,
                'text' => $text,
                'category' => $category,
                'url' => $url,
            );
        }
    }
    return $rabbit_holes;
}

/**
 * Get panoramas for v22 About page
 * Returns array organized by position
 */
function kunaal_get_panoramas_v22(): array {
    $panoramas_by_position = array(
        'after_hero' => array(),
        'after_numbers' => array(),
        'after_rabbit_holes' => array(),
        'after_media' => array(),
        'after_map' => array(),
        'after_inspirations' => array(),
    );
    
    for ($i = 1; $i <= 10; $i++) {
        $position = kunaal_mod("kunaal_about_v22_panorama_{$i}_position", 'none');
        if ($position !== 'none' && isset($panoramas_by_position[$position])) {
            $image_id = kunaal_mod("kunaal_about_v22_panorama_{$i}_image", 0);
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                if ($image_url) {
                    $panoramas_by_position[$position][] = array(
                        'image' => $image_url,
                        'height' => kunaal_mod("kunaal_about_v22_panorama_{$i}_height", '140'),
                        'cut' => kunaal_mod("kunaal_about_v22_panorama_{$i}_cut", 'none'),
                        'bg' => kunaal_mod("kunaal_about_v22_panorama_{$i}_bg", 'default'),
                        'speed' => kunaal_mod("kunaal_about_v22_panorama_{$i}_speed", '2.0'),
                    );
                }
            }
        }
    }
    
    return $panoramas_by_position;
}

/**
 * Get books for v22 About page
 * Returns array of book items (up to 6)
 */
function kunaal_get_books_v22(): array {
    $books = array();
    for ($i = 1; $i <= 6; $i++) {
        $title = kunaal_mod("kunaal_about_v22_book_{$i}_title", '');
        if (!empty($title)) {
            $cover_id = kunaal_mod("kunaal_about_v22_book_{$i}_cover", 0);
            $cover_url = $cover_id ? wp_get_attachment_image_url($cover_id, 'medium') : '';
            
            $books[] = array(
                'cover' => $cover_url,
                'title' => $title,
                'author' => kunaal_mod("kunaal_about_v22_book_{$i}_author", ''),
                'url' => kunaal_mod("kunaal_about_v22_book_{$i}_url", ''),
            );
        }
    }
    return $books;
}

/**
 * Get digital media for v22 About page
 * Returns array of digital items (up to 6)
 */
function kunaal_get_digital_media_v22(): array {
    $digital = array();
    for ($i = 1; $i <= 6; $i++) {
        $title = kunaal_mod("kunaal_about_v22_digital_{$i}_title", '');
        if (!empty($title)) {
            $cover_id = kunaal_mod("kunaal_about_v22_digital_{$i}_cover", 0);
            $cover_url = $cover_id ? wp_get_attachment_image_url($cover_id, 'medium') : '';
            $link_type = kunaal_mod("kunaal_about_v22_digital_{$i}_link_type", 'spotify');
            
            $digital[] = array(
                'cover' => $cover_url,
                'title' => $title,
                'artist' => kunaal_mod("kunaal_about_v22_digital_{$i}_artist", ''),
                'link_type' => $link_type,
                'url' => kunaal_mod("kunaal_about_v22_digital_{$i}_url", ''),
            );
        }
    }
    return $digital;
}

/**
 * Get places data for v22 About page
 * Returns array with lived, visited, and current location ISO codes
 */
function kunaal_get_places_v22(): array {
    $lived_str = kunaal_mod('kunaal_about_v22_places_lived', '');
    $visited_str = kunaal_mod('kunaal_about_v22_places_visited', '');
    $current = kunaal_mod('kunaal_about_v22_places_current', '');
    
    $lived = array();
    if (!empty($lived_str)) {
        $lived = array_map('trim', array_map('strtoupper', explode(',', $lived_str)));
        $lived = array_filter($lived);
    }
    
    $visited = array();
    if (!empty($visited_str)) {
        $visited = array_map('trim', array_map('strtoupper', explode(',', $visited_str)));
        $visited = array_filter($visited);
    }
    
    // Ensure current is an array (even if empty)
    $current_array = array();
    if (!empty($current)) {
        $current_trimmed = strtoupper(trim($current));
        if (!empty($current_trimmed)) {
            $current_array = array($current_trimmed);
        }
    }
    
    return array(
        'lived' => $lived,
        'visited' => $visited,
        'current' => $current_array,
    );
}

/**
 * Get inspirations for v22 About page
 * Returns array of inspiration items (up to 10)
 */
function kunaal_get_inspirations_v22(): array {
    $inspirations = array();
    for ($i = 1; $i <= 10; $i++) {
        $name = kunaal_mod("kunaal_about_v22_inspiration_{$i}_name", '');
        if (!empty($name)) {
            $photo_id = kunaal_mod("kunaal_about_v22_inspiration_{$i}_photo", 0);
            $photo_url = $photo_id ? wp_get_attachment_image_url($photo_id, 'medium') : '';
            
            $inspirations[] = array(
                'photo' => $photo_url,
                'name' => $name,
                'role' => kunaal_mod("kunaal_about_v22_inspiration_{$i}_role", ''),
                'note' => kunaal_mod("kunaal_about_v22_inspiration_{$i}_note", ''),
                'url' => kunaal_mod("kunaal_about_v22_inspiration_{$i}_url", ''),
            );
        }
    }
    return $inspirations;
}

