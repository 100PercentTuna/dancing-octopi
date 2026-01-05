<?php
/**
 * Subscribe Handler
 * 
 * Handles subscription requests, confirmation emails, and subscriber management.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate subscribe confirmation token
 */
function kunaal_generate_subscribe_token(): string {
    return wp_generate_password(32, false, false);
}

/**
 * Send subscribe confirmation email
 * 
 * Logs PHPMailer errors on failure for debugging.
 */
function kunaal_subscribe_confirmation_preflight(string $email): bool {
    if (!function_exists('kunaal_smtp_preflight_fast')) {
        return true;
    }
    $preflight = kunaal_smtp_preflight_fast();
    if (!isset($preflight['ok']) || $preflight['ok'] !== false) {
        return true;
    }
    // Log reachability details (no secrets)
    if (function_exists('kunaal_theme_log')) {
        kunaal_theme_log('Subscribe confirmation preflight failed', array(
            'to' => $email,
            'details' => isset($preflight['details']) ? $preflight['details'] : array(),
        ));
    }
    return false;
}

function kunaal_send_subscribe_confirmation(string $email, string $token): bool {
    // Fail-fast SMTP preflight (avoids 30s hangs when SMTP host/port is unreachable)
    $smtp_unreachable = !kunaal_subscribe_confirmation_preflight($email);

    $to = $email;
    $confirm_url = add_query_arg(array('kunaal_sub_confirm' => $token), home_url('/'));

    // Render via template layer (ensures entity decoding, footer, unsubscribe line).
    $row = function_exists('kunaal_subscriber_get_by_email') ? kunaal_subscriber_get_by_email($email) : null;
    $subscriber = ($row && isset($row['id']) && isset($row['email']))
        ? array('id' => (int) $row['id'], 'email' => (string) $row['email'])
        : array('id' => 0, 'email' => $email);

    if (function_exists('kunaal_email_render_confirmation') && $subscriber['id'] > 0) {
        $rendered = kunaal_email_render_confirmation($subscriber, $confirm_url);
        $subject = $rendered['subject'];
        $body = $rendered['body'];
    } else {
        // Fallback (should be rare).
        $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $subject = '[' . $site . '] Confirm your subscription';
        $body = "Hi!\n\nPlease confirm your subscription by clicking the link below:\n\n" . esc_url_raw($confirm_url) . "\n\nIf you didn't request this, you can ignore this email.\n";
    }
    
    // If SMTP is unreachable, temporarily disable SMTP hook for this send and use PHP mail.
    if ($smtp_unreachable && function_exists('kunaal_action_phpmailer_init')) {
        remove_action('phpmailer_init', 'kunaal_action_phpmailer_init');
    }
    $sent = wp_mail($to, $subject, $body);
    if ($smtp_unreachable && function_exists('kunaal_action_phpmailer_init')) {
        add_action('phpmailer_init', 'kunaal_action_phpmailer_init');
    }
    
    // Log PHPMailer error on failure for debugging
    if (!$sent) {
        global $phpmailer;
        $error_info = '';
        if (isset($phpmailer) && is_object($phpmailer) && property_exists($phpmailer, 'ErrorInfo')) {
            $error_info = $phpmailer->ErrorInfo;
        }
        kunaal_theme_log('Subscribe confirmation email failed', array(
            'to' => $email,
            'error' => $error_info ?: 'Unknown error (PHPMailer ErrorInfo empty)',
        ));
    }
    
    return $sent;
}

/**
 * Rate-limit resend attempts (default 10 minutes).
 *
 * @param string $last_sent_gmt
 * @return bool True if resend is allowed
 */
function kunaal_subscribe_can_resend(string $last_sent_gmt): bool {
    if ($last_sent_gmt === '') {
        return true;
    }
    $ts = strtotime($last_sent_gmt);
    if ($ts === false) {
        return true;
    }
    return (time() - $ts) >= 10 * MINUTE_IN_SECONDS;
}

/**
 * Validate subscribe request nonce and mode
 *
 * @return array|WP_Error Returns error array on failure, null on success
 */
