# 08 - STATES & EDGE CASES
## Complete Specification

---

## OVERVIEW

Every section must handle **incomplete data gracefully**. The page should never break, look empty, or show errors. This document defines:
- Empty states for each section
- Fallback UI for missing content
- Error handling strategies
- Minimum and maximum content scenarios

---

## EMPTY STATES BY SECTION

### Hero Section

| Scenario | Behavior |
|----------|----------|
| No photos | Show name/tagline only, centered, no collage |
| No name | Use site title as fallback |
| No tagline | Hide tagline, name only |
| No annotation | Hide annotation element completely |
| 1 photo only | Single photo, larger, centered-left |

```html
<!-- Empty hero fallback -->
<section class="about-hero about-hero--minimal">
  <div class="hero-identity hero-identity--centered">
    <h1 class="hero-name"><?php echo esc_html($name ?: get_bloginfo('name')); ?></h1>
    <?php if ($tagline) : ?>
      <p class="hero-tagline"><?php echo esc_html($tagline); ?></p>
    <?php endif; ?>
  </div>
</section>
```

```css
.about-hero--minimal {
  min-height: 60vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hero-identity--centered {
  text-align: center;
  position: static;
}
```

### Bio Section

| Scenario | Behavior |
|----------|----------|
| No bio content | Hide entire bio section |
| Very short bio (< 50 chars) | Still display with drop cap |
| Very long bio (> 2000 chars) | No truncation, natural flow |
| No pull quote | Hide pull quote block |
| Pull quote, no attribution | Hide attribution line |

```php
<?php 
$bio_content = get_the_content();
if (!empty(trim($bio_content))) : ?>
  <section class="about-bio">
    <!-- Bio displays -->
  </section>
<?php endif; ?>
```

### Bookshelf Section

| Scenario | Behavior |
|----------|----------|
| No books | Hide entire bookshelf section |
| 1 book only | Center single book, shorter shelf |
| 2-3 books | Centered group, proportional shelf |
| Missing book cover | Show placeholder gradient |
| Missing book title | Show "Untitled" |
| Missing author | Hide author line |

```css
/* Single book centering */
.bookshelf--single {
  justify-content: center;
}

.bookshelf--single .book {
  margin: 0 auto;
}

.bookshelf--single::after {
  max-width: 200px; /* Shorter shelf */
}

/* Book placeholder */
.book--placeholder img {
  display: none;
}

.book--placeholder::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    var(--warmLight) 0%,
    var(--warm) 100%
  );
  border-radius: 2px 4px 4px 2px;
}
```

### Atmospheric Images

| Scenario | Behavior |
|----------|----------|
| No images configured | No atmospheric sections rendered |
| Some positions empty | Skip those positions, flow continues |
| Image fails to load | Show subtle gradient placeholder |
| Quote without image | Don't show quote (requires context) |
| All set to "hidden" | Content-only layout |

```javascript
// Image load error handling
document.querySelectorAll('.atmo-strip img, .atmo-window img').forEach(img => {
  img.addEventListener('error', () => {
    img.parentElement.classList.add('atmo--fallback');
    img.style.display = 'none';
  });
});
```

```css
.atmo--fallback {
  background: linear-gradient(
    135deg,
    var(--bgAlt) 0%,
    var(--bgWarm) 100%
  );
  min-height: 150px;
}
```

### World Map

| Scenario | Behavior |
|----------|----------|
| No countries selected | Hide entire map section |
| Current only | Show map with one country, marker |
| Invalid country codes | Silently ignore, log warning |
| Story too long | Truncate at 200 chars with ellipsis |
| Story without country | Don't display (invalid) |
| No stories at all | Countries colored but no tooltips |

```php
<?php
$has_countries = !empty($visited) || !empty($lived) || !empty($current);
if ($show_map && $has_countries) : ?>
  <section class="about-map">
    <!-- Map renders -->
  </section>
<?php endif; ?>
```

### Interests Cloud

| Scenario | Behavior |
|----------|----------|
| No interests | Hide entire section |
| 1-3 interests | Centered small group |
| Interest without image | Show first letter in circle |
| Interest without name | Skip that interest |
| 15+ interests | Wrap to multiple rows |

```css
/* Few interests centering */
.interests-cloud--few {
  max-width: 400px;
}

/* Interest letter fallback */
.interest-image--letter {
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--warmLight);
  color: var(--warm);
  font-family: var(--serif);
  font-size: 24px;
  font-weight: 500;
}
```

### Inspirations Grid

