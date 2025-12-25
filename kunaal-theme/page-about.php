<?php
/**
 * Template Name: About Page
 * 
 * The Layered Exhibition - A grayscale photo gallery with pops of color
 * Flowing, layered composition with parallax and scrollytelling
 *
 * @package Kunaal_Theme
 */

get_header();

// Include interest icons mapping
if (file_exists(get_template_directory() . '/inc/interest-icons.php')) {
    require_once get_template_directory() . '/inc/interest-icons.php';
}

// ========================================
// GET THEME MOD DATA
// ========================================

// Author info
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = trim($first_name . ' ' . $last_name);
if (empty($full_name)) {
    $full_name = get_bloginfo('name');
}
$tagline = get_theme_mod('kunaal_author_tagline', get_bloginfo('description'));
$email = get_theme_mod('kunaal_contact_email', '');
$linkedin = get_theme_mod('kunaal_linkedin_handle', '');
$twitter = get_theme_mod('kunaal_twitter_handle', '');

// Hero
$hero_photo = get_theme_mod('kunaal_about_photo', '');
$hero_annotation = get_theme_mod('kunaal_about_hero_annotation', '');

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
$bio_year = get_theme_mod('kunaal_about_bio_year', '');
$pullquote_text = get_theme_mod('kunaal_about_pullquote', '');
$pullquote_attr = get_theme_mod('kunaal_about_pullquote_attr', '');

$map_intro = get_theme_mod('kunaal_about_map_intro', 'The places that shaped me');
$map_visited = get_theme_mod('kunaal_about_map_visited', '');
$map_lived = get_theme_mod('kunaal_about_map_lived', '');
$map_notes_raw = get_theme_mod('kunaal_about_map_notes', '');
$map_places_raw = get_theme_mod('kunaal_about_map_places', '');

$books_title = get_theme_mod('kunaal_about_books_title', 'Currently Reading');
$books_data = get_theme_mod('kunaal_about_books_data', '');

$interests_title = get_theme_mod('kunaal_about_interests_title', 'Things that fascinate me');
$interests_list = get_theme_mod('kunaal_about_interests_list', '');

$inspirations_title = get_theme_mod('kunaal_about_inspirations_title', 'People who inspire me');
$inspirations_data = get_theme_mod('kunaal_about_inspirations_data', '');

$stats_data = get_theme_mod('kunaal_about_stats_data', '');
$connect_title = get_theme_mod('kunaal_about_connect_title', 'Let\'s connect');

// Interstitial
$interstitial_image = get_theme_mod('kunaal_about_interstitial_image', '');
$interstitial_caption = get_theme_mod('kunaal_about_interstitial_caption', '');
$interstitial_quote = get_theme_mod('kunaal_about_interstitial_quote', '');

// ========================================
// SAFE JSON PARSER
// ========================================
if (!function_exists('kunaal_safe_json_decode')) {
    function kunaal_safe_json_decode($json_string, $field_name = '', &$warnings = array()) {
        if (empty($json_string)) {
            return array();
        }
        $data = json_decode($json_string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            if (!empty($field_name)) {
                $warnings[] = "{$field_name}: " . json_last_error_msg();
            }
            return array();
        }
        return is_array($data) ? $data : array();
    }
}

// Parse JSON data
$warnings = array();
$books = kunaal_safe_json_decode($books_data, 'Books', $warnings);
$inspirations = kunaal_safe_json_decode($inspirations_data, 'Inspirations', $warnings);
$stats = kunaal_safe_json_decode($stats_data, 'Stats', $warnings);
$map_places = kunaal_safe_json_decode($map_places_raw, 'Map Places', $warnings);
$map_notes = kunaal_safe_json_decode($map_notes_raw, 'Map Notes', $warnings);

// Parse interests
$interests = array();
if (!empty($interests_list)) {
    $lines = explode("\n", $interests_list);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        
        $size = 1;
        if (preg_match('/^(.+):(\d)$/', $line, $matches)) {
            $line = trim($matches[1]);
            $size = min(3, max(1, (int)$matches[2]));
        }
        
        $icon = function_exists('kunaal_get_interest_icon') ? kunaal_get_interest_icon($line) : '✨';
        
        $interests[] = array(
            'name' => $line,
            'size' => $size,
            'icon' => $icon,
        );
    }
}

