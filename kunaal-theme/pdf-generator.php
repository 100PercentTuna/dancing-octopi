<?php
/**
 * Custom PDF Generator for Kunaal Theme
 * Creates eBook-style PDFs with proper formatting using DOMPDF
 * 
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load Composer autoloader if available
$autoloader = KUNAAL_THEME_DIR . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
}

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Generate PDF for a post
 */
function kunaal_generate_pdf() {
    // Security check
    if (!isset($_GET['kunaal_pdf']) || !isset($_GET['post_id'])) {
        return;
    }
    
    // Check if DOMPDF is available
    $use_dompdf = class_exists('Dompdf\Dompdf');
    
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
    $post_url = get_permalink($post_id);
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
    
    // Determine post type for filename
    $post_type_label = $post->post_type === 'essay' ? 'Essay' : 'Jotting';
    
    // Build HTML
    $html = kunaal_build_pdf_html(array(
        'title'       => $title,
        'subtitle'    => $subtitle,
        'author'      => $author_name,
        'date'        => $date,
        'post_url'    => $post_url,
        'content'     => $pdf_content,
        'toc'         => $toc,
        'hero_url'    => $hero_url,
        'avatar_url'  => $avatar_url,
        'topics'      => $topics,
        'read_time'   => $read_time,
        'post_type'   => $post_type_label,
    ));
    
    // Generate filename: "Kunaal Wadhwa – Essay – Title.pdf"
    $safe_title = sanitize_file_name($title);
    $filename = "{$author_name} - {$post_type_label} - {$safe_title}.pdf";
    
    if ($use_dompdf) {
        // Use DOMPDF for proper PDF generation
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('dpi', 150);
        $options->set('defaultPaperSize', 'A4');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Add page numbers via canvas
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica');
        
        $canvas->page_script(function($pageNumber, $pageCount, $canvas, $fontMetrics) use ($font, $author_name, $title, $date, $post_url) {
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            
            // Header: Date (left) | Title (center) | Author (right)
            $header_y = 25;
            $canvas->text(40, $header_y, $date, $font, 8, array(0.4, 0.4, 0.4));
            
            $short_title = mb_strlen($title) > 50 ? mb_substr($title, 0, 47) . '...' : $title;
            $title_width = $fontMetrics->getTextWidth($short_title, $font, 8);
            $canvas->text(($width - $title_width) / 2, $header_y, $short_title, $font, 8, array(0.4, 0.4, 0.4));
            
            $author_width = $fontMetrics->getTextWidth($author_name, $font, 8);
            $canvas->text($width - 40 - $author_width, $header_y, $author_name, $font, 8, array(0.4, 0.4, 0.4));
            
            // Footer: URL (left) | Page X of Y (right)
            $footer_y = $height - 25;
            $canvas->text(40, $footer_y, $post_url, $font, 7, array(0.5, 0.5, 0.5));
            
            $page_text = "Page {$pageNumber} of {$pageCount}";
            $page_width = $fontMetrics->getTextWidth($page_text, $font, 8);
            $canvas->text($width - 40 - $page_width, $footer_y, $page_text, $font, 8, array(0.4, 0.4, 0.4));
        });
        
        // Output PDF
        $dompdf->stream($filename, array('Attachment' => false));
        
    } else {
        // Fallback: Browser print dialog
        // This works but doesn't have server-generated page numbers
        $styles = kunaal_get_pdf_styles();
        
        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>' . esc_html($title) . '</title>
    <style>
    ' . $styles . '
    @media print {
        @page { margin: 2cm; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
    .print-notice { 
        background: #fff3cd; 
        border: 1px solid #ffc107; 
        padding: 1rem; 
        margin-bottom: 2rem; 
        border-radius: 4px;
        font-family: sans-serif;
    }
    @media print { .print-notice { display: none; } }
    </style>
</head>
<body>
    <div class="print-notice">
        <strong>Print to PDF:</strong> Use your browser\'s print dialog (Ctrl/Cmd + P) and select "Save as PDF" as the destination.
        For the best experience, <a href="https://getcomposer.org" target="_blank">install Composer</a> and run <code>composer install</code> in the theme folder.
    </div>
    ' . $html . '
    <script>window.print();</script>
</body>
</html>';
    }
    
    exit;
}
add_action('template_redirect', 'kunaal_generate_pdf');

/**
 * Build PDF HTML structure
 */
function kunaal_build_pdf_html($data) {
    $styles = kunaal_get_pdf_styles();
    
    $topics_html = '';
    if (!empty($data['topics']) && !is_wp_error($data['topics'])) {
        $topic_names = array_map(function($t) { return '#' . $t->name; }, $data['topics']);
        $topics_html = '<p class="pdf-topics">' . implode(' ', $topic_names) . '</p>';
    }
    
    $toc_html = !empty($data['toc']) ? $data['toc'] : '';
    
    $hero_html = '';
    if (!empty($data['hero_url'])) {
        $hero_html = '<div class="pdf-hero"><img src="' . esc_url($data['hero_url']) . '" alt="" /></div>';
    }
    
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>' . esc_html($data['title']) . '</title>
    <style>' . $styles . '</style>
</head>
<body>
    <div class="pdf-wrapper">
        <!-- Cover Page -->
        <div class="pdf-cover">
            <div class="pdf-cover-content">
                <p class="pdf-type">' . esc_html($data['post_type']) . '</p>
                <h1 class="pdf-title">' . esc_html($data['title']) . '</h1>
                ' . ($data['subtitle'] ? '<p class="pdf-subtitle">' . esc_html($data['subtitle']) . '</p>' : '') . '
                <div class="pdf-byline">
                    <p class="pdf-author">by ' . esc_html($data['author']) . '</p>
                    <p class="pdf-date">' . esc_html($data['date']) . '</p>
                    ' . ($data['read_time'] ? '<p class="pdf-readtime">' . esc_html($data['read_time']) . ' min read</p>' : '') . '
                </div>
                ' . $topics_html . '
            </div>
        </div>
        
        ' . $toc_html . '
        
        ' . $hero_html . '
        
        <!-- Main Content -->
        <div class="pdf-content">
            ' . $data['content'] . '
        </div>
        
        <!-- Colophon -->
        <div class="pdf-colophon">
            <p>This document was generated from <a href="' . esc_url($data['post_url']) . '">' . esc_url($data['post_url']) . '</a></p>
            <p>© ' . date('Y') . ' ' . esc_html($data['author']) . '. All rights reserved.</p>
        </div>
    </div>
</body>
</html>';
}

/**
 * Process content for PDF output
 */
function kunaal_process_content_for_pdf($content) {
    // Expand accordions (only add 'open' if not already present)
    $content = preg_replace('/<details(?![^>]*\bopen\b)([^>]*)>/', '<details$1 open>', $content);
    
    // Remove interactive elements
    $content = preg_replace('/<button[^>]*>.*?<\/button>/s', '', $content);
    
    // Remove share/subscribe elements
    $content = preg_replace('/<div[^>]*class="[^"]*(?:shareItem|subscribe-form|actionDock)[^"]*"[^>]*>.*?<\/div>/s', '', $content);
    
    // Simplify sidenotes for print (make inline)
    $content = preg_replace('/<span class="sidenote">(.*?)<\/span>/s', '<span class="sidenote-inline">[$1]</span>', $content);
    
    // Convert relative URLs to absolute
    $site_url = home_url();
    $content = preg_replace('/src="\/(?!\/)/i', 'src="' . $site_url . '/', $content);
    $content = preg_replace('/href="\/(?!\/)/i', 'href="' . $site_url . '/', $content);
    
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
    $css_file = KUNAAL_THEME_DIR . '/assets/css/pdf-ebook.css';
    if (file_exists($css_file)) {
        return file_get_contents($css_file);
    }
    return '';
}
