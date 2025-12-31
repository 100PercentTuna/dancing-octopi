<?php
/**
 * SMTP Configuration
 * 
 * Configures PHPMailer for SMTP email delivery.
 * 
 * SECURITY: SMTP credentials (host, username, password) are read from wp-config.php
 * constants to avoid storing secrets in the database. Define these constants:
 * 
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
 * Check if SMTP is enabled
 * 
 * SMTP is enabled if the required constants are defined in wp-config.php
 * and the Customizer toggle is enabled.
 */
function kunaal_smtp_is_enabled(): bool {
    // Check if Customizer toggle is enabled
    if (!(bool) kunaal_mod('kunaal_smtp_enabled', false)) {
        return false;
    }
    
    // Check if required constants are defined
    return defined('KUNAAL_SMTP_HOST') && 
           defined('KUNAAL_SMTP_USER') && 
           defined('KUNAAL_SMTP_PASS');
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
 * Reads credentials from wp-config.php constants for security.
 */
function kunaal_action_phpmailer_init(PHPMailer\PHPMailer\PHPMailer $phpmailer): void {
    if (!kunaal_smtp_is_enabled()) {
        return;
    }

    // Read credentials from constants (defined in wp-config.php)
    $host = defined('KUNAAL_SMTP_HOST') ? (string) KUNAAL_SMTP_HOST : '';
    $user = defined('KUNAAL_SMTP_USER') ? (string) KUNAAL_SMTP_USER : '';
    $pass = defined('KUNAAL_SMTP_PASS') ? (string) KUNAAL_SMTP_PASS : '';
    $port = defined('KUNAAL_SMTP_PORT') ? (int) KUNAAL_SMTP_PORT : 587;
    $enc  = defined('KUNAAL_SMTP_SECURE') ? (string) KUNAAL_SMTP_SECURE : 'tls';

    if (empty($host) || empty($user) || empty($pass) || $port <= 0) {
        // Fail-safe: do not partially configure SMTP.
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host = $host;
    $phpmailer->Port = $port;
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $user;
    $phpmailer->Password = $pass;
    $phpmailer->SMTPAutoTLS = true;

    if ($enc === 'ssl') {
        $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } elseif ($enc === 'tls') {
        $phpmailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $phpmailer->SMTPSecure = '';
    }
}
add_action('phpmailer_init', 'kunaal_action_phpmailer_init');

