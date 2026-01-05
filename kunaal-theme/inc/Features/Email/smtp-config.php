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
    
    // Check if wp-config.php constants are defined
    $has_config_creds = defined('KUNAAL_SMTP_HOST') && 
                        defined('KUNAAL_SMTP_USER') && 
                        defined('KUNAAL_SMTP_PASS');
    
    if ($has_config_creds) {
        return true;
    }
    
    // Check if GUI credentials are set
    $gui_host = kunaal_mod('kunaal_smtp_host_gui', '');
    $gui_user = kunaal_mod('kunaal_smtp_username_gui', '');
    $gui_pass = kunaal_mod('kunaal_smtp_password_gui', '');
    
    return !empty($gui_host) && !empty($gui_user) && !empty($gui_pass);
}

/**
 * Filter: Set SMTP from email
 */
function kunaal_filter_wp_mail_from(string $from_email): string {
    if (!kunaal_smtp_is_enabled()) {
        return $from_email;
    }
    $custom = kunaal_mod('kunaal_smtp_from_email', '');
    return is_email($custom) ? $custom : $from_email;
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
    } else {
        // Use GUI credentials from Customizer
        $host = (string) kunaal_mod('kunaal_smtp_host_gui', '');
        $user = (string) kunaal_mod('kunaal_smtp_username_gui', '');
        $pass = (string) kunaal_mod('kunaal_smtp_password_gui', '');
        $port = (int) kunaal_mod('kunaal_smtp_port_gui', 587);
        $enc  = (string) kunaal_mod('kunaal_smtp_encryption_gui', 'tls');
    }

    if (empty($host) || empty($user) || empty($pass) || $port <= 0) {
        // Fail-safe: do not partially configure SMTP
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host = $host;
    $phpmailer->Port = $port;
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $user;
    $phpmailer->Password = $pass;
    $phpmailer->SMTPAutoTLS = true;
    
    // Set shorter timeout (15 seconds instead of default 30)
    // Helps with O365 which can hang on misconfiguration
    $phpmailer->Timeout = 15;
    
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
    }
}
add_action('phpmailer_init', 'kunaal_action_phpmailer_init');
