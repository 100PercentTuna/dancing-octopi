<?php
/**
 * Template Name: About Page
 * 
 * Premium scrollytelling About page
 * Sotheby's meets The Atlantic longform
 *
 * @package Kunaal_Theme
 */

get_header();

// Include interest icons mapping
if (file_exists(get_template_directory() . '/inc/interest-icons.php')) {
    require_once get_template_directory() . '/inc/interest-icons.php';
}

// Get basic info - with sensible defaults
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = trim($first_name . ' ' . $last_name);
if (empty($full_name)) {
    $full_name = get_bloginfo('name');
}
$tagline = get_theme_mod('kunaal_author_tagline', get_bloginfo('description'));
$avatar = get_theme_mod('kunaal_avatar', '');
$email = get_theme_mod('kunaal_contact_email', '');
$linkedin = get_theme_mod('kunaal_linkedin_handle', '');
$twitter = get_theme_mod('kunaal_twitter_handle', '');

// Hero settings - fall back to avatar if no specific about photo
$about_photo = get_theme_mod('kunaal_about_photo', '');
if (empty($about_photo)) {
    $about_photo = $avatar;
}

// Section toggles
$show_bio = get_theme_mod('kunaal_about_show_bio', true);
$show_map = get_theme_mod('kunaal_about_show_map', true);
$show_books = get_theme_mod('kunaal_about_show_books', true);
$show_interests = get_theme_mod('kunaal_about_show_interests', true);
$show_inspirations = get_theme_mod('kunaal_about_show_inspirations', true);
$show_stats = get_theme_mod('kunaal_about_show_stats', true);
$show_connect = get_theme_mod('kunaal_about_show_connect', true);

// Section content
$bio_title = get_theme_mod('kunaal_about_bio_title', 'About');
$map_title = get_theme_mod('kunaal_about_map_title', 'Places');
$map_intro = get_theme_mod('kunaal_about_map_intro', 'The places that have shaped who I am.');
$books_title = get_theme_mod('kunaal_about_books_title', 'Currently Reading');
$books_data = get_theme_mod('kunaal_about_books_data', '');
$interests_title = get_theme_mod('kunaal_about_interests_title', 'Things That Fascinate Me');
$interests_list = get_theme_mod('kunaal_about_interests_list', '');
$inspirations_title = get_theme_mod('kunaal_about_inspirations_title', 'People Who Inspire Me');
$inspirations_data = get_theme_mod('kunaal_about_inspirations_data', '');
$stats_title = get_theme_mod('kunaal_about_stats_title', '');
$stats_data = get_theme_mod('kunaal_about_stats_data', '');
$connect_title = get_theme_mod('kunaal_about_connect_title', 'Want to connect?');
$interstitial_image = get_theme_mod('kunaal_about_interstitial_image', '');
$interstitial_caption = get_theme_mod('kunaal_about_interstitial_caption', '');

// Map places data
$map_places_data = get_theme_mod('kunaal_about_map_places', '');

// Track JSON parse warnings for admins
$kunaal_about_json_warnings = array();

// Safe JSON parser (wrapped in function_exists to prevent redeclaration)
if (!function_exists('kunaal_safe_json_decode')) {
    function kunaal_safe_json_decode($json_string, $field_name, &$warnings) {
        if (empty($json_string)) {
            return array();
        }
        $data = json_decode($json_string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $warnings[] = "{$field_name}: Invalid JSON - " . json_last_error_msg();
            return array();
        }
        if (!is_array($data)) {
            $warnings[] = "{$field_name}: JSON must be an array";
            return array();
        }
        return $data;
    }
}

// Parse JSON data safely
$books = kunaal_safe_json_decode($books_data, 'Books', $kunaal_about_json_warnings);
$inspirations = kunaal_safe_json_decode($inspirations_data, 'Inspirations', $kunaal_about_json_warnings);
$stats = kunaal_safe_json_decode($stats_data, 'Stats', $kunaal_about_json_warnings);
$map_places = kunaal_safe_json_decode($map_places_data, 'Map Places', $kunaal_about_json_warnings);

