# About Page - Comprehensive Technical Root Cause Analysis (RCA)

## Executive Summary

This document provides a thorough technical RCA for all identified issues on the About page, exploring multiple hypotheses and root causes based on code analysis, reference comparisons, and best practices research.

---

## Issue 1: Hero Images Layout & Dog Ear Accent

### Symptom
- Hero layout doesn't match reference exactly
- Dog ear accent (blue triangle) not visible on third hero photo
- Layout issues on mobile and different desktop widths

### Root Cause Analysis

#### Hypothesis 1: CSS Overflow Property Mismatch
**Evidence:**
- Current implementation: `.hero-photo { overflow: visible; }` (line 260 in `about-page-v22.css`)
- Reference implementation: `.hero-photo { overflow: hidden; }` (line 230 in reference HTML)
- Dog ear uses `::before` pseudo-element with `z-index: 10`
- Image has `z-index: 1` and `position: relative`

**Analysis:**
- `overflow: visible` allows content to overflow the container, which may cause layout shifts
- However, this alone shouldn't hide the dog ear if z-index is correct
- **Verdict:** Partial cause - overflow mismatch may contribute but not primary issue

#### Hypothesis 2: Z-Index Stacking Context Issues
**Evidence:**
- Dog ear: `z-index: 10`, `position: absolute`
- Image: `z-index: 1`, `position: relative`
- Hero photo container: `position: relative`

**Analysis:**
- Z-index should work correctly (10 > 1)
- However, if `.hero-photo` creates a new stacking context, the dog ear might be clipped
- `overflow: visible` should allow the accent to show, but `overflow: hidden` would clip it
- **Verdict:** Likely cause - need to verify stacking context and ensure accent is above image

#### Hypothesis 3: CSS Specificity or Override Issues
**Evidence:**
- Multiple CSS files may have conflicting rules
- Theme's main `style.css` might override About page styles
- CSS loading order matters

**Analysis:**
- Need to check if any global styles override `.hero-photo.has-accent::before`
- Check for `!important` rules that might interfere
- **Verdict:** Possible cause - requires CSS audit

#### Hypothesis 4: Missing or Incorrect Class Application
**Evidence:**
- PHP template applies `has-accent` class: `$has_accent = ($i === 2);` (line 86)
- Third photo (index 2) should have the class

**Analysis:**
- Logic appears correct
- Need to verify the class is actually in the rendered HTML
- **Verdict:** Unlikely but possible - verify rendered HTML

#### Hypothesis 5: Mobile Layout Grid Issues
**Evidence:**
- Mobile grid: `grid-template-columns: 30px 1fr 1fr 30px` (4 columns)
- Desktop grid: `grid-template-columns: 60px 1fr 1fr 1fr minmax(280px, 400px) 60px` (6 columns)
- Hero text moves to `grid-row: 3` on mobile

**Analysis:**
- Photo ordering might cause dog-ear photo to be cut off
- Grid layout changes might affect photo visibility
- **Verdict:** Likely cause for mobile cut-off issue

#### Hypothesis 6: Responsive Width Handling
**Evidence:**
- Grid uses `1fr` for flexible columns
- Images use `object-fit: cover`
- No explicit max-width constraints on images

**Analysis:**
- As viewport widens, `1fr` columns expand
- `object-fit: cover` crops images, but doesn't reveal more content
- Need to ensure images are large enough to show more content on wider screens
- **Verdict:** Likely cause - images may need larger source sizes or different object-position

### Primary Root Causes (Ranked)
1. **Z-index stacking context** - Dog ear may be behind image or clipped
2. **Overflow property mismatch** - `overflow: visible` vs `overflow: hidden` affects clipping
3. **Mobile grid layout** - Photo ordering causes cut-off
4. **Responsive image handling** - Images don't reveal more content on wider screens

### Recommended Fixes
1. Change `.hero-photo` to `overflow: hidden` (match reference)
2. Ensure dog ear `z-index: 10` is above image `z-index: 1`
3. Verify dog ear is not clipped by container bounds
4. Adjust mobile photo ordering to prevent cut-off
5. Optimize image sizes and object-position for responsive widths

---

## Issue 2: Scroll Indicator Animation & Position

### Symptom
- Scroll indicator animates to the side (horizontal) instead of up/down (vertical)
- Scroll indicator is positioned incorrectly (inside hero-text instead of hero section)

