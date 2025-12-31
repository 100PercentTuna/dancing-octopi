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
    <ul class="grid">
      <?php while (have_posts()) : the_post(); ?>
        <?php kunaal_render_essay_card(get_the_ID()); ?>
      <?php endwhile; ?>
    </ul>
    
    <?php the_posts_navigation(); ?>
  <?php else : ?>
    <p class="no-posts">No posts found.</p>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
