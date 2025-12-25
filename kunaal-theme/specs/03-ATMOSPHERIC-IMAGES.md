# 03 - ATMOSPHERIC IMAGES
## Complete Specification

---

## OVERVIEW

Atmospheric images are the **visual bridges** between content sections. They create the "layered exhibition" feel by appearing at different depths and bleeding through the white content panels.

**Four Display Types:**
1. **Strip** — Full-bleed horizontal band
2. **Window** — Content foreground with cutout revealing image
3. **Dual** — Two images side-by-side
4. **Background** — Behind content, parallax movement

---

## TYPE 1: FULL-BLEED STRIP

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   [Content above...]                                                        │
│                                                                             │
├─────────────────────────────────────────────────────────────────────────────┤
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  ATMOSPHERIC IMAGE (full width)  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─│ ← Angled clip
│                                                                             │
│   [Content below...]                                                        │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### CSS Implementation

```css
.atmo-strip {
  position: relative;
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  height: clamp(200px, 30vh, 400px);
  overflow: hidden;
  z-index: 10;
}

.atmo-strip img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  filter: var(--img-grayscale);
  transition: filter 600ms ease-out;
}

.atmo-strip.is-colored img {
  filter: var(--img-color);
}

/* Parallax movement */
.atmo-strip--parallax {
  will-change: transform;
}

/* Clip path variations */
.atmo-strip--angle-bottom {
  clip-path: polygon(0 0, 100% 0, 100% 90%, 0 100%);
}

.atmo-strip--angle-top {
  clip-path: polygon(0 10%, 100% 0, 100% 100%, 0 100%);
}

.atmo-strip--angle-both {
  clip-path: polygon(0 8%, 100% 0, 100% 92%, 0 100%);
}

/* Optional caption */
.atmo-strip__caption {
  position: absolute;
  bottom: var(--space-3);
  right: var(--space-4);
  font-family: var(--mono);
  font-size: 11px;
  color: rgba(255, 255, 255, 0.8);
  text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
  letter-spacing: 0.05em;
}
```

---

## TYPE 2: WINDOW CUTOUT

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   Content text wrapping around the window...                                │
│                                                                             │
│   ████████████████████████████████████████████████████████████████████████  │
│   ██                                                                    ██  │
│   ██                                                                    ██  │
│   ██     ┌───────────────────────────────────────────┐                  ██  │
│   ██     │                                           │                  ██  │
│   ██     │        IMAGE VISIBLE THROUGH              │                  ██  │
│   ██     │            THE WINDOW                     │                  ██  │
│   ██     │                                           │                  ██  │
│   ██     └───────────────────────────────────────────┘                  ██  │
│   ██                                                                    ██  │
│   ██                                                                    ██  │
│   ████████████████████████████████████████████████████████████████████████  │
│                                                                             │
│   More content continues below...                                           │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘

████ = White foreground (var(--bg))
```

### CSS Implementation

```css
.atmo-window {
  position: relative;
  min-height: 400px;
  overflow: hidden;
}

/* Background image */
.atmo-window__image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 10;
}

.atmo-window__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: var(--img-grayscale);
  transition: filter 600ms ease-out;
}

.atmo-window.is-colored .atmo-window__image img {
  filter: var(--img-color);
}

/* Foreground with cutout */
.atmo-window__foreground {
  position: relative;
  z-index: 20;
  background: var(--bg);
  padding: var(--space-10) var(--space-4);
}

/* The window cutout itself */
.atmo-window__cutout {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: clamp(300px, 60%, 700px);
  height: clamp(180px, 25vh, 280px);
  background: transparent;
}

/* Create the window effect using box-shadow */
.atmo-window__mask {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 15;
  pointer-events: none;
}

.atmo-window__mask::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: clamp(300px, 60%, 700px);
  height: clamp(180px, 25vh, 280px);
  box-shadow: 0 0 0 9999px var(--bg);
  border-radius: 4px;
}

/* Angular window shape */
.atmo-window--angular .atmo-window__mask::before {
  clip-path: polygon(2% 0, 100% 0, 98% 100%, 0 100%);
}

