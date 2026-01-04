<?php
/**
 * About Page Customizer - Section Helpers
 * 
 * Split from about-customizer-v22.php to reduce function length and cognitive complexity.
 * Each section registration is now in its own function.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Hero Section
 */
function kunaal_register_about_hero_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_hero', array(
        'title' => 'Hero Section',
        'panel' => 'kunaal_about_panel',
        'priority' => 10,
        'description' => '10-photo collage grid with intro text overlay.',
    ));
    
    // Hero Photos (10 photos)
    for ($i = 1; $i <= 10; $i++) {
        $wp_customize->add_setting("kunaal_about_hero_photo_{$i}", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_hero_photo_{$i}", array(
                'label' => "Photo {$i}" . ($i === 1 ? ' (Primary)' : ''),
                'description' => $i === 1 ? 'Main hero photo' : 'Additional collage photo',
                'section' => 'kunaal_about_hero',
                'mime_type' => 'image',
            )
        ));
    }
    
    // Hero Intro Text
    $wp_customize->add_setting('kunaal_about_hero_intro', array(
        'default' => 'A curiosity about humans and human collectives — how we organize, what we believe, why we do what we do.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_intro', array(
        'label' => 'Intro Text',
        'description' => 'Main introduction text displayed in hero section',
        'section' => 'kunaal_about_hero',
        'type' => 'textarea',
    ));
    
    // Handwritten Note
    $wp_customize->add_setting('kunaal_about_hero_hand_note', array(
        'default' => 'slightly alarming?',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_hand_note', array(
        'label' => 'Handwritten Note',
        'description' => 'Small personal note in handwritten style (appears after intro text)',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Location
    $wp_customize->add_setting('kunaal_about_hero_location', array(
        'default' => 'Singapore',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_location', array(
        'label' => 'Location',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Listening
    $wp_customize->add_setting('kunaal_about_hero_listening', array(
        'default' => 'Ezra Klein Show',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_listening', array(
        'label' => 'Currently Listening',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Reading
    $wp_customize->add_setting('kunaal_about_hero_reading', array(
        'default' => 'Master of the Senate',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_reading', array(
        'label' => 'Currently Reading',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Custom Meta Row 1
    $wp_customize->add_setting('kunaal_about_hero_custom1_show', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_hero_custom1_show', array(
        'label' => 'Show Custom Meta Row 1',
        'section' => 'kunaal_about_hero',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_hero_custom1_label', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_custom1_label', array(
        'label' => 'Custom 1: Label',
        'description' => 'Left side label (e.g., "Watching", "Playing")',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_hero_custom1_value', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_custom1_value', array(
        'label' => 'Custom 1: Value',
        'description' => 'Right side value (e.g., "The Bear", "Chess")',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    // Custom Meta Row 2
    $wp_customize->add_setting('kunaal_about_hero_custom2_show', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_hero_custom2_show', array(
        'label' => 'Show Custom Meta Row 2',
        'section' => 'kunaal_about_hero',
        'type' => 'checkbox',
    ));
    
    $wp_customize->add_setting('kunaal_about_hero_custom2_label', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_custom2_label', array(
        'label' => 'Custom 2: Label',
        'description' => 'Left side label (e.g., "Cooking", "Learning")',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('kunaal_about_hero_custom2_value', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_hero_custom2_value', array(
        'label' => 'Custom 2: Value',
        'description' => 'Right side value (e.g., "Italian food", "Spanish")',
        'section' => 'kunaal_about_hero',
        'type' => 'text',
    ));
}

/**
 * Register Numbers Section
 */
function kunaal_register_about_numbers_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_numbers', array(
        'title' => 'By the Numbers',
        'panel' => 'kunaal_about_panel',
        'priority' => 20,
    ));
    
    // Show Numbers Section
    $wp_customize->add_setting('kunaal_about_numbers_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_numbers_show', array(
        'label' => 'Show Numbers Section',
        'section' => 'kunaal_about_numbers',
        'type' => 'checkbox',
    ));
    
    // Numbers (up to 8)
    for ($i = 1; $i <= 8; $i++) {
        // Number Value
        $wp_customize->add_setting("kunaal_about_number_{$i}_value", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_number_{$i}_value", array(
            'label' => "Number {$i}: Value",
            'description' => 'Number or text (e.g., "4705", "312", "35", "∞")',
            'section' => 'kunaal_about_numbers',
            'type' => 'text',
        ));
        
        // Number Suffix
        $wp_customize->add_setting("kunaal_about_number_{$i}_suffix", array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_number_{$i}_suffix", array(
            'label' => "Number {$i}: Suffix",
            'description' => 'Optional suffix (e.g., "+", "%", "hrs", " things"). Spaces are preserved.',
            'section' => 'kunaal_about_numbers',
            'type' => 'text',
        ));
        
        // Suffix as Subscript
        $wp_customize->add_setting("kunaal_about_number_{$i}_suffix_subscript", array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control("kunaal_about_number_{$i}_suffix_subscript", array(
            'label' => "Number {$i}: Show suffix as subscript",
            'description' => 'Display suffix as smaller subscript text (ideal for words like "things", "cups")',
            'section' => 'kunaal_about_numbers',
            'type' => 'checkbox',
        ));
        
        // Number Label
        $wp_customize->add_setting("kunaal_about_number_{$i}_label", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_number_{$i}_label", array(
            'label' => "Number {$i}: Label",
            'description' => 'Text below the number (e.g., "Iced coffees", "Books read")',
            'section' => 'kunaal_about_numbers',
            'type' => 'text',
        ));
    }
    
    // Infinity Toggle
    $wp_customize->add_setting('kunaal_about_numbers_infinity_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_numbers_infinity_show', array(
        'label' => 'Show Infinity Symbol',
        'description' => 'Display infinity symbol (∞) as one of the numbers',
        'section' => 'kunaal_about_numbers',
        'type' => 'checkbox',
    ));
    
    // Infinity Label
    $wp_customize->add_setting('kunaal_about_numbers_infinity_label', array(
        'default' => 'Rabbit holes',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_numbers_infinity_label', array(
        'label' => 'Infinity Label',
        'description' => 'Text below infinity symbol',
        'section' => 'kunaal_about_numbers',
        'type' => 'text',
    ));
}

/**
 * Register Categories Section
 */
function kunaal_register_about_categories_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_categories', array(
        'title' => 'Rabbit Holes - Categories',
        'panel' => 'kunaal_about_panel',
        'priority' => 25,
        'description' => 'Define up to 12 categories for organizing rabbit holes. Each category has a name and color.',
    ));
    
    // Categories (up to 12)
    for ($i = 1; $i <= 12; $i++) {
        // Category Name
        $wp_customize->add_setting("kunaal_about_category_{$i}_name", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_category_{$i}_name", array(
            'label' => "Category {$i}: Name",
            'description' => 'Category name (e.g., "Media", "Knowledge", "Craft")',
            'section' => 'kunaal_about_categories',
            'type' => 'text',
        ));
        
        // Category Color
        $wp_customize->add_setting("kunaal_about_category_{$i}_color", array(
            'default' => '#7D6B5D',
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize,
            "kunaal_about_category_{$i}_color", array(
                'label' => "Category {$i}: Color",
                'section' => 'kunaal_about_categories',
            )
        ));
    }
}

/**
 * Register Rabbit Holes Section
 */
function kunaal_register_about_rabbit_holes_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_rabbit_holes', array(
        'title' => 'Rabbit Holes',
        'panel' => 'kunaal_about_panel',
        'priority' => 30,
        'description' => 'Add up to 200 rabbit holes. Each has an image, text, and category.',
    ));
    
    // Show Rabbit Holes Section
    $wp_customize->add_setting('kunaal_about_rabbit_holes_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_rabbit_holes_show', array(
        'label' => 'Show Rabbit Holes Section',
        'section' => 'kunaal_about_rabbit_holes',
        'type' => 'checkbox',
    ));
    
    // Section Title
    $wp_customize->add_setting('kunaal_about_rabbit_holes_title', array(
        'default' => "Things I can't stop exploring",
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_rabbit_holes_title', array(
        'label' => KUNAAL_CUSTOMIZER_SECTION_TITLE_LABEL,
        'section' => 'kunaal_about_rabbit_holes',
        'type' => 'text',
    ));
    
    // Section Label (small text above title)
    $wp_customize->add_setting('kunaal_about_rabbit_holes_label', array(
        'default' => 'Rabbit Holes',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_rabbit_holes_label', array(
        'label' => 'Section Label',
        'description' => 'Small text above the section title',
        'section' => 'kunaal_about_rabbit_holes',
        'type' => 'text',
    ));
    
    // Show Background Word
    $wp_customize->add_setting('kunaal_about_rabbit_holes_bgword_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_rabbit_holes_bgword_show', array(
        'label' => 'Show Background Word',
        'description' => 'Display the large faint word behind the section',
        'section' => 'kunaal_about_rabbit_holes',
        'type' => 'checkbox',
    ));
    
    // Background Word Text
    $wp_customize->add_setting('kunaal_about_rabbit_holes_bgword_text', array(
        'default' => 'Rabbit Holes',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_rabbit_holes_bgword_text', array(
        'label' => 'Background Word',
        'description' => 'Large faint text displayed behind the section',
        'section' => 'kunaal_about_rabbit_holes',
        'type' => 'text',
    ));
    
    // Show Legend
    $wp_customize->add_setting('kunaal_about_rabbit_holes_legend_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_rabbit_holes_legend_show', array(
        'label' => 'Show Category Legend',
        'description' => 'Display the color-coded category legend below the capsules',
        'section' => 'kunaal_about_rabbit_holes',
        'type' => 'checkbox',
    ));
    
    // Rabbit Holes (up to 200)
    for ($i = 1; $i <= 200; $i++) {
        // Visible Toggle
        $wp_customize->add_setting("kunaal_about_rabbit_hole_{$i}_visible", array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control("kunaal_about_rabbit_hole_{$i}_visible", array(
            'label' => "Rabbit Hole {$i}: Show",
            'description' => 'Uncheck to hide this rabbit hole without deleting it',
            'section' => 'kunaal_about_rabbit_holes',
            'type' => 'checkbox',
        ));
        
        // Image
        $wp_customize->add_setting("kunaal_about_rabbit_hole_{$i}_image", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_rabbit_hole_{$i}_image", array(
                'label' => "Rabbit Hole {$i}: Image",
                'description' => 'Square image (will be displayed as circle)',
                'section' => 'kunaal_about_rabbit_holes',
                'mime_type' => 'image',
            )
        ));
        
        // Text
        $wp_customize->add_setting("kunaal_about_rabbit_hole_{$i}_text", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_rabbit_hole_{$i}_text", array(
            'label' => "Rabbit Hole {$i}: Text",
            'description' => 'Name of the rabbit hole (e.g., "YouTube Essays", "Specialty Coffee")',
            'section' => 'kunaal_about_rabbit_holes',
            'type' => 'text',
        ));
        
        // Category (dropdown - populated from categories section)
        $wp_customize->add_setting("kunaal_about_rabbit_hole_{$i}_category", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_rabbit_hole_{$i}_category", array(
            'label' => "Rabbit Hole {$i}: Category",
            'description' => 'Select category (must be defined in Categories section first)',
            'section' => 'kunaal_about_rabbit_holes',
            'type' => 'select',
            'choices' => kunaal_get_category_choices(),
        ));
        
        // URL (optional link)
        $wp_customize->add_setting("kunaal_about_rabbit_hole_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_about_rabbit_hole_{$i}_url", array(
            'label' => "Rabbit Hole {$i}: URL (Optional)",
            'description' => 'Optional link for this rabbit hole',
            'section' => 'kunaal_about_rabbit_holes',
            'type' => 'url',
        ));
    }
}

