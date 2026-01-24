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
function kunaal_customize_register_author_section(WP_Customize_Manager $wp_customize): void {
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
function kunaal_customize_register_sharing_section(WP_Customize_Manager $wp_customize): void {
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
function kunaal_customize_register_site_identity(WP_Customize_Manager $wp_customize): void {
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
 * Register Essay Layout section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_essay_layout_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_essay_layout', array(
        'title' => 'Essay Layout',
        'priority' => 42,
    ));

    // Sidebar TOC Enable
    $wp_customize->add_setting('kunaal_essay_sidebar_toc', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_essay_sidebar_toc', array(
        'label' => 'Show Sidebar Table of Contents',
        'description' => 'Auto-generated TOC from headings in the sidebar. For custom TOC, use the Custom TOC block instead.',
        'section' => 'kunaal_essay_layout',
        'type' => 'checkbox',
    ));

    // Sidebar TOC Title
    $wp_customize->add_setting('kunaal_essay_sidebar_toc_title', array(
        'default' => 'On this page',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_essay_sidebar_toc_title', array(
        'label' => 'Sidebar TOC Title',
        'section' => 'kunaal_essay_layout',
        'type' => 'text',
    ));

    // Custom TOC Block - Hide on Mobile
    $wp_customize->add_setting('kunaal_custom_toc_hide_mobile', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_custom_toc_hide_mobile', array(
        'label' => 'Hide Custom TOC on Mobile',
        'description' => 'Hide the Custom TOC block on mobile devices (< 640px width)',
        'section' => 'kunaal_essay_layout',
        'type' => 'checkbox',
    ));
}

