<?php
/**
 * Home Page Content (shared by front-page.php and page.php fallback)
 *
 * @package Kunaal_Theme
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get all topics for filter
$all_topics = function_exists('kunaal_get_all_topics') ? kunaal_get_all_topics() : array();

$home_posts_limit = defined('KUNAAL_HOME_POSTS_LIMIT') ? KUNAAL_HOME_POSTS_LIMIT : 6;
$essays_query = kunaal_home_query('essay', $home_posts_limit);
$jottings_query = kunaal_home_query('jotting', $home_posts_limit);

$total_essays = wp_count_posts('essay')->publish;
$total_jottings = wp_count_posts('jotting')->publish;
$shown_essays = $essays_query->post_count;
$shown_jottings = $jottings_query->post_count;
?>

<main class="container" id="main">
  
  <!-- Toolbar: Filter controls -->
  <?php
  get_template_part('template-parts/components/filter-bar', null, array(
    'topics' => $all_topics,
    'show_search' => true,
    'show_reset' => true,
  ));
  ?>

  <!-- Essays Section -->
  <section class="section" id="essays" aria-label="<?php esc_attr_e('Essays', 'kunaal-theme'); ?>">
    <?php
    // Get label from Customizer (with fallback)
    $essay_label = $shown_essays === 1
      ? kunaal_mod('kunaal_essay_label_singular', 'long one')
      : kunaal_mod('kunaal_essay_label_plural', 'long ones');
    
    get_template_part('template-parts/components/section-head', null, array(
      'title' => __('Essays', 'kunaal-theme'),
      'count' => $shown_essays,
      'count_label' => $essay_label,
      'count_id' => 'essayCountShown',
      'label_id' => 'essayLabel',
      'more_link' => $total_essays > $home_posts_limit ? get_post_type_archive_link('essay') : '',
      'more_text' => __('more', 'kunaal-theme'),
    ));
    ?>

    <?php if ($essays_query->have_posts()) : ?>
      <ul class="grid" id="essayGrid" data-ui="essay-grid" data-post-type="essay">
        <?php while ($essays_query->have_posts()) : $essays_query->the_post(); ?>
          <?php kunaal_render_essay_card(get_the_ID()); ?>
        <?php endwhile; ?>
      </ul>
    <?php elseif (!empty($total_essays)) : ?>
      <?php
      $essay_ids = kunaal_home_recent_ids('essay', 6);
      ?>
      <?php if (!empty($essay_ids)) : ?>
      <ul class="grid" id="essayGridFallback" data-post-type="essay">
        <?php foreach ($essay_ids as $post_id) : ?>
          <?php kunaal_render_essay_card($post_id); ?>
        <?php endforeach; ?>
      </ul>
      <?php wp_reset_postdata(); ?>
      <?php else : ?>
      <p class="no-posts"><?php esc_html_e('No essays yet.', 'kunaal-theme'); ?></p>
      <?php endif; ?>
    <?php else : ?>
      <p class="no-posts"><?php esc_html_e('No essays yet.', 'kunaal-theme'); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </section>

  <!-- Jottings Section -->
  <section class="section" id="jottings" aria-label="<?php esc_attr_e('Jottings', 'kunaal-theme'); ?>">
    <?php
    // Get label from Customizer (with fallback)
    $jotting_label = $shown_jottings === 1
      ? kunaal_mod('kunaal_jotting_label_singular', 'short one')
      : kunaal_mod('kunaal_jotting_label_plural', 'short ones');
    
    get_template_part('template-parts/components/section-head', null, array(
      'title' => __('Jottings', 'kunaal-theme'),
      'count' => $shown_jottings,
      'count_label' => $jotting_label,
      'count_id' => 'jotCountShown',
      'label_id' => 'jotLabel',
      'more_link' => $total_jottings > $home_posts_limit ? get_post_type_archive_link('jotting') : '',
      'more_text' => __('more', 'kunaal-theme'),
    ));
    ?>

    <?php if ($jottings_query->have_posts()) : ?>
      <ul class="ledger" id="jotList" data-ui="jot-list" data-post-type="jotting">
        <?php while ($jottings_query->have_posts()) : $jottings_query->the_post(); ?>
          <?php kunaal_render_jotting_row(get_the_ID()); ?>
        <?php endwhile; ?>
      </ul>
    <?php elseif (!empty($total_jottings)) : ?>
      <?php
      $jotting_ids = kunaal_home_recent_ids('jotting', $home_posts_limit);
      ?>
      <?php if (!empty($jotting_ids)) : ?>
      <ul class="ledger" id="jotListFallback" data-ui="jot-list" data-post-type="jotting">
        <?php foreach ($jotting_ids as $post_id) : ?>
          <?php kunaal_render_jotting_row($post_id); ?>
        <?php endforeach; ?>
      </ul>
      <?php wp_reset_postdata(); ?>
      <?php else : ?>
      <p class="no-posts"><?php esc_html_e('No jottings yet.', 'kunaal-theme'); ?></p>
      <?php endif; ?>
    <?php else : ?>
      <p class="no-posts"><?php esc_html_e('No jottings yet.', 'kunaal-theme'); ?></p>
    <?php endif; ?>
    <?php wp_reset_postdata(); ?>
  </section>
</main>