// Get page content for bio
$bio_content = '';
while (have_posts()) : the_post();
    $bio_content = get_the_content();
endwhile;
?>

<main class="about-exhibition">

    <?php if (!empty($warnings) && is_user_logged_in() && current_user_can('edit_theme_options')) : ?>
    <div class="about-admin-notice">
        <strong>About page data warnings:</strong>
        <ul>
            <?php foreach ($warnings as $w) : ?>
            <li><?php echo esc_html($w); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- ========================================
         OPENING - Name emerges from imagery
         ======================================== -->
    <section class="about-opening about-layer-content">
        <?php if ($hero_photo) : ?>
        <div class="about-opening-bg about-layer-image">
            <img src="<?php echo esc_url($hero_photo); ?>" alt="" class="about-image parallax-slow">
        </div>
        <?php endif; ?>
        <div class="about-opening-overlay"></div>
        
        <div class="about-opening-content">
            <h1 class="about-name reveal-up">
                <span class="about-name-line"><?php echo esc_html($first_name); ?></span>
                <span class="about-name-line"><?php echo esc_html($last_name); ?></span>
            </h1>
            
            <?php if ($tagline) : ?>
            <p class="about-tagline reveal-up" data-delay="1"><?php echo esc_html($tagline); ?></p>
            <?php endif; ?>
            
            <?php if ($hero_annotation) : ?>
            <p class="about-annotation reveal-up" data-delay="2"><?php echo esc_html($hero_annotation); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="about-scroll-hint">Scroll</div>
    </section>

    <!-- ========================================
         BIO - Gallery wall text
         ======================================== -->
    <?php if ($show_bio && !empty(trim(strip_tags($bio_content)))) : ?>
    <section class="about-bio about-layer-content">
        <div class="gallery-label reveal-up">
            <span class="gallery-label-number">01</span>
            <h2 class="gallery-label-title"><?php echo esc_html($bio_title); ?></h2>
            <?php if ($bio_year) : ?>
            <span class="gallery-label-year">Est. <?php echo esc_html($bio_year); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="bio-text reveal-up" data-delay="1">
            <?php echo apply_filters('the_content', $bio_content); ?>
        </div>
        
        <?php if ($pullquote_text) : ?>
        <blockquote class="bio-pullquote reveal-up" data-delay="2">
            <?php echo esc_html($pullquote_text); ?>
            <?php if ($pullquote_attr) : ?>
            <span class="bio-pullquote-attr">— <?php echo esc_html($pullquote_attr); ?></span>
            <?php endif; ?>
        </blockquote>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- ========================================
         INTERSTITIAL IMAGE
         ======================================== -->
    <?php if ($interstitial_image) : ?>
        <?php if ($interstitial_quote) : ?>
        <section class="about-quote-image about-layer-image">
            <div class="about-quote-image-bg parallax-slow">
                <img src="<?php echo esc_url($interstitial_image); ?>" alt="" class="about-image">
            </div>
            <div class="about-quote-content reveal-up">
                <p class="about-quote-text">"<?php echo esc_html($interstitial_quote); ?>"</p>
                <?php if ($interstitial_caption) : ?>
                <span class="about-quote-attr">— <?php echo esc_html($interstitial_caption); ?></span>
                <?php endif; ?>
            </div>
        </section>
        <?php else : ?>
        <div class="atmo-full clip-angle-bottom about-layer-image">
            <img src="<?php echo esc_url($interstitial_image); ?>" alt="" class="about-image parallax-slow">
            <?php if ($interstitial_caption) : ?>
            <span class="about-quote-caption"><?php echo esc_html($interstitial_caption); ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- ========================================
         MAP - Interactive world map
         ======================================== -->
    <?php if ($show_map && ($map_visited || $map_lived || !empty($map_places))) : ?>
    <section class="about-map about-layer-content">
        <?php if ($map_intro) : ?>
        <p class="about-map-intro reveal-up"><?php echo esc_html($map_intro); ?></p>
        <?php endif; ?>
        
        <div class="about-map-container reveal-up" data-delay="1">
            <div id="about-map" 
                 data-visited="<?php echo esc_attr($map_visited); ?>"
                 data-lived="<?php echo esc_attr($map_lived); ?>"
                 data-notes="<?php echo esc_attr(wp_json_encode($map_notes)); ?>"
                 data-places="<?php echo esc_attr(wp_json_encode($map_places)); ?>">
            </div>
            <div class="map-tooltip"></div>
        </div>
        
        <div class="about-map-legend reveal-up" data-delay="2">
            <span class="legend-item"><span class="legend-dot lived"></span> Lived</span>
            <span class="legend-item"><span class="legend-dot visited"></span> Visited</span>
        </div>
    </section>
    <?php endif; ?>

    <!-- Color swatch divider -->
    <div class="swatch-divider blue"></div>

    <!-- ========================================
         BOOKSHELF - Library display
         ======================================== -->
    <?php if ($show_books && !empty($books)) : ?>
    <section class="about-books about-layer-content">
        <p class="about-books-label reveal-up"><?php echo esc_html($books_title); ?></p>
        
        <div class="bookshelf reveal-up" data-delay="1">
            <?php foreach ($books as $book) : ?>
            <div class="book-slot">
                <a href="<?php echo esc_url($book['link'] ?? '#'); ?>" target="_blank" rel="noopener" class="book-cover-3d">
                    <?php if (!empty($book['cover'])) : ?>
                    <img src="<?php echo esc_url($book['cover']); ?>" alt="<?php echo esc_attr($book['title'] ?? ''); ?>">
                    <?php else : ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;align-items:center;justify-content:center;color:white;font-size:11px;padding:8px;text-align:center;">
                        <?php echo esc_html($book['title'] ?? ''); ?>
                    </div>
                    <?php endif; ?>
                </a>
                <div class="book-info">
                    <p class="book-title"><?php echo esc_html($book['title'] ?? ''); ?></p>
                    <p class="book-author"><?php echo esc_html($book['author'] ?? ''); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="shelf-surface"></div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         INTERESTS - Floating cloud
         ======================================== -->
    <?php if ($show_interests && !empty($interests)) : ?>
    <section class="about-interests about-layer-content">
        <p class="about-interests-label reveal-up"><?php echo esc_html($interests_title); ?></p>
        
        <div class="interests-cloud reveal-up" data-delay="1">
            <?php foreach ($interests as $interest) : ?>
            <div class="interest-item size-<?php echo esc_attr($interest['size']); ?>">
                <div class="interest-icon"><?php echo esc_html($interest['icon']); ?></div>
                <span class="interest-name"><?php echo esc_html($interest['name']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         INSPIRATIONS - Portrait cards
         ======================================== -->
    <?php if ($show_inspirations && !empty($inspirations)) : ?>
    <section class="about-inspirations about-layer-content">
        <p class="about-inspirations-label reveal-up"><?php echo esc_html($inspirations_title); ?></p>
        
        <div class="inspirations-grid reveal-up" data-delay="1">
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
                <?php if (!empty($person['note'])) : ?>
                <p class="inspiration-note"><?php echo esc_html($person['note']); ?></p>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         STATS - Animated counters
         ======================================== -->
    <?php if ($show_stats && !empty($stats)) : ?>
    <section class="about-stats about-layer-content">
        <div class="stats-row">
            <?php foreach ($stats as $stat) : ?>
            <div class="stat-item reveal-up">
                <div class="stat-number" data-target="<?php echo esc_attr($stat['number'] ?? '0'); ?>">
                    <?php echo esc_html($stat['number'] ?? '0'); ?>
                </div>
                <div class="stat-label"><?php echo esc_html($stat['label'] ?? ''); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CONNECT
         ======================================== -->
    <?php if ($show_connect && ($email || $linkedin || $twitter)) : ?>
    <section class="about-connect about-layer-content">
        <h2 class="about-connect-title reveal-up"><?php echo esc_html($connect_title); ?></h2>
        
        <div class="connect-icons reveal-up" data-delay="1">
            <?php if ($email) : ?>
            <a href="mailto:<?php echo esc_attr($email); ?>" class="connect-icon" aria-label="Email">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="M22 6l-10 7L2 6"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if ($linkedin) : ?>
            <a href="<?php echo esc_url($linkedin); ?>" class="connect-icon" target="_blank" rel="noopener" aria-label="LinkedIn">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if ($twitter) : ?>
            <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="connect-icon" target="_blank" rel="noopener" aria-label="X / Twitter">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="connect-link reveal-up" data-delay="2">
            or drop me a note
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
