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

/**
 * Some managed hosts/plugins hook query filters differently on the front page.
 * We do a normal query first, and if it returns empty, we retry with
 * suppress_filters to bypass third-party query mutations.
 */
if (!function_exists('kunaal_home_query')) {
    function kunaal_home_query($post_type, $limit = 6) {
        $base = array(
            'post_type' => $post_type,
            'posts_per_page' => (int) $limit,
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        );

        $q = new WP_Query($base);
        if ($q->have_posts()) {
            return $q;
        }

        $base['suppress_filters'] = true;
        return new WP_Query($base);
    }
}

$essays_query = kunaal_home_query('essay', 6);
$jottings_query = kunaal_home_query('jotting', 6);

$total_essays = wp_count_posts('essay')->publish;
$total_jottings = wp_count_posts('jotting')->publish;
$shown_essays = $essays_query->post_count;
$shown_jottings = $jottings_query->post_count;

/**
 * Last-resort fallback: bypass WP_Query (and any pre_get_posts interference) by
 * selecting IDs directly from the posts table.
 */
function kunaal_home_recent_ids($post_type, $limit = 6) {
    global $wpdb;
    $limit = max(1, (int) $limit);
    $sql = $wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = 'publish' ORDER BY post_date DESC LIMIT %d",
        $post_type,
        $limit
    );
    $ids = $wpdb->get_col($sql);
    return array_map('intval', is_array($ids) ? $ids : array());
}