// Parse interests
$interests = array();
if (!empty($interests_list)) {
    $lines = explode("\n", $interests_list);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $size = 1;
        if (preg_match('/^(.+):(\d)$/', $line, $matches)) {
            $line = $matches[1];
            $size = (int)$matches[2];
        }
        
        $icon = function_exists('kunaal_get_interest_icon') ? kunaal_get_interest_icon($line) : '✨';
        
        $interests[] = array(
            'name' => $line,
            'size' => $size,
            'icon' => $icon
        );
    }
}

// Chapter counter
$chapter = 0;
?>

<!-- BULLETPROOF INLINE CSS - Will work regardless of external stylesheet loading -->
<style>
/* Reset & Base */
.about-page-premium {
    background: #FDFCFB;
    color: #0b1220;
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.about-page-premium * { box-sizing: border-box; }

/* Hero Section */
.about-hero {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 24px;
}
.hero-content {
    text-align: center;
    max-width: 600px;
}
.hero-portrait {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    margin: 0 auto 40px;
    overflow: hidden;
    border: 3px solid #1E5AFF;
    background: #f0f0f0;
}
.hero-portrait img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.hero-portrait .initials {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1E5AFF 0%, #0b1220 100%);
    color: white;
    font-size: 56px;
    font-weight: 300;
    font-family: 'Cormorant Garamond', Georgia, serif;
}
.hero-name {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 48px;
    font-weight: 400;
    margin: 0 0 16px;
    color: #0b1220;
    line-height: 1.2;
}
.hero-tagline {
    font-size: 18px;
    color: #6b7280;
    line-height: 1.6;
    max-width: 480px;
    margin: 0 auto;
}
.scroll-indicator {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    width: 1px;
    height: 60px;
    background: linear-gradient(to bottom, #1E5AFF, transparent);
    animation: scrollPulse 2s ease-in-out infinite;
}
@keyframes scrollPulse {
    0%, 100% { opacity: 0.3; height: 60px; }
    50% { opacity: 1; height: 80px; }
}

/* Chapter Sections */
.about-chapter {
    padding: 100px 24px;
}
.chapter-container {
    max-width: 680px;
    margin: 0 auto;
}
.chapter-container.wide {
    max-width: 1000px;
}
.chapter-number {
    display: block;
    font-size: 12px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: #1E5AFF;
    margin-bottom: 8px;
}
.chapter-title {
    font-size: 13px;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #6b7280;
    margin: 0 0 48px;
    font-weight: 500;
}

/* Bio Section */
.bio-content {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 20px;
    line-height: 1.8;
    color: #374151;
}
.bio-content p {
    margin: 0 0 1.5em;
}
.bio-content p:first-of-type::first-letter {
    float: left;
    font-size: 4em;
    line-height: 0.8;
    padding-right: 12px;
    color: #1E5AFF;
    font-weight: 400;
}

/* Interstitial */
.about-interstitial {
    min-height: 60vh;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    position: relative;
}
.interstitial-caption {
    position: absolute;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255,255,255,0.95);
    padding: 12px 24px;
    border-radius: 4px;
    font-size: 13px;
    color: #6b7280;
}

/* Map Section */
.map-container {
    margin-top: 32px;
}
#places-map {
    width: 100%;
    height: 450px;
    border-radius: 8px;
    background: #f0f4f8;
    border: 1px solid #e5e7eb;
}
.map-legend {
    display: flex;
    justify-content: center;
    gap: 24px;
    margin-top: 16px;
    font-size: 13px;
    color: #6b7280;
}
.legend-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #1E5AFF;
    margin-right: 6px;
    vertical-align: middle;
}
.legend-dot.lived {
    background: #1E5AFF;
}

