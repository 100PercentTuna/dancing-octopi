<?php
/**
 * Embed Card - Open Graph Data Fetcher
 * REST API endpoint to fetch OG meta from URLs
 *
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST API route for fetching OG data
 */
function kunaal_register_og_data_endpoint(): void {
    register_rest_route('kunaal/v1', '/og-data', array(
        'methods'             => 'POST',
        'callback'            => 'kunaal_fetch_og_data',
        'permission_callback' => function () {
            return current_user_can('edit_posts');
        },
        'args'                => array(
            'url' => array(
                'required'          => true,
                'type'              => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'validate_callback' => function ($param) {
                    return filter_var($param, FILTER_VALIDATE_URL) !== false;
                },
            ),
        ),
    ));
}
add_action('rest_api_init', 'kunaal_register_og_data_endpoint');

/**
 * Fetch Open Graph data from a URL
 *
 * @param WP_REST_Request $request The request object.
 * @return WP_REST_Response
 */
function kunaal_fetch_og_data(WP_REST_Request $request): WP_REST_Response {
    $url = $request->get_param('url');
    
    if (empty($url)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('URL is required', 'kunaal-theme'),
        ), 400);
    }
    
    // Fetch the URL content
    $response = wp_remote_get($url, array(
        'timeout'    => 10,
        'user-agent' => 'Mozilla/5.0 (compatible; Kunaal Theme OG Fetcher; +' . home_url() . ')',
        'sslverify'  => true,
    ));
    
    if (is_wp_error($response)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => $response->get_error_message(),
        ), 500);
    }
    
    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code !== 200) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => sprintf(__('Failed to fetch URL (status %d)', 'kunaal-theme'), $status_code),
        ), 500);
    }
    
    $html = wp_remote_retrieve_body($response);
    
    if (empty($html)) {
        return new WP_REST_Response(array(
            'success' => false,
            'message' => __('Empty response from URL', 'kunaal-theme'),
        ), 500);
    }
    
    // Parse OG data
    $og_data = kunaal_parse_og_data($html, $url);
    
    return new WP_REST_Response(array(
        'success' => true,
        'data'    => $og_data,
    ), 200);
}

/**
 * Parse Open Graph and meta data from HTML
 *
 * @param string $html The HTML content.
 * @param string $url  The original URL (for resolving relative URLs).
 * @return array Parsed OG data.
 */
function kunaal_parse_og_data(string $html, string $url): array {
    $data = array(
        'title'       => '',
        'description' => '',
        'image'       => '',
        'siteName'    => '',
        'favicon'     => '',
    );
    
    // Use DOMDocument for parsing
    $doc = new DOMDocument();
    
    // Suppress warnings from malformed HTML
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
    libxml_clear_errors();
    
    // Get meta tags
    $metas = $doc->getElementsByTagName('meta');
    
    foreach ($metas as $meta) {
        $property = $meta->getAttribute('property');
        $name = $meta->getAttribute('name');
        $content = $meta->getAttribute('content');
        
        // Open Graph tags
        if ($property === 'og:title' && empty($data['title'])) {
            $data['title'] = $content;
        }
        if ($property === 'og:description' && empty($data['description'])) {
            $data['description'] = $content;
        }
        if ($property === 'og:image' && empty($data['image'])) {
            $data['image'] = kunaal_resolve_url($content, $url);
        }
        if ($property === 'og:site_name' && empty($data['siteName'])) {
            $data['siteName'] = $content;
        }
        
        // Twitter cards as fallback
        if ($name === 'twitter:title' && empty($data['title'])) {
            $data['title'] = $content;
        }
        if ($name === 'twitter:description' && empty($data['description'])) {
            $data['description'] = $content;
        }
        if ($name === 'twitter:image' && empty($data['image'])) {
            $data['image'] = kunaal_resolve_url($content, $url);
        }
        
        // Standard meta description
        if ($name === 'description' && empty($data['description'])) {
            $data['description'] = $content;
        }
    }
    
    // Get title from <title> tag if not found in OG
    if (empty($data['title'])) {
        $titles = $doc->getElementsByTagName('title');
        if ($titles->length > 0) {
            $data['title'] = trim($titles->item(0)->textContent);
        }
    }
    
    // Get favicon
    $links = $doc->getElementsByTagName('link');
    foreach ($links as $link) {
        $rel = strtolower($link->getAttribute('rel'));
        if (strpos($rel, 'icon') !== false) {
            $href = $link->getAttribute('href');
            if (!empty($href)) {
                $data['favicon'] = kunaal_resolve_url($href, $url);
                break;
            }
        }
    }
    
    // Fallback: use Google's favicon service
    if (empty($data['favicon'])) {
        $parsed = wp_parse_url($url);
        if (!empty($parsed['host'])) {
            $data['favicon'] = 'https://www.google.com/s2/favicons?domain=' . rawurlencode($parsed['host']) . '&sz=32';
        }
    }
    
    // Fallback: extract site name from domain
    if (empty($data['siteName'])) {
        $parsed = wp_parse_url($url);
        if (!empty($parsed['host'])) {
            $data['siteName'] = preg_replace('/^www\./', '', $parsed['host']);
        }
    }
    
    // Truncate description if too long
    if (strlen($data['description']) > 200) {
        $data['description'] = substr($data['description'], 0, 197) . '...';
    }
    
    return $data;
}

/**
 * Resolve relative URLs to absolute
 *
 * @param string $relative The potentially relative URL.
 * @param string $base     The base URL.
 * @return string Absolute URL.
 */
function kunaal_resolve_url(string $relative, string $base): string {
    // Already absolute
    if (preg_match('/^https?:\/\//i', $relative)) {
        return $relative;
    }
    
    // Protocol-relative
    if (strpos($relative, '//') === 0) {
        $parsed = wp_parse_url($base);
        return ($parsed['scheme'] ?? 'https') . ':' . $relative;
    }
    
    $parsed = wp_parse_url($base);
    $scheme = $parsed['scheme'] ?? 'https';
    $host = $parsed['host'] ?? '';
    
    if (empty($host)) {
        return $relative;
    }
    
    // Root-relative
    if (strpos($relative, '/') === 0) {
        return $scheme . '://' . $host . $relative;
    }
    
    // Relative to current path
    $path = $parsed['path'] ?? '/';
    $path = preg_replace('/[^\/]*$/', '', $path);
    
    return $scheme . '://' . $host . $path . $relative;
}

