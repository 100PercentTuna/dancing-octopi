# 09 - ACCESSIBILITY
## Complete Specification

---

## OVERVIEW

The About page must be **fully accessible** to all users, including those who:
- Use screen readers
- Navigate with keyboard only
- Have motor impairments
- Are color blind
- Experience motion sensitivity
- Use assistive technologies

**Target Compliance:** WCAG 2.1 Level AA

---

## SKIP LINKS

Provide a skip link at the very top of the page.

```html
<a href="#about-content" class="skip-link">Skip to content</a>

<!-- Later in the page -->
<main id="about-content" tabindex="-1">
  <!-- Page content -->
</main>
```

```css
.skip-link {
  position: absolute;
  top: -100%;
  left: 0;
  padding: var(--space-2) var(--space-4);
  background: var(--ink);
  color: white;
  font-family: var(--sans);
  font-size: 14px;
  z-index: 9999;
  transition: top 200ms ease;
}

.skip-link:focus {
  top: 0;
}
```

---

## ARIA LABELS & ROLES

### Page Structure

```html
<main id="about-content" role="main" aria-label="About page content">
  
  <section class="about-hero" aria-label="Introduction">
    <!-- Hero content -->
  </section>
  
  <section class="about-bio" aria-label="Biography">
    <!-- Bio content -->
  </section>
  
  <section class="about-bookshelf" aria-label="Currently reading">
    <!-- Bookshelf content -->
  </section>
  
  <section class="about-map" aria-label="Places visited and lived">
    <!-- Map content -->
  </section>
  
  <section class="about-interests" aria-label="Things that fascinate me">
    <!-- Interests content -->
  </section>
  
  <section class="about-inspirations" aria-label="People who inspire me">
    <!-- Inspirations content -->
  </section>
  
  <section class="about-stats" aria-label="Statistics">
    <!-- Stats content -->
  </section>
  
  <section class="about-connect" aria-label="Connect">
    <!-- Connect content -->
  </section>
  
</main>
```

### Hero Images

```html
<div class="hero-photo" role="img" aria-label="Photo of [Name] in [context]">
  <img src="..." alt="" aria-hidden="true">
  <!-- Decorative in context of aria-label on parent -->
</div>

<!-- Or if single photo: -->
<img src="..." alt="Portrait of Kunaal Wadhwa" class="hero-photo">
```

### Decorative Elements

```html
<!-- Annotation is decorative/supplementary -->
<span class="hero-annotation" aria-hidden="true">still figuring it out</span>

<!-- Scroll hint is decorative -->
<div class="hero-scroll-hint" aria-hidden="true">
  <span class="hero-scroll-hint__text">scroll</span>
  <span class="hero-scroll-hint__line"></span>
</div>
```

### Interactive Elements

```html
<!-- Book with tooltip -->
<div class="book" 
     role="button" 
     tabindex="0" 
     aria-haspopup="true"
     aria-expanded="false"
     aria-label="View details for The Great Gatsby by F. Scott Fitzgerald">
  <img src="book-cover.jpg" alt="">
  <div class="book-tooltip" role="tooltip" id="book-1-tooltip">
    <!-- Tooltip content -->
  </div>
</div>

<!-- Map country -->
<path class="map-country map-country--lived" 
      data-country="IN"
      role="button"
      tabindex="0"
      aria-haspopup="true"
      aria-expanded="false"
      aria-label="India - Lived here, click for story">
</path>

<!-- Map tooltip -->
<div class="map-tooltip" 
     role="dialog" 
     aria-label="Story about India"
     aria-modal="true">
  <button class="map-tooltip__close" aria-label="Close">
    <svg aria-hidden="true"><!-- X icon --></svg>
  </button>
  <!-- Content -->
</div>
```

---

## KEYBOARD NAVIGATION

### Focus Order

1. Skip link (visible on focus)
2. Navigation (header)
3. Hero content (name is heading)
4. Each section in document order
5. Interactive elements within sections
6. Footer

### Tab Stops