/**
 * Register Panoramas Section
 */
function kunaal_register_about_panoramas_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_panoramas', array(
        'title' => 'Panorama Dividers',
        'panel' => 'kunaal_about_panel',
        'priority' => 35,
        'description' => 'Full-bleed parallax images that create visual breaks between sections. Assign images to positions.',
    ));
    
    // Panoramas (up to 10)
    for ($i = 1; $i <= 10; $i++) {
        // Image
        $wp_customize->add_setting("kunaal_about_panorama_{$i}_image", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_panorama_{$i}_image", array(
                'label' => "Panorama {$i}: Image",
                'description' => 'Wide landscape image for parallax effect',
                'section' => 'kunaal_about_panoramas',
                'mime_type' => 'image',
            )
        ));
        
        // Position
        $wp_customize->add_setting("kunaal_about_panorama_{$i}_position", array(
            'default' => 'none',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_panorama_{$i}_position", array(
            'label' => "Panorama {$i}: Position",
            'description' => 'Where to display this panorama',
            'section' => 'kunaal_about_panoramas',
            'type' => 'select',
            'choices' => array(
                'none' => 'Not displayed',
                'after_hero' => 'After Hero Section',
                'after_numbers' => 'After Numbers Section',
                'after_rabbit_holes' => 'After Rabbit Holes Section',
                'after_media' => 'After Media Section',
                'after_map' => 'After Map Section',
                'after_inspirations' => 'After Inspirations Section',
            ),
        ));
        
        // Height (20px to 200px, default 140px)
        $wp_customize->add_setting("kunaal_about_panorama_{$i}_height", array(
            'default' => '140',
            'sanitize_callback' => 'absint',
        ));
        $height_choices = array();
        for ($h = 20; $h <= 200; $h += 10) {
            $height_choices[$h] = $h . 'px';
        }
        $wp_customize->add_control("kunaal_about_panorama_{$i}_height", array(
            'label' => "Panorama {$i}: Height",
            'section' => 'kunaal_about_panoramas',
            'type' => 'select',
            'choices' => $height_choices,
        ));
        
        // Cut Direction (slant)
        $wp_customize->add_setting("kunaal_about_panorama_{$i}_cut", array(
            'default' => 'none',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_panorama_{$i}_cut", array(
            'label' => "Panorama {$i}: Slant Direction",
            'section' => 'kunaal_about_panoramas',
            'type' => 'select',
            'choices' => array(
                'none' => 'None (straight)',
                'left' => 'Left slant',
                'right' => 'Right slant',
            ),
        ));
        
        // Background Color
        $wp_customize->add_setting("kunaal_about_panorama_{$i}_bg", array(
            'default' => 'default',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_panorama_{$i}_bg", array(
            'label' => "Panorama {$i}: Background Color",
            'section' => 'kunaal_about_panoramas',
            'type' => 'select',
            'choices' => array(
                'default' => 'Default',
                'warm' => 'Warm',
            ),
        ));
        
        // Parallax Speed
        $wp_customize->add_setting("kunaal_about_panorama_{$i}_speed", array(
            'default' => '2.0',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_panorama_{$i}_speed", array(
            'label' => "Panorama {$i}: Parallax Speed",
            'description' => 'Parallax scroll speed (e.g., 1.8, 2.0, 2.2)',
            'section' => 'kunaal_about_panoramas',
            'type' => 'text',
        ));
    }
}

