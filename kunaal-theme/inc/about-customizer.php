<?php
/**
 * About Page Customizer - NO JSON VERSION
 * Individual fields for all data per spec 11-ADMIN-CUSTOMIZER.md
 *
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register About Page Customizer Panel and Sections with NO JSON fields
 */
function kunaal_about_customizer_v2($wp_customize) {
    
    // ============================
    // PANEL: About Page
    // ============================
    $wp_customize->add_panel('kunaal_about_panel', array(
        'title' => 'About Page',
        'priority' => 50,
        'description' => 'Configure your About page sections. NO JSON required - individual fields for everything.',
    ));
    
    // ============================
    // SECTION: Hero
    // ============================
    $wp_customize->add_section('kunaal_about_hero_v2', array(
        'title' => 'Hero Section',
        'panel' => 'kunaal_about_panel',
        'priority' => 10,
    ));
    
    // Show Hero
    $wp_customize->add_setting('kunaal_about_hero_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_hero_show', array(
        'label' => 'Show Hero Section',
        'section' => 'kunaal_about_hero_v2',
        'type' => 'checkbox',
    ));
    
    // Name (first name used from author settings)
    $wp_customize->add_setting('kunaal_about_hero_annotation', array(
        'default' => 'still figuring it out',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_hero_annotation', array(
        'label' => 'Handwritten Annotation',
        'description' => 'Small personal note in handwritten style (max 40 chars)',
        'section' => 'kunaal_about_hero_v2',
        'type' => 'text',
    ));
    
    // Photos 1-4 using media control
    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("kunaal_about_hero_photo_{$i}", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_about_hero_photo_{$i}", array(
                'label' => "Photo {$i}" . ($i === 1 ? ' (Primary - required)' : ' (optional)'),
                'description' => $i === 1 ? 'Main hero photo' : 'Additional collage photo',
                'section' => 'kunaal_about_hero_v2',
                'mime_type' => 'image',
            )
        ));
    }
    
    // ============================
    // SECTION: Bio
    // ============================
    $wp_customize->add_section('kunaal_about_bio_v2', array(
        'title' => 'Bio Section',
        'panel' => 'kunaal_about_panel',
        'priority' => 20,
        'description' => 'Bio content comes from the page editor. Configure pull quote here.',
    ));
    
    // Show Bio
    $wp_customize->add_setting('kunaal_about_bio_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_bio_show', array(
        'label' => 'Show Bio Section',
        'section' => 'kunaal_about_bio_v2',
        'type' => 'checkbox',
    ));
    
    // Bio Year
    $wp_customize->add_setting('kunaal_about_bio_year', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_bio_year', array(
        'label' => 'Established Year',
        'description' => 'Optional year for the gallery label (e.g., "1988")',
        'section' => 'kunaal_about_bio_v2',
        'type' => 'text',
    ));
    
    // Show Pull Quote
    $wp_customize->add_setting('kunaal_about_pullquote_show', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_pullquote_show', array(
        'label' => 'Show Pull Quote',
        'section' => 'kunaal_about_bio_v2',
        'type' => 'checkbox',
    ));
    
    // Pull Quote Text
    $wp_customize->add_setting('kunaal_about_pullquote_text', array(
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('kunaal_about_pullquote_text', array(
        'label' => 'Pull Quote Text',
        'section' => 'kunaal_about_bio_v2',
        'type' => 'textarea',
    ));
    
    // Pull Quote Attribution
    $wp_customize->add_setting('kunaal_about_pullquote_attr', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_pullquote_attr', array(
        'label' => 'Pull Quote Attribution',
        'section' => 'kunaal_about_bio_v2',
        'type' => 'text',
    ));
    
    // ============================
    // SECTION: Bookshelf
    // ============================
    $wp_customize->add_section('kunaal_about_books_v2', array(
        'title' => 'Bookshelf',
        'panel' => 'kunaal_about_panel',
        'priority' => 30,
    ));
    
    // Show Bookshelf
    $wp_customize->add_setting('kunaal_about_books_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_books_show', array(
        'label' => 'Show Bookshelf',
        'section' => 'kunaal_about_books_v2',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_books_label', array(
        'default' => 'Currently Reading',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_books_label', array(
        'label' => 'Section Label',
        'section' => 'kunaal_about_books_v2',
        'type' => 'text',
    ));
    
    // Books 1-8 (NO JSON - individual fields)
    for ($i = 1; $i <= 8; $i++) {
        // Cover image
        $wp_customize->add_setting("kunaal_book_{$i}_cover", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_book_{$i}_cover", array(
                'label' => "Book {$i}: Cover Image",
                'section' => 'kunaal_about_books_v2',
                'mime_type' => 'image',
            )
        ));
        
        // Title
        $wp_customize->add_setting("kunaal_book_{$i}_title", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_book_{$i}_title", array(
            'label' => "Book {$i}: Title",
            'section' => 'kunaal_about_books_v2',
            'type' => 'text',
        ));
        
        // Author
        $wp_customize->add_setting("kunaal_book_{$i}_author", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_book_{$i}_author", array(
            'label' => "Book {$i}: Author",
            'section' => 'kunaal_about_books_v2',
            'type' => 'text',
        ));
        
        // Link (optional)
        $wp_customize->add_setting("kunaal_book_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_book_{$i}_url", array(
            'label' => "Book {$i}: Link (optional)",
            'section' => 'kunaal_about_books_v2',
            'type' => 'url',
        ));
    }
    
    // ============================
    // SECTION: World Map
    // ============================
    $wp_customize->add_section('kunaal_about_map_v2', array(
        'title' => 'World Map',
        'panel' => 'kunaal_about_panel',
        'priority' => 40,
    ));
    
    // Show Map
    $wp_customize->add_setting('kunaal_about_map_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_map_show', array(
        'label' => 'Show Map Section',
        'section' => 'kunaal_about_map_v2',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_map_label', array(
        'default' => "Places I've Called Home",
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_label', array(
        'label' => 'Section Label',
        'section' => 'kunaal_about_map_v2',
        'type' => 'text',
    ));
    
    // Map Introduction
    $wp_customize->add_setting('kunaal_about_map_intro_v2', array(
        'default' => 'The places that have shaped who I am.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_map_intro_v2', array(
        'label' => 'Map Introduction',
        'description' => 'Handwritten-style intro above the map',
        'section' => 'kunaal_about_map_v2',
        'type' => 'text',
    ));
    
    // Countries Visited (comma-separated ISO - this is OK per spec)
    $wp_customize->add_setting('kunaal_map_visited', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_map_visited', array(
        'label' => 'Countries Visited',
        'description' => 'Comma-separated ISO codes (e.g., US, GB, JP, FR, DE)',
        'section' => 'kunaal_about_map_v2',
        'type' => 'text',
    ));
    
    // Countries Lived (comma-separated ISO)
    $wp_customize->add_setting('kunaal_map_lived', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_map_lived', array(
        'label' => 'Countries Lived In',
        'description' => 'Comma-separated ISO codes',
        'section' => 'kunaal_about_map_v2',
        'type' => 'text',
    ));
    
    // Current Location
    $wp_customize->add_setting('kunaal_map_current', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_map_current', array(
        'label' => 'Current Location',
        'description' => 'Single ISO code (e.g., IN) - highlighted with terracotta',
        'section' => 'kunaal_about_map_v2',
        'type' => 'text',
    ));
    
    // Country Stories 1-10 (NO JSON - individual fields)
    for ($i = 1; $i <= 10; $i++) {
        $wp_customize->add_setting("kunaal_map_story_{$i}_country", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_map_story_{$i}_country", array(
            'label' => "Story {$i}: Country Code",
            'description' => 'ISO code (e.g., US, IN, JP)',
            'section' => 'kunaal_about_map_v2',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_map_story_{$i}_years", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_map_story_{$i}_years", array(
            'label' => "Story {$i}: Years",
            'description' => 'e.g., "2015-2020" or "Childhood"',
            'section' => 'kunaal_about_map_v2',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_map_story_{$i}_text", array(
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control("kunaal_map_story_{$i}_text", array(
            'label' => "Story {$i}: Story",
            'description' => 'Brief note (max 200 chars)',
            'section' => 'kunaal_about_map_v2',
            'type' => 'textarea',
        ));
    }
    
    // ============================
    // SECTION: Interests
    // ============================
    $wp_customize->add_section('kunaal_about_interests_v2', array(
        'title' => 'Interests',
        'panel' => 'kunaal_about_panel',
        'priority' => 50,
        'description' => 'Circular images for things that fascinate you. Images should be square.',
    ));
    
    // Show Interests
    $wp_customize->add_setting('kunaal_about_interests_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_interests_show', array(
        'label' => 'Show Interests Section',
        'section' => 'kunaal_about_interests_v2',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_interests_label', array(
        'default' => 'Things That Fascinate Me',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_interests_label', array(
        'label' => 'Section Label',
        'section' => 'kunaal_about_interests_v2',
        'type' => 'text',
    ));
    
    // Interests 1-20 (NO JSON - individual fields with images)
    for ($i = 1; $i <= 20; $i++) {
        $wp_customize->add_setting("kunaal_interest_{$i}_name", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_interest_{$i}_name", array(
            'label' => "Interest {$i}: Name",
            'section' => 'kunaal_about_interests_v2',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_interest_{$i}_image", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_interest_{$i}_image", array(
                'label' => "Interest {$i}: Image",
                'description' => 'Square image (will be displayed as circle)',
                'section' => 'kunaal_about_interests_v2',
                'mime_type' => 'image',
            )
        ));
    }
    
    // ============================
    // SECTION: Inspirations
    // ============================
    $wp_customize->add_section('kunaal_about_inspirations_v2', array(
        'title' => 'Inspirations',
        'panel' => 'kunaal_about_panel',
        'priority' => 60,
    ));
    
    // Show Inspirations
    $wp_customize->add_setting('kunaal_about_inspirations_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_show', array(
        'label' => 'Show Inspirations Section',
        'section' => 'kunaal_about_inspirations_v2',
        'type' => 'checkbox',
    ));
    
    // Section Label
    $wp_customize->add_setting('kunaal_about_inspirations_label', array(
        'default' => 'People Who Inspire Me',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_inspirations_label', array(
        'label' => 'Section Label',
        'section' => 'kunaal_about_inspirations_v2',
        'type' => 'text',
    ));
    
    // Inspirations 1-8 (NO JSON - individual fields)
    for ($i = 1; $i <= 8; $i++) {
        $wp_customize->add_setting("kunaal_inspiration_{$i}_photo", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_inspiration_{$i}_photo", array(
                'label' => "Person {$i}: Photo",
                'section' => 'kunaal_about_inspirations_v2',
                'mime_type' => 'image',
            )
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_name", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_name", array(
            'label' => "Person {$i}: Name",
            'section' => 'kunaal_about_inspirations_v2',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_role", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_role", array(
            'label' => "Person {$i}: Role/Title",
            'section' => 'kunaal_about_inspirations_v2',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_note", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_note", array(
            'label' => "Person {$i}: Note",
            'description' => 'Brief note about why they inspire you',
            'section' => 'kunaal_about_inspirations_v2',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_inspiration_{$i}_url", array(
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("kunaal_inspiration_{$i}_url", array(
            'label' => "Person {$i}: URL",
            'description' => 'Link to their work (optional)',
            'section' => 'kunaal_about_inspirations_v2',
            'type' => 'url',
        ));
    }
    
    // ============================
    // SECTION: Stats
    // ============================
    $wp_customize->add_section('kunaal_about_stats_v2', array(
        'title' => 'Stats Counters',
        'panel' => 'kunaal_about_panel',
        'priority' => 70,
    ));
    
    // Show Stats
    $wp_customize->add_setting('kunaal_about_stats_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_stats_show', array(
        'label' => 'Show Stats Section',
        'section' => 'kunaal_about_stats_v2',
        'type' => 'checkbox',
    ));
    
    // Stats 1-4 (NO JSON - individual fields)
    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("kunaal_stat_{$i}_value", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_stat_{$i}_value", array(
            'label' => "Stat {$i}: Value",
            'description' => 'Number or text (e.g., "30+", "500", "∞")',
            'section' => 'kunaal_about_stats_v2',
            'type' => 'text',
        ));
        
        $wp_customize->add_setting("kunaal_stat_{$i}_label", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_stat_{$i}_label", array(
            'label' => "Stat {$i}: Label",
            'description' => 'e.g., "countries", "essays", "languages"',
            'section' => 'kunaal_about_stats_v2',
            'type' => 'text',
        ));
    }
    
    // ============================
    // SECTION: Atmospheric Images
    // ============================
    $wp_customize->add_section('kunaal_about_atmo', array(
        'title' => 'Atmospheric Images',
        'panel' => 'kunaal_about_panel',
        'priority' => 80,
        'description' => 'Full-bleed images that create visual breaks between sections. Per spec: 12 slots for maximum richness.',
    ));
    
    // Atmospheric Images 1-12 (full spec compliance)
    for ($i = 1; $i <= 12; $i++) {
        // Image
        $wp_customize->add_setting("kunaal_atmo_{$i}_image", array(
            'sanitize_callback' => 'absint',
        ));
        $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
            "kunaal_atmo_{$i}_image", array(
                'label' => "Image {$i}",
                'section' => 'kunaal_about_atmo',
                'mime_type' => 'image',
            )
        ));
        
        // Type
        $wp_customize->add_setting("kunaal_atmo_{$i}_type", array(
            'default' => 'hidden',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_type", array(
            'label' => "Image {$i}: Display Type",
            'section' => 'kunaal_about_atmo',
            'type' => 'select',
            'choices' => array(
                'hidden' => 'Hidden (not displayed)',
                'strip' => 'Full-bleed Strip (100vw × 200-300px)',
                'window' => 'Window Cutout',
                'background' => 'Background Layer',
            ),
        ));
        
        // Position
        $wp_customize->add_setting("kunaal_atmo_{$i}_position", array(
            'default' => 'auto',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_position", array(
            'label' => "Image {$i}: Position",
            'section' => 'kunaal_about_atmo',
            'type' => 'select',
            'choices' => array(
                'after_hero' => 'After Hero',
                'after_bio' => 'After Bio/Bookshelf',
                'after_map' => 'After Map',
                'after_interests' => 'After Interests',
                'after_inspirations' => 'After Inspirations',
                'before_closing' => 'Before Closing',
                'auto' => 'Auto-place',
            ),
        ));
        
        // Clip Style
        $wp_customize->add_setting("kunaal_atmo_{$i}_clip", array(
            'default' => 'straight',
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_clip", array(
            'label' => "Image {$i}: Clip Style",
            'section' => 'kunaal_about_atmo',
            'type' => 'select',
            'choices' => array(
                'straight' => 'Straight Edges',
                'angle_bottom' => 'Angle Bottom',
                'angle_top' => 'Angle Top',
                'angle_both' => 'Angle Both',
            ),
        ));
        
        // Has Quote
        $wp_customize->add_setting("kunaal_atmo_{$i}_has_quote", array(
            'default' => false,
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_has_quote", array(
            'label' => "Image {$i}: Show Quote Overlay",
            'section' => 'kunaal_about_atmo',
            'type' => 'checkbox',
        ));
        
        // Quote Text
        $wp_customize->add_setting("kunaal_atmo_{$i}_quote", array(
            'sanitize_callback' => 'sanitize_textarea_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_quote", array(
            'label' => "Image {$i}: Quote Text",
            'section' => 'kunaal_about_atmo',
            'type' => 'textarea',
        ));
        
        // Quote Attribution
        $wp_customize->add_setting("kunaal_atmo_{$i}_quote_attr", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_quote_attr", array(
            'label' => "Image {$i}: Quote Attribution",
            'section' => 'kunaal_about_atmo',
            'type' => 'text',
        ));
        
        // Caption
        $wp_customize->add_setting("kunaal_atmo_{$i}_caption", array(
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("kunaal_atmo_{$i}_caption", array(
            'label' => "Image {$i}: Caption",
            'section' => 'kunaal_about_atmo',
            'type' => 'text',
        ));
    }
    
    // ============================
    // SECTION: Connect
    // ============================
    $wp_customize->add_section('kunaal_about_connect_v2', array(
        'title' => 'Connect Section',
        'panel' => 'kunaal_about_panel',
        'priority' => 90,
        'description' => 'Social links use the Social Sharing settings. Configure heading here.',
    ));
    
    // Show Connect
    $wp_customize->add_setting('kunaal_about_connect_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_about_connect_show', array(
        'label' => 'Show Connect Section',
        'section' => 'kunaal_about_connect_v2',
        'type' => 'checkbox',
    ));
    
    // Heading
    $wp_customize->add_setting('kunaal_about_connect_heading', array(
        'default' => "Let's Connect",
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_about_connect_heading', array(
        'label' => 'Heading',
        'section' => 'kunaal_about_connect_v2',
        'type' => 'text',
    ));
}
add_action('customize_register', 'kunaal_about_customizer_v2', 20);

