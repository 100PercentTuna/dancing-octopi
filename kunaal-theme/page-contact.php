<?php
/**
 * Template Name: Contact Page
 *
 * Elegant note-taking interface with optional name/email
 * Polished design matching site aesthetic
 *
 * @package Kunaal_Theme
 */

get_header();

// Get enabled social links using helper function
$social_platforms = array('email', 'linkedin', 'twitter', 'instagram', 'whatsapp', 'viber', 'line', 'telegram', 'signal', 'facebook', 'youtube', 'tiktok', 'github');
$enabled_socials = array();
foreach ($social_platforms as $platform) {
    $social_data = kunaal_get_social_link($platform);
    if ($social_data) {
        $enabled_socials[$platform] = $social_data;
    }
}

// Contact-specific settings
$contact_label = kunaal_mod('kunaal_contact_label', __('Note', 'kunaal-theme'));
$contact_headline = kunaal_mod('kunaal_contact_headline', __('Say hello', 'kunaal-theme'));
$contact_headline_show = kunaal_mod('kunaal_contact_headline_show', true);
$contact_headline_size = kunaal_mod('kunaal_contact_headline_size', 'large');
$contact_placeholder = kunaal_mod('kunaal_contact_placeholder', __('Leave a note...', 'kunaal-theme'));

// Map size setting to CSS class
$headline_size_class = 'contact-title--' . $contact_headline_size;
?>
<main id="main" class="contact-page">
    <div class="contact-container">
        <div class="contact-card">
            <header class="contact-header">
                <?php if ($contact_label) : ?>
                <div class="contact-label" data-reveal="up"><?php echo esc_html($contact_label); ?></div>
                <?php endif; ?>
                <?php if ($contact_headline_show && $contact_headline) : ?>
                <h1 class="contact-title <?php echo esc_attr($headline_size_class); ?>" data-reveal="up"><?php echo esc_html($contact_headline); ?></h1>
                <?php endif; ?>
            </header>
            
            <form id="contact-form" class="contact-form" method="post" novalidate>
                <?php wp_nonce_field('kunaal_contact_form', 'kunaal_contact_nonce'); ?>
                
                <!-- Honeypot -->
                <div class="contact-honeypot" aria-hidden="true">
                    <input type="text" name="contact_company" tabindex="-1" autocomplete="off">
                </div>
                
                <!-- Message textarea (always visible) -->
                <div class="contact-field contact-message-field">
                    <textarea
                        id="contact-message"
                        name="contact_message"
                        rows="5"
                        required
                        placeholder="<?php echo esc_attr($contact_placeholder); ?>"
                        data-reveal="up"
                    ></textarea>
                </div>
                
                <!-- Optional name/email checkbox -->
                <div class="contact-optional" data-reveal="up">
                    <label class="contact-checkbox-label">
                        <input type="checkbox" id="contact-include-info" class="contact-checkbox">
                        <span class="contact-checkbox-custom"></span>
                        <span class="contact-checkbox-text"><?php esc_html_e('Include my name and email', 'kunaal-theme'); ?></span>
                    </label>
                </div>
                
                <!-- Name and email fields (hidden by default, animated reveal) -->
                <div class="contact-optional-fields" id="contact-optional-fields" style="display: none;">
                    <div class="contact-field" data-reveal="up">
                        <input
                            type="text"
                            id="contact-name"
                            name="contact_name"
                            placeholder="<?php esc_attr_e('Your name', 'kunaal-theme'); ?>"
                        >
                    </div>
                    <div class="contact-field" data-reveal="up">
                        <input
                            type="email"
                            id="contact-email"
                            name="contact_email"
                            placeholder="<?php esc_attr_e('Your email', 'kunaal-theme'); ?>"
                        >
                    </div>
                </div>
                
                <!-- Submit button with paper airplane icon -->
                <button type="submit" class="contact-submit" id="contact-submit" data-reveal="up">
                    <svg class="contact-submit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    <span class="contact-submit-text"><?php esc_html_e('Send', 'kunaal-theme'); ?></span>
                    <span class="contact-submit-loading" style="display: none;"><?php esc_html_e('Sending...', 'kunaal-theme'); ?></span>
                    <span class="contact-submit-success" style="display: none;"><?php esc_html_e('Sent', 'kunaal-theme'); ?></span>
                </button>
                
                <div class="contact-status" id="contact-status" aria-live="polite"></div>
            </form>
            
            <!-- Social Links - dynamically rendered based on admin settings -->
            <?php if (!empty($enabled_socials)) : ?>
            <div class="contact-social" data-reveal="up">
                <p class="contact-social-label"><?php esc_html_e('Or reach out via', 'kunaal-theme'); ?></p>
                <div class="contact-social-links">
                    <?php foreach ($enabled_socials as $platform => $data) : ?>
                    <a href="<?php echo esc_url($data['url']); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php echo esc_attr($data['label']); ?>">
                        <?php echo kunaal_get_social_icon($platform); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG is hardcoded ?>
                        <span><?php echo esc_html($data['label']); ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
// Contact page CSS and JS are now enqueued in functions.php
// See: kunaal_enqueue_assets() for conditional loading
?>

<?php get_footer(); ?>
