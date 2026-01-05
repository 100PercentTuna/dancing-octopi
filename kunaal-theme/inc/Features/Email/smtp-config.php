<?php
/**
 * SMTP Configuration
 * 
 * Configures PHPMailer for SMTP email delivery.
 * 
 * Credentials can be provided via:
 * 1. wp-config.php constants (recommended for public git repos)
 * 2. GUI fields in Customizer > Email Delivery (SMTP)
 * 
 * wp-config.php constants (if defined, these take priority):
 *   define('KUNAAL_SMTP_HOST', 'smtp.example.com');
 *   define('KUNAAL_SMTP_PORT', 587);
 *   define('KUNAAL_SMTP_USER', 'your-username');
 *   define('KUNAAL_SMTP_PASS', 'your-password');
 *   define('KUNAAL_SMTP_SECURE', 'tls'); // 'tls', 'ssl', or ''
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cache key for SMTP TCP preflight results (host/port specific).
 *
 * @return string
 */
function kunaal_smtp_preflight_cache_key(): string {
    // Mirror the same priority rules as PHPMailer init (constants > GUI).
    $has_config_creds = defined('KUNAAL_SMTP_HOST') &&
        defined('KUNAAL_SMTP_USER') &&
        defined('KUNAAL_SMTP_PASS');

    $host = $has_config_creds ? (string) KUNAAL_SMTP_HOST : (string) kunaal_mod('kunaal_smtp_host_gui', '');
    $port = $has_config_creds
        ? (defined('KUNAAL_SMTP_PORT') ? (int) KUNAAL_SMTP_PORT : 587)
        : (int) kunaal_mod('kunaal_smtp_port_gui', 587);

    return 'kunaal_smtp_preflight_tcp_v1_' . md5(strtolower($host) . '|' . (string) $port);
}

/**
 * Check if SMTP is enabled and has valid credentials
 * 
 * SMTP is enabled if:
 * 1. Customizer toggle is enabled, AND
 * 2. Either wp-config.php constants OR GUI fields have credentials
 */
function kunaal_smtp_is_enabled(): bool {
    // Check if Customizer toggle is enabled
    if (!(bool) kunaal_mod('kunaal_smtp_enabled', false)) {
        return false;
    }

    // If we have a cached reachability failure, disable SMTP to avoid long timeouts.
    // The diagnostics layer caches this for 10 minutes.
    $cached = get_transient(kunaal_smtp_preflight_cache_key());
    if (is_array($cached) && isset($cached['ok']) && $cached['ok'] === false) {
        return false;
    }
    
    // Check if wp-config.php constants are defined
    $has_config_creds = defined('KUNAAL_SMTP_HOST') && 
                        defined('KUNAAL_SMTP_USER') && 
                        defined('KUNAAL_SMTP_PASS');
    
    if ($has_config_creds) {
        return true;
    }
    
    // Check if GUI credentials are set
    $gui_host = (string) kunaal_mod('kunaal_smtp_host_gui', '');
    $gui_port = (int) kunaal_mod('kunaal_smtp_port_gui', 587);
    $gui_auth = (bool) kunaal_mod('kunaal_smtp_auth_gui', true);
    $gui_user = (string) kunaal_mod('kunaal_smtp_username_gui', '');
    $gui_pass = (string) kunaal_mod('kunaal_smtp_password_gui', '');

    if ($gui_host === '' || $gui_port <= 0) {
        return false;
    }

    // For GoDaddy relay/local SMTP, auth is off and username/password may be empty.
    if ($gui_auth === false) {
        return true;
    }

    return ($gui_user !== '' && $gui_pass !== '');
}

/**
 * Filter: Set SMTP from email
 */
function kunaal_filter_wp_mail_from(string $from_email): string {
    if (!kunaal_smtp_is_enabled()) {
        return $from_email;
    }
    $custom = kunaal_mod('kunaal_smtp_from_email', '');
    if (is_email($custom)) {
        return $custom;
    }

    // If SMTP is enabled and no explicit FROM is set, default to SMTP username.
    // This improves deliverability for O365 which expects FROM to match authenticated user.
    if (defined('KUNAAL_SMTP_USER') && is_email((string) KUNAAL_SMTP_USER)) {
        return (string) KUNAAL_SMTP_USER;
    }
    $gui_user = (string) kunaal_mod('kunaal_smtp_username_gui', '');
    return is_email($gui_user) ? $gui_user : $from_email;
}
add_filter('wp_mail_from', 'kunaal_filter_wp_mail_from');

