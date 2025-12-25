<?php
/**
 * Template Name: About Page
 * 
 * Modular About page with toggle-able sections
 *
 * @package Kunaal_Theme
 */

get_header();

// Include interest icons mapping
require_once get_template_directory() . '/inc/interest-icons.php';

// Get basic info
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = $first_name . ' ' . $last_name;
$tagline = get_theme_mod('kunaal_author_tagline', '');
$avatar = get_theme_mod('kunaal_avatar', '');
$email = get_theme_mod('kunaal_contact_email', '');
$linkedin = get_theme_mod('kunaal_linkedin_handle', '');
$twitter = get_theme_mod('kunaal_twitter_handle', '');

// Hero settings
$about_greeting = get_theme_mod('kunaal_about_greeting', 'Hi, I\'m ' . $first_name . '.');
$about_photo = get_theme_mod('kunaal_about_photo', $avatar);

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
$map_visited = get_theme_mod('kunaal_about_map_visited', '');
$map_lived = get_theme_mod('kunaal_about_map_lived', '');
$map_notes = get_theme_mod('kunaal_about_map_notes', '');
$books_title = get_theme_mod('kunaal_about_books_title', 'Currently Reading');
$books_data = get_theme_mod('kunaal_about_books_data', '');
$interests_title = get_theme_mod('kunaal_about_interests_title', 'Things I Love');
$interests_list = get_theme_mod('kunaal_about_interests_list', '');
$inspirations_title = get_theme_mod('kunaal_about_inspirations_title', 'People Who Inspire Me');
$inspirations_data = get_theme_mod('kunaal_about_inspirations_data', '');
$stats_title = get_theme_mod('kunaal_about_stats_title', 'By the Numbers');
$stats_data = get_theme_mod('kunaal_about_stats_data', '');
$connect_title = get_theme_mod('kunaal_about_connect_title', 'Say Hello');

// Parse JSON data
$books = !empty($books_data) ? json_decode($books_data, true) : array();
$inspirations = !empty($inspirations_data) ? json_decode($inspirations_data, true) : array();
$stats = !empty($stats_data) ? json_decode($stats_data, true) : array();
$notes = !empty($map_notes) ? json_decode($map_notes, true) : array();

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
        
        $interests[] = array(
            'name' => $line,
            'size' => $size,
            'icon' => kunaal_get_interest_icon($line)
        );
    }
}

// Parse country lists
$visited_countries = array_filter(array_map('trim', explode(',', strtoupper($map_visited))));
$lived_countries = array_filter(array_map('trim', explode(',', strtoupper($map_lived))));
?>

