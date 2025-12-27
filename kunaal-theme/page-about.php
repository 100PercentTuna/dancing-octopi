<?php
/**
 * Template Name: About Page
 * 
 * V22 Polished Design - About page template
 * Matches kunaal-about-v22-polished.html exactly
 *
 * @package Kunaal_Theme
 * @since 4.21.0
 */

get_header();

// Generate dynamic CSS for category colors
$categories = kunaal_get_categories_v22();
if (!empty($categories)) :
?>
<style>
<?php foreach ($categories as $slug => $category) : ?>
.capsule[data-cat="<?php echo esc_attr($slug); ?>"] .capsule-dot {
  background: <?php echo esc_attr($category['color']); ?> !important;
}
<?php endforeach; ?>
</style>
<?php endif; ?>

<!-- Skip Link handled in header.php for consistency -->
<!-- Map tooltip (outside sections for proper positioning) -->
<div class="map-tooltip" id="mapTooltip"></div>

<?php
// Get all data
$first_name = kunaal_mod('kunaal_author_first_name', 'Kunaal');
$last_name = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = trim($first_name . ' ' . $last_name);

$hero_photos = kunaal_get_hero_photos_v22();
$hero_intro = kunaal_mod('kunaal_about_v22_hero_intro', 'A curiosity about humans and human collectives — how we organize, what we believe, why we do what we do.');
$hero_hand_note = kunaal_mod('kunaal_about_v22_hero_hand_note', 'slightly alarming?');
$hero_location = kunaal_mod('kunaal_about_v22_hero_location', 'Singapore');
$hero_listening = kunaal_mod('kunaal_about_v22_hero_listening', 'Ezra Klein Show');
$hero_reading = kunaal_mod('kunaal_about_v22_hero_reading', 'Master of the Senate');

$numbers = kunaal_get_numbers_v22();
$numbers_show = kunaal_mod('kunaal_about_v22_numbers_show', true);

$categories = kunaal_get_categories_v22();
$rabbit_holes = kunaal_get_rabbit_holes_v22();
$rabbit_holes_show = kunaal_mod('kunaal_about_v22_rabbit_holes_show', true);
$rabbit_holes_title = kunaal_mod('kunaal_about_v22_rabbit_holes_title', "Things I can't stop exploring");

$panoramas = kunaal_get_panoramas_v22();

$books = kunaal_get_books_v22();
$books_show = kunaal_mod('kunaal_about_v22_books_show', true);

$digital = kunaal_get_digital_media_v22();
$digital_show = kunaal_mod('kunaal_about_v22_digital_show', true);

$places = kunaal_get_places_v22();
$places_show = kunaal_mod('kunaal_about_v22_places_show', true);
$places_title = kunaal_mod('kunaal_about_v22_places_title', "Where I've been");

$inspirations = kunaal_get_inspirations_v22();
$inspirations_show = kunaal_mod('kunaal_about_v22_inspirations_show', true);
$inspirations_title = kunaal_mod('kunaal_about_v22_inspirations_title', "People I learn from");

$say_hello_show = kunaal_mod('kunaal_about_v22_say_hello_show', true);
$contact_email = kunaal_mod('kunaal_contact_email', '');
$linkedin_url = kunaal_mod('kunaal_linkedin_handle', '');
$twitter_handle = kunaal_mod('kunaal_twitter_handle', '');
?>

