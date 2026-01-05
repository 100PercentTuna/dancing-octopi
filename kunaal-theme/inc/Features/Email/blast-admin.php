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

/**
 * Register Subscriber Emails admin page under Tools.
 */
function kunaal_subscriber_emails_admin_menu(): void {
    add_management_page(
        __('Subscriber Emails', 'kunaal-theme'),
        __('Subscriber Emails', 'kunaal-theme'),
        'manage_options',
        'kunaal-subscriber-emails',
        'kunaal_render_subscriber_emails_admin_page'
    );
}
add_action('admin_menu', 'kunaal_subscriber_emails_admin_menu');

/**
 * Handle admin POST actions for blast compose.
 */
function kunaal_subscriber_emails_admin_handle_post(): void {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- verified below
    if (!isset($_GET['page']) || sanitize_text_field(wp_unslash($_GET['page'])) !== 'kunaal-subscriber-emails') {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified below
    if (!isset($_POST['kunaal_subscriber_emails_action'])) {
        return;
    }

    $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
    if (!wp_verify_nonce($nonce, 'kunaal_subscriber_emails')) {
        wp_die('Invalid nonce.', 'Subscriber Emails', array('response' => 400));
    }

    $action = sanitize_text_field(wp_unslash($_POST['kunaal_subscriber_emails_action']));
    $subject_tpl = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : '';
    $body_tpl = isset($_POST['body']) ? sanitize_textarea_field(wp_unslash($_POST['body'])) : '';

    if ($subject_tpl === '' || $body_tpl === '') {
        set_transient('kunaal_subscriber_emails_notice', array('type' => 'error', 'text' => 'Subject and body are required.'), 60);
        return;
    }

    // Determine schedule.
    $send_mode = isset($_POST['send_mode']) ? sanitize_text_field(wp_unslash($_POST['send_mode'])) : 'now';
    $scheduled_local = isset($_POST['scheduled_local']) ? sanitize_text_field(wp_unslash($_POST['scheduled_local'])) : '';

    $min_delay = function_exists('kunaal_subscribe_global_min_delay_minutes') ? kunaal_subscribe_global_min_delay_minutes() : 60;
    $now = time();
    $earliest = $now + ($min_delay * MINUTE_IN_SECONDS);

    $scheduled_ts = $earliest;
    if ($send_mode === 'time' && $scheduled_local !== '') {
        $scheduled_gmt = get_gmt_from_date($scheduled_local, 'Y-m-d H:i:s');
        $ts = $scheduled_gmt !== '' ? strtotime($scheduled_gmt . ' UTC') : false;
        if ($ts !== false && $ts >= $earliest) {
            $scheduled_ts = $ts;
        }
    }
    $scheduled_gmt_final = gmdate('Y-m-d H:i:s', $scheduled_ts);

    // Test send (does not queue).
    if ($action === 'test') {
        $to = isset($_POST['test_to']) ? sanitize_email(wp_unslash($_POST['test_to'])) : '';
        if ($to === '' && function_exists('wp_get_current_user')) {
            $u = wp_get_current_user();
            $to = ($u && isset($u->user_email)) ? (string) $u->user_email : '';
        }
        if (!is_email($to)) {
            set_transient('kunaal_subscriber_emails_notice', array('type' => 'error', 'text' => 'Provide a valid test email recipient.'), 60);
            return;
        }

        $site = function_exists('kunaal_email_decode_entities')
            ? kunaal_email_decode_entities((string) get_bloginfo('name'))
            : wp_specialchars_decode((string) get_bloginfo('name'), ENT_QUOTES);

        $vars = array(
            'site' => $site,
            'unsubscribe_url' => esc_url_raw(home_url('/')),
        );

        $subject = function_exists('kunaal_email_apply_vars') && function_exists('kunaal_email_decode_entities')
            ? kunaal_email_apply_vars(kunaal_email_decode_entities($subject_tpl), $vars)
            : $subject_tpl;

        $body = function_exists('kunaal_email_apply_vars') && function_exists('kunaal_email_decode_entities')
            ? kunaal_email_apply_vars(kunaal_email_decode_entities($body_tpl), $vars)
            : $body_tpl;

        if (function_exists('kunaal_email_footer_text')) {
            $body .= kunaal_email_footer_text();
        }
        $body .= "\n\n(Test send – unsubscribe link will be real in subscriber emails.)";

        $sent = wp_mail($to, $subject, $body);
        set_transient(
            'kunaal_subscriber_emails_notice',
            array('type' => $sent ? 'success' : 'error', 'text' => $sent ? 'Test email sent.' : 'Test email failed to send.'),
            60
        );
        return;
    }

    // Queue blast to all confirmed subscribers.
    if ($action === 'queue') {
        if (!function_exists('kunaal_subscribers_install_schema') || !function_exists('kunaal_subscribers_get_confirmed_batch') || !function_exists('kunaal_email_queue_insert')) {
            set_transient('kunaal_subscriber_emails_notice', array('type' => 'error', 'text' => 'Subscriber system is unavailable.'), 60);
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
                    : home_url('/');

                $vars = array(
                    'site' => $site,
                    'unsubscribe_url' => esc_url_raw($unsub_url),
                );

                $subject = function_exists('kunaal_email_apply_vars') && function_exists('kunaal_email_decode_entities')
                    ? kunaal_email_apply_vars(kunaal_email_decode_entities($subject_tpl), $vars)
                    : $subject_tpl;

                $body = function_exists('kunaal_email_apply_vars') && function_exists('kunaal_email_decode_entities')
                    ? kunaal_email_apply_vars(kunaal_email_decode_entities($body_tpl), $vars)
                    : $body_tpl;

                if (function_exists('kunaal_email_footer_text')) {
                    $body .= kunaal_email_footer_text();
                }
                if (function_exists('kunaal_email_unsubscribe_line')) {
                    $body .= kunaal_email_unsubscribe_line($unsub_url);
                }

                $res = kunaal_email_queue_insert(array(
                    'type' => 'manual_blast',
                    'subscriber_id' => (int) $s['id'],
                    'subject' => $subject,
                    'body' => $body,
                    'scheduled_gmt' => $scheduled_gmt_final,
                ));
                if (!is_wp_error($res)) {
                    $queued++;
                }
            }
            $offset += $batch;
        }

        set_transient(
            'kunaal_subscriber_emails_notice',
            array('type' => 'success', 'text' => 'Queued ' . (string) $queued . ' emails for delivery.'),
            60
        );
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

    $notice = get_transient('kunaal_subscriber_emails_notice');
    if (is_array($notice) && isset($notice['type'], $notice['text'])) {
        $class = $notice['type'] === 'success' ? 'notice notice-success' : 'notice notice-error';
        echo '<div class="' . esc_attr($class) . '"><p>' . esc_html((string) $notice['text']) . '</p></div>';
        delete_transient('kunaal_subscriber_emails_notice');
    }

    $min_delay = function_exists('kunaal_subscribe_global_min_delay_minutes') ? kunaal_subscribe_global_min_delay_minutes() : 60;
    $earliest_local = date_i18n('Y-m-d\\TH:i', time() + ($min_delay * MINUTE_IN_SECONDS));

    echo '<div class="wrap">';
    echo '<h1>Subscriber Emails</h1>';
    echo '<p>Compose an email to all confirmed subscribers. Placeholders supported: <code>{site}</code>, <code>{unsubscribe_url}</code>.</p>';
    echo '<p><strong>Global minimum delay:</strong> ' . esc_html((string) $min_delay) . ' minutes (enforced).</p>';

    echo '<form method="post" action="' . esc_url(admin_url('tools.php?page=kunaal-subscriber-emails')) . '">';
    wp_nonce_field('kunaal_subscriber_emails');

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


