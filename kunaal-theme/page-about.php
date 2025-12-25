<?php
/**
 * Template Name: About Page
 * A stunning, immersive about page with blend-to-background photo
 * 
 * @package Kunaal_Theme
 */

get_header();

// Get customizer values
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = $first_name . ' ' . $last_name;
$tagline = get_theme_mod('kunaal_author_tagline', 'A slightly alarming curiosity about humans and human collectives.');

// About page specific settings
$about_photo = get_theme_mod('kunaal_about_photo', '');
$about_headline = get_theme_mod('kunaal_about_headline', 'Hello, I\'m ' . $first_name);
$about_intro = get_theme_mod('kunaal_about_intro', 'I write to understand the world and my place in it.');
$about_bio_1 = get_theme_mod('kunaal_about_bio_1', '');
$about_bio_2 = get_theme_mod('kunaal_about_bio_2', '');
$about_interests = get_theme_mod('kunaal_about_interests', '');
$about_currently = get_theme_mod('kunaal_about_currently', '');

// Socials
$linkedin_url = get_theme_mod('kunaal_linkedin_handle', '');
$twitter_handle = get_theme_mod('kunaal_twitter_handle', '');
$email = get_theme_mod('kunaal_email', '');
?>

<main class="about-page">
  
  <!-- Hero Section with Blending Photo -->
  <section class="about-hero">
    <div class="about-hero-bg"></div>
    <?php if ($about_photo) : ?>
    <div class="about-photo-wrapper">
      <img src="<?php echo esc_url($about_photo); ?>" alt="<?php echo esc_attr($full_name); ?>" class="about-photo" />
      <div class="about-photo-gradient"></div>
    </div>
    <?php endif; ?>
    <div class="about-hero-content">
      <p class="about-greeting reveal motion-fade-up stagger-1"><?php echo esc_html($about_headline); ?></p>
      <h1 class="about-name reveal motion-fade-up stagger-2"><?php echo esc_html($full_name); ?></h1>
      <p class="about-tagline reveal motion-fade-up stagger-3"><?php echo esc_html($tagline); ?></p>
    </div>
    <div class="about-scroll-hint reveal motion-fade-in stagger-5" aria-hidden="true">
      <span>Scroll</span>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 5v14M5 12l7 7 7-7"/>
      </svg>
    </div>
  </section>
  
  <!-- Intro Section -->
  <section class="about-intro container">
    <div class="about-intro-inner">
      <p class="about-intro-text reveal motion-fade-up"><?php echo wp_kses_post($about_intro); ?></p>
    </div>
  </section>
  
  <!-- Bio Section -->
  <?php if ($about_bio_1 || $about_bio_2) : ?>
  <section class="about-bio container">
    <div class="about-bio-inner">
      <?php if ($about_bio_1) : ?>
      <div class="about-bio-block reveal motion-fade-up stagger-1">
        <div class="about-bio-accent" aria-hidden="true">01</div>
        <div class="about-bio-content">
          <?php echo wp_kses_post(wpautop($about_bio_1)); ?>
        </div>
      </div>
      <?php endif; ?>
      
      <?php if ($about_bio_2) : ?>
      <div class="about-bio-block reveal motion-fade-up stagger-2">
        <div class="about-bio-accent" aria-hidden="true">02</div>
        <div class="about-bio-content">
          <?php echo wp_kses_post(wpautop($about_bio_2)); ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>
  <?php endif; ?>
  
  <!-- Interests / Currently Section -->
  <?php if ($about_interests || $about_currently) : ?>
  <section class="about-details">
    <div class="container">
      <div class="about-details-grid">
        <?php if ($about_interests) : ?>
        <div class="about-detail-card reveal motion-scale-in stagger-1">
          <h3 class="about-detail-title">I'm interested in</h3>
          <div class="about-detail-content">
            <?php echo wp_kses_post($about_interests); ?>
          </div>
        </div>
        <?php endif; ?>
        
        <?php if ($about_currently) : ?>
        <div class="about-detail-card reveal motion-scale-in stagger-2">
          <h3 class="about-detail-title">Currently</h3>
          <div class="about-detail-content">
            <?php echo wp_kses_post($about_currently); ?>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  
  <!-- Page Content (from WordPress editor) -->
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php if (trim(get_the_content())) : ?>
    <section class="about-custom-content container">
      <div class="prose">
        <?php the_content(); ?>
      </div>
    </section>
    <?php endif; ?>
  <?php endwhile; endif; ?>
  
  <!-- Connect Section -->
  <section class="about-connect">
    <div class="container">
      <div class="about-connect-inner reveal motion-fade-up">
        <h2 class="about-connect-title">Let's Connect</h2>
        <p class="about-connect-text">I'd love to hear from you—whether it's a thought on something I wrote, an idea worth exploring, or just to say hello.</p>
        
        <div class="about-connect-actions">
          <?php if ($email) : ?>
          <a href="mailto:<?php echo esc_attr($email); ?>" class="about-connect-btn primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="2" y="4" width="20" height="16" rx="2"/>
              <path d="M22 7l-10 6L2 7"/>
            </svg>
            Say Hello
          </a>
          <?php endif; ?>
          
          <?php 
          $contact_page = get_page_by_path('contact');
          if ($contact_page) : ?>
          <a href="<?php echo esc_url(get_permalink($contact_page)); ?>" class="about-connect-btn secondary">
            Leave a Message
          </a>
          <?php endif; ?>
        </div>
        
        <div class="about-social-links">
          <?php if ($linkedin_url) : ?>
          <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
            </svg>
          </a>
          <?php endif; ?>
          
          <?php if ($twitter_handle) : ?>
          <a href="https://twitter.com/<?php echo esc_attr($twitter_handle); ?>" target="_blank" rel="noopener" aria-label="X / Twitter">
            <svg viewBox="0 0 24 24" fill="currentColor">
              <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
            </svg>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  
</main>

<?php get_footer(); ?>

