# 01 - HERO COLLAGE SECTION
## Complete Specification

---

## OVERVIEW

The hero section creates an **immersive first impression** through an asymmetric photo collage that transitions from grayscale to color as the user scrolls. This is NOT a centered circle photo with text — it's a curated gallery arrangement that feels intentional yet organic.

**Key Principles:**
- Asymmetric, organic arrangement (NOT a grid)
- Photos at different depths (z-index layers)
- Grayscale-to-color scroll transition
- Parallax movement at different speeds
- Angular clip-paths for visual interest

---

## VISUAL LAYOUT

### Desktop Layout (1-4 Photos)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│     ┌──────────────┐                                                        │
│     │              │                        ┌─────────────────┐             │
│     │   PHOTO 1    │   ← Primary, largest   │                 │             │
│     │   (main)     │     grayscale→color    │    PHOTO 3      │             │
│     │              │                        │    (accent)     │             │
│     │              │                        └─────────────────┘             │
│     └──────────────┘                              ↑ Parallax: 0.4           │
│           ↑ Parallax: 0.2                                                   │
│                                                                             │
│              ┌─────────────┐                                                │
│              │             │                                                │
│              │  PHOTO 2    │    ← Secondary                                 │
│              │  (overlap)  │      overlaps photo 1                          │
│              │             │                                                │
│              └─────────────┘                                                │
│                   ↑ Parallax: 0.6                                           │
│                                                                             │
│                                        ┌──────────┐                         │
│                                        │ PHOTO 4  │ ← Small accent          │
│                        ┌───────────────┴──────────┘                         │
│                        │                                                    │
│                        │   Kunaal Wadhwa          ← Name: Newsreader, large │
│                        │   writer · analyst       ← Tagline: Sans, muted    │
│                        │                                                    │
│                        │   "still figuring        ← Annotation: Caveat,     │
│                        │    it out"                  brown, rotated -3°     │
│                        │                                                    │
│                        └────────────────────────────────────────────────────│
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Photo Positioning Algorithm

```javascript
// Photo positions based on count
const positions = {
  1: [
    { top: '10vh', left: '8vw', width: '45vw', zIndex: 15 }
  ],
  2: [
    { top: '8vh', left: '5vw', width: '42vw', zIndex: 15 },
    { top: '25vh', left: '30vw', width: '35vw', zIndex: 16 }
  ],
  3: [
    { top: '5vh', left: '5vw', width: '40vw', zIndex: 15 },
    { top: '20vh', left: '28vw', width: '32vw', zIndex: 16 },
    { top: '8vh', right: '5vw', width: '28vw', zIndex: 14 }
  ],
  4: [
    { top: '5vh', left: '5vw', width: '38vw', zIndex: 15 },
    { top: '22vh', left: '25vw', width: '30vw', zIndex: 17 },
    { top: '6vh', right: '8vw', width: '26vw', zIndex: 14 },
    { top: '35vh', right: '5vw', width: '20vw', zIndex: 16 }
  ]
};

// Parallax speeds per photo
const parallaxSpeeds = [0.2, 0.6, 0.4, 0.3];
```

---

## CSS IMPLEMENTATION

