<?php
/**
 * Template Name: About Page
 *
 * About page template with hero mosaic, rabbit holes, media shelves, map, and inspirations.
 *
 * @package Kunaal_Theme
 * @since 4.21.0
 */

get_header();

// Panorama constants are now defined in functions.php
// CSS variables for category colors are enqueued in functions.php
?>

<!-- Skip Link handled in header.php for consistency -->
<!-- Map tooltip (outside sections for proper positioning) -->
<div class="map-tooltip" id="mapTooltip"></div>

<?php
// Get all data
$first_name = kunaal_mod('kunaal_author_first_name', 'Kunaal');
$last_name = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = trim($first_name . ' ' . $last_name);

// Get photo IDs for optimized rendering with srcset
$hero_photo_ids = kunaal_get_hero_photo_ids();
$hero_intro = kunaal_mod('kunaal_about_hero_intro', 'A curiosity about humans and human collectives — how we organize, what we believe, why we do what we do.');
$hero_hand_note = kunaal_mod('kunaal_about_hero_hand_note', 'slightly alarming?');
$hero_location = kunaal_mod('kunaal_about_hero_location', 'Singapore');
$hero_listening = kunaal_mod('kunaal_about_hero_listening', 'Ezra Klein Show');
$hero_reading = kunaal_mod('kunaal_about_hero_reading', 'Master of the Senate');

$numbers = kunaal_get_numbers();
$numbers_show = kunaal_mod('kunaal_about_numbers_show', true);

$categories = kunaal_get_categories();
$rabbit_holes = kunaal_get_rabbit_holes();
$rabbit_holes_show = kunaal_mod('kunaal_about_rabbit_holes_show', true);
$rabbit_holes_title = kunaal_mod('kunaal_about_rabbit_holes_title', "Things I can't stop exploring");

$panoramas = kunaal_get_panoramas();

$books = kunaal_get_books();
$books_show = kunaal_mod('kunaal_about_books_show', true);

$digital = kunaal_get_digital_media();
$digital_show = kunaal_mod('kunaal_about_digital_show', true);

$places = kunaal_get_places();
$places_show = kunaal_mod('kunaal_about_places_show', true);
$places_title = kunaal_mod('kunaal_about_places_title', "Where I've been");

$inspirations = kunaal_get_inspirations();
$inspirations_show = kunaal_mod('kunaal_about_inspirations_show', true);
$inspirations_title = kunaal_mod('kunaal_about_inspirations_title', "People I learn from");

$say_hello_show = kunaal_mod('kunaal_about_say_hello_show', true);
$contact_email = kunaal_mod('kunaal_contact_email', '');
// Get enabled social links for say-hello section
$about_social_platforms = array('linkedin', 'twitter', 'instagram', 'facebook', 'youtube', 'github', 'tiktok');
$about_enabled_socials = array();
foreach ($about_social_platforms as $platform) {
    $social_data = kunaal_get_social_link($platform);
    if ($social_data) {
        $about_enabled_socials[$platform] = $social_data;
    }
}
?>

<main id="main">
<!-- HERO - Bleed collage layout -->
<section class="hero about-hero" aria-label="About hero">
<div class="hero-grid">
<?php
// Render photos by SLOT NUMBER (1-10), not array index
// Slots 1-4: top row left-to-right
// Slot 5: right-strip top
// Slots 6-9: bottom row left-to-right
// Slot 10: right-strip bottom

// Row 1 - Slots 1-4
for ($slot = 1; $slot <= 4; $slot++) :
    if (!isset($hero_photo_ids[$slot])) continue;
    $photo_id = $hero_photo_ids[$slot];
    // Slot 3 gets accent (dog-ear) unconditionally if it exists
    $has_accent = ($slot === 3);
    $loading = $slot === 1 ? 'eager' : 'lazy';
    $fetchpriority = $slot === 1 ? 'high' : 'auto';
    // Use wp_get_attachment_image for srcset/sizes optimization
    $image_attrs = array(
        'alt' => '',
        'decoding' => 'async',
        'loading' => $loading,
        'fetchpriority' => $fetchpriority,
        'class' => '',
    );