function kunaal_validate_subscribe_request(): array|null {
    if (empty($_POST['nonce'])) {
        return array('message' => 'Security check failed. Please refresh and try again.');
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified below
    $nonce = sanitize_text_field(wp_unslash($_POST['nonce']));
    if (!wp_verify_nonce($nonce, 'kunaal_theme_nonce')) {
        return array('message' => 'Security check failed. Please refresh and try again.');
    }

    $mode = kunaal_mod('kunaal_subscribe_mode', 'builtin');
    if ($mode === 'external') {
        return array('message' => 'Subscribe is configured for an external provider.');
    }

    return null;
}

/**
 * Validate and sanitize email from POST data
 *
 * @return string|WP_Error Valid email address or error
 */
function kunaal_validate_subscribe_email(): string|WP_Error {
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Please enter a valid email address.');
    }
    return strtolower(trim($email));
}

/**
 * Handle existing subscriber response
 *
 * @param int $subscriber_id Subscriber post ID
 * @return bool True if response sent, false otherwise
 */
function kunaal_handle_existing_subscriber(int $subscriber_id): bool {
    $row = function_exists('kunaal_subscriber_get_by_id') ? kunaal_subscriber_get_by_id($subscriber_id) : null;
    if (!$row) {
        wp_send_json_error(array('message' => 'Subscription record not found. Please try again.'));
        wp_die();
        return true;
    }

    $status = isset($row['status']) ? (string) $row['status'] : 'pending';
    if ($status === 'confirmed') {
        wp_send_json_success(array('message' => 'You are already subscribed.'));
    } else {
        $last_sent = isset($row['last_confirm_sent_gmt']) ? (string) $row['last_confirm_sent_gmt'] : '';
        if (!kunaal_subscribe_can_resend($last_sent)) {
            wp_send_json_success(array('message' => 'Check your inbox to confirm your subscription. (You can request a new email in a few minutes.)'));
            wp_die();
            return true;
        }

        $email = isset($row['email']) ? (string) $row['email'] : '';
        if (!is_email($email)) {
            wp_send_json_error(array('message' => 'Subscription record is invalid. Please try again later.'));
            wp_die();
            return true;
        }

        $token = kunaal_generate_subscribe_token();
        if (function_exists('kunaal_subscribers_hash_token') && function_exists('kunaal_subscriber_set_token_hash')) {
            $token_hash = kunaal_subscribers_hash_token($token);
            kunaal_subscriber_set_token_hash($subscriber_id, $token_hash);
        }

        $sent = kunaal_send_subscribe_confirmation($email, $token);
        if (!$sent) {
            wp_send_json_error(array('message' => 'Unable to send confirmation email. Please try again later.'));
            wp_die();
            return true;
        }

        wp_send_json_success(array('message' => 'Check your inbox to confirm your subscription.'));
    }
    wp_die();
    return true;
}

/**
 * Create new subscriber post with meta
 *
 * @param string $email Subscriber email
 * @param string $token Confirmation token
 * @return int|WP_Error Subscriber post ID or error
 */
function kunaal_create_subscriber_post(string $email, string $token): int|WP_Error {
    if (!function_exists('kunaal_subscriber_insert') || !function_exists('kunaal_subscribers_hash_token')) {
        return new WP_Error('db_missing', 'Subscription database is not available.');
    }

    $token_hash = kunaal_subscribers_hash_token($token);
    $source = isset($_POST['source']) ? sanitize_text_field(wp_unslash($_POST['source'])) : '';
    $id = kunaal_subscriber_insert($email, 'pending', $source, $token_hash);
    if (is_wp_error($id)) {
        return $id;
    }
    if (function_exists('kunaal_subscriber_set_token_hash')) {
        // Ensures last_confirm_sent_gmt is set.
        kunaal_subscriber_set_token_hash((int) $id, $token_hash);
    }
    return (int) $id;
}

/**
 * Main subscribe handler - refactored to reduce cognitive complexity
 */