### Root Cause Analysis

#### Hypothesis 1: Incorrect DOM Position
**Evidence:**
- Current: Scroll indicator is INSIDE `.hero-text` div (line 117 in `page-about.php`)
- Reference: Scroll indicator is a direct child of `.hero` section (line 1478 in reference)
- Current CSS: `.scroll-indicator { margin-top: 1.5rem; }` (relative positioning)
- Reference CSS: `.scroll-indicator { position: absolute; bottom: 2rem; left: 50%; }`

**Analysis:**
- **PRIMARY ROOT CAUSE** - Scroll indicator is in wrong DOM location
- Being inside `.hero-text` means it's constrained by the text container
- Should be absolutely positioned within `.hero` section

#### Hypothesis 2: Animation Transform Conflict
**Evidence:**
- Current animation: `@keyframes scrollBounce { transform: rotate(45deg) translateY(0); }`
- Animation uses `translateY` which is correct for vertical movement
- However, if there's a conflicting transform from GSAP or CSS, it might override

**Analysis:**
- GSAP fade-in uses: `.from('#scrollIndicator', { y: 8, opacity: 0 })` (line 68)
- This sets initial `y: 8` which might conflict with animation
- GSAP might be applying transforms that interfere with CSS animation
- **Verdict:** Possible cause - GSAP transforms might override CSS animation

#### Hypothesis 3: CSS Animation Specificity
**Evidence:**
- Animation keyframes look correct: `translateY(0)` to `translateY(6px)`
- Chevron has `transform: rotate(45deg)` base transform
- Animation combines: `rotate(45deg) translateY(...)`

**Analysis:**
- If GSAP applies a transform, it might override the CSS animation
- CSS animations should work, but GSAP transforms have higher specificity
- **Verdict:** Likely cause - GSAP transform override

#### Hypothesis 4: Missing Absolute Positioning
**Evidence:**
- Current CSS: No `position: absolute` on `.scroll-indicator`
- Uses `margin-top: 1.5rem` for positioning (relative to parent)
- Reference: Uses `position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);`

**Analysis:**
- Without absolute positioning, indicator is in document flow
- This causes it to be positioned relative to hero-text, not hero section
- **Verdict:** PRIMARY ROOT CAUSE - missing absolute positioning

### Primary Root Causes (Ranked)
1. **Wrong DOM position** - Indicator inside `.hero-text` instead of `.hero`
2. **Missing absolute positioning** - Should be `position: absolute` within `.hero`
3. **GSAP transform conflict** - GSAP `y: 8` might override CSS animation
4. **Animation transform specificity** - CSS animation might be overridden

### Recommended Fixes
1. Move scroll indicator to be direct child of `.hero` section (not inside `.hero-text`)
2. Add `position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);`
3. Remove GSAP `y: 8` transform or ensure it doesn't conflict with CSS animation
4. Ensure CSS animation has proper specificity or use GSAP animation instead

---

## Issue 3: "On Repeat" Image Sizing Inconsistency

### Symptom
- Images in "On repeat" section have inconsistent sizes
- First image (Eminem Show) is larger than others (This American Life)

### Root Cause Analysis

#### Hypothesis 1: Aspect Ratio Not Consistently Applied
**Evidence:**
- CSS has: `.media-cover.album { aspect-ratio: 1; }` (line 791)
- Also has `::before` pseudo-element with `padding-top: 100%` (line 793-797)
- Image is absolutely positioned: `position: absolute; inset: 0;` (line 799-804)
- Override rules for nth-child (lines 811-820) - but these should all apply `aspect-ratio: 1 !important`

**Analysis:**
- Aspect ratio should be consistent
- However, if parent container has different sizing, aspect-ratio might not work
- `::before` padding technique should work as fallback
- **Verdict:** Possible cause - parent container sizing issue

#### Hypothesis 2: Parent Container Sizing
**Evidence:**
- `.media-item` containers might have different widths
- Grid layout: `.media-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); }`
- `auto-fit` with `minmax` might cause inconsistent column widths

**Analysis:**
- If grid columns have different widths, aspect-ratio: 1 will create different sized squares
- First item might be in a wider column
- **Verdict:** Likely cause - grid column width inconsistency

#### Hypothesis 3: Image Source Size Differences
**Evidence:**
- Different source images might have different aspect ratios
- `object-fit: cover` should handle this, but if container sizes differ, results differ