```css
/* === HERO CONTAINER === */
.about-hero {
  position: relative;
  min-height: 100vh;
  padding: var(--space-10) var(--space-4);
  overflow: hidden;
}

/* === PHOTO COLLAGE === */
.hero-collage {
  position: relative;
  width: 100%;
  max-width: var(--max);
  margin: 0 auto;
  min-height: 70vh;
}

.hero-photo {
  position: absolute;
  border-radius: 4px;
  overflow: hidden;
  box-shadow: 
    0 8px 32px rgba(0, 0, 0, 0.12),
    0 2px 8px rgba(0, 0, 0, 0.08);
  transition: transform 0.1s linear; /* Smooth parallax */
}

.hero-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: var(--img-grayscale);
  transition: filter 600ms ease-out;
}

/* Grayscale to color on scroll */
.hero-photo.is-colored img {
  filter: var(--img-color);
}

/* === CLIP PATHS FOR ANGULAR EDGES === */
.hero-photo--clip-1 {
  clip-path: polygon(0 0, 100% 0, 100% 95%, 0 100%);
}

.hero-photo--clip-2 {
  clip-path: polygon(0 5%, 100% 0, 100% 100%, 0 100%);
}

.hero-photo--clip-3 {
  clip-path: polygon(0 0, 98% 0, 100% 100%, 2% 100%);
}

/* === PHOTO POSITIONS (Desktop) === */
.hero-photo--1 {
  top: 5vh;
  left: 5vw;
  width: clamp(280px, 40vw, 500px);
  aspect-ratio: 4/5;
  z-index: 15;
}

.hero-photo--2 {
  top: 22vh;
  left: 28vw;
  width: clamp(200px, 32vw, 400px);
  aspect-ratio: 3/4;
  z-index: 17;
}

.hero-photo--3 {
  top: 6vh;
  right: 8vw;
  width: clamp(180px, 26vw, 340px);
  aspect-ratio: 5/4;
  z-index: 14;
}

.hero-photo--4 {
  top: 38vh;
  right: 5vw;
  width: clamp(140px, 20vw, 260px);
  aspect-ratio: 1/1;
  z-index: 16;
}

/* === NAME & TAGLINE === */
.hero-identity {
  position: absolute;
  bottom: 15vh;
  right: 10vw;
  text-align: right;
  z-index: 20;
}

.hero-name {
  font-family: var(--serif);
  font-size: clamp(32px, 5vw, 56px);
  font-weight: 400;
  letter-spacing: -0.01em;
  color: var(--ink);
  margin: 0;
  line-height: 1.1;
}

.hero-tagline {
  font-family: var(--sans);
  font-size: clamp(14px, 1.5vw, 18px);
  color: var(--muted);
  margin-top: var(--space-2);
  letter-spacing: 0.02em;
}

/* Separator dots */
.hero-tagline span::before {
  content: ' · ';
  color: var(--warm);
}

.hero-tagline span:first-child::before {
  content: '';
}

/* === HANDWRITTEN ANNOTATION === */
.hero-annotation {
  position: absolute;
  bottom: 8vh;
  right: 5vw;
  font-family: var(--hand);
  font-size: clamp(16px, 2vw, 22px);
  color: var(--warm);
  transform: rotate(-3deg);
  z-index: 21;
  opacity: 0;
  animation: fadeInAnnotation 800ms ease-out 1.2s forwards;
}

@keyframes fadeInAnnotation {
  from {
    opacity: 0;
    transform: rotate(-3deg) translateY(10px);
  }
  to {
    opacity: 1;
    transform: rotate(-3deg) translateY(0);
  }
}

/* === SCROLL INDICATOR === */
.hero-scroll-hint {
  position: absolute;
  bottom: var(--space-6);
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-2);
  opacity: 0.6;
  transition: opacity 300ms ease;
  z-index: 20;
}

.hero-scroll-hint:hover {
  opacity: 1;
}

.hero-scroll-hint__text {
  font-family: var(--mono);
  font-size: 10px;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: var(--muted2);
}

.hero-scroll-hint__line {
  width: 1px;
  height: 40px;
  background: linear-gradient(
    to bottom,
    var(--warm) 0%,
    transparent 100%
  );
  animation: scrollPulse 2s ease-in-out infinite;
}

@keyframes scrollPulse {
  0%, 100% { opacity: 0.3; transform: scaleY(0.8); }
  50% { opacity: 1; transform: scaleY(1); }
}
```

---

## JAVASCRIPT IMPLEMENTATION

```javascript
class HeroCollage {
  constructor(container) {
    this.container = container;
    this.photos = container.querySelectorAll('.hero-photo');
    this.colorThreshold = window.innerHeight * 0.3; // 30vh
    this.parallaxSpeeds = [0.2, 0.6, 0.4, 0.3];
    
    this.init();
  }
  
  init() {
    // Check for reduced motion preference
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.photos.forEach(photo => photo.classList.add('is-colored'));
      return;
    }
    
    this.bindEvents();
    this.onScroll(); // Initial state
  }
  
  bindEvents() {
    let ticking = false;
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          this.onScroll();
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }
  
  onScroll() {
    const scrollY = window.scrollY;
    
    // Grayscale to color transition
    this.photos.forEach(photo => {
      if (scrollY > this.colorThreshold) {
        photo.classList.add('is-colored');
      } else {
        photo.classList.remove('is-colored');
      }
    });
    
    // Parallax effect
    this.photos.forEach((photo, index) => {
      const speed = this.parallaxSpeeds[index] || 0.3;
      const offset = scrollY * speed;
      photo.style.transform = `translateY(${offset}px)`;
    });
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  const hero = document.querySelector('.about-hero');
  if (hero) {
    new HeroCollage(hero);
  }
});
```