/* Bookshelf */
.bookshelf {
    margin-top: 32px;
}
.books-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 24px;
    margin-bottom: 16px;
}
.book-item {
    width: 110px;
    text-align: center;
    transition: transform 0.3s ease;
}
.book-item:hover {
    transform: translateY(-12px);
}
.book-cover {
    width: 110px;
    height: 165px;
    border-radius: 2px;
    overflow: hidden;
    box-shadow: 8px 8px 20px rgba(0,0,0,0.15);
    transform: perspective(1000px) rotateY(-5deg);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.book-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.book-cover-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    padding: 8px;
    text-align: center;
}
.book-info {
    margin-top: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.book-item:hover .book-info {
    opacity: 1;
}
.book-title {
    font-size: 13px;
    font-weight: 600;
    color: #0b1220;
    margin: 0 0 4px;
}
.book-author {
    font-size: 12px;
    color: #6b7280;
    margin: 0;
}
.shelf-line {
    height: 8px;
    background: linear-gradient(180deg, #8B7355 0%, #6B5344 50%, #5a4636 100%);
    border-radius: 2px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
}

/* Interests Cloud */
.interests-cloud {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px 16px;
    margin-top: 32px;
}
.interest-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #FFFFFF;
    border: 1px solid #E5E5E5;
    border-radius: 24px;
    padding: 10px 20px;
    font-size: 15px;
    font-weight: 500;
    color: #374151;
    transition: all 0.3s ease;
    animation: floatTag 4s ease-in-out infinite;
}
.interest-tag:nth-child(odd) { animation-delay: -1s; }
.interest-tag:nth-child(3n) { animation-delay: -2s; }
.interest-tag:hover {
    border-color: #1E5AFF;
    transform: translateY(-4px) scale(1.05);
}
.interest-tag.size-2 { font-size: 17px; padding: 12px 24px; }
.interest-tag.size-3 { font-size: 19px; padding: 14px 28px; font-weight: 600; }
.interest-icon { font-size: 1.2em; }
@keyframes floatTag {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

/* Inspirations Grid */
.inspirations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 24px;
    margin-top: 32px;
}
.inspiration-card {
    background: #FFFFFF;
    border: 1px solid #F0F0F0;
    border-radius: 8px;
    padding: 32px 24px;
    text-align: center;
    transition: all 0.3s ease;
}
.inspiration-card:hover {
    border-color: #1E5AFF;
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}
.inspiration-photo {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    margin: 0 auto 16px;
    overflow: hidden;
    border: 2px solid #F0F0F0;
    background: #f0f0f0;
}
.inspiration-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.inspiration-photo-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1E5AFF, #0b1220);
    color: white;
    font-size: 24px;
    font-weight: 300;
}
.inspiration-name {
    font-size: 16px;
    font-weight: 600;
    color: #0b1220;
    margin: 0 0 8px;
}
.inspiration-role {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
    margin: 0;
}

/* Stats Section */
.stats-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 48px;
    margin-top: 32px;
}
.stat-item {
    text-align: center;
    min-width: 120px;
}
.stat-number {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 56px;
    font-weight: 300;
    color: #1E5AFF;
    line-height: 1;
    margin-bottom: 8px;
}
.stat-label {
    font-size: 13px;
    font-weight: 500;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #6b7280;
}

/* Connect Section */
.about-connect {
    text-align: center;
    padding: 100px 24px;
    background: #f9fafb;
}
.connect-heading {
    font-family: 'Cormorant Garamond', Georgia, serif;
    font-size: 32px;
    font-weight: 400;
    margin: 0 0 40px;
    color: #0b1220;
}
.connect-icons {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-bottom: 32px;
}
.connect-icon-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border: 1px solid #E5E5E5;
    border-radius: 50%;
    color: #6b7280;
    text-decoration: none;
    transition: all 0.3s ease;
}
.connect-icon-link:hover {
    border-color: #1E5AFF;
    color: #1E5AFF;
    transform: scale(1.1);
}
.connect-icon-link svg {
    width: 24px;
    height: 24px;
}
.connect-note-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #1E5AFF;
    text-decoration: none;
    font-size: 15px;
    transition: gap 0.3s ease;
}
.connect-note-link:hover {
    gap: 12px;
}