function kunaal_handle_subscribe(): void {
    try {
        // Validate request
        $validation_error = kunaal_validate_subscribe_request();
        if ($validation_error) {
            wp_send_json_error($validation_error);
            wp_die();
        }

        // Validate email
        $email = kunaal_validate_subscribe_email();
        if (is_wp_error($email)) {
            wp_send_json_error(array('message' => $email->get_error_message()));
            wp_die();
        }

        // Check for existing subscriber (DB).
        $existing = function_exists('kunaal_subscriber_get_by_email') ? kunaal_subscriber_get_by_email($email) : null;
        if ($existing && isset($existing['id'])) {
            kunaal_handle_existing_subscriber((int) $existing['id']);
            return;
        }

        // Create new subscriber
        $token = kunaal_generate_subscribe_token();
        $subscriber_id = kunaal_create_subscriber_post($email, $token);
        if (is_wp_error($subscriber_id)) {
            wp_send_json_error(array('message' => $subscriber_id->get_error_message()));
            wp_die();
        }

        // Send confirmation email
        $sent = kunaal_send_subscribe_confirmation($email, $token);
        if (!$sent) {
            wp_send_json_error(array('message' => 'Unable to send confirmation email. Please try again later.'));
            wp_die();
        }

        wp_send_json_success(array('message' => 'Check your inbox to confirm your subscription.'));
        wp_die();
    } catch (\Throwable $e) {
        kunaal_theme_log('Subscribe error', array('error' => $e->getMessage()));
        wp_send_json_error(array('message' => KUNAAL_ERROR_MESSAGE_GENERIC));
        wp_die();
    }
}
add_action('wp_ajax_kunaal_subscribe', 'kunaal_handle_subscribe');
add_action('wp_ajax_nopriv_kunaal_subscribe', 'kunaal_handle_subscribe');

/**
 * Handle subscribe confirmation request
 */
function kunaal_handle_subscribe_confirmation_request(): void {
    if (empty($_GET['kunaal_sub_confirm'])) {
        return;
    }
    $token = sanitize_text_field(wp_unslash($_GET['kunaal_sub_confirm']));
    if (empty($token)) {
        return;
    }

    if (!function_exists('kunaal_subscribers_hash_token') || !function_exists('kunaal_subscriber_get_by_token_hash')) {
        wp_die('Subscription system is unavailable.', 'Subscription', array('response' => 500));
    }

    $hash = kunaal_subscribers_hash_token($token);
    $row = kunaal_subscriber_get_by_token_hash($hash);
    if (!$row || !isset($row['id'])) {
        wp_die('Invalid or expired confirmation link.', 'Subscription', array('response' => 400));
    }

    $id = (int) $row['id'];
    if (function_exists('kunaal_subscriber_update_status')) {
        kunaal_subscriber_update_status($id, 'confirmed');
    }

    // Notify admin (optional)
    $notify = kunaal_mod('kunaal_subscribe_notify_email', get_option('admin_email'));
    if (is_email($notify)) {
        $site = get_bloginfo('name');
        $email = isset($row['email']) ? (string) $row['email'] : '';
        if (is_email($email)) {
            wp_mail($notify, '[' . $site . '] New subscriber', "New subscriber confirmed:\n\n" . $email . "\n");
        }
    }

    wp_die('Subscription confirmed. Thank you!', 'Subscription', array('response' => 200));
}
add_action('template_redirect', 'kunaal_handle_subscribe_confirmation_request');

/**
 * Signed unsubscribe link handler.
 *
 * URL format:
 *   /?kunaal_unsub=1&sid=123&sig=...
 */
function kunaal_handle_subscribe_unsubscribe_request(): void {
    if (empty($_GET['kunaal_unsub']) || empty($_GET['sid']) || empty($_GET['sig'])) {
        return;
    }

    $sid = absint(wp_unslash($_GET['sid']));
    $sig = sanitize_text_field(wp_unslash($_GET['sig']));
    if ($sid <= 0 || $sig === '') {
        return;
    }

    if (!function_exists('kunaal_subscriber_get_by_id') || !function_exists('kunaal_subscriber_update_status')) {
        wp_die('Subscription system is unavailable.', 'Unsubscribe', array('response' => 500));
    }

    $row = kunaal_subscriber_get_by_id($sid);
    if (!$row || !isset($row['email'])) {
        wp_die('Invalid unsubscribe link.', 'Unsubscribe', array('response' => 400));
    }

    $email = (string) $row['email'];
    $expected = hash_hmac('sha256', $sid . '|' . strtolower($email), wp_salt('nonce'));
    if (!hash_equals($expected, $sig)) {
        wp_die('Invalid unsubscribe link.', 'Unsubscribe', array('response' => 400));
    }

    kunaal_subscriber_update_status($sid, 'unsubscribed');
    wp_die('You have been unsubscribed.', 'Unsubscribe', array('response' => 200));
}
add_action('template_redirect', 'kunaal_handle_subscribe_unsubscribe_request');