| Scenario | Behavior |
|----------|----------|
| No inspirations | Hide entire section |
| 1 inspiration | Single card, centered |
| Missing photo | Show initials in circle |
| No URL | Card not clickable, no blue hover |
| No note | Note line hidden |

```php
<?php
$has_link = !empty($url);
$tag = $has_link ? 'a' : 'div';
$href = $has_link ? 'href="' . esc_url($url) . '"' : '';
?>
<<?php echo $tag; ?> class="inspiration-card <?php echo !$has_link ? 'inspiration-card--static' : ''; ?>" <?php echo $href; ?>>
```

```css
/* Non-link card */
.inspiration-card--static {
  cursor: default;
}

.inspiration-card--static:hover {
  border-color: transparent;
  background-color: transparent;
}

/* Still allow photo color on hover */
.inspiration-card--static:hover .inspiration-photo img {
  filter: var(--img-color);
}
```

### Stats Section

| Scenario | Behavior |
|----------|----------|
| No stats | Hide entire section |
| 1 stat only | Single stat, centered |
| 2 stats | Two-column layout |
| Value is 0 | Display 0, still valid |
| No label | Hide that stat |

---

## SECTION VISIBILITY

When a section is toggled OFF in Customizer:

```php
<?php if (get_theme_mod('kunaal_about_hero_show', true)) : ?>
  <!-- Hero section -->
<?php endif; ?>

<?php if (get_theme_mod('kunaal_about_bio_show', true)) : ?>
  <!-- Bio section -->
<?php endif; ?>

<!-- And so on... -->
```

### Layout Continuity

Hidden sections must not break layout flow:

```css
/* Each section is self-contained */
.about-section {
  /* No margin collapse issues */
  overflow: hidden;
}

/* Adjacent section margins */
.about-section + .about-section {
  margin-top: 0; /* Overlap handles spacing */
}
```

---

## ERROR HANDLING

### Image Load Errors

```javascript
class ImageErrorHandler {
  constructor() {
    this.images = document.querySelectorAll('.about-page img');
    this.init();
  }
  
  init() {
    this.images.forEach(img => {
      img.addEventListener('error', () => this.handleError(img));
      
      // Check if already failed
      if (img.complete && img.naturalWidth === 0) {
        this.handleError(img);
      }
    });
  }
  
  handleError(img) {
    const container = img.closest('[data-image-container]') || img.parentElement;
    
    // Hide broken image
    img.style.display = 'none';
    
    // Add fallback class
    container.classList.add('image--fallback');
    
    // Log for debugging
    console.warn('Image failed to load:', img.src);
  }
}
```

### JavaScript Errors

```javascript
// Wrap all initializations in try-catch
try {
  new HeroCollage(document.querySelector('.about-hero'));
} catch (e) {
  console.error('Hero initialization failed:', e);
  // Hero still displays, just without animations
}

try {
  new WorldMap(document.querySelector('.about-map'));
} catch (e) {
  console.error('Map initialization failed:', e);
  // Map still displays, just without interactions
}
```

### Missing Dependencies

```javascript
// Check for required libraries
if (typeof IntersectionObserver === 'undefined') {
  // Fallback: show all content immediately
  document.querySelectorAll('[data-reveal]').forEach(el => {
    el.classList.add('is-revealed');
  });
}
```

---

## MINIMUM CONTENT SCENARIOS

### Absolute Minimum (Page Must Not Break)

| Requirement | Minimum |
|-------------|---------|
| Name | Site title fallback |
| Photos | 0 (graceful degradation) |
| Bio | 0 (section hidden) |
| Books | 0 (section hidden) |
| Map countries | 0 (section hidden) |
| Interests | 0 (section hidden) |
| Inspirations | 0 (section hidden) |
| Stats | 0 (section hidden) |

### Recommended Minimum (Good Experience)

| Section | Recommended |
|---------|-------------|
| Hero photos | At least 2 |
| Bio | 100+ characters |
| Books | 3-4 |
| Countries | 5+ visited |
| Interests | 6-8 |
| Inspirations | 3-4 |
| Stats | 2-3 |
| Atmospheric images | 2-3 |

---

## MAXIMUM CONTENT SCENARIOS

### Hero Section

| Field | Max | Overflow Handling |
|-------|-----|-------------------|
| Photos | 4 | Hard limit in Customizer |
| Name | 50 chars | Truncate with ellipsis |
| Tagline | 100 chars | Wrap naturally |
| Annotation | 40 chars | Hard limit in Customizer |

### Bookshelf