| Element | Focusable? | Notes |
|---------|------------|-------|
| Photos | No | Decorative |
| Name heading | No | Not interactive |
| Books | Yes | Shows tooltip |
| Map countries | Yes | Shows story |
| Interests | No | Not links |
| Inspirations (with URL) | Yes | Links |
| Inspirations (no URL) | No | Static |
| Stats | No | Not interactive |
| Connect links | Yes | External links |

### Keyboard Handlers

```javascript
// Book keyboard support
document.querySelectorAll('.book').forEach(book => {
  book.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      toggleBookTooltip(book);
    }
    if (e.key === 'Escape') {
      closeBookTooltip(book);
    }
  });
});

// Map country keyboard support
document.querySelectorAll('.map-country[tabindex="0"]').forEach(country => {
  country.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      showMapTooltip(country);
    }
  });
});

// Tooltip close on Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeAllTooltips();
  }
});
```

### Focus Trap in Tooltips

```javascript
class FocusTrap {
  constructor(element) {
    this.element = element;
    this.focusableElements = element.querySelectorAll(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    this.firstFocusable = this.focusableElements[0];
    this.lastFocusable = this.focusableElements[this.focusableElements.length - 1];
  }
  
  activate() {
    this.firstFocusable?.focus();
    this.element.addEventListener('keydown', this.handleKeydown.bind(this));
  }
  
  deactivate() {
    this.element.removeEventListener('keydown', this.handleKeydown.bind(this));
  }
  
  handleKeydown(e) {
    if (e.key !== 'Tab') return;
    
    if (e.shiftKey) {
      if (document.activeElement === this.firstFocusable) {
        e.preventDefault();
        this.lastFocusable.focus();
      }
    } else {
      if (document.activeElement === this.lastFocusable) {
        e.preventDefault();
        this.firstFocusable.focus();
      }
    }
  }
}
```

---

## FOCUS MANAGEMENT

### Visible Focus States

```css
/* Global focus style */
:focus-visible {
  outline: 2px solid var(--blue);
  outline-offset: 2px;
}

/* Remove default focus, use focus-visible */
:focus:not(:focus-visible) {
  outline: none;
}

/* Specific element focus */
.book:focus-visible {
  outline: 2px solid var(--blue);
  outline-offset: 4px;
  border-radius: 4px;
}

.map-country:focus-visible {
  outline: 2px solid var(--blue);
  outline-offset: 0;
}

.inspiration-card:focus-visible {
  outline: 2px solid var(--blue);
  outline-offset: 2px;
  border-radius: 8px;
}

/* High contrast mode */
@media (prefers-contrast: high) {
  :focus-visible {
    outline: 3px solid currentColor;
    outline-offset: 3px;
  }
}
```

### Focus Return

```javascript
// When tooltip closes, return focus to trigger
class TooltipManager {
  constructor() {
    this.triggerElement = null;
  }
  
  open(trigger, tooltip) {
    this.triggerElement = trigger;
    tooltip.classList.add('is-visible');
    tooltip.querySelector('[tabindex], button, a')?.focus();
  }
  
  close(tooltip) {
    tooltip.classList.remove('is-visible');
    this.triggerElement?.focus();
    this.triggerElement = null;
  }
}
```

---

## SCREEN READER SUPPORT

### Live Regions

```html
<!-- Announce tooltip content -->
<div class="sr-announcer" aria-live="polite" aria-atomic="true">
  <!-- JS will inject announcements here -->
</div>
```

```javascript
function announce(message) {
  const announcer = document.querySelector('.sr-announcer');
  announcer.textContent = '';
  // Small delay to ensure announcement
  setTimeout(() => {
    announcer.textContent = message;
  }, 100);
}

// Example usage
function showMapTooltip(country) {
  const name = getCountryName(country.dataset.country);
  const story = getCountryStory(country.dataset.country);
  announce(`${name}. ${story.years}. ${story.text}`);
}
```

### Heading Structure

