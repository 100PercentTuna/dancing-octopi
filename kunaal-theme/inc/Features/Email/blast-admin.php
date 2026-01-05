<?php
/**
 * Subscriber Email Blast Admin
 *
 * Allows admins to compose an email and queue it to all confirmed subscribers.
 *
 * @package Kunaal_Theme
 * @since 5.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const KUNAAL_SUBSCRIBER_EMAILS_PAGE = 'kunaal-subscriber-emails';
const KUNAAL_SUBSCRIBER_EMAILS_NOTICE_TRANSIENT = 'kunaal_subscriber_emails_notice';
const KUNAAL_SUBSCRIBER_EMAILS_NONCE_ACTION = 'kunaal_subscriber_emails';
const KUNAAL_SUBSCRIBER_EMAILS_NONCE_ERROR = 'Invalid nonce.';

/**
 * Register Subscriber Emails admin page under Tools.
 */
function kunaal_subscriber_emails_admin_menu(): void {
    add_management_page(
        __('Subscriber Emails', 'kunaal-theme'),
        __('Subscriber Emails', 'kunaal-theme'),
        'manage_options',
        KUNAAL_SUBSCRIBER_EMAILS_PAGE,
        'kunaal_render_subscriber_emails_admin_page'
    );
}
add_action('admin_menu', 'kunaal_subscriber_emails_admin_menu');

function kunaal_subscriber_emails_notice_set(string $type, string $text): void {
    set_transient(KUNAAL_SUBSCRIBER_EMAILS_NOTICE_TRANSIENT, array('type' => $type, 'text' => $text), 60);
}

function kunaal_subscriber_emails_is_page(): bool {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- just routing
    if (!isset($_GET['page'])) {
        return false;
    }
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- just routing
    return sanitize_text_field(wp_unslash($_GET['page'])) === KUNAAL_SUBSCRIBER_EMAILS_PAGE;
}

/**
 * @return string Scheduled GMT datetime (Y-m-d H:i:s)
 */
function kunaal_subscriber_emails_compute_scheduled_gmt(string $send_mode, string $scheduled_local): string {
    $min_delay = function_exists('kunaal_subscribe_global_min_delay_minutes') ? kunaal_subscribe_global_min_delay_minutes() : 60;
    $now = time();
    $earliest = $now + ($min_delay * MINUTE_IN_SECONDS);

    $scheduled_ts = $earliest;
    if ($send_mode === 'time' && $scheduled_local !== '') {
        $scheduled_gmt = get_gmt_from_date($scheduled_local, KUNAAL_GMT_DATETIME_FORMAT);
        $ts = $scheduled_gmt !== '' ? strtotime($scheduled_gmt . ' UTC') : false;
        if ($ts !== false && $ts >= $earliest) {
            $scheduled_ts = $ts;
        }
    }

    return gmdate(KUNAAL_GMT_DATETIME_FORMAT, $scheduled_ts);
}

/**
 * Render a blast email for a specific subscriber.
 *
 * @param string $subject_tpl
 * @param string $body_tpl
 * @param array<string,string> $vars
 * @param string $unsubscribe_url
 * @return array{subject:string,body:string}
 */
function kunaal_subscriber_emails_render_for_vars(string $subject_tpl, string $body_tpl, array $vars, string $unsubscribe_url): array {
    $subject = function_exists('kunaal_email_apply_vars') && function_exists('kunaal_email_decode_entities')
        ? kunaal_email_apply_vars(kunaal_email_decode_entities($subject_tpl), $vars)
        : $subject_tpl;

    $body = function_exists('kunaal_email_apply_vars') && function_exists('kunaal_email_decode_entities')
        ? kunaal_email_apply_vars(kunaal_email_decode_entities($body_tpl), $vars)
        : $body_tpl;

    if (function_exists('kunaal_email_footer_text')) {
        $body .= kunaal_email_footer_text();
    }
    if ($unsubscribe_url !== '' && function_exists('kunaal_email_unsubscribe_line')) {
        $body .= kunaal_email_unsubscribe_line($unsubscribe_url);
    }

    return array('subject' => $subject, 'body' => $body);
}

