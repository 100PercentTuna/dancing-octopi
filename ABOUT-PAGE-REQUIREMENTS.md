# About Page Requirements - Comprehensive Summary

## Overview
This document summarizes ALL requirements and asks regarding the About page hero section and related issues, based on the conversation history and reference files.

---

## Issue 1: Hero Images Layout & Dog Ear Accent

### Requirements:
1. **Desktop Layout (Standard Laptop Screen):**
   - Must use EXACT grid structure from reference: `grid-template-columns: 60px 1fr 1fr 1fr minmax(280px, 400px) 60px`
   - Must use `grid-template-rows: 1fr 1fr` (2 rows)
   - Must cover exactly ONE browser viewport height (`min-height: 100vh`)
   - Hero photos must be DIRECT children of `.hero` section (NOT nested in a separate container like `.hero-collage` or `.about-hero-inner`)
   - Photo order in HTML:
     - **Row 1:** Photo 1, Photo 2, Photo 3 (with `has-accent` class), Photo 4, Hero-text (column 5, spans both rows), Photo 5
     - **Row 2:** Photo 6, Photo 7, Photo 8, Photo 9, Photo 10
   - Hero text positioned at `grid-column: 5; grid-row: 1 / 3`
   - No padding on `.hero` section itself (padding handled by grid columns 1 and 6)
   - Hero photos must have `overflow: hidden` (NOT `overflow: visible`)

2. **Dog Ear Accent (Blue Triangle):**
   - Must appear on the third hero photo (index 2, the one with `has-accent` class)
   - Must use CSS pseudo-element `::before` (NOT a DOM element)
   - Must use `linear-gradient(135deg, var(--blue) 50%, transparent 50%)` (NOT clip-path)
   - Must have `z-index: 2` (NOT 10 or 5)
   - Must have `pointer-events: none`
   - Size: `width: 28px; height: 28px`
   - Position: `top: 0; left: 0`
   - Must be visible and work correctly

3. **Mobile Layout:**
   - Grid changes to: `grid-template-columns: 30px 1fr 1fr 30px` (4 columns)
   - Grid rows: `1fr 1fr auto` (3 rows)
   - Hero text moves to `grid-column: 1 / -1; grid-row: 3` (full width, below photos)
   - Hero photos should fill width without excessive whitespace
   - The photo with the dog ear (third photo) should NOT be cut off at the top
   - **Option A:** Move the dog-ear photo to the second row on mobile
   - **Option B:** Ensure the photo grid layout prevents the dog-ear photo from being cut off
   - Photos should mosaic/fill the available space elegantly

4. **Responsive Width Handling:**
   - On different desktop widths, the hero should show MORE of the cropped images (not just get bigger)
   - The first image and other photos should reveal more content as the viewport widens
   - Layout should adapt elegantly across all desktop widths

---

## Issue 2: Scroll Indicator Animation & Position

### Requirements:
1. **Position:**
   - Must be positioned INSIDE the `.hero` section (as a direct child, NOT inside `.hero-text`)
   - Must use `position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);`
   - Must have `z-index: 20`
   - Must be visible and properly positioned

2. **Animation:**
   - Animation must be **vertical (up/down movement)**, NOT horizontal (side-to-side)
   - Must use `translateY` for movement (NOT `translateX`)
   - Animation keyframes:
     ```css
     @keyframes scrollBounce {
       0%, 100% { transform: rotate(45deg) translateY(0); opacity: 1; }
       50% { transform: rotate(45deg) translateY(6px); opacity: 0.5; }
     }
     ```
   - Chevron rotates 45deg (pointing down), then moves down 6px and back up
   - Animation duration: `1.8s ease-in-out infinite`
   - Must NOT animate to the side

3. **Initial State:**
   - Must fade in on page load (via GSAP)
   - Must be visible by default (NOT hidden)

---

## Issue 3: "On Repeat" Image Sizing

### Requirements:
1. **Consistent Sizing:**
   - ALL album cover images in the "On repeat" section must be EXACTLY the same size
   - Must use a robust square aspect ratio technique (1:1)
   - Current implementation uses `aspect-ratio: 1` with `::before` pseudo-element padding technique
   - NO special-case rules for first image or nth-child overrides that cause inconsistencies
   - All images must render at identical dimensions regardless of their source image size

2. **CSS Implementation:**
   - Use `aspect-ratio: 1` on `.media-cover.album`
   - Use `::before` pseudo-element with `padding-top: 100%` and `height: 0` for fallback
   - Image inside must be `position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;`
   - Remove any `nth-child` selectors that might override sizing

---

## Issue 3b: Social Media Icons Size (NEW ISSUE)

### Requirements:
1. **"On Repeat" Section - Media Link Icons:**
   - The SVG icons in `.media-link` (Spotify, Apple Podcasts, etc.) must be appropriately sized
   - Should match the text size (8px font-size for `.media-link`)
   - SVG should be small and proportional to the text

