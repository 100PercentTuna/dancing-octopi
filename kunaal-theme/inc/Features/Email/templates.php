<?php
/**
 * Subscriber Email Templates
 *
 * Responsible for rendering plain-text email subjects/bodies with placeholders,
 * correct entity decoding, standard footer, and unsubscribe line.
 *
 * @package Kunaal_Theme
 * @since 5.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Decode entities for email rendering.
 *
 * WordPress site title and customizer fields can contain entities like &amp;.
 *
 * @param string $text
 * @return string
 */
function kunaal_email_decode_entities(string $text): string {
    $text = wp_specialchars_decode($text, ENT_QUOTES);
    return html_entity_decode($text, ENT_QUOTES, get_bloginfo('charset'));
}

/**
 * Safe placeholder replacement.
 *
 * @param string $template
 * @param array<string,string> $vars
 * @return string
 */
function kunaal_email_apply_vars(string $template, array $vars): string {
    $out = $template;
    foreach ($vars as $k => $v) {
        $out = str_replace('{' . $k . '}', $v, $out);
    }
    return $out;
}

/**
 * Get standard footer configured in Customizer.
 *
 * @return string
 */
function kunaal_email_footer_text(): string {
    $footer = (string) kunaal_mod('kunaal_subscribe_email_footer', '');
    $footer = trim(kunaal_email_decode_entities($footer));
    if ($footer === '') {
        return '';
    }
    return "\n\n" . $footer;
}

/**
 * Get unsubscribe line with custom text.
 *
 * @param string $unsubscribe_url
 * @return string
 */
function kunaal_email_unsubscribe_line(string $unsubscribe_url): string {
    $text = (string) kunaal_mod('kunaal_subscribe_unsubscribe_text', 'Unsubscribe');
    $text = trim(kunaal_email_decode_entities($text));
    if ($text === '') {
        $text = 'Unsubscribe';
    }
    return "\n\n" . $text . ': ' . esc_url_raw($unsubscribe_url);
}

/**
 * Create signed unsubscribe URL for a subscriber.
 *
 * @param int $subscriber_id
 * @param string $email
 * @return string
 */
function kunaal_subscribe_unsubscribe_url(int $subscriber_id, string $email): string {
    $sig = hash_hmac('sha256', $subscriber_id . '|' . strtolower($email), wp_salt('nonce'));
    return add_query_arg(
        array(
            'kunaal_unsub' => '1',
            'sid' => (string) $subscriber_id,
            'sig' => $sig,
        ),
        home_url('/')
    );
}

/**
 * Render subscribe confirmation email.
 *
 * Placeholders supported:
 * - {site}
 * - {confirm_url}
 * - {unsubscribe_url}
 *
 * @param array{id:int,email:string} $subscriber
 * @param string $confirm_url
 * @return array{subject:string,body:string}
 */
function kunaal_email_render_confirmation(array $subscriber, string $confirm_url): array {
    $site = kunaal_email_decode_entities((string) get_bloginfo('name'));
    $email = (string) $subscriber['email'];
    $unsub_url = kunaal_subscribe_unsubscribe_url((int) $subscriber['id'], $email);

    $subject_tpl = (string) kunaal_mod('kunaal_subscribe_confirm_subject', '[{site}] Confirm your subscription');
    $body_tpl = (string) kunaal_mod(
        'kunaal_subscribe_confirm_body',
        "Hi!\n\nPlease confirm your subscription by clicking the link below:\n\n{confirm_url}\n\nIf you didn't request this, you can ignore this email."
    );

    $vars = array(
        'site' => $site,
        'confirm_url' => esc_url_raw($confirm_url),
        'unsubscribe_url' => esc_url_raw($unsub_url),
    );

    $subject = kunaal_email_apply_vars(kunaal_email_decode_entities($subject_tpl), $vars);
    $body = kunaal_email_apply_vars(kunaal_email_decode_entities($body_tpl), $vars);

    $body .= kunaal_email_footer_text();
    $body .= kunaal_email_unsubscribe_line($unsub_url);

    return array(
        'subject' => $subject,
        'body' => $body,
    );
}


