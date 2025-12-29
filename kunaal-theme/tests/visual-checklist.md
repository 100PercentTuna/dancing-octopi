# Visual Regression Checklist

Run this checklist after any CSS, template, or JavaScript changes that affect the UI.

---

## Viewports to Test

Test at these standard breakpoints:

- [ ] 375px (mobile - iPhone SE)
- [ ] 768px (tablet - iPad portrait)
- [ ] 1024px (small desktop)
- [ ] 1440px (standard desktop)

---

## Themes to Test

- [ ] Light mode
- [ ] Dark mode

---

## About Page

### Hero Section
- [ ] Hero mosaic fills viewport (no white gap above or below)
- [ ] Dog-ear visible on tile 3 (blue triangle, top-left corner)
- [ ] Photos are grayscale at rest
- [ ] Photos reveal color on hover
- [ ] No photos cut off or clipped unexpectedly
- [ ] Hero text readable and properly positioned

### Rabbit Holes (Capsules)
- [ ] Capsule dots show per-category colors (not all same color)
- [ ] Legend dots match capsule dot colors
- [ ] Hover states work correctly
- [ ] Animation on scroll reveal works

### Media Shelves
- [ ] "On the nightstand" row 2 aligns with "On repeat" row 2
- [ ] Titles/subtitles have tight spacing (no large gaps)
- [ ] Long titles truncate with ellipsis
- [ ] Cover images display correctly
- [ ] Play icon visible on hover for digital items

### Places Map
- [ ] Map renders (if Places data is configured in Customizer)
- [ ] Lived countries have distinct color
- [ ] Visited countries have distinct color
- [ ] Current location beacon pulses
- [ ] Legend visible and styled correctly

### Inspirations
- [ ] Cards render (not plain text)
- [ ] Photos display if configured
- [ ] Cards clickable if URL exists
- [ ] Consistent spacing between cards

### Footer
- [ ] Email link centered
- [ ] Footer visible and readable in both themes

---

## Essay/Jotting Archives

### Filters
- [ ] Filters visible below header (not hidden)
- [ ] Filter dropdowns open above header (z-index correct)
- [ ] Filter controls are clickable and functional
- [ ] Mobile filter toggle works

### Cards/Tiles
- [ ] Tile text is WHITE (not black/colored)
- [ ] Tile overlay gradient visible
- [ ] Hover states work (lift, scrim darkens)
- [ ] Title truncation with ellipsis works
- [ ] Details reveal on hover (desktop)

---

## All Pages

### Navigation
- [ ] Nav links visible in dark mode
- [ ] Current page indicator visible
- [ ] Mobile menu toggle works

### Typography
- [ ] No black text on dark backgrounds
- [ ] No white text on light backgrounds
- [ ] Headings properly styled
- [ ] Body text readable

### Links
- [ ] No underline at rest
- [ ] Blue underline animates left-to-right on hover
- [ ] Footer email link hover matches nav links
- [ ] Button-style links excluded from underline animation

### General
- [ ] No console errors (check DevTools)
- [ ] No layout shift on page load
- [ ] Scroll behavior smooth
- [ ] Progress bar visible on long pages

---

## Quick Smoke Test Commands

```bash
# Run UI contract checks
./scripts/check-ui-contracts.sh

# PHP syntax validation
find . -name "*.php" -exec php -l {} \;

# Check for console.log in production JS
grep -r "console.log" --include="*.js" assets/js/

# Check for hardcoded colors
grep -rn "#000\|#111\|#1E5AFF\|color:\s*black" assets/css/ --include="*.css" | grep -v tokens | grep -v variables
```

---

## Reporting Issues

When reporting visual regression issues:

1. **Screenshot**: Capture the issue
2. **Viewport**: Note the screen width
3. **Theme**: Light or dark mode
4. **Browser**: Chrome, Firefox, Safari, Edge
5. **Steps**: How to reproduce
6. **Expected**: What should happen
7. **Actual**: What actually happens

---

## Automated Visual Testing (Future)

For CI integration, consider:

- **Percy** (visual diff testing)
- **Chromatic** (Storybook-based testing)
- **BackstopJS** (open source alternative)

These tools capture screenshots at each commit and highlight visual differences.

Currently tracked as tech debt - see TECH_DEBT.md.

