<?php
/**
 * Single Post Template (Fallback)
 *
 * @package Kunaal_Theme
 */

get_header();
?>

<main id="main" class="article container">
  <?php while (have_posts()) : the_post(); ?>
    <article>
      <header class="articleHeader reveal">
        <div class="articleMeta">
          <span><?php echo esc_html(get_the_date('j F Y')); ?></span>
        </div>
        <h1 class="articleTitle"><?php the_title(); ?></h1>
      </header>

      <?php if (has_post_thumbnail()) : ?>
        <figure class="heroImage reveal">
          <?php the_post_thumbnail('essay-hero'); ?>
        </figure>
      <?php endif; ?>

      <div class="articleContent">
        <div class="prose">
          <?php the_content(); ?>
        </div>
        <aside class="rail"></aside>
      </div>
    </article>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
