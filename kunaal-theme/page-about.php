<?php
/**
 * Template Name: About Page
 * 
 * Customizable About page with hero, bio, interests, and social links
 *
 * @package Kunaal_Theme
 */

get_header();

// Get Customizer values
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = $first_name . ' ' . $last_name;
$tagline = get_theme_mod('kunaal_author_tagline', 'A slightly alarming curiosity about humans and human collectives.');
$avatar = get_theme_mod('kunaal_avatar', '');
$email = get_theme_mod('kunaal_contact_email', '');

// About-specific settings
$about_headline = get_theme_mod('kunaal_about_headline', 'Hello, I\'m ' . $first_name);
$about_intro = get_theme_mod('kunaal_about_intro', 'I write essays exploring ideas at the intersection of technology, society, and human behavior.');
$about_bio = get_theme_mod('kunaal_about_bio', '');
$about_photo = get_theme_mod('kunaal_about_photo', $avatar);
$about_interests = get_theme_mod('kunaal_about_interests', 'Systems thinking, behavioral economics, data visualization, strategic analysis');

// Social links
$linkedin = get_theme_mod('kunaal_linkedin_handle', '');
$twitter = get_theme_mod('kunaal_twitter_handle', '');
?>

<main class="about-page">
    <!-- Hero Section -->
    <section class="about-hero reveal">
        <div class="about-hero-content">
            <?php if ($about_photo) : ?>
                <div class="about-photo">
                    <img src="<?php echo esc_url($about_photo); ?>" alt="<?php echo esc_attr($full_name); ?>">
                </div>
            <?php else : ?>
                <div class="about-photo about-initials">
                    <?php echo esc_html(kunaal_get_initials()); ?>
                </div>
            <?php endif; ?>
            
            <h1 class="about-headline"><?php echo esc_html($about_headline); ?></h1>
            <p class="about-tagline"><?php echo esc_html($tagline); ?></p>
        </div>
    </section>
    
    <!-- Introduction -->
    <section class="about-intro reveal">
        <p class="intro-text"><?php echo esc_html($about_intro); ?></p>
    </section>
    
    <!-- Bio Section -->
    <?php if ($about_bio || have_posts()) : ?>
    <section class="about-bio reveal">
        <?php if ($about_bio) : ?>
            <div class="bio-content">
                <?php echo wp_kses_post(wpautop($about_bio)); ?>
            </div>
        <?php endif; ?>
        
        <?php 
        // Also show page content if any
        while (have_posts()) : the_post();
            $content = get_the_content();
            if (!empty($content)) :
        ?>
            <div class="bio-content page-content">
                <?php the_content(); ?>
            </div>
        <?php 
            endif;
        endwhile; 
        ?>
    </section>
    <?php endif; ?>
    
    <!-- Interests -->
    <?php if ($about_interests) : 
        $interests_array = array_map('trim', explode(',', $about_interests));
    ?>
    <section class="about-interests reveal">
        <h2 class="section-label">Areas of Interest</h2>
        <div class="interests-grid">
            <?php foreach ($interests_array as $interest) : ?>
                <span class="interest-tag"><?php echo esc_html($interest); ?></span>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Connect Section -->
    <section class="about-connect reveal">
        <h2 class="section-label">Connect</h2>
        <div class="connect-links">
            <?php if ($email) : ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="connect-link email">
                    <span class="connect-icon">✉️</span>
                    <span class="connect-text">Email me</span>
                </a>
            <?php endif; ?>
            
            <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="connect-link linkedin" target="_blank" rel="noopener">
                    <span class="connect-icon">💼</span>
                    <span class="connect-text">LinkedIn</span>
                </a>
            <?php endif; ?>
            
            <?php if ($twitter) : ?>
                <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="connect-link twitter" target="_blank" rel="noopener">
                    <span class="connect-icon">𝕏</span>
                    <span class="connect-text">@<?php echo esc_html($twitter); ?></span>
                </a>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Recent Work -->
    <?php
    $recent_essays = new WP_Query(array(
        'post_type' => 'essay',
        'posts_per_page' => 3,
        'post_status' => 'publish'
    ));
    
    if ($recent_essays->have_posts()) :
    ?>
    <section class="about-recent reveal">
        <h2 class="section-label">Recent Essays</h2>
        <div class="recent-essays">
            <?php while ($recent_essays->have_posts()) : $recent_essays->the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="recent-essay-link">
                    <span class="essay-title"><?php the_title(); ?></span>
                    <span class="essay-arrow">→</span>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
        <a href="<?php echo get_post_type_archive_link('essay'); ?>" class="view-all-link">View all essays →</a>
    </section>
    <?php endif; ?>
</main>

<?php get_footer(); ?>

