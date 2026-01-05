<?php
/**
 * SMTP Diagnostics
 *
 * Adds a small, deterministic connectivity test for SMTP so email failures
 * (timeouts, blocked ports) are actionable on managed hosts.
 *
 * Admin-only via AJAX: action=kunaal_smtp_diagnostics
 *
 * @package Kunaal_Theme
 * @since 4.99.9
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Resolve SMTP settings from wp-config.php constants (preferred) or Customizer GUI.
 *
 * @return array{host:string,port:int,enc:string,user:string}
 */
function kunaal_smtp_resolve_settings(): array {
    $has_config_creds = defined('KUNAAL_SMTP_HOST') &&
        defined('KUNAAL_SMTP_USER') &&
        defined('KUNAAL_SMTP_PASS');

    if ($has_config_creds) {
        $host = (string) KUNAAL_SMTP_HOST;
        $user = (string) KUNAAL_SMTP_USER;
        $port = defined('KUNAAL_SMTP_PORT') ? (int) KUNAAL_SMTP_PORT : 587;
        $enc  = defined('KUNAAL_SMTP_SECURE') ? (string) KUNAAL_SMTP_SECURE : 'tls';
    } else {
        $host = (string) kunaal_mod('kunaal_smtp_host_gui', '');
        $user = (string) kunaal_mod('kunaal_smtp_username_gui', '');
        $port = (int) kunaal_mod('kunaal_smtp_port_gui', 587);
        $enc  = (string) kunaal_mod('kunaal_smtp_encryption_gui', 'tls');
    }

    return array(
        'host' => $host,
        'port' => $port > 0 ? $port : 587,
        'enc'  => $enc !== '' ? $enc : 'tls',
        'user' => $user,
    );
}

/**
 * Lightweight TCP reachability test for SMTP host/port.
 * This intentionally does NOT authenticate or send credentials.
 *
 * @param int $timeout_seconds Connection timeout
 * @return array{ok:bool,host:string,port:int,ip:string,error:string}
 */
function kunaal_smtp_test_tcp_connectivity(int $timeout_seconds = 6): array {
    $s = kunaal_smtp_resolve_settings();
    $host = $s['host'];
    $port = (int) $s['port'];

    if ($host === '' || $port <= 0) {
        return array(
            'ok' => false,
            'host' => $host,
            'port' => $port,
            'ip' => '',
            'error' => 'Missing SMTP host/port.',
        );
    }

    $ip = gethostbyname($host);
    if ($ip === $host) {
        // DNS resolution failed (gethostbyname returns input on failure)
        return array(
            'ok' => false,
            'host' => $host,
            'port' => $port,
            'ip' => '',
            'error' => 'DNS resolution failed for host.',
        );
    }

    $errno = 0;
    $errstr = '';

    // Suppress warnings; we return errors explicitly.
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout_seconds);
    if (is_resource($fp)) {
        fclose($fp);
        return array(
            'ok' => true,
            'host' => $host,
            'port' => $port,
            'ip' => $ip,
            'error' => '',
        );
    }

    return array(
        'ok' => false,
        'host' => $host,
        'port' => $port,
        'ip' => $ip,
        'error' => trim($errstr) !== '' ? (trim($errstr) . ' (errno ' . (string) $errno . ')') : ('Connection failed (errno ' . (string) $errno . ').'),
    );
}

/**
 * Preflight hook used by contact/subscribe to fail fast when SMTP is unreachable.
 *
 * @return array{ok:bool,message:string,details:array}
 */
function kunaal_smtp_preflight_fast(): array {
    if (!function_exists('kunaal_smtp_is_enabled') || !kunaal_smtp_is_enabled()) {
        return array('ok' => true, 'message' => '', 'details' => array());
    }

    $test = kunaal_smtp_test_tcp_connectivity(6);
    if ($test['ok']) {
        return array('ok' => true, 'message' => '', 'details' => $test);
    }

    // Log reachability details (no secrets).
    if (function_exists('kunaal_theme_log')) {
        kunaal_theme_log('SMTP preflight failed', array(
            'host' => $test['host'],
            'port' => $test['port'],
            'ip' => $test['ip'],
            'error' => $test['error'],
        ));
    }

    return array(
        'ok' => false,
        'message' => 'Email delivery is currently unavailable (SMTP connection failed).',
        'details' => $test,
    );
}

/**
 * Admin-only AJAX endpoint to test SMTP reachability.
 */
function kunaal_handle_smtp_diagnostics(): void {
    try {
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized.'), 403);
            wp_die();
        }

        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (!$nonce || !wp_verify_nonce($nonce, 'kunaal_theme_nonce')) {
            wp_send_json_error(array('message' => 'Invalid nonce.'), 400);
            wp_die();
        }

        $settings = kunaal_smtp_resolve_settings();
        $test = kunaal_smtp_test_tcp_connectivity(6);

        wp_send_json_success(array(
            'enabled' => function_exists('kunaal_smtp_is_enabled') ? (bool) kunaal_smtp_is_enabled() : false,
            'settings' => array(
                'host' => $settings['host'],
                'port' => $settings['port'],
                'enc' => $settings['enc'],
                // Do not expose username in full
                'userHint' => $settings['user'] !== '' ? substr($settings['user'], 0, 2) . '***' : '',
            ),
            'tcp' => $test,
        ));
        wp_die();
    } catch (\Throwable $e) {
        if (function_exists('kunaal_theme_log')) {
            kunaal_theme_log('SMTP diagnostics error', array('error' => $e->getMessage()));
        }
        wp_send_json_error(array('message' => 'SMTP diagnostics failed.'), 500);
        wp_die();
    }
}
add_action('wp_ajax_kunaal_smtp_diagnostics', 'kunaal_handle_smtp_diagnostics');


