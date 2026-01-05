<?php
/**
 * Subscriber Email Queue
 *
 * Queues and sends subscriber emails in batches via WP-Cron to avoid timeouts.
 *
 * @package Kunaal_Theme
 * @since 5.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const KUNAAL_EMAIL_QUEUE_EVENT = 'kunaal_email_queue_process';
const KUNAAL_EMAIL_QUEUE_CRON_SCHEDULE = 'kunaal_5min';

/**
 * Get global minimum delay for subscriber notifications (minutes).
 *
 * Hard rule: at least 60 minutes.
 */
function kunaal_subscribe_global_min_delay_minutes(): int {
    $min = (int) kunaal_mod('kunaal_subscribe_global_min_delay_minutes', 60);
    return max(60, $min);
}

/**
 * Get default delay for subscriber notifications (minutes) when post-level delay is not set.
 */
function kunaal_subscribe_default_delay_minutes(): int {
    $default = (int) kunaal_mod('kunaal_subscribe_default_delay_minutes', 0);
    if ($default > 0) {
        return $default;
    }
    // Back-compat: old setting was hours.
    $hours = (int) kunaal_mod('kunaal_subscribe_notify_delay_hours', 0);
    return max(0, $hours * 60);
}

/**
 * Add custom cron schedule.
 *
 * GoDaddy WP-Cron can be slow; 5 minutes is a realistic cadence.
 */
function kunaal_email_queue_cron_schedules(array $schedules): array {
    if (!isset($schedules['kunaal_5min'])) {
        $schedules['kunaal_5min'] = array(
            'interval' => 300,
            'display' => 'Every 5 minutes (Kunaal)',
        );
    }
    return $schedules;
}
add_filter('cron_schedules', 'kunaal_email_queue_cron_schedules');

/**
 * Ensure cron event is scheduled.
 */
function kunaal_email_queue_ensure_cron(): void {
    if (!wp_next_scheduled(KUNAAL_EMAIL_QUEUE_EVENT)) {
        wp_schedule_event(time() + 120, KUNAAL_EMAIL_QUEUE_CRON_SCHEDULE, KUNAAL_EMAIL_QUEUE_EVENT);
    }
}
add_action('init', 'kunaal_email_queue_ensure_cron');

/**
 * Insert a queued email row.
 *
 * @param array{type:string,subscriber_id:int,subject:string,body:string,scheduled_gmt:string,post_id?:int,headers?:array<int,string>} $email
 * @return int|WP_Error
 */
function kunaal_email_queue_insert(array $email): int|WP_Error {
    if (!function_exists('kunaal_email_queue_table')) {
        return new WP_Error('queue_unavailable', 'Email queue unavailable.');
    }
    global $wpdb;
    $table = kunaal_email_queue_table();

    $headers = isset($email['headers']) && is_array($email['headers']) ? wp_json_encode($email['headers']) : null;
    $post_id = isset($email['post_id']) ? (int) $email['post_id'] : null;

    $ok = $wpdb->insert(
        $table,
        array(
            'type' => (string) $email['type'],
            'subscriber_id' => (int) $email['subscriber_id'],
            'post_id' => $post_id,
            'subject' => (string) $email['subject'],
            'body' => (string) $email['body'],
            'headers_json' => $headers,
            'scheduled_gmt' => (string) $email['scheduled_gmt'],
            'attempts' => 0,
            'last_error' => null,
            'status' => 'queued',
            'created_gmt' => gmdate(KUNAAL_GMT_DATETIME_FORMAT),
            'sent_gmt' => null,
        ),
        array('%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
    );
    if ($ok === false) {
        return new WP_Error('queue_insert_failed', 'Failed to queue email.');
    }
    return (int) $wpdb->insert_id;
}

/**
 * Compute scheduled send time for a post notification.
 */
function kunaal_subscribe_compute_scheduled_gmt(int $post_id): string {
    $min_delay = kunaal_subscribe_global_min_delay_minutes();
    $now = time();

    $mode = (string) get_post_meta($post_id, 'kunaal_notify_mode', true);
    $mode = $mode !== '' ? $mode : 'delay';

    if ($mode === 'time') {
        $scheduled_gmt = (string) get_post_meta($post_id, 'kunaal_notify_scheduled_gmt', true);
        $ts = $scheduled_gmt !== '' ? strtotime($scheduled_gmt . ' UTC') : false;
        $earliest = $now + ($min_delay * MINUTE_IN_SECONDS);
        if ($ts === false || $ts < $earliest) {
            $ts = $earliest;
        }
        return gmdate(KUNAAL_GMT_DATETIME_FORMAT, $ts);
    }

    $delay_minutes = (int) get_post_meta($post_id, 'kunaal_notify_delay_minutes', true);
    if ($delay_minutes <= 0) {
        $delay_minutes = kunaal_subscribe_default_delay_minutes();
    }
    $delay_minutes = max($min_delay, $delay_minutes);
    return gmdate(KUNAAL_GMT_DATETIME_FORMAT, $now + ($delay_minutes * MINUTE_IN_SECONDS));
}

/**
 * Fetch due queue rows.
 *
 * @param int $limit
 * @param string $now_gmt
 * @return array<int,array<string,mixed>>
 */
function kunaal_email_queue_fetch_due(int $limit, string $now_gmt): array {
    global $wpdb;
    $table = kunaal_email_queue_table();

    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared below
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table} WHERE status = %s AND scheduled_gmt <= %s ORDER BY scheduled_gmt ASC, id ASC LIMIT %d",
            'queued',
            $now_gmt,
            $limit
        ),
        ARRAY_A
    );
    return is_array($rows) ? $rows : array();
}