/**
 * Helper: Get books from individual fields (NO JSON)
 */
function kunaal_get_books_v2() {
    $books = array();
    for ($i = 1; $i <= 8; $i++) {
        $cover_id = get_theme_mod("kunaal_book_{$i}_cover", 0);
        $title = get_theme_mod("kunaal_book_{$i}_title", '');
        
        // Only add if title exists
        if (!empty($title)) {
            $cover_url = $cover_id ? wp_get_attachment_image_url($cover_id, 'medium') : '';
            $books[] = array(
                'cover' => $cover_url,
                'title' => $title,
                'author' => get_theme_mod("kunaal_book_{$i}_author", ''),
                'link' => get_theme_mod("kunaal_book_{$i}_url", ''),
            );
        }
    }
    return $books;
}

/**
 * Helper: Get interests from individual fields (NO JSON)
 */
function kunaal_get_interests_v2() {
    $interests = array();
    for ($i = 1; $i <= 20; $i++) {
        $name = get_theme_mod("kunaal_interest_{$i}_name", '');
        
        if (!empty($name)) {
            $image_id = get_theme_mod("kunaal_interest_{$i}_image", 0);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'thumbnail') : '';
            $interests[] = array(
                'name' => $name,
                'image' => $image_url,
            );
        }
    }
    return $interests;
}

