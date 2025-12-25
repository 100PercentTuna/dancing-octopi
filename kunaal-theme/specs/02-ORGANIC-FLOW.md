# 02 - ORGANIC FLOW
## Complete Specification

---

## OVERVIEW

The organic flow system eliminates the traditional "boxed sections" approach of web pages. Instead of:
```
[SECTION A]
─────────────
[SECTION B]
─────────────
[SECTION C]
```

We create:
```
[SECTION A content...]
    [atmospheric image bleeding through]
        [...SECTION B content overlapping image]
            [SECTION C starting while B fades...]
```

This creates the feeling of walking through a **continuous photography exhibition** where one experience flows into the next.

---

## THE NO-SECTIONS PHILOSOPHY

### Traditional Web Problem
- Hard borders between content areas
- Predictable scroll-stop patterns
- "Template-y" feel
- Each section feels isolated

### Our Solution
- Content overlaps at edges (40-80px)
- Atmospheric images bridge sections
- Z-depth creates continuous space
- Scroll triggers gradual transitions

---

## VISUAL RHYTHM CHART

```
SCROLL POSITION    VISUAL STATE                          Z-DEPTH
──────────────────────────────────────────────────────────────────
0vh                Hero collage visible                  Layer 1-2
                   Photos in grayscale
                   Name/tagline visible

30vh               Photos transition to color            Layer 1-2
                   First atmospheric strip begins
                   entering from bottom

50vh               Atmospheric strip 1 fully visible     Layer 0-1
                   Bio section label fading in           Layer 2

80vh               Bio text begins appearing             Layer 2
                   Atmospheric strip starts exiting

100vh              Bio text fully visible                Layer 2
                   Pull quote enters (if enabled)
                   Strip 1 almost gone

120vh              Bookshelf section entering            Layer 2
                   Books still below fold

140vh              Bookshelf visible                     Layer 2
                   Atmospheric window forming

160vh              Window cutout reveals image           Layer 0-1
                   Map section label appearing           Layer 2

180vh              Map fully visible                     Layer 2
                   User can interact

220vh              Interests cloud entering              Layer 2
                   Map fading slightly

250vh              Interests fully visible               Layer 2
                   Inspirations beginning

280vh              Inspirations grid visible             Layer 2
                   Stats counters entering

300vh              Stats counters animating              Layer 2
                   Connect section entering

320vh              Connect section visible               Layer 2
                   Final atmospheric image
                   Footer approaching
```

---

## OVERLAP ZONES

### Zone Definition
The overlap zone is where two "sections" share vertical space. Content from both sections is visible simultaneously.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│                        SECTION A CONTENT                                    │
│                           (ending)                                          │
│                                                                             │
│ ════════════════════════════════════════════════════════════════════════════│
│ │                                                                         │ │
│ │                    OVERLAP ZONE (40-80px)                               │ │
│ │        Section A fading out, Section B fading in                        │ │
│ │                                                                         │ │
│ ════════════════════════════════════════════════════════════════════════════│
│                                                                             │
│                        SECTION B CONTENT                                    │
│                          (starting)                                         │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Overlap Sizes by Transition Type

| Transition | Overlap | Notes |
|------------|---------|-------|
| Hero → Atmospheric | 60px | Image slides under hero |
| Atmospheric → Content | 80px | Content overlays image |
| Content → Content | 40px | Subtle blend |
| Content → Window | 60px | Window reveals gradually |
| Any → Map | 40px | Map needs clear space |

---

## Z-DEPTH LAYER INTERACTIONS

```css
/* Layer 0: Background */
.about-layer-bg {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
  pointer-events: none;
}

/* Layer 1: Atmospheric images */
.about-layer-atmo {
  position: relative;
  z-index: 10;
}

/* Layer 2: Content */
.about-layer-content {
  position: relative;
  z-index: 20;
  background: var(--bg);
}

/* Layer 3: Foreground (annotations, tooltips) */
.about-layer-fg {
  position: relative;
  z-index: 30;
}
```

### Interaction Rules

1. **Layer 0 (Background)** — Fixed position, very slow parallax (0.1)
2. **Layer 1 (Atmospheric)** — Relative position, slow parallax (0.2-0.4)
3. **Layer 2 (Content)** — Normal scroll, no parallax
4. **Layer 3 (Foreground)** — Fixed tooltips, floating elements

---

## CSS IMPLEMENTATION

