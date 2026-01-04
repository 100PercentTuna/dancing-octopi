<?php
/**
 * Embed Card Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$url = isset($attributes['url']) ? $attributes['url'] : '';
$title = isset($attributes['title']) ? $attributes['title'] : '';
$description = isset($attributes['description']) ? $attributes['description'] : '';
$image_url = isset($attributes['imageUrl']) ? $attributes['imageUrl'] : '';
$site_name = isset($attributes['siteName']) ? $attributes['siteName'] : '';
$favicon = isset($attributes['favicon']) ? $attributes['favicon'] : '';
$layout = isset($attributes['layout']) ? $attributes['layout'] : 'horizontal';
$open_in_new_tab = isset($attributes['openInNewTab']) ? $attributes['openInNewTab'] : true;
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

if (empty($url)) {
    return;
}

// Extract domain from URL
$parsed_url = wp_parse_url($url);
$domain = isset($parsed_url['host']) ? preg_replace('/^www\./', '', $parsed_url['host']) : '';

// Use domain as fallback for site name
if (empty($site_name)) {
    $site_name = $domain;
}

// Generate default favicon if none provided
if (empty($favicon) && !empty($domain)) {
    $favicon = 'https://www.google.com/s2/favicons?domain=' . rawurlencode($domain) . '&sz=32';
}

$target = $open_in_new_tab ? ' target="_blank"' : '';
$rel = $open_in_new_tab ? ' rel="noopener"' : '';
$show_image = $layout !== 'compact' && !empty($image_url);
?>
<div<?php echo $anchor; ?> class="wp-block-kunaal-embed-card embed-card embed-card--<?php echo esc_attr($layout); ?><?php echo $class_name; ?>">
    <a href="<?php echo esc_url($url); ?>" class="embed-card__link"<?php echo $target . $rel; ?>>
        <?php if ($show_image) { ?>
            <div class="embed-card__image">
                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
            </div>
        <?php } ?>
        
        <div class="embed-card__content">
            <div class="embed-card__meta">
                <?php if (!empty($favicon)) { ?>
                    <img src="<?php echo esc_url($favicon); ?>" alt="" class="embed-card__favicon" loading="lazy" />
                <?php } ?>
                <span class="embed-card__domain"><?php echo esc_html($site_name); ?></span>
            </div>
            
            <?php if (!empty($title)) { ?>
                <h4 class="embed-card__title"><?php echo esc_html($title); ?></h4>
            <?php } ?>
            
            <?php if (!empty($description)) { ?>
                <p class="embed-card__description"><?php echo esc_html($description); ?></p>
            <?php } ?>
        </div>
        
        <div class="embed-card__arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 17L17 7M17 7H7M17 7V17"/>
            </svg>
        </div>
    </a>
</div>

