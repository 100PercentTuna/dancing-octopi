<?php
/**
 * AJAX: Debug Log
 * 
 * Handles debug logging requests from JavaScript.
 * Only active when WP_DEBUG is enabled.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper: Validate debug log request
 *
 * @return array Array with 'valid' (bool) and 'error' (string) keys
 */
function kunaal_validate_debug_log_request(): array {
    $result = array('valid' => true, 'error' => '');
    
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        $result = array('valid' => false, 'error' => 'Debug logging disabled');
    } elseif (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'kunaal_debug_log_nonce')) {
        $result = array('valid' => false, 'error' => 'Invalid nonce');
    } elseif (!current_user_can('edit_posts')) {
        $result = array('valid' => false, 'error' => 'Insufficient permissions');
    }
    
    return $result;
}

/**
 * Helper: Get log data from POST
 */
function kunaal_get_debug_log_data(): string {
    $log_json = isset($_POST['log_data']) ? stripslashes($_POST['log_data']) : '';
    if (empty($log_json)) {
        $raw_input = file_get_contents('php://input');
        if (!empty($raw_input)) {
            $log_json = $raw_input;
        }
    }
    return $log_json;
}

/**
 * Helper: Validate log data structure
 *
 * @param mixed $log_data Log data to validate
 * @return bool True if valid, false otherwise
 */
function kunaal_validate_debug_log_data(mixed $log_data): bool {
    if (!$log_data || !isset($log_data['location']) || !isset($log_data['message'])) {
        return false;
    }
    return true;
}

/**
 * Helper: Write log to file
 */
function kunaal_write_debug_log(array $log_data): void {
    $log_file = get_template_directory() . '/debug.log';
    $log_line = json_encode($log_data) . "\n";
    @file_put_contents($log_file, $log_line, FILE_APPEND | LOCK_EX);
}

/**
 * Debug log handler - receives logs from JavaScript and writes to theme debug.log
 * Only active during development/debugging (WP_DEBUG must be true)
 * Nonce-protected and capability-checked for security
 */
function kunaal_handle_debug_log(): void {
    $validation = kunaal_validate_debug_log_request();
    if (!$validation['valid']) {
        wp_send_json_error(array('message' => $validation['error']));
        wp_die();
    }
    
    $log_json = kunaal_get_debug_log_data();
    $log_data = json_decode($log_json, true);
    
    if (!kunaal_validate_debug_log_data($log_data)) {
        wp_send_json_error(array('message' => 'Invalid log data'));
        wp_die();
    }
    
    kunaal_write_debug_log($log_data);
    
    wp_send_json_success(array('logged' => true));
    wp_die();
}

// Only register handlers if WP_DEBUG is enabled
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('wp_ajax_kunaal_debug_log', 'kunaal_handle_debug_log');
    // Note: Removed nopriv handler - debug logging requires authentication
}

