# UI Contracts

This document defines the canonical implementations for all UI motifs in the Kunaal Theme.
Each motif has ONE owner file. Do not duplicate implementations elsewhere.

---

## Z-Index Scale

All z-index values must follow this scale. Do not use values outside this scale without documenting why.

| Layer | Z-Index | Usage |
|-------|---------|-------|
| Base content | 1-2 | Main content, sections |
| Local stacking | 10 | Within-component layering (dog-ear, badges) |
| Elevated content | 100 | Tooltips, popovers within sections |
| Floating UI | 500 | Dropdown menus, autocomplete |
| Sticky elements | 1000 | Sticky headers, sidebars |
| Modal backdrops | 8000 | Overlay backgrounds |
| Fixed header | 9998 | `.mast` navigation bar |
| Filter dropdowns | 9999 | `.topicDropdown`, `.filterPanel.open` |
| Progress bar | 9999 | `.progress` loading indicator |
| Critical overlays | 10000+ | Emergency modals only (avoid) |

---

## Link Underline Motif

**Canonical owner**: `assets/css/utilities.css`

**Behavior**:
- Default state: No underline visible, color remains unchanged
- Hover/focus-visible: Blue underline animates left-to-right (280ms)
- **CRITICAL**: Link color does NOT change on hover - only the underline animates
- Uses `background-image` gradient technique (not `text-decoration`)
- Animation: `background-size: 0% → 100%` with `cubic-bezier(0.4, 0, 0.2, 1)`
- Letter-spacing may increase slightly on hover for visual polish (nav links only)

**Excluded automatically** (via `:not()` selectors):
- Buttons: `[class*="button"]`, `[class*="btn"]`
- Cards: `.card`, `.capsule`, `.media-item`, `.inspiration-item`
- Special elements: `.u-link-plain`, `.skip-link`, `.tTitle`

**Opt-out**: Add `.u-link-plain` class to links that should NOT animate

**CSS Tokens**:
- `--k-underline-blue-color`: Line color (defaults to `var(--blue)`)
- `--k-underline-blue-thickness`: Line thickness (defaults to `2px`)
- `--k-underline-blue-offset`: Vertical offset (defaults to `-1px`)

---

## Section Header Rule

**Canonical owner**: `assets/css/utilities.css` (`.u-section-underline`)

**Behavior**:
- Gray line: Full width, 1px, on parent `.sectionHead` container
- Blue segment: 30px wide, 3px thick, positioned over gray line
- Animation: Scales in from left on page load (600ms, 350ms delay)

**Usage**:
```html
<div class="sectionHead">
  <h2 class="u-section-underline">Title</h2>
</div>
```

---

## Dog-ear Accent

**Canonical owner**: `assets/css/about-page.css`

**Behavior**:
- Blue triangle in top-left corner of `.hero-photo.has-accent`
- CSS triangle via `border` technique (more reliable than `clip-path`)
- Size controlled by `--k-dogear-size` token (default: 28px)

**Important**: Parent must have `overflow: visible !important` to prevent clipping.

---

## Card Overlay Text

**Canonical owner**: `assets/css/components.css`

**Behavior**:
- Text color is ALWAYS white (`#fff`) regardless of light/dark theme
- Applies to `.overlay`, `.tTitle`, `.details` and their children
- Legibility ensured by `.scrim` gradient overlay on images

**Do not**: Override overlay text colors based on theme variables.

---

## Filter Controls

**Canonical owner**: `assets/css/filters.css`

**Behavior**:
- Toolbar has top margin to appear below fixed header
- Dropdown panels have `z-index: 9999` to appear above header
- Mobile filter panel is hidden by default, shown via `.open` class

---

## Dark Mode

**Canonical owner**: `assets/css/dark-mode.css`

**Token overrides**: All core color tokens are redefined for dark backgrounds:
- `--bg`: Dark background (#1A1A1A)
- `--ink`: Light text (#F5F0EB)
- `--blue`: Warm accent (#E07A62) - shifts to coral for contrast

**Scattered overrides**: Some component-specific dark mode rules remain in their owner files
(e.g., `header.css` for nav colors). These should be consolidated over time.

---

## Mobile Responsiveness Contract

**Canonical breakpoints** (consistent across all CSS files):

| Breakpoint | Usage |
|------------|-------|
| `640px` | Mobile phones |
| `760px` | Large phones / nav collapse point |
| `900px` | Tablets |
| `960px` | Small laptops |
| `1200px` | Standard desktop |

### Touch Targets (WCAG 2.1 / Apple HIG)

**Minimum size**: 44x44px for all interactive elements.

Applies to:
- Buttons (`.dockButton`, `.filterToggle`, `.resetBtn`)
- Form controls (`.modernSelect`, `.searchWrap`, inputs)
- Navigation items (`.navToggle`, `.theme-toggle`, nav links)
- Social/action links (`.say-hello-link`, `.contact-socials a`)

### iOS Considerations

**Input font size**: Minimum 16px on all `<input>` and `<textarea>` elements.
- iOS Safari zooms the viewport when focusing inputs with font-size < 16px
- All filter inputs, search fields, form fields must use `font-size: 16px` in mobile breakpoints

**Viewport height**: Use `100svh` with `100vh` fallback.
```css
height: calc(100vh - var(--mastH));  /* Fallback for older browsers */
height: calc(100svh - var(--mastH)); /* Modern browsers - respects iOS address bar */
```

**Width units**: Never use `100vw` - it includes scrollbar width and causes horizontal scroll.
- Use `width: 100%` instead
- Add `overflow-x: hidden` to `html` and `body`

### Horizontal Scroll Prevention

**Canonical owner**: `assets/css/base.css`

Required on all pages:
```css
html { overflow-x: hidden; }
body { overflow-x: hidden; }
```

**Forbidden patterns**:
- `width: 100vw` or `max-width: 100vw`
- Transforms/animations that extend elements beyond viewport without parent `overflow: hidden`
- Negative margins without container overflow control

### Mobile Layout Patterns

**Filters** (`assets/css/filters.css`):
- Stack vertically on mobile
- Full-width controls
- Unified dropdown styling between Topics and Sort

**About Page** (`assets/css/about-page.css`):
- Hero grid: 2x2 mosaic on mobile (hide slots 5-10)
- Numbers: 2x2 CSS Grid
- Capsules: Compact sizing (26px height, 9px font)
- Sections: Reduced padding (2rem 1rem)

**Header** (`assets/css/header.css`):
- Subtitle: Scaled down (10px) with ellipsis, NOT hidden
- Nav dropdown: Full z-index (9999) above all content

---

## Updating This Document

When changing any UI motif:
1. Update this document to reflect changes
2. Verify only the canonical owner file implements the motif
3. Run UI contract checks: `./scripts/check-ui-contracts.sh`