**Analysis:**
- If containers are same size, `object-fit: cover` should work
- If containers differ, images will appear different sizes
- **Verdict:** Symptom, not cause - caused by container sizing

#### Hypothesis 4: CSS Specificity or Override
**Evidence:**
- Multiple CSS rules for `.media-cover.album`
- Override rules use `!important` which should work
- But if parent has conflicting styles, it might affect sizing

**Analysis:**
- Need to check if any parent styles affect width/height
- Check for conflicting flex/grid properties
- **Verdict:** Possible cause - requires CSS audit

### Primary Root Causes (Ranked)
1. **Grid column width inconsistency** - `auto-fit` with `minmax` creates uneven columns
2. **Parent container sizing** - Different widths cause aspect-ratio to create different sized squares
3. **CSS specificity** - Override rules might not be applying correctly

### Recommended Fixes
1. Use fixed grid columns: `grid-template-columns: repeat(3, 1fr);` or similar
2. Ensure all `.media-item` containers have same width
3. Add explicit width constraints to `.media-cover.album`
4. Verify aspect-ratio is applying correctly with browser DevTools

---

## Issue 3b: Social Media Icons Size (NEW)

### Symptom
- Spotify link icons in "On repeat" section are too large
- X (Twitter) and LinkedIn icons in "Say Hello" section are too large

### Root Cause Analysis

#### Hypothesis 1: Missing SVG Size Constraints
**Evidence:**
- `.media-link` has `font-size: 8px` but no SVG size specified
- `.say-hello-link svg { width: 15px; height: 15px; }` exists (line 1061)
- But `.media-link svg` has no size constraints

**Analysis:**
- SVG without size constraints uses default size (often 24px or viewBox size)
- `.media-link` text is 8px, so SVG should be similar size (8-10px)
- **Verdict:** PRIMARY ROOT CAUSE - missing SVG size for media-link

#### Hypothesis 2: Inherited SVG Sizes
**Evidence:**
- SVGs might inherit sizes from parent or global styles
- No explicit size means browser default or viewBox size

**Analysis:**
- SVG viewBox might be 24x24, causing large default size
- Need explicit width/height to constrain
- **Verdict:** Likely cause

#### Hypothesis 3: Say Hello Icons Actually Too Large
**Evidence:**
- CSS shows: `.say-hello-link svg { width: 15px; height: 15px; }`
- But user says they're "HORRENDOUSLY large"
- Container is `36px × 36px`, so 15px icon should be fine

**Analysis:**
- 15px icon in 36px container = 42% of container (reasonable)
- But if SVG viewBox is large and not constrained, it might render larger
- Or CSS might not be applying correctly
- **Verdict:** Possible cause - CSS not applying or SVG not constrained

### Primary Root Causes (Ranked)
1. **Missing SVG size for media-link** - No width/height specified
2. **SVG default size** - Using viewBox or browser default (too large)
3. **CSS not applying** - Say hello icons might have specificity issues

### Recommended Fixes
1. Add `.media-link svg { width: 8px; height: 8px; }` to match text size
2. Verify `.say-hello-link svg` CSS is applying correctly
3. Add explicit size constraints to all SVGs in these sections

---

## Issue 11: Mobile Hero - Dog Ear Photo Cut Off

### Symptom
- Hero image with dog ear accent is cut off at the top on mobile
- Photo should be fully visible

### Root Cause Analysis

#### Hypothesis 1: Mobile Grid Layout Causes Cut-Off
**Evidence:**
- Mobile grid: `grid-template-columns: 30px 1fr 1fr 30px` (4 columns, 2 rows)
- Photos 1-4 go in first row, photos 5-6 in second row
- Dog ear photo is photo 3 (index 2), goes in first row
- Grid rows: `1fr 1fr auto` - first two rows are equal height

**Analysis:**
- If first row photos are taller than available space, they get cut off
- Dog ear photo in first row might be clipped by grid row height
- **Verdict:** Likely cause - grid row height constraint

#### Hypothesis 2: Photo Ordering on Mobile
**Evidence:**
- Current: Photos render in order 1-4, then 5-6 in second row
- Dog ear is photo 3, always in first row
- User wants "different photo ordering on mobile"