/**
 * Filter: Set SMTP from name
 */
function kunaal_filter_wp_mail_from_name(string $from_name): string {
    if (!kunaal_smtp_is_enabled()) {
        return $from_name;
    }
    $custom = kunaal_mod('kunaal_smtp_from_name', '');
    return !empty($custom) ? $custom : $from_name;
}
add_filter('wp_mail_from_name', 'kunaal_filter_wp_mail_from_name');

/**
 * Action: Configure PHPMailer for SMTP
 * 
 * Priority: wp-config.php constants > GUI settings
 */
function kunaal_action_phpmailer_init(PHPMailer\PHPMailer\PHPMailer $phpmailer): void {
    if (!kunaal_smtp_is_enabled()) {
        return;
    }

    // Check if wp-config.php constants are defined (these take priority)
    $has_config_creds = defined('KUNAAL_SMTP_HOST') && 
                        defined('KUNAAL_SMTP_USER') && 
                        defined('KUNAAL_SMTP_PASS');
    
    if ($has_config_creds) {
        // Use wp-config.php credentials
        $host = (string) KUNAAL_SMTP_HOST;
        $user = (string) KUNAAL_SMTP_USER;
        $pass = (string) KUNAAL_SMTP_PASS;
        $port = defined('KUNAAL_SMTP_PORT') ? (int) KUNAAL_SMTP_PORT : 587;
        $enc  = defined('KUNAAL_SMTP_SECURE') ? (string) KUNAAL_SMTP_SECURE : 'tls';
        $auth = true;
    } else {
        // Use GUI credentials from Customizer
        $host = (string) kunaal_mod('kunaal_smtp_host_gui', '');
        $user = (string) kunaal_mod('kunaal_smtp_username_gui', '');
        $pass = (string) kunaal_mod('kunaal_smtp_password_gui', '');
        $port = (int) kunaal_mod('kunaal_smtp_port_gui', 587);
        $enc  = (string) kunaal_mod('kunaal_smtp_encryption_gui', 'tls');
        $auth = (bool) kunaal_mod('kunaal_smtp_auth_gui', true);
    }

    if ($host === '' || $port <= 0 || ($auth && ($user === '' || $pass === ''))) {
        // Fail-safe: do not partially configure SMTP
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host = $host;
    $phpmailer->Port = $port;
    $phpmailer->SMTPAuth = $auth;
    $phpmailer->Username = $auth ? $user : '';
    $phpmailer->Password = $auth ? $pass : '';
    $phpmailer->SMTPAutoTLS = ($enc === 'tls' || $enc === 'ssl');
    
    // Set longer timeout for O365 (can be slow to respond)
    $phpmailer->Timeout = 30;
    
    // O365 CRITICAL: Force TLS 1.2 (required by Microsoft)
    // Only applies when we are doing TLS/SSL; for GoDaddy local relay (no enc), don't force crypto.
    if ($enc === 'tls' || $enc === 'ssl') {
        $phpmailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false,
                'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
            ),
        );
    }
    
    // O365 CRITICAL: Set FROM address to match authenticated username
    // O365 rejects emails where FROM doesn't match the authenticated user
    $from_email = kunaal_mod('kunaal_smtp_from_email', '');
    if (empty($from_email) || !is_email($from_email)) {
        // If no custom FROM is set, use the SMTP username (required for O365 when auth on).
        // For relay/no-auth mode, fall back to admin_email.
        $from_email = $auth ? $user : (string) get_option('admin_email');
    }
    $phpmailer->setFrom($from_email, kunaal_mod('kunaal_smtp_from_name', get_bloginfo('name')));
    
    // Enable debug mode if constant is defined
    // Add define('KUNAAL_SMTP_DEBUG', true); to wp-config.php to enable
    if (defined('KUNAAL_SMTP_DEBUG') && KUNAAL_SMTP_DEBUG) {
        $phpmailer->SMTPDebug = 2; // Show connection status and errors
        $phpmailer->Debugoutput = function($str, $level) {
            error_log("SMTP Debug [$level]: $str");
        };
    }

    if ($enc === 'ssl') {
        $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($enc === 'tls') {
        $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $phpmailer->SMTPSecure = '';
        $phpmailer->SMTPAutoTLS = false;
    }
}
add_action('phpmailer_init', 'kunaal_action_phpmailer_init');
