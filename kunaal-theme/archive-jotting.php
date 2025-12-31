<?php
/**
 * Archive Template for Jottings
 * Layout matches home page EXACTLY
 *
 * @package Kunaal_Theme
 * @version 3.4.1
 */

get_header();
$total_jottings = wp_count_posts('jotting')->publish;
$all_topics = kunaal_get_all_topics();
?>

<main id="main" class="container">
  
  <!-- Toolbar: Filter controls ABOVE section header -->
  <?php
  get_template_part('template-parts/components/filter-bar', null, array(
    'topics' => $all_topics,
    'show_search' => true,
    'show_reset' => true,
  ));
  ?>

  <!-- Section header - EXACTLY like home page with blue underline and count -->
  <section class="section" id="jottings" aria-label="Jottings">
    <?php
    get_template_part('template-parts/components/section-head', null, array(
      'title' => 'Jottings',
      'count' => $total_jottings,
      'count_label' => $total_jottings == 1 ? 'quick jotted-down rough idea' : 'quick jotted-down rough ideas',
      'count_id' => 'jotCountShown',
      'label_id' => 'jotLabel',
    ));
    ?>

    <?php if (have_posts()) : ?>
      <ul class="ledger" id="jotList" data-ui="jot-list" data-post-type="jotting">
        <?php while (have_posts()) : the_post(); ?>
          <?php kunaal_render_jotting_row(get_the_ID()); ?>
        <?php endwhile; ?>
      </ul>
      
      <div class="infiniteLoader hidden" id="infiniteLoader">
        <div class="spinner"></div>
      </div>
    <?php else : ?>
      <p class="no-posts">No jottings yet.</p>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>
