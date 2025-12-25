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

// Parse JSON data
$books = !empty($books_data) ? json_decode($books_data, true) : array();
$inspirations = !empty($inspirations_data) ? json_decode($inspirations_data, true) : array();
$stats = !empty($stats_data) ? json_decode($stats_data, true) : array();
$map_places = !empty($map_places_data) ? json_decode($map_places_data, true) : array();

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

<!-- Critical inline styles as fallback -->
<style>
.about-page-premium { background: #FDFCFB; color: #0b1220; min-height: 100vh; }
.about-hero { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
.hero-content { text-align: center; padding: 24px; }
.hero-portrait { width: 200px; height: 200px; border-radius: 50%; margin: 0 auto 40px; overflow: hidden; border: 2px solid #1E5AFF; }
.hero-portrait img { width: 100%; height: 100%; object-fit: cover; }
.hero-portrait .initials { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #1E5AFF; color: white; font-size: 56px; }
.hero-name { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 48px; font-weight: 400; margin: 0 0 16px; }
.hero-tagline { font-size: 18px; color: #6b7280; max-width: 480px; margin: 0 auto; }
.about-chapter { padding: 120px 24px; }
.chapter-container { max-width: 680px; margin: 0 auto; }
.chapter-container.wide { max-width: 1000px; }
.chapter-number { font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase; color: #1E5AFF; }
.chapter-title { font-size: 14px; letter-spacing: 0.15em; text-transform: uppercase; color: #6b7280; margin: 0 0 48px; }
.bio-content { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 18px; line-height: 1.8; }
.about-connect { text-align: center; padding: 120px 24px; }
.connect-heading { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 32px; margin: 0 0 40px; }
.connect-icons { display: flex; justify-content: center; gap: 16px; margin-bottom: 32px; }
.connect-icon-link { display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border: 1px solid #E5E5E5; border-radius: 50%; color: #6b7280; text-decoration: none; transition: all 0.3s ease; }
.connect-icon-link:hover { border-color: #1E5AFF; color: #1E5AFF; }
</style>

<main class="about-page-premium">

    <!-- ========================================
         HERO - The Portrait
         ======================================== -->
    <section class="about-step about-hero">
        <div class="hero-content" data-parallax="slow">
            <div class="hero-portrait reveal">
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
            <span class="chapter-number reveal"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title reveal reveal-stagger-1"><?php echo esc_html($bio_title); ?></h2>
            
            <div class="bio-content reveal reveal-stagger-2">
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
            <p class="reveal"><?php echo esc_html($interstitial_caption); ?></p>
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
            <span class="chapter-number reveal"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title reveal reveal-stagger-1"><?php echo esc_html($map_title); ?></h2>
            
            <?php if ($map_intro) : ?>
            <p class="bio-content reveal reveal-stagger-2" style="text-align: center; margin-bottom: 32px;">
                <?php echo esc_html($map_intro); ?>
            </p>
            <?php endif; ?>
            
            <div class="map-container reveal reveal-stagger-3">
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
            <span class="chapter-number reveal"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title reveal reveal-stagger-1"><?php echo esc_html($books_title); ?></h2>
            
            <div class="bookshelf">
                <div class="books-row">
                    <?php foreach ($books as $book) : ?>
                    <a href="<?php echo esc_url($book['link'] ?? '#'); ?>" class="book" target="_blank" rel="noopener">
                        <?php if (!empty($book['cover'])) : ?>
                        <div class="book-cover">
                            <img src="<?php echo esc_url($book['cover']); ?>" alt="<?php echo esc_attr($book['title'] ?? ''); ?>">
                        </div>
                        <?php else : ?>
                        <div class="book-spine">
                            <span class="book-spine-title"><?php echo esc_html($book['title'] ?? ''); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="book-info">
                            <span class="book-title"><?php echo esc_html($book['title'] ?? ''); ?></span>
                            <span class="book-author"><?php echo esc_html($book['author'] ?? ''); ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
                <div class="shelf-line"></div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CHAPTER 4 - Interests
         ======================================== -->
    <?php if ($show_interests && !empty($interests)) : $chapter++; ?>
    <section class="about-step about-chapter about-interests-section">
        <div class="chapter-container wide">
            <span class="chapter-number reveal"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title reveal reveal-stagger-1"><?php echo esc_html($interests_title); ?></h2>
            
            <div class="interests-cloud reveal reveal-stagger-2">
                <?php foreach ($interests as $interest) : ?>
                <span class="interest-tag size-<?php echo esc_attr($interest['size']); ?>">
                    <span class="interest-icon"><?php echo $interest['icon']; ?></span>
                    <span><?php echo esc_html($interest['name']); ?></span>
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
            <span class="chapter-number reveal"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title reveal reveal-stagger-1"><?php echo esc_html($inspirations_title); ?></h2>
            
            <div class="inspirations-grid reveal reveal-stagger-2">
                <?php foreach ($inspirations as $person) : ?>
                <a href="<?php echo esc_url($person['link'] ?? '#'); ?>" class="inspiration-card" target="_blank" rel="noopener">
                    <?php if (!empty($person['photo'])) : ?>
                    <div class="inspiration-photo">
                        <img src="<?php echo esc_url($person['photo']); ?>" alt="<?php echo esc_attr($person['name'] ?? ''); ?>">
                    </div>
                    <?php else : ?>
                    <div class="inspiration-photo placeholder">
                        <?php echo esc_html(substr($person['name'] ?? 'X', 0, 1)); ?>
                    </div>
                    <?php endif; ?>
                    <span class="inspiration-name"><?php echo esc_html($person['name'] ?? ''); ?></span>
                    <span class="inspiration-role"><?php echo esc_html($person['role'] ?? ''); ?></span>
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
            <span class="chapter-number reveal"><?php echo sprintf('%02d', $chapter); ?></span>
            <h2 class="chapter-title reveal reveal-stagger-1"><?php echo esc_html($stats_title); ?></h2>
            <?php endif; ?>
            
            <div class="stats-row reveal reveal-stagger-2">
                <?php foreach ($stats as $stat) : ?>
                <div class="stat-item">
                    <div class="stat-number" data-value="<?php echo esc_attr($stat['number'] ?? ''); ?>">0</div>
                    <div class="stat-label"><?php echo esc_html($stat['label'] ?? ''); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CONNECT
         ======================================== -->
    <?php if ($show_connect && ($email || $linkedin || $twitter)) : ?>
    <section class="about-step about-connect">
        <div class="chapter-container">
            <h2 class="connect-heading reveal"><?php echo esc_html($connect_title); ?></h2>
            
            <div class="connect-icons reveal reveal-stagger-1">
                <?php if ($email) : ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="connect-icon-link" aria-label="Email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                </a>
                <?php endif; ?>
                
                <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="connect-icon-link" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <?php endif; ?>
                
                <?php if ($twitter) : ?>
                <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="connect-icon-link" target="_blank" rel="noopener" aria-label="X / Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
            
            <p class="connect-cta reveal reveal-stagger-2">
                or <a href="<?php echo esc_url(home_url('/contact/')); ?>">drop me a note →</a>
            </p>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
