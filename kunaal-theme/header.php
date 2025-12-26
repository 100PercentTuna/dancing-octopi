<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php 
  // Custom favicon
  $favicon = get_theme_mod('kunaal_favicon', '');
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

<!-- Progress bar -->
<div class="progress" aria-hidden="true">
  <div class="progressFill" id="progressFill"></div>
</div>

<?php
$avatar_url = get_theme_mod('kunaal_avatar', '');
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$tagline = get_theme_mod('kunaal_author_tagline', 'A slightly alarming curiosity about humans and human collectives.');
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
      <a class="uBlue<?php echo (is_post_type_archive('essay') || is_singular('essay')) ? ' current' : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('essay')); ?>">Essays</a>
      <a class="uBlue<?php echo (is_post_type_archive('jotting') || is_singular('jotting')) ? ' current' : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('jotting')); ?>">Jottings</a>
      <?php
      $about_page = get_page_by_path('about');
      if ($about_page) :
      ?>
        <a class="uBlue<?php echo is_page('about') ? ' current' : ''; ?>" href="<?php echo esc_url(get_permalink($about_page)); ?>">About</a>
      <?php endif; ?>
      <?php
      $contact_page = get_page_by_path('contact');
      if ($contact_page) :
      ?>
        <a class="uBlue<?php echo is_page('contact') ? ' current' : ''; ?>" href="<?php echo esc_url(get_permalink($contact_page)); ?>">Contact</a>
      <?php endif; ?>
    </nav>

    <button class="theme-toggle" type="button" aria-label="Toggle dark mode" aria-pressed="false">
      <span class="theme-toggle-icon" aria-hidden="true">🌙</span>
      <span class="sr-only">Toggle dark mode</span>
    </button>

    <button class="navToggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <path d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
  </div>
</header>
