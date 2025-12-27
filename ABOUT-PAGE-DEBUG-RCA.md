# About Page Debug - Root Cause Analysis

## Issues Reported
1. **Dog-ear not showing on desktop** (but shows on mobile, too large)
2. **Scroll indicator not visible**
3. **Map disappeared** (was working before)
4. **Hero text disappearing on resize**
5. **Social icons too large** at bottom of About page
6. **Contact page background whitespace** above/below
7. **X/Twitter text wrapping** to two lines
8. **Theme toggle not aligned/snug** with menu
9. **Dog-ear/hero photo cut-off on mobile**

---

## Hypothesis Set 1: Dog-ear Not Showing on Desktop

**H1.1:** The `::before` pseudo-element is being rendered but is behind the image due to stacking context issues
- **Evidence needed:** Check computed z-index values, stacking context creation
- **Test:** Log computed styles for `.hero-photo.has-accent::before` and `.hero-photo.has-accent img`

**H1.2:** The `isolation: isolate` is creating a new stacking context that isolates the accent from the image
- **Evidence needed:** Check if removing isolation fixes it, or if image needs negative z-index
- **Test:** Log whether isolation is applied and if image has any z-index

**H1.3:** The image element itself has a stacking context (position:relative or z-index) that puts it above the ::before
- **Evidence needed:** Check computed styles for `.hero-photo.has-accent img`
- **Test:** Log position, z-index, and transform values

**H1.4:** CSS variable `--blue` is not defined or is transparent
- **Evidence needed:** Check computed background color of ::before element
- **Test:** Log computed background value

**H1.5:** The accent is being clipped by `overflow: hidden` on `.hero-photo`
- **Evidence needed:** Check if accent extends beyond photo bounds
- **Test:** Log computed overflow and clip-path values

---

## Hypothesis Set 2: Scroll Indicator Not Visible

**H2.1:** GSAP animation is setting opacity to 0 and not completing
- **Evidence needed:** Check if GSAP animation runs and final opacity value
- **Test:** Log GSAP animation state and computed opacity

**H2.2:** Element is positioned off-screen or has display:none
- **Evidence needed:** Check computed position, display, visibility
- **Test:** Log getBoundingClientRect() and computed display/visibility

**H2.3:** Z-index is too low and element is behind other content
- **Evidence needed:** Check z-index and stacking order
- **Test:** Log computed z-index and parent z-index values

**H2.4:** CSS animation is broken or not applied
- **Evidence needed:** Check if scrollBounce animation is active
- **Test:** Log computed animation properties

**H2.5:** Element exists in DOM but CSS is hiding it (opacity:0, visibility:hidden, etc.)
- **Evidence needed:** Check all visibility-related CSS properties
- **Test:** Log all computed styles affecting visibility

---

## Hypothesis Set 3: Map Disappeared

**H3.1:** D3.js or TopoJSON library not loaded when initWorldMap() runs
- **Evidence needed:** Check if window.d3 and window.topojson exist
- **Test:** Log library availability at init time

**H3.2:** Element #world-map not found in DOM when initWorldMap() runs
- **Evidence needed:** Check if element exists when function is called
- **Test:** Log element existence check

**H3.3:** Places data is empty or malformed
- **Evidence needed:** Check window.kunaalAboutV22.places structure
- **Test:** Log places data structure and values

**H3.4:** D3.js rendering fails silently (error in draw function)
- **Evidence needed:** Check for JavaScript errors in console
- **Test:** Wrap draw() in try-catch and log errors

**H3.5:** Map container has zero width/height
- **Evidence needed:** Check host.clientWidth and host.clientHeight
- **Test:** Log container dimensions

---

## Hypothesis Set 4: Hero Text Disappearing on Resize

**H4.1:** ScrollTrigger not refreshing on window resize
- **Evidence needed:** Check if ScrollTrigger.refresh() is called
- **Test:** Log resize events and ScrollTrigger refresh calls

**H4.2:** GSAP animations are setting opacity:0 and not reversing
- **Evidence needed:** Check ScrollTrigger toggleActions and animation state
- **Test:** Log animation state after resize

**H4.3:** Elements are positioned off-screen after resize
- **Evidence needed:** Check getBoundingClientRect() after resize
- **Test:** Log element positions before/after resize

**H4.4:** CSS media queries are hiding elements
- **Evidence needed:** Check computed display/visibility at different widths
- **Test:** Log styles at different viewport widths

---

## Hypothesis Set 5: Social Icons Too Large

**H5.1:** Inline styles or other CSS overriding the !important rules
- **Evidence needed:** Check computed width/height vs CSS rules
- **Test:** Log computed SVG dimensions

**H5.2:** SVG viewBox is causing scaling issues
- **Evidence needed:** Check SVG viewBox attribute
- **Test:** Log SVG attributes

**H5.3:** Parent container is forcing larger size
- **Evidence needed:** Check parent flex/grid constraints
- **Test:** Log parent container styles

---

## Hypothesis Set 6: Contact Page Background Whitespace

**H6.1:** min-height: 100vh doesn't account for header height
- **Evidence needed:** Check computed height vs viewport
- **Test:** Log computed min-height and viewport height

**H6.2:** Negative margin not working correctly
- **Evidence needed:** Check computed margin-top
- **Test:** Log margin values

**H6.3:** Background not covering full area due to padding
- **Evidence needed:** Check padding and background-size
- **Test:** Log padding and background properties

---

## Hypothesis Set 7: X/Twitter Text Wrapping

**H7.1:** white-space: nowrap not applied
- **Evidence needed:** Check computed white-space property
- **Test:** Log computed white-space value

**H7.2:** Container width too narrow
- **Evidence needed:** Check container width vs text width
- **Test:** Log container and text dimensions

---

## Hypothesis Set 8: Theme Toggle Alignment

**H8.1:** align-self: center not working due to flex direction
- **Evidence needed:** Check flex-direction and align-items
- **Test:** Log flex container properties

**H8.2:** Margin values not reducing gap enough
- **Evidence needed:** Check computed margin-left
- **Test:** Log margin values

**H8.3:** Vertical alignment offset by line-height or font-size
- **Evidence needed:** Check line-height and vertical alignment
- **Test:** Log typography and alignment properties

---

## Hypothesis Set 9: Dog-ear Too Large / Photo Cut-off on Mobile

**H9.1:** Mobile media query not applying correctly
- **Evidence needed:** Check if @media (max-width: 900px) is active
- **Test:** Log viewport width and media query match

**H9.2:** object-position cutting off top area
- **Evidence needed:** Check object-position value
- **Test:** Log object-position computed value

**H9.3:** Grid reordering placing photo in wrong position
- **Evidence needed:** Check CSS order and grid-row
- **Test:** Log computed order and grid-row values

