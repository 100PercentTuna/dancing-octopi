<?php
/**
 * Subscriber Admin Pages
 *
 * Minimal admin UI for viewing/exporting/adding/unsubscribing subscribers.
 *
 * @package Kunaal_Theme
 * @since 5.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const KUNAAL_SUBSCRIBERS_ADMIN_PAGE = 'kunaal-subscribers';
const KUNAAL_SUBSCRIBERS_ADMIN_BASE = 'tools.php?page=kunaal-subscribers';
const KUNAAL_SUBSCRIBERS_NONCE_ERROR = 'Invalid nonce.';

function kunaal_subscribers_is_admin_page(): bool {
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- routing only
    if (!isset($_GET['page'])) {
        return false;
    }
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- routing only
    return sanitize_text_field(wp_unslash($_GET['page'])) === KUNAAL_SUBSCRIBERS_ADMIN_PAGE;
}

function kunaal_subscribers_admin_redirect(): void {
    wp_safe_redirect(admin_url(KUNAAL_SUBSCRIBERS_ADMIN_BASE));
    exit;
}

/**
 * Register Subscribers admin page under Tools.
 */
function kunaal_subscribers_admin_menu(): void {
    add_management_page(
        __('Subscribers', 'kunaal-theme'),
        __('Subscribers', 'kunaal-theme'),
        'manage_options',
        'kunaal-subscribers',
        'kunaal_render_subscribers_admin_page'
    );
}
add_action('admin_menu', 'kunaal_subscribers_admin_menu');

/**
 * Handle admin actions (add/unsubscribe/export).
 */
function kunaal_subscribers_admin_handle_actions(): void {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    if (!kunaal_subscribers_is_admin_page()) {
        return;
    }

    // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only routing
    $action = isset($_GET['action']) ? sanitize_text_field(wp_unslash($_GET['action'])) : '';

    // Export CSV
    if ($action === 'export') {
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        if (!wp_verify_nonce($nonce, 'kunaal_subscribers_export')) {
            wp_die(KUNAAL_SUBSCRIBERS_NONCE_ERROR, 'Subscribers', array('response' => 400));
        }
        kunaal_subscribers_admin_export_csv();
        exit;
    }

    // Unsubscribe action
    if ($action === 'unsubscribe' && isset($_GET['id'])) {
        $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
        if (!wp_verify_nonce($nonce, 'kunaal_subscribers_unsub')) {
            wp_die(KUNAAL_SUBSCRIBERS_NONCE_ERROR, 'Subscribers', array('response' => 400));
        }
        $id = absint(wp_unslash($_GET['id']));
        if ($id > 0 && function_exists('kunaal_subscriber_update_status')) {
            kunaal_subscriber_update_status($id, 'unsubscribed');
        }
        kunaal_subscribers_admin_redirect();
    }

    // Add subscriber (POST)
    // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified below
    if (isset($_POST['kunaal_subscribers_add']) && $_POST['kunaal_subscribers_add'] === '1') {
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if (!wp_verify_nonce($nonce, 'kunaal_subscribers_add')) {
            wp_die(KUNAAL_SUBSCRIBERS_NONCE_ERROR, 'Subscribers', array('response' => 400));
        }
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $status = isset($_POST['status']) ? sanitize_text_field(wp_unslash($_POST['status'])) : 'confirmed';
        $status = in_array($status, array('pending', 'confirmed', 'unsubscribed'), true) ? $status : 'confirmed';

        if (is_email($email) && function_exists('kunaal_subscriber_get_by_email') && function_exists('kunaal_subscriber_insert')) {
            $existing = kunaal_subscriber_get_by_email($email);
            if (!$existing) {
                $id = kunaal_subscriber_insert($email, $status, 'admin', null);
                if (!is_wp_error($id) && $status === 'confirmed' && function_exists('kunaal_subscriber_update_status')) {
                    kunaal_subscriber_update_status((int) $id, 'confirmed');
                }
            }
        }
        kunaal_subscribers_admin_redirect();
    }
}
add_action('admin_init', 'kunaal_subscribers_admin_handle_actions');

/**
 * Export subscribers as CSV.
 */
function kunaal_subscribers_admin_export_csv(): void {
    if (!function_exists('kunaal_subscribers_list')) {
        wp_die('Subscriber system unavailable.', 'Subscribers', array('response' => 500));
    }
    $data = kunaal_subscribers_list(array('limit' => 500, 'offset' => 0));
    $rows = $data['rows'];

    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="kunaal-subscribers.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, array('email', 'status', 'created_gmt', 'confirmed_gmt', 'unsubscribed_gmt', 'last_email_sent_gmt'));
    foreach ($rows as $r) {
        fputcsv($out, array(
            isset($r['email']) ? (string) $r['email'] : '',
            isset($r['status']) ? (string) $r['status'] : '',
            isset($r['created_gmt']) ? (string) $r['created_gmt'] : '',
            isset($r['confirmed_gmt']) ? (string) $r['confirmed_gmt'] : '',
            isset($r['unsubscribed_gmt']) ? (string) $r['unsubscribed_gmt'] : '',
            isset($r['last_email_sent_gmt']) ? (string) $r['last_email_sent_gmt'] : '',
        ));
    }
    fclose($out);
}

/**
 * Render the Subscribers admin page.
 */
