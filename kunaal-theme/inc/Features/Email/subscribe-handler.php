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
 * Find subscriber by email address
 */
function kunaal_find_subscriber_by_email(string $email): int {
    $email = strtolower(trim($email));
    if (!is_email($email)) {
        return 0;
    }
    $q = new WP_Query(array(
        'post_type' => 'kunaal_subscriber',
        'post_status' => 'private',
        'fields' => 'ids',
        'posts_per_page' => 1,
        'meta_query' => array(
            array(
                'key' => 'kunaal_email',
                'value' => $email,
                'compare' => '=',
            ),
        ),
        'no_found_rows' => true,
    ));
    return !empty($q->posts) ? (int) $q->posts[0] : 0;
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
function kunaal_send_subscribe_confirmation(string $email, string $token): bool {
    $to = $email;
    $site = get_bloginfo('name');
    $confirm_url = add_query_arg(array('kunaal_sub_confirm' => $token), home_url('/'));
    $subject = '[' . $site . '] Confirm your subscription';
    $body = "Hi!\n\nPlease confirm your subscription by clicking the link below:\n\n" . esc_url_raw($confirm_url) . "\n\nIf you didn't request this, you can ignore this email.\n";
    
    $sent = wp_mail($to, $subject, $body);
    
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
    $status = get_post_meta($subscriber_id, 'kunaal_status', true);
    if ($status === 'confirmed') {
        wp_send_json_success(array('message' => 'You are already subscribed.'));
    } else {
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
    $subscriber_id = wp_insert_post(array(
        'post_type' => 'kunaal_subscriber',
        'post_status' => 'private',
        'post_title' => $email,
    ), true);

    if (is_wp_error($subscriber_id) || empty($subscriber_id)) {
        return new WP_Error('create_failed', 'Unable to create subscription. Please try again.');
    }

    update_post_meta($subscriber_id, 'kunaal_email', $email);
    update_post_meta($subscriber_id, 'kunaal_status', 'pending');
    update_post_meta($subscriber_id, 'kunaal_token', $token);
    update_post_meta($subscriber_id, 'kunaal_created_gmt', gmdate('c'));

    return $subscriber_id;
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

        // Check for existing subscriber
        $existing_id = kunaal_find_subscriber_by_email($email);
        if ($existing_id) {
            kunaal_handle_existing_subscriber($existing_id);
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

    $q = new WP_Query(array(
        'post_type' => 'kunaal_subscriber',
        'post_status' => 'private',
        'posts_per_page' => 1,
        'no_found_rows' => true,
        'meta_query' => array(
            array(
                'key' => 'kunaal_token',
                'value' => $token,
                'compare' => '=',
            ),
        ),
    ));

    if (empty($q->posts)) {
        wp_die('Invalid or expired confirmation link.', 'Subscription', array('response' => 400));
    }

    $post = $q->posts[0];
    update_post_meta($post->ID, 'kunaal_status', 'confirmed');
    delete_post_meta($post->ID, 'kunaal_token');

    // Notify admin (optional)
    $notify = kunaal_mod('kunaal_subscribe_notify_email', get_option('admin_email'));
    if (is_email($notify)) {
        $site = get_bloginfo('name');
        $email = get_post_meta($post->ID, 'kunaal_email', true);
        wp_mail($notify, '[' . $site . '] New subscriber', "New subscriber confirmed:\n\n" . $email . "\n");
    }

    wp_die('Subscription confirmed. Thank you!', 'Subscription', array('response' => 200));
}
add_action('template_redirect', 'kunaal_handle_subscribe_confirmation_request');