/**
 * Register Books Section
 */
function kunaal_register_about_books_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_books', array(
        'title' => 'Media - Books (On the Nightstand)',
        'panel' => 'kunaal_about_panel',
        'priority' => 40,
        'description' => 'Add up to 6 books currently reading.',
    ));
    
    // Show Books Section
    $wp_customize->add_setting('kunaal_about_books_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_books_show', array(
        'label' => 'Show Books Section',
        'section' => 'kunaal_about_books',
        'type' => 'checkbox',
    ));
    
    // Books (up to 6)
    for ($i = 1; $i <= 6; $i++) {
        // Visible Toggle
        $wp_customize->add_setting("kunaal_about_book_{$i}_visible", array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control("kunaal_about_book_{$i}_visible", array(
            'label' => "Book {$i}: Show",
            'description' => 'Uncheck to hide this book without deleting it',
            'section' => 'kunaal_about_books',
            'type' => 'checkbox',
        ));
        
        // Cover Image
        $wp_customize->add_setting("kunaal_about_book_{$i}_cover", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_book_{$i}_cover", array(
                'label' => "Book {$i}: Cover Image",
                'description' => 'Book cover image (2:3 aspect ratio recommended)',
                'section' => 'kunaal_about_books',
                'mime_type' => 'image',
            )
        ));
        
        // Title
        $wp_customize->add_setting("kunaal_about_book_{$i}_title", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_book_{$i}_title", array(
            'label' => "Book {$i}: Title",
            'section' => 'kunaal_about_books',
            'type' => 'text',
        ));
        
        // Author
        $wp_customize->add_setting("kunaal_about_book_{$i}_author", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_book_{$i}_author", array(
            'label' => "Book {$i}: Author",
            'section' => 'kunaal_about_books',
            'type' => 'text',
        ));
        
        // Link URL
        $wp_customize->add_setting("kunaal_about_book_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_about_book_{$i}_url", array(
            'label' => "Book {$i}: Link URL (optional)",
            'description' => 'Link to book page (Amazon, Goodreads, etc.)',
            'section' => 'kunaal_about_books',
            'type' => 'url',
        ));
    }
}

