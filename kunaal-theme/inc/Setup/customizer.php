<?php
/**
 * Customizer Registration
 * 
 * Registers all Customizer sections and settings.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Customizer Registration
 * Delegates to section-specific functions
 */
function kunaal_customize_register(WP_Customize_Manager $wp_customize): void {
    kunaal_customize_register_author_section($wp_customize);
    kunaal_customize_register_sharing_section($wp_customize);
    kunaal_customize_register_site_identity($wp_customize);
    kunaal_customize_register_essay_layout_section($wp_customize);
    kunaal_customize_register_subscribe_section($wp_customize);
    kunaal_customize_register_contact_page_section($wp_customize);
    kunaal_customize_register_email_delivery_section($wp_customize);
    kunaal_customize_register_contact_social_links($wp_customize);
}
add_action('customize_register', 'kunaal_customize_register');

/**
 * Enqueue Customizer Preview Script
 *
 * Loads JavaScript that handles live preview updates without page refresh.
 * Uses debouncing to prevent excessive updates while typing.
 */
function kunaal_customizer_preview_js(): void {
    wp_enqueue_script(
        'kunaal-customizer-preview',
        KUNAAL_THEME_URI . '/assets/js/customizer-preview.js',
        array('jquery', 'customize-preview'),
        KUNAAL_THEME_VERSION,
        true
    );
}
add_action('customize_preview_init', 'kunaal_customizer_preview_js');