<main id="main">
<!-- HERO -->
<section class="hero about-hero">
  <?php $photo_count = count($hero_photos); ?>
  <div class="about-hero-inner">
    <!-- Collage -->
    <div class="hero-collage" aria-hidden="true">
      <?php
      // Robust rendering: always output up to 10 tiles in a dedicated collage grid.
      // This avoids mobile/width breakage caused by auto-placing 10 items into a desktop-only grid.
      for ($i = 0; $i < min(10, $photo_count); $i++) :
          $photo_url = $hero_photos[$i];
          $is_accent = ($i === 2); // Third photo (index 2) is the accent tile (dog-ear).
          $loading = $i < 2 ? 'eager' : 'lazy';
          $tile_classes = array('hero-photo', 'hero-photo--' . ($i + 1));
          if ($is_accent) { $tile_classes[] = 'has-accent'; }
      ?>
      <div class="<?php echo esc_attr(implode(' ', $tile_classes)); ?>" data-idx="<?php echo esc_attr($i + 1); ?>">
        <?php if ($is_accent) : ?>
          <span class="hero-accent" aria-hidden="true"></span>
        <?php endif; ?>
        <img alt="Photo" decoding="async" loading="<?php echo esc_attr($loading); ?>" src="<?php echo esc_url($photo_url); ?>"/>
      </div>
      <?php endfor; ?>
    </div>

    <!-- Text -->
    <div class="hero-text">
      <div class="hero-label" data-reveal="up">About</div>
      <h1 class="hero-title" data-reveal="up">Hi, I'm <span class="name"><?php echo esc_html($first_name); ?></span></h1>
      <p class="hero-intro" data-reveal="up">
        <?php echo esc_html($hero_intro); ?> <span class="hand-note"><?php echo esc_html($hero_hand_note); ?></span>
      </p>
      <div class="hero-meta" data-reveal="up">
        <div class="hero-meta-row">
          <span class="label">Location</span>
          <span class="value"><?php echo esc_html($hero_location); ?></span>
        </div>
        <div class="hero-meta-row">
          <span class="label">Listening</span>
          <span class="value"><?php echo esc_html($hero_listening); ?></span>
        </div>
        <div class="hero-meta-row">
          <span class="label">Reading</span>
          <span class="value"><?php echo esc_html($hero_reading); ?></span>
        </div>
      </div>

      <!-- Mobile-only scroll hint (kept inside the text column so it can't be pushed off-screen) -->
      <div class="scroll-indicator scroll-indicator--inline" aria-hidden="true">
        <span class="scroll-indicator-text">Scroll</span>
        <div class="scroll-indicator-line"></div>
        <div class="scroll-indicator-chevron"></div>
      </div>
    </div>
  </div>

  <!-- Desktop scroll indicator -->
  <div class="scroll-indicator scroll-indicator--abs" id="scrollIndicator" aria-hidden="true">
    <span class="scroll-indicator-text">Scroll</span>
    <div class="scroll-indicator-line"></div>
    <div class="scroll-indicator-chevron"></div>
  </div>
</section>

<?php
// Panorama after hero
if (!empty($panoramas['after_hero'])) :
    foreach ($panoramas['after_hero'] as $panorama) :
        $height_class = 'h-' . esc_attr($panorama['height']);
        $cut_class = $panorama['cut'] !== 'none' ? ' cut-' . esc_attr($panorama['cut']) : '';
        $bg_class = $panorama['bg'] === 'warm' ? ' bg-warm' : '';
?>
<!-- PANORAMA -->
<div class="panorama <?php echo esc_attr($height_class . $cut_class . $bg_class); ?>" data-speed="<?php echo esc_attr($panorama['speed']); ?>">
    <div class="panorama-inner">
        <img alt="Photo" class="panorama-img" decoding="async" loading="lazy" src="<?php echo esc_url($panorama['image']); ?>"/>
    </div>
</div>
<?php
    endforeach;
endif;
?>

<?php if ($numbers_show && !empty($numbers)) : ?>
<!-- NUMBERS - Compact -->
<section class="numbers">
    <div class="numbers-inner">
        <div class="section-label" data-reveal="up" style="text-align:center">By the Numbers</div>
        <div class="numbers-grid">
            <?php foreach ($numbers as $number) : ?>
                <?php if ($number['value'] === 'infinity') : ?>
                    <div class="number-item" data-reveal="up">
                        <div class="number-value infinity-value" style="opacity:0;transform:scale(0.5)">∞</div>
                        <!-- Label removed for infinity - symbol is self-explanatory -->
                    </div>
                <?php else : ?>
                    <div class="number-item" data-reveal="up">
                        <div class="number-value" data-suffix="<?php echo esc_attr($number['suffix']); ?>" data-target="<?php echo esc_attr($number['value']); ?>">0</div>
                        <div class="number-label"><?php echo esc_html($number['label']); ?></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Panorama after numbers
if (!empty($panoramas['after_numbers'])) :
    foreach ($panoramas['after_numbers'] as $panorama) :
        $height_class = 'h-' . esc_attr($panorama['height']);
        $cut_class = $panorama['cut'] !== 'none' ? ' cut-' . esc_attr($panorama['cut']) : '';
        $bg_class = $panorama['bg'] === 'warm' ? ' bg-warm' : '';
?>
<!-- PANORAMA -->
<div class="panorama <?php echo esc_attr($height_class . $cut_class . $bg_class); ?>" data-speed="<?php echo esc_attr($panorama['speed']); ?>">
    <div class="panorama-inner">
        <img alt="Photo" class="panorama-img" decoding="async" loading="lazy" src="<?php echo esc_url($panorama['image']); ?>"/>
    </div>
</div>
<?php
    endforeach;
endif;
?>

<?php if ($rabbit_holes_show && !empty($rabbit_holes)) : ?>
<!-- RABBIT HOLES - Redesigned cloud with images -->
<section class="rabbit-holes section warm">
    <div class="section-inner">
        <div class="section-bgword" data-dir="left" data-marquee="">Rabbit Holes</div>
        <div class="section-label" data-reveal="up">Rabbit Holes</div>
        <h2 class="section-title" data-reveal="up"><?php echo esc_html($rabbit_holes_title); ?></h2>
        <div class="capsules-cloud">
            <?php foreach ($rabbit_holes as $hole) : 
                $category_slug = !empty($hole['category']) ? esc_attr($hole['category']) : '';
                $has_link = !empty($hole['url']);
                $tag = $has_link ? 'a' : 'span';
            ?>
            <<?php echo $tag; ?> class="capsule" data-cat="<?php echo $category_slug; ?>" data-reveal="left" <?php echo $has_link ? 'href="' . esc_url($hole['url']) . '" target="_blank" rel="noopener"' : ''; ?>>
                <?php if (!empty($hole['image'])) : ?>
                <img alt="Photo" class="capsule-img" decoding="async" loading="lazy" src="<?php echo esc_url($hole['image']); ?>"/>
                <?php endif; ?>
                <span class="capsule-text"><?php echo esc_html($hole['text']); ?></span>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($categories)) : ?>
        <div class="capsules-legend">
            <?php foreach ($categories as $slug => $category) : ?>
            <div class="legend-item">
                <div class="legend-dot" style="background:<?php echo esc_attr($category['color']); ?>"></div>
                <?php echo esc_html($category['name']); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php
