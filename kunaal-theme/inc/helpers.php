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

// Removed: kunaal_build_messenger_target_url and kunaal_qr_img_src - no longer used (messenger QR codes removed)