/**
 * Helper: Get inspirations from individual fields (NO JSON)
 */
function kunaal_get_inspirations_v2() {
    $inspirations = array();
    for ($i = 1; $i <= 8; $i++) {
        $name = get_theme_mod("kunaal_inspiration_{$i}_name", '');
        
        if (!empty($name)) {
            $photo_id = get_theme_mod("kunaal_inspiration_{$i}_photo", 0);
            $photo_url = $photo_id ? wp_get_attachment_image_url($photo_id, 'medium') : '';
            $inspirations[] = array(
                'photo' => $photo_url,
                'name' => $name,
                'role' => get_theme_mod("kunaal_inspiration_{$i}_role", ''),
                'note' => get_theme_mod("kunaal_inspiration_{$i}_note", ''),
                'link' => get_theme_mod("kunaal_inspiration_{$i}_url", ''),
            );
        }
    }
    return $inspirations;
}

/**
 * Helper: Get stats from individual fields (NO JSON)
 */
function kunaal_get_stats_v2() {
    $stats = array();
    for ($i = 1; $i <= 4; $i++) {
        $value = get_theme_mod("kunaal_stat_{$i}_value", '');
        $label = get_theme_mod("kunaal_stat_{$i}_label", '');
        
        if (!empty($value) && !empty($label)) {
            $stats[] = array(
                'number' => $value,
                'label' => $label,
            );
        }
    }
    return $stats;
}