**Analysis:**
- Moving dog ear photo to second row would prevent first-row cut-off
- Need to reorder photos on mobile only
- **Verdict:** Solution approach - reorder photos for mobile

#### Hypothesis 3: Min-Height Constraint
**Evidence:**
- Mobile: `.hero-photo { min-height: 120px; }` (line 437)
- But grid rows use `1fr` which might not respect min-height properly

**Analysis:**
- `1fr` distributes available space equally
- If space is limited, photos might be smaller than 120px
- Dog ear might be clipped if photo is too small
- **Verdict:** Possible cause

#### Hypothesis 4: Object-Position or Object-Fit Issues
**Evidence:**
- Images use `object-fit: cover` and `object-position: center`
- If container is too small, important parts (dog ear area) might be cropped

**Analysis:**
- `object-fit: cover` maintains aspect ratio but crops
- Dog ear is at `top: 0; left: 0`, so if image is cropped from top, accent is lost
- **Verdict:** Possible cause - image cropping

### Primary Root Causes (Ranked)
1. **Mobile grid layout** - First row height constraint causes cut-off
2. **Photo ordering** - Dog ear photo in first row gets clipped
3. **Image cropping** - `object-fit: cover` might crop the accent area

### Recommended Fixes
1. Reorder photos on mobile: Move dog ear photo (index 2) to second row
2. Adjust mobile grid to ensure adequate row heights
3. Use `object-position: top center` for dog ear photo to preserve accent area
4. Consider different grid structure for mobile (e.g., single column with dog ear photo prominent)

---

## Issue 12: Hero Section Across Different Widths

### Symptom
- Hero section doesn't render elegantly across different desktop widths
- Should show more of cropped images as viewport widens, not just get bigger

### Root Cause Analysis

#### Hypothesis 1: Grid Column Behavior
**Evidence:**
- Grid: `grid-template-columns: 60px 1fr 1fr 1fr minmax(280px, 400px) 60px`
- `1fr` columns expand proportionally as viewport widens
- Images use `object-fit: cover` which crops to fill container

**Analysis:**
- As `1fr` columns expand, image containers get wider
- `object-fit: cover` maintains aspect ratio, so wider container = shows more horizontal content
- But if images are not wide enough, they just scale up (get bigger, not reveal more)
- **Verdict:** Likely cause - images need to be wider to reveal more content

#### Hypothesis 2: Image Source Size Limitations
**Evidence:**
- Images are loaded from WordPress media library
- May be constrained to specific sizes (e.g., 1024px width)
- If source image is 1024px wide, and container becomes 1200px, image just scales

**Analysis:**
- Need larger source images to reveal more content on wider screens
- Or use `srcset` with multiple sizes
- **Verdict:** Likely cause - image size constraints

#### Hypothesis 3: Object-Position Not Optimized
**Evidence:**
- Images use `object-position: center` (default)
- This centers the crop, might not show the most interesting part

**Analysis:**
- For first image especially, might want `object-position: left center` or similar
- This would reveal more content from one side as container widens
- **Verdict:** Possible enhancement

#### Hypothesis 4: Grid Column Max Constraints
**Evidence:**
- Hero text column: `minmax(280px, 400px)` - has max constraint
- Photo columns: `1fr` - no max constraint
- As viewport widens, photo columns expand but text column caps at 400px

**Analysis:**
- This is actually correct behavior - text should have max width
- But photo columns should reveal more image content, not just scale
- **Verdict:** Not a cause, but related to image sizing

### Primary Root Causes (Ranked)
1. **Image source size** - Images not wide enough to reveal more content
2. **Object-fit behavior** - `cover` scales images instead of revealing more
3. **Object-position** - Centered crop doesn't optimize for wider screens

### Recommended Fixes
1. Use larger source images (e.g., 2000px+ width) to allow more content reveal
2. Implement `srcset` with responsive image sizes
3. Adjust `object-position` for key images (e.g., first image: `left center`)
4. Consider using `object-fit: contain` for some images (but this might leave gaps)

---

## Issue 13: Mobile Theme Toggle Position & Styling

### Symptom
- Theme toggle is too far left on mobile
- Not snug to menu, positioned lower than menu items
- Has box styling that doesn't match menu

### Root Cause Analysis

#### Hypothesis 1: Flexbox Alignment Issues
**Evidence:**
- `.mastInner { display: flex; align-items: center; justify-content: space-between; }`
- Theme toggle: `margin-left: var(--space-2);` on mobile (line 417)
- Menu and toggle are separate flex items

