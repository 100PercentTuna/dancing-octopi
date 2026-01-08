<?php
/**
 * Component: Hero Image
 *
 * Canonical hero image markup for single content templates.
 * Contract-first: templates render the structural wrapper required by CSS/JS.
 *
 * @package Kunaal_Theme
 * @since 4.99.28
 *
 * @param array $args {
 *   @type int    $post_id Post ID (defaults to current post).
 *   @type string $size    Image size (default: 'essay-hero').
 *   @type bool   $reveal  Whether to include the .reveal class (default: true).
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_the_ID();
$size    = isset($args['size']) ? (string) $args['size'] : 'essay-hero';
$reveal  = isset($args['reveal']) ? (bool) $args['reveal'] : true;

if (!$post_id || !has_post_thumbnail($post_id)) {
    return;
}

$classes = 'heroImage' . ($reveal ? ' reveal' : '');
?>
<figure class="<?php echo esc_attr($classes); ?>">
  <div class="heroImage__media">
    <?php
    echo get_the_post_thumbnail(
        $post_id,
        $size,
        array(
            'class'         => 'heroImage__img',
            'decoding'      => 'async',
            'fetchpriority' => 'high',
        )
    );
    ?>
  </div>
</figure>