---

## USER STORIES

### US-HERO-001: Photo Collage Display
As a visitor, I want to see an asymmetric photo arrangement.
- [ ] Photos positioned organically, not in a grid
- [ ] Different sizes based on photo count (1-4)
- [ ] Overlapping photos create depth
- [ ] Photos have subtle shadows

### US-HERO-002: Grayscale Initial State
As a visitor, I want photos to start in grayscale.
- [ ] All photos: `grayscale(100%) sepia(10%)`
- [ ] Creates "exhibition" feel
- [ ] Warm-tinted grayscale, not cold

### US-HERO-003: Color Transition on Scroll
As a visitor, I want photos to transition to color as I scroll.
- [ ] Transition triggers at 30vh scroll
- [ ] 600ms ease-out transition
- [ ] All photos transition together
- [ ] Respects reduced motion preference

### US-HERO-004: Parallax Movement
As a visitor, I want photos to move at different speeds.
- [ ] Photo 1: speed 0.2 (slowest)
- [ ] Photo 2: speed 0.6 (fastest)
- [ ] Photo 3: speed 0.4
- [ ] Photo 4: speed 0.3
- [ ] Smooth movement via requestAnimationFrame

### US-HERO-005: Angular Clip Paths
As a visitor, I want photos to have angular edges.
- [ ] Subtle polygon clip-paths
- [ ] Not all photos clipped (variety)
- [ ] Angles create visual interest

### US-HERO-006: Photo Shadows
As a visitor, I want photos to feel elevated.
- [ ] Multi-layer box-shadow
- [ ] Larger spread for higher z-index
- [ ] Subtle, not harsh

### US-HERO-007: Name Display
As a visitor, I want to see the person's name prominently.
- [ ] Font: Newsreader (--serif)
- [ ] Size: clamp(32px, 5vw, 56px)
- [ ] Weight: 400 (regular)
- [ ] Positioned bottom-right
- [ ] Color: --ink

### US-HERO-008: Tagline Display
As a visitor, I want to see a brief descriptor.
- [ ] Font: Inter (--sans)
- [ ] Size: clamp(14px, 1.5vw, 18px)
- [ ] Color: --muted
- [ ] Items separated by brown dots (·)

### US-HERO-009: Handwritten Annotation
As a visitor, I want a personal touch.
- [ ] Font: Caveat (--hand)
- [ ] Color: --warm (brown, NOT blue)
- [ ] Rotated: -3 degrees
- [ ] Fade-in animation after 1.2s
- [ ] Max 40 characters

### US-HERO-010: Scroll Hint
As a visitor, I want to know I can scroll.
- [ ] Centered at bottom
- [ ] Text: "scroll" in mono uppercase
- [ ] Animated vertical line
- [ ] Fades on hover

### US-HERO-011: Single Photo Layout
As a visitor with one photo, I want proper layout.
- [ ] Centered-left positioning
- [ ] Larger size (45vw)
- [ ] Name/tagline positions adjust
- [ ] Still feels balanced

### US-HERO-012: Two Photo Layout
As a visitor with two photos, I want overlap.
- [ ] Second photo overlaps first
- [ ] Creates depth hierarchy
- [ ] Different aspect ratios

### US-HERO-013: Three Photo Layout
As a visitor with three photos, I want spread.
- [ ] Photos distributed across viewport
- [ ] Third photo on right side
- [ ] Varied heights

### US-HERO-014: Four Photo Layout
As a visitor with four photos, I want collage.
- [ ] Maximum coverage
- [ ] Complex layering
- [ ] Fourth photo smallest (accent)

### US-HERO-015: Hero Height
As a visitor, I want the hero to fill the viewport.
- [ ] min-height: 100vh
- [ ] Content doesn't overflow
- [ ] Breathing room maintained