**Analysis:**
- `justify-content: space-between` pushes items to edges
- Theme toggle might not be properly aligned with menu
- Need to ensure toggle is in same flex container as menu, or adjust alignment
- **Verdict:** Likely cause - flexbox alignment

#### Hypothesis 2: Vertical Alignment Mismatch
**Evidence:**
- `.mastInner { align-items: center; }` - should center vertically
- But menu items and toggle might have different heights
- Toggle: `32px × 32px` on mobile
- Menu items: height not explicitly set, might be different

**Analysis:**
- If heights differ, `align-items: center` centers based on tallest item
- Toggle might appear lower if menu items are taller
- **Verdict:** Possible cause - height mismatch

#### Hypothesis 3: CSS Not Applying Correctly
**Evidence:**
- Mobile CSS: `border: none; background: transparent; box-shadow: none;` (lines 414-416)
- But user says toggle still has box
- CSS might be overridden by more specific rules

**Analysis:**
- Need to check if `.theme-toggle` has other styles that override
- Check `style.css` for theme toggle base styles
- **Verdict:** Possible cause - CSS specificity

#### Hypothesis 4: Spacing/Gap Issues
**Evidence:**
- Toggle has `margin-left: var(--space-2);` (16px gap)
- User wants it "snug" to menu
- Gap might be too large or toggle positioned incorrectly

**Analysis:**
- 16px gap might be too much
- Or toggle is in wrong flex container
- **Verdict:** Likely cause - spacing too large

### Primary Root Causes (Ranked)
1. **Flexbox alignment** - Toggle not properly aligned with menu items
2. **Spacing too large** - 16px gap is not "snug"
3. **CSS override** - Box styles not being removed
4. **Height mismatch** - Different heights cause vertical misalignment

### Recommended Fixes
1. Reduce `margin-left` to `var(--space-1)` (8px) or `0.5rem` (4px) for snug fit
2. Ensure toggle and menu are in same flex container with `align-items: center`
3. Add `!important` to mobile styles if needed to override base styles
4. Match toggle height to menu item height for perfect alignment

---

## Issue 14: Hero Text Disappearing on Mobile

### Symptom
- Text below "Hi, I'm Kunaal" (`.hero-intro` and `.hero-meta`) doesn't render on mobile
- Text disappears when screen width changes

### Root Cause Analysis

#### Hypothesis 1: GSAP ScrollTrigger Not Firing
**Evidence:**
- All text elements have `data-reveal="up"` attribute
- GSAP ScrollTrigger: `start: 'top 86%'` (line 96)
- Elements start with `opacity: 0` and `y: 14` (hidden, moved down)
- On mobile, `.hero-text` is in `grid-row: 3` (below photos, likely below viewport initially)

**Analysis:**
- **PRIMARY ROOT CAUSE** - If `.hero-text` is below viewport on page load, ScrollTrigger `start: 'top 86%'` means element's top must be at 86% of viewport
- If element is already below viewport, ScrollTrigger might not fire immediately
- Elements stay hidden (`opacity: 0`) until ScrollTrigger fires
- On mobile, hero-text is in row 3, definitely below initial viewport
- **Verdict:** PRIMARY ROOT CAUSE - ScrollTrigger not firing for below-viewport elements

#### Hypothesis 2: ScrollTrigger Refresh Issues
**Evidence:**
- Code has resize listener: `window.addEventListener('resize', ...)` (line 103)
- Calls `st.scrollTrigger.refresh()` on resize
- But if ScrollTrigger never initialized (element below viewport), refresh does nothing

**Analysis:**
- ScrollTrigger calculates positions on init
- If element is below viewport, it might not be in ScrollTrigger's calculation
- Resize might not help if trigger never fired
- **Verdict:** Contributing cause

#### Hypothesis 3: CSS Display/Visibility Issues
**Evidence:**
- No CSS rules that hide elements on mobile
- But GSAP sets `opacity: 0` initially
- If ScrollTrigger doesn't fire, opacity stays 0

**Analysis:**
- Elements are in DOM (confirmed from template)
- But visually hidden by GSAP opacity
- **Verdict:** Symptom, not cause - caused by GSAP not revealing

