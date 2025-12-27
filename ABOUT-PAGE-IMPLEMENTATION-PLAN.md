# About Page - Master Implementation Plan

## Solution Architecture Overview

This plan addresses all identified issues systematically, following WordPress and front-end best practices. The approach prioritizes fixes that unblock other issues and ensures robust, maintainable code.

---

## Implementation Strategy

### Phase 1: Critical Foundation Fixes
**Goal:** Fix issues that block content visibility and core functionality
**Duration:** ~2-3 hours
**Dependencies:** None

### Phase 2: Layout & Visual Consistency
**Goal:** Ensure hero layout matches reference exactly
**Duration:** ~2-3 hours
**Dependencies:** Phase 1 complete

### Phase 3: Performance & Polish
**Goal:** Optimize performance and fix remaining visual issues
**Duration:** ~1-2 hours
**Dependencies:** Phase 2 complete

---

## Phase 1: Critical Foundation Fixes

### Fix 1.1: Hero Text Visibility on Mobile (Issue 14)
**Priority:** CRITICAL
**Files:** `kunaal-theme/assets/js/about-page-v22.js`, `kunaal-theme/assets/css/about-page-v22.css`

#### Root Cause
- GSAP ScrollTrigger `start: 'top 86%'` doesn't fire for elements below viewport
- On mobile, `.hero-text` is in `grid-row: 3`, below initial viewport
- Elements stay hidden (`opacity: 0`) until ScrollTrigger fires

#### Implementation Approach

**Step 1: Add Mobile-Specific ScrollTrigger Configuration**
```javascript
// In initScrollReveals function
var isMobile = window.innerWidth < 900;
var startPos = isMobile ? 'top bottom' : 'top 86%';

// For hero-text elements specifically on mobile
if (isMobile && el.closest('.hero-text')) {
  startPos = 'top 100%'; // Trigger immediately when element enters viewport
  // Or use immediateRender: false with different approach
}
```

**Step 2: Add CSS Fallback for Mobile**
```css
@media (max-width: 900px) {
  /* Ensure hero text is visible even if GSAP doesn't load */
  .hero-text [data-reveal] {
    opacity: 1 !important;
    transform: none !important;
  }
  
  /* Override GSAP initial state on mobile */
  .hero-text .hero-intro,
  .hero-text .hero-meta {
    opacity: 1;
    transform: translateY(0);
  }
}
```

**Step 3: Improve ScrollTrigger Refresh**
```javascript
// Add mobile-specific refresh logic
window.addEventListener('resize', function() {
  if (window.gsap && window.ScrollTrigger) {
    var isMobile = window.innerWidth < 900;
    // Force refresh and recalculate for mobile
    window.ScrollTrigger.refresh();
    // Re-initialize hero-text reveals if needed
  }
}, { passive: true });
```

**Step 4: Add Immediate Render Option**
```javascript
// For hero-text elements, use immediateRender for mobile
var st = window.gsap.from(el, {
  // ... existing config
  scrollTrigger: {
    trigger: el,
    start: isMobile && el.closest('.hero-text') ? 'top 100%' : 'top 86%',
    toggleActions: 'play none none reverse',
    refreshPriority: 1,
    // Add immediate render for mobile to ensure visibility
    immediateRender: isMobile && el.closest('.hero-text') ? false : true,
    invalidateOnRefresh: true
  }
});
```

**Testing:**
- Test on mobile devices (iOS Safari, Chrome Android)
- Test with GSAP disabled (should still show text)
- Test on resize from desktop to mobile
- Verify text appears immediately on mobile load

---

### Fix 1.2: Scroll Indicator Position & Animation (Issue 2)
**Priority:** CRITICAL
**Files:** `kunaal-theme/page-about.php`, `kunaal-theme/assets/css/about-page-v22.css`, `kunaal-theme/assets/js/about-page-v22.js`

