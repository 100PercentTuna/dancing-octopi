<?php
/**
 * Author Bio Block - Server-side rendering
 *
 * @package Kunaal_Theme
 */

$use_theme_settings = isset($attributes['useThemeSettings']) ? $attributes['useThemeSettings'] : true;
$layout = isset($attributes['layout']) ? $attributes['layout'] : 'horizontal';
$anchor = isset($attributes['anchor']) ? ' id="' . esc_attr($attributes['anchor']) . '"' : '';
$class_name = isset($attributes['className']) ? ' ' . esc_attr($attributes['className']) : '';

// Get author details from theme settings or block attributes
if ($use_theme_settings) {
    $name = kunaal_mod('kunaal_author_first_name', 'Kunaal') . ' ' . kunaal_mod('kunaal_author_last_name', 'Wadhwa');
    $title_text = kunaal_mod('kunaal_author_title', '');
    $bio = isset($attributes['bio']) ? $attributes['bio'] : kunaal_mod('kunaal_author_bio', '');
    $avatar_url = kunaal_mod('kunaal_author_avatar', '');
    $email = kunaal_mod('kunaal_author_email', '');
    $website = home_url('/');
    $twitter = kunaal_mod('kunaal_author_twitter', '');
    $linkedin = kunaal_mod('kunaal_author_linkedin', '');
} else {
    $name = isset($attributes['name']) ? $attributes['name'] : '';
    $title_text = isset($attributes['title']) ? $attributes['title'] : '';
    $bio = isset($attributes['bio']) ? $attributes['bio'] : '';
    $avatar_url = isset($attributes['avatarUrl']) ? $attributes['avatarUrl'] : '';
    $email = isset($attributes['email']) ? $attributes['email'] : '';
    $website = isset($attributes['website']) ? $attributes['website'] : '';
    $twitter = isset($attributes['twitter']) ? $attributes['twitter'] : '';
    $linkedin = isset($attributes['linkedin']) ? $attributes['linkedin'] : '';
}

$show_more_link = isset($attributes['showMoreLink']) ? $attributes['showMoreLink'] : false;
$more_url = isset($attributes['moreUrl']) ? $attributes['moreUrl'] : '';

// Use Gravatar as fallback if no avatar set
if (empty($avatar_url) && !empty($email)) {
    $avatar_url = get_avatar_url($email, array('size' => 160));
}

// Generate placeholder if still no avatar
if (empty($avatar_url)) {
    $avatar_url = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23999'%3E%3Cpath d='M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z'/%3E%3C/svg%3E";
}

$has_social = !empty($email) || !empty($website) || !empty($twitter) || !empty($linkedin);
?>
<aside<?php echo $anchor; ?> class="wp-block-kunaal-author-bio author-bio author-bio--<?php echo esc_attr($layout); ?><?php echo $class_name; ?>" role="complementary" aria-label="<?php esc_attr_e('About the author', 'kunaal-theme'); ?>">
    <div class="author-bio__avatar">
        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
    </div>
    
    <div class="author-bio__content">
        <?php if (!empty($name)) { ?>
            <h3 class="author-bio__name"><?php echo esc_html($name); ?></h3>
        <?php } ?>
        
        <?php if (!empty($title_text)) { ?>
            <p class="author-bio__title"><?php echo esc_html($title_text); ?></p>
        <?php } ?>
        
        <?php if (!empty($bio)) { ?>
            <div class="author-bio__bio">
                <?php echo wp_kses_post($bio); ?>
            </div>
        <?php } ?>
        
        <?php if ($has_social) { ?>
            <div class="author-bio__social">
                <?php if (!empty($website)) { ?>
                    <a href="<?php echo esc_url($website); ?>" class="author-bio__social-link" aria-label="<?php esc_attr_e('Website', 'kunaal-theme'); ?>" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>
                    </a>
                <?php } ?>
                
                <?php if (!empty($twitter)) { ?>
                    <a href="https://twitter.com/<?php echo esc_attr($twitter); ?>" class="author-bio__social-link" aria-label="<?php esc_attr_e('Twitter/X', 'kunaal-theme'); ?>" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                <?php } ?>
                
                <?php if (!empty($linkedin)) { ?>
                    <a href="<?php echo esc_url($linkedin); ?>" class="author-bio__social-link" aria-label="<?php esc_attr_e('LinkedIn', 'kunaal-theme'); ?>" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                <?php } ?>
                
                <?php if (!empty($email)) { ?>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="author-bio__social-link" aria-label="<?php esc_attr_e('Email', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 01-2.06 0L2 7"/></svg>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
        
        <?php if ($show_more_link && !empty($more_url)) { ?>
            <a href="<?php echo esc_url($more_url); ?>" class="author-bio__more">
                <?php esc_html_e('More by this author', 'kunaal-theme'); ?>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        <?php } ?>
    </div>
</aside>