```css
/* === ORGANIC FLOW CONTAINER === */
.about-page {
  position: relative;
  overflow-x: hidden;
  background: var(--bg);
}

/* === SECTION BASE === */
.about-section {
  position: relative;
  z-index: 20;
}

/* === OVERLAP MECHANICS === */
.about-section--overlap-top {
  margin-top: -60px;
  padding-top: 80px;
}

.about-section--overlap-bottom {
  margin-bottom: -60px;
  padding-bottom: 80px;
}

/* === FADE TRANSITIONS === */
.about-section--fade-in {
  opacity: 0;
  transform: translateY(30px);
  transition: 
    opacity 800ms ease-out,
    transform 800ms ease-out;
}

.about-section--fade-in.is-visible {
  opacity: 1;
  transform: translateY(0);
}

/* === STAGGER CHILDREN === */
.about-section--stagger > * {
  opacity: 0;
  transform: translateY(20px);
  transition: 
    opacity 600ms ease-out,
    transform 600ms ease-out;
}

.about-section--stagger.is-visible > *:nth-child(1) { 
  transition-delay: 0ms; 
  opacity: 1;
  transform: translateY(0);
}

.about-section--stagger.is-visible > *:nth-child(2) { 
  transition-delay: 100ms; 
  opacity: 1;
  transform: translateY(0);
}

.about-section--stagger.is-visible > *:nth-child(3) { 
  transition-delay: 200ms; 
  opacity: 1;
  transform: translateY(0);
}

.about-section--stagger.is-visible > *:nth-child(4) { 
  transition-delay: 300ms; 
  opacity: 1;
  transform: translateY(0);
}

.about-section--stagger.is-visible > *:nth-child(5) { 
  transition-delay: 400ms; 
  opacity: 1;
  transform: translateY(0);
}

/* === CONTENT PANELS === */
.about-panel {
  background: var(--bg);
  position: relative;
  z-index: 20;
}

/* Panel that overlays atmospheric image */
.about-panel--overlay {
  background: linear-gradient(
    to bottom,
    transparent 0%,
    var(--bg) 30px
  );
  padding-top: 60px;
}

/* Panel with cutout window */
.about-panel--window {
  position: relative;
}

.about-panel--window::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 80%;
  height: 200px;
  background: transparent;
  box-shadow: 0 0 0 9999px var(--bg);
  pointer-events: none;
}

/* === SEAMLESS TRANSITIONS === */
.about-transition {
  height: 60px;
  background: linear-gradient(
    to bottom,
    var(--bg) 0%,
    transparent 100%
  );
  position: relative;
  z-index: 15;
  margin-bottom: -60px;
}

.about-transition--reverse {
  background: linear-gradient(
    to top,
    var(--bg) 0%,
    transparent 100%
  );
  margin-top: -60px;
  margin-bottom: 0;
}

/* === RHYTHM SPACERS === */
.about-spacer--sm { height: var(--space-8); }   /* 64px */
.about-spacer--md { height: var(--space-12); }  /* 96px */
.about-spacer--lg { height: var(--space-15); }  /* 120px */
.about-spacer--xl { height: var(--space-20); }  /* 160px */
```

---

## JAVASCRIPT IMPLEMENTATION

```javascript
class OrganicFlow {
  constructor() {
    this.sections = document.querySelectorAll('.about-section--fade-in');
    this.staggerSections = document.querySelectorAll('.about-section--stagger');
    
    this.init();
  }
  
  init() {
    // Check for reduced motion
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      this.showAllImmediately();
      return;
    }
    
    this.setupIntersectionObserver();
  }
  
  showAllImmediately() {
    this.sections.forEach(section => section.classList.add('is-visible'));
    this.staggerSections.forEach(section => section.classList.add('is-visible'));
  }
  
  setupIntersectionObserver() {
    const options = {
      root: null,
      rootMargin: '-10% 0px -10% 0px', // Trigger when 10% visible
      threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          // Don't unobserve - allow re-animation on scroll back
        } else {
          // Optional: remove class when out of view for re-animation
          // entry.target.classList.remove('is-visible');
        }
      });
    }, options);
    
    this.sections.forEach(section => observer.observe(section));
    this.staggerSections.forEach(section => observer.observe(section));
  }
}

// Scroll position tracker for rhythm
class ScrollRhythm {
  constructor() {
    this.vh = window.innerHeight;
    this.callbacks = new Map();
    
    this.init();
  }
  
  init() {
    let ticking = false;
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          this.checkTriggers();
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
    
    window.addEventListener('resize', () => {
      this.vh = window.innerHeight;
    });
  }
  
  // Register callback at specific scroll percentage
  at(vhPercentage, callback) {
    const trigger = this.vh * (vhPercentage / 100);
    this.callbacks.set(trigger, callback);
  }
  
  checkTriggers() {
    const scrollY = window.scrollY;
    
    this.callbacks.forEach((callback, trigger) => {
      if (scrollY >= trigger) {
        callback();
        this.callbacks.delete(trigger); // Fire once
      }
    });
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  new OrganicFlow();
  
  // Example rhythm triggers
  const rhythm = new ScrollRhythm();
  rhythm.at(30, () => console.log('30vh reached'));
  rhythm.at(100, () => console.log('Bio section'));
});
```