#### Root Cause
- Scroll indicator is INSIDE `.hero-text` div (wrong DOM position)
- Missing absolute positioning within `.hero` section
- GSAP transform might conflict with CSS animation

#### Implementation Approach

**Step 1: Move Scroll Indicator in DOM**
```php
<!-- In page-about.php, move scroll indicator OUTSIDE hero-text -->
<div class="hero-text">
    <!-- ... hero text content ... -->
    <!-- REMOVE scroll indicator from here -->
</div>

<!-- Add scroll indicator as direct child of .hero section -->
<div class="scroll-indicator" id="scrollIndicator">
    <span class="scroll-indicator-text">Scroll</span>
    <div class="scroll-indicator-line"></div>
    <div class="scroll-indicator-chevron"></div>
</div>
```

**Step 2: Update CSS Positioning**
```css
/* Remove margin-top, add absolute positioning */
.scroll-indicator {
  position: absolute;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  z-index: 20;
  opacity: 1;
  transition: opacity 0.5s ease;
  pointer-events: none;
  color: var(--warm);
}

/* Remove .scroll-indicator--hero-text class and related styles */
```

**Step 3: Fix GSAP Animation Conflict**
```javascript
// In initPageLoad, ensure GSAP doesn't interfere with CSS animation
.from('#scrollIndicator', { 
  opacity: 0, 
  duration: 0.35,
  // Don't set y transform - let CSS animation handle movement
  clearProps: 'transform' // Clear any transforms after fade-in
}, '<0.25');
```

**Step 4: Verify CSS Animation**
```css
/* Ensure animation is vertical (up/down) */
@keyframes scrollBounce {
  0%, 100% { 
    transform: rotate(45deg) translateY(0); 
    opacity: 1; 
  }
  50% { 
    transform: rotate(45deg) translateY(6px); 
    opacity: 0.5; 
  }
}

.scroll-indicator-chevron {
  width: 8px;
  height: 8px;
  border-right: 2px solid var(--warm);
  border-bottom: 2px solid var(--warm);
  transform: rotate(45deg);
  animation: scrollBounce 1.8s ease-in-out infinite;
  opacity: 0.9;
}
```

**Testing:**
- Verify indicator is centered horizontally in hero section
- Verify indicator is at bottom of hero section (2rem from bottom)
- Verify animation is vertical (up/down), not horizontal
- Test on desktop and mobile

---

### Fix 1.3: Dog Ear Accent Visibility (Issue 16)
**Priority:** CRITICAL
**Files:** `kunaal-theme/assets/css/about-page-v22.css`

#### Root Cause
- Multiple potential causes: overflow clipping, z-index stacking, CSS not applying
- Need systematic fix addressing all hypotheses

#### Implementation Approach

**Step 1: Fix Overflow Property**
```css
.hero-photo {
  position: relative;
  overflow: hidden; /* Change from visible to hidden (match reference) */
  background: var(--bgAlt);
}
```

**Step 2: Ensure Proper Z-Index Stacking**
```css
.hero-photo img {
  /* ... existing styles ... */
  position: relative;
  z-index: 1; /* Keep image at z-index: 1 */
}

/* Dog-ear accent - ensure it's above image */
.hero-photo.has-accent::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, var(--blue) 50%, transparent 50%);
  z-index: 20; /* Increase from 10 to 20 to ensure it's above everything */
  pointer-events: none;
  /* Add explicit positioning to ensure it's not clipped */
  clip-path: none;
}
```

**Step 3: Verify CSS Variable**
```css
/* Ensure --blue is defined (should be in :root, but verify) */
:root {
  --blue: #1E5AFF; /* Verify this value */
}

/* Add fallback color if variable fails */
.hero-photo.has-accent::before {
  background: linear-gradient(135deg, var(--blue, #1E5AFF) 50%, transparent 50%);
}
```