$kunaal_home_used_fallback_essays = false;
$kunaal_home_used_fallback_jottings = false;
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
          <div class="topicDropdown" id="topicMenu" role="listbox" aria-multiselectable="true">
            <div class="topicOpt" data-tag="__ALL__" role="option" aria-selected="true">
              <input type="checkbox" checked tabindex="-1" />
              <span class="tName"><?php esc_html_e('all topics', 'kunaal-theme'); ?></span>
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
        <?php if ($total_essays > 6) : ?>
          &nbsp;&middot;&nbsp;
          <a href="<?php echo esc_url(get_post_type_archive_link('essay')); ?>" class="uBlue"><?php esc_html_e('more', 'kunaal-theme'); ?> &rarr;</a>
        <?php endif; ?>
      </span>
    </div>

    <?php if ($essays_query->have_posts()) : ?>
      <div class="grid" id="essayGrid" role="list" data-post-type="essay">
        <?php while ($essays_query->have_posts()) : $essays_query->the_post(); ?>
          <?php
          $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
          $read_time = get_post_meta(get_the_ID(), 'kunaal_read_time', true);
          $topics = get_the_terms(get_the_ID(), 'topic');
          $card_image = '';
          if (function_exists('kunaal_get_card_image_url')) {
              $card_image = @kunaal_get_card_image_url(get_the_ID());
          }
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
                <svg viewBox="0 0 400 500" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <rect width="400" height="500" fill="url(#grad<?php echo get_the_ID(); ?>)"/>
                  <defs>
                    <linearGradient id="grad<?php echo get_the_ID(); ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" style="stop-color:rgba(30,90,255,0.08)"/>
                      <stop offset="100%" style="stop-color:rgba(11,18,32,0.02)"/>
                    </linearGradient>
                  </defs>
                </svg>
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
    <?php elseif (!empty($total_essays)) : ?>
      <?php
      $kunaal_home_used_fallback_essays = true;
      $GLOBALS['kunaal_home_used_fallback_essays'] = true;
      $essay_ids = kunaal_home_recent_ids('essay', 6);
      ?>
      <?php if (!empty($essay_ids)) : ?>
      <div class="grid" id="essayGrid" role="list" data-post-type="essay">
        <?php foreach ($essay_ids as $post_id) : ?>
          <?php
          $post_obj = get_post($post_id);
          if (!$post_obj) continue;
          setup_postdata($post_obj);

          $subtitle = get_post_meta($post_id, 'kunaal_subtitle', true);
          $read_time = get_post_meta($post_id, 'kunaal_read_time', true);
          $topics = get_the_terms($post_id, 'topic');
          $card_image = function_exists('kunaal_get_card_image_url') ? kunaal_get_card_image_url($post_id) : '';
          $topic_slugs = array();
          if ($topics && !is_wp_error($topics)) {
              foreach ($topics as $t) {
                  $topic_slugs[] = $t->slug;
              }
          }
          ?>
          <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="card" role="listitem"
             data-title="<?php echo esc_attr(get_the_title($post_id)); ?>"
             data-dek="<?php echo esc_attr($subtitle); ?>"
             data-date="<?php echo esc_attr(get_the_date('Y-m-d', $post_id)); ?>"
             data-tags="<?php echo esc_attr(implode(',', $topic_slugs)); ?>">
            <div class="media" data-parallax="true">
              <?php if ($card_image) : ?>
                <img src="<?php echo esc_url($card_image); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>" loading="lazy" />
              <?php else : ?>
                <svg viewBox="0 0 400 500" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <rect width="400" height="500" fill="url(#grad<?php echo (int) $post_id; ?>)"/>
                  <defs>
                    <linearGradient id="grad<?php echo (int) $post_id; ?>" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" style="stop-color:rgba(30,90,255,0.08)"/>
                      <stop offset="100%" style="stop-color:rgba(11,18,32,0.02)"/>
                    </linearGradient>
                  </defs>
                </svg>
              <?php endif; ?>
              <div class="scrim"></div>
            </div>
            <div class="overlay">
              <h3 class="tTitle"><?php echo esc_html(get_the_title($post_id)); ?></h3>
              <div class="details">
                <p class="meta">
                  <span><?php echo esc_html(get_the_date('j F Y', $post_id)); ?></span>
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
        <?php endforeach; ?>
      </div>
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
        <?php if ($total_jottings > 6) : ?>
          &nbsp;&middot;&nbsp;
          <a href="<?php echo esc_url(get_post_type_archive_link('jotting')); ?>" class="uBlue"><?php esc_html_e('more', 'kunaal-theme'); ?> &rarr;</a>
        <?php endif; ?>
      </span>
    </div>

    <?php if ($jottings_query->have_posts()) : ?>
      <div class="ledger" id="jotList" role="list" data-post-type="jotting">
        <?php while ($jottings_query->have_posts()) : $jottings_query->the_post(); ?>
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
    <?php elseif (!empty($total_jottings)) : ?>
      <?php
      $kunaal_home_used_fallback_jottings = true;
      $GLOBALS['kunaal_home_used_fallback_jottings'] = true;
      $jotting_ids = kunaal_home_recent_ids('jotting', 6);
      ?>
      <?php if (!empty($jotting_ids)) : ?>
      <div class="ledger" id="jotList" role="list" data-post-type="jotting">
        <?php foreach ($jotting_ids as $post_id) : ?>
          <?php
          $post_obj = get_post($post_id);
          if (!$post_obj) continue;
          setup_postdata($post_obj);

          $subtitle = get_post_meta($post_id, 'kunaal_subtitle', true);
          $topics = get_the_terms($post_id, 'topic');
          $topic_slugs = array();
          if ($topics && !is_wp_error($topics)) {
              foreach ($topics as $t) {
                  $topic_slugs[] = $t->slug;
              }
          }
          ?>
          <a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="jRow" role="listitem"
             data-title="<?php echo esc_attr(get_the_title($post_id)); ?>"
             data-text="<?php echo esc_attr($subtitle); ?>"
             data-date="<?php echo esc_attr(get_the_date('Y-m-d', $post_id)); ?>"
             data-tags="<?php echo esc_attr(implode(',', $topic_slugs)); ?>">
            <span class="jDate"><?php echo esc_html(get_the_date('j M Y', $post_id)); ?></span>
            <div class="jContent">
              <h3 class="jTitle"><?php echo esc_html(get_the_title($post_id)); ?></h3>
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
        <?php endforeach; ?>
      </div>
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

<?php
// Mark successful completion for debugging.
$GLOBALS['kunaal_home_layout_loaded'] = true;
?>
