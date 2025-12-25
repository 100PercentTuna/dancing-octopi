# 10 - RESPONSIVE DESIGN
## Complete Specification

---

## OVERVIEW

The About page must work flawlessly across all screen sizes while maintaining the **layered exhibition** aesthetic. Key principles:
- **Mobile-first CSS** where practical
- **No horizontal scroll** at any breakpoint
- **Touch-optimized** interactions on mobile
- **Simplified animations** on mobile
- **Maintained design language** across sizes

---

## BREAKPOINTS

```css
:root {
  /* Breakpoint values */
  --bp-mobile: 640px;
  --bp-tablet: 1024px;
  --bp-desktop: 1280px;
}

/* Mobile: < 640px */
@media (max-width: 639px) { }

/* Tablet: 640px - 1023px */
@media (min-width: 640px) and (max-width: 1023px) { }

/* Desktop: 1024px+ */
@media (min-width: 1024px) { }

/* Large desktop: 1280px+ */
@media (min-width: 1280px) { }
```

---

## SECTION-BY-SECTION ADAPTATIONS

### Hero Section

#### Desktop (1024px+)
- Photos in asymmetric collage
- 4 photos visible
- Parallax enabled
- Name/tagline positioned bottom-right

#### Tablet (640px - 1023px)
- Photos in simpler arrangement
- 2-3 photos visible
- Parallax enabled (reduced speed)
- Name/tagline repositioned

#### Mobile (<640px)
- Single primary photo only
- No parallax
- Name/tagline below photo, centered
- Full-width layout

```css
/* Hero responsive */
@media (max-width: 1023px) {
  .hero-photo--3,
  .hero-photo--4 {
    display: none;
  }
  
  .hero-photo--1 {
    width: clamp(200px, 50vw, 350px);
    left: 10vw;
  }
  
  .hero-photo--2 {
    width: clamp(180px, 40vw, 280px);
    top: 35vh;
    left: 35vw;
  }
}

@media (max-width: 639px) {
  .about-hero {
    min-height: auto;
    padding: var(--space-8) var(--space-4);
  }
  
  .hero-collage {
    min-height: auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-6);
  }
  
  .hero-photo--1 {
    position: relative;
    top: auto;
    left: auto;
    width: 80%;
    max-width: 300px;
  }
  
  .hero-photo--2,
  .hero-photo--3,
  .hero-photo--4 {
    display: none;
  }
  
  .hero-identity {
    position: relative;
    bottom: auto;
    right: auto;
    text-align: center;
  }
  
  .hero-annotation {
    position: relative;
    bottom: auto;
    right: auto;
    margin-top: var(--space-4);
  }
}
```

### Bio Section

#### All Breakpoints
- Max-width: prose (620px)
- Centered
- Drop cap on all sizes

#### Mobile Adjustments
- Reduced font size
- Tighter line-height
- Pull quote full-width

```css
@media (max-width: 639px) {
  .about-bio-text p {
    font-size: 17px;
    line-height: 1.65;
  }
  
  .about-bio-text p:first-of-type::first-letter {
    font-size: 3.5em;
  }
  
  .about-pullquote {
    font-size: 18px;
    padding-left: var(--space-3);
  }
}
```

### Bookshelf

#### Desktop
- 6-8 books visible
- Hover reveals tooltip
- Shelf width adjusts to books

#### Tablet
- 4-6 books visible
- Touch reveals tooltip
- Shelf centered

#### Mobile
- 3-4 books visible
- Horizontal scroll optional
- Larger touch targets

```css
@media (max-width: 1023px) {
  .bookshelf {
    gap: var(--space-2);
    overflow-x: auto;
    padding-bottom: var(--space-4);
    justify-content: flex-start;
    -webkit-overflow-scrolling: touch;
  }
  
  .book {
    width: clamp(45px, 12vw, 60px);
    height: clamp(70px, 18vw, 95px);
    flex-shrink: 0;
  }
}

@media (max-width: 639px) {
  .bookshelf {
    justify-content: center;
    flex-wrap: nowrap;
  }
  
  .book {
    width: 50px;
    height: 75px;
  }
  
  /* Touch-friendly tooltip */
  .book-tooltip {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100vw - 40px);
    max-width: 300px;
  }
}
```