/* Content that sits on the foreground */
.atmo-window__content {
  position: relative;
  z-index: 25;
  max-width: var(--prose);
  margin: 0 auto;
}
```

---

## TYPE 3: DUAL IMAGES

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   ┌────────────────────────────┐ ┌────────────────────────────┐             │
│   │                            │ │                            │             │
│   │                            │ │                            │             │
│   │        IMAGE A             │ │        IMAGE B             │             │
│   │        (left)              │ │        (right)             │             │
│   │                            │ │                            │             │
│   │                            │ │                            │             │
│   └────────────────────────────┘ └────────────────────────────┘             │
│                                                                             │
│   Images overlap slightly in center, different heights                      │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### CSS Implementation

```css
.atmo-dual {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: var(--space-4);
  width: 100vw;
  margin-left: calc(-50vw + 50%);
  padding: 0 var(--space-4);
  position: relative;
  z-index: 10;
}

.atmo-dual__item {
  position: relative;
  overflow: hidden;
  border-radius: 4px;
}

.atmo-dual__item:first-child {
  height: clamp(200px, 35vh, 350px);
  transform: translateY(20px);
}

.atmo-dual__item:last-child {
  height: clamp(250px, 40vh, 400px);
  transform: translateY(-20px);
}

.atmo-dual__item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: var(--img-grayscale);
  transition: filter 600ms ease-out;
}

.atmo-dual.is-colored img {
  filter: var(--img-color);
}

/* Overlap effect */
.atmo-dual--overlap {
  gap: calc(var(--space-4) * -1);
}

.atmo-dual--overlap .atmo-dual__item:first-child {
  z-index: 11;
  margin-right: -40px;
}

.atmo-dual--overlap .atmo-dual__item:last-child {
  z-index: 10;
  margin-left: -40px;
}
```

---

## TYPE 4: BACKGROUND LAYER

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
│░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
│░░░  ┌─────────────────────────────────────────────────────────────┐  ░░░░░░│
│░░░  │                                                             │  ░░░░░░│
│░░░  │                    CONTENT PANEL                            │  ░░░░░░│
│░░░  │                    (white background)                       │  ░░░░░░│
│░░░  │                                                             │  ░░░░░░│
│░░░  │         Text content sits on white, image peeks             │  ░░░░░░│
│░░░  │         around the edges with parallax movement             │  ░░░░░░│
│░░░  │                                                             │  ░░░░░░│
│░░░  └─────────────────────────────────────────────────────────────┘  ░░░░░░│
│░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
│░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░│
└─────────────────────────────────────────────────────────────────────────────┘

░░░ = Background image (visible around content panel edges)
```

### CSS Implementation

```css
.atmo-background {
  position: relative;
  padding: var(--space-15) 0;
  overflow: hidden;
}

.atmo-background__image {
  position: absolute;
  top: -50px;
  left: 0;
  width: 100%;
  height: calc(100% + 100px);
  z-index: 0;
  will-change: transform;
}

.atmo-background__image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: var(--img-grayscale);
  transition: filter 600ms ease-out;
}

.atmo-background.is-colored .atmo-background__image img {
  filter: var(--img-color);
}

.atmo-background__content {
  position: relative;
  z-index: 10;
  max-width: var(--wide);
  margin: 0 auto;
  padding: var(--space-10) var(--space-6);
  background: var(--bg);
  border-radius: 4px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
}
```

---

## QUOTE OVERLAYS

Some atmospheric images can have quote overlays.

```css
.atmo-quote {
  position: absolute;
  bottom: var(--space-8);
  left: var(--space-6);
  max-width: 500px;
  z-index: 15;
}

.atmo-quote__text {
  font-family: var(--serif);
  font-size: clamp(18px, 2.5vw, 24px);
  font-style: italic;
  color: white;
  line-height: 1.5;
  text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
}

.atmo-quote__attr {
  font-family: var(--mono);
  font-size: 11px;
  color: rgba(255, 255, 255, 0.8);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-top: var(--space-2);
  text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
}

/* Dark overlay for readability */
.atmo-quote::before {
  content: '';
  position: absolute;
  bottom: -20px;
  left: -20px;
  width: calc(100% + 40px);
  height: calc(100% + 40px);
  background: linear-gradient(
    to top,
    rgba(0, 0, 0, 0.6) 0%,
    transparent 100%
  );
  z-index: -1;
  border-radius: 4px;
}
```

---

## JAVASCRIPT IMPLEMENTATION