?>
<div class="hero-photo hero-photo--<?php echo esc_attr($slot); ?><?php echo $has_accent ? ' has-accent' : ''; ?>">
    <?php echo wp_get_attachment_image($photo_id, 'full', false, $image_attrs); ?>
</div>
<?php endfor; ?>

<!-- Hero Text (positioned in grid, spans both rows) -->
<div class="hero-text">
    <div class="hero-label" data-reveal="up">About</div>
    <h1 class="hero-title" data-reveal="up">Hi, I'm <span class="name"><?php echo esc_html($first_name); ?></span></h1>
    <p class="hero-intro" data-reveal="up">
        <?php echo nl2br(esc_html($hero_intro)); ?> <span class="hand-note"><?php echo esc_html($hero_hand_note); ?></span>
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
    
    <!-- Scroll indicator - moved inside hero-text after meta rows -->
    <div class="hero-scroll" id="scrollIndicator" aria-hidden="true">
        <span class="hero-scroll__label">SCROLL</span>
        <span class="hero-scroll__line"></span>
    </div>
</div>

<?php
// Row 1 - Slot 5 (right-strip top)
if (isset($hero_photo_ids[5])) :
    $photo_id = $hero_photo_ids[5];
    $image_attrs = array(
        'alt' => '',
        'decoding' => 'async',
        'loading' => 'lazy',
        'class' => '',
    );
?>
<div class="hero-photo hero-photo--5">
    <?php echo wp_get_attachment_image($photo_id, 'full', false, $image_attrs); ?>
</div>
<?php endif; ?>

<!-- Row 2 - Slots 6-9 -->
<?php
for ($slot = 6; $slot <= 9; $slot++) :
    if (!isset($hero_photo_ids[$slot])) continue;
    $photo_id = $hero_photo_ids[$slot];
    $image_attrs = array(
        'alt' => '',
        'decoding' => 'async',
        'loading' => 'lazy',
        'class' => '',
    );
?>
<div class="hero-photo hero-photo--<?php echo esc_attr($slot); ?>">
    <?php echo wp_get_attachment_image($photo_id, 'full', false, $image_attrs); ?>
</div>
<?php endfor; ?>

<?php
// Row 2 - Slot 10 (right-strip bottom)
if (isset($hero_photo_ids[10])) :
    $photo_id = $hero_photo_ids[10];
    $image_attrs = array(
        'alt' => '',
        'decoding' => 'async',
        'loading' => 'lazy',
        'class' => '',
    );
?>
<div class="hero-photo hero-photo--10">
    <?php echo wp_get_attachment_image($photo_id, 'full', false, $image_attrs); ?>
</div>
<?php endif; ?>
</div>
</section>

<?php
// Panorama after hero
kunaal_render_panoramas($panoramas['after_hero'] ?? array());
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
                        <div class="number-label"><?php echo esc_html($number['label']); ?></div>
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
kunaal_render_panoramas($panoramas['after_numbers'] ?? array());
?>

<?php if ($rabbit_holes_show && !empty($rabbit_holes)) : ?>
<!-- RABBIT HOLES - Redesigned cloud with images -->
<section class="rabbit-holes section warm">
    <div class="section-inner">
        <div class="section-bgword" data-dir="left" data-marquee="">Rabbit Holes</div>
        <div class="section-label" data-reveal="up">Rabbit Holes</div>
        <h2 class="section-title u-section-underline" data-reveal="up"><?php echo esc_html($rabbit_holes_title); ?></h2>
        <div class="capsules-cloud">
            <?php foreach ($rabbit_holes as $index => $hole) :
                $category_slug = !empty($hole['category']) ? esc_attr($hole['category']) : '';
                $has_link = !empty($hole['url']);
                $tag = $has_link ? 'a' : 'span';
                // Set float duration based on index (replaces nth-child())
                $float_durations = [6.2, 7.1, 5.8];
                $float_dur = $float_durations[$index % 3] ?? 6.0;
            ?>
            <<?php echo $tag; ?> class="capsule" data-cat="<?php echo $category_slug; ?>" data-reveal="left" <?php echo $has_link ? 'href="' . esc_url($hole['url']) . '" target="_blank" rel="noopener"' : ''; ?> style="--float-dur: <?php echo esc_attr($float_dur); ?>s;">
                <span class="capsule-inner">
                    <?php if (!empty($hole['image'])) : ?>
                    <img alt="" class="capsule-img" decoding="async" loading="lazy" src="<?php echo esc_url($hole['image']); ?>"/>
                    <?php endif; ?>
                    <span class="capsule-text"><?php echo esc_html($hole['text']); ?></span>
                    <span class="capsule-dot" aria-hidden="true"></span>
                </span>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($categories)) : ?>
        <div class="capsules-legend">
            <?php foreach ($categories as $slug => $category) : ?>
            <div class="legend-item">
                <div class="legend-dot" data-cat="<?php echo esc_attr($slug); ?>"></div>
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
kunaal_render_panoramas($panoramas['after_rabbit_holes'] ?? array(), 'squeeze-after-rabbit after-rabbit-holes');
?>

