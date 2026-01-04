<?php
/**
 * Archive Template for Essays
 * Layout matches home page EXACTLY
 *
 * @package Kunaal_Theme
 * @version 3.4.1
 */

get_header();
$total_essays = wp_count_posts('essay')->publish;
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
  <section class="section" id="essays" aria-label="Essays">
    <?php
    // Get label from Customizer (with fallback)
    $essay_label = $total_essays == 1
      ? kunaal_mod('kunaal_essay_label_singular', 'long one')
      : kunaal_mod('kunaal_essay_label_plural', 'long ones');
    
    get_template_part('template-parts/components/section-head', null, array(
      'title' => 'Essays',
      'count' => $total_essays,
      'count_label' => $essay_label,
      'count_id' => 'essayCountShown',
      'label_id' => 'essayLabel',
    ));
    ?>

    <?php if (have_posts()) : ?>
      <ul class="grid" id="essayGrid" data-ui="essay-grid" data-post-type="essay">
        <?php while (have_posts()) : the_post(); ?>
          <?php kunaal_render_essay_card(get_the_ID()); ?>
        <?php endwhile; ?>
      </ul>
      
      <div class="infiniteLoader hidden" id="infiniteLoader">
        <div class="spinner"></div>
      </div>
    <?php else : ?>
      <p class="no-posts">No essays yet.</p>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>