/**
 * Mark row as sending and increment attempts.
 */
function kunaal_email_queue_mark_sending(int $queue_id, int $attempts): void {
    global $wpdb;
    $table = kunaal_email_queue_table();
    $wpdb->update(
        $table,
        array('status' => 'sending', 'attempts' => $attempts + 1),
        array('id' => $queue_id),
        array('%s', '%d'),
        array('%d')
    );
}

/**
 * Mark row as sent.
 */
function kunaal_email_queue_mark_sent(int $queue_id): void {
    global $wpdb;
    $table = kunaal_email_queue_table();
    $wpdb->update(
        $table,
        array('status' => 'sent', 'sent_gmt' => gmdate(KUNAAL_GMT_DATETIME_FORMAT), 'last_error' => null),
        array('id' => $queue_id),
        array('%s', '%s', '%s'),
        array('%d')
    );
}

/**
 * Mark row as failed.
 */
function kunaal_email_queue_mark_failed(int $queue_id, string $error): void {
    global $wpdb;
    $table = kunaal_email_queue_table();
    $wpdb->update(
        $table,
        array('status' => 'failed', 'last_error' => $error),
        array('id' => $queue_id),
        array('%s', '%s'),
        array('%d')
    );
}

/**
 * Extract headers from a queue row.
 *
 * @param array<string,mixed> $row
 * @return array<int,string>
 */
function kunaal_email_queue_headers_from_row(array $row): array {
    if (!isset($row['headers_json']) || !$row['headers_json']) {
        return array();
    }
    $decoded = json_decode((string) $row['headers_json'], true);
    return is_array($decoded) ? $decoded : array();
}

/**
 * Apply click tracking to the body for post notifications (best-effort).
 */
function kunaal_email_queue_apply_click_tracking(int $queue_id, int $subscriber_id, array $row, string $body): string {
    if (!function_exists('kunaal_subscribe_click_tracking_enabled') || !kunaal_subscribe_click_tracking_enabled()) {
        return $body;
    }
    $type = isset($row['type']) ? (string) $row['type'] : '';
    $post_id = isset($row['post_id']) ? (int) $row['post_id'] : 0;
    if ($type !== 'post_notify' || $post_id <= 0 || !function_exists('kunaal_email_click_tracking_url')) {
        return $body;
    }
    $post_url = get_permalink($post_id);
    if (!is_string($post_url) || $post_url === '') {
        return $body;
    }
    $tracked = kunaal_email_click_tracking_url($queue_id, $subscriber_id, $post_url);
    return str_replace($post_url, esc_url_raw($tracked), $body);
}

/**
 * Send a single queued email row.
 *
 * @param array<string,mixed> $row
 * @return void
 */
function kunaal_email_queue_send_row(array $row): void {
    $queue_id = isset($row['id']) ? (int) $row['id'] : 0;
    $subscriber_id = isset($row['subscriber_id']) ? (int) $row['subscriber_id'] : 0;
    if ($queue_id <= 0 || $subscriber_id <= 0) {
        return;
    }

    $attempts = isset($row['attempts']) ? (int) $row['attempts'] : 0;
    kunaal_email_queue_mark_sending($queue_id, $attempts);

    $subscriber = function_exists('kunaal_subscriber_get_by_id') ? kunaal_subscriber_get_by_id($subscriber_id) : null;
    $to = ($subscriber && isset($subscriber['email'])) ? (string) $subscriber['email'] : '';
    if (!is_email($to)) {
        kunaal_email_queue_mark_failed($queue_id, 'Invalid subscriber email.');
        return;
    }

    $subject = isset($row['subject']) ? (string) $row['subject'] : '';
    $body = isset($row['body']) ? (string) $row['body'] : '';
    $headers = kunaal_email_queue_headers_from_row($row);
    $body = kunaal_email_queue_apply_click_tracking($queue_id, $subscriber_id, $row, $body);

    $sent = wp_mail($to, $subject, $body, $headers);
    if ($sent) {
        kunaal_email_queue_mark_sent($queue_id);
        if (function_exists('kunaal_subscriber_touch_last_email_sent')) {
            kunaal_subscriber_touch_last_email_sent($subscriber_id);
        }
        return;
    }

    global $phpmailer;
    $err = (isset($phpmailer) && is_object($phpmailer) && isset($phpmailer->ErrorInfo)) ? (string) $phpmailer->ErrorInfo : 'wp_mail failed';
    kunaal_email_queue_mark_failed($queue_id, $err);
}

