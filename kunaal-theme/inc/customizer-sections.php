<?php
/**
 * Customizer Section Helpers
 *
 * Split from kunaal_customize_register() to reduce function length and complexity.
 * Each function handles a specific Customizer section.
 *
 * @package Kunaal_Theme
 * @since 4.21.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Author Info section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_author_section($wp_customize) {
    $wp_customize->add_section('kunaal_author', array(
        'title' => 'Author Info',
        'priority' => 30,
    ));

    // Avatar
    $wp_customize->add_setting('kunaal_avatar', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kunaal_avatar', array(
        'label' => 'Author Avatar',
        'description' => 'Upload a square image (at least 88x88px). If not set, initials will be displayed.',
        'section' => 'kunaal_author',
    )));

    // First Name (live preview)
    $wp_customize->add_setting('kunaal_author_first_name', array(
        'default' => 'Kunaal',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_author_first_name', array(
        'label' => 'First Name',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Last Name (live preview)
    $wp_customize->add_setting('kunaal_author_last_name', array(
        'default' => 'Wadhwa',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_author_last_name', array(
        'label' => 'Last Name',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Tagline (live preview)
    $wp_customize->add_setting('kunaal_author_tagline', array(
        'default' => 'A slightly alarming curiosity about humans and human collectives.',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage',
    ));
    $wp_customize->add_control('kunaal_author_tagline', array(
        'label' => 'Tagline',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));

    // Contact Email
    $wp_customize->add_setting('kunaal_contact_email', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_contact_email', array(
        'label' => 'Contact Email',
        'description' => 'Displayed in footer with envelope animation',
        'section' => 'kunaal_author',
        'type' => 'email',
    ));

    // Footer Disclaimer
    $wp_customize->add_setting('kunaal_footer_disclaimer', array(
        'default' => 'Personal writing. Independent of my day job.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_footer_disclaimer', array(
        'label' => 'Footer Disclaimer',
        'section' => 'kunaal_author',
        'type' => 'text',
    ));
}

/**
 * Register Social Sharing section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_sharing_section($wp_customize) {
    $wp_customize->add_section('kunaal_sharing', array(
        'title' => 'Social Sharing',
        'priority' => 35,
        'description' => 'Customize how posts are shared on social media.',
    ));

    // LinkedIn Handle
    $wp_customize->add_setting('kunaal_linkedin_handle', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_linkedin_handle', array(
        'label' => 'LinkedIn Profile URL',
        'description' => 'Full LinkedIn profile URL (e.g., https://linkedin.com/in/yourname)',
        'section' => 'kunaal_sharing',
        'type' => 'url',
    ));

    // Twitter/X Handle
    $wp_customize->add_setting('kunaal_twitter_handle', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_twitter_handle', array(
        'label' => 'Twitter/X Handle',
        'description' => 'Without @ (e.g., yourhandle)',
        'section' => 'kunaal_sharing',
        'type' => 'text',
    ));

    // Default Share Text
    $wp_customize->add_setting('kunaal_share_text', array(
        'default' => 'Check out this article:',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_share_text', array(
        'label' => 'Default Share Text',
        'description' => 'Text that appears before the article title when sharing',
        'section' => 'kunaal_sharing',
        'type' => 'text',
    ));
}

/**
 * Register Site Identity additions (favicon)
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_site_identity($wp_customize) {
    // Favicon (PNG without border)
    $wp_customize->add_setting('kunaal_favicon', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kunaal_favicon', array(
        'label' => 'Favicon (PNG)',
        'description' => 'Upload a PNG favicon (recommended 32x32 or 180x180 for Apple Touch). No white outline.',
        'section' => 'title_tagline',
        'priority' => 100,
    )));
}

/**
 * Register Subscribe section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_subscribe_section($wp_customize) {
    $wp_customize->add_section('kunaal_subscribe', array(
        'title' => 'Subscribe Section',
        'priority' => 45,
    ));

    // Enable Subscribe Section
    $wp_customize->add_setting('kunaal_subscribe_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_subscribe_enabled', array(
        'label' => 'Enable Subscribe Section',
        'description' => 'Show email subscribe form on essays and jottings',
        'section' => 'kunaal_subscribe',
        'type' => 'checkbox',
    ));

    // Subscribe Display Location
    $wp_customize->add_setting('kunaal_subscribe_location', array(
        'default' => 'both',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_location', array(
        'label' => 'Subscribe Display Location',
        'description' => 'Choose where to show subscribe form',
        'section' => 'kunaal_subscribe',
        'type' => 'radio',
        'choices' => array(
            'dock' => 'Dock only (floating button)',
            'bottom' => 'Bottom section only',
            'both' => 'Both dock and bottom',
            'neither' => 'Disabled',
        ),
    ));

    // Subscribe Heading
    $wp_customize->add_setting('kunaal_subscribe_heading', array(
        'default' => 'Stay updated',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_heading', array(
        'label' => 'Subscribe Heading',
        'section' => 'kunaal_subscribe',
        'type' => 'text',
    ));

    // Subscribe Description
    $wp_customize->add_setting('kunaal_subscribe_description', array(
        'default' => __('Get notified when new essays and jottings are published.', 'kunaal-theme'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_description', array(
        'label' => 'Subscribe Description',
        'section' => 'kunaal_subscribe',
        'type' => 'textarea',
    ));

    // Subscribe Form Action URL
    $wp_customize->add_setting('kunaal_subscribe_form_action', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_subscribe_form_action', array(
        'label' => 'Form Action URL',
        'description' => 'Optional: external provider form action URL (Mailchimp/ConvertKit/etc). If empty, the theme will use its built-in subscribe flow.',
        'section' => 'kunaal_subscribe',
        'type' => 'url',
    ));

    // Subscribe notification delay (hours after publish)
    $wp_customize->add_setting('kunaal_subscribe_notify_delay_hours', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_subscribe_notify_delay_hours', array(
        'label' => 'Email Delay (Hours)',
        'description' => 'Send notification emails X hours after a new essay/jotting is published (0 = immediately)',
        'section' => 'kunaal_subscribe',
        'type' => 'number',
        'input_attrs' => array('min' => 0, 'max' => 168, 'step' => 1),
    ));

    // Subscribe mode (built-in vs external)
    $wp_customize->add_setting('kunaal_subscribe_mode', array(
        'default' => 'builtin',
        'sanitize_callback' => function ($value) {
            $allowed = array('builtin', 'external');
            return in_array($value, $allowed, true) ? $value : 'builtin';
        },
    ));
    $wp_customize->add_control('kunaal_subscribe_mode', array(
        'label' => 'Subscribe Mode',
        'description' => 'Built-in: stores subscribers in WordPress (private) and sends confirmation emails. External: posts to your provider form action URL.',
        'section' => 'kunaal_subscribe',
        'type' => 'radio',
        'choices' => array(
            'builtin' => 'Built-in (recommended)',
            'external' => 'External provider (form action URL)',
        ),
    ));

    // Where subscription notifications go (built-in mode)
    $wp_customize->add_setting('kunaal_subscribe_notify_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_subscribe_notify_email', array(
        'label' => 'Subscribe Notifications Email',
        'description' => 'Built-in mode: confirmations and admin notifications use this email.',
        'section' => 'kunaal_subscribe',
        'type' => 'email',
    ));
}

/**
 * Register Contact Page section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_contact_page_section($wp_customize) {
    $wp_customize->add_section('kunaal_contact_page', array(
        'title' => 'Contact Page',
        'priority' => 51,
        'description' => 'Customize the Contact page. Create a page with the "Contact Page" template.',
    ));

    // Recipient email (where messages are delivered)
    $wp_customize->add_setting('kunaal_contact_recipient_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_contact_recipient_email', array(
        'label' => 'Recipient Email (for Contact form delivery)',
        'description' => 'Messages submitted on the Contact page will be sent to this address.',
        'section' => 'kunaal_contact_page',
        'type' => 'email',
    ));

    $wp_customize->add_setting('kunaal_contact_headline', array(
        'default' => 'Get in Touch',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_headline', array(
        'label' => 'Headline',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_contact_placeholder', array(
        'default' => 'Leave a note...',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_placeholder', array(
        'label' => 'Message Placeholder Text',
        'description' => 'Text shown in the message textarea when empty',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_contact_response_time', array(
        'default' => 'I typically respond within 2-3 business days.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_response_time', array(
        'label' => 'Response Time Note',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));
}

/**
 * Register Email Delivery (SMTP) section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_email_delivery_section($wp_customize) {
    $wp_customize->add_section('kunaal_email_delivery', array(
        'title' => 'Email Delivery (SMTP)',
        'priority' => 52,
        'description' => 'Configure SMTP so contact + subscribe emails deliver reliably on shared hosts.',
    ));

    $wp_customize->add_setting('kunaal_smtp_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_smtp_enabled', array(
        'label' => 'Enable SMTP',
        'section' => 'kunaal_email_delivery',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('kunaal_smtp_from_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_smtp_from_email', array(
        'label' => 'From Email',
        'description' => 'Use an address that matches your domain if possible.',
        'section' => 'kunaal_email_delivery',
        'type' => 'email',
    ));

    $wp_customize->add_setting('kunaal_smtp_from_name', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_from_name', array(
        'label' => 'From Name',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_smtp_host', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_host', array(
        'label' => 'SMTP Host',
        'description' => 'Example (Brevo): smtp-relay.brevo.com',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_smtp_port', array(
        'default' => 587,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_smtp_port', array(
        'label' => 'SMTP Port',
        'description' => '587 (TLS) is typical; 465 for SSL.',
        'section' => 'kunaal_email_delivery',
        'type' => 'number',
        'input_attrs' => array('min' => 1, 'max' => 65535),
    ));

    $wp_customize->add_setting('kunaal_smtp_encryption', array(
        'default' => 'tls',
        'sanitize_callback' => function ($value) {
            $allowed = array('none', 'tls', 'ssl');
            return in_array($value, $allowed, true) ? $value : 'tls';
        },
    ));
    $wp_customize->add_control('kunaal_smtp_encryption', array(
        'label' => 'Encryption',
        'section' => 'kunaal_email_delivery',
        'type' => 'select',
        'choices' => array(
            'none' => 'None',
            'tls'  => 'TLS',
            'ssl'  => 'SSL',
        ),
    ));

    $wp_customize->add_setting('kunaal_smtp_username', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_username', array(
        'label' => 'SMTP Username',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_smtp_password', array(
        'default' => '',
        'sanitize_callback' => function ($value) {
            // Allow most characters; keep as plain text (shared-host reality). Avoid stripping symbols.
            return is_string($value) ? $value : '';
        },
    ));
    $wp_customize->add_control('kunaal_smtp_password', array(
        'label' => 'SMTP Password / Key',
        'section' => 'kunaal_email_delivery',
        'type' => 'password',
    ));
}

/**
 * Register Contact Page social links
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_contact_social_links($wp_customize) {
    // Instagram
    $wp_customize->add_setting('kunaal_contact_instagram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_instagram', array(
        'label' => 'Instagram URL',
        'description' => 'Your Instagram profile URL (e.g., https://instagram.com/yourname)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));

    // WhatsApp (public link, no phone number)
    $wp_customize->add_setting('kunaal_contact_whatsapp', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_whatsapp', array(
        'label' => 'WhatsApp Link',
        'description' => 'Public WhatsApp link (e.g., https://wa.me/yourlink - avoid phone numbers)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));

    // Viber (public link, no phone number)
    $wp_customize->add_setting('kunaal_contact_viber', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_viber', array(
        'label' => 'Viber Link',
        'description' => 'Public Viber link (e.g., https://chats.viber.com/yourname - avoid phone numbers)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));

    // LINE (public link, no phone number)
    $wp_customize->add_setting('kunaal_contact_line', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('kunaal_contact_line', array(
        'label' => 'LINE Link',
        'description' => 'Public LINE link (e.g., https://line.me/R/ti/p/@yourname - avoid phone numbers)',
        'section' => 'kunaal_contact_page',
        'type' => 'url',
    ));
}

