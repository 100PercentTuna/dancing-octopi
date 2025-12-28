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
  <div class="toolbar">
    <div class="filterControls">
      <div class="filterPanel" id="filterPanel">
        <!-- Topics dropdown -->
        <?php if (!empty($all_topics)) : ?>
        <div class="topicSelect" id="topicsWrap" aria-label="Filter by topic">
          <button class="topicDropdownBtn" id="topicBtn" type="button" aria-haspopup="listbox" aria-expanded="false">
            <span class="topicSummary" id="topicSummary"><?php esc_html_e('all topics', 'kunaal-theme'); ?></span>
            <svg class="caret" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <ul class="topicDropdown" id="topicMenu" aria-multiselectable="true">
            <li class="topicOpt" data-tag="__ALL__" aria-selected="true">
              <input type="checkbox" checked tabindex="-1" />
              <span class="tName"><?php esc_html_e('all topics', 'kunaal-theme'); ?></span>
            </li>
            <li class="topicDivider" role="separator"></li>
            <?php foreach ($all_topics as $topic) : ?>
            <li class="topicOpt" data-tag="<?php echo esc_attr($topic['slug']); ?>" aria-selected="false">
              <input type="checkbox" tabindex="-1" />
              <span class="tName">#<?php echo esc_html($topic['name']); ?></span>
              <span class="tCount"><?php echo esc_html($topic['count']); ?></span>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
        
        <!-- Sort -->
        <select class="modernSelect" id="sortSelect" aria-label="Sort by">
          <option value="new"><?php esc_html_e('newest first', 'kunaal-theme'); ?></option>
          <option value="old"><?php esc_html_e('oldest first', 'kunaal-theme'); ?></option>
          <option value="title"><?php esc_html_e('alphabetical', 'kunaal-theme'); ?></option>
        </select>
        
        <!-- Search -->
        <div class="searchWrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
          </svg>
          <input type="search" class="searchInput" id="searchInput" placeholder="<?php esc_attr_e('search…', 'kunaal-theme'); ?>" autocomplete="off" />
        </div>
        
        <!-- Reset -->
        <button class="resetBtn" id="resetBtn" type="button"><?php esc_html_e('reset', 'kunaal-theme'); ?></button>
      </div>
      
      <!-- Filter toggle button -->
      <button class="filterToggle" id="filterBtn" type="button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M3 6h18M7 12h10M10 18h4"/>
        </svg>
        <span><?php esc_html_e('filter', 'kunaal-theme'); ?></span>
      </button>
    </div>
  </div>

  <!-- Essays Section -->
  <section class="section" id="essays" aria-label="<?php esc_attr_e('Essays', 'kunaal-theme'); ?>">
    <div class="sectionHead">
      <h2><?php esc_html_e('Essays', 'kunaal-theme'); ?></h2>
      <span class="sectionCount">
        <span id="essayCountShown"><?php echo esc_html($shown_essays); ?></span>
        <span id="essayLabel"><?php echo $shown_essays === 1 ? esc_html__('essay', 'kunaal-theme') : esc_html__('essays', 'kunaal-theme'); ?></span>
        <?php if ($total_essays > $home_posts_limit) : ?>
          &nbsp;&middot;&nbsp;
          <a href="<?php echo esc_url(get_post_type_archive_link('essay')); ?>" class="uBlue"><?php esc_html_e('more', 'kunaal-theme'); ?> &rarr;</a>
        <?php endif; ?>
      </span>
    </div>

    <?php if ($essays_query->have_posts()) : ?>
      <ul class="grid" id="essayGrid" data-post-type="essay">
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
    <div class="sectionHead">
      <h2><?php esc_html_e('Jottings', 'kunaal-theme'); ?></h2>
      <span class="sectionCount">
        <span id="jotCountShown"><?php echo esc_html($shown_jottings); ?></span>
        <span id="jotLabel"><?php echo $shown_jottings === 1 ? esc_html__('quick jotted-down rough idea', 'kunaal-theme') : esc_html__('quick jotted-down rough ideas', 'kunaal-theme'); ?></span>
        <?php if ($total_jottings > $home_posts_limit) : ?>
          &nbsp;&middot;&nbsp;
          <a href="<?php echo esc_url(get_post_type_archive_link('jotting')); ?>" class="uBlue"><?php esc_html_e('more', 'kunaal-theme'); ?> &rarr;</a>
        <?php endif; ?>
      </span>
    </div>

    <?php if ($jottings_query->have_posts()) : ?>
      <ul class="ledger" id="jotList" data-post-type="jotting">
        <?php while ($jottings_query->have_posts()) : $jottings_query->the_post(); ?>
          <?php kunaal_render_jotting_row(get_the_ID()); ?>
        <?php endwhile; ?>
      </ul>
    <?php elseif (!empty($total_jottings)) : ?>
      <?php
      $jotting_ids = kunaal_home_recent_ids('jotting', $home_posts_limit);
      ?>
      <?php if (!empty($jotting_ids)) : ?>
      <ul class="ledger" id="jotListFallback" data-post-type="jotting">
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