---

## USER STORIES

### US-FLOW-001: No Hard Section Boundaries
As a visitor, I want a continuous experience.
- [ ] No visible dividers between sections
- [ ] Content flows naturally
- [ ] No "jump" between areas

### US-FLOW-002: Overlap Zones
As a visitor, I want smooth transitions.
- [ ] Sections overlap by 40-80px
- [ ] Gradient fades between areas
- [ ] No abrupt content changes

### US-FLOW-003: Z-Depth Perception
As a visitor, I want depth.
- [ ] Background layer fixed
- [ ] Atmospheric layer parallax
- [ ] Content layer normal scroll
- [ ] Clear hierarchy

### US-FLOW-004: Fade-In Reveals
As a visitor, I want content to appear.
- [ ] Sections fade in on scroll
- [ ] 800ms transition duration
- [ ] 30px translateY on entry

### US-FLOW-005: Staggered Children
As a visitor, I want sequential reveals.
- [ ] Child elements appear one by one
- [ ] 100ms delay between each
- [ ] Max 5 children staggered

### US-FLOW-006: Scroll Rhythm
As a visitor, I want pacing.
- [ ] Visual events at specific scroll positions
- [ ] Consistent timing between events
- [ ] Not too fast, not too slow

### US-FLOW-007: Content Panels
As a visitor, I want readable content.
- [ ] White background panels
- [ ] Clear text contrast
- [ ] Proper z-index above images

### US-FLOW-008: Panel Overlays
As a visitor, I want layered panels.
- [ ] Panels can overlay atmospheric images
- [ ] Gradient fade from transparent to bg
- [ ] Content readable over images

### US-FLOW-009: Window Cutouts
As a visitor, I want reveal moments.
- [ ] Panel with transparent center
- [ ] Background image visible through
- [ ] Creates "window" effect

### US-FLOW-010: Seamless Transitions
As a visitor, I want invisible seams.
- [ ] Gradient overlays hide edges
- [ ] Smooth color transitions
- [ ] No visible boundaries

### US-FLOW-011: Rhythm Spacers
As a visitor, I want breathing room.
- [ ] Consistent spacing values
- [ ] sm/md/lg/xl sizes
- [ ] Maintains visual rhythm

### US-FLOW-012: Scroll Position Awareness
As the page, I want to track scroll.
- [ ] Track viewport scroll position
- [ ] Fire callbacks at thresholds
- [ ] Use requestAnimationFrame

### US-FLOW-013: Re-animation Support
As a visitor scrolling back, I want animations.
- [ ] Elements can re-animate
- [ ] Optional behavior
- [ ] Smooth in both directions

### US-FLOW-014: Performance
As a visitor, I want smooth scrolling.
- [ ] Passive scroll listeners
- [ ] RequestAnimationFrame for updates
- [ ] Minimal DOM manipulation

### US-FLOW-015: Reduced Motion
As a motion-sensitive visitor, I want comfort.
- [ ] All content visible immediately
- [ ] No fade-in animations
- [ ] No parallax movement

### US-FLOW-016: Mobile Simplification
As a mobile visitor, I want clarity.
- [ ] Reduced overlap zones (20px)
- [ ] Simpler transitions
- [ ] No parallax

### US-FLOW-017: Print Layout
As a printing visitor, I want all content.
- [ ] All sections visible
- [ ] No overlaps
- [ ] Sequential layout

### US-FLOW-018: Section Labels
As a visitor, I want orientation.
- [ ] Mono font labels ("01 ABOUT")
- [ ] Fade in with section
- [ ] Brown accent underline

---

## EDGE CASES

### E-FLOW-001: Very Fast Scroll
- Animations still complete
- No visual glitches
- Performance maintained

### E-FLOW-002: Scroll Back Up
- Animations reverse properly
- No stuck states
- Content remains visible

### E-FLOW-003: Page Refresh Mid-Scroll
- Correct state restored
- No flash of wrong content
- Smooth initialization

### E-FLOW-004: Few Sections
- Still feels continuous
- Spacing adjusts
- No awkward gaps

---

## FINAL CHECKLIST

### Structure
- [ ] Organic flow container wraps all content
- [ ] Sections have overlap modifiers
- [ ] Transition elements between major areas
- [ ] Spacers maintain rhythm

### Animations
- [ ] IntersectionObserver for reveals
- [ ] Staggered child animations
- [ ] Scroll rhythm callbacks
- [ ] Reduced motion support

### Z-Depth
- [ ] Layer 0: Fixed background
- [ ] Layer 1: Atmospheric images
- [ ] Layer 2: Content panels
- [ ] Layer 3: Tooltips/annotations

### Performance
- [ ] Passive scroll listeners
- [ ] RequestAnimationFrame
- [ ] Minimal repaints
- [ ] Efficient observer usage