<main class="about-page-v2">

    <!-- ========================================
         HERO SECTION (Always visible)
         ======================================== -->
    <section class="about-hero">
        <div class="about-hero-bg" <?php if ($about_photo) : ?>style="background-image: url('<?php echo esc_url($about_photo); ?>');"<?php endif; ?>></div>
        <div class="about-hero-overlay"></div>
        <div class="about-hero-content">
            <div class="about-avatar">
                <?php if ($about_photo) : ?>
                    <img src="<?php echo esc_url($about_photo); ?>" alt="<?php echo esc_attr($full_name); ?>">
                <?php else : ?>
                    <span class="initials"><?php echo esc_html(kunaal_get_initials()); ?></span>
                <?php endif; ?>
            </div>
            <h1 class="about-greeting"><?php echo esc_html($about_greeting); ?></h1>
            <?php if ($tagline) : ?>
            <p class="about-tagline"><?php echo esc_html($tagline); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ========================================
         BIO SECTION
         ======================================== -->
    <?php if ($show_bio) : ?>
    <section class="about-section about-bio-section">
        <div class="section-container">
            <h2 class="section-title reveal-up"><?php echo esc_html($bio_title); ?></h2>
            <div class="bio-content reveal-up">
                <?php 
                while (have_posts()) : the_post();
                    the_content();
                endwhile;
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         WORLD MAP SECTION
         ======================================== -->
    <?php if ($show_map && (!empty($visited_countries) || !empty($lived_countries))) : ?>
    <section class="about-section about-map-section">
        <div class="section-container wide">
            <h2 class="section-title reveal-up"><?php echo esc_html($map_title); ?></h2>
            <div class="map-wrapper reveal-up">
                <div class="world-map" 
                     data-visited="<?php echo esc_attr(implode(',', $visited_countries)); ?>"
                     data-lived="<?php echo esc_attr(implode(',', $lived_countries)); ?>"
                     data-notes="<?php echo esc_attr(json_encode($notes)); ?>">
                    <?php echo file_get_contents(get_template_directory() . '/assets/svg/world-map.svg'); ?>
                </div>
                <div class="map-tooltip"></div>
                <div class="map-legend">
                    <span class="legend-item"><span class="legend-dot visited"></span> Visited</span>
                    <span class="legend-item"><span class="legend-dot lived"></span> Lived</span>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         BOOKSHELF SECTION
         ======================================== -->
    <?php if ($show_books && !empty($books)) : ?>
    <section class="about-section about-books-section">
        <div class="section-container">
            <h2 class="section-title reveal-up"><?php echo esc_html($books_title); ?></h2>
            <div class="bookshelf reveal-up">
                <div class="shelf-books">
                    <?php foreach ($books as $index => $book) : ?>
                    <a href="<?php echo esc_url($book['link'] ?? '#'); ?>" 
                       class="book" 
                       target="_blank" 
                       rel="noopener"
                       style="--delay: <?php echo $index * 0.1; ?>s">
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
                <div class="shelf-wood"></div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         INTERESTS CLOUD SECTION
         ======================================== -->
    <?php if ($show_interests && !empty($interests)) : ?>
    <section class="about-section about-interests-section">
        <div class="section-container wide">
            <h2 class="section-title reveal-up"><?php echo esc_html($interests_title); ?></h2>
            <div class="interests-cloud reveal-up">
                <?php foreach ($interests as $index => $interest) : ?>
                <span class="interest-tag size-<?php echo esc_attr($interest['size']); ?>" 
                      style="--delay: <?php echo $index * 0.03; ?>s; --float-offset: <?php echo rand(-10, 10); ?>px;">
                    <span class="interest-icon"><?php echo $interest['icon']; ?></span>
                    <span class="interest-name"><?php echo esc_html($interest['name']); ?></span>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         STATS SECTION
         ======================================== -->
    <?php if ($show_stats && !empty($stats)) : ?>
    <section class="about-section about-stats-section">
        <div class="section-container">
            <h2 class="section-title reveal-up"><?php echo esc_html($stats_title); ?></h2>
            <div class="stats-grid reveal-up">
                <?php foreach ($stats as $index => $stat) : ?>
                <div class="stat-card" style="--delay: <?php echo $index * 0.1; ?>s">
                    <span class="stat-number" data-value="<?php echo esc_attr($stat['number'] ?? ''); ?>">
                        <?php echo esc_html($stat['number'] ?? ''); ?>
                    </span>
                    <span class="stat-label"><?php echo esc_html($stat['label'] ?? ''); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         INSPIRATIONS SECTION
         ======================================== -->
    <?php if ($show_inspirations && !empty($inspirations)) : ?>
    <section class="about-section about-inspirations-section">
        <div class="section-container">
            <h2 class="section-title reveal-up"><?php echo esc_html($inspirations_title); ?></h2>
            <div class="inspirations-grid reveal-up">
                <?php foreach ($inspirations as $index => $person) : ?>
                <a href="<?php echo esc_url($person['link'] ?? '#'); ?>" 
                   class="inspiration-card" 
                   target="_blank" 
                   rel="noopener"
                   style="--delay: <?php echo $index * 0.1; ?>s">
                    <?php if (!empty($person['photo'])) : ?>
                    <div class="inspiration-photo">
                        <img src="<?php echo esc_url($person['photo']); ?>" alt="<?php echo esc_attr($person['name'] ?? ''); ?>">
                    </div>
                    <?php else : ?>
                    <div class="inspiration-photo placeholder">
                        <span><?php echo esc_html(substr($person['name'] ?? 'X', 0, 1)); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="inspiration-info">
                        <span class="inspiration-name"><?php echo esc_html($person['name'] ?? ''); ?></span>
                        <span class="inspiration-role"><?php echo esc_html($person['role'] ?? ''); ?></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ========================================
         CONNECT SECTION
         ======================================== -->
    <?php if ($show_connect && ($email || $linkedin || $twitter)) : ?>
    <section class="about-section about-connect-section">
        <div class="section-container">
            <h2 class="section-title reveal-up"><?php echo esc_html($connect_title); ?></h2>
            <div class="connect-links reveal-up">
                <?php if ($email) : ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="connect-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                    <span>Email</span>
                </a>
                <?php endif; ?>
                <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="connect-link" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                    <span>LinkedIn</span>
                </a>
                <?php endif; ?>
                <?php if ($twitter) : ?>
                <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="connect-link" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                    <span>@<?php echo esc_html($twitter); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
