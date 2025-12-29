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

<main class="container" id="top">
  
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
          <?php
          $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
          $topics = get_the_terms(get_the_ID(), 'topic');
          $topic_slugs = array();
          if ($topics && !is_wp_error($topics)) {
              foreach ($topics as $t) {
                  $topic_slugs[] = $t->slug;
              }
          }
          ?>
          <li><a href="<?php the_permalink(); ?>" class="jRow"
             data-title="<?php echo esc_attr(get_the_title()); ?>"
             data-text="<?php echo esc_attr($subtitle); ?>"
             data-date="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"
             data-tags="<?php echo esc_attr(implode(',', $topic_slugs)); ?>">
            <span class="jDate"><?php echo esc_html(get_the_date('j M Y')); ?></span>
            <div class="jContent">
              <h3 class="jTitle"><?php the_title(); ?></h3>
              <?php if ($subtitle) : ?>
                <p class="jText"><?php echo esc_html($subtitle); ?></p>
              <?php endif; ?>
              <?php if ($topics && !is_wp_error($topics)) : ?>
                <div class="jTags">
                  <?php foreach ($topics as $topic) : ?>
                    <span class="tag">#<?php echo esc_html($topic->name); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </a></li>
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
