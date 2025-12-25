<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo esc_html(get_the_title()); ?></title>
    <style>
        @page {
            margin: 2cm;
        }
        
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        body {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.7;
            color: #0b1220;
            background: #FDFCFA;
        }
        
        /* Cover Page */
        .pdf-cover {
            text-align: center;
            padding: 4cm 2cm;
            page-break-after: always;
        }
        
        .pdf-avatar {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            margin: 0 auto 2cm;
            display: block;
        }
        
        .pdf-title {
            font-size: 28pt;
            font-weight: 600;
            line-height: 1.2;
            color: #0b1220;
            margin: 0 0 0.75cm;
        }
        
        .pdf-subtitle {
            font-size: 13pt;
            line-height: 1.5;
            color: rgba(11,18,32,0.7);
            margin: 0 0 1cm;
        }
        
        .pdf-byline {
            font-size: 12pt;
            color: rgba(11,18,32,0.6);
            margin: 0 0 1.5cm;
        }
        
        .pdf-meta {
            font-size: 10pt;
            color: rgba(11,18,32,0.5);
        }
        
        /* Content */
        .pdf-content {
            text-align: justify;
        }
        
        .pdf-content h2,
        .pdf-content h3 {
            font-weight: 600;
            color: #0b1220;
            margin: 1.5em 0 0.75em;
        }
        
        .pdf-content h2 { font-size: 18pt; }
        .pdf-content h3 { font-size: 14pt; }
        
        .pdf-content p {
            margin: 0 0 1em;
        }
        
        .pdf-content img {
            max-width: 100%;
            height: auto;
            margin: 1.5em 0;
        }
        
        .pdf-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5em 0;
        }
        
        .pdf-content td, .pdf-content th {
            padding: 0.5em;
            border: 1pt solid #ddd;
        }
        
        /* Author Box */
        .pdf-author-box {
            margin-top: 3cm;
            padding: 1cm;
            background: #F9F7F4;
            border-left: 3pt solid #7D6B5D;
            page-break-inside: avoid;
        }
        
        .pdf-author-box h4 {
            font-size: 12pt;
            margin: 0 0 0.5cm;
            color: #7D6B5D;
        }
        
        .pdf-author-box p {
            font-size: 10pt;
            margin: 0.25cm 0;
        }
    </style>
</head>
<body>
    <?php
    $post_id = get_the_ID();
    $subtitle = get_post_meta($post_id, 'subtitle', true);
    $read_time = get_post_meta($post_id, 'read_time', true);
    $avatar = get_theme_mod('kunaal_avatar', '');
    $first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
    $last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
    $author_name = $first_name . ' ' . $last_name;
    $email = get_theme_mod('kunaal_contact_email', '');
    ?>
    
    <!-- Cover Page -->
    <div class="pdf-cover">
        <?php if ($avatar) : ?>
            <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="pdf-avatar" />
        <?php endif; ?>
        
        <h1 class="pdf-title"><?php the_title(); ?></h1>
        
        <?php if ($subtitle) : ?>
            <p class="pdf-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
        
        <p class="pdf-byline">by <?php echo esc_html($author_name); ?></p>
        
        <p class="pdf-meta">
            <?php echo esc_html(get_the_date('j F Y')); ?>
            <?php if ($read_time) : ?>
                &nbsp;•&nbsp; <?php echo esc_html($read_time); ?> min read
            <?php endif; ?>
        </p>
    </div>
    
    <!-- Content -->
    <div class="pdf-content">
        <?php
        $content = get_the_content();
        // Expand accordions
        $content = str_replace('<details', '<details open', $content);
        echo apply_filters('the_content', $content);
        ?>
    </div>
    
    <!-- Author Box -->
    <div class="pdf-author-box">
        <h4>About the Author</h4>
        <p><strong><?php echo esc_html($author_name); ?></strong></p>
        <?php if ($email) : ?>
            <p><?php echo esc_html($email); ?></p>
        <?php endif; ?>
        <p><?php echo esc_url(home_url()); ?></p>
    </div>
</body>
</html>