**Step 4: Add Debug Styles (Temporary)**
```css
/* Temporary: Make accent very obvious for testing */
.hero-photo.has-accent::before {
  /* ... existing styles ... */
  /* Temporary: Add border to verify it's rendering */
  /* border: 2px solid red; */ /* Uncomment for debugging */
}
```

**Step 5: Ensure No Parent Clipping**
```css
/* Verify .hero section doesn't clip */
.hero {
  /* ... existing styles ... */
  overflow: visible; /* Ensure hero doesn't clip children */
}

/* If needed, create stacking context for accent */
.hero-photo.has-accent {
  isolation: isolate; /* Create new stacking context */
}
```

**Testing:**
- Use browser DevTools to inspect `.hero-photo.has-accent::before`
- Verify element exists in DOM
- Verify z-index is 20 (above image's z-index: 1)
- Verify gradient is rendering (check computed styles)
- Test on desktop and mobile
- Verify accent is visible on third photo (index 2)

---

## Phase 2: Layout & Visual Consistency

### Fix 2.1: Hero Layout Exact Match (Issue 1)
**Priority:** HIGH
**Files:** `kunaal-theme/page-about.php`, `kunaal-theme/assets/css/about-page-v22.css`

#### Implementation Approach

**Step 1: Verify Grid Structure**
```css
/* Ensure exact grid match with reference */
.hero {
  display: grid;
  grid-template-columns: 60px 1fr 1fr 1fr minmax(280px, 400px) 60px;
  grid-template-rows: 1fr 1fr;
  gap: 2px;
  min-height: 100vh;
  background: var(--bg);
  position: relative;
  /* Ensure no padding that affects grid */
  padding: 0;
  margin: 0;
}
```

**Step 2: Verify Photo Ordering**
```php
<!-- Ensure photos are in correct order -->
<!-- Row 1: Photos 1-4, Hero-text, Photo 5 -->
<!-- Row 2: Photos 6-10 -->
<!-- Verify has-accent is on photo 3 (index 2) -->
```

**Step 3: Fix Overflow for Dog Ear**
```css
/* Already fixed in 1.3, but ensure it's correct */
.hero-photo {
  overflow: hidden; /* Match reference */
}
```

**Step 4: Mobile Photo Reordering**
```php
<!-- Add mobile-specific photo ordering -->
<?php
// Desktop: normal order
// Mobile: reorder to put dog-ear photo in safer position
$is_mobile = false; // Will be handled by CSS
$mobile_photo_order = [0, 1, 4, 2, 3, 5, 6, 7, 8, 9]; // Dog-ear (2) moved to index 3

// Or use CSS order property for mobile
?>
```

```css
/* Use CSS order to reorder photos on mobile */
@media (max-width: 900px) {
  .hero {
    grid-template-columns: 30px 1fr 1fr 30px;
    grid-template-rows: 1fr 1fr auto;
  }
  
  /* Reorder photos: move dog-ear photo (3rd) to second row */
  .hero-photo:nth-child(3) {
    order: 5; /* Move to after photo 5 */
  }
  
  /* Or use different approach: move to row 2 */
  .hero-photo.has-accent {
    /* Ensure it's in row 2, not row 1 */
    grid-row: 2;
  }
}
```

**Step 5: Responsive Width Handling**
```css
/* Ensure images reveal more content on wider screens */
.hero-photo img {
  /* ... existing styles ... */
  /* Use object-position to optimize for wider screens */
  object-position: center center; /* Default */
}

/* First photo: reveal more from left as screen widens */
.hero-photo:first-child img {
  object-position: left center;
}

/* Ensure images are large enough */
/* Note: This requires source images to be 2000px+ wide */
/* Add to documentation/requirements */
```

**Testing:**
- Compare with reference HTML side-by-side
- Test at standard laptop width (1366px, 1440px, 1920px)
- Verify grid columns match exactly
- Verify photo ordering matches
- Test mobile layout
- Verify dog-ear photo is not cut off on mobile

---

### Fix 2.2: "On Repeat" Image Sizing (Issue 3)
**Priority:** HIGH
**Files:** `kunaal-theme/assets/css/about-page-v22.css`

#### Implementation Approach

**Step 1: Fix Grid Column Consistency**
```css
.media-grid {
  display: grid;
  /* Use fixed columns instead of auto-fit */
  grid-template-columns: repeat(3, 1fr); /* Fixed 3 columns */
  gap: 1rem;
  /* Or use minmax for flexibility but consistency */
  /* grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); */
}

/* Ensure all items have same width */
.media-item {
  width: 100%;
  min-width: 0; /* Prevent overflow */
}
```

**Step 2: Strengthen Aspect Ratio Enforcement**
```css
.media-cover.album {
  position: relative;
  width: 100%;
  border-radius: 6px;
  overflow: hidden;
  background: var(--bgAlt);
  border: 1px solid var(--hair2);
  aspect-ratio: 1;
  /* Add explicit height constraint */
  height: 0;
  padding-bottom: 100%; /* Fallback for older browsers */
}

/* Remove all nth-child overrides - they're not needed if grid is consistent */
/* Delete lines 811-820 */

.media-cover.album::before {
  content: '';
  display: block;
  padding-top: 100%;
  height: 0;
}

.media-cover.album img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  margin: 0;
  border-radius: 0;
}
```

**Step 3: Add Container Constraints**
```css
/* Ensure parent container doesn't affect sizing */
.media-item {
  display: flex;
  flex-direction: column;
  width: 100%;
  min-width: 0;
  max-width: 100%;
}

.media-cover {
  width: 100%;
  flex-shrink: 0;
}
```

**Testing:**
- Verify all album covers are exactly same size
- Test with different numbers of items (1, 2, 3, 4, 5, 6)
- Test on different screen widths
- Use browser DevTools to measure actual dimensions

---

### Fix 2.3: Social Media Icons Sizing (Issue 3b)
**Priority:** MEDIUM
**Files:** `kunaal-theme/assets/css/about-page-v22.css`

#### Implementation Approach

**Step 1: Fix Media Link SVG Sizing**
```css
.media-link {
  font-family: var(--mono);
  font-size: 8px;
  color: var(--blue);
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

/* Add explicit SVG sizing */
.media-link svg {
  width: 8px;
  height: 8px;
  flex-shrink: 0;
  display: block;
}
```

**Step 2: Verify Say Hello Icons**
```css
/* Ensure say-hello icons are correct size */
.say-hello-social-link {
  width: 36px;
  height: 36px;
  border: 1px solid var(--warmBorder);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--muted);
  transition: all var(--transition);
}

.say-hello-social-link svg {
  width: 15px !important; /* Force size */
  height: 15px !important;
  display: block;
  flex-shrink: 0;
}
```

**Step 3: Add Container Constraints**
```css
/* Ensure containers don't affect icon sizing */
.say-hello-social {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  align-items: center; /* Ensure vertical alignment */
}
```

**Testing:**
- Verify media-link SVGs are 8px × 8px
- Verify say-hello SVGs are 15px × 15px
- Test on desktop and mobile
- Verify icons are not "HORRENDOUSLY large"

---

## Phase 3: Performance & Polish

### Fix 3.1: Panorama Parallax Performance (Issue 17)
**Priority:** HIGH
**Files:** `kunaal-theme/assets/js/about-page-v22.js`, `kunaal-theme/assets/css/about-page-v22.css`

#### Implementation Approach

**Step 1: Optimize ScrollTrigger Scrub**
```javascript
function initPanoramaParallax(gsapOk) {
  if (reduceMotion || !gsapOk) return;
  var bands = document.querySelectorAll('.panorama');
  for (var i = 0; i < bands.length; i++) {
    (function (band) {
      var img = band.querySelector('.panorama-img');
      if (!img) return;
      var speed = parseFloat(band.getAttribute('data-speed') || '1');
      if (!isFinite(speed)) speed = 1;
      var amp = 10 * speed;
      try {
        window.gsap.fromTo(img,
          { yPercent: -amp },
          {
            yPercent: amp,
            ease: 'none',
            force3D: true, // Add hardware acceleration
            scrollTrigger: {
              trigger: band,
              start: 'top bottom',
              end: 'bottom top',
              scrub: true // Change from scrub: 1 to scrub: true (smooth, no lag)
            }
          }
        );
      } catch (e) {
        console.warn('Panorama parallax failed:', e);
      }
    })(bands[i]);
  }
}
```

**Step 2: Add CSS Performance Optimizations**
```css
.panorama-img {
  width: 100%;
  height: 220%;
  object-fit: cover;
  object-position: center 65%;
  filter: grayscale(100%);
  position: absolute;
  top: -60%;
  left: 0;
  /* Add performance optimizations */
  will-change: transform;
  transform: translateZ(0); /* Force GPU acceleration */
  backface-visibility: hidden; /* Improve rendering performance */
  contain: layout style paint; /* Isolate rendering context */
}
```

**Step 3: Optimize Image Loading**
```php
<!-- In page-about.php, add loading optimization -->
<img 
  alt="Photo" 
  class="panorama-img" 
  decoding="async" 
  loading="lazy" 
  src="<?php echo esc_url($panorama['image']); ?>"
  fetchpriority="low" <!-- For below-fold panoramas -->
/>
```

**Step 4: Add RequestAnimationFrame Optimization (if needed)**
```javascript
// If still laggy, add RAF optimization
// But GSAP already uses RAF internally, so this might not be needed
```

**Testing:**
- Test parallax smoothness on desktop (Chrome, Firefox, Safari)
- Test on mobile devices (iOS, Android)
- Use Chrome DevTools Performance tab to measure FPS
- Verify parallax feels "real" and not laggy
- Test with different scroll speeds

---

### Fix 3.2: Mobile Hero Layout Improvements (Issue 11, 12)
**Priority:** MEDIUM
**Files:** `kunaal-theme/page-about.php`, `kunaal-theme/assets/css/about-page-v22.css`

#### Implementation Approach

**Step 1: Mobile Photo Reordering**
```css
@media (max-width: 900px) {
  .hero {
    grid-template-columns: 30px 1fr 1fr 30px;
    grid-template-rows: 1fr 1fr auto;
    min-height: auto;
  }
  
  /* Reorder photos: move dog-ear photo to second row */
  .hero-photo:nth-child(1) { order: 1; }
  .hero-photo:nth-child(2) { order: 2; }
  .hero-photo:nth-child(3) { order: 5; } /* Dog-ear: move to after photo 5 */
  .hero-photo:nth-child(4) { order: 3; }
  .hero-photo:nth-child(5) { order: 4; }
  .hero-photo:nth-child(6) { order: 6; }
  .hero-photo:nth-child(7) { order: 7; }
  .hero-photo:nth-child(8) { order: 8; }
  .hero-photo:nth-child(9) { order: 9; }
  .hero-photo:nth-child(10) { order: 10; }
  
  /* Or use grid-row assignment */
  .hero-photo.has-accent {
    grid-row: 2; /* Force to second row */
  }
}
```

**Step 2: Optimize Mobile Grid Row Heights**
```css
@media (max-width: 900px) {
  .hero {
    /* Ensure rows have adequate height */
    grid-template-rows: minmax(150px, 1fr) minmax(150px, 1fr) auto;
  }
  
  .hero-photo {
    min-height: 150px; /* Increase from 120px */
  }
}
```

**Step 3: Responsive Width Image Handling**
```css
/* Ensure images are optimized for different widths */
.hero-photo img {
  /* Use srcset in HTML for responsive images */
  /* CSS can't control this, but ensure object-position is optimal */
}

/* First photo: reveal more from left */
.hero-photo:first-child img {
  object-position: left center;
}
```

**Testing:**
- Test mobile layout on actual devices
- Verify dog-ear photo is not cut off
- Verify photos fill width without excessive whitespace
- Test at various mobile widths (320px, 375px, 414px, 768px)

---

### Fix 3.3: Mobile Theme Toggle Alignment (Issue 13)
**Priority:** MEDIUM
**Files:** `kunaal-theme/style.css`

#### Implementation Approach

**Step 1: Fix Flexbox Alignment**
```css
@media (max-width: 640px) {
  .mastInner {
    /* Ensure proper alignment */
    align-items: center;
    gap: 12px; /* Reduce gap for snug fit */
  }
  
  .mastInner > .nav {
    margin-right: var(--space-1); /* Reduce from space-3 to space-1 */
  }
  
  .mastInner > .theme-toggle {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    box-shadow: none;
    margin-left: var(--space-1); /* Reduce gap - make it snug */
    padding: 0;
    /* Ensure same vertical alignment as nav */
    align-self: center;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .theme-toggle-icon {
    width: 16px;
    height: 16px;
  }
  
  .theme-toggle:hover,
  .theme-toggle:focus-visible {
    background: transparent;
    transform: none;
    box-shadow: none;
  }
}
```

**Step 2: Match Nav Item Height**
```css
/* Ensure nav items and toggle have same height */
.nav a {
  /* Check nav item height and match toggle */
  line-height: 1.5; /* Adjust to match toggle height */
  padding: 0.5rem 0; /* Adjust padding to match */
}

.mastInner > .theme-toggle {
  /* Match nav item height exactly */
  height: auto; /* Or match nav item computed height */
  min-height: 32px;
}
```

**Testing:**
- Verify toggle is on same row as nav items
- Verify toggle is snug to menu (small gap)
- Verify toggle has no box styling
- Test on various mobile widths

---

### Fix 3.4: Mobile Action Dock Size (Issue 15)
**Priority:** LOW
**Files:** `kunaal-theme/style.css`

#### Implementation Approach

**Step 1: Reduce Mobile Dock Size**
```css
@media (max-width: 640px) {
  .actionDock {
    right: var(--space-2);
    bottom: var(--space-2);
    gap: 6px; /* Reduce from 8px */
  }
  
  .dockButton {
    width: 28px; /* Reduce from 32px */
    height: 28px;
  }
  
  .dockButton svg {
    width: 12px; /* Reduce from 14px */
    height: 12px;
  }
}
```

**Testing:**
- Verify dock is smaller and less obstructive
- Test on mobile devices
- Verify icons are still usable/tappable

---

## Implementation Order & Dependencies

### Critical Path
1. **Fix 1.1** (Hero text visibility) - Blocks content, must fix first
2. **Fix 1.2** (Scroll indicator) - Core UX element
3. **Fix 1.3** (Dog ear) - Core design element
4. **Fix 2.1** (Hero layout) - Depends on 1.3
5. **Fix 2.2** (Image sizing) - Independent
6. **Fix 2.3** (Icon sizing) - Independent
7. **Fix 3.1** (Panorama) - Performance, independent
8. **Fix 3.2** (Mobile layout) - Depends on 2.1
9. **Fix 3.3** (Theme toggle) - Independent
10. **Fix 3.4** (Action dock) - Independent

### Testing Strategy

#### Unit Testing (Manual)
- Test each fix individually
- Verify fix doesn't break other functionality
- Test on multiple browsers (Chrome, Firefox, Safari, Edge)
- Test on multiple devices (Desktop, Tablet, Mobile)

#### Integration Testing
- Test all fixes together
- Verify no conflicts between fixes
- Test responsive behavior across all breakpoints
- Test with GSAP enabled and disabled

#### Performance Testing
- Use Chrome DevTools Performance tab
- Measure FPS during scroll
- Check for layout shifts (CLS)
- Verify images load efficiently

#### Accessibility Testing
- Test with keyboard navigation
- Test with screen reader
- Verify reduced motion preferences are respected
- Check color contrast

---

## Risk Mitigation

### Risk 1: Breaking Existing Functionality
**Mitigation:**
- Test each fix in isolation
- Keep backups of current code
- Use feature flags if needed
- Test on staging before production

### Risk 2: Performance Regression
**Mitigation:**
- Monitor performance metrics before/after
- Use performance budgets
- Optimize images and assets
- Test on low-end devices

### Risk 3: Browser Compatibility
**Mitigation:**
- Test on all major browsers
- Use CSS fallbacks for new features
- Polyfill if needed for older browsers
- Progressive enhancement approach

### Risk 4: Mobile-Specific Issues
**Mitigation:**
- Test on actual devices, not just DevTools
- Test on iOS and Android
- Test various screen sizes
- Test with different network conditions

---

## Code Quality Standards

### CSS Standards
- Use CSS variables for all colors/spacing
- Follow BEM-like naming conventions
- Keep specificity low (avoid !important where possible)
- Comment complex layouts
- Use mobile-first approach

### JavaScript Standards
- Use vanilla JavaScript (no jQuery dependencies)
- Follow existing code style
- Add error handling for all GSAP operations
- Comment complex logic
- Ensure graceful degradation

### PHP Standards
- Follow WordPress coding standards
- Use proper escaping functions
- Add sanitization for all inputs
- Comment template logic
- Keep template files clean and readable

---

## Documentation Requirements

### Code Comments
- Add comments for complex CSS layouts
- Document GSAP ScrollTrigger configurations
- Explain mobile-specific workarounds
- Note any browser-specific fixes

### Change Log
- Document all changes in commit messages
- Update theme version number
- Note any breaking changes
- Document new Customizer options (if any)

### User Documentation
- Update README if needed
- Document any new Customizer settings
- Note any configuration requirements

---

## Success Criteria

### Functional Requirements
- ✅ All hero text visible on mobile
- ✅ Dog ear accent visible and working
- ✅ Scroll indicator positioned correctly with vertical animation
- ✅ Hero layout matches reference exactly
- ✅ All images consistently sized
- ✅ Icons appropriately sized
- ✅ Panorama parallax smooth and performant
- ✅ Mobile layout elegant and functional

### Performance Requirements
- ✅ Panorama parallax: 60 FPS during scroll
- ✅ No layout shifts (CLS < 0.1)
- ✅ Images load efficiently
- ✅ No JavaScript errors in console

### Quality Requirements
- ✅ Code follows WordPress best practices
- ✅ No CSS/JS conflicts
- ✅ Responsive across all breakpoints
- ✅ Accessible (keyboard nav, screen readers)
- ✅ Works with reduced motion preferences

---

## Rollback Plan

### If Critical Issues Arise
1. Revert to previous commit
2. Identify specific fix causing issue
3. Fix in isolation
4. Re-test before re-deploying

### Partial Rollback
- Keep working fixes
- Revert only problematic fixes
- Document issues for future fixes

---

## Next Steps After Implementation

1. **QA Testing**
   - Comprehensive testing on all devices/browsers
   - Performance testing
   - Accessibility audit

2. **User Acceptance**
   - Get user approval on fixes
   - Address any remaining issues
   - Iterate if needed

3. **Documentation**
   - Update README
   - Document any new features
   - Create maintenance guide

4. **Deployment**
   - Deploy to staging
   - Final QA on staging
   - Deploy to production
   - Monitor for issues

---

## Estimated Timeline

- **Phase 1 (Critical):** 2-3 hours
- **Phase 2 (Layout):** 2-3 hours
- **Phase 3 (Polish):** 1-2 hours
- **Testing & QA:** 1-2 hours
- **Total:** 6-10 hours

---

## Notes

- All fixes should be implemented incrementally
- Test after each major fix
- Commit after each phase
- Keep code clean and maintainable
- Follow WordPress and front-end best practices
- Prioritize user experience and performance