### Atmospheric Images

#### Desktop
- Full parallax effect
- All clip-path variations
- Full heights

#### Tablet
- Reduced parallax
- Simplified clips
- Reduced heights

#### Mobile
- No parallax
- Straight edges only
- Minimum heights

```css
@media (max-width: 1023px) {
  .atmo-strip {
    height: clamp(150px, 25vh, 280px);
  }
  
  /* Simplify clip paths on tablet */
  .atmo-strip--angle-bottom {
    clip-path: polygon(0 0, 100% 0, 100% 95%, 0 100%);
  }
}

@media (max-width: 639px) {
  .atmo-strip {
    height: clamp(120px, 30vh, 200px);
    clip-path: none; /* Straight edges on mobile */
  }
  
  .atmo-window {
    min-height: 300px;
  }
  
  .atmo-window__cutout {
    width: 85%;
    height: 150px;
  }
  
  .atmo-dual {
    grid-template-columns: 1fr;
    gap: var(--space-3);
  }
  
  .atmo-dual__item:first-child,
  .atmo-dual__item:last-child {
    transform: none;
    height: clamp(150px, 40vw, 220px);
  }
}
```

### World Map

#### Desktop
- Full interactive map
- Hover tooltips
- All countries visible

#### Tablet
- Full map, touch interactions
- Tap for tooltips
- Pinch-to-zoom optional

#### Mobile
- Simplified map view
- Tap for tooltips
- Consider horizontal scroll or zoom

```css
@media (max-width: 1023px) {
  .map-container {
    aspect-ratio: 16/10;
  }
  
  .map-tooltip {
    max-width: 240px;
    font-size: 13px;
  }
}

@media (max-width: 639px) {
  .about-map {
    padding: 0;
    margin-left: calc(-1 * var(--space-4));
    margin-right: calc(-1 * var(--space-4));
    width: calc(100% + var(--space-4) * 2);
  }
  
  .map-container {
    aspect-ratio: 4/3;
    border-radius: 0;
    overflow-x: auto;
  }
  
  .map-svg {
    min-width: 500px; /* Allow horizontal scroll */
  }
  
  .map-tooltip {
    position: fixed;
    bottom: 20px;
    left: 10px;
    right: 10px;
    width: auto;
    max-width: none;
    transform: none;
  }
  
  .map-legend {
    flex-direction: column;
    align-items: center;
    gap: var(--space-2);
  }
}
```

### Interests Cloud

#### Desktop
- Organic cloud layout
- Varied vertical offsets
- 56px images

#### Tablet
- Tighter layout
- Reduced offsets
- 48px images

#### Mobile
- Grid layout (cleaner)
- No offsets
- 40px images

```css
@media (max-width: 1023px) {
  .interests-cloud {
    gap: var(--space-3) var(--space-4);
  }
  
  .interest-image {
    width: 48px;
    height: 48px;
  }
}

@media (max-width: 639px) {
  .interests-cloud {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: var(--space-3);
    max-width: 100%;
  }
  
  .interest-item {
    transform: none; /* Remove offsets */
  }
  
  .interest-image {
    width: 40px;
    height: 40px;
  }
  
  .interest-label {
    font-size: 10px;
  }
}
```

### Inspirations Grid

#### Desktop
- 4 columns
- Card hover effects
- Full photo size

#### Tablet
- 2-3 columns
- Touch interactions
- Slightly smaller photos

#### Mobile
- 2 columns or 1 column
- Full-width cards
- Stack layout

```css
@media (max-width: 1023px) {
  .inspirations-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-3);
  }
  
  .inspiration-photo {
    width: 70px;
    height: 70px;
  }
}

@media (max-width: 639px) {
  .inspirations-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-3);
  }
  
  .inspiration-card {
    padding: var(--space-3);
  }
  
  .inspiration-photo {
    width: 60px;
    height: 60px;
  }
  
  .inspiration-name {
    font-size: 15px;
  }
  
  .inspiration-role {
    font-size: 12px;
  }
  
  .inspiration-note {
    display: none; /* Hide on mobile for cleaner look */
  }
}
```