<?php if (($books_show && !empty($books)) || ($digital_show && !empty($digital))) : ?>
<!-- READING + LISTENING - Unified grid with cross-column row alignment -->
<section class="media-section section">
    <div class="section-inner">
        <div class="media-row-layout">
            <?php
            // Calculate grid placement
            // Books: columns 1-3, Digital: columns 5-7
            // Both share rows starting from row 2 (row 1 = headers)
            $books_count = count($books);
            $digital_count = count($digital);
            $max_rows = max(ceil($books_count / 3), ceil($digital_count / 3));
            ?>
            
            <!-- Books Column -->
            <?php if ($books_show && !empty($books)) : ?>
            <div class="media-column media-column--books">
                <div class="media-col-header">
                    <h3 class="media-col-title u-section-underline">On the nightstand</h3>
                </div>
                <div class="media-items-grid">
                    <?php 
                    foreach ($books as $index => $book) : 
                        // Calculate grid position: columns 1-3, rows start at 2
                        $col = ($index % 3) + 1; // 1, 2, or 3
                        $row = floor($index / 3) + 2; // Starting from row 2
                    ?>
                    <div class="media-item media-item--book" data-reveal="up" style="grid-column: <?php echo esc_attr($col); ?>; grid-row: <?php echo esc_attr($row); ?>;">
                        <div class="media-cover book">
                            <?php if (!empty($book['cover'])) : ?>
                            <img alt="<?php echo esc_attr($book['title']); ?> cover" decoding="async" loading="lazy" src="<?php echo esc_url($book['cover']); ?>"/>
                            <?php endif; ?>
                        </div>
                        <div class="media-title"><?php echo esc_html($book['title']); ?></div>
                        <div class="media-subtitle"><?php echo esc_html($book['author']); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Digital Column -->
            <?php if ($digital_show && !empty($digital)) : ?>
            <div class="media-column media-column--digital">
                <div class="media-col-header">
                    <h3 class="media-col-title u-section-underline">On repeat</h3>
                </div>
                <div class="media-items-grid">
                    <?php 
                    foreach ($digital as $index => $item) :
                        $link_type_label = ucfirst($item['link_type']);
                        if ($item['link_type'] === 'apple') {
                            $link_type_label = 'Apple Podcasts';
                        }
                        $has_link = !empty($item['url']);
                        $tag = $has_link ? 'a' : 'div';
                        // Calculate grid position: columns 5-7, rows start at 2
                        $col = ($index % 3) + 5; // 5, 6, or 7
                        $row = floor($index / 3) + 2; // Starting from row 2
                    ?>
                    <<?php echo $tag; ?> class="media-item media-item--digital" <?php echo $has_link ? 'href="' . esc_url($item['url']) . '" target="_blank" rel="noopener"' : ''; ?> data-reveal="up" style="grid-column: <?php echo esc_attr($col); ?>; grid-row: <?php echo esc_attr($row); ?>;">
                        <div class="media-cover album">
                            <?php if (!empty($item['cover'])) : ?>
                            <img alt="<?php echo esc_attr($item['title']); ?> cover" decoding="async" loading="lazy" src="<?php echo esc_url($item['cover']); ?>"/>
                            <?php endif; ?>
                            <?php if ($has_link) : ?>
                            <span class="play-icon">▶</span>
                            <?php endif; ?>
                        </div>
                        <div class="media-title"><?php echo esc_html($item['title']); ?></div>
                        <div class="media-subtitle"><?php echo esc_html($item['artist']); ?></div>
                        <span class="media-link">↗ <?php echo esc_html($link_type_label); ?></span>
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
kunaal_render_panoramas($panoramas['after_media'] ?? array());
?>