2. **"Say Hello" Section - Social Icons:**
   - LinkedIn and X (Twitter) icons in `.say-hello-social-link` must be appropriately sized
   - Reference shows: `width: 36px; height: 36px` for the link container
   - SVG inside should be: `width: 15px; height: 15px`
   - Icons should NOT be "HORRENDOUSLY large"
   - Must match the aesthetic of other icons on the site

---

## Issue 11: Mobile Hero - Dog Ear Photo Cut Off

### Requirements:
1. **Problem:** The hero image with the dog ear accent is getting cut off at the top on mobile
2. **Solution Options:**
   - **Option A:** Move the dog-ear photo (third photo, index 2) to the second row on mobile
   - **Option B:** Adjust the mobile grid layout to ensure the photo is fully visible
   - **Option C:** Use different photo ordering on mobile to ensure the dog-ear photo is prominent and not cut off
3. **Acceptance Criteria:**
   - Dog-ear photo must be fully visible on mobile
   - Photo should not be cut off at the top
   - Layout should still be elegant and not leave excessive whitespace

---

## Issue 12: Hero Section Across Different Widths

### Requirements:
1. **Problem:** Hero section does not render elegantly across different desktop widths
2. **Requirements:**
   - As viewport width increases, the hero should reveal MORE of the cropped images (especially the first image)
   - Should NOT just "get bigger" - should show more content from the images
   - Layout should adapt gracefully across all desktop widths
   - Grid columns using `1fr` should expand to show more image content
   - Must look elegant and professional at all widths

---

## Issue 13: Mobile Theme Toggle Position & Styling

### Requirements:
1. **Position:**
   - Must be "snug" to the menu (navigation links)
   - Should NOT be "too far left"
   - Should be on the same visual row as the menu (aligned properly)
   - Should NOT be positioned lower than the menu items
   - Must be vertically aligned with the navigation menu items (same baseline/center)
   - Should appear immediately after the last nav link with appropriate spacing

2. **Size:**
   - Must be smaller on mobile (currently 32px × 32px is correct)
   - Icon size: 16px × 16px (currently correct)

3. **Styling:**
   - Must be "stylistically similar" to the menu
   - Menu does NOT have a box around it
   - Theme toggle should NOT have a box (border/background) on mobile
   - Should match the menu's aesthetic (transparent background, no border, no box-shadow)
   - Current implementation has `border: none; background: transparent; box-shadow: none;` - verify this is working

4. **Alignment:**
   - Must be properly aligned with the navigation menu
   - Should appear on the same horizontal line as menu items
   - Spacing should be consistent and "snug"

---

## Issue 14: Hero Text Disappearing on Mobile

### Requirements:
1. **Problem:** The text below "Hi, I'm Kunaal" (the `.hero-intro` paragraph and `.hero-meta` section) does NOT render on mobile at all
2. **Root Cause Analysis:**
   - Text IS in the HTML (confirmed from template: `page-about.php` lines 95-114)
   - Text has `data-reveal="up"` attributes, which trigger GSAP ScrollTrigger animations
   - On mobile, the hero-text is moved to `grid-row: 3` (below photos)
   - GSAP ScrollTrigger may be hiding elements initially and not revealing them on mobile
   - CSS may be hiding elements with `display: none` or `opacity: 0` that never gets reset
   - ScrollTrigger `start: 'top 86%'` may not trigger correctly on mobile if element is below viewport
3. **Requirements:**
   - All hero text content must be visible on mobile
   - `.hero-intro` paragraph must display
   - `.hero-meta` section (Location, Listening, Reading) must display
   - Text should NOT disappear when screen width changes
   - Must work correctly on resize (not require page refresh)
   - Fix GSAP ScrollTrigger to ensure elements are visible on mobile
   - May need to add mobile-specific CSS to ensure visibility
   - May need to adjust ScrollTrigger start position for mobile
   - May need to force visibility on mobile if ScrollTrigger doesn't fire

---

## Issue 15: Mobile Action Dock Icons

### Requirements:
1. **Problem:** Share, subscribe, and download icons in the action dock are "too large and obstructive" on mobile
2. **Requirements:**
   - Icons must be smaller on mobile
   - Current implementation: `32px × 32px` buttons with `14px × 14px` icons
   - May need to be even smaller or positioned differently
   - Should NOT be obstructive or take up too much screen space
   - Should be less prominent on mobile devices

---

## Issue 16: Dog Ear Accent Still Not Working

