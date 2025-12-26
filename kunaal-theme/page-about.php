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
?>

<!-- Skip Link handled in header.php for consistency -->

<?php
// ========================================
// GET THEME MOD DATA (Using NO-JSON helpers)
// ========================================

// Author info (from existing Author section)
$first_name = kunaal_mod('kunaal_author_first_name', 'Kunaal');
$last_name = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = trim($first_name . ' ' . $last_name);
if (empty($full_name)) {
    $full_name = get_bloginfo('name');
}
$tagline = kunaal_mod('kunaal_author_tagline', get_bloginfo('description'));
$email = kunaal_mod('kunaal_contact_email', '');
$linkedin = kunaal_mod('kunaal_linkedin_handle', '');
$twitter = kunaal_mod('kunaal_twitter_handle', '');

// Hero
$hero_annotation = kunaal_mod('kunaal_about_hero_annotation', 'still figuring it out');
$hero_photos = function_exists('kunaal_get_hero_photos_v2') ? kunaal_get_hero_photos_v2() : array();

// Fallback to old single photo if no new photos set
if (empty($hero_photos)) {
    $old_photo = kunaal_mod('kunaal_about_photo', '');
    if ($old_photo) {
        $hero_photos = array($old_photo);
    }
}

// Section toggles
$show_hero = kunaal_mod('kunaal_about_hero_show', true);
$show_bio = kunaal_mod('kunaal_about_bio_show', kunaal_mod('kunaal_about_show_bio', true));
$show_map = kunaal_mod('kunaal_about_map_show', kunaal_mod('kunaal_about_show_map', true));
$show_books = kunaal_mod('kunaal_about_books_show', kunaal_mod('kunaal_about_show_books', true));
$show_interests = kunaal_mod('kunaal_about_interests_show', kunaal_mod('kunaal_about_show_interests', true));
$show_inspirations = kunaal_mod('kunaal_about_inspirations_show', kunaal_mod('kunaal_about_show_inspirations', true));
$show_stats = kunaal_mod('kunaal_about_stats_show', kunaal_mod('kunaal_about_show_stats', true));
$show_connect = kunaal_mod('kunaal_about_connect_show', kunaal_mod('kunaal_about_show_connect', true));

// Bio section
$bio_year = kunaal_mod('kunaal_about_bio_year', '');
$pullquote_show = kunaal_mod('kunaal_about_pullquote_show', false);
$pullquote_text = kunaal_mod('kunaal_about_pullquote_text', kunaal_mod('kunaal_about_pullquote', ''));
$pullquote_attr = kunaal_mod('kunaal_about_pullquote_attr', '');

// Map section
$map_label = kunaal_mod('kunaal_about_map_label', "Places I've Called Home");
$map_intro = kunaal_mod('kunaal_about_map_intro_v2', kunaal_mod('kunaal_about_map_intro', 'The places that have shaped who I am.'));
$map_visited = kunaal_mod('kunaal_map_visited', kunaal_mod('kunaal_about_map_visited', ''));
$map_lived = kunaal_mod('kunaal_map_lived', kunaal_mod('kunaal_about_map_lived', ''));
$map_current = kunaal_mod('kunaal_map_current', '');
$map_stories = function_exists('kunaal_get_map_stories_v2') ? kunaal_get_map_stories_v2() : array();

// Books section - use new helpers with fallback
$books_label = kunaal_mod('kunaal_about_books_label', kunaal_mod('kunaal_about_books_title', 'Currently Reading'));
$books = function_exists('kunaal_get_books_v2') ? kunaal_get_books_v2() : array();

// Interests section - use new helpers with fallback
$interests_label = kunaal_mod('kunaal_about_interests_label', kunaal_mod('kunaal_about_interests_title', 'Things That Fascinate Me'));
$interests = function_exists('kunaal_get_interests_v2') ? kunaal_get_interests_v2() : array();

// Inspirations section - use new helpers with fallback
$inspirations_label = kunaal_mod('kunaal_about_inspirations_label', kunaal_mod('kunaal_about_inspirations_title', 'People Who Inspire Me'));
$inspirations = function_exists('kunaal_get_inspirations_v2') ? kunaal_get_inspirations_v2() : array();

// Stats section - use new helpers
$stats = function_exists('kunaal_get_stats_v2') ? kunaal_get_stats_v2() : array();

// Connect section
$connect_heading = kunaal_mod('kunaal_about_connect_heading', kunaal_mod('kunaal_about_connect_title', "Let's Connect"));

