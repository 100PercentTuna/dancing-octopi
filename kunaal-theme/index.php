<?php
/**
 * Index Template (Fallback)
 *
 * @package Kunaal_Theme
 */

get_header();
?>

<div class="container">
  <?php
  // If WordPress routes the site homepage through index.php (no front-page.php/home.php picked),
  // force the home layout.
  if (is_front_page() || is_home()) {
    get_template_part('template-parts/home');
    get_footer();
    return;
  }
  ?>
  <?php if (have_posts()) : ?>
    <ul class="grid">
      <?php while (have_posts()) : the_post(); ?>
        <?php
        $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
        $read_time = get_post_meta(get_the_ID(), 'kunaal_read_time', true);
        $card_image = function_exists('kunaal_get_card_image_url') ? kunaal_get_card_image_url(get_the_ID()) : get_the_post_thumbnail_url(get_the_ID(), 'essay-card');
        ?>
        <li><a href="<?php the_permalink(); ?>" class="card" data-parallax="true">
          <div class="media">
            <?php if ($card_image) : ?>
              <img src="<?php echo esc_url($card_image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
            <?php elseif (has_post_thumbnail()) : ?>
              <?php the_post_thumbnail('essay-card'); ?>
            <?php else : ?>
              <svg viewBox="0 0 400 500" fill="none"><rect width="400" height="500" fill="#F5F3EF"/></svg>
            <?php endif; ?>
            <div class="scrim"></div>
          </div>
          <div class="overlay">
            <h3 class="tTitle"><?php the_title(); ?></h3>
            <div class="details">
              <p class="meta">
                <span><?php echo esc_html(get_the_date('j F Y')); ?></span>
                <?php if ($read_time) : ?>
                  <span class="dot"></span>
                  <span><?php echo esc_html($read_time); ?> <?php esc_html_e('min', 'kunaal-theme'); ?></span>
                <?php endif; ?>
              </p>
              <?php if ($subtitle) : ?>
                <p class="dek"><?php echo esc_html($subtitle); ?></p>
              <?php endif; ?>
            </div>
          </div>
        </a>
      <?php endwhile; ?>
    </div>
    <?php the_posts_navigation(); ?>
  <?php else : ?>
    <p class="no-posts"><?php esc_html_e('No content found.', 'kunaal-theme'); ?></p>
  <?php endif; ?>

<?php get_footer(); ?>