#### Hypothesis 4: Mobile-Specific CSS Hiding
**Evidence:**
- No `display: none` or `visibility: hidden` in mobile media queries
- But need to verify no global styles hide these elements

**Analysis:**
- Unlikely - would need to check all CSS files
- **Verdict:** Unlikely but possible

#### Hypothesis 5: ScrollTrigger Start Position Too High
**Evidence:**
- `start: 'top 86%'` means element's top at 86% of viewport
- For elements below viewport, this might never trigger
- Should use `start: 'top bottom'` or `start: 'top 100%'` for below-viewport elements

**Analysis:**
- For elements initially visible: `top 86%` works (triggers when scrolled into view)
- For elements below viewport: `top 86%` might never trigger if user doesn't scroll
- On mobile, hero-text is below viewport, so needs different start position
- **Verdict:** Likely cause - start position inappropriate for mobile

### Primary Root Causes (Ranked)
1. **ScrollTrigger not firing** - Elements below viewport don't trigger `start: 'top 86%'`
2. **Inappropriate start position** - `top 86%` doesn't work for below-viewport elements
3. **No fallback visibility** - Elements stay hidden if ScrollTrigger doesn't fire

### Recommended Fixes
1. Add mobile-specific ScrollTrigger start: `start: window.innerWidth < 900 ? 'top bottom' : 'top 86%'`
2. Add CSS fallback: `.hero-text [data-reveal] { opacity: 1; }` in mobile media query if GSAP fails
3. Use `immediateRender: false` and `invalidateOnRefresh: true` for ScrollTrigger
4. Add `once: true` or ensure elements are visible even if ScrollTrigger doesn't fire
5. Consider using `ScrollTrigger.batch()` for hero-text elements on mobile

---

## Issue 15: Mobile Action Dock Icons

### Symptom
- Share, subscribe, download icons are too large and obstructive on mobile
- Current: 32px buttons, 14px icons

### Root Cause Analysis

#### Hypothesis 1: Size Still Too Large
**Evidence:**
- Current mobile: `32px × 32px` buttons, `14px × 14px` icons
- User wants smaller: suggested `28px buttons, 12px icons`
- But user says they're "too large and obstructive"

**Analysis:**
- 32px might still be too large for mobile screens
- Icons at 14px in 32px container = 44% of container (reasonable, but might feel large)
- **Verdict:** Likely cause - size needs further reduction

#### Hypothesis 2: Positioning Issues
**Evidence:**
- Action dock: `position: fixed; right: var(--space-3); bottom: var(--space-3);`
- On mobile: `right: var(--space-2); bottom: var(--space-2);`
- Fixed positioning means dock is always visible

**Analysis:**
- Fixed position + large size = obstructive
- Might need to hide on mobile or make smaller
- **Verdict:** Contributing cause

#### Hypothesis 3: Z-Index Too High
**Evidence:**
- Action dock: `z-index: 40`
- Might be above important content

**Analysis:**
- High z-index ensures visibility, but also ensures it's always on top
- Combined with large size, this makes it obstructive
- **Verdict:** Contributing cause

### Primary Root Causes (Ranked)
1. **Size too large** - 32px/14px still too big for mobile
2. **Fixed positioning** - Always visible, can't be avoided
3. **Z-index** - Always on top of content

### Recommended Fixes
1. Reduce to `28px × 28px` buttons, `12px × 12px` icons (or even smaller: 24px/10px)
2. Consider hiding dock on mobile or making it collapsible
3. Reduce z-index or add touch-friendly spacing

---

## Issue 16: Dog Ear Accent Still Not Working

### Symptom
- Blue dog ear accent on third hero image is still not visible
- Despite previous fixes, issue persists

### Root Cause Analysis

