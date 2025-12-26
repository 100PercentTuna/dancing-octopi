<?php
/**
 * Page Template - For About, Contact, and other pages
 * These pages are fully editable via the WordPress block editor
 *
 * @package Kunaal_Theme
 */

get_header();
?>

<main id="main" class="article container">
  <?php while (have_posts()) : the_post(); ?>
    <article class="pageContent">
      <header class="articleHeader reveal">
        <h1 class="articleTitle"><?php the_title(); ?></h1>
      </header>

      <div class="prose reveal">
        <?php the_content(); ?>
        
        <?php 
        // If this is the Contact page, show contact info from Customizer
        if (is_page('contact')) :
          $email = kunaal_mod('kunaal_contact_email', '');
          if ($email) :
        ?>
          <div class="contactInfo">
            <a href="mailto:<?php echo esc_attr($email); ?>" class="uBlue">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <rect x="2" y="4" width="20" height="16" rx="2"/>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
              </svg>
              <span><?php echo esc_html($email); ?></span>
            </a>
          </div>
        <?php 
          endif;
        endif; 
        ?>
      </div>
    </article>
  <?php endwhile; ?>

<?php get_footer(); ?>
