<?php
/**
 * Index Template (Fallback)
 *
 * @package Kunaal_Theme
 */

get_header();

// If WordPress routes the site homepage through index.php (no front-page.php/home.php picked),
// force the home layout which includes its own <main id="main">
if (is_front_page() || is_home()) {
    get_template_part('template-parts/home');
    get_footer();
    return;
}
?>

<main id="main" class="container">
  <?php if (have_posts()) : ?>
    <ul class="grid">
      <?php while (have_posts()) : the_post(); ?>
        <?php kunaal_render_essay_card(get_the_ID()); ?>
      <?php endwhile; ?>
    </ul>
    <?php the_posts_navigation(); ?>
  <?php else : ?>
    <p class="no-posts"><?php esc_html_e('No content found.', 'kunaal-theme'); ?></p>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