### US-HERO-016: Photo Aspect Ratios
As a visitor, I want natural-looking photos.
- [ ] Photo 1: 4:5 (portrait)
- [ ] Photo 2: 3:4 (portrait)
- [ ] Photo 3: 5:4 (landscape)
- [ ] Photo 4: 1:1 (square)

### US-HERO-017: Z-Index Layering
As a visitor, I want clear depth.
- [ ] Photos: z-index 14-17
- [ ] Identity text: z-index 20
- [ ] Annotation: z-index 21
- [ ] Consistent throughout scroll

### US-HERO-018: Responsive Breakpoints
As a mobile visitor, I want proper layout.
- [ ] Stack photos vertically on mobile
- [ ] Reduce photo count on small screens
- [ ] Maintain parallax on tablet
- [ ] Disable parallax on mobile

### US-HERO-019: Image Loading
As a visitor, I want fast photo loading.
- [ ] Lazy loading for below-fold
- [ ] Proper srcset for responsive
- [ ] Placeholder while loading
- [ ] No layout shift

### US-HERO-020: Animation Performance
As a visitor, I want smooth animations.
- [ ] GPU-accelerated transforms
- [ ] will-change: transform on photos
- [ ] RequestAnimationFrame for scroll
- [ ] Passive scroll listener

### US-HERO-021: Reduced Motion
As a visitor with motion sensitivity, I want comfort.
- [ ] @media (prefers-reduced-motion)
- [ ] Skip parallax entirely
- [ ] Photos start in color
- [ ] No scroll animations

### US-HERO-022: Print Styles
As a visitor printing the page, I want photos visible.
- [ ] Full color in print
- [ ] No parallax offsets
- [ ] Simple stacked layout

### US-HERO-023: Keyboard Focus
As a keyboard user, I want accessibility.
- [ ] Focus visible on any links
- [ ] Skip to content link above
- [ ] Logical tab order

### US-HERO-024: Screen Reader
As a screen reader user, I want context.
- [ ] Alt text on all photos
- [ ] Aria-label on hero section
- [ ] Name/tagline read properly
- [ ] Decorative annotation marked aria-hidden

---

## CUSTOMIZER FIELDS

```php
// Already defined in 11-ADMIN-CUSTOMIZER.md
// Hero Section in About Page panel:
// - kunaal_about_hero_show (toggle)
// - kunaal_about_hero_name (text)
// - kunaal_about_hero_tagline (text)
// - kunaal_about_hero_annotation (text, max 40 chars)
// - kunaal_about_hero_photo_1 through _4 (media)
```

---

## EDGE CASES

### E-HERO-001: No Photos
- Show name/tagline only
- Centered layout
- Optional placeholder gradient

### E-HERO-002: Single Very Tall Photo
- Constrain max-height
- Maintain aspect ratio
- Crop via object-fit

### E-HERO-003: Slow Network
- Show placeholder shimmer
- Progressive image loading
- No content jump on load

### E-HERO-004: Missing Name
- Fall back to site title
- Graceful degradation

---

## FINAL CHECKLIST

### Structure
- [ ] Hero container with min-height: 100vh
- [ ] Collage container for photo positioning
- [ ] Identity block for name/tagline
- [ ] Annotation positioned separately
- [ ] Scroll hint at bottom

### Photos
- [ ] 1-4 photos supported
- [ ] Positioned via absolute/relative
- [ ] Parallax speeds: 0.2, 0.6, 0.4, 0.3
- [ ] Grayscale → color at 30vh
- [ ] Angular clip-paths (subtle)
- [ ] Box shadows for depth

### Typography
- [ ] Name: --serif, clamp(32px, 5vw, 56px)
- [ ] Tagline: --sans, clamp(14px, 1.5vw, 18px)
- [ ] Annotation: --hand, brown, rotated

### Animations
- [ ] Parallax via requestAnimationFrame
- [ ] Color transition 600ms
- [ ] Annotation fade-in 1.2s delay
- [ ] Scroll hint pulse animation

### Accessibility
- [ ] Reduced motion support
- [ ] Alt text on images
- [ ] Aria labels
- [ ] Keyboard navigable


