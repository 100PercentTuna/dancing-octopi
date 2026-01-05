<?php
/**
 * Email Handlers
 * 
 * Handles contact form email sending and subscribe functionality.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper: Validate contact form request
 *
 * @return bool True if valid, false otherwise
 */
function kunaal_validate_contact_request(): bool {
    if (!isset($_POST['kunaal_contact_nonce'])) {
        return false;
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified below
    $nonce = sanitize_text_field(wp_unslash($_POST['kunaal_contact_nonce']));
    if (!wp_verify_nonce($nonce, 'kunaal_contact_form')) {
        return false;
    }
    return true;
}

/**
 * Helper: Sanitize contact form inputs
 */
function kunaal_sanitize_contact_inputs(): array {
    // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- all values sanitized below
    return array(
        'name' => isset($_POST['contact_name']) ? sanitize_text_field(wp_unslash($_POST['contact_name'])) : '',
        'email' => isset($_POST['contact_email']) ? sanitize_email(wp_unslash($_POST['contact_email'])) : '',
        'message' => isset($_POST['contact_message']) ? sanitize_textarea_field(wp_unslash($_POST['contact_message'])) : '',
        'honeypot' => isset($_POST['contact_company']) ? sanitize_text_field(wp_unslash($_POST['contact_company'])) : '',
    );
    // phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
}

/**
 * Helper: Check honeypot (bot detection)
 *
 * @param string $honeypot Honeypot field value
 * @return bool True if honeypot is empty (valid), false if filled (bot)
 */
function kunaal_check_contact_honeypot(string $honeypot): bool {
    return empty($honeypot);
}

/**
 * Helper: Get client IP address
 */
function kunaal_get_client_ip(): string {
    // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwarded = explode(',', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])));
        return trim($forwarded[0]);
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
    }
    // phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    return '';
}

/**
 * Helper: Check rate limit for contact form
 *
 * @return bool True if within rate limit, false if rate limited
 */
function kunaal_check_contact_rate_limit(): bool {
    $ip = kunaal_get_client_ip();
    if (empty($ip)) {
        return true; // Can't rate limit without IP, allow request
    }
    
    $rate_key = 'kunaal_contact_rl_' . wp_hash($ip);
    $count = (int) get_transient($rate_key);
    if ($count >= 5) {
        return false; // Rate limited
    }
    set_transient($rate_key, $count + 1, 10 * MINUTE_IN_SECONDS);
    return true;
}

/**
 * Helper: Validate contact form data
 *
 * @param string $message Message content
 * @param string $email Email address
 * @return array Array with 'valid' (bool) and 'error' (string) keys
 */
function kunaal_validate_contact_data(string $message, string $email): array {
    if (empty($message)) {
        return array('valid' => false, 'error' => 'Please enter a message.');
    }
    
    if (!empty($email) && !is_email($email)) {
        return array('valid' => false, 'error' => 'Please enter a valid email address.');
    }
    
    return array('valid' => true, 'error' => '');
}

/**
 * Helper: Get contact form recipient email
 */
function kunaal_get_contact_recipient(): string {
    $to_email = kunaal_mod('kunaal_contact_recipient_email', get_option('admin_email'));
    if (!is_email($to_email)) {
        $to_email = get_option('admin_email');
    }
    return $to_email;
}

/**
 * Helper: Build contact form email
 */
function kunaal_build_contact_email(string $name, string $email, string $message): array {
    $site_name = get_bloginfo('name');
    $sender_name = !empty($name) ? $name : 'Anonymous';
    $email_subject = '[' . $site_name . '] New note' . (!empty($name) ? ' from ' . $name : '');
    
    $email_body = "You received a new message from your site contact form.\n\n";
    if (!empty($name)) {
        $email_body .= "Name: {$name}\n";
    }
    if (!empty($email)) {
        $email_body .= "Email: {$email}\n";
    }
    $email_body .= "Page: " . esc_url_raw(wp_get_referer()) . "\n";
    $email_body .= "Time: " . gmdate('c') . " (UTC)\n\n";
    $email_body .= "Message:\n{$message}\n";
    
    // IMPORTANT: Do NOT set From header here - let the SMTP filter handle it.
    // O365 requires the From address to match the authenticated SMTP username.
    // The wp_mail_from filter in smtp-config.php sets this correctly.
    $headers = array();
    if (!empty($email)) {
        // Reply-To is safe to set - allows recipient to reply directly to the sender
        $headers[] = 'Reply-To: ' . $sender_name . ' <' . $email . '>';
    }
    
    return array(
        'to' => kunaal_get_contact_recipient(),
        'subject' => $email_subject,
        'body' => $email_body,
        'headers' => $headers,
    );
}

/**
 * Helper: Handle contact form email error
 */
function kunaal_handle_contact_email_error(string $to_email, string $email_subject): void {
    global $phpmailer;
    $error_message = 'Sorry, there was an error sending your message.';
    
    if (isset($phpmailer) && is_object($phpmailer) && isset($phpmailer->ErrorInfo)) {
        kunaal_theme_log('Contact form wp_mail error', array('error' => $phpmailer->ErrorInfo, 'to' => $to_email));
        $error_message .= ' Please check your email configuration or try emailing directly.';
    } else {
        kunaal_theme_log('Contact form wp_mail failed', array('to' => $to_email, 'subject' => $email_subject));
    }
    
    wp_send_json_error(array('message' => $error_message));
    wp_die();
}

/**
 * Contact Form AJAX Handler
 */
function kunaal_handle_contact_form(): void {
    try {
        if (!kunaal_validate_contact_request()) {
            wp_send_json_error(array('message' => 'Security check failed. Please refresh and try again.'));
            wp_die();
        }
        
        $inputs = kunaal_sanitize_contact_inputs();
        
        if (!kunaal_check_contact_honeypot($inputs['honeypot'])) {
            wp_send_json_error(array('message' => 'Sorry, your message could not be sent.'));
            wp_die();
        }

        // Fail-fast SMTP preflight (avoids 30s hangs on managed hosts when SMTP is unreachable)
        if (function_exists('kunaal_smtp_preflight_fast')) {
            $preflight = kunaal_smtp_preflight_fast();
            if (isset($preflight['ok']) && $preflight['ok'] === false) {
                wp_send_json_error(array('message' => $preflight['message'] . ' Please email directly.'));
                wp_die();
            }
        }
        
        if (!kunaal_check_contact_rate_limit()) {
            wp_send_json_error(array('message' => 'Please wait a bit before sending another message.'));
            wp_die();
        }
        
        $validation = kunaal_validate_contact_data($inputs['message'], $inputs['email']);
        if (!$validation['valid']) {
            wp_send_json_error(array('message' => $validation['error']));
            wp_die();
        }
        
        $email_data = kunaal_build_contact_email($inputs['name'], $inputs['email'], $inputs['message']);
        $sent = wp_mail($email_data['to'], $email_data['subject'], $email_data['body'], $email_data['headers']);
        
        if ($sent) {
            wp_send_json_success(array('message' => 'Thank you! Your message has been sent.'));
            wp_die();
        } else {
            kunaal_handle_contact_email_error($email_data['to'], $email_data['subject']);
        }
    } catch (\Throwable $e) {
        kunaal_theme_log('Contact form error', array('error' => $e->getMessage(), 'trace' => $e->getTraceAsString()));
        wp_send_json_error(array('message' => KUNAAL_ERROR_MESSAGE_GENERIC));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_contact_form', 'kunaal_handle_contact_form');
add_action('wp_ajax_nopriv_kunaal_contact_form', 'kunaal_handle_contact_form');