/**
 * Render a post notification email (plain text).
 *
 * @param array{id:int,email:string} $subscriber
 * @param WP_Post $post
 * @param string $post_url
 * @return array{subject:string,body:string}
 */
function kunaal_email_render_post_notify(array $subscriber, WP_Post $post, string $post_url): array {
    $site = function_exists('kunaal_email_decode_entities')
        ? kunaal_email_decode_entities((string) get_bloginfo('name'))
        : wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES);

    $title = function_exists('kunaal_email_decode_entities')
        ? kunaal_email_decode_entities((string) get_the_title($post))
        : wp_specialchars_decode((string) get_the_title($post), ENT_QUOTES);

    $subject_tpl = (string) kunaal_mod('kunaal_subscribe_post_subject', '[{site}] New: {title}');
    $body_tpl = (string) kunaal_mod(
        'kunaal_subscribe_post_body',
        "{title}\n\nRead: {url}\n\n—\nYou are receiving this because you subscribed to {site}."
    );

    $unsub_url = function_exists('kunaal_subscribe_unsubscribe_url')
        ? kunaal_subscribe_unsubscribe_url((int) $subscriber['id'], (string) $subscriber['email'])
        : '';

    $vars = array(
        'site' => $site,
        'title' => $title,
        'url' => esc_url_raw($post_url),
        'unsubscribe_url' => esc_url_raw($unsub_url),
    );

    if (function_exists('kunaal_email_apply_vars') && function_exists('kunaal_email_decode_entities')) {
        $subject = kunaal_email_apply_vars(kunaal_email_decode_entities($subject_tpl), $vars);
        $body = kunaal_email_apply_vars(kunaal_email_decode_entities($body_tpl), $vars);
        if (function_exists('kunaal_email_footer_text')) {
            $body .= kunaal_email_footer_text();
        }
        if ($unsub_url !== '' && function_exists('kunaal_email_unsubscribe_line')) {
            $body .= kunaal_email_unsubscribe_line($unsub_url);
        }
        return array('subject' => $subject, 'body' => $body);
    }

    // Fallback.
    $subject = '[' . $site . '] New: ' . $title;
    $body = $title . "\n\nRead: " . esc_url_raw($post_url);
    return array('subject' => $subject, 'body' => $body);
}

/**
 * Hook on publish to enqueue post notification emails.
 */
function kunaal_subscribe_maybe_enqueue_on_publish(string $new_status, string $old_status, WP_Post $post): void {
    if ($old_status === 'publish' || $new_status !== 'publish') {
        return;
    }
    if (!in_array($post->post_type, array('essay', 'jotting'), true)) {
        return;
    }
    $enabled = (bool) get_post_meta($post->ID, 'kunaal_notify_subscribers', true);
    if (!$enabled) {
        return;
    }
    if (!function_exists('kunaal_subscribers_install_schema')) {
        return;
    }
    kunaal_subscribers_install_schema();

    $scheduled_gmt = kunaal_subscribe_compute_scheduled_gmt((int) $post->ID);
    $url = get_permalink($post);
    if (!$url) {
        return;
    }

    // Enqueue for confirmed subscribers in batches.
    $offset = 0;
    $batch = 500;
    while (true) {
        $subs = function_exists('kunaal_subscribers_get_confirmed_batch')
            ? kunaal_subscribers_get_confirmed_batch($offset, $batch)
            : array();
        if (!$subs) {
            break;
        }
        foreach ($subs as $s) {
            $rendered = kunaal_email_render_post_notify($s, $post, $url);
            kunaal_email_queue_insert(array(
                'type' => 'post_notify',
                'subscriber_id' => (int) $s['id'],
                'post_id' => (int) $post->ID,
                'subject' => (string) $rendered['subject'],
                'body' => (string) $rendered['body'],
                'scheduled_gmt' => $scheduled_gmt,
            ));
        }
        $offset += $batch;
    }
}
add_action('transition_post_status', 'kunaal_subscribe_maybe_enqueue_on_publish', 10, 3);

/**
 * Process queued emails (cron worker).
 */
function kunaal_email_queue_process(): void {
    if (!function_exists('kunaal_email_queue_table')) {
        return;
    }
    $now = gmdate(KUNAAL_GMT_DATETIME_FORMAT);
    $limit = 30; // batch size per cron tick
    $rows = kunaal_email_queue_fetch_due($limit, $now);
    if (!$rows) {
        return;
    }

    foreach ($rows as $r) {
        kunaal_email_queue_send_row($r);
    }
}
add_action(KUNAAL_EMAIL_QUEUE_EVENT, 'kunaal_email_queue_process');


