<?php
/**
 * Block Registration Helper Functions
 * 
 * Extracted from kunaal_register_blocks() to reduce cognitive complexity.
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validate block.json file
 * 
 * @param string $block_path Path to block directory
 * @param string $block Block name
 * @return array|false Block data array if valid, false otherwise
 */
function kunaal_validate_block_json($block_path, $block) {
    $block_json = $block_path . '/block.json';
    
    if (!file_exists($block_json)) {
        return false;
    }
    
    $block_data = json_decode(file_get_contents($block_json), true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($block_data)) {
        kunaal_theme_log('Invalid block.json', array('block' => $block, 'error' => json_last_error_msg()));
        return false;
    }
    
    if (empty($block_data['name']) || empty($block_data['title'])) {
        kunaal_theme_log('Block.json missing required fields', array('block' => $block));
        return false;
    }
    
    return $block_data;
}

/**
 * Register a single block
 * 
 * @param string $block_path Path to block directory
 * @param string $block Block name
 * @return bool True if registered successfully, false otherwise
 */
function kunaal_register_single_block($block_path, $block) {
    $block_data = kunaal_validate_block_json($block_path, $block);
    if (!$block_data) {
        return false;
    }
    
    try {
        $result = register_block_type($block_path);
        if (!$result) {
            kunaal_theme_log('Block registration failed', array('block' => $block));
            return false;
        }
        return true;
    } catch (Exception $e) {
        kunaal_theme_log('Block registration exception', array('block' => $block, 'error' => $e->getMessage()));
        return false;
    }
}

