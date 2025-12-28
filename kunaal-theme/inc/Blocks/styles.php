<?php
/**
 * Block Styles Registration
 * 
 * Registers custom block styles for core blocks.
 *
 * @package Kunaal_Theme
 * @since 4.32.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Double Underline Block Style
 * 
 * Makes the canonical double underline motif available as a block style
 * for core/paragraph and core/heading blocks.
 */
function kunaal_register_double_underline_style() {
    if (!function_exists('register_block_style')) {
        return;
    }

    // Register for paragraph blocks
    register_block_style('core/paragraph', array(
        'name'         => 'double-underline',
        'label'        => __('Double Underline', 'kunaal-theme'),
        'style_handle' => 'kunaal-theme-utilities', // Uses utilities.css
    ));

    // Register for heading blocks
    register_block_style('core/heading', array(
        'name'         => 'double-underline',
        'label'        => __('Double Underline', 'kunaal-theme'),
        'style_handle' => 'kunaal-theme-utilities',
    ));
}
add_action('init', 'kunaal_register_double_underline_style', 20);

