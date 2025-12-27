<?php
/**
 * Helper Functions
 * 
 * @package Kunaal_Theme
 * @version 4.20.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Helper: Get initials
 * 
 * @return string Uppercase initials from author first and last name
 */
if (!function_exists('kunaal_get_initials')) {
    function kunaal_get_initials() {
        $first = kunaal_mod('kunaal_author_first_name', 'Kunaal');
        $last = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
        return strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
    }
}

/**
 * Helper: Output Subscribe Section
 * 
 * @return void
 */
if (!function_exists('kunaal_subscribe_section')) {
    function kunaal_subscribe_section() {
    if (!kunaal_mod('kunaal_subscribe_enabled', false)) {
        return;
    }
    
    // Check location setting - only show bottom if 'bottom' or 'both'
    $sub_location = kunaal_mod('kunaal_subscribe_location', 'both');
    if (!in_array($sub_location, array('bottom', 'both'))) {
        return;
    }
    
    $heading = kunaal_mod('kunaal_subscribe_heading', 'Stay updated');
    $description = kunaal_mod('kunaal_subscribe_description', 'Get notified when new essays and jottings are published.');
    $form_action = kunaal_mod('kunaal_subscribe_form_action', '');
    $mode = kunaal_mod('kunaal_subscribe_mode', 'builtin');
    
    ?>
    <section class="subscribe-section reveal">
        <h3><?php echo esc_html($heading); ?></h3>
        <p><?php echo esc_html($description); ?></p>
        <form class="subscribe-form" data-subscribe-form="bottom" data-subscribe-mode="<?php echo esc_attr($mode); ?>" action="<?php echo $mode === 'external' ? esc_url($form_action) : ''; ?>" method="post" novalidate>
            <input type="email" name="email" placeholder="<?php echo esc_attr__('Your email address', 'kunaal-theme'); ?>" required />
            <button type="submit"><?php echo esc_html__('Subscribe', 'kunaal-theme'); ?></button>
        </form>
        <div class="subscribe-status" aria-live="polite"></div>
    </section>
    <?php
    }
}

/**
 * Helper: Get all topics with counts
 * 
 * @return array Array of topic data with slug, name, and count
 */
if (!function_exists('kunaal_get_all_topics')) {
    function kunaal_get_all_topics() {
    $topics = get_terms(array(
        'taxonomy' => 'topic',
        'hide_empty' => false,
    ));

    if (is_wp_error($topics) || empty($topics)) {
        return array();
    }

    $result = array();
    foreach ($topics as $topic) {
        $result[] = array(
            'slug' => $topic->slug,
            'name' => $topic->name,
            'count' => $topic->count,
        );
    }
    return $result;
    }
}

/**
 * Helper: Get card image URL
 * 
 * @param int    $post_id Post ID
 * @param string $size    Image size
 * @return string Image URL or empty string
 */
if (!function_exists('kunaal_get_card_image_url')) {
    function kunaal_get_card_image_url($post_id, $size = 'essay-card') {
    $card_image = get_post_meta($post_id, 'kunaal_card_image', true);
    if ($card_image) {
        return wp_get_attachment_image_url($card_image, $size);
    }
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail_url($post_id, $size);
    }
    return '';
    }
}

/**
 * Helper: Render atmosphere images for About page
 * Moved from page-about.php template to prevent side effects
 */
if (!function_exists('kunaal_render_atmo_images')) {
    function kunaal_render_atmo_images($position, $images) {
        if (empty($images)) {
            return;
        }
        
        foreach ($images as $img) {
            if ($img['position'] !== $position && $img['position'] !== 'auto') {
                continue;
            }
            if ($img['type'] === 'hidden') {
                continue;
            }
            
            $clip_class = '';
            switch ($img['clip']) {
                case 'angle_bottom':
                    $clip_class = 'clip-angle-bottom';
                    break;
                case 'angle_top':
                    $clip_class = 'clip-angle-top';
                    break;
                case 'angle_both':
                    $clip_class = 'clip-angle-both';
                    break;
            }
            
            if ($img['has_quote'] && !empty($img['quote'])) {
                ?>
                <section class="about-quote-image about-layer-image">
                    <div class="about-quote-image-bg parallax-slow <?php echo esc_attr($clip_class); ?>">
                        <img src="<?php echo esc_url($img['image']); ?>" alt="" class="about-image">
                    </div>
                    <div class="about-quote-content reveal-up">
                        <p class="about-quote-text">"<?php echo esc_html($img['quote']); ?>"</p>
                        <?php if (!empty($img['quote_attr'])) : ?>
                        <span class="about-quote-attr">— <?php echo esc_html($img['quote_attr']); ?></span>
                        <?php endif; ?>
                    </div>
                </section>
                <?php
            } else {
                ?>
                <div class="atmo-full <?php echo esc_attr($clip_class); ?> about-layer-image">
                    <img src="<?php echo esc_url($img['image']); ?>" alt="" class="about-image parallax-slow">
                    <?php if (!empty($img['caption'])) : ?>
                    <span class="about-quote-caption"><?php echo esc_html($img['caption']); ?></span>
                    <?php endif; ?>
                </div>
                <?php
            }
        }
    }
}

