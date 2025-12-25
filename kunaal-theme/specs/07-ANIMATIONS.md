# 07 - ANIMATIONS & SCROLL CHOREOGRAPHY
## Complete Specification

---

## OVERVIEW

Animations are the **soul of the scrollytelling experience**. They must be:
- **Subtle** — Never distract from content
- **Purposeful** — Each animation communicates something
- **Performant** — Smooth 60fps on all devices
- **Accessible** — Respect reduced motion preferences

---

## ANIMATION CATEGORIES

1. **Scroll-Triggered Reveals** — Content appears as user scrolls
2. **Parallax Movement** — Layers move at different speeds
3. **Color Transitions** — Grayscale to full color
4. **Micro-interactions** — Hovers, clicks, feedback
5. **Counter Animations** — Numbers count up
6. **Choreographed Sequences** — Multiple elements in concert

---

## SCROLL-TRIGGERED REVEALS

### IntersectionObserver Setup

```javascript
class ScrollReveal {
  constructor() {
    this.elements = document.querySelectorAll('[data-reveal]');
    this.init();
  }
  
  init() {
    // Respect reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.elements.forEach(el => el.classList.add('is-revealed'));
      return;
    }
    
    const options = {
      root: null,
      rootMargin: '-10% 0px -10% 0px',
      threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
        }
      });
    }, options);
    
    this.elements.forEach(el => observer.observe(el));
  }
}
```

### Reveal CSS Patterns

```css
/* === BASE REVEAL === */
[data-reveal] {
  opacity: 0;
  transform: translateY(30px);
  transition: 
    opacity 800ms ease-out,
    transform 800ms ease-out;
}

[data-reveal].is-revealed {
  opacity: 1;
  transform: translateY(0);
}

/* === REVEAL VARIANTS === */

/* Fade only (no movement) */
[data-reveal="fade"] {
  transform: none;
}

[data-reveal="fade"].is-revealed {
  transform: none;
}

/* Slide from left */
[data-reveal="left"] {
  transform: translateX(-40px);
}

[data-reveal="left"].is-revealed {
  transform: translateX(0);
}

/* Slide from right */
[data-reveal="right"] {
  transform: translateX(40px);
}

[data-reveal="right"].is-revealed {
  transform: translateX(0);
}

/* Scale up */
[data-reveal="scale"] {
  transform: scale(0.95);
}

[data-reveal="scale"].is-revealed {
  transform: scale(1);
}

/* === STAGGER DELAYS === */
[data-reveal-delay="1"] { transition-delay: 100ms; }
[data-reveal-delay="2"] { transition-delay: 200ms; }
[data-reveal-delay="3"] { transition-delay: 300ms; }
[data-reveal-delay="4"] { transition-delay: 400ms; }
[data-reveal-delay="5"] { transition-delay: 500ms; }
[data-reveal-delay="6"] { transition-delay: 600ms; }

/* === REVEAL DURATIONS === */
[data-reveal-duration="fast"] {
  transition-duration: 400ms;
}

[data-reveal-duration="slow"] {
  transition-duration: 1200ms;
}
```

---

## PARALLAX MOVEMENT

### Formula

```javascript
// From master spec
offset = (elementCenterY - viewportCenterY) * speed * direction;
transform: translateY(${offset}px);
```

### Speed Reference

| Layer | Speed | Use Case |
|-------|-------|----------|
| Background | 0.1 | Fixed atmospheric images |
| Slow | 0.2 | Hero primary photo |
| Medium | 0.4 | Secondary photos, strips |
| Fast | 0.6 | Accent elements, overlapping photos |
| Content | 1.0 | Normal scroll (no parallax) |

### Implementation

```javascript
class Parallax {
  constructor() {
    this.elements = document.querySelectorAll('[data-parallax]');
    this.vh = window.innerHeight;
    this.vhCenter = this.vh / 2;
    
    this.init();
  }
  
  init() {
    // Skip on mobile or reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
        window.innerWidth < 768) {
      return;
    }
    
    this.bindEvents();
  }
  
  bindEvents() {
    let ticking = false;
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          this.update();
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
    
    window.addEventListener('resize', () => {
      this.vh = window.innerHeight;
      this.vhCenter = this.vh / 2;
    });
  }
  
  update() {
    this.elements.forEach(el => {
      const rect = el.getBoundingClientRect();
      const elementCenter = rect.top + rect.height / 2;
      const distanceFromCenter = elementCenter - this.vhCenter;
      
      const speed = parseFloat(el.dataset.parallax) || 0.2;
      const direction = el.dataset.parallaxDirection === 'up' ? -1 : 1;
      const offset = distanceFromCenter * speed * direction;
      
      el.style.transform = `translateY(${offset}px)`;
    });
  }
}
```

### CSS Support