### Stats Section

#### Desktop
- Horizontal row of stats
- Large numbers
- Spacing between

#### Mobile
- 2x2 grid
- Slightly smaller numbers
- Centered

```css
.about-stats-grid {
  display: flex;
  justify-content: center;
  gap: var(--space-10);
  flex-wrap: wrap;
}

@media (max-width: 639px) {
  .about-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-6);
    text-align: center;
  }
  
  .stat-value {
    font-size: clamp(28px, 8vw, 40px);
  }
}
```

---

## TYPOGRAPHY SCALING

```css
/* Fluid typography */
:root {
  --text-xs: clamp(10px, 1.5vw, 11px);
  --text-sm: clamp(12px, 2vw, 14px);
  --text-base: clamp(16px, 2.5vw, 19px);
  --text-lg: clamp(18px, 3vw, 24px);
  --text-xl: clamp(24px, 4vw, 36px);
  --text-2xl: clamp(32px, 5vw, 48px);
  --text-3xl: clamp(40px, 6vw, 64px);
}

/* Apply */
.hero-name {
  font-size: var(--text-2xl);
}

.about-bio-text p {
  font-size: var(--text-base);
}

.section-label {
  font-size: var(--text-xs);
}
```

---

## TOUCH INTERACTIONS

### Disable Hover Effects on Touch

```css
/* Only apply hover on devices that support it */
@media (hover: hover) and (pointer: fine) {
  .book:hover {
    transform: translateY(-15px) rotateX(-5deg);
  }
  
  .interest-item:hover .interest-image {
    transform: scale(1.08);
  }
  
  .inspiration-card:hover {
    border-color: var(--blue);
  }
}

/* Touch devices get tap states instead */
@media (hover: none) {
  .book:active {
    transform: translateY(-8px);
  }
  
  .interest-item:active .interest-image {
    transform: scale(1.05);
  }
  
  .inspiration-card:active {
    background-color: var(--blueTint);
  }
}
```

### Touch Target Enforcement

```css
@media (max-width: 1023px) {
  /* Ensure all touch targets are 44px minimum */
  .book,
  .interest-item,
  .inspiration-card,
  .map-tooltip__close,
  button,
  a {
    min-width: 44px;
    min-height: 44px;
  }
}
```

---

## ANIMATION ADJUSTMENTS

### Mobile: Simplified Animations

```css
@media (max-width: 639px) {
  /* Disable parallax on mobile */
  [data-parallax] {
    transform: none !important;
  }
  
  /* Faster reveals */
  [data-reveal] {
    transition-duration: 400ms;
  }
  
  /* Simpler book animation */
  .book:active {
    transition-duration: 150ms;
  }
  
  /* No map pulse on mobile (battery) */
  .map-current-marker__pulse {
    display: none;
  }
}
```

### Tablet: Reduced Parallax

```css
@media (min-width: 640px) and (max-width: 1023px) {
  /* Reduce parallax speed by 50% */
  [data-parallax="0.2"] { --actual-speed: 0.1; }
  [data-parallax="0.4"] { --actual-speed: 0.2; }
  [data-parallax="0.6"] { --actual-speed: 0.3; }
}
```

---

## SPACING ADJUSTMENTS

```css
/* Tighter spacing on mobile */
@media (max-width: 639px) {
  :root {
    --space-mobile-factor: 0.75;
  }
  
  .about-section {
    padding: calc(var(--space-10) * var(--space-mobile-factor)) var(--space-4);
  }
  
  .about-spacer--lg {
    height: calc(var(--space-15) * var(--space-mobile-factor));
  }
  
  .about-spacer--xl {
    height: calc(var(--space-20) * var(--space-mobile-factor));
  }
}
```

---

## OVERLAP ZONE ADJUSTMENTS