```javascript
class AtmosphericImages {
  constructor() {
    this.images = document.querySelectorAll('[data-atmo]');
    this.parallaxImages = document.querySelectorAll('[data-atmo-parallax]');
    
    this.init();
  }
  
  init() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.images.forEach(img => img.classList.add('is-colored'));
      return;
    }
    
    this.setupColorTransition();
    this.setupParallax();
  }
  
  setupColorTransition() {
    const options = {
      root: null,
      rootMargin: '0px',
      threshold: 0.3
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-colored');
        }
      });
    }, options);
    
    this.images.forEach(img => observer.observe(img));
  }
  
  setupParallax() {
    if (this.parallaxImages.length === 0) return;
    
    let ticking = false;
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          this.updateParallax();
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }
  
  updateParallax() {
    const scrollY = window.scrollY;
    const viewportCenter = window.innerHeight / 2;
    
    this.parallaxImages.forEach(container => {
      const rect = container.getBoundingClientRect();
      const elementCenter = rect.top + rect.height / 2;
      const distanceFromCenter = elementCenter - viewportCenter;
      
      const speed = parseFloat(container.dataset.atmoParallax) || 0.2;
      const offset = distanceFromCenter * speed * -1;
      
      const img = container.querySelector('img, .atmo-background__image');
      if (img) {
        img.style.transform = `translateY(${offset}px)`;
      }
    });
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  new AtmosphericImages();
});
```

---

## POSITION OPTIONS

Images can be placed at specific points in the page flow:

| Position Key | Description |
|-------------|-------------|
| `after_hero` | Between hero and bio section |
| `mid_bio` | Within bio text (window type works well) |
| `after_bio` | Between bio/bookshelf and map |
| `after_map` | Between map and interests |
| `after_interests` | Between interests and inspirations |
| `after_inspirations` | Between inspirations and stats |
| `before_closing` | Final atmospheric before footer |
| `auto` | System places based on content length |

---

## USER STORIES

### Strip Type

**US-ATMO-001: Full-Width Strip Display**
- [ ] 100vw width, edge to edge
- [ ] Height: clamp(200px, 30vh, 400px)
- [ ] object-fit: cover

**US-ATMO-002: Strip Clip Paths**
- [ ] Straight (default)
- [ ] Angle bottom
- [ ] Angle top
- [ ] Angle both

**US-ATMO-003: Strip Parallax**
- [ ] Speed: 0.2 (slow)
- [ ] Smooth movement
- [ ] will-change: transform

**US-ATMO-004: Strip Caption**
- [ ] Mono font, 11px
- [ ] Bottom-right position
- [ ] White text with shadow

### Window Type

**US-ATMO-005: Window Cutout Display**
- [ ] Image behind, panel in front
- [ ] Cutout reveals image
- [ ] Width: clamp(300px, 60%, 700px)

**US-ATMO-006: Window Mask Effect**
- [ ] Box-shadow creates mask
- [ ] Clean edge on cutout
- [ ] Border-radius: 4px

**US-ATMO-007: Window Content**
- [ ] Content on foreground layer
- [ ] z-index above image
- [ ] Readable typography

**US-ATMO-008: Angular Window**
- [ ] Optional angular cutout
- [ ] Polygon clip-path
- [ ] Matches design language

### Dual Type

**US-ATMO-009: Dual Image Grid**
- [ ] Two-column grid
- [ ] Gap: var(--space-4)
- [ ] Different heights

**US-ATMO-010: Dual Offset**
- [ ] Left: translateY(20px)
- [ ] Right: translateY(-20px)
- [ ] Visual interest

**US-ATMO-011: Dual Overlap**
- [ ] Optional overlap mode
- [ ] Negative margins
- [ ] Z-index layering

**US-ATMO-012: Dual Responsive**
- [ ] Stack on mobile
- [ ] Full width each
- [ ] Maintain aspect ratios

### Background Type

**US-ATMO-013: Background Layer**
- [ ] Fixed behind content
- [ ] Extends beyond content bounds
- [ ] Height: content + 100px

**US-ATMO-014: Background Parallax**
- [ ] Slowest speed (0.1)
- [ ] Creates depth
- [ ] Peeks around panel edges

**US-ATMO-015: Background Content Panel**
- [ ] White background
- [ ] Centered, max-width
- [ ] Box shadow for lift

**US-ATMO-016: Background Visibility**
- [ ] Visible at edges
- [ ] Creates frame effect
- [ ] Adjusts with scroll

### Quote Overlays

**US-ATMO-017: Quote Display**
- [ ] Positioned bottom-left
- [ ] Max-width: 500px
- [ ] Text shadow for readability