// Panorama after rabbit holes
if (!empty($panoramas['after_rabbit_holes'])) :
    foreach ($panoramas['after_rabbit_holes'] as $panorama) :
        $height_class = 'h-' . esc_attr($panorama['height']);
        $cut_class = $panorama['cut'] !== 'none' ? ' cut-' . esc_attr($panorama['cut']) : '';
        $bg_class = $panorama['bg'] === 'warm' ? ' bg-warm' : '';
        $squeeze_class = ' squeeze-after-rabbit';
?>
<!-- PANORAMA -->
<div class="panorama <?php echo esc_attr($height_class . $cut_class . $bg_class . $squeeze_class); ?>" data-speed="<?php echo esc_attr($panorama['speed']); ?>">
    <div class="panorama-inner">
        <img alt="Photo" class="panorama-img" decoding="async" fetchpriority="high" loading="eager" src="<?php echo esc_url($panorama['image']); ?>"/>
    </div>
</div>
<?php
    endforeach;
endif;
?>

<?php if (($books_show && !empty($books)) || ($digital_show && !empty($digital))) : ?>
<!-- READING + LISTENING -->
<section class="media-section section">
    <div class="section-inner">
        <div class="media-row">
            <?php if ($books_show && !empty($books)) : ?>
            <div class="media-col">
                <div class="media-col-header">
                    <h3 class="media-col-title">On the nightstand</h3>
                </div>
                <div class="media-grid">
                    <?php foreach ($books as $book) : ?>
                    <div class="media-item" data-reveal="up">
                        <div class="media-cover book">
                            <?php if (!empty($book['cover'])) : ?>
                            <img alt="Photo" decoding="async" loading="lazy" src="<?php echo esc_url($book['cover']); ?>"/>
                            <?php endif; ?>
                        </div>
                        <div class="media-title"><?php echo esc_html($book['title']); ?></div>
                        <div class="media-subtitle"><?php echo esc_html($book['author']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="media-spacer"></div>
            
            <?php if ($digital_show && !empty($digital)) : ?>
            <div class="media-col">
                <div class="media-col-header">
                    <h3 class="media-col-title">On repeat</h3>
                </div>
                <div class="media-grid">
                    <?php foreach ($digital as $item) : 
                        $link_type_label = ucfirst($item['link_type']);
                        if ($item['link_type'] === 'apple') {
                            $link_type_label = 'Apple Podcasts';
                        }
                        $has_link = !empty($item['url']);
                        $tag = $has_link ? 'a' : 'div';
                    ?>
                    <<?php echo $tag; ?> class="media-item" <?php echo $has_link ? 'href="' . esc_url($item['url']) . '" target="_blank" rel="noopener"' : ''; ?> data-reveal="up">
                        <div class="media-cover album">
                            <?php if (!empty($item['cover'])) : ?>
                            <img alt="Photo" decoding="async" loading="lazy" src="<?php echo esc_url($item['cover']); ?>"/>
                            <?php endif; ?>
                            <?php if ($has_link) : ?>
                            <span class="play-icon">▶</span>
                            <?php endif; ?>
                        </div>
                        <div class="media-title"><?php echo esc_html($item['title']); ?></div>
                        <div class="media-subtitle"><?php echo esc_html($item['artist']); ?></div>
                        <?php if ($has_link) : ?>
                        <span class="media-link">↗ <?php echo esc_html($link_type_label); ?></span>
                        <?php endif; ?>
                    </<?php echo $tag; ?>>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Panorama after media
if (!empty($panoramas['after_media'])) :
    foreach ($panoramas['after_media'] as $panorama) :
        $height_class = 'h-' . esc_attr($panorama['height']);
        $cut_class = $panorama['cut'] !== 'none' ? ' cut-' . esc_attr($panorama['cut']) : '';
        $bg_class = $panorama['bg'] === 'warm' ? ' bg-warm' : '';
?>
<!-- PANORAMA -->
<div class="panorama <?php echo esc_attr($height_class . $cut_class . $bg_class); ?>" data-speed="<?php echo esc_attr($panorama['speed']); ?>">
    <div class="panorama-inner">
        <img alt="Photo" class="panorama-img" decoding="async" loading="lazy" src="<?php echo esc_url($panorama['image']); ?>"/>
    </div>
</div>
<?php
    endforeach;
endif;
?>

<?php if ($places_show) : ?>
<!-- MAP -->
<section class="map-section section warm">
    <div class="section-inner">
        <div class="section-bgword" data-dir="right" data-marquee="">Places</div>
        <div class="section-label" data-reveal="up">Places</div>
        <h2 class="section-title" data-reveal="up"><?php echo esc_html($places_title); ?></h2>
        <div id="world-map"></div>
        <div class="map-legend">
            <?php if (!empty($places['current'])) : ?>
            <div class="map-legend-item"><div class="map-legend-dot current"></div> Now</div>
            <?php endif; ?>
            <?php if (!empty($places['lived'])) : ?>
            <div class="map-legend-item"><div class="map-legend-dot lived"></div> Lived</div>
            <?php endif; ?>
            <?php if (!empty($places['visited'])) : ?>
            <div class="map-legend-item"><div class="map-legend-dot visited"></div> Visited</div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Panorama after map
if (!empty($panoramas['after_map'])) :
    foreach ($panoramas['after_map'] as $panorama) :
        $height_class = 'h-' . esc_attr($panorama['height']);
        $cut_class = $panorama['cut'] !== 'none' ? ' cut-' . esc_attr($panorama['cut']) : '';
        $bg_class = $panorama['bg'] === 'warm' ? ' bg-warm' : '';
?>
<!-- PANORAMA -->
<div class="panorama <?php echo esc_attr($height_class . $cut_class . $bg_class); ?>" data-speed="<?php echo esc_attr($panorama['speed']); ?>">
    <div class="panorama-inner">
        <img alt="Photo" class="panorama-img" decoding="async" loading="lazy" src="<?php echo esc_url($panorama['image']); ?>"/>
    </div>
</div>
<?php
    endforeach;
endif;
?>

<?php if ($inspirations_show && !empty($inspirations)) : ?>
<!-- INSPIRATIONS -->
<section class="inspirations section">
    <div class="section-inner">
        <div class="section-bgword" data-dir="left" data-marquee="">Inspirations</div>
        <div class="section-label" data-reveal="up">Inspirations</div>
        <h2 class="section-title" data-reveal="up"><?php echo esc_html($inspirations_title); ?></h2>
        <div class="inspirations-grid">
            <?php foreach ($inspirations as $person) : 
                $has_link = !empty($person['url']);
                $tag = $has_link ? 'a' : 'div';
            ?>
            <<?php echo $tag; ?> class="person-card" <?php echo $has_link ? 'href="' . esc_url($person['url']) . '" target="_blank" rel="noopener"' : ''; ?> data-reveal="up">
                <div class="person-avatar">
                    <?php if (!empty($person['photo'])) : ?>
                    <img alt="Photo" decoding="async" loading="lazy" src="<?php echo esc_url($person['photo']); ?>"/>
                    <?php endif; ?>
                </div>
                <div class="person-info">
                    <div class="person-name"><?php echo esc_html($person['name']); ?></div>
                    <div class="person-role"><?php echo esc_html($person['role']); ?></div>
                </div>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Panorama after inspirations
if (!empty($panoramas['after_inspirations'])) :
    foreach ($panoramas['after_inspirations'] as $panorama) :
        $height_class = 'h-' . esc_attr($panorama['height']);
        $cut_class = $panorama['cut'] !== 'none' ? ' cut-' . esc_attr($panorama['cut']) : '';
        $bg_class = $panorama['bg'] === 'warm' ? ' bg-warm' : '';
?>
<!-- PANORAMA -->
<div class="panorama <?php echo esc_attr($height_class . $cut_class . $bg_class); ?>" data-speed="<?php echo esc_attr($panorama['speed']); ?>">
    <div class="panorama-inner">
        <img alt="Photo" class="panorama-img" decoding="async" loading="lazy" src="<?php echo esc_url($panorama['image']); ?>"/>
    </div>
</div>
<?php
    endforeach;
endif;
?>

<?php if ($say_hello_show && ($contact_email || $linkedin_url || $twitter_handle)) : ?>
<!-- SAY HELLO -->
<section class="say-hello">
    <div class="say-hello-label">Say Hello</div>
    <div class="say-hello-links">
        <?php if ($contact_email) : ?>
        <a class="say-hello-link" href="mailto:<?php echo esc_attr($contact_email); ?>" target="_blank" rel="noopener">
            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewbox="0 0 24 24">
                <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </a>
        <?php endif; ?>
        
        <?php if ($linkedin_url) : ?>
        <a class="say-hello-link" href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener">
            <svg fill="currentColor" viewbox="0 0 24 24">
                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></path>
            </svg>
        </a>
        <?php endif; ?>
        
        <?php if ($twitter_handle) : 
            $twitter_url = 'https://x.com/' . ltrim($twitter_handle, '@');
        ?>
        <a class="say-hello-link" href="<?php echo esc_url($twitter_url); ?>" target="_blank" rel="noopener">
            <svg fill="currentColor" viewbox="0 0 24 24">
                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path>
            </svg>
        </a>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

</main>

<?php get_footer(); ?>
