<?php
/**
 * SMTP Configuration
 * 
 * Configures PHPMailer for SMTP email delivery if enabled in Customizer.
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
 */
function kunaal_smtp_is_enabled() {
    return (bool) kunaal_mod('kunaal_smtp_enabled', false);
}

/**
 * Filter: Set SMTP from email
 */
function kunaal_filter_wp_mail_from($from_email) {
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
function kunaal_filter_wp_mail_from_name($from_name) {
    if (!kunaal_smtp_is_enabled()) {
        return $from_name;
    }
    $custom = kunaal_mod('kunaal_smtp_from_name', '');
    return !empty($custom) ? $custom : $from_name;
}
add_filter('wp_mail_from_name', 'kunaal_filter_wp_mail_from_name');

/**
 * Action: Configure PHPMailer for SMTP
 */
function kunaal_action_phpmailer_init($phpmailer) {
    if (!kunaal_smtp_is_enabled()) {
        return;
    }

    $host = trim((string) kunaal_mod('kunaal_smtp_host', ''));
    $user = (string) kunaal_mod('kunaal_smtp_username', '');
    $pass = (string) kunaal_mod('kunaal_smtp_password', '');
    $port = (int) kunaal_mod('kunaal_smtp_port', 587);
    $enc  = (string) kunaal_mod('kunaal_smtp_encryption', 'tls');

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