/**
 * Get all theme mods (cached for request lifetime)
 * 
 * @return array All theme modification values
 */
if (!function_exists('kunaal_get_theme_mods')) {
    function kunaal_get_theme_mods() {
    static $mods = null;
    if ($mods === null) {
        $mods = get_theme_mods();
    }
    return $mods;
    }
}

/**
 * Get theme mod with caching
 * 
 * @param string $key     Theme mod key
 * @param mixed  $default Default value if not set
 * @return mixed Theme mod value or default
 */
if (!function_exists('kunaal_mod')) {
    function kunaal_mod($key, $default = '') {
    $mods = kunaal_get_theme_mods();
    return isset($mods[$key]) ? $mods[$key] : $default;
    }
}

/**
 * Home page query with fallback for managed hosts
 * 
 * Some managed hosts/plugins hook query filters differently on the front page.
 * We do a normal query first, and if it returns empty, we retry with
 * suppress_filters to bypass third-party query mutations.
 * 
 * @param string $post_type Post type to query
 * @param int    $limit     Number of posts to retrieve
 * @return WP_Query Query object
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

        // Fallback: bypass third-party filters
        $base['suppress_filters'] = true;
        return new WP_Query($base);
    }
}

/**
 * Last-resort fallback: bypass WP_Query (and any pre_get_posts interference) by
 * selecting IDs directly from the posts table.
 * 
 * WARNING: This bypasses WordPress query system. Only use when WP_Query fails
 * due to third-party interference. Uses prepared statements for security.
 * 
 * @param string $post_type Post type to query
 * @param int    $limit     Number of posts to retrieve
 * @return array Array of post IDs
 */
if (!function_exists('kunaal_home_recent_ids')) {
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
}

/**
 * Helper: Render essay card markup
 * 
 * @param int|WP_Post $post Post ID or post object
 * @return void Outputs HTML
 */
if (!function_exists('kunaal_render_essay_card')) {
    function kunaal_render_essay_card($post) {
        $post_id = is_object($post) ? $post->ID : (int) $post;
        $post_obj = is_object($post) ? $post : get_post($post_id);
        
        if (!$post_obj) {
            return;
        }
        
        // Get post data directly (don't modify global $post)
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
        
        // Get post data using direct function calls (safer, no global state)
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        $date = get_the_date('Y-m-d', $post_id);
        $date_display = get_the_date('j F Y', $post_id);
        $title_attr = esc_attr(get_the_title($post_id));
        ?>
        <a href="<?php echo esc_url($permalink); ?>" class="card" role="listitem"
           data-title="<?php echo esc_attr($title); ?>"
           data-dek="<?php echo esc_attr($subtitle); ?>"
           data-date="<?php echo esc_attr($date); ?>"
           data-tags="<?php echo esc_attr(implode(',', $topic_slugs)); ?>">
          <div class="media" data-parallax="true">
            <?php if ($card_image) : ?>
              <img src="<?php echo esc_url($card_image); ?>" alt="<?php echo esc_attr($title_attr); ?>" loading="lazy" />
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
            <h3 class="tTitle"><?php echo esc_html($title); ?></h3>
            <div class="details">
              <p class="meta">
                <span><?php echo esc_html($date_display); ?></span>
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
        <?php
    }
}

/**
 * Helper: Render jotting row markup
 * 
 * @param int|WP_Post $post Post ID or post object
 * @return void Outputs HTML
 */
if (!function_exists('kunaal_render_jotting_row')) {
    function kunaal_render_jotting_row($post) {
        $post_id = is_object($post) ? $post->ID : (int) $post;
        $post_obj = is_object($post) ? $post : get_post($post_id);
        
        if (!$post_obj) {
            return;
        }
        
        // Get post data directly (don't modify global $post)
        $subtitle = get_post_meta($post_id, 'kunaal_subtitle', true);
        $topics = get_the_terms($post_id, 'topic');
        $topic_slugs = array();
        
        if ($topics && !is_wp_error($topics)) {
            foreach ($topics as $t) {
                $topic_slugs[] = $t->slug;
            }
        }
        
        // Get post data using direct function calls (safer, no global state)
        $title = get_the_title($post_id);
        $permalink = get_permalink($post_id);
        $date = get_the_date('Y-m-d', $post_id);
        $date_display = get_the_date('j M Y', $post_id);
        ?>
        <a href="<?php echo esc_url($permalink); ?>" class="jRow" role="listitem"
           data-title="<?php echo esc_attr($title); ?>"
           data-text="<?php echo esc_attr($subtitle); ?>"
           data-date="<?php echo esc_attr($date); ?>"
           data-tags="<?php echo esc_attr(implode(',', $topic_slugs)); ?>">
          <span class="jDate"><?php echo esc_html($date_display); ?></span>
          <div class="jContent">
            <h3 class="jTitle"><?php echo esc_html($title); ?></h3>
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
        <?php
    }
}

// Removed: kunaal_build_messenger_target_url and kunaal_qr_img_src - no longer used (messenger QR codes removed)