```css
[data-parallax] {
  will-change: transform;
}

/* Prevent layout shift during parallax */
.parallax-container {
  overflow: hidden;
}
```

---

## COLOR TRANSITIONS

### Grayscale to Color

```css
/* Default grayscale with warm sepia tint */
.grayscale-image {
  filter: grayscale(100%) sepia(10%);
  transition: filter 600ms ease-out;
}

/* Full color on trigger */
.grayscale-image.is-colored {
  filter: grayscale(0%) sepia(0%);
}
```

### Trigger Points

| Element | Trigger Point | Duration |
|---------|---------------|----------|
| Hero photos | 30vh scroll | 600ms |
| Atmospheric images | 30% visible | 600ms |
| Interest images | Hover | 400ms |
| Inspiration photos | Hover | 400ms |

### JavaScript Trigger

```javascript
class ColorTransition {
  constructor() {
    this.images = document.querySelectorAll('[data-color-trigger]');
    this.init();
  }
  
  init() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.images.forEach(img => img.classList.add('is-colored'));
      return;
    }
    
    // Scroll-based triggers
    this.images.forEach(img => {
      const trigger = img.dataset.colorTrigger;
      
      if (trigger === 'scroll') {
        this.setupScrollTrigger(img);
      } else if (trigger === 'hover') {
        this.setupHoverTrigger(img);
      }
    });
  }
  
  setupScrollTrigger(img) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-colored');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });
    
    observer.observe(img);
  }
  
  setupHoverTrigger(img) {
    const container = img.closest('[data-color-container]') || img.parentElement;
    
    container.addEventListener('mouseenter', () => {
      img.classList.add('is-colored');
    });
    
    container.addEventListener('mouseleave', () => {
      img.classList.remove('is-colored');
    });
  }
}
```

---

## MICRO-INTERACTIONS

### Book Lift Animation

```css
.book {
  transform-origin: bottom center;
  transition: 
    transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1),
    box-shadow 300ms ease;
}

.book:hover {
  transform: translateY(-15px) rotateX(-5deg);
}

.book:hover img {
  box-shadow: 
    4px 0 8px rgba(0, 0, 0, 0.25),
    6px 8px 20px rgba(0, 0, 0, 0.2);
}
```

### Interest Scale Animation

```css
.interest-image {
  transition: 
    transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1),
    border-color 300ms ease;
}

.interest-item:hover .interest-image {
  transform: scale(1.08);
  border-color: var(--warmLight);
}
```

### Inspiration Card Hover

```css
.inspiration-card {
  transition: 
    border-color 300ms ease,
    background-color 300ms ease;
}

.inspiration-card:hover {
  border-color: var(--blue);
  background-color: var(--blueTint);
}
```

### Button Hover

```css
.about-button {
  transition: 
    background-color 200ms ease,
    transform 200ms ease,
    box-shadow 200ms ease;
}

.about-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.about-button:active {
  transform: translateY(0);
}
```

---

## COUNTER ANIMATIONS

### Number Count-Up

```javascript
class CounterAnimation {
  constructor(element) {
    this.element = element;
    this.target = parseInt(element.dataset.countTo, 10);
    this.duration = parseInt(element.dataset.countDuration, 10) || 2000;
    this.started = false;
  }
  
  start() {
    if (this.started) return;
    this.started = true;
    
    const startTime = performance.now();
    const startValue = 0;
    
    const animate = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / this.duration, 1);
      
      // Ease out cubic
      const easeProgress = 1 - Math.pow(1 - progress, 3);
      const currentValue = Math.round(startValue + (this.target - startValue) * easeProgress);
      
      this.element.textContent = this.formatNumber(currentValue);
      
      if (progress < 1) {
        requestAnimationFrame(animate);
      }
    };
    
    requestAnimationFrame(animate);
  }
  
  formatNumber(num) {
    // Add commas for thousands
    return num.toLocaleString();
  }
}

// Initialize with IntersectionObserver
class Counters {
  constructor() {
    this.counters = document.querySelectorAll('[data-count-to]');
    this.init();
  }
  
  init() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.counters.forEach(el => {
        el.textContent = parseInt(el.dataset.countTo, 10).toLocaleString();
      });
      return;
    }
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          new CounterAnimation(entry.target).start();
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    
    this.counters.forEach(el => observer.observe(el));
  }
}
```

### CSS for Counters

```css
.stat-value {
  font-family: var(--sans);
  font-size: clamp(36px, 6vw, 56px);
  font-weight: 600;
  color: var(--ink);
  line-height: 1;
}

.stat-label {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted2);
  margin-top: var(--space-2);
}
```

---

## CHOREOGRAPHED SEQUENCES

### Hero Entry Sequence