```html
<main id="about-content">
  <!-- h1 is page title (in header typically) -->
  
  <section class="about-hero">
    <h2 class="hero-name">Kunaal Wadhwa</h2>
    <!-- h2 for the person's name -->
  </section>
  
  <section class="about-bio">
    <h2 class="sr-only">About</h2>
    <!-- Visible label is decorative, real h2 for structure -->
  </section>
  
  <section class="about-bookshelf">
    <h2 class="sr-only">Currently Reading</h2>
  </section>
  
  <!-- And so on... -->
</main>
```

### Screen Reader Only Content

```css
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
```

---

## COLOR CONTRAST

### Minimum Ratios (WCAG AA)

| Text Type | Ratio Required | Our Colors |
|-----------|----------------|------------|
| Body text (19px) | 4.5:1 | --ink on --bg = 14:1 ✓ |
| Large text (24px+) | 3:1 | --ink on --bg = 14:1 ✓ |
| Muted text | 4.5:1 | --muted on --bg = 8:1 ✓ |
| Links | 4.5:1 | --blue on --bg = 5.2:1 ✓ |
| Focus ring | 3:1 | --blue on --bg = 5.2:1 ✓ |

### Color-Blind Safe

- Map colors use **value** (light/dark) not just hue
- Terracotta and brown distinguishable
- Links have underline (not color only)
- Focus uses visible outline (not color only)

```css
/* Links always have underline or other indicator */
.about-page a {
  text-decoration: underline;
  text-underline-offset: 2px;
}

/* Or use outline on hover */
.inspiration-card {
  border: 1px solid transparent;
}

.inspiration-card:hover {
  border-color: var(--blue);
}
```

---

## REDUCED MOTION

### CSS

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
  
  /* Parallax disabled */
  [data-parallax] {
    transform: none !important;
  }
  
  /* All reveals immediate */
  [data-reveal] {
    opacity: 1 !important;
    transform: none !important;
  }
  
  /* Map pulse disabled */
  .map-current-marker__pulse {
    display: none;
  }
  
  /* Images in full color */
  .grayscale-image {
    filter: none !important;
  }
}
```

### JavaScript Check

```javascript
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

function initWithMotionCheck() {
  if (prefersReducedMotion.matches) {
    // Show all content immediately
    document.querySelectorAll('[data-reveal]').forEach(el => {
      el.classList.add('is-revealed');
    });
    
    // Color all images
    document.querySelectorAll('.grayscale-image').forEach(img => {
      img.classList.add('is-colored');
    });
    
    // Skip parallax initialization
    return;
  }
  
  // Normal animation initialization
  new ScrollReveal();
  new Parallax();
}

// Listen for preference changes
prefersReducedMotion.addEventListener('change', (e) => {
  if (e.matches) {
    // User enabled reduced motion
    document.body.classList.add('reduced-motion');
  } else {
    document.body.classList.remove('reduced-motion');
  }
});
```

---

## TOUCH TARGETS

### Minimum Size

All interactive elements must be at least **44x44 pixels**.

```css
/* Book touch target */
.book {
  min-width: 44px;
  min-height: 44px;
}

