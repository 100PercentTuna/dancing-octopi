<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php
  // Custom favicon
  $favicon = kunaal_mod('kunaal_favicon', '');
  if ($favicon) : ?>
    <link rel="icon" type="image/png" href="<?php echo esc_url($favicon); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url($favicon); ?>">
  <?php endif; ?>
  <?php
  // Inline script to set theme before render (prevents flash)
  ?>
  <script>
    (function() {
      const saved = localStorage.getItem('kunaal-theme-preference');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = saved || (prefersDark ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
    })();
  </script>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if (function_exists('wp_body_open')) { wp_body_open(); } ?>

<!-- Skip Links for Accessibility -->
<a href="#main" class="skip-link"><?php esc_html_e('Skip to main content', 'kunaal-theme'); ?></a>
<a href="#nav" class="skip-link"><?php esc_html_e('Skip to navigation', 'kunaal-theme'); ?></a>

<!-- Progress bar -->
<div class="progress" aria-hidden="true">
  <div class="progressFill" id="progressFill"></div>
</div>

<?php
$avatar_url = kunaal_mod('kunaal_avatar', '');
$first_name = kunaal_mod('kunaal_author_first_name', 'Kunaal');
$last_name = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
$tagline = kunaal_mod('kunaal_author_tagline', 'A slightly alarming curiosity about humans and human collectives.');
$initials = kunaal_get_initials();
?>

<header class="mast" id="mast">
  <div class="container mastInner">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Home">
      <div class="avatar<?php echo empty($avatar_url) ? ' noImg' : ''; ?>" id="avatar" data-initials="<?php echo esc_attr($initials); ?>">
        <?php if (!empty($avatar_url)) : ?>
          <img id="avatarImg" src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($first_name . ' ' . $last_name); ?>" />
        <?php endif; ?>
      </div>
      <div class="nameWrap">
        <p class="nameLine">
          <span><?php echo esc_html($first_name); ?></span>
          <span class="surname"><?php echo esc_html($last_name); ?></span>
        </p>
        <p class="subtitle"><?php echo esc_html($tagline); ?></p>
      </div>
    </a>

    <nav class="nav" id="nav" role="navigation">
      <?php
      // Constant for current navigation class
      if (!defined('KUNAAL_NAV_CURRENT_CLASS')) {
          define('KUNAAL_NAV_CURRENT_CLASS', ' current');
      }
      $current_class = KUNAAL_NAV_CURRENT_CLASS;
      ?>
      <a class="uBlue<?php echo (is_post_type_archive('essay') || is_singular('essay')) ? $current_class : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('essay')); ?>"><?php esc_html_e('Essays', 'kunaal-theme'); ?></a>
      <a class="uBlue<?php echo (is_post_type_archive('jotting') || is_singular('jotting')) ? $current_class : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('jotting')); ?>"><?php esc_html_e('Jottings', 'kunaal-theme'); ?></a>
      <?php
      $about_page = get_page_by_path('about');
      if ($about_page) {
      ?>
        <a class="uBlue<?php echo is_page('about') ? $current_class : ''; ?>" href="<?php echo esc_url(get_permalink($about_page)); ?>"><?php esc_html_e('About', 'kunaal-theme'); ?></a>
      <?php
      }
      ?>
      <?php
      $contact_page = get_page_by_path('contact');
      if ($contact_page) {
      ?>
        <a class="uBlue<?php echo is_page('contact') ? $current_class : ''; ?>" href="<?php echo esc_url(get_permalink($contact_page)); ?>"><?php esc_html_e('Contact', 'kunaal-theme'); ?></a>
      <?php
      }
      ?>
    </nav>

    <button class="theme-toggle" type="button" aria-label="<?php esc_attr_e('Toggle dark mode', 'kunaal-theme'); ?>" aria-pressed="false">
      <svg class="theme-toggle-icon theme-toggle-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
      </svg>
      <svg class="theme-toggle-icon theme-toggle-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <circle cx="12" cy="12" r="5"/>
        <line x1="12" y1="1" x2="12" y2="3"/>
        <line x1="12" y1="21" x2="12" y2="23"/>
        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
        <line x1="1" y1="12" x2="3" y2="12"/>
        <line x1="21" y1="12" x2="23" y2="12"/>
        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
      </svg>
      <span class="sr-only"><?php esc_html_e('Toggle dark mode', 'kunaal-theme'); ?></span>
    </button>

    <button class="navToggle" id="navToggle" aria-label="<?php esc_attr_e('Toggle navigation', 'kunaal-theme'); ?>" aria-expanded="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <path d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>
</header>