// Atmospheric images
$atmo_images = function_exists('kunaal_get_atmo_images_v2') ? kunaal_get_atmo_images_v2('all') : array();

// Get page content for bio
$bio_content = '';
while (have_posts()) : the_post();
    $bio_content = get_the_content();
endwhile;

// Helper function moved to functions.php to prevent side effects
?>

<main id="about-content" class="about-exhibition" role="main" aria-label="About page content" tabindex="-1">

    <?php if ($show_hero) : ?>
    <!-- ========================================
         HERO - Photo Collage with Name
         ======================================== -->
    <section class="about-hero about-layer-content <?php echo empty($hero_photos) ? 'about-hero--minimal' : ''; ?>" aria-label="Introduction">
        
        <?php if (!empty($hero_photos)) : ?>
        <div class="hero-collage" data-photo-count="<?php echo count($hero_photos); ?>">
            <?php foreach ($hero_photos as $index => $photo_url) : 
                $photo_num = $index + 1;
                $clip_class = $photo_num <= 3 ? "hero-photo--clip-{$photo_num}" : '';
            ?>
            <div class="hero-photo hero-photo--<?php echo $photo_num; ?> <?php echo $clip_class; ?>" data-parallax-speed="<?php echo array(0.2, 0.6, 0.4, 0.3)[$index] ?? 0.3; ?>">
                <img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($full_name); ?>" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <div class="hero-identity reveal-up <?php echo empty($hero_photos) ? 'hero-identity--centered' : ''; ?>">
            <h1 class="hero-name">
                <span class="hero-name-first"><?php echo esc_html($first_name); ?></span>
                <span class="hero-name-last"><?php echo esc_html($last_name); ?></span>
            </h1>
            
            <?php if ($tagline) : ?>
            <p class="hero-tagline"><?php 
                // Split tagline by common delimiters and wrap in spans
                $parts = preg_split('/\s*[·|,]\s*/', $tagline);
                echo implode('', array_map(function($part) {
                    return '<span>' . esc_html(trim($part)) . '</span>';
                }, array_filter($parts)));
            ?></p>
            <?php endif; ?>
        </div>
        
        <?php if ($hero_annotation) : ?>
        <p class="hero-annotation" aria-hidden="true"><?php echo esc_html($hero_annotation); ?></p>
        <?php endif; ?>
        
        <div class="hero-scroll-hint" aria-hidden="true">
            <span class="hero-scroll-hint__text">Scroll</span>
            <span class="hero-scroll-hint__line"></span>
        </div>
    </section>
    <?php endif; ?>

    <?php kunaal_render_atmo_images('after_hero', $atmo_images); ?>

    <?php if ($show_bio && !empty(trim(strip_tags($bio_content)))) : ?>
    <!-- ========================================
         BIO - Gallery wall text with drop cap
         ======================================== -->
    <section class="about-bio about-layer-content" aria-label="Biography">
        <h2 class="sr-only">About</h2>
        <div class="gallery-label reveal-up" aria-hidden="true">
            <span class="gallery-label-number">01</span>
            <span class="gallery-label-title">About</span>
            <?php if ($bio_year) : ?>
            <span class="gallery-label-year">Est. <?php echo esc_html($bio_year); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="bio-text reveal-up" data-delay="1">
            <?php echo apply_filters('the_content', $bio_content); ?>
        </div>
        
        <?php if ($pullquote_show && !empty($pullquote_text)) : ?>
        <blockquote class="bio-pullquote reveal-up" data-delay="2">
            <p><?php echo esc_html($pullquote_text); ?></p>
            <?php if ($pullquote_attr) : ?>
            <cite class="bio-pullquote-attr">— <?php echo esc_html($pullquote_attr); ?></cite>
            <?php endif; ?>
        </blockquote>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <?php if ($show_books && !empty($books)) : ?>
    <!-- ========================================
         BOOKSHELF - 3D book display
         ======================================== -->
    <section class="about-books about-layer-content" aria-label="Currently reading">
        <h2 class="sr-only"><?php echo esc_html($books_label); ?></h2>
        <p class="about-books-label reveal-up" aria-hidden="true"><?php echo esc_html($books_label); ?></p>
        
        <div class="bookshelf reveal-up" data-delay="1" role="list">
            <?php foreach ($books as $index => $book) : 
                $book_id = 'book-tooltip-' . ($index + 1);
                $is_link = !empty($book['link']);
                $tag = $is_link ? 'a' : 'div';
            ?>
            <div class="book-slot" role="listitem">
                <?php if ($is_link) : ?>
                <a href="<?php echo esc_url($book['link']); ?>" 
                   class="book-cover-3d"
                   target="_blank" 
                   rel="noopener"
                   aria-label="<?php echo esc_attr($book['title']); ?> by <?php echo esc_attr($book['author']); ?> - opens in new tab">
                <?php else : ?>
                <div class="book-cover-3d" 
                     role="button"
                     tabindex="0"
                     aria-describedby="<?php echo $book_id; ?>"
                     aria-label="<?php echo esc_attr($book['title']); ?> by <?php echo esc_attr($book['author']); ?>">
                <?php endif; ?>
                    <?php if (!empty($book['cover'])) : ?>
                    <img src="<?php echo esc_url($book['cover']); ?>" alt="">
                    <?php else : ?>
                    <div class="book-cover-placeholder">
                        <span><?php echo esc_html($book['title']); ?></span>
                    </div>
                    <?php endif; ?>
                <?php echo $is_link ? '</a>' : '</div>'; ?>
                <div class="book-info" id="<?php echo $book_id; ?>" role="tooltip">
                    <p class="book-title"><?php echo esc_html($book['title']); ?></p>
                    <p class="book-author"><?php echo esc_html($book['author']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="shelf-surface" aria-hidden="true"></div>
    </section>
    <?php endif; ?>

    <?php kunaal_render_atmo_images('after_bio', $atmo_images); ?>

    <?php if ($show_map && ($map_visited || $map_lived)) : ?>
    <!-- ========================================
         MAP - Interactive world map with country shading
         ======================================== -->
    <section class="about-map about-layer-content" aria-label="Places I've been">
        <h2 class="sr-only"><?php echo esc_html($map_label); ?></h2>
        <div class="gallery-label reveal-up" aria-hidden="true">
            <span class="gallery-label-number">02</span>
            <span class="gallery-label-title"><?php echo esc_html($map_label); ?></span>
        </div>
        
        <?php if ($map_intro) : ?>
        <p class="about-map-intro reveal-up"><?php echo esc_html($map_intro); ?></p>
        <?php endif; ?>
        
        <div class="about-map-container reveal-up" data-delay="1">
            <div id="about-map" 
                 data-visited="<?php echo esc_attr($map_visited); ?>"
                 data-lived="<?php echo esc_attr($map_lived); ?>"
                 data-current="<?php echo esc_attr($map_current); ?>"
                 data-stories="<?php echo esc_attr(wp_json_encode($map_stories)); ?>"
                 role="application"
                 tabindex="0"
                 aria-label="Interactive world map showing countries I've visited and lived in. Use arrow keys to pan, plus and minus to zoom."
                 aria-describedby="map-instructions">
            </div>
            <p id="map-instructions" class="sr-only">
                Click or tap on highlighted countries to view stories. Use arrow keys to pan the map, plus and minus keys to zoom.
            </p>
            <div class="map-tooltip" role="tooltip" aria-hidden="true"></div>
            <?php if ($map_current) : ?>
            <!-- Current location marker (pulsing) per 05-WORLD-MAP.md -->
            <div class="map-current-marker" data-country="<?php echo esc_attr($map_current); ?>" aria-hidden="true">
                <div class="map-current-marker__pulse"></div>
                <div class="map-current-marker__dot"></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="about-map-legend reveal-up" data-delay="2">
            <span class="legend-item"><span class="legend-dot lived"></span> Lived</span>
            <span class="legend-item"><span class="legend-dot visited"></span> Visited</span>
            <?php if ($map_current) : ?>
            <span class="legend-item"><span class="legend-dot current"></span> Now</span>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php kunaal_render_atmo_images('after_map', $atmo_images); ?>

    <?php if ($show_interests && !empty($interests)) : ?>
    <!-- ========================================
         INTERESTS - Cloud with circular photos
         ======================================== -->
    <section class="about-interests about-layer-content" aria-label="Things I'm interested in">
        <h2 class="sr-only"><?php echo esc_html($interests_label); ?></h2>
        <div class="gallery-label reveal-up" aria-hidden="true">
            <span class="gallery-label-number">03</span>
            <span class="gallery-label-title"><?php echo esc_html($interests_label); ?></span>
        </div>
        
        <div class="interests-cloud reveal-up" data-delay="1">
            <?php foreach ($interests as $interest) : ?>
            <div class="interest-item">
                <div class="interest-image">
                    <?php if (!empty($interest['image'])) : ?>
                    <img src="<?php echo esc_url($interest['image']); ?>" alt="<?php echo esc_attr($interest['name']); ?>">
                    <?php else : ?>
                    <span class="interest-placeholder"><?php echo esc_html(mb_substr($interest['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <span class="interest-label"><?php echo esc_html($interest['name']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php kunaal_render_atmo_images('after_interests', $atmo_images); ?>

    <?php if ($show_inspirations && !empty($inspirations)) : ?>
    <!-- ========================================
         INSPIRATIONS - Portrait cards (links get blue)
         ======================================== -->
    <section class="about-inspirations about-layer-content" aria-label="People who inspire me">
        <h2 class="sr-only"><?php echo esc_html($inspirations_label); ?></h2>
        <div class="gallery-label reveal-up" aria-hidden="true">
            <span class="gallery-label-number">04</span>
            <span class="gallery-label-title"><?php echo esc_html($inspirations_label); ?></span>
        </div>
        
        <div class="inspirations-grid reveal-up" data-delay="1">
            <?php foreach ($inspirations as $person) : 
                $has_link = !empty($person['link']);
                $tag = $has_link ? 'a' : 'div';
                $attrs = $has_link ? 'href="' . esc_url($person['link']) . '" target="_blank" rel="noopener"' : '';
                $class = $has_link ? 'inspiration-card inspiration-card--link' : 'inspiration-card';
            ?>
            <<?php echo $tag; ?> <?php echo $attrs; ?> class="<?php echo $class; ?>">
                <div class="inspiration-photo">
                    <?php if (!empty($person['photo'])) : ?>
                    <img src="<?php echo esc_url($person['photo']); ?>" alt="<?php echo esc_attr($person['name']); ?>">
                    <?php else : ?>
                    <span class="inspiration-photo-placeholder"><?php echo esc_html(mb_substr($person['name'], 0, 1)); ?></span>
                    <?php endif; ?>
                </div>
                <h3 class="inspiration-name"><?php echo esc_html($person['name']); ?></h3>
                <p class="inspiration-role"><?php echo esc_html($person['role']); ?></p>
                <?php if (!empty($person['note'])) : ?>
                <p class="inspiration-note">"<?php echo esc_html($person['note']); ?>"</p>
                <?php endif; ?>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php kunaal_render_atmo_images('after_inspirations', $atmo_images); ?>

    <?php if ($show_stats && !empty($stats)) : ?>
    <!-- ========================================
         STATS - Animated counters
         ======================================== -->
    <section class="about-stats about-layer-content" aria-label="By the numbers">
        <h2 class="sr-only">Stats</h2>
        <div class="stats-row">
            <?php foreach ($stats as $stat) : ?>
            <div class="stat-item reveal-up">
                <div class="stat-number" data-target="<?php echo esc_attr($stat['number']); ?>">
                    <?php echo esc_html($stat['number']); ?>
                </div>
                <div class="stat-label"><?php echo esc_html($stat['label']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php kunaal_render_atmo_images('before_closing', $atmo_images); ?>

    <?php if ($show_connect && ($email || $linkedin || $twitter)) : ?>
    <!-- ========================================
         CONNECT - Social links
         ======================================== -->
    <section class="about-connect about-layer-content" aria-label="Connect with me">
        <h2 class="sr-only"><?php echo esc_html($connect_heading); ?></h2>
        <p class="about-connect-title reveal-up" aria-hidden="true"><?php echo esc_html($connect_heading); ?></p>
        
        <div class="connect-icons reveal-up" data-delay="1">
            <?php if ($email) : ?>
            <a href="mailto:<?php echo esc_attr($email); ?>" class="connect-icon" aria-label="Email me">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="M22 6l-10 7L2 6"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if ($linkedin) : ?>
            <a href="<?php echo esc_url($linkedin); ?>" class="connect-icon" target="_blank" rel="noopener" aria-label="LinkedIn profile">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
            </a>
            <?php endif; ?>
            
            <?php if ($twitter) : ?>
            <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="connect-icon" target="_blank" rel="noopener" aria-label="X / Twitter profile">
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                </svg>
            </a>
            <?php endif; ?>
        </div>
        
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="connect-link reveal-up" data-delay="2">
            or drop me a note
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16" aria-hidden="true">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </a>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