| Field | Max | Overflow Handling |
|-------|-----|-------------------|
| Books | 8 | Hard limit |
| Title | 100 chars | Truncate in tooltip |
| Author | 50 chars | Truncate in tooltip |

### Map

| Field | Max | Overflow Handling |
|-------|-----|-------------------|
| Visited countries | Unlimited | Performance monitored |
| Lived countries | 10 | Practical limit |
| Stories | 10 | Hard limit |
| Story text | 200 chars | Truncate with ellipsis |

### Interests

| Field | Max | Overflow Handling |
|-------|-----|-------------------|
| Interests | 20 | Hard limit |
| Name | 30 chars | Truncate |

### Inspirations

| Field | Max | Overflow Handling |
|-------|-----|-------------------|
| People | 8 | Hard limit |
| Name | 40 chars | Truncate |
| Role | 50 chars | Truncate |
| Note | 100 chars | Truncate |

### Atmospheric Images

| Field | Max | Overflow Handling |
|-------|-----|-------------------|
| Images | 12 | Hard limit |
| Quote | 200 chars | Truncate |
| Caption | 50 chars | Truncate |

---

## USER STORIES

### Empty States

**US-EDGE-001: Hero Without Photos**
- [ ] Shows name/tagline centered
- [ ] No broken layout
- [ ] Minimal style applied

**US-EDGE-002: Empty Bio**
- [ ] Section hidden completely
- [ ] Flow continues to next section
- [ ] No gap or placeholder

**US-EDGE-003: No Books**
- [ ] Bookshelf hidden
- [ ] No empty shelf visible
- [ ] Smooth transition

**US-EDGE-004: No Map Countries**
- [ ] Map section hidden
- [ ] No empty map SVG
- [ ] Content flows

**US-EDGE-005: No Interests**
- [ ] Section hidden
- [ ] No empty cloud
- [ ] Clean layout

**US-EDGE-006: No Inspirations**
- [ ] Section hidden
- [ ] No empty grid
- [ ] Proper spacing

### Fallbacks

**US-EDGE-007: Missing Book Cover**
- [ ] Gradient placeholder
- [ ] Title/author still show
- [ ] No broken image icon

**US-EDGE-008: Missing Interest Image**
- [ ] Letter initial in circle
- [ ] First letter of name
- [ ] Styled appropriately

**US-EDGE-009: Missing Inspiration Photo**
- [ ] Initials in circle
- [ ] First + last initial
- [ ] Card still functional

**US-EDGE-010: Image Load Error**
- [ ] Fallback displayed
- [ ] Error logged
- [ ] No visual break

### Toggles

**US-EDGE-011: Section Toggle Off**
- [ ] Section not rendered
- [ ] No empty space
- [ ] Other sections adjust

**US-EDGE-012: Multiple Sections Off**
- [ ] Page still cohesive
- [ ] Flow maintained
- [ ] Minimum viable page

### Content Limits

**US-EDGE-013: Very Long Bio**
- [ ] No truncation
- [ ] Natural paragraph flow
- [ ] Readable layout

**US-EDGE-014: Single Interest**
- [ ] Centered display
- [ ] Still feels intentional
- [ ] Section not awkward

**US-EDGE-015: Maximum Books**
- [ ] 8 books fit
- [ ] Shelf width adjusts
- [ ] Tooltips work

**US-EDGE-016: Many Countries**
- [ ] Map performs well
- [ ] All tooltips work
- [ ] No slowdown

### Error Recovery

**US-EDGE-017: JavaScript Disabled**
- [ ] Content visible
- [ ] No animations (acceptable)
- [ ] Links work

**US-EDGE-018: Slow Network**
- [ ] Progressive loading
- [ ] Placeholders shown
- [ ] Content appears gradually

---

## FINAL CHECKLIST

### Empty States
- [ ] Hero: centered name fallback
- [ ] Bio: hide if empty
- [ ] Bookshelf: hide if no books
- [ ] Map: hide if no countries
- [ ] Interests: hide if none
- [ ] Inspirations: hide if none
- [ ] Stats: hide if none

### Fallbacks
- [ ] Image placeholders (gradient)
- [ ] Letter initials for avatars
- [ ] Site title for missing name
- [ ] Graceful degradation

### Error Handling
- [ ] Image error listeners
- [ ] Try-catch on JS init
- [ ] Console warnings (dev)
- [ ] No user-facing errors

### Content Limits
- [ ] Hard limits in Customizer
- [ ] Truncation with ellipsis
- [ ] Performance tested at max

### Toggle Behavior
- [ ] Sections hide cleanly
- [ ] No layout shift
- [ ] Flow maintained


