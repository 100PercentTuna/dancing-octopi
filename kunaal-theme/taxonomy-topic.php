<?php
/**
 * Topic Archive Template
 *
 * @package Kunaal_Theme
 */

get_header();
$term = get_queried_object();
?>

<div class="container">
  <header class="archiveHeader">
    <h1 class="archiveTitle">#<?php echo esc_html($term->name); ?></h1>
    <?php if ($term->description) : ?>
      <p class="archiveDesc"><?php echo esc_html($term->description); ?></p>
    <?php else : ?>
      <p class="archiveDesc"><?php echo esc_html($term->count); ?> posts tagged with #<?php echo esc_html($term->name); ?></p>
    <?php endif; ?>
  </header>

  <?php if (have_posts()) : ?>
    <div class="grid" role="list">
      <?php while (have_posts()) : the_post(); ?>
        <?php
        $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
        $read_time = get_post_meta(get_the_ID(), 'kunaal_read_time', true);
        $topics = get_the_terms(get_the_ID(), 'topic');
        $card_image = function_exists('kunaal_get_card_image_url') ? kunaal_get_card_image_url(get_the_ID()) : get_the_post_thumbnail_url(get_the_ID(), 'essay-card');
        ?>
        <a href="<?php the_permalink(); ?>" class="card" role="listitem" data-parallax="true">
          <div class="media">
            <?php if ($card_image) : ?>
              <img src="<?php echo esc_url($card_image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
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
                  <span><?php echo esc_html($read_time); ?> min</span>
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
    <p class="no-posts">No posts found.</p>
  <?php endif; ?>

<?php get_footer(); ?>