```javascript
class HeroChoreography {
  constructor(hero) {
    this.hero = hero;
    this.photos = hero.querySelectorAll('.hero-photo');
    this.identity = hero.querySelector('.hero-identity');
    this.annotation = hero.querySelector('.hero-annotation');
    
    this.init();
  }
  
  init() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.showAll();
      return;
    }
    
    this.playSequence();
  }
  
  playSequence() {
    // Timeline:
    // 0ms: First photo starts fading in
    // 200ms: Second photo starts
    // 400ms: Third photo starts
    // 600ms: Fourth photo starts
    // 800ms: Identity (name/tagline) fades in
    // 1200ms: Annotation appears
    
    this.photos.forEach((photo, index) => {
      photo.style.opacity = '0';
      photo.style.transform = 'translateY(30px)';
      
      setTimeout(() => {
        photo.style.transition = 'opacity 600ms ease-out, transform 600ms ease-out';
        photo.style.opacity = '1';
        photo.style.transform = 'translateY(0)';
      }, index * 200);
    });
    
    if (this.identity) {
      this.identity.style.opacity = '0';
      setTimeout(() => {
        this.identity.style.transition = 'opacity 600ms ease-out';
        this.identity.style.opacity = '1';
      }, 800);
    }
    
    // Annotation handled by CSS animation (see 01-HERO-COLLAGE.md)
  }
  
  showAll() {
    this.photos.forEach(photo => {
      photo.style.opacity = '1';
      photo.style.transform = 'none';
    });
    if (this.identity) this.identity.style.opacity = '1';
    if (this.annotation) this.annotation.style.opacity = '1';
  }
}
```

### Section Entry Stagger

```css
/* Staggered children within a section */
.section-stagger > * {
  opacity: 0;
  transform: translateY(20px);
}

.section-stagger.is-revealed > *:nth-child(1) {
  animation: revealChild 600ms ease-out 0ms forwards;
}

.section-stagger.is-revealed > *:nth-child(2) {
  animation: revealChild 600ms ease-out 100ms forwards;
}

.section-stagger.is-revealed > *:nth-child(3) {
  animation: revealChild 600ms ease-out 200ms forwards;
}

.section-stagger.is-revealed > *:nth-child(4) {
  animation: revealChild 600ms ease-out 300ms forwards;
}

.section-stagger.is-revealed > *:nth-child(5) {
  animation: revealChild 600ms ease-out 400ms forwards;
}

@keyframes revealChild {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
```

---

## EASING CURVES

| Name | Curve | Use Case |
|------|-------|----------|
| ease-out | `ease-out` | Most reveals, fades |
| bounce | `cubic-bezier(0.34, 1.56, 0.64, 1)` | Book lift, interest scale |
| smooth | `cubic-bezier(0.4, 0, 0.2, 1)` | Parallax, scroll-based |
| snappy | `cubic-bezier(0.4, 0, 0.6, 1)` | Tooltips, hover states |

### CSS Variables

```css
:root {
  --ease-out: ease-out;
  --ease-bounce: cubic-bezier(0.34, 1.56, 0.64, 1);
  --ease-smooth: cubic-bezier(0.4, 0, 0.2, 1);
  --ease-snappy: cubic-bezier(0.4, 0, 0.6, 1);
}
```

---

## PERFORMANCE OPTIMIZATION

### GPU Acceleration

```css
/* Elements with heavy animations */
.hero-photo,
[data-parallax],
.book,
.interest-image {
  will-change: transform;
}

/* Remove will-change after animation completes */
.animation-complete {
  will-change: auto;
}
```

### Throttling

```javascript
// All scroll handlers use requestAnimationFrame
let ticking = false;

window.addEventListener('scroll', () => {
  if (!ticking) {
    requestAnimationFrame(() => {
      // Animation updates here
      ticking = false;
    });
    ticking = true;
  }
}, { passive: true });
```

### Passive Event Listeners

```javascript
// Always use passive for scroll/touch
element.addEventListener('scroll', handler, { passive: true });
element.addEventListener('touchmove', handler, { passive: true });
```

---

## REDUCED MOTION SUPPORT

### CSS

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  
  [data-parallax] {
    transform: none !important;
  }
  
  [data-reveal] {
    opacity: 1 !important;
    transform: none !important;
  }
  
  .grayscale-image {
    filter: none !important;
  }
}
```

### JavaScript Check

```javascript
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

if (prefersReducedMotion.matches) {
  // Skip all animations
  // Show content immediately
  // Disable parallax
}