function kunaal_subscriber_emails_handle_test(string $subject_tpl, string $body_tpl): void {
    $to = isset($_POST['test_to']) ? sanitize_email(wp_unslash($_POST['test_to'])) : '';
    if ($to === '' && function_exists('wp_get_current_user')) {
        $u = wp_get_current_user();
        $to = ($u && isset($u->user_email)) ? (string) $u->user_email : '';
    }
    if (!is_email($to)) {
        kunaal_subscriber_emails_notice_set('error', 'Provide a valid test email recipient.');
        return;
    }

    $site = function_exists('kunaal_email_decode_entities')
        ? kunaal_email_decode_entities((string) get_bloginfo('name'))
        : wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES);

    $vars = array(
        'site' => $site,
        'unsubscribe_url' => esc_url_raw(home_url('/')),
    );

    $rendered = kunaal_subscriber_emails_render_for_vars($subject_tpl, $body_tpl, $vars, '');
    $rendered['body'] .= "\n\n(Test send – unsubscribe link will be real in subscriber emails.)";

    $sent = wp_mail($to, $rendered['subject'], $rendered['body']);
    kunaal_subscriber_emails_notice_set($sent ? 'success' : 'error', $sent ? 'Test email sent.' : 'Test email failed to send.');
}

function kunaal_subscriber_emails_handle_queue(string $subject_tpl, string $body_tpl, string $scheduled_gmt): void {
    if (!function_exists('kunaal_subscribers_install_schema') || !function_exists('kunaal_subscribers_get_confirmed_batch') || !function_exists('kunaal_email_queue_insert')) {
        kunaal_subscriber_emails_notice_set('error', 'Subscriber system is unavailable.');
        return;
    }
    kunaal_subscribers_install_schema();

    $site = function_exists('kunaal_email_decode_entities')
        ? kunaal_email_decode_entities((string) get_bloginfo('name'))
        : wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES);

    $queued = 0;
    $offset = 0;
    $batch = 500;
    while (true) {
        $subs = kunaal_subscribers_get_confirmed_batch($offset, $batch);
        if (!$subs) {
            break;
        }
        foreach ($subs as $s) {
            $unsub_url = function_exists('kunaal_subscribe_unsubscribe_url')
                ? kunaal_subscribe_unsubscribe_url((int) $s['id'], (string) $s['email'])
                : '';

            $vars = array(
                'site' => $site,
                'unsubscribe_url' => esc_url_raw($unsub_url),
            );

            $rendered = kunaal_subscriber_emails_render_for_vars($subject_tpl, $body_tpl, $vars, $unsub_url);
            $res = kunaal_email_queue_insert(array(
                'type' => 'manual_blast',
                'subscriber_id' => (int) $s['id'],
                'subject' => $rendered['subject'],
                'body' => $rendered['body'],
                'scheduled_gmt' => $scheduled_gmt,
            ));
            if (!is_wp_error($res)) {
                $queued++;
            }
        }
        $offset += $batch;
    }

    kunaal_subscriber_emails_notice_set('success', 'Queued ' . (string) $queued . ' emails for delivery.');
}

/**
 * Handle admin POST actions for blast compose.
 */
function kunaal_subscriber_emails_admin_handle_post(): void {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    if (!kunaal_subscriber_emails_is_page()) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified below
    if (!isset($_POST['kunaal_subscriber_emails_action'])) {
        return;
    }

    $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
    if (!wp_verify_nonce($nonce, KUNAAL_SUBSCRIBER_EMAILS_NONCE_ACTION)) {
        wp_die(KUNAAL_SUBSCRIBER_EMAILS_NONCE_ERROR, 'Subscriber Emails', array('response' => 400));
    }

    $action = sanitize_text_field(wp_unslash($_POST['kunaal_subscriber_emails_action']));
    $subject_tpl = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : '';
    $body_tpl = isset($_POST['body']) ? sanitize_textarea_field(wp_unslash($_POST['body'])) : '';

    if ($subject_tpl === '' || $body_tpl === '') {
        kunaal_subscriber_emails_notice_set('error', 'Subject and body are required.');
        return;
    }

    // Determine schedule.
    $send_mode = isset($_POST['send_mode']) ? sanitize_text_field(wp_unslash($_POST['send_mode'])) : 'now';
    $scheduled_local = isset($_POST['scheduled_local']) ? sanitize_text_field(wp_unslash($_POST['scheduled_local'])) : '';
    $scheduled_gmt_final = kunaal_subscriber_emails_compute_scheduled_gmt($send_mode, $scheduled_local);

    // Test send (does not queue).
    if ($action === 'test') {
        kunaal_subscriber_emails_handle_test($subject_tpl, $body_tpl);
        return;
    }

    // Queue blast to all confirmed subscribers.
    if ($action === 'queue') {
        kunaal_subscriber_emails_handle_queue($subject_tpl, $body_tpl, $scheduled_gmt_final);
        return;
    }
}
add_action('admin_init', 'kunaal_subscriber_emails_admin_handle_post');

/**
 * Render queue overview table.
 */