### Requirements:
1. **Problem:** The blue dog ear accent on the third hero image is still not visible
2. **Root Cause Analysis Needed:**
   - Verify the `has-accent` class is correctly applied to the third photo (index 2)
   - Verify CSS selector `.hero-photo.has-accent::before` is correct
   - Verify `z-index: 2` is sufficient (may need to be higher if photo has `z-index: 1`)
   - Verify `overflow: hidden` on `.hero-photo` is NOT clipping the accent (may need `overflow: visible` on the photo container, or adjust z-index)
   - Verify the gradient is correct: `linear-gradient(135deg, var(--blue) 50%, transparent 50%)`
   - Verify the accent is positioned correctly: `top: 0; left: 0; width: 28px; height: 28px`
   - Check for CSS conflicts or specificity issues

3. **Solution:**
   - Ensure the accent is a CSS `::before` pseudo-element (NOT a DOM element)
   - Ensure proper z-index stacking (accent must be above the image)
   - May need to adjust `overflow` property on `.hero-photo` or use a different approach
   - Must be visible and work correctly on both desktop and mobile

---

## Additional Requirements from Reference HTML

### Scroll Indicator (Reference Implementation):
- Position: `position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);`
- Inside `.hero` section (NOT inside `.hero-text`)
- Animation: `@keyframes bounce` with `translateY` movement (vertical, not horizontal)

### Hero Photo Overflow:
- Reference shows: `overflow: hidden` on `.hero-photo`
- This may need to be `overflow: visible` if the dog ear is being clipped, OR the z-index needs adjustment

### Say Hello Section (Reference):
- `.say-hello-link` containers: `width: 36px; height: 36px`
- SVG icons: `width: 15px; height: 15px`
- Must match this sizing exactly

### Media Link (Reference):
- `.media-link` uses text with an arrow character: `↗ Spotify`
- Current implementation uses SVG icon - may need to adjust sizing

---

## Issue 17: Panorama Parallax Performance (NEW REQUIREMENT)

### Requirements:
1. **Problem:** Panorama images are lagging when scrolling, making the parallax effect feel choppy and unrealistic
2. **Requirements:**
   - Panorama parallax scrolling must be SMOOTH and feel "real" like the background is actually there
   - Must feel realistic and not laggy
   - Should use hardware acceleration (CSS `transform` and `will-change`)
   - GSAP ScrollTrigger `scrub: 1` may need adjustment (try `scrub: true` or `scrub: 0.5` for smoother feel)
   - May need to use `force3D: true` in GSAP for better performance
   - May need to optimize image loading/sizing
   - Should feel as smooth as native scrolling
   - Performance should be consistent across devices (desktop and mobile)
3. **Technical Implementation:**
   - Current implementation uses `yPercent` animation with `scrub: 1` (in `about-page-v22.js` line 140)
   - Current CSS: `.panorama-img` has `object-position: center bottom` and `height: 160%` (or 140% for h-140)
   - **Performance Optimizations Needed:**
     - Add `will-change: transform` to `.panorama-img` for browser optimization
     - Add `transform: translateZ(0)` or `force3D: true` in GSAP for hardware acceleration
     - Consider changing `scrub: 1` to `scrub: true` (smoother interpolation) or `scrub: 0.5` (faster response)
     - Ensure images are optimized (not too large file size)
     - May need to use `contain: layout style paint` for better rendering performance
     - Consider using `requestAnimationFrame` optimization if needed
   - The parallax should feel "real" - like the background is actually moving, not lagging behind

---

## Clarifications Received

1. **Hero Mobile Layout (Issue 11):** Use different photo ordering on mobile (dog-ear photo in a safer position)
2. **Action Dock Mobile (Issue 15):** Make them smaller (e.g., 28px buttons, 12px icons)
3. **Media Link Icons (Issue 3b):** Keep SVG icons but make them much smaller to match text size
4. **Hero Text on Mobile (Issue 14):** Text is in HTML - need to investigate why it's not visible (likely GSAP/CSS issue)
5. **Theme Toggle Alignment (Issue 13):** Same visual row, properly aligned vertically with nav links

---

## Technical Notes

- Reference HTML file: `reference-files/kunaal-about-v22-polished.html`
- Current "bad" version: `reference-files/About - Kunaal _ Essays & Jottings -- bad version 2512271531.html`
- Current source dump: `reference-files/About - Current Source 2512271531.html` (saved for RCA)
- All fixes must maintain WordPress best practices
- Code must be non-bloated, efficient, and well-commented
- Must work across all screen sizes and browsers
- Must respect reduced motion preferences
- Must be accessible (ARIA labels, keyboard navigation, etc.)

---

## Priority Order

Based on user feedback, the issues are listed in order of mention. However, the most critical are:
1. Hero layout matching reference exactly (Issue 1)
2. Dog ear accent working (Issue 16)
3. Scroll indicator animation fixed (Issue 2)
4. Mobile hero text visible (Issue 14)
5. Panorama parallax smoothness (Issue 17) - NEW REQUIREMENT
6. Image sizing consistency (Issue 3)
7. Icon sizing fixes (Issue 3b, 15)
8. Mobile layout improvements (Issues 11, 12, 13)

