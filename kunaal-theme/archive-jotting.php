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
  <div class="toolbar">
    <div class="filterControls">
      <div class="filterPanel" id="filterPanel">
        <?php if (!empty($all_topics)) : ?>
        <div class="topicSelect" id="topicsWrap" aria-label="Filter by topic">
          <button class="topicDropdownBtn" id="topicBtn" type="button" aria-haspopup="listbox" aria-expanded="false">
            <span class="topicSummary" id="topicSummary">all topics</span>
            <svg class="caret" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <div class="topicDropdown" id="topicMenu" role="listbox" aria-multiselectable="true">
            <div class="topicOpt" data-tag="__ALL__" role="option" aria-selected="true">
              <input type="checkbox" checked tabindex="-1" />
              <span class="tName">all topics</span>
            </div>
            <div class="topicDivider"></div>
            <?php foreach ($all_topics as $topic) : ?>
            <div class="topicOpt" data-tag="<?php echo esc_attr($topic['slug']); ?>" role="option" aria-selected="false">
              <input type="checkbox" tabindex="-1" />
              <span class="tName">#<?php echo esc_html($topic['name']); ?></span>
              <span class="tCount"><?php echo esc_html($topic['count']); ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
        <select class="modernSelect" id="sortSelect" aria-label="Sort by">
          <option value="new">newest first</option>
          <option value="old">oldest first</option>
          <option value="title">alphabetical</option>
        </select>
        <div class="searchWrap">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
          </svg>
          <input type="search" class="searchInput" id="searchInput" placeholder="search…" autocomplete="off" />
        </div>
        <button class="resetBtn" id="resetBtn" type="button">reset</button>
      </div>
      <button class="filterToggle" id="filterBtn" type="button">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M3 6h18M7 12h10M10 18h4"/>
        </svg>
        <span>filter</span>
      </button>
    </div>
  </div>

  <!-- Section header - EXACTLY like home page with blue underline and count -->
  <section class="section" id="jottings" aria-label="Jottings">
    <div class="sectionHead">
      <h2>Jottings</h2>
      <span class="sectionCount">
        <span id="jotCountShown"><?php echo esc_html($total_jottings); ?></span>
        <span id="jotLabel"><?php echo $total_jottings == 1 ? 'quick jotted-down rough idea' : 'quick jotted-down rough ideas'; ?></span>
      </span>
    </div>

    <?php if (have_posts()) : ?>
      <div class="ledger" id="jotList" role="list" data-post-type="jotting">
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
          <a href="<?php the_permalink(); ?>" class="jRow" role="listitem"
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
          </a>
        <?php endwhile; ?>
      </div>
      
      <div class="infiniteLoader hidden" id="infiniteLoader">
        <div class="spinner"></div>
      </div>
    <?php else : ?>
      <p class="no-posts">No jottings yet.</p>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>