/**
 * Helper: Get map stories from individual fields (NO JSON)
 */
function kunaal_get_map_stories_v2() {
    $stories = array();
    for ($i = 1; $i <= 10; $i++) {
        $country = get_theme_mod("kunaal_map_story_{$i}_country", '');
        
        if (!empty($country)) {
            $stories[strtoupper(trim($country))] = array(
                'years' => get_theme_mod("kunaal_map_story_{$i}_years", ''),
                'text' => get_theme_mod("kunaal_map_story_{$i}_text", ''),
            );
        }
    }
    return $stories;
}

/**
 * Helper: Get hero photos from individual fields
 */
function kunaal_get_hero_photos_v2() {
    $photos = array();
    for ($i = 1; $i <= 4; $i++) {
        $photo_id = get_theme_mod("kunaal_about_hero_photo_{$i}", 0);
        if ($photo_id) {
            $photo_url = wp_get_attachment_image_url($photo_id, 'large');
            if ($photo_url) {
                $photos[] = $photo_url;
            }
        }
    }
    return $photos;
}

/**
 * Helper: Get atmospheric images for a specific position
 */
function kunaal_get_atmo_images_v2($position = 'all') {
    $images = array();
    for ($i = 1; $i <= 12; $i++) {
        $image_id = get_theme_mod("kunaal_atmo_{$i}_image", 0);
        $type = get_theme_mod("kunaal_atmo_{$i}_type", 'hidden');
        $pos = get_theme_mod("kunaal_atmo_{$i}_position", 'auto');
        
        if ($image_id && $type !== 'hidden') {
            if ($position === 'all' || $pos === $position || $pos === 'auto') {
                $images[] = array(
                    'image' => wp_get_attachment_image_url($image_id, 'full'),
                    'type' => $type,
                    'position' => $pos,
                    'clip' => get_theme_mod("kunaal_atmo_{$i}_clip", 'straight'),
                    'has_quote' => get_theme_mod("kunaal_atmo_{$i}_has_quote", false),
                    'quote' => get_theme_mod("kunaal_atmo_{$i}_quote", ''),
                    'quote_attr' => get_theme_mod("kunaal_atmo_{$i}_quote_attr", ''),
                    'caption' => get_theme_mod("kunaal_atmo_{$i}_caption", ''),
                );
            }
        }
    }
    return $images;
}