/**
 * Register Content Labels section and controls
 * Controls singular/plural labels for essays and jottings on homepage and archives
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_labels_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_labels', array(
        'title' => 'Content Labels',
        'priority' => 43,
        'description' => 'Customize the labels shown for essays and jottings on the homepage and archive pages.',
    ));

    // Essay Labels
    $wp_customize->add_setting('kunaal_essay_label_singular', array(
        'default' => 'long one',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_essay_label_singular', array(
        'label' => 'Essay Label (Singular)',
        'description' => 'Label when there is exactly 1 essay (e.g., "long one")',
        'section' => 'kunaal_labels',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_essay_label_plural', array(
        'default' => 'long ones',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_essay_label_plural', array(
        'label' => 'Essay Label (Plural)',
        'description' => 'Label when there are 0 or 2+ essays (e.g., "long ones")',
        'section' => 'kunaal_labels',
        'type' => 'text',
    ));

    // Jotting Labels
    $wp_customize->add_setting('kunaal_jotting_label_singular', array(
        'default' => 'short one',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_jotting_label_singular', array(
        'label' => 'Jotting Label (Singular)',
        'description' => 'Label when there is exactly 1 jotting (e.g., "short one")',
        'section' => 'kunaal_labels',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_jotting_label_plural', array(
        'default' => 'short ones',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_jotting_label_plural', array(
        'label' => 'Jotting Label (Plural)',
        'description' => 'Label when there are 0 or 2+ jottings (e.g., "short ones")',
        'section' => 'kunaal_labels',
        'type' => 'text',
    ));
}

/**
 * Register Subscribe section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_subscribe_section(WP_Customize_Manager $wp_customize): void {
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
        'description' => 'Legacy default delay (hours). New system uses minutes + a global minimum delay; this remains as a fallback default if minutes are not set.',
        'section' => 'kunaal_subscribe',
        'type' => 'number',
        'input_attrs' => array('min' => 0, 'max' => 168, 'step' => 1),
    ));

    // Global minimum delay (minutes). Enforced as >= 60 minutes.
    $wp_customize->add_setting('kunaal_subscribe_global_min_delay_minutes', array(
        'default' => 60,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_subscribe_global_min_delay_minutes', array(
        'label' => 'Global Minimum Delay (Minutes)',
        'description' => 'All subscriber emails (post notifications) will be scheduled at least this many minutes in the future (minimum enforced: 60).',
        'section' => 'kunaal_subscribe',
        'type' => 'number',
        'input_attrs' => array('min' => 60, 'max' => 10080, 'step' => 1),
    ));

    // Default delay minutes for post notifications when a post-level delay is not set.
    $wp_customize->add_setting('kunaal_subscribe_default_delay_minutes', array(
        'default' => 0,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_subscribe_default_delay_minutes', array(
        'label' => 'Default Post Email Delay (Minutes)',
        'description' => 'If a post does not specify its own delay/time, this default is used (0 = use the legacy hours setting).',
        'section' => 'kunaal_subscribe',
        'type' => 'number',
        'input_attrs' => array('min' => 0, 'max' => 10080, 'step' => 1),
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

    // Confirmation email subject/body templates (built-in mode).
    $wp_customize->add_setting('kunaal_subscribe_confirm_subject', array(
        'default' => '[{site}] Confirm your subscription',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_confirm_subject', array(
        'label' => 'Confirmation Email Subject',
        'description' => 'Placeholders: {site}, {confirm_url}, {unsubscribe_url}',
        'section' => 'kunaal_subscribe',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_subscribe_confirm_body', array(
        'default' => "Hi!\n\nPlease confirm your subscription by clicking the link below:\n\n{confirm_url}\n\nIf you didn't request this, you can ignore this email.",
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_confirm_body', array(
        'label' => 'Confirmation Email Body',
        'description' => 'Plain text. Placeholders: {site}, {confirm_url}, {unsubscribe_url}',
        'section' => 'kunaal_subscribe',
        'type' => 'textarea',
    ));

    // Post notification templates.
    $wp_customize->add_setting('kunaal_subscribe_post_subject', array(
        'default' => '[{site}] New: {title}',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_post_subject', array(
        'label' => 'New Post Email Subject',
        'description' => 'Placeholders: {site}, {title}, {url}, {unsubscribe_url}',
        'section' => 'kunaal_subscribe',
        'type' => 'text',
    ));

    $wp_customize->add_setting('kunaal_subscribe_post_body', array(
        'default' => "{title}\n\nRead: {url}\n\n—\nYou are receiving this because you subscribed to {site}.",
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_post_body', array(
        'label' => 'New Post Email Body',
        'description' => 'Plain text. Placeholders: {site}, {title}, {url}, {unsubscribe_url}',
        'section' => 'kunaal_subscribe',
        'type' => 'textarea',
    ));

    // Optional click tracking (best-effort).
    $wp_customize->add_setting('kunaal_subscribe_click_tracking', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_subscribe_click_tracking', array(
        'label' => 'Enable Click Tracking (Best-effort)',
        'description' => 'Adds redirect links to post notification emails to track clicks. Some privacy tools may block or strip tracking.',
        'section' => 'kunaal_subscribe',
        'type' => 'checkbox',
    ));

    // Footer appended to all subscriber emails.
    $wp_customize->add_setting('kunaal_subscribe_email_footer', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_email_footer', array(
        'label' => 'Subscriber Email Footer',
        'description' => 'Appended to all subscriber emails (plain text).',
        'section' => 'kunaal_subscribe',
        'type' => 'textarea',
    ));

    // Unsubscribe text.
    $wp_customize->add_setting('kunaal_subscribe_unsubscribe_text', array(
        'default' => 'Unsubscribe',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_subscribe_unsubscribe_text', array(
        'label' => 'Unsubscribe Link Text',
        'description' => 'Shown above the unsubscribe URL in subscriber emails.',
        'section' => 'kunaal_subscribe',
        'type' => 'text',
    ));
}

/**
 * Register Contact Page section and controls
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_contact_page_section(WP_Customize_Manager $wp_customize): void {
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

    // Label (small text above title)
    $wp_customize->add_setting('kunaal_contact_label', array(
        'default' => 'Note',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_label', array(
        'label' => 'Label (small text)',
        'description' => 'Small uppercase text above the headline',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));

    // Show Headline toggle
    $wp_customize->add_setting('kunaal_contact_headline_show', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_contact_headline_show', array(
        'label' => 'Show Headline',
        'description' => 'Toggle to hide the large headline text',
        'section' => 'kunaal_contact_page',
        'type' => 'checkbox',
    ));

    // Headline text
    $wp_customize->add_setting('kunaal_contact_headline', array(
        'default' => 'Get in Touch',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_headline', array(
        'label' => 'Headline',
        'section' => 'kunaal_contact_page',
        'type' => 'text',
    ));

    // Headline font size
    $wp_customize->add_setting('kunaal_contact_headline_size', array(
        'default' => 'large',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_contact_headline_size', array(
        'label' => 'Headline Font Size',
        'section' => 'kunaal_contact_page',
        'type' => 'select',
        'choices' => array(
            'small' => 'Small (1.25rem)',
            'medium' => 'Medium (1.5rem)',
            'large' => 'Large (2rem) - Default',
        ),
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
 * Two options for credentials:
 * 1. wp-config.php constants (more secure - recommended for public repos)
 * 2. GUI fields below (convenient - stored in database)
 * 
 * wp-config.php constants (if used, these take priority):
 *   define('KUNAAL_SMTP_HOST', 'smtp.example.com');
 *   define('KUNAAL_SMTP_PORT', 587);
 *   define('KUNAAL_SMTP_USER', 'your-username');
 *   define('KUNAAL_SMTP_PASS', 'your-password');
 *   define('KUNAAL_SMTP_SECURE', 'tls');
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_email_delivery_section(WP_Customize_Manager $wp_customize): void {
    // Check if wp-config.php constants are set
    $has_config_creds = defined('KUNAAL_SMTP_HOST') && defined('KUNAAL_SMTP_USER') && defined('KUNAAL_SMTP_PASS');
    
    if ($has_config_creds) {
        $description = sprintf(
            '<p style="color: green; font-weight: bold;">✓ Using SMTP credentials from wp-config.php</p><p><strong>Host:</strong> %s<br><strong>Port:</strong> %s</p><p>The GUI fields below are ignored when wp-config.php constants are set.</p>',
            esc_html(KUNAAL_SMTP_HOST),
            esc_html(defined('KUNAAL_SMTP_PORT') ? KUNAAL_SMTP_PORT : '587')
        );
    } else {
        $description = '<p>Configure SMTP to send emails reliably (Contact form, Subscribe confirmations).</p><p><strong>For Gmail:</strong> Use smtp.gmail.com, port 587, TLS encryption, and an <a href="https://myaccount.google.com/apppasswords" target="_blank">App Password</a> (not your regular password).</p>';
    }

    $wp_customize->add_section('kunaal_email_delivery', array(
        'title' => 'Email Delivery (SMTP)',
        'priority' => 52,
        'description' => $description,
    ));

    // Enable toggle
    $wp_customize->add_setting('kunaal_smtp_enabled', array(
        'default' => false,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_smtp_enabled', array(
        'label' => 'Enable SMTP',
        'description' => 'Turn on to use SMTP for outgoing emails.',
        'section' => 'kunaal_email_delivery',
        'type' => 'checkbox',
    ));

    // SMTP Host
    $wp_customize->add_setting('kunaal_smtp_host_gui', array(
        'default' => 'smtp.gmail.com',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_host_gui', array(
        'label' => 'SMTP Host',
        'description' => 'Example: smtp.gmail.com (TLS 587). If you are on GoDaddy shared hosting and O365/Gmail times out, use GoDaddy SMTP relay (commonly relay-hosting.secureserver.net) with port 25, no encryption, and SMTP Authentication OFF.',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    // SMTP Port
    $wp_customize->add_setting('kunaal_smtp_port_gui', array(
        'default' => '587',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('kunaal_smtp_port_gui', array(
        'label' => 'SMTP Port',
        'description' => '587 for TLS (recommended), 465 for SSL',
        'section' => 'kunaal_email_delivery',
        'type' => 'number',
        'input_attrs' => array('min' => 1, 'max' => 65535),
    ));

    // SMTP Encryption
    $wp_customize->add_setting('kunaal_smtp_encryption_gui', array(
        'default' => 'tls',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_encryption_gui', array(
        'label' => 'Encryption',
        'section' => 'kunaal_email_delivery',
        'type' => 'select',
        'choices' => array(
            'tls' => 'TLS (Port 587)',
            'ssl' => 'SSL (Port 465)',
            '' => 'None (not recommended)',
        ),
    ));

    // SMTP Authentication
    // Some managed hosts (e.g., GoDaddy shared hosting) only support a local/relay SMTP hop
    // without authentication (e.g., host "localhost" on port 25).
    $wp_customize->add_setting('kunaal_smtp_auth_gui', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('kunaal_smtp_auth_gui', array(
        'label' => 'SMTP Authentication',
        'description' => 'Turn OFF for GoDaddy relay/local SMTP (e.g., host "localhost" on port 25). Leave ON for Gmail/O365/etc.',
        'section' => 'kunaal_email_delivery',
        'type' => 'checkbox',
    ));

    // SMTP Username
    $wp_customize->add_setting('kunaal_smtp_username_gui', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_username_gui', array(
        'label' => 'SMTP Username',
        'description' => 'Usually your email address',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));

    // SMTP Password (stored in DB - less secure but more convenient)
    $wp_customize->add_setting('kunaal_smtp_password_gui', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_password_gui', array(
        'label' => 'SMTP Password / App Password',
        'description' => 'For Gmail, use an App Password from Google Account settings.',
        'section' => 'kunaal_email_delivery',
        'type' => 'password',
    ));

    // From Email
    $wp_customize->add_setting('kunaal_smtp_from_email', array(
        'default' => get_option('admin_email'),
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('kunaal_smtp_from_email', array(
        'label' => 'From Email',
        'description' => 'Emails will appear to come from this address.',
        'section' => 'kunaal_email_delivery',
        'type' => 'email',
    ));

    // From Name
    $wp_customize->add_setting('kunaal_smtp_from_name', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('kunaal_smtp_from_name', array(
        'label' => 'From Name',
        'description' => 'Name shown in the email "From" field.',
        'section' => 'kunaal_email_delivery',
        'type' => 'text',
    ));
}

/**
 * Register Contact Page social links with enable/disable toggles
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_contact_social_links(WP_Customize_Manager $wp_customize): void {
    // Social platforms configuration - each can be enabled/disabled independently
    $social_platforms = array(
        'email' => array(
            'label' => 'Email',
            'default_enabled' => true,
            'description' => 'Show email link (uses Contact Email setting)',
            'url_setting' => false, // Uses kunaal_contact_email
        ),
        'linkedin' => array(
            'label' => 'LinkedIn',
            'default_enabled' => true,
            'description' => 'Show LinkedIn link (uses LinkedIn Handle from About page)',
            'url_setting' => false, // Uses kunaal_linkedin_handle
        ),
        'twitter' => array(
            'label' => 'X / Twitter',
            'default_enabled' => true,
            'description' => 'Show X/Twitter link (uses Twitter Handle from About page)',
            'url_setting' => false, // Uses kunaal_twitter_handle
        ),
        'instagram' => array(
            'label' => 'Instagram',
            'default_enabled' => true,
            'description' => 'Instagram profile URL',
            'url_setting' => true,
            'url_placeholder' => 'https://instagram.com/yourname',
        ),
        'whatsapp' => array(
            'label' => 'WhatsApp',
            'default_enabled' => false,
            'description' => 'Public WhatsApp link (avoid phone numbers)',
            'url_setting' => true,
            'url_placeholder' => 'https://wa.me/yourlink',
        ),
        'viber' => array(
            'label' => 'Viber',
            'default_enabled' => false,
            'description' => 'Public Viber link (avoid phone numbers)',
            'url_setting' => true,
            'url_placeholder' => 'https://chats.viber.com/yourname',
        ),
        'line' => array(
            'label' => 'LINE',
            'default_enabled' => false,
            'description' => 'Public LINE link (avoid phone numbers)',
            'url_setting' => true,
            'url_placeholder' => 'https://line.me/R/ti/p/@yourname',
        ),
        'telegram' => array(
            'label' => 'Telegram',
            'default_enabled' => false,
            'description' => 'Telegram username link',
            'url_setting' => true,
            'url_placeholder' => 'https://t.me/yourname',
        ),
        'signal' => array(
            'label' => 'Signal',
            'default_enabled' => false,
            'description' => 'Signal group or profile link',
            'url_setting' => true,
            'url_placeholder' => 'https://signal.group/#...',
        ),
        'facebook' => array(
            'label' => 'Facebook',
            'default_enabled' => false,
            'description' => 'Facebook profile or page URL',
            'url_setting' => true,
            'url_placeholder' => 'https://facebook.com/yourpage',
        ),
        'youtube' => array(
            'label' => 'YouTube',
            'default_enabled' => false,
            'description' => 'YouTube channel URL',
            'url_setting' => true,
            'url_placeholder' => 'https://youtube.com/@yourchannel',
        ),
        'tiktok' => array(
            'label' => 'TikTok',
            'default_enabled' => false,
            'description' => 'TikTok profile URL',
            'url_setting' => true,
            'url_placeholder' => 'https://tiktok.com/@yourname',
        ),
        'github' => array(
            'label' => 'GitHub',
            'default_enabled' => false,
            'description' => 'GitHub profile URL',
            'url_setting' => true,
            'url_placeholder' => 'https://github.com/yourname',
        ),
    );

    // Register settings for each platform
    foreach ($social_platforms as $key => $config) {
        // Enable/Disable toggle
        $wp_customize->add_setting("kunaal_social_{$key}_enabled", array(
            'default' => $config['default_enabled'],
            'sanitize_callback' => 'wp_validate_boolean',
        ));
        $wp_customize->add_control("kunaal_social_{$key}_enabled", array(
            'label' => sprintf(__('Enable %s', 'kunaal-theme'), $config['label']),
            'description' => $config['description'],
            'section' => 'kunaal_contact_page',
            'type' => 'checkbox',
        ));

        // URL setting (only for platforms that need one)
        if ($config['url_setting']) {
            $wp_customize->add_setting("kunaal_contact_{$key}", array(
                'default' => '',
                'sanitize_callback' => 'esc_url_raw',
            ));
            $wp_customize->add_control("kunaal_contact_{$key}", array(
                'label' => sprintf(__('%s URL', 'kunaal-theme'), $config['label']),
                'description' => sprintf(__('e.g., %s', 'kunaal-theme'), $config['url_placeholder']),
                'section' => 'kunaal_contact_page',
                'type' => 'url',
            ));
        }
    }
}

/**
 * Helper function to check if a social platform is enabled and has a valid URL
 *
 * @param string $platform Platform key (e.g., 'linkedin', 'viber')
 * @return array|false Array with 'url' and 'label' if enabled and valid, false otherwise
 */