/**
 * Register Digital Content Section
 */
function kunaal_register_about_digital_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_digital', array(
        'title' => 'Media - Digital Content (On Repeat)',
        'panel' => 'kunaal_about_panel',
        'priority' => 45,
        'description' => 'Add up to 6 podcasts, albums, YouTube channels, or songs.',
    ));
    
    // Show Digital Section
    $wp_customize->add_setting('kunaal_about_digital_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_digital_show', array(
        'label' => 'Show Digital Content Section',
        'section' => 'kunaal_about_digital',
        'type' => 'checkbox',
    ));
    
    // Digital Items (up to 6)
    for ($i = 1; $i <= 6; $i++) {
        // Visible Toggle
        $wp_customize->add_setting("kunaal_about_digital_{$i}_visible", array(
            'default' => true,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control("kunaal_about_digital_{$i}_visible", array(
            'label' => "Digital {$i}: Show",
            'description' => 'Uncheck to hide this item without deleting it',
            'section' => 'kunaal_about_digital',
            'type' => 'checkbox',
        ));
        
        // Cover Image
        $wp_customize->add_setting("kunaal_about_digital_{$i}_cover", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_digital_{$i}_cover", array(
                'label' => "Digital {$i}: Cover Image",
                'description' => 'Album/podcast cover or thumbnail (square recommended)',
                'section' => 'kunaal_about_digital',
                'mime_type' => 'image',
            )
        ));
        
        // Title/Name
        $wp_customize->add_setting("kunaal_about_digital_{$i}_title", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_digital_{$i}_title", array(
            'label' => "Digital {$i}: Title/Name",
            'section' => 'kunaal_about_digital',
            'type' => 'text',
        ));
        
        // Artist/Creator
        $wp_customize->add_setting("kunaal_about_digital_{$i}_artist", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_digital_{$i}_artist", array(
            'label' => "Digital {$i}: Artist/Creator",
            'section' => 'kunaal_about_digital',
            'type' => 'text',
        ));
        
        // Link Type
        $wp_customize->add_setting("kunaal_about_digital_{$i}_link_type", array(
            'default' => 'spotify',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_digital_{$i}_link_type", array(
            'label' => "Digital {$i}: Link Type",
            'section' => 'kunaal_about_digital',
            'type' => 'select',
            'choices' => array(
                'spotify' => 'Spotify',
                'youtube' => 'YouTube',
                'apple' => 'Apple Podcasts',
                'other' => 'Other',
            ),
        ));
        
        // Link URL
        $wp_customize->add_setting("kunaal_about_digital_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_about_digital_{$i}_url", array(
            'label' => "Digital {$i}: Link URL",
            'section' => 'kunaal_about_digital',
            'type' => 'url',
        ));
    }
}