```css
/* Reduced overlaps on mobile */
@media (max-width: 639px) {
  .about-section--overlap-top {
    margin-top: -30px;
    padding-top: 50px;
  }
  
  .about-section--overlap-bottom {
    margin-bottom: -30px;
    padding-bottom: 50px;
  }
}
```

---

## LANDSCAPE MOBILE

```css
/* Handle landscape phones */
@media (max-width: 896px) and (orientation: landscape) {
  .about-hero {
    min-height: auto;
    padding: var(--space-6) var(--space-4);
  }
  
  .hero-collage {
    flex-direction: row;
    justify-content: center;
  }
  
  .hero-photo--1 {
    max-height: 50vh;
    width: auto;
  }
  
  .atmo-strip {
    height: 30vh;
    max-height: 150px;
  }
}
```

---

## PRINT STYLES

```css
@media print {
  /* Reset all animations */
  * {
    animation: none !important;
    transition: none !important;
  }
  
  /* Full color */
  .grayscale-image {
    filter: none !important;
  }
  
  /* No parallax offsets */
  [data-parallax] {
    transform: none !important;
  }
  
  /* Simple layout */
  .about-hero {
    min-height: auto;
    page-break-after: always;
  }
  
  .hero-photo {
    position: static !important;
    width: 100% !important;
    max-width: 400px !important;
    margin: 0 auto var(--space-4);
  }
  
  /* Hide interactive elements */
  .map-tooltip,
  .book-tooltip,
  .hero-scroll-hint {
    display: none !important;
  }
  
  /* Stack all content */
  .about-section {
    margin: 0;
    padding: var(--space-6) 0;
    page-break-inside: avoid;
  }
  
  /* Ensure readability */
  body {
    font-size: 12pt;
    line-height: 1.5;
  }
}
```

---

## CONTAINER QUERIES (FUTURE)

```css
/* When browser support improves */
@container (max-width: 400px) {
  .inspiration-card {
    flex-direction: column;
    text-align: center;
  }
}

@container (max-width: 300px) {
  .book {
    width: 40px;
    height: 60px;
  }
}
```

---

## TESTING CHECKLIST

### Devices to Test

- [ ] iPhone SE (375px)
- [ ] iPhone 12/13 (390px)
- [ ] iPhone 12/13 Pro Max (428px)
- [ ] iPad Mini (768px)
- [ ] iPad Pro 11" (834px)
- [ ] iPad Pro 12.9" (1024px)
- [ ] MacBook 13" (1280px)
- [ ] Desktop 1440px
- [ ] Desktop 1920px

### Orientations

- [ ] Portrait mobile
- [ ] Landscape mobile
- [ ] Portrait tablet
- [ ] Landscape tablet

### Scenarios

- [ ] Touch scrolling smooth
- [ ] No horizontal overflow
- [ ] All content visible
- [ ] Tooltips work
- [ ] Animations smooth
- [ ] Fonts readable
- [ ] Touch targets adequate

---

## FINAL CHECKLIST

### Breakpoints
- [ ] Mobile: < 640px
- [ ] Tablet: 640-1023px
- [ ] Desktop: 1024px+
- [ ] Large: 1280px+

### Hero
- [ ] Single photo on mobile
- [ ] 2-3 photos on tablet
- [ ] Full collage on desktop
- [ ] Centered identity on mobile

### Bookshelf
- [ ] Horizontal scroll mobile
- [ ] 3-4 books visible
- [ ] Fixed tooltip position mobile

### Map
- [ ] Horizontal scroll mobile
- [ ] Fixed tooltip position
- [ ] Legend stacked mobile

### Interests
- [ ] Grid on mobile
- [ ] Smaller images
- [ ] No offsets mobile

### Inspirations
- [ ] 2 columns mobile
- [ ] Hidden note mobile
- [ ] Smaller photos

### Animations
- [ ] No parallax mobile
- [ ] Faster transitions
- [ ] No battery-drain animations

### Touch
- [ ] 44px targets
- [ ] Active states
- [ ] No hover-only content