function kunaal_get_social_link(string $platform): array|false {
    // Check if enabled
    $enabled = kunaal_mod("kunaal_social_{$platform}_enabled", true);
    if (!$enabled) {
        return false;
    }

    // Get URL based on platform
    $url = '';
    $label = ucfirst($platform);
    
    switch ($platform) {
        case 'email':
            $url = kunaal_mod('kunaal_contact_email', '');
            $label = 'Email';
            if ($url) {
                $url = 'mailto:' . $url;
            }
            break;
        case 'linkedin':
            $url = kunaal_mod('kunaal_linkedin_handle', '');
            $label = 'LinkedIn';
            break;
        case 'twitter':
            $handle = kunaal_mod('kunaal_twitter_handle', '');
            $label = 'X / Twitter';
            if ($handle) {
                $url = 'https://x.com/' . ltrim($handle, '@');
            }
            break;
        case 'instagram':
            $url = kunaal_mod('kunaal_contact_instagram', '');
            $label = 'Instagram';
            break;
        case 'whatsapp':
            $url = kunaal_mod('kunaal_contact_whatsapp', '');
            $label = 'WhatsApp';
            break;
        case 'viber':
            $url = kunaal_mod('kunaal_contact_viber', '');
            $label = 'Viber';
            break;
        case 'line':
            $url = kunaal_mod('kunaal_contact_line', '');
            $label = 'LINE';
            break;
        case 'telegram':
            $url = kunaal_mod('kunaal_contact_telegram', '');
            $label = 'Telegram';
            break;
        case 'signal':
            $url = kunaal_mod('kunaal_contact_signal', '');
            $label = 'Signal';
            break;
        case 'facebook':
            $url = kunaal_mod('kunaal_contact_facebook', '');
            $label = 'Facebook';
            break;
        case 'youtube':
            $url = kunaal_mod('kunaal_contact_youtube', '');
            $label = 'YouTube';
            break;
        case 'tiktok':
            $url = kunaal_mod('kunaal_contact_tiktok', '');
            $label = 'TikTok';
            break;
        case 'github':
            $url = kunaal_mod('kunaal_contact_github', '');
            $label = 'GitHub';
            break;
    }

    if (empty($url)) {
        return false;
    }

    return array(
        'url' => $url,
        'label' => $label,
    );
}