<?php if ($places_show && !empty($places)) : ?>
<!-- PLACES -->
<section class="places section">
    <div class="section-inner">
        <div class="section-label" data-reveal="up">Places</div>
        <h2 class="section-title u-section-underline" data-reveal="up"><?php echo esc_html($places_title); ?></h2>
        <div class="map-container" id="world-map" data-reveal="up">
            <!-- Map rendered by D3.js -->
        </div>
        <div class="map-legend" data-reveal="up">
            <div class="map-legend-item">
                <div class="map-legend-dot current"></div>
                <span>Current</span>
            </div>
            <div class="map-legend-item">
                <div class="map-legend-dot lived"></div>
                <span>Lived</span>
            </div>
            <div class="map-legend-item">
                <div class="map-legend-dot visited"></div>
                <span>Visited</span>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php
// Panorama after places (also check after_map for backward compatibility)
$places_panoramas = array_merge(
    $panoramas['after_places'] ?? array(),
    $panoramas['after_map'] ?? array()
);
kunaal_render_panoramas($places_panoramas);
?>

<?php if ($inspirations_show && !empty($inspirations)) : ?>
<!-- INSPIRATIONS -->
<section class="inspirations section warm">
    <div class="section-inner">
        <div class="section-label" data-reveal="up">Inspirations</div>
        <h2 class="section-title u-section-underline" data-reveal="up"><?php echo esc_html($inspirations_title); ?></h2>
        <div class="inspirations-grid">
            <?php foreach ($inspirations as $inspiration) :
                $has_url = !empty($inspiration['url']);
                $tag = $has_url ? 'a' : 'div';
            ?>
            <<?php echo $tag; ?> class="inspiration-item" <?php echo $has_url ? 'href="' . esc_url($inspiration['url']) . '" target="_blank" rel="noopener"' : ''; ?> data-reveal="up">
                <?php if (!empty($inspiration['photo'])) : ?>
                <div class="inspiration-photo">
                    <img alt="" decoding="async" loading="lazy" src="<?php echo esc_url($inspiration['photo']); ?>"/>
                </div>
                <?php endif; ?>
                <div class="inspiration-name"><?php echo esc_html($inspiration['name']); ?></div>
                <div class="inspiration-role"><?php echo esc_html($inspiration['role']); ?></div>
                <div class="inspiration-note"><?php echo esc_html($inspiration['note']); ?></div>
            </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($say_hello_show) : ?>
<!-- SAY HELLO -->
<section class="say-hello section">
    <div class="section-inner">
        <div class="section-label" data-reveal="up">Say Hello</div>
        <h2 class="section-title u-section-underline" data-reveal="up">Let's connect</h2>
        <p class="say-hello-text" data-reveal="up">
            <?php if ($contact_email) : ?>
            <a href="mailto:<?php echo esc_attr($contact_email); ?>"><?php echo esc_html($contact_email); ?></a>
            <?php endif; ?>
        </p>
        <?php if (!empty($about_enabled_socials)) : ?>
        <div class="say-hello-social" data-reveal="up">
            <?php foreach ($about_enabled_socials as $platform => $data) : ?>
            <a href="<?php echo esc_url($data['url']); ?>" target="_blank" rel="noopener" class="say-hello-social-link" aria-label="<?php echo esc_attr($data['label']); ?>">
                <?php echo kunaal_get_social_icon($platform); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG is hardcoded ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
</main>

<?php
// Panorama after inspirations (if section is shown)
if ($inspirations_show) {
    kunaal_render_panoramas($panoramas['after_inspirations'] ?? array());
}
?>

<?php get_footer(); ?>
