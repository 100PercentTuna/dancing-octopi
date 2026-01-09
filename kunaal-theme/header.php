<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <?php
  // Favicon implementation for Google Search (meets Google's requirements)
  // Priority: WordPress Site Icon (preferred by Google) → Custom favicon → /favicon.ico fallback
  // Google requirements: minimum 48x48px, multiple sizes recommended, stable URL
  
  $has_site_icon = false;
  if (function_exists('get_site_icon_url')) {
      $test_icon = get_site_icon_url(32);
      $has_site_icon = !empty($test_icon);
  }
  
  if ($has_site_icon) {
      // WordPress Site Icon is set (preferred by Google)
      // WordPress automatically generates multiple sizes, so we output all available sizes
      $sizes = array(32, 180, 192, 270, 512);
      foreach ($sizes as $size) {
          $icon_url = get_site_icon_url($size);
          if ($icon_url) {
              if ($size === 180) {
                  // Apple touch icon
                  echo '<link rel="apple-touch-icon" sizes="' . esc_attr($size . 'x' . $size) . '" href="' . esc_url($icon_url) . '">' . "\n";
              } else {
                  // Standard favicon with sizes attribute (Google requirement)
                  echo '<link rel="icon" type="image/png" sizes="' . esc_attr($size . 'x' . $size) . '" href="' . esc_url($icon_url) . '">' . "\n";
              }
          }
      }
  } else {
      // Fallback to custom favicon from Customizer
      $custom_favicon = kunaal_mod('kunaal_favicon', '');
      if (!empty($custom_favicon)) {
          // Output multiple sizes for better compatibility (Google recommends 48x48 minimum)
          // Use the same image for all sizes (browsers will scale appropriately)
          echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url($custom_favicon) . '">' . "\n";
          echo '<link rel="icon" type="image/png" sizes="48x48" href="' . esc_url($custom_favicon) . '">' . "\n";
          echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url($custom_favicon) . '">' . "\n";
          echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url($custom_favicon) . '">' . "\n";
      }
  }
  
  // Fallback: /favicon.ico (Google checks this as a last resort)
  // Note: WordPress doesn't automatically create this file, but the link tag helps
  echo '<link rel="icon" href="' . esc_url(home_url('/favicon.ico')) . '" type="image/x-icon">' . "\n";
  ?>
  <?php
  // Theme preference script (prevents flash) - inline for critical path to avoid FOUC
  // This must run synchronously before styles load, so inline is appropriate here
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
<?php
if (function_exists('wp_body_open')) {
    wp_body_open();
}
?>

<!-- Progress bar -->
<div class="progress" aria-hidden="true">
  <div class="progressFill no-transition" id="progressFill"></div>
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
        <?php if (!empty($avatar_url)) { ?>
          <img id="avatarImg" src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($first_name . ' ' . $last_name); ?>" />
        <?php } ?>
      </div>
      <div class="nameWrap">
        <p class="nameLine">
          <span><?php echo esc_html($first_name); ?></span>
          <span class="surname"><?php echo esc_html($last_name); ?></span>
        </p>
        <p class="subtitle"><?php echo nl2br(esc_html($tagline)); ?></p>
      </div>
    </a>

    <nav class="nav" id="nav" data-ui="nav" role="navigation">
      <?php
      // Use constant from inc/Setup/constants.php (single source of truth)
      $current_class = KUNAAL_NAV_CURRENT_CLASS;
      ?>
      <a class="<?php echo (is_post_type_archive('essay') || is_singular('essay')) ? $current_class : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('essay')); ?>"><?php esc_html_e('Essays', 'kunaal-theme'); ?></a>
      <a class="<?php echo (is_post_type_archive('jotting') || is_singular('jotting')) ? $current_class : ''; ?>" href="<?php echo esc_url(get_post_type_archive_link('jotting')); ?>"><?php esc_html_e('Jottings', 'kunaal-theme'); ?></a>
      <?php
      $about_page = get_page_by_path('about');
      if ($about_page) {
          echo '<a class="' . (is_page('about') ? $current_class : '') . '" href="' . esc_url(get_permalink($about_page)) . '">';
          esc_html_e('About', 'kunaal-theme');
          echo '</a>';
      }
      ?>
      <?php
      $contact_page = get_page_by_path('contact');
      if ($contact_page) {
          echo '<a class="' . (is_page('contact') ? $current_class : '') . '" href="' . esc_url(get_permalink($contact_page)) . '">';
          esc_html_e('Contact', 'kunaal-theme');
          echo '</a>';
      }
      ?>
    </nav>

    <div class="mastControls">
      <button class="navToggle" id="navToggle" data-ui="nav-toggle" aria-label="<?php esc_attr_e('Toggle navigation', 'kunaal-theme'); ?>" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

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
    </div>
  </div>
</header>