#### Hypothesis 1: Overflow Clipping
**Evidence:**
- Current: `.hero-photo { overflow: visible; }`
- Reference: `.hero-photo { overflow: hidden; }`
- Dog ear: `position: absolute; top: 0; left: 0; width: 28px; height: 28px;`
- Z-index: 10 (above image's z-index: 1)

**Analysis:**
- If `overflow: visible`, accent should show
- But if parent container (`.hero`) has `overflow: hidden`, accent is clipped
- Or if image container clips, accent is lost
- **Verdict:** Likely cause - overflow clipping somewhere in parent chain

#### Hypothesis 2: Z-Index Stacking Context
**Evidence:**
- Dog ear: `z-index: 10`
- Image: `z-index: 1`
- Hero photo: `position: relative` (creates stacking context)

**Analysis:**
- Stacking context should work (10 > 1)
- But if image has `position: relative` and `z-index: 1`, it creates its own context
- Accent with `z-index: 10` should be above, but might be in different context
- **Verdict:** Possible cause - stacking context isolation

#### Hypothesis 3: CSS Not Applying
**Evidence:**
- CSS rule: `.hero-photo.has-accent::before { ... }`
- Need to verify class is applied: `$has_accent = ($i === 2);`
- Need to verify CSS is loaded and not overridden

**Analysis:**
- Class application looks correct
- But CSS might not be applying due to specificity
- Or CSS file might not be loading
- **Verdict:** Possible cause - CSS not applying

#### Hypothesis 4: Gradient Not Rendering
**Evidence:**
- Gradient: `linear-gradient(135deg, var(--blue) 50%, transparent 50%)`
- If `var(--blue)` is not defined or transparent, gradient won't show
- Need to verify CSS variable is set

**Analysis:**
- CSS variable should be defined in `:root`
- But if not loaded or overridden, gradient is transparent
- **Verdict:** Possible cause - CSS variable issue

#### Hypothesis 5: Image Overlaying Accent
**Evidence:**
- Image has `z-index: 1` and `position: relative`
- Accent has `z-index: 10`
- But if image is rendered after accent in DOM, it might overlay

**Analysis:**
- Z-index should handle this, but DOM order + z-index can be tricky
- Image is child of `.hero-photo`, accent is `::before` (pseudo-element, before image in stacking)
- **Verdict:** Unlikely - pseudo-elements should be above

### Primary Root Causes (Ranked)
1. **Overflow clipping** - Parent container clipping the accent
2. **CSS not applying** - Rule not loading or overridden
3. **Z-index stacking** - Stacking context isolation
4. **CSS variable** - `var(--blue)` not defined

### Recommended Fixes
1. Change `.hero-photo` to `overflow: hidden` (match reference) - BUT ensure accent z-index is high enough
2. OR keep `overflow: visible` but ensure no parent clips
3. Increase accent z-index to `z-index: 20` or higher
4. Verify CSS variable `--blue` is defined and has correct value
5. Add `!important` to accent styles if needed to override
6. Use browser DevTools to inspect and verify accent is rendering

---

## Issue 17: Panorama Parallax Performance (NEW)

### Symptom
- Panorama images lag when scrolling
- Parallax effect feels choppy, not smooth and realistic

### Root Cause Analysis

#### Hypothesis 1: ScrollTrigger Scrub Value Too High
**Evidence:**
- Current: `scrub: 1` (line 140 in `about-page-v22.js`)
- This creates 1-second lag between scroll and animation
- Makes parallax feel delayed and laggy

**Analysis:**
- **PRIMARY ROOT CAUSE** - `scrub: 1` adds significant lag
- Should use `scrub: true` (smooth, no lag) or `scrub: 0.5` (half-second lag)
- Or use `scrub: 0.1` for near-instant response
- **Verdict:** PRIMARY ROOT CAUSE

#### Hypothesis 2: Missing Hardware Acceleration
**Evidence:**
- CSS: `.panorama-img` has no `will-change` property
- `.panorama-inner` has `will-change: transform` (line 208)
- But image itself doesn't have hardware acceleration hints

**Analysis:**
- `will-change: transform` on image would hint browser to optimize
- `transform: translateZ(0)` forces GPU acceleration
- GSAP should use transforms, but explicit hints help
- **Verdict:** Contributing cause

#### Hypothesis 3: Large Image File Sizes
**Evidence:**
- Panorama images are full-width, potentially very large
- Large images take time to decode and render
- Causes jank during scroll

**Analysis:**
- Need optimized images (WebP, proper compression)
- Or use responsive images with `srcset`
- **Verdict:** Contributing cause

#### Hypothesis 4: JavaScript Scroll Event Overhead
**Evidence:**
- GSAP ScrollTrigger uses scroll events
- `scrub: 1` means animation updates on every scroll frame
- But with lag, feels disconnected

**Analysis:**
- GSAP is optimized, but `scrub: 1` adds delay
- Should use `scrub: true` for smooth, lag-free parallax
- **Verdict:** Primary cause (same as Hypothesis 1)

#### Hypothesis 5: No Force3D in GSAP
**Evidence:**
- GSAP animation uses `yPercent` transform
- No `force3D: true` specified
- Browser might not use GPU acceleration

**Analysis:**
- `force3D: true` forces 3D transforms, ensuring GPU acceleration
- Should improve performance significantly
- **Verdict:** Contributing cause

#### Hypothesis 6: Image Rendering Optimization
**Evidence:**
- Images use `object-fit: cover` and `object-position`
- Large images (220% height) need to be rendered
- Browser might struggle with large image rendering during scroll

**Analysis:**
- Need to optimize image rendering
- Use `contain: layout style paint` for better performance
- Or use smaller images with better compression
- **Verdict:** Contributing cause

### Primary Root Causes (Ranked)
1. **Scrub value too high** - `scrub: 1` adds 1-second lag
2. **Missing hardware acceleration** - No `will-change` or `force3D`
3. **Large image files** - Slow to decode/render
4. **No rendering optimization** - Browser not optimizing image rendering

### Recommended Fixes
1. Change `scrub: 1` to `scrub: true` or `scrub: 0.1` for smooth, responsive parallax
2. Add `will-change: transform` to `.panorama-img`
3. Add `force3D: true` to GSAP animation
4. Optimize images (WebP, compression, responsive sizes)
5. Add `contain: layout style paint` to `.panorama-img` for rendering optimization
6. Consider using `requestAnimationFrame` optimization if needed

---

## Cross-Issue Patterns & Systemic Issues

### Pattern 1: CSS Specificity Conflicts
- Multiple CSS files (theme CSS, About page CSS) might conflict
- Need comprehensive CSS audit to identify overrides

### Pattern 2: Mobile-First Responsive Design Gaps
- Many issues manifest on mobile
- Need better mobile-specific handling
- Consider mobile-first CSS approach

### Pattern 3: GSAP ScrollTrigger Configuration
- ScrollTrigger settings not optimized for all scenarios
- Need different configurations for mobile vs desktop
- Need fallbacks for when GSAP doesn't load

### Pattern 4: Z-Index Management
- Multiple z-index values across components
- Need systematic z-index scale
- Document z-index hierarchy

### Pattern 5: Performance Optimization Gaps
- Missing hardware acceleration hints
- Large unoptimized images
- No lazy loading for below-fold content

---

## Best Practices Research Findings

### GSAP ScrollTrigger Best Practices
1. Use `scrub: true` for smooth parallax (not `scrub: 1`)
2. Use `force3D: true` for GPU acceleration
3. Use `invalidateOnRefresh: true` for responsive layouts
4. Use `refreshPriority` for elements that need frequent updates
5. Provide fallbacks for when GSAP doesn't load

### CSS Performance Best Practices
1. Use `will-change` sparingly and remove after animation
2. Use `transform` and `opacity` for animations (GPU accelerated)
3. Avoid animating `width`, `height`, `top`, `left` (causes reflow)
4. Use `contain` property for isolated rendering contexts
5. Optimize images (WebP, compression, responsive sizes)

### Responsive Design Best Practices
1. Mobile-first CSS approach
2. Test on actual devices, not just browser DevTools
3. Use `clamp()` for fluid typography
4. Consider container queries for component-level responsiveness
5. Provide fallbacks for JavaScript-dependent features

---

## Implementation Priority Matrix

### Critical (Must Fix Immediately)
1. Issue 14: Hero text disappearing on mobile (blocks content)
2. Issue 16: Dog ear accent not working (core design element)
3. Issue 2: Scroll indicator position (user experience)

### High Priority (Fix Soon)
4. Issue 1: Hero layout matching reference (design integrity)
5. Issue 17: Panorama performance (user experience)
6. Issue 3: Image sizing consistency (visual quality)

### Medium Priority (Fix When Possible)
7. Issue 3b: Icon sizing (visual polish)
8. Issue 11: Mobile hero cut-off (mobile UX)
9. Issue 12: Responsive width handling (desktop UX)

### Low Priority (Nice to Have)
10. Issue 13: Theme toggle alignment (minor mobile UX)
11. Issue 15: Action dock size (minor mobile UX)

---

## Next Steps

1. Create comprehensive implementation plan based on RCAs
2. Prioritize fixes based on impact and complexity
3. Implement fixes systematically, testing after each
4. Document all changes for future reference