/* Admin Notice */
.kunaal-admin-notice {
    max-width: 1000px;
    margin: 24px auto;
    padding: 12px 16px;
    border: 1px solid #f3c6c6;
    background: #fff7f7;
    color: #7a1f1f;
    border-radius: 8px;
}
.kunaal-admin-notice ul {
    margin: 8px 0 0 18px;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-name { font-size: 36px; }
    .hero-portrait { width: 160px; height: 160px; }
    .about-chapter { padding: 60px 20px; }
    .stat-number { font-size: 40px; }
    .stats-row { gap: 32px; }
    .books-row { gap: 16px; }
    .book-item { width: 90px; }
    .book-cover { width: 90px; height: 135px; }
}
</style>

<main class="about-page-premium">

    <?php if (!empty($kunaal_about_json_warnings) && is_user_logged_in() && current_user_can('edit_theme_options')) : ?>
    <div class="kunaal-admin-notice">
        <strong>About page settings need attention:</strong>
        <ul>
            <?php foreach ($kunaal_about_json_warnings as $warning) : ?>
            <li><?php echo esc_html($warning); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- ========================================
         HERO - The Portrait (ALWAYS SHOWS)
         ======================================== -->
    <section class="about-step about-hero">
        <div class="hero-content" data-parallax="slow">
            <div class="hero-portrait">
                <?php if ($about_photo) : ?>
                    <img src="<?php echo esc_url($about_photo); ?>" alt="<?php echo esc_attr($full_name); ?>">
                <?php else : ?>
                    <span class="initials"><?php echo esc_html(kunaal_get_initials()); ?></span>
                <?php endif; ?>
            </div>
            <h1 class="hero-name"><?php echo esc_html($full_name); ?></h1>
            <?php if ($tagline) : ?>
            <p class="hero-tagline"><?php echo esc_html($tagline); ?></p>
            <?php endif; ?>
        </div>
        <div class="scroll-indicator"></div>
    </section>

    <!-- ========================================
         CHAPTER 1 - Bio / Introduction
         ======================================== -->
    <?php if ($show_bio) : $chapter++; 
        // Get page content
        $bio_content = '';
        while (have_posts()) : the_post();
            $bio_content = get_the_content();
        endwhile;
        // Only show section if there's content
        if (!empty(trim(strip_tags($bio_content)))) :
    ?>
    <section class="about-step about-chapter about-bio-section">
        <div class="chapter-container">
            <span class="chapter-number"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title"><?php echo esc_html($bio_title); ?></h2>
            
            <div class="bio-content">
                <?php echo apply_filters('the_content', $bio_content); ?>
            </div>
        </div>
    </section>
    <?php endif; endif; ?>

    <!-- ========================================
         INTERSTITIAL - Full Bleed Image
         ======================================== -->
    <?php if ($interstitial_image) : ?>
    <section class="about-step about-interstitial" style="background-image: url('<?php echo esc_url($interstitial_image); ?>');">
        <?php if ($interstitial_caption) : ?>
        <div class="interstitial-caption">
            <p><?php echo esc_html($interstitial_caption); ?></p>
        </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CHAPTER 2 - Map (Places)
         ======================================== -->
    <?php if ($show_map && !empty($map_places)) : $chapter++; ?>
    <section class="about-step about-chapter about-map-section">
        <div class="chapter-container wide">
            <span class="chapter-number"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title"><?php echo esc_html($map_title); ?></h2>
            
            <?php if ($map_intro) : ?>
            <p class="bio-content" style="text-align: center; margin-bottom: 32px;">
                <?php echo esc_html($map_intro); ?>
            </p>
            <?php endif; ?>
            
            <div class="map-container">
                <div id="places-map" data-places="<?php echo esc_attr(wp_json_encode($map_places)); ?>"></div>
                <div class="map-legend">
                    <span class="legend-item"><span class="legend-dot lived"></span> Lived</span>
                    <span class="legend-item"><span class="legend-dot"></span> Visited</span>
                </div>
            </div>
        </div>
        <div id="map-sidebar"></div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CHAPTER 3 - Bookshelf
         ======================================== -->
    <?php if ($show_books && !empty($books)) : $chapter++; ?>
    <section class="about-step about-chapter about-books-section">
        <div class="chapter-container wide">
            <span class="chapter-number"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title"><?php echo esc_html($books_title); ?></h2>
            
            <div class="bookshelf">
                <div class="books-row">
                    <?php foreach ($books as $book) : ?>
                    <div class="book-item">
                        <a href="<?php echo esc_url($book['link'] ?? '#'); ?>" target="_blank" rel="noopener" class="book-cover">
                            <?php if (!empty($book['cover'])) : ?>
                                <img src="<?php echo esc_url($book['cover']); ?>" alt="<?php echo esc_attr($book['title'] ?? ''); ?>">
                            <?php else : ?>
                                <span class="book-cover-placeholder"><?php echo esc_html($book['title'] ?? ''); ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="book-info">
                            <p class="book-title"><?php echo esc_html($book['title'] ?? ''); ?></p>
                            <p class="book-author"><?php echo esc_html($book['author'] ?? ''); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="shelf-line"></div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CHAPTER 4 - Interests Cloud
         ======================================== -->
    <?php if ($show_interests && !empty($interests)) : $chapter++; ?>
    <section class="about-step about-chapter about-interests-section">
        <div class="chapter-container wide">
            <span class="chapter-number"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title"><?php echo esc_html($interests_title); ?></h2>
            
            <div class="interests-cloud">
                <?php foreach ($interests as $interest) : ?>
                <span class="interest-tag size-<?php echo esc_attr($interest['size']); ?>">
                    <span class="interest-icon"><?php echo esc_html($interest['icon']); ?></span>
                    <?php echo esc_html($interest['name']); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CHAPTER 5 - Inspirations
         ======================================== -->
    <?php if ($show_inspirations && !empty($inspirations)) : $chapter++; ?>
    <section class="about-step about-chapter about-inspirations-section">
        <div class="chapter-container wide">
            <span class="chapter-number"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title"><?php echo esc_html($inspirations_title); ?></h2>
            
            <div class="inspirations-grid">
                <?php foreach ($inspirations as $person) : ?>
                <a href="<?php echo esc_url($person['link'] ?? '#'); ?>" target="_blank" rel="noopener" class="inspiration-card">
                    <div class="inspiration-photo">
                        <?php if (!empty($person['photo'])) : ?>
                            <img src="<?php echo esc_url($person['photo']); ?>" alt="<?php echo esc_attr($person['name'] ?? ''); ?>">
                        <?php else : ?>
                            <span class="inspiration-photo-placeholder"><?php echo esc_html(substr($person['name'] ?? '?', 0, 1)); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="inspiration-name"><?php echo esc_html($person['name'] ?? ''); ?></h3>
                    <p class="inspiration-role"><?php echo esc_html($person['role'] ?? ''); ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CHAPTER 6 - Stats
         ======================================== -->
    <?php if ($show_stats && !empty($stats)) : $chapter++; ?>
    <section class="about-step about-chapter about-stats-section">
        <div class="chapter-container">
            <?php if ($stats_title) : ?>
            <span class="chapter-number"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title"><?php echo esc_html($stats_title); ?></h2>
            <?php endif; ?>
            
            <div class="stats-row">
                <?php foreach ($stats as $stat) : ?>
                <div class="stat-item">
                    <div class="stat-number" data-target="<?php echo esc_attr($stat['number'] ?? '0'); ?>">
                        <?php echo esc_html($stat['number'] ?? '0'); ?>
                    </div>
                    <div class="stat-label"><?php echo esc_html($stat['label'] ?? ''); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CONNECT - Always shows if any link exists
         ======================================== -->
    <?php if ($show_connect && ($email || $linkedin || $twitter)) : ?>
    <section class="about-step about-connect">
        <h2 class="connect-heading"><?php echo esc_html($connect_title); ?></h2>
        
        <div class="connect-icons">
            <?php if ($email) : ?>
            <a href="mailto:<?php echo esc_attr($email); ?>" class="connect-icon-link" aria-label="Email">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="M22 6l-10 7L2 6"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if ($linkedin) : ?>
            <a href="<?php echo esc_url($linkedin); ?>" class="connect-icon-link" target="_blank" rel="noopener" aria-label="LinkedIn">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if ($twitter) : ?>
            <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="connect-icon-link" target="_blank" rel="noopener" aria-label="X / Twitter">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="connect-note-link">
            or drop me a note
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