/**
 * Register Places Section
 */
function kunaal_register_about_places_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_places', array(
        'title' => 'Places',
        'panel' => 'kunaal_about_panel',
        'priority' => 50,
    ));
    
    // Show Places Section
    $wp_customize->add_setting('kunaal_about_places_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_places_show', array(
        'label' => 'Show Places Section',
        'section' => 'kunaal_about_places',
        'type' => 'checkbox',
    ));
    
    // Section Title
    $wp_customize->add_setting('kunaal_about_places_title', array(
        'default' => "Where I've been",
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_places_title', array(
        'label' => KUNAAL_CUSTOMIZER_SECTION_TITLE_LABEL,
        'section' => 'kunaal_about_places',
        'type' => 'text',
    ));
    
    // Section Label (small text above title)
    $wp_customize->add_setting('kunaal_about_places_label', array(
        'default' => 'Places',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_places_label', array(
        'label' => 'Section Label',
        'description' => 'Small text above the section title',
        'section' => 'kunaal_about_places',
        'type' => 'text',
    ));
    
    // Show Background Word
    $wp_customize->add_setting('kunaal_about_places_bgword_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_places_bgword_show', array(
        'label' => 'Show Background Word',
        'description' => 'Display the large faint word behind the section',
        'section' => 'kunaal_about_places',
        'type' => 'checkbox',
    ));
    
    // Background Word Text
    $wp_customize->add_setting('kunaal_about_places_bgword_text', array(
        'default' => 'Wanderlust',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_places_bgword_text', array(
        'label' => 'Background Word',
        'description' => 'Large faint text displayed behind the section',
        'section' => 'kunaal_about_places',
        'type' => 'text',
    ));
    
    // Countries Lived
    $wp_customize->add_setting('kunaal_about_places_lived', array(
        'default' => 'USA,IND,GBR',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_places_lived', array(
        'label' => 'Countries Lived In',
        'description' => 'Comma-separated ISO codes (e.g., USA,IND,GBR)',
        'section' => 'kunaal_about_places',
        'type' => 'text',
    ));
    
    // Countries Visited
    $wp_customize->add_setting('kunaal_about_places_visited', array(
        'default' => 'THA,PHL,CHE,CAN,MYS,MDV,BRA,MEX,ZAF',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_places_visited', array(
        'label' => 'Countries Visited',
        'description' => 'Comma-separated ISO codes',
        'section' => 'kunaal_about_places',
        'type' => 'text',
    ));
    
    // Current Location
    $wp_customize->add_setting('kunaal_about_places_current', array(
        'default' => 'SGP',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_places_current', array(
        'label' => 'Current Location',
        'description' => 'Single ISO code (e.g., SGP)',
        'section' => 'kunaal_about_places',
        'type' => 'text',
    ));
}

