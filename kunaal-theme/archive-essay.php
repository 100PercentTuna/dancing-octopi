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
  <section class="section" id="essays" aria-label="Essays">
    <div class="sectionHead">
      <h2>Essays</h2>
      <span class="sectionCount">
        <span id="essayCountShown"><?php echo esc_html($total_essays); ?></span>
        <span id="essayLabel"><?php echo $total_essays == 1 ? 'essay' : 'essays'; ?></span>
      </span>
    </div>

    <?php if (have_posts()) : ?>
      <div class="grid" id="essayGrid" role="list" data-post-type="essay">
        <?php while (have_posts()) : the_post(); ?>
          <?php
          $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
          $read_time = get_post_meta(get_the_ID(), 'kunaal_read_time', true);
          $topics = get_the_terms(get_the_ID(), 'topic');
          $card_image = kunaal_get_card_image_url(get_the_ID());
          $topic_slugs = array();
          if ($topics && !is_wp_error($topics)) {
              foreach ($topics as $t) {
                  $topic_slugs[] = $t->slug;
              }
          }
          ?>
          <a href="<?php the_permalink(); ?>" class="card" role="listitem"
             data-title="<?php echo esc_attr(get_the_title()); ?>"
             data-dek="<?php echo esc_attr($subtitle); ?>"
             data-date="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"
             data-tags="<?php echo esc_attr(implode(',', $topic_slugs)); ?>">
            <div class="media" data-parallax="true">
              <?php if ($card_image) : ?>
                <img src="<?php echo esc_url($card_image); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy" />
              <?php else : ?>
                <svg viewBox="0 0 400 500" fill="none"><rect width="400" height="500" fill="url(#g<?php echo get_the_ID(); ?>)"/><defs><linearGradient id="g<?php echo get_the_ID(); ?>" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:rgba(30,90,255,0.08)"/><stop offset="100%" style="stop-color:rgba(11,18,32,0.02)"/></linearGradient></defs></svg>
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
                <?php if ($topics && !is_wp_error($topics)) : ?>
                <p class="metaTags">
                  <?php foreach (array_slice($topics, 0, 2) as $index => $topic) : ?>
                    <?php if ($index > 0) : ?><span class="dot"></span><?php endif; ?>
                    <span class="tag">#<?php echo esc_html($topic->name); ?></span>
                  <?php endforeach; ?>
                </p>
                <?php endif; ?>
                <?php if ($subtitle) : ?>
                  <p class="dek"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endwhile; ?>
      </div>
      
      <div class="infiniteLoader hidden" id="infiniteLoader">
        <div class="spinner"></div>
      </div>
    <?php else : ?>
      <p class="no-posts">No essays yet.</p>
    <?php endif; ?>
  </section>
</main>

<?php get_footer(); ?>
