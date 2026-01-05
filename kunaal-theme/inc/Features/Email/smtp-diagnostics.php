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
    $result = array(
        'ok' => false,
        'host' => $host,
        'port' => $port,
        'ip' => '',
        'error' => '',
    );

    if ($host === '' || $port <= 0) {
        $result['error'] = 'Missing SMTP host/port.';
        return $result;
    }

    $ip = gethostbyname($host);
    if ($ip === $host) {
        // DNS resolution failed (gethostbyname returns input on failure)
        $result['error'] = 'DNS resolution failed for host.';
        return $result;
    }
    $result['ip'] = $ip;

    $errno = 0;
    $errstr = '';

    // Suppress warnings; we return errors explicitly.
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout_seconds);
    if (is_resource($fp)) {
        fclose($fp);
        $result['ok'] = true;
        return $result;
    }

    $result['error'] = trim($errstr) !== ''
        ? (trim($errstr) . ' (errno ' . (string) $errno . ')')
        : ('Connection failed (errno ' . (string) $errno . ').');
    return $result;
}

/**
 * Preflight hook used by contact/subscribe to fail fast when SMTP is unreachable.
 *
 * @return array{ok:bool,message:string,details:array}
 */
function kunaal_smtp_preflight_fast(): array {
    $result = array('ok' => true, 'message' => '', 'details' => array());
    if (!function_exists('kunaal_smtp_is_enabled') || !kunaal_smtp_is_enabled()) {
        return $result;
    }

    // Cache reachability results to avoid repeated multi-second delays on form submits.
    // This is especially important on managed hosts where outbound SMTP ports time out.
    $cache_key = 'kunaal_smtp_preflight_tcp_v1';
    $cached = get_transient($cache_key);
    if (is_array($cached) && isset($cached['ok'])) {
        if ($cached['ok']) {
            $result['details'] = isset($cached['details']) && is_array($cached['details']) ? $cached['details'] : array();
            return $result;
        }
        $result['ok'] = false;
        $result['message'] = 'Email delivery is currently unavailable (SMTP connection failed).';
        $result['details'] = isset($cached['details']) && is_array($cached['details']) ? $cached['details'] : array();
        return $result;
    }

    // Keep the preflight fast; diagnostics endpoint uses the longer timeout.
    $test = kunaal_smtp_test_tcp_connectivity(2);
    if ($test['ok']) {
        $result['details'] = $test;
        set_transient($cache_key, array('ok' => true, 'details' => $test), 10 * MINUTE_IN_SECONDS);
        return $result;
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

    $result['ok'] = false;
    $result['message'] = 'Email delivery is currently unavailable (SMTP connection failed).';
    $result['details'] = $test;
    set_transient($cache_key, array('ok' => false, 'details' => $test), 10 * MINUTE_IN_SECONDS);
    return $result;
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

/**
 * Admin-only AJAX endpoint to test actual sending via wp_mail.
 * This helps diagnose deliverability vs connectivity vs From-address policy issues.
 *
 * POST:
 * - nonce (kunaal_theme_nonce)
 * - type: "contact" | "subscribe"
 * - to: optional recipient (defaults to current user email)
 * - forcePhp: "1" to temporarily disable SMTP hook for this send
 */
function kunaal_handle_smtp_send_test(): void {
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

        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : 'contact';
        $to = isset($_POST['to']) ? sanitize_email(wp_unslash($_POST['to'])) : '';
        if ($to === '' && function_exists('wp_get_current_user')) {
            $u = wp_get_current_user();
            $to = ($u && isset($u->user_email)) ? (string) $u->user_email : '';
        }
        if (!is_email($to)) {
            wp_send_json_error(array('message' => 'Provide a valid recipient email in `to`.'), 400);
            wp_die();
        }

        $force_php = isset($_POST['forcePhp']) ? sanitize_text_field(wp_unslash($_POST['forcePhp'])) : '';
        $force_php = ($force_php === '1');

        $site = get_bloginfo('name');
        $subject = '[' . $site . '] Test email (' . $type . ')';
        $body = "This is a test email triggered from Kunaal Theme SMTP diagnostics.\n\n"
            . "Type: " . $type . "\n"
            . "Time (UTC): " . gmdate('c') . "\n"
            . "Home URL: " . home_url('/') . "\n";

        if ($force_php && function_exists('kunaal_action_phpmailer_init')) {
            remove_action('phpmailer_init', 'kunaal_action_phpmailer_init');
        }

        $sent = wp_mail($to, $subject, $body);

        if ($force_php && function_exists('kunaal_action_phpmailer_init')) {
            add_action('phpmailer_init', 'kunaal_action_phpmailer_init');
        }

        global $phpmailer;
        $error_info = '';
        if (!$sent && isset($phpmailer) && is_object($phpmailer) && property_exists($phpmailer, 'ErrorInfo')) {
            $error_info = (string) $phpmailer->ErrorInfo;
        }

        wp_send_json_success(array(
            'sent' => (bool) $sent,
            'to' => $to,
            'type' => $type,
            'forcePhp' => $force_php,
            'smtpEnabled' => function_exists('kunaal_smtp_is_enabled') ? (bool) kunaal_smtp_is_enabled() : false,
            'tcp' => kunaal_smtp_test_tcp_connectivity(6),
            'errorInfo' => $error_info,
        ));
        wp_die();
    } catch (\Throwable $e) {
        if (function_exists('kunaal_theme_log')) {
            kunaal_theme_log('SMTP send test error', array('error' => $e->getMessage()));
        }
        wp_send_json_error(array('message' => 'SMTP send test failed.'), 500);
        wp_die();
    }
}
add_action('wp_ajax_kunaal_smtp_send_test', 'kunaal_handle_smtp_send_test');


