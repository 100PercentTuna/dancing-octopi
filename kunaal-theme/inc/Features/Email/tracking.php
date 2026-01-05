<?php
/**
 * Subscriber Email Tracking (Best-effort)
 *
 * WordPress-only constraint: we can do click tracking via redirect links and log events.
 * Open tracking via pixels is not reliable for plain-text email; we do not enable HTML emails by default.
 *
 * @package Kunaal_Theme
 * @since 5.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const KUNAAL_TRACKING_ERROR_INVALID = 'Invalid tracking link.';

/**
 * Enable click tracking.
 */
function kunaal_subscribe_click_tracking_enabled(): bool {
    return (bool) kunaal_mod('kunaal_subscribe_click_tracking', false);
}

/**
 * URL-safe base64 encode.
 */
function kunaal_b64url_encode(string $raw): string {
    return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
}

/**
 * URL-safe base64 decode.
 */
function kunaal_b64url_decode(string $encoded): string {
    $encoded = strtr($encoded, '-_', '+/');
    $pad = strlen($encoded) % 4;
    if ($pad) {
        $encoded .= str_repeat('=', 4 - $pad);
    }
    $decoded = base64_decode($encoded, true);
    return $decoded !== false ? $decoded : '';
}

/**
 * Create a signed click tracking URL.
 */
function kunaal_email_click_tracking_url(int $queue_id, int $subscriber_id, string $target_url): string {
    $payload = $queue_id . '|' . $subscriber_id . '|' . $target_url;
    $sig = hash_hmac('sha256', $payload, wp_salt('nonce'));
    return add_query_arg(
        array(
            'kunaal_track' => 'click',
            'qid' => (string) $queue_id,
            'sid' => (string) $subscriber_id,
            'u' => kunaal_b64url_encode($target_url),
            'sig' => $sig,
        ),
        home_url('/')
    );
}

/**
 * Log an email event (best-effort).
 */
function kunaal_email_event_log(int $subscriber_id, int $queue_id, string $event, string $url = ''): void {
    if (!function_exists('kunaal_email_events_table')) {
        return;
    }
    global $wpdb;
    $table = kunaal_email_events_table();
    $wpdb->insert(
        $table,
        array(
            'subscriber_id' => $subscriber_id,
            'queue_id' => $queue_id > 0 ? $queue_id : null,
            'event' => $event,
            'url' => $url !== '' ? $url : null,
            'ua_hash' => isset($_SERVER['HTTP_USER_AGENT']) ? hash('sha256', (string) $_SERVER['HTTP_USER_AGENT']) : null,
            'created_gmt' => gmdate(KUNAAL_GMT_DATETIME_FORMAT),
        ),
        array('%d', '%d', '%s', '%s', '%s', '%s')
    );
}

/**
 * Handle click tracking redirects.
 */
function kunaal_handle_email_click_tracking(): void {
    if (!kunaal_subscribe_click_tracking_enabled()) {
        return;
    }
    if (!isset($_GET['kunaal_track']) || sanitize_text_field(wp_unslash($_GET['kunaal_track'])) !== 'click') {
        return;
    }
    $qid = isset($_GET['qid']) ? absint(wp_unslash($_GET['qid'])) : 0;
    $sid = isset($_GET['sid']) ? absint(wp_unslash($_GET['sid'])) : 0;
    $u = isset($_GET['u']) ? sanitize_text_field(wp_unslash($_GET['u'])) : '';
    $sig = isset($_GET['sig']) ? sanitize_text_field(wp_unslash($_GET['sig'])) : '';

    if ($qid <= 0 || $sid <= 0 || $u === '' || $sig === '') {
        wp_die(KUNAAL_TRACKING_ERROR_INVALID, 'Tracking', array('response' => 400));
    }

    $target = kunaal_b64url_decode($u);
    if ($target === '' || !preg_match('#^https?://#i', $target)) {
        wp_die(KUNAAL_TRACKING_ERROR_INVALID, 'Tracking', array('response' => 400));
    }

    $payload = $qid . '|' . $sid . '|' . $target;
    $expected = hash_hmac('sha256', $payload, wp_salt('nonce'));
    if (!hash_equals($expected, $sig)) {
        wp_die(KUNAAL_TRACKING_ERROR_INVALID, 'Tracking', array('response' => 400));
    }

    kunaal_email_event_log($sid, $qid, 'click', $target);
    wp_safe_redirect($target, 302);
    exit;
}
add_action('template_redirect', 'kunaal_handle_email_click_tracking');


