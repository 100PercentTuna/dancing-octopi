<?php
/**
 * Custom PDF Generator for Kunaal Theme
 * Creates clean journal-paper style PDFs using DOMPDF
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
    if (!isset($_GET['kunaal_pdf']) || !isset($_GET['post_id'])) {
        return;
    }
    
    // Load Composer autoloader if available (inside function to avoid top-level issues)
    $autoloader = KUNAAL_THEME_DIR . '/vendor/autoload.php';
    if (file_exists($autoloader)) {
        require_once $autoloader;
    }
    
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
    $site_url = home_url('/');
    $post_url = get_permalink($post_id);
    $date = get_the_date('j F Y', $post_id);
    $read_time = get_post_meta($post_id, 'kunaal_read_time', true);
    $topics = get_the_terms($post_id, 'topic');
    
    // Process content for PDF
    $pdf_content = kunaal_process_content_for_pdf($content);
    
    // Determine post type for filename
    $post_type_label = $post->post_type === 'essay' ? 'Essay' : 'Jotting';
    
    // Build HTML (journal paper style - no cover page)
    $html = kunaal_build_pdf_html(array(
        'title'       => $title,
        'subtitle'    => $subtitle,
        'author'      => $author_name,
        'date'        => $date,
        'post_url'    => $post_url,
        'site_url'    => $site_url,
        'content'     => $pdf_content,
        'topics'      => $topics,
        'read_time'   => $read_time,
        'post_type'   => $post_type_label,
    ));
    
    // Generate filename: "Title - by Author.pdf"
    $safe_title = sanitize_file_name($title);
    $filename = "{$safe_title} - by {$author_name}.pdf";
    
    if ($use_dompdf) {
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        $options->set('dpi', 150);
        $options->set('defaultPaperSize', 'A4');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Add header and page numbers via canvas
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('Helvetica');
        
        $canvas->page_script(function($pageNumber, $pageCount, $canvas, $fontMetrics) use ($font, $author_name, $site_url) {
            $width = $canvas->get_width();
            $height = $canvas->get_height();
            
            // Header: Author name | website (gray, unobtrusive)
            $header_y = 30;
            $header_text = $author_name . '  •  ' . parse_url($site_url, PHP_URL_HOST);
            $header_width = $fontMetrics->getTextWidth($header_text, $font, 8);
            $canvas->text(($width - $header_width) / 2, $header_y, $header_text, $font, 8, array(0.6, 0.6, 0.6));
            
            // Footer: Page X of Y (bottom right)
            $footer_y = $height - 30;
            $page_text = "Page {$pageNumber} of {$pageCount}";
            $page_width = $fontMetrics->getTextWidth($page_text, $font, 9);
            $canvas->text($width - 56 - $page_width, $footer_y, $page_text, $font, 9, array(0.5, 0.5, 0.5));
        });
        
        $dompdf->stream($filename, array('Attachment' => false));
        
    } else {
        // Fallback: Browser print dialog
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
    }
    .print-notice { 
        background: #fffbeb; 
        border: 1px solid #f59e0b; 
        padding: 1rem; 
        margin-bottom: 2rem; 
        border-radius: 6px;
        font-family: system-ui, sans-serif;
        font-size: 14px;
    }
    @media print { .print-notice { display: none; } }
    </style>
</head>
<body>
    <div class="print-notice">
        <strong>Save as PDF:</strong> Press Ctrl/Cmd + P and select "Save as PDF" as destination.
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
 * Build PDF HTML structure (journal paper style)
 */
function kunaal_build_pdf_html($data) {
    $styles = kunaal_get_pdf_styles();
    
    $topics_html = '';
    if (!empty($data['topics']) && !is_wp_error($data['topics'])) {
        $topic_names = array_map(function($t) { return '#' . $t->name; }, $data['topics']);
        $topics_html = '<p class="pdf-topics">' . implode('  ', $topic_names) . '</p>';
    }
    
    $meta_parts = array();
    $meta_parts[] = '<span>' . esc_html($data['author']) . '</span>';
    $meta_parts[] = '<span class="sep">•</span>';
    $meta_parts[] = '<span>' . esc_html($data['date']) . '</span>';
    if (!empty($data['read_time'])) {
        $meta_parts[] = '<span class="sep">•</span>';
        $meta_parts[] = '<span>' . esc_html($data['read_time']) . ' min read</span>';
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
        <header class="pdf-header">
            <h1 class="pdf-title">' . esc_html($data['title']) . '</h1>
            ' . ($data['subtitle'] ? '<p class="pdf-subtitle">' . esc_html($data['subtitle']) . '</p>' : '') . '
            <div class="pdf-meta">' . implode(' ', $meta_parts) . '</div>
            ' . $topics_html . '
        </header>
        
        <div class="pdf-content">
            ' . $data['content'] . '
        </div>
        
        <footer class="pdf-footer">
            <p>' . esc_html($data['author']) . ' · ' . esc_html(parse_url($data['site_url'], PHP_URL_HOST)) . '</p>
        </footer>
    </div>
</body>
</html>';
}

/**
 * Process content for PDF output
 */
function kunaal_process_content_for_pdf($content) {
    // Expand accordions (only add 'open' if not already present - more robust check)
    $content = preg_replace_callback('/<details([^>]*)>/i', function($matches) {
        $attrs = $matches[1];
        // Check if 'open' attribute already exists (case-insensitive, with or without quotes)
        if (preg_match('/\bopen\b/i', $attrs)) {
            return '<details' . $attrs . '>';
        }
        // Add space before 'open' if attributes exist, otherwise just 'open'
        return '<details' . ($attrs ? $attrs . ' ' : ' ') . 'open>';
    }, $content);
    
    // Remove interactive elements
    $content = preg_replace('/<button[^>]*>.*?<\/button>/s', '', $content);
    
    // Remove action dock and panels
    $content = preg_replace('/<div[^>]*class="[^"]*(?:actionDock|sharePanel|subscribePanel)[^"]*"[^>]*>.*?<\/div>/s', '', $content);
    
    // Convert relative URLs to absolute
    $site_url = home_url();
    $content = preg_replace('/src="\/(?!\/)/i', 'src="' . $site_url . '/', $content);
    $content = preg_replace('/href="\/(?!\/)/i', 'href="' . $site_url . '/', $content);
    
    return $content;
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