// Listen for changes
prefersReducedMotion.addEventListener('change', (e) => {
  if (e.matches) {
    // User enabled reduced motion
  }
});
```

---

## USER STORIES

### Scroll Reveals

**US-ANIM-001: Fade-In Reveal**
- [ ] Elements fade in on scroll
- [ ] 30px translateY
- [ ] 800ms duration

**US-ANIM-002: Reveal Variants**
- [ ] Fade only option
- [ ] Slide from left
- [ ] Slide from right
- [ ] Scale up

**US-ANIM-003: Stagger Delays**
- [ ] 100ms increments
- [ ] Up to 6 delays
- [ ] Per-element control

**US-ANIM-004: Reveal Threshold**
- [ ] 10% visible triggers
- [ ] -10% root margin
- [ ] Consistent behavior

### Parallax

**US-ANIM-005: Parallax Movement**
- [ ] Layer-based speeds
- [ ] Smooth 60fps
- [ ] RequestAnimationFrame

**US-ANIM-006: Parallax Speeds**
- [ ] 0.1 (slowest)
- [ ] 0.2, 0.4, 0.6
- [ ] 1.0 (normal scroll)

**US-ANIM-007: Parallax Direction**
- [ ] Up or down option
- [ ] Default: down
- [ ] Data attribute control

**US-ANIM-008: Parallax Bounds**
- [ ] No overflow visible
- [ ] Contained in wrapper
- [ ] No layout shift

### Color Transitions

**US-ANIM-009: Grayscale Default**
- [ ] grayscale(100%) sepia(10%)
- [ ] Warm tone
- [ ] Applied to all photos

**US-ANIM-010: Color Reveal**
- [ ] 600ms transition
- [ ] ease-out curve
- [ ] At 30% visible

**US-ANIM-011: Hover Color**
- [ ] 400ms transition
- [ ] Interest/inspiration images
- [ ] Reversible

### Micro-interactions

**US-ANIM-012: Book Lift**
- [ ] translateY(-15px)
- [ ] rotateX(-5deg)
- [ ] Bounce easing

**US-ANIM-013: Interest Scale**
- [ ] scale(1.08)
- [ ] Brown border appear
- [ ] Bounce easing

**US-ANIM-014: Card Hover**
- [ ] Blue border (links only)
- [ ] Blue tint background
- [ ] 300ms transition

**US-ANIM-015: Button Feedback**
- [ ] translateY(-2px) on hover
- [ ] Return on active
- [ ] Shadow change

### Counter Animations

**US-ANIM-016: Count-Up**
- [ ] Start at 0
- [ ] 2s default duration
- [ ] Ease-out cubic

**US-ANIM-017: Counter Trigger**
- [ ] 50% visible
- [ ] IntersectionObserver
- [ ] One-time animation

**US-ANIM-018: Number Format**
- [ ] Locale-aware commas
- [ ] Integer only
- [ ] No decimals

### Choreography

**US-ANIM-019: Hero Sequence**
- [ ] Photos stagger (200ms)
- [ ] Identity at 800ms
- [ ] Annotation at 1200ms

**US-ANIM-020: Section Stagger**
- [ ] Children stagger 100ms
- [ ] Max 5 children
- [ ] CSS keyframes

**US-ANIM-021: Scroll Rhythm**
- [ ] Events at specific vh
- [ ] Consistent pacing
- [ ] Documented triggers

### Performance

**US-ANIM-022: GPU Layers**
- [ ] will-change on animated
- [ ] Remove after complete
- [ ] Minimal repaints

**US-ANIM-023: Passive Listeners**
- [ ] All scroll listeners
- [ ] All touch listeners
- [ ] No blocking

**US-ANIM-024: RequestAnimationFrame**
- [ ] All scroll updates
- [ ] Single RAF per frame
- [ ] Throttled callbacks

### Accessibility

**US-ANIM-025: Reduced Motion**
- [ ] Respect preference
- [ ] Skip all animations
- [ ] Show content immediately

**US-ANIM-026: Motion Detection**
- [ ] Check on load
- [ ] Listen for changes
- [ ] Apply dynamically

---

## FINAL CHECKLIST

### Scroll Reveals
- [ ] IntersectionObserver setup
- [ ] Fade-in default
- [ ] Variants (fade, left, right, scale)
- [ ] Stagger delays (1-6)

### Parallax
- [ ] Speed tiers defined
- [ ] RequestAnimationFrame
- [ ] Container overflow hidden
- [ ] Disabled on mobile

### Color Transitions
- [ ] Grayscale filter
- [ ] Scroll trigger (30%)
- [ ] Hover trigger (interests)
- [ ] 600ms duration

### Micro-interactions
- [ ] Book lift + tilt
- [ ] Interest scale
- [ ] Card hover (blue for links)
- [ ] Button feedback

### Counters
- [ ] Count-up animation
- [ ] IntersectionObserver trigger
- [ ] 2s duration
- [ ] Number formatting

### Choreography
- [ ] Hero entry sequence
- [ ] Section staggers
- [ ] Timing documented

### Performance
- [ ] will-change strategic
- [ ] Passive listeners
- [ ] RAF for scroll
- [ ] No jank

### Accessibility
- [ ] prefers-reduced-motion
- [ ] Content always visible
- [ ] No motion sickness


