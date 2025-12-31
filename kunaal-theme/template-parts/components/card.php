<?php
/**
 * Component: Essay Card
 *
 * Canonical essay card markup with image, scrim, overlay, and details.
 * Use this component for ALL essay/article cards site-wide.
 *
 * @package Kunaal_Theme
 * @since 4.31.0
 *
 * @param array $args {
 *     @type int    $post_id   Post ID (required)
 *     @type string $title     Post title
 *     @type string $permalink Post URL
 *     @type string $date      Date in Y-m-d format (for data attribute)
 *     @type string $date_display Human-readable date
 *     @type string $subtitle  Post subtitle/dek
 *     @type array  $topics    Array of topic term objects
 *     @type array  $topic_slugs Array of topic slugs
 *     @type string $card_image Card image URL
 *     @type string $read_time Read time in minutes
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get args with defaults
$post_id      = $args['post_id'] ?? 0;
$title        = $args['title'] ?? '';
$permalink    = $args['permalink'] ?? '';
$date         = $args['date'] ?? '';
$date_display = $args['date_display'] ?? '';
$subtitle     = $args['subtitle'] ?? '';
$topics       = $args['topics'] ?? [];
$topic_slugs  = $args['topic_slugs'] ?? [];
$card_image   = $args['card_image'] ?? '';
$read_time    = $args['read_time'] ?? '';

if (empty($post_id) || empty($title) || empty($permalink)) {
    return;
}
?>
<li>
<a href="<?php echo esc_url($permalink); ?>" class="card"
   data-title="<?php echo esc_attr($title); ?>"
   data-dek="<?php echo esc_attr($subtitle); ?>"
   data-date="<?php echo esc_attr($date); ?>"
   data-tags="<?php echo esc_attr(implode(',', $topic_slugs)); ?>">
  <div class="media" data-parallax="true">
    <?php if ($card_image) : ?>
      <img src="<?php echo esc_url($card_image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
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
</li>