function kunaal_render_subscribers_admin_page(): void {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized.', 'Subscribers', array('response' => 403));
    }

    if (!function_exists('kunaal_subscribers_install_schema')) {
        echo '<div class="wrap"><h1>Subscribers</h1><p>Subscriber system unavailable.</p></div>';
        return;
    }

    // Ensure schema and migration are attempted (safe).
    kunaal_subscribers_install_schema();
    if (function_exists('kunaal_subscribers_migrate_from_cpt')) {
        kunaal_subscribers_migrate_from_cpt();
    }

    $status = isset($_GET['status']) ? sanitize_text_field(wp_unslash($_GET['status'])) : '';
    $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

    $page = isset($_GET['paged']) ? max(1, absint(wp_unslash($_GET['paged']))) : 1;
    $per_page = 50;
    $offset = ($page - 1) * $per_page;

    $list = function_exists('kunaal_subscribers_list')
        ? kunaal_subscribers_list(array('status' => $status, 'search' => $search, 'offset' => $offset, 'limit' => $per_page))
        : array('rows' => array(), 'total' => 0);

    $total = (int) $list['total'];
    $rows = (array) $list['rows'];
    $total_pages = max(1, (int) ceil($total / $per_page));

    $export_url = wp_nonce_url(admin_url(KUNAAL_SUBSCRIBERS_ADMIN_BASE . '&action=export'), 'kunaal_subscribers_export');

    echo '<div class="wrap">';
    echo '<h1>Subscribers</h1>';
    echo '<p><a class="button" href="' . esc_url($export_url) . '">Export CSV</a></p>';

    // Add form.
    echo '<h2>Add subscriber</h2>';
    echo '<form method="post" action="' . esc_url(admin_url(KUNAAL_SUBSCRIBERS_ADMIN_BASE)) . '">';
    wp_nonce_field('kunaal_subscribers_add');
    echo '<input type="hidden" name="kunaal_subscribers_add" value="1" />';
    echo '<input type="email" name="email" placeholder="email@example.com" required style="min-width:280px" /> ';
    echo '<select name="status">';
    echo '<option value="confirmed">confirmed</option>';
    echo '<option value="pending">pending</option>';
    echo '<option value="unsubscribed">unsubscribed</option>';
    echo '</select> ';
    echo '<button class="button button-primary" type="submit">Add</button>';
    echo '</form>';

    // Search/filter.
    echo '<hr/>';
    echo '<form method="get" style="margin: 12px 0;">';
    echo '<input type="hidden" name="page" value="' . esc_attr(KUNAAL_SUBSCRIBERS_ADMIN_PAGE) . '" />';
    echo '<input type="search" name="s" value="' . esc_attr($search) . '" placeholder="Search email" /> ';
    echo '<select name="status">';
    echo '<option value="">all</option>';
    foreach (array('pending', 'confirmed', 'unsubscribed') as $st) {
        $sel = ($status === $st) ? ' selected' : '';
        echo '<option value="' . esc_attr($st) . '"' . $sel . '>' . esc_html($st) . '</option>';
    }
    echo '</select> ';
    echo '<button class="button" type="submit">Filter</button>';
    echo '</form>';

    // Table.
    echo '<table class="widefat striped">';
    echo '<thead><tr>';
    echo '<th>Email</th><th>Status</th><th>Created (UTC)</th><th>Confirmed (UTC)</th><th>Unsubscribed (UTC)</th><th>Actions</th>';
    echo '</tr></thead><tbody>';

    foreach ($rows as $r) {
        $id = isset($r['id']) ? (int) $r['id'] : 0;
        $email = isset($r['email']) ? (string) $r['email'] : '';
        $st = isset($r['status']) ? (string) $r['status'] : '';
        $created = isset($r['created_gmt']) ? (string) $r['created_gmt'] : '';
        $confirmed = isset($r['confirmed_gmt']) ? (string) $r['confirmed_gmt'] : '';
        $unsub = isset($r['unsubscribed_gmt']) ? (string) $r['unsubscribed_gmt'] : '';

        $unsub_url = $id > 0
            ? wp_nonce_url(admin_url(KUNAAL_SUBSCRIBERS_ADMIN_BASE . '&action=unsubscribe&id=' . $id), 'kunaal_subscribers_unsub')
            : '';

        echo '<tr>';
        echo '<td>' . esc_html($email) . '</td>';
        echo '<td>' . esc_html($st) . '</td>';
        echo '<td>' . esc_html($created) . '</td>';
        echo '<td>' . esc_html($confirmed) . '</td>';
        echo '<td>' . esc_html($unsub) . '</td>';
        echo '<td>';
        if ($st !== 'unsubscribed' && $unsub_url !== '') {
            echo '<a class="button button-small" href="' . esc_url($unsub_url) . '">Unsubscribe</a>';
        } else {
            echo '—';
        }
        echo '</td>';
        echo '</tr>';
    }
    if (!$rows) {
        echo '<tr><td colspan="6">No subscribers found.</td></tr>';
    }

    echo '</tbody></table>';

    // Pagination.
    if ($total_pages > 1) {
        $base = admin_url(KUNAAL_SUBSCRIBERS_ADMIN_BASE);
        $query = array();
        if ($status !== '') {
            $query['status'] = $status;
        }
        if ($search !== '') {
            $query['s'] = $search;
        }

        echo '<div style="margin-top:12px;">';
        echo '<span>Page ' . esc_html((string) $page) . ' of ' . esc_html((string) $total_pages) . '</span> ';
        if ($page > 1) {
            $query['paged'] = $page - 1;
            echo '<a class="button" href="' . esc_url(add_query_arg($query, $base)) . '">Prev</a> ';
        }
        if ($page < $total_pages) {
            $query['paged'] = $page + 1;
            echo '<a class="button" href="' . esc_url(add_query_arg($query, $base)) . '">Next</a>';
        }
        echo '</div>';
    }

    echo '</div>';
}