**US-ATMO-018: Quote Typography**
- [ ] Serif, italic
- [ ] Size: clamp(18px, 2.5vw, 24px)
- [ ] White color

**US-ATMO-019: Quote Attribution**
- [ ] Mono, 11px, uppercase
- [ ] Slightly transparent
- [ ] Below quote text

**US-ATMO-020: Quote Background**
- [ ] Gradient overlay
- [ ] Bottom to top fade
- [ ] Ensures readability

### Color Transitions

**US-ATMO-021: Grayscale Default**
- [ ] grayscale(100%) sepia(10%)
- [ ] Warm grayscale
- [ ] Consistent across types

**US-ATMO-022: Color on Scroll**
- [ ] Transition at 30% visible
- [ ] 600ms ease-out
- [ ] Smooth transition

**US-ATMO-023: Color Persistence**
- [ ] Once colored, stays colored
- [ ] No re-grayscale on scroll back
- [ ] Single transition

### General

**US-ATMO-024: Image Sizing**
- [ ] object-fit: cover always
- [ ] Responsive heights
- [ ] No distortion

**US-ATMO-025: Z-Index Layering**
- [ ] Strip: z-index 10
- [ ] Window image: z-index 10
- [ ] Window foreground: z-index 20
- [ ] Quote: z-index 15

**US-ATMO-026: Admin Positions**
- [ ] 8 position options
- [ ] Auto-place option
- [ ] Clear in Customizer

**US-ATMO-027: Admin Type Selection**
- [ ] 4 type options + hidden
- [ ] Dropdown select
- [ ] Live preview

**US-ATMO-028: Performance**
- [ ] Lazy loading
- [ ] will-change for parallax
- [ ] RequestAnimationFrame

**US-ATMO-029: Reduced Motion**
- [ ] Skip parallax
- [ ] Immediate color
- [ ] Static display

**US-ATMO-030: Mobile Adaptation**
- [ ] Reduced heights
- [ ] Simpler layouts
- [ ] No parallax

**US-ATMO-031: Print Styles**
- [ ] Full color
- [ ] Static position
- [ ] Visible in print

**US-ATMO-032: Empty State**
- [ ] Hidden if no image
- [ ] No broken layout
- [ ] Smooth flow continues

---

## CUSTOMIZER FIELDS

```php
// Already defined in 11-ADMIN-CUSTOMIZER.md
// Atmospheric Images section:
// - 12 image slots
// - Each with:
//   - Image upload
//   - Type (strip/window/dual/background/hidden)
//   - Position (after_hero, mid_bio, etc.)
//   - Clip style (straight/angle_bottom/angle_top/angle_both)
//   - Has quote toggle
//   - Quote text
//   - Quote attribution
//   - Caption
```

---

## EDGE CASES

### E-ATMO-001: No Images Configured
- Skip all atmospheric sections
- Content flows directly
- No empty spaces

### E-ATMO-002: Single Image Only
- Display in first position
- Skip remaining positions
- Maintains rhythm

### E-ATMO-003: Very Wide Image
- object-fit: cover handles
- Center positioning
- No distortion

### E-ATMO-004: Very Tall Image
- Constrain to max-height
- Crop as needed
- Maintain aspect in window

### E-ATMO-005: Quote Without Image
- Don't display quote
- Quote requires image context

### E-ATMO-006: All Hidden Type
- Page still works
- No atmospheric sections
- Content-only layout

---

## FINAL CHECKLIST

### Strip Type
- [ ] Full width (100vw)
- [ ] Height clamp (200-400px)
- [ ] 4 clip-path options
- [ ] Optional caption
- [ ] Parallax support

### Window Type
- [ ] Image behind panel
- [ ] Box-shadow mask
- [ ] Cutout dimensions
- [ ] Angular option
- [ ] Content overlay

### Dual Type
- [ ] Two-column grid
- [ ] Height offsets
- [ ] Overlap mode
- [ ] Mobile stack

### Background Type
- [ ] Fixed behind
- [ ] Extended height
- [ ] Panel on top
- [ ] Parallax slowest

### Quotes
- [ ] Bottom-left position
- [ ] Serif italic
- [ ] Attribution mono
- [ ] Gradient background

### Transitions
- [ ] Grayscale default
- [ ] Color at 30% visible
- [ ] 600ms duration
- [ ] Reduced motion support