/**
 * Register Inspirations Section
 */
function kunaal_register_about_inspirations_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_inspirations', array(
        'title' => 'Inspirations',
        'panel' => 'kunaal_about_panel',
        'priority' => 60,
        'description' => 'Add up to 10 people who inspire you.',
    ));
    
    // Show Inspirations Section
    $wp_customize->add_setting('kunaal_about_inspirations_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_show', array(
        'label' => 'Show Inspirations Section',
        'section' => 'kunaal_about_inspirations',
        'type' => 'checkbox',
    ));
    
    // Section Title
    $wp_customize->add_setting('kunaal_about_inspirations_title', array(
        'default' => "People I learn from",
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_title', array(
        'label' => KUNAAL_CUSTOMIZER_SECTION_TITLE_LABEL,
        'section' => 'kunaal_about_inspirations',
        'type' => 'text',
    ));
    
    // Inspirations (up to 10)
    for ($i = 1; $i <= 10; $i++) {
        // Photo
        $wp_customize->add_setting("kunaal_about_inspiration_{$i}_photo", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_inspiration_{$i}_photo", array(
                'label' => "Person {$i}: Photo",
                'description' => 'Square photo (will be displayed as circle)',
                'section' => 'kunaal_about_inspirations',
                'mime_type' => 'image',
            )
        ));
        
        // Name
        $wp_customize->add_setting("kunaal_about_inspiration_{$i}_name", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_inspiration_{$i}_name", array(
            'label' => "Person {$i}: Name",
            'section' => 'kunaal_about_inspirations',
            'type' => 'text',
        ));
        
        // Role
        $wp_customize->add_setting("kunaal_about_inspiration_{$i}_role", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_inspiration_{$i}_role", array(
            'label' => "Person {$i}: Role",
            'description' => 'Their role or title (e.g., "Comedian", "Biographer")',
            'section' => 'kunaal_about_inspirations',
            'type' => 'text',
        ));
        
        // Inspiration Note (1-3 words)
        $wp_customize->add_setting("kunaal_about_inspiration_{$i}_note", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_about_inspiration_{$i}_note", array(
            'label' => "Person {$i}: Inspiration Note",
            'description' => '1-3 words about what inspires you about them (optional)',
            'section' => 'kunaal_about_inspirations',
            'type' => 'text',
        ));
        
        // Link URL
        $wp_customize->add_setting("kunaal_about_inspiration_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_about_inspiration_{$i}_url", array(
            'label' => "Person {$i}: Link URL (optional)",
            'description' => 'Link to their website or work',
            'section' => 'kunaal_about_inspirations',
            'type' => 'url',
        ));
    }
}