/**
 * Get SVG icon for a social platform
 *
 * @param string $platform Platform key
 * @return string SVG markup
 */
function kunaal_get_social_icon(string $platform): string {
    $icons = array(
        'email' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg>',
        'linkedin' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
        'twitter' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'instagram' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>',
        'whatsapp' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .925 4.57.925 10.127c0 1.79.413 3.47 1.155 4.965L0 24l9.115-2.064a11.86 11.86 0 005.05 1.115h.005c5.554 0 10.124-4.57 10.124-10.127 0-3.033-1.34-5.75-3.47-7.625"/></svg>',
        'viber' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.398.002C9.473.028 5.331.344 3.014 2.467 1.294 4.182.518 6.77.453 9.96c-.065 3.19-.141 9.168 5.618 10.804h.005l-.004 2.466s-.037.996.62 1.198c.792.243 1.258-.51 2.014-1.326.415-.447.988-1.105 1.42-1.608 3.912.328 6.919-.423 7.263-.534.791-.256 5.27-.83 5.997-6.774.75-6.126-.447-10.003-2.904-11.74-.001 0 0 0 0 0-1.63-1.2-5.455-1.907-9.084-1.444zm.115 1.754c3.272-.394 6.696.193 8.056 1.201 2.1 1.506 2.902 4.954 2.284 9.97-.62 5.088-4.376 5.473-5.04 5.687-.278.091-2.91.732-6.27.53 0 0-2.479 2.99-3.252 3.765-.122.121-.266.168-.36.148-.134-.029-.17-.168-.169-.372l.02-4.08s0-.001 0 0c-4.946-1.336-4.655-6.327-4.598-8.995.057-2.668.693-4.88 2.168-6.348 1.96-1.792 5.542-2.092 7.16-2.506zm.956 3.065c-.17 0-.307.135-.31.306-.003.174.133.317.306.32.893.016 1.735.347 2.371.933.636.587.996 1.378 1.01 2.228a.313.313 0 00.317.303h.004a.31.31 0 00.303-.316c-.017-1.023-.45-1.975-1.218-2.683-.769-.708-1.78-1.108-2.846-1.127-.011-.001-.025.036-.037.036zm-2.457.975c-.133 0-.265.026-.39.08l-.024.01c-.327.164-.593.378-.784.619-.191.243-.364.51-.515.799a.31.31 0 00.055.354c.074.066.143.137.218.2a7.91 7.91 0 00.49.406c.176.131.366.255.567.367.057.032.117.055.177.072a.504.504 0 00.48-.135.39.39 0 00.071-.103l.004-.007.034-.057c.14-.227.286-.455.414-.632.08-.11.16-.18.23-.218.083-.047.157-.053.242-.02.098.042.204.096.347.175l.246.143c.206.12.42.25.607.377.19.129.302.214.343.24.14.1.243.215.302.387a.944.944 0 01.028.507c-.051.256-.15.522-.282.758a2.15 2.15 0 01-.453.553c-.175.156-.379.3-.647.444-.118.064-.27.091-.428.082a2.076 2.076 0 01-.398-.065 4.09 4.09 0 01-.404-.131 8.03 8.03 0 01-.42-.178l-.033-.015a13.078 13.078 0 01-2.142-1.256 14.326 14.326 0 01-1.846-1.637l-.012-.014a11.043 11.043 0 01-.575-.674 12.222 12.222 0 01-.547-.765l-.008-.012a9.93 9.93 0 01-.627-1.16 6.017 6.017 0 01-.32-.89 2.776 2.776 0 01-.096-.502c-.011-.18.032-.315.102-.437.23-.41.485-.754.839-.989.171-.115.376-.2.611-.248a.41.41 0 01.228.016.43.43 0 01.179.119.9.9 0 01.131.193c.095.168.226.41.357.653l.154.284c.058.106.115.213.17.314l.131.238a.63.63 0 01.057.467.59.59 0 01-.278.37.9.9 0 01-.127.068zm3.316.024a.31.31 0 00-.3.318.31.31 0 00.316.3c.403-.009.783.139 1.073.417.29.278.456.654.468 1.056a.31.31 0 00.317.3h.003a.31.31 0 00.3-.317 2.1 2.1 0 00-.652-1.476 2.093 2.093 0 00-1.497-.599h-.028zm.106 1.152a.31.31 0 00-.287.329.31.31 0 00.328.287c.26-.018.493.18.518.44a.31.31 0 00.337.278.31.31 0 00.28-.335c-.055-.583-.559-1.021-1.145-.997h-.031z"/></svg>',
        'line' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.77.058 1.093l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/></svg>',
        'telegram' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
        'signal' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 3.6c2.318 0 4.2 1.882 4.2 4.2s-1.882 4.2-4.2 4.2-4.2-1.882-4.2-4.2 1.882-4.2 4.2-4.2zm0 16.8c-3 0-5.647-1.518-7.2-3.828.036-2.388 4.8-3.696 7.2-3.696s7.164 1.308 7.2 3.696c-1.553 2.31-4.2 3.828-7.2 3.828z"/></svg>',
        'facebook' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'youtube' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
        'github' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>',
    );

    return $icons[$platform] ?? '';
}

/**
 * Register Essay Ordering section and controls
 * 
 * Allows site owner to set default order for essays (manual, popularity, date, title).
 * Visitors can still override via the filter dropdown.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance
 */
function kunaal_customize_register_essay_ordering_section(WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('kunaal_essay_ordering', array(
        'title' => 'Essay Ordering',
        'priority' => 35,
        'description' => 'Set the default order for essays on archive and home pages. Visitors can still choose their own sort option via the filter dropdown.',
    ));

    $wp_customize->add_setting('kunaal_essay_default_order', array(
        'default' => 'date',
        'sanitize_callback' => function($value) {
            $allowed = array('manual', 'popular', 'date', 'title');
            return in_array($value, $allowed, true) ? $value : 'date';
        },
    ));

    $wp_customize->add_control('kunaal_essay_default_order', array(
        'label' => 'Default Order',
        'description' => 'How essays are ordered by default (visitors can override via filter)',
        'section' => 'kunaal_essay_ordering',
        'type' => 'select',
        'choices' => array(
            'date' => 'Date (newest first)',
            'popular' => 'Popularity (most views)',
            'manual' => 'Manual (drag-and-drop in admin)',
            'title' => 'Title (alphabetical)',
        ),
    ));
}




