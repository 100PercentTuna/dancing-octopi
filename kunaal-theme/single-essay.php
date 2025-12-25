<?php
/**
 * Single Essay Template
 *
 * @package Kunaal_Theme
 */

get_header();
?>

<!-- Unified Action Dock (Right Side) - Share, Subscribe, Download -->
<div class="actionDock" id="actionDock">
  <!-- Share Button -->
  <button class="dockButton" id="shareToggle" aria-label="Share this essay" data-action="share">
    <span class="tip">Share</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/>
      <path d="M8.59 13.51l6.83 3.98M15.41 6.51l-6.82 3.98"/>
    </svg>
  </button>
  
  <!-- Subscribe Button -->
  <?php 
  $sub_location = get_theme_mod('kunaal_subscribe_location', 'both');
  if (get_theme_mod('kunaal_subscribe_enabled', false) && in_array($sub_location, array('dock', 'both'))) : 
  ?>
  <button class="dockButton" id="subscribeToggle" aria-label="Subscribe to updates" data-action="subscribe">
    <span class="tip">Subscribe</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
      <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
  </button>
  <?php endif; ?>
  
  <!-- Download/Print Button -->
  <?php if (shortcode_exists('dkpdf-button') || true) : ?>
  <button class="dockButton" id="downloadButton" aria-label="Download PDF" data-action="download">
    <span class="tip">Download PDF</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
      <polyline points="7 10 12 15 17 10"/>
      <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
  </button>
  <?php endif; ?>
</div>

<!-- Share Panel (slides out when share button clicked) -->
<div class="sharePanel" id="sharePanel">
  <button class="shareItem" data-share="linkedin" aria-label="Share on LinkedIn">
    <span class="tip">LinkedIn</span>
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
  </button>
  <button class="shareItem" data-share="x" aria-label="Share on X">
    <span class="tip">X</span>
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
  </button>
  <button class="shareItem" data-share="whatsapp" aria-label="Share on WhatsApp">
    <span class="tip">WhatsApp</span>
    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
  </button>
  <button class="shareItem" data-share="email" aria-label="Share via Email">
    <span class="tip">Email</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
  </button>
  <button class="shareItem" data-share="copy" aria-label="Copy link">
    <span class="tip">Copy link</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
  </button>
</div>

<!-- Subscribe Panel (slides out when subscribe button clicked) -->
<?php 
$sub_location = get_theme_mod('kunaal_subscribe_location', 'both');
if (get_theme_mod('kunaal_subscribe_enabled', false) && in_array($sub_location, array('dock', 'both'))) : 
?>
<div class="subscribePanel" id="subscribePanel">
  <div class="subscribePanelContent">
    <h4><?php echo esc_html(get_theme_mod('kunaal_subscribe_heading', 'Stay updated')); ?></h4>
    <p><?php echo esc_html(get_theme_mod('kunaal_subscribe_description', 'Get notified when new essays are published.')); ?></p>
    <form class="subscribe-form-dock" action="<?php echo esc_url(get_theme_mod('kunaal_subscribe_form_action', '')); ?>" method="post">
      <input type="email" name="email" placeholder="Your email" required />
      <button type="submit">Subscribe</button>
    </form>
  </div>
</div>
<?php endif; ?>


<main class="article container">
  <?php while (have_posts()) : the_post(); ?>
    <?php
    $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
    $read_time = get_post_meta(get_the_ID(), 'kunaal_read_time', true);
    $topics = get_the_terms(get_the_ID(), 'topic');
    ?>

    <article>
      <header class="articleHeader reveal">
        <div class="articleMeta">
          <span><?php echo esc_html(get_the_date('j F Y')); ?></span>
          <?php if ($read_time) : ?>
            <span class="dot"></span>
            <span><?php echo esc_html($read_time); ?> min read</span>
          <?php endif; ?>
          <?php if ($topics && !is_wp_error($topics)) : ?>
            <?php foreach ($topics as $topic) : ?>
              <span class="dot"></span>
              <a href="<?php echo esc_url(get_term_link($topic)); ?>">#<?php echo esc_html($topic->name); ?></a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
        
        <h1 class="articleTitle"><?php the_title(); ?></h1>
        
        <?php if ($subtitle) : ?>
          <p class="articleDek"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
      </header>

      <?php if (has_post_thumbnail()) : ?>
        <figure class="heroImage reveal">
          <?php the_post_thumbnail('essay-hero'); ?>
        </figure>
      <?php endif; ?>

      <div class="articleContent">
        <div class="prose" id="articleProse">
          <?php the_content(); ?>
          
          <?php // Subscribe Section (controlled via Customizer) ?>
          <?php kunaal_subscribe_section(); ?>
        </div>
        
        <aside class="rail" id="articleRail">
          <div class="railSection">
            <h5>On this page</h5>
            <ul class="tocList" id="tocList">
              <!-- Populated by JS -->
            </ul>
          </div>
        </aside>
      </div>
    </article>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
