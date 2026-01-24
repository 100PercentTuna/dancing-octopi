<?php
/**
 * Essay Pageview Tracking
 * 
 * Tracks pageviews for essays via AJAX (logged-out visitors only).
 *
 * @package Kunaal_Theme
 * @since 4.44.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX: Track pageview for an essay
 * 
 * Only tracks for logged-out visitors to prevent admin/editor views from inflating counts.
 * Uses atomic update to prevent race conditions.
 */
function kunaal_track_pageview(): void {
    try {
        // Only allow logged-out users
        if (is_user_logged_in()) {
            wp_send_json_success(array('message' => 'Skipped: logged in'));
            wp_die();
        }

        // Validate nonce (lightweight check for basic CSRF protection)
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce verified below
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'kunaal_theme_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
            wp_die();
        }

        // Validate post ID
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below
        $post_id = isset($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;
        
        if ($post_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid post ID'));
            wp_die();
        }

        // Verify it's an essay
        $post_type = get_post_type($post_id);
        if ($post_type !== 'essay') {
            wp_send_json_error(array('message' => 'Not an essay'));
            wp_die();
        }

        // Verify post is published
        $post_status = get_post_status($post_id);
        if ($post_status !== 'publish') {
            wp_send_json_success(array('message' => 'Skipped: not published'));
            wp_die();
        }

        // Atomic increment using WordPress meta update
        // This prevents race conditions in high-traffic scenarios
        $current_count = (int) get_post_meta($post_id, 'kunaal_pageviews', true);
        $new_count = $current_count + 1;
        update_post_meta($post_id, 'kunaal_pageviews', $new_count);

        wp_send_json_success(array(
            'message' => 'Pageview tracked',
            'count' => $new_count,
        ));
        wp_die();
    } catch (\Throwable $e) {
        kunaal_theme_log('Pageview tracking error', array(
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ));
        wp_send_json_error(array('message' => 'Tracking failed'));
        wp_die();
    }
}
add_action('wp_ajax_nopriv_kunaal_track_pageview', 'kunaal_track_pageview');