/* Map tooltip close button */
.map-tooltip__close {
  width: 44px;
  height: 44px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Inspiration card */
.inspiration-card {
  min-height: 44px;
  padding: var(--space-4); /* Ensures size */
}

/* Interest item */
.interest-item {
  min-width: 44px;
  min-height: 44px;
}
```

### Touch Target Spacing

```css
/* Ensure targets don't overlap */
.inspirations-grid {
  gap: var(--space-4); /* 32px minimum */
}

.interests-cloud {
  gap: var(--space-4) var(--space-5);
}

.bookshelf {
  gap: var(--space-2); /* Books can be closer, they're larger */
}
```

---

## ALT TEXT REQUIREMENTS

### Hero Photos

```php
<?php
// Alt text from media library
$photo_id = get_theme_mod('kunaal_about_hero_photo_1');
$alt = get_post_meta($photo_id, '_wp_attachment_image_alt', true);
$alt = $alt ?: 'Photo of ' . get_theme_mod('kunaal_about_hero_name', get_bloginfo('name'));
?>
<img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($alt); ?>">
```

### Atmospheric Images

```php
<?php
// Background/atmospheric images are decorative
?>
<div class="atmo-strip" aria-hidden="true">
  <img src="<?php echo esc_url($src); ?>" alt="">
</div>
```

### Book Covers

```php
<img src="<?php echo esc_url($cover_src); ?>" 
     alt="Cover of <?php echo esc_attr($title); ?> by <?php echo esc_attr($author); ?>">
```

### Interest Images

```php
<img src="<?php echo esc_url($interest_img); ?>" 
     alt="<?php echo esc_attr($interest_name); ?>">
```

### Inspiration Photos

```php
<img src="<?php echo esc_url($photo_src); ?>" 
     alt="Photo of <?php echo esc_attr($person_name); ?>">
```

---

## USER STORIES

**US-A11Y-001: Skip Link**
- [ ] Skip link present
- [ ] Visible on focus
- [ ] Jumps to main content

**US-A11Y-002: ARIA Labels**
- [ ] All sections labeled
- [ ] Interactive elements described
- [ ] Decorative elements hidden

**US-A11Y-003: Keyboard Navigation**
- [ ] Tab through all interactive
- [ ] Enter/Space activate
- [ ] Escape closes dialogs

**US-A11Y-004: Focus Visible**
- [ ] Clear focus indicator
- [ ] 2px blue outline
- [ ] Offset for visibility

**US-A11Y-005: Focus Management**
- [ ] Focus returns after close
- [ ] Focus trap in dialogs
- [ ] Logical focus order

**US-A11Y-006: Screen Reader**
- [ ] Heading structure
- [ ] Live announcements
- [ ] sr-only content

**US-A11Y-007: Color Contrast**
- [ ] 4.5:1 for body text
- [ ] 3:1 for large text
- [ ] Links distinguishable

**US-A11Y-008: Color-Blind Safe**
- [ ] Not color-only info
- [ ] Value differences
- [ ] Underlines on links

**US-A11Y-009: Reduced Motion**
- [ ] CSS media query
- [ ] JavaScript check
- [ ] Content visible immediately

**US-A11Y-010: Touch Targets**
- [ ] 44x44px minimum
- [ ] Adequate spacing
- [ ] No overlap

**US-A11Y-011: Alt Text**
- [ ] Meaningful for content
- [ ] Empty for decorative
- [ ] Dynamic from admin

**US-A11Y-012: Tooltip Accessibility**
- [ ] Role="tooltip"
- [ ] aria-haspopup
- [ ] Close button focusable

**US-A11Y-013: Map Accessibility**
- [ ] Countries focusable
- [ ] Aria labels per country
- [ ] Story announced

**US-A11Y-014: Print Accessibility**
- [ ] Content visible
- [ ] No interactive elements
- [ ] Readable layout

**US-A11Y-015: Zoom Support**
- [ ] Works at 200% zoom
- [ ] No horizontal scroll
- [ ] Text reflows

**US-A11Y-016: Text Resize**
- [ ] Works with browser text resize
- [ ] Layout adjusts
- [ ] No content clipping

---

## FINAL CHECKLIST

### Structure
- [ ] Skip link to main content
- [ ] Semantic HTML structure
- [ ] Heading hierarchy (h1-h6)
- [ ] Landmark regions

### ARIA
- [ ] Labels on sections
- [ ] Roles on interactive
- [ ] States (expanded, selected)
- [ ] Hidden on decorative

### Keyboard
- [ ] All interactive focusable
- [ ] Enter/Space activation
- [ ] Escape closes overlays
- [ ] Tab order logical

### Focus
- [ ] Visible focus rings
- [ ] Focus return on close
- [ ] Focus trap in dialogs
- [ ] No focus loss

### Screen Reader
- [ ] sr-only headings where needed
- [ ] Live regions for updates
- [ ] Meaningful alt text
- [ ] Announced state changes

### Visual
- [ ] WCAG AA contrast
- [ ] Not color-only info
- [ ] 200% zoom support
- [ ] Text resize works

### Motion
- [ ] Reduced motion support
- [ ] No auto-playing
- [ ] User-controlled animations

### Touch
- [ ] 44x44px targets
- [ ] Adequate spacing
- [ ] No hover-only content