/**
 * Register Say Hello Section
 */
function kunaal_register_about_say_hello_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_about_say_hello', array(
        'title' => 'Say Hello',
        'panel' => 'kunaal_about_panel',
        'priority' => 70,
        'description' => 'Contact section. Uses email and social links from Author Info settings.',
    ));
    
    // Show Say Hello Section
    $wp_customize->add_setting('kunaal_about_say_hello_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_say_hello_show', array(
        'label' => 'Show Say Hello Section',
        'section' => 'kunaal_about_say_hello',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_say_hello_label', array(
        'default' => 'Say Hello',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_say_hello_label', array(
        'label' => 'Section Label',
        'description' => 'Small text above the title (e.g., "Say Hello", "Get in Touch", "Best reached in person.")',
        'section' => 'kunaal_about_say_hello',
        'type' => 'text',
    ));
    
    // Label Font Size
    $wp_customize->add_setting('kunaal_about_say_hello_label_size', array(
        'default' => 'auto',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_say_hello_label_size', array(
        'label' => 'Label Font Size',
        'description' => 'Auto adjusts based on text length',
        'section' => 'kunaal_about_say_hello',
        'type' => 'select',
        'choices' => array(
            'auto' => 'Auto (adjusts to text length)',
            'small' => 'Small (10px)',
            'medium' => 'Medium (12px)',
            'large' => 'Large (14px)',
        ),
    ));
    
    // Show Section Title
    $wp_customize->add_setting('kunaal_about_say_hello_title_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_say_hello_title_show', array(
        'label' => 'Show Section Title',
        'description' => 'Toggle to hide the main heading',
        'section' => 'kunaal_about_say_hello',
        'type' => 'checkbox',
    ));

    // Section Title
    $wp_customize->add_setting('kunaal_about_say_hello_title', array(
        'default' => "Let's connect",
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_say_hello_title', array(
        'label' => KUNAAL_CUSTOMIZER_SECTION_TITLE_LABEL,
        'description' => 'Main heading (e.g., "Let\'s connect", "Leave a note...", "Drop me a line")',
        'section' => 'kunaal_about_say_hello',
        'type' => 'text',
    ));
    
    // Optional Description
    $wp_customize->add_setting('kunaal_about_say_hello_description', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_about_say_hello_description', array(
        'label' => 'Description (Optional)',
        'description' => 'Additional text below the title (e.g., "I\'d love to hear from you")',
        'section' => 'kunaal_about_say_hello',
        'type' => 'textarea',
    ));
}