function kunaal_render_email_queue_overview(): void {
    if (!function_exists('kunaal_email_queue_table')) {
        echo '<p>Email queue unavailable.</p>';
        return;
    }
    global $wpdb;
    $table = kunaal_email_queue_table();
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- table is internal
    $rows = $wpdb->get_results("SELECT id,type,status,scheduled_gmt,sent_gmt,attempts,last_error FROM {$table} ORDER BY id DESC LIMIT 50", ARRAY_A);
    if (!is_array($rows)) {
        $rows = array();
    }

    echo '<h2>Queue status (last 50)</h2>';
    echo '<table class="widefat striped"><thead><tr>';
    echo '<th>ID</th><th>Type</th><th>Status</th><th>Scheduled (UTC)</th><th>Sent (UTC)</th><th>Attempts</th><th>Last error</th>';
    echo '</tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td>' . esc_html((string) $r['id']) . '</td>';
        echo '<td>' . esc_html((string) $r['type']) . '</td>';
        echo '<td>' . esc_html((string) $r['status']) . '</td>';
        echo '<td>' . esc_html((string) $r['scheduled_gmt']) . '</td>';
        echo '<td>' . esc_html((string) $r['sent_gmt']) . '</td>';
        echo '<td>' . esc_html((string) $r['attempts']) . '</td>';
        echo '<td style="max-width:420px;white-space:normal;">' . esc_html((string) $r['last_error']) . '</td>';
        echo '</tr>';
    }
    if (!$rows) {
        echo '<tr><td colspan="7">Queue is empty.</td></tr>';
    }
    echo '</tbody></table>';
}

/**
 * Render the Subscriber Emails admin page.
 */
function kunaal_render_subscriber_emails_admin_page(): void {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized.', 'Subscriber Emails', array('response' => 403));
    }

    if (function_exists('kunaal_subscribers_install_schema')) {
        kunaal_subscribers_install_schema();
    }

    $notice = get_transient(KUNAAL_SUBSCRIBER_EMAILS_NOTICE_TRANSIENT);
    if (is_array($notice) && isset($notice['type'], $notice['text'])) {
        $class = $notice['type'] === 'success' ? 'notice notice-success' : 'notice notice-error';
        echo '<div class="' . esc_attr($class) . '"><p>' . esc_html((string) $notice['text']) . '</p></div>';
        delete_transient(KUNAAL_SUBSCRIBER_EMAILS_NOTICE_TRANSIENT);
    }

    $min_delay = function_exists('kunaal_subscribe_global_min_delay_minutes') ? kunaal_subscribe_global_min_delay_minutes() : 60;
    $earliest_local = date_i18n('Y-m-d\\TH:i', time() + ($min_delay * MINUTE_IN_SECONDS));

    echo '<div class="wrap">';
    echo '<h1>Subscriber Emails</h1>';
    echo '<p>Compose an email to all confirmed subscribers. Placeholders supported: <code>{site}</code>, <code>{unsubscribe_url}</code>.</p>';
    echo '<p><strong>Global minimum delay:</strong> ' . esc_html((string) $min_delay) . ' minutes (enforced).</p>';

    echo '<form method="post" action="' . esc_url(admin_url('tools.php?page=kunaal-subscriber-emails')) . '">';
    wp_nonce_field(KUNAAL_SUBSCRIBER_EMAILS_NONCE_ACTION);

    echo '<table class="form-table"><tbody>';
    echo '<tr><th scope="row"><label for="kunaal-subject">Subject</label></th><td>';
    echo '<input id="kunaal-subject" name="subject" type="text" class="regular-text" required value="" />';
    echo '</td></tr>';

    echo '<tr><th scope="row"><label for="kunaal-body">Body (plain text)</label></th><td>';
    echo '<textarea id="kunaal-body" name="body" rows="10" class="large-text code" required></textarea>';
    echo '</td></tr>';

    echo '<tr><th scope="row">Send timing</th><td>';
    echo '<label><input type="radio" name="send_mode" value="now" checked /> Queue at earliest allowed time</label><br/>';
    echo '<label><input type="radio" name="send_mode" value="time" /> Schedule a specific time (must be at least the earliest allowed time)</label><br/>';
    echo '<input type="datetime-local" name="scheduled_local" value="' . esc_attr($earliest_local) . '" />';
    echo '</td></tr>';

    echo '<tr><th scope="row">Test recipient</th><td>';
    echo '<input name="test_to" type="email" class="regular-text" placeholder="you@example.com" /> ';
    echo '<span class="description">Optional. If empty, uses your logged-in email.</span>';
    echo '</td></tr>';

    echo '</tbody></table>';

    echo '<p>';
    echo '<button class="button" type="submit" name="kunaal_subscriber_emails_action" value="test">Send test</button> ';
    echo '<button class="button button-primary" type="submit" name="kunaal_subscriber_emails_action" value="queue">Queue blast</button>';
    echo '</p>';
    echo '</form>';

    kunaal_render_email_queue_overview();

    echo '</div>';
}


