<?php
/**
 * Crash-Safe Logging System
 * 
 * Best-effort logger for managed hosts where debug.log is blocked.
 * Writes to PHP error log and wp-content/kunaal-theme-debug.log.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Best-effort logger.
 * Writes to:
 * - PHP error log (always attempted)
 * - wp-content/kunaal-theme-debug.log (if writable)
 */
function kunaal_theme_log($message, $context = array()) {
    try {
        $prefix = '[kunaal-theme] ';
        $ts = gmdate('c');
        $ctx = '';
        if (!empty($context)) {
            // Avoid throwing on non-UTF8 / non-serializable values.
            $ctx = ' ' . wp_json_encode($context);
        }
        @error_log($prefix . $ts . ' ' . (string) $message . $ctx);

        if (defined('WP_CONTENT_DIR') && is_dir(WP_CONTENT_DIR) && is_writable(WP_CONTENT_DIR)) {
            $logFile = trailingslashit(WP_CONTENT_DIR) . 'kunaal-theme-debug.log';
            $line = $prefix . $ts . ' ' . (string) $message . $ctx . PHP_EOL;
            @file_put_contents($logFile, $line, FILE_APPEND);
        }
    } catch (\Throwable $e) {
        // Never allow logging to crash the site.
        @error_log('[kunaal-theme] log failure: ' . $e->getMessage());
    }
}

/**
 * Shutdown handler callback for fatal errors
 */
function kunaal_theme_shutdown_handler() {
    $err = error_get_last();
    if (!$err || !isset($err['type'])) {
        return;
    }
    $fatal_types = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
    if (in_array($err['type'], $fatal_types, true)) {
        kunaal_theme_log('FATAL', array(
            'type' => $err['type'],
            'message' => $err['message'] ?? '',
            'file' => $err['file'] ?? '',
            'line' => $err['line'] ?? 0,
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
        ));
    }
}

/**
 * Capture fatal errors that result in white-screen/500 without wp-content/debug.log.
 */
function kunaal_theme_register_shutdown_handler() {
    register_shutdown_function('kunaal_theme_shutdown_handler');
}
kunaal_theme_register_shutdown_handler();

/**
 * Crash-safe require_once wrapper.
 * If a file is missing (bad zip / partial deploy), we log and continue.
 */
function kunaal_theme_safe_require_once($absolute_path) {
    if (is_string($absolute_path) && $absolute_path !== '' && file_exists($absolute_path)) {
        require_once $absolute_path;
        return true;
    }
    kunaal_theme_log('Missing required file', array('path' => (string) $absolute_path));
    return false;
}

