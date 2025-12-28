<?php
/**
 * About Page Customizer
 *
 * Comprehensive Customizer implementation for the About page design.
 * All fields use native WordPress controls - no JSON editing required.
 *
 * @package Kunaal_Theme
 * @since 4.21.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Constants for repeated literals
if (!defined('KUNAAL_CUSTOMIZER_SECTION_TITLE_LABEL')) {
    define('KUNAAL_CUSTOMIZER_SECTION_TITLE_LABEL', 'Section Title');
}

/**
 * Register About Page Customizer Panel and Sections
 * 
 * Main function that creates the panel and delegates to section-specific functions.
 * This reduces the function length from 777 lines to ~30 lines.
 */
function kunaal_about_customizer_v22(WP_Customize_Manager $wp_customize): void {
    // Load section helpers
    require_once KUNAAL_THEME_DIR . '/inc/about-customizer-sections.php';
    
    // ============================
    // PANEL: About Page
    // ============================
    $wp_customize->add_panel('kunaal_about_v22_panel', array(
        'title' => 'About Page',
        'priority' => 50,
        'description' => 'Configure your About page sections. All fields are intuitive - no JSON required.',
    ));
    
    // Register all sections via helper functions
    kunaal_register_about_hero_section($wp_customize);
    kunaal_register_about_numbers_section($wp_customize);
    kunaal_register_about_categories_section($wp_customize);
    kunaal_register_about_rabbit_holes_section($wp_customize);
    kunaal_register_about_panoramas_section($wp_customize);
    kunaal_register_about_books_section($wp_customize);
    kunaal_register_about_digital_section($wp_customize);
    kunaal_register_about_places_section($wp_customize);
    kunaal_register_about_inspirations_section($wp_customize);
    kunaal_register_about_say_hello_section($wp_customize);
}
add_action('customize_register', 'kunaal_about_customizer_v22', 20);

/**
 * Helper: Get category choices for dropdown
 * Dynamically builds choices from defined categories
 * 
 * @param WP_Customize_Manager $wp_customize Optional. Customizer instance for live preview.
 * @return array Category choices for select dropdown
 */
function kunaal_get_category_choices_v22(): array {
    $choices = array('' => '-- Select Category --');
    for ($i = 1; $i <= 12; $i++) {
        $name = kunaal_mod("kunaal_about_v22_category_{$i}_name", '');
        if (!empty($name)) {
            // Use slugified version as key
            $slug = sanitize_title($name);
            $choices[$slug] = $name;
        }
    }
    return $choices;
}

