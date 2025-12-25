<?php
/**
 * Custom PDF Generator for Kunaal Theme
 * Creates eBook-style PDFs with proper formatting
 * 
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate PDF for a post
 */
function kunaal_generate_pdf() {
    // Security check
    if (!isset($_GET['kunaal_pdf']) || !isset($_GET['post_id'])) {
        return;
    }
    
    $post_id = absint($_GET['post_id']);
    $post = get_post($post_id);
    
    if (!$post || !in_array($post->post_type, array('essay', 'jotting'))) {
        wp_die('Invalid post');
    }
    
    // Get post data
    $title = get_the_title($post_id);
    $subtitle = get_post_meta($post_id, 'kunaal_subtitle', true);
    $content = apply_filters('the_content', $post->post_content);
    $author_first = get_theme_mod('kunaal_author_first_name', 'Kunaal');
    $author_last = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
    $author_name = $author_first . ' ' . $author_last;
    $author_email = get_theme_mod('kunaal_contact_email', '');
    $site_url = home_url('/');
    $date = get_the_date('j F Y', $post_id);
    $read_time = get_post_meta($post_id, 'kunaal_read_time', true);
    $topics = get_the_terms($post_id, 'topic');
    
    // Get avatar
    $avatar_id = get_theme_mod('kunaal_author_avatar');
    $avatar_url = $avatar_id ? wp_get_attachment_image_url($avatar_id, 'thumbnail') : '';
    
    // Get hero image
    $hero_url = get_the_post_thumbnail_url($post_id, 'full');
    
    // Process content for PDF
    $pdf_content = kunaal_process_content_for_pdf($content);
    
    // Generate table of contents
    $toc = kunaal_generate_toc($content);
    
    // Load template
    ob_start();
    include(get_template_directory() . '/pdf-template.php');
    $html = ob_get_clean();
    
    // Output PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . sanitize_file_name($title) . '.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    // For now, convert HTML to PDF using browser print
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . esc_html($title) . '</title>
    <style>' . kunaal_get_pdf_styles() . '</style>
</head>
<body onload="window.print()">
' . $html . '
</body>
</html>';
    exit;
}
add_action('template_redirect', 'kunaal_generate_pdf');

/**
 * Process content for PDF output
 */
function kunaal_process_content_for_pdf($content) {
    // Expand accordions
    $content = preg_replace('/<details(.*?)>/', '<details$1 open>', $content);
    
    // Remove interactive elements
    $content = preg_replace('/<button[^>]*>.*?<\/button>/s', '', $content);
    
    // Simplify charts to static display
    // (Charts are SVG, they'll render fine in PDF)
    
    return $content;
}

/**
 * Generate table of contents from h2 headers
 */
function kunaal_generate_toc($content) {
    preg_match_all('/<h2[^>]*id="([^"]*)"[^>]*>(.*?)<\/h2>/', $content, $matches);
    
    if (empty($matches[1])) {
        return '';
    }
    
    $toc = '<div class="pdf-toc">';
    $toc .= '<h3>Contents</h3>';
    $toc .= '<ol>';
    
    foreach ($matches[1] as $index => $id) {
        $heading_text = wp_strip_all_tags($matches[2][$index]);
        $toc .= '<li><a href="#' . esc_attr($id) . '">' . esc_html($heading_text) . '</a></li>';
    }
    
    $toc .= '</ol>';
    $toc .= '</div>';
    
    return $toc;
}

/**
 * Get PDF-specific CSS styles
 */
function kunaal_get_pdf_styles() {
    return file_get_contents(get_template_directory() . '/assets/css/pdf-ebook.css');
}
