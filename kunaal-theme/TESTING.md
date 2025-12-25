# Testing Checklist - Version 3.9.0

**Release Date**: December 25, 2025  
**Version**: 3.9.0  
**Testing Scope**: New blocks, redesigned blocks, bug fixes, pattern cleanup

---

## Pre-Testing Setup

- [ ] Clear browser cache
- [ ] Test in incognito/private mode
- [ ] Test on multiple browsers (Chrome, Safari, Firefox, Edge)
- [ ] Test on multiple devices (Desktop, Tablet, Mobile)
- [ ] Check WordPress admin for any errors/warnings

---

## 1. NEW BLOCKS - Functionality Tests

### 1.1 Footnote Block
- [ ] **Editor**: Can add footnote block from block inserter
- [ ] **Editor**: Footnote content field is editable
- [ ] **Editor**: Preview shows numbered reference marker
- [ ] **Frontend**: Footnote reference appears as superscript number in text
- [ ] **Frontend**: Clicking footnote reference scrolls to footnote at bottom
- [ ] **Frontend**: Clicking footnote number at bottom scrolls back to reference
- [ ] **Frontend**: Smooth scroll animation works
- [ ] **Frontend**: URL hash updates correctly (#fn-1, etc)
- [ ] **Frontend**: Direct navigation via URL hash works
- [ ] **Frontend**: Multiple footnotes auto-number correctly (1, 2, 3...)
- [ ] **Visual**: Reference marker is blue and clickable
- [ ] **Visual**: Footnotes section has proper styling at bottom

### 1.2 Footnotes Section Block
- [ ] **Editor**: Can add footnotes section block
- [ ] **Editor**: Shows placeholder text when no footnotes exist
- [ ] **Editor**: Title field is editable in sidebar
- [ ] **Frontend**: Renders all footnotes in order
- [ ] **Frontend**: Only appears if footnotes exist in content
- [ ] **Visual**: Proper spacing and typography

### 1.3 Parallax Section Block
- [ ] **Editor**: Can add parallax section block
- [ ] **Editor**: Can upload/select background image
- [ ] **Editor**: Can remove background image
- [ ] **Editor**: Min height selector works (40vh, 60vh, 80vh, 100vh)
- [ ] **Editor**: Overlay darkness slider works (0-90%)
- [ ] **Editor**: Parallax intensity slider works (0-50%)
- [ ] **Editor**: Content alignment selector works (left, center, right)
- [ ] **Editor**: InnerBlocks allow adding content
- [ ] **Frontend**: Background image displays correctly
- [ ] **Frontend**: Parallax effect works on scroll (desktop)
- [ ] **Frontend**: Parallax disabled on mobile/touch devices
- [ ] **Frontend**: Overlay opacity matches settings
- [ ] **Frontend**: Content alignment matches settings
- [ ] **Visual**: Full-width alignment works (alignwide, alignfull)
- [ ] **Visual**: Text is readable over background

### 1.4 Scrollytelling Block
- [ ] **Editor**: Can add scrollytelling block
- [ ] **Editor**: Sticky label field is editable
- [ ] **Editor**: Sticky title is editable (RichText)
- [ ] **Editor**: Sticky description is editable (RichText)
- [ ] **Editor**: Sticky position selector works (left/right)
- [ ] **Editor**: Can add scrolly-step child blocks
- [ ] **Frontend**: Sticky panel stays in viewport while scrolling
- [ ] **Frontend**: Steps activate as they scroll into view
- [ ] **Frontend**: Sticky content updates when step becomes active
- [ ] **Frontend**: Works on both left and right positions
- [ ] **Frontend**: Stacks vertically on mobile
- [ ] **Visual**: Proper borders and spacing
- [ ] **Visual**: Active step is highlighted

### 1.5 Scrolly Step Block
- [ ] **Editor**: Can only be added inside scrollytelling block
- [ ] **Editor**: Step number displays correctly
- [ ] **Editor**: Sticky title update field works (optional)
- [ ] **Editor**: Sticky description update field works (optional)
- [ ] **Editor**: InnerBlocks allow adding content
- [ ] **Frontend**: Steps render in order
- [ ] **Frontend**: Data attributes pass correctly to parent

### 1.6 Reveal Wrapper Block
- [ ] **Editor**: Can add reveal wrapper block
- [ ] **Editor**: Animation type selector works (fade-up, slide-left, etc)
- [ ] **Editor**: Delay slider works (0-1000ms)
- [ ] **Editor**: Duration slider works (200-1500ms)
- [ ] **Editor**: Threshold slider works (0-50%)
- [ ] **Editor**: InnerBlocks allow adding content
- [ ] **Frontend**: Content is hidden initially
- [ ] **Frontend**: Animation triggers when scrolling into view
- [ ] **Frontend**: All animation types work correctly
- [ ] **Frontend**: Respects prefers-reduced-motion (no animation)
- [ ] **Visual**: Smooth transitions

---

## 2. REDESIGNED BLOCKS - Visual & Functional Tests

### 2.1 Sidenote Block (Complete Redesign)
- [ ] **Editor**: No marker symbol field (removed)
- [ ] **Editor**: Only content field is editable
- [ ] **Editor**: Preview shows reference marker
- [ ] **Frontend**: Sidenote appears in RIGHT margin (not inline)
- [ ] **Frontend**: Uses Garfield Signature font (custom font loads)
- [ ] **Frontend**: Auto-numbered (1, 2, 3...)
- [ ] **Frontend**: Reference marker is clickable
- [ ] **Frontend**: Works at all zoom levels (100%, 125%, 150%)
- [ ] **Frontend**: On mobile/tablet (< 1000px): becomes expandable inline note
- [ ] **Frontend**: Mobile toggle works (click to show/hide)
- [ ] **Visual**: Font looks sophisticated (not childish)
- [ ] **Visual**: Proper spacing in margin
- [ ] **Visual**: Border-left styling on sidenote

### 2.2 Takeaways Block (Redesign)
- [ ] **Editor**: Title field is editable
- [ ] **Editor**: Can add takeaway-item blocks
- [ ] **Frontend**: Large accent numbers (01, 02, 03) on left
- [ ] **Frontend**: Each item is visually distinct (card-like)
- [ ] **Frontend**: NOT collapsible (always visible)
- [ ] **Visual**: Clearly different from accordion block
- [ ] **Visual**: Numbers are prominent and styled
- [ ] **Visual**: Hover state on numbers works

### 2.3 Citation Block (Redesign)
- [ ] **Editor**: Quote field is editable (RichText)
- [ ] **Editor**: Author field works
- [ ] **Editor**: Source text field works
- [ ] **Editor**: Source URL field works
- [ ] **Frontend**: Centered layout
- [ ] **Frontend**: Decorative opening quote mark (large, faded)
- [ ] **Frontend**: Em-dash before author
- [ ] **Frontend**: Source link works (if provided)
- [ ] **Visual**: Elegant serif typography
- [ ] **Visual**: Border-top and border-bottom
- [ ] **Visual**: Distinct from pullquote block

### 2.4 Aside Block (Redesign)
- [ ] **Editor**: Label type selector works (Case Study, Example, Note, etc)
- [ ] **Editor**: Custom label field appears when "Custom" selected
- [ ] **Editor**: Outcome field works
- [ ] **Editor**: InnerBlocks allow adding content
- [ ] **Frontend**: Label appears at top (if selected)
- [ ] **Frontend**: Warm background color
- [ ] **Frontend**: Outcome appears at bottom (if provided)
- [ ] **Frontend**: Different label types render correctly
- [ ] **Visual**: Warning type has red border
- [ ] **Visual**: Note type has blue border
- [ ] **Visual**: Visually distinct from other blocks

---

## 3. BUG FIXES - Verification Tests

### 3.1 Header Z-Index Fix
- [ ] **Desktop (narrow)**: Content does NOT appear above header
- [ ] **Desktop (narrow)**: Header stays on top at all widths
- [ ] **All widths**: Header is always visible and clickable
- [ ] **Scroll**: Header remains fixed correctly

### 3.2 Footer Spacing Fix
- [ ] **Mobile (< 768px)**: Footer stacks vertically
- [ ] **Mobile**: Gap between footer elements is tight (0.25rem)
- [ ] **Mobile**: Footer doesn't take excessive vertical space
- [ ] **Desktop**: Footer layout unchanged (horizontal)

---

## 4. PATTERN CLEANUP - Verification

- [ ] **WordPress Admin**: Old HTML patterns no longer appear in pattern inserter
- [ ] **WordPress Admin**: Only proper Gutenberg blocks appear
- [ ] **Existing Content**: Old pattern-based content still renders (backward compatibility)
- [ ] **No Errors**: No PHP errors in WordPress debug log

---

## 5. RESPONSIVE DESIGN TESTS

### Desktop (1280px+)
- [ ] All blocks render correctly
- [ ] Sidenotes appear in margin
- [ ] Parallax effects work
- [ ] Scrollytelling sticky panel works

### Tablet (768px - 1279px)
- [ ] Blocks adapt appropriately
- [ ] Sidenotes become expandable inline
- [ ] Parallax disabled (performance)
- [ ] Scrollytelling stacks vertically

### Mobile (< 768px)
- [ ] All blocks are readable
- [ ] Touch interactions work
- [ ] Footer spacing is tight
- [ ] No horizontal scrolling

---

## 6. ACCESSIBILITY TESTS

- [ ] **Keyboard Navigation**: All interactive elements are focusable
- [ ] **Keyboard Navigation**: Tab order is logical
- [ ] **Screen Reader**: Footnote references have proper ARIA labels
- [ ] **Screen Reader**: Sidenote references are announced correctly
- [ ] **Focus States**: All interactive elements have visible focus indicators
- [ ] **Reduced Motion**: Animations respect prefers-reduced-motion
- [ ] **Color Contrast**: All text meets WCAG AA standards
- [ ] **Alt Text**: Images in blocks have proper alt text

---

## 7. PERFORMANCE TESTS

- [ ] **Font Loading**: Garfield Signature font loads correctly
- [ ] **Font Loading**: Font doesn't cause layout shift
- [ ] **JavaScript**: No console errors
- [ ] **JavaScript**: Parallax uses requestAnimationFrame (smooth)
- [ ] **JavaScript**: Intersection Observer used efficiently
- [ ] **CSS**: No layout shifts on page load
- [ ] **Mobile**: Parallax disabled for performance

---

## 8. CROSS-BROWSER TESTS

### Chrome
- [ ] All blocks render correctly
- [ ] Animations work smoothly
- [ ] Parallax works

### Safari
- [ ] All blocks render correctly
- [ ] backdrop-filter works (header)
- [ ] Animations work

### Firefox
- [ ] All blocks render correctly
- [ ] Animations work

### Edge
- [ ] All blocks render correctly
- [ ] All features work

---

## 9. INTEGRATION TESTS

- [ ] **Existing Content**: Old posts with patterns still display
- [ ] **Block Inserter**: All new blocks appear in correct categories
- [ ] **Block Inserter**: Categories are "Kunaal — Editorial" and "Kunaal — Interactive"
- [ ] **PDF Export**: Footnotes render correctly in PDF
- [ ] **PDF Export**: Sidenotes render correctly in PDF
- [ ] **Search**: Content in new blocks is searchable

---

## 10. EDGE CASES

- [ ] **Empty Blocks**: Blocks handle empty content gracefully
- [ ] **Multiple Instances**: Multiple footnotes number correctly
- [ ] **Multiple Instances**: Multiple sidenotes number correctly
- [ ] **Nested Content**: InnerBlocks work in all blocks
- [ ] **Long Content**: Blocks handle long text gracefully
- [ ] **Special Characters**: Special characters render correctly
- [ ] **Links**: Links in block content work correctly

---

## Testing Notes Template

**Tester**: _______________  
**Date**: _______________  
**Browser**: _______________  
**Device**: _______________  

**Issues Found**:
1. 
2. 
3. 

**Pass/Fail Summary**:
- New Blocks: ___ / 36 tests
- Redesigned Blocks: ___ / 28 tests
- Bug Fixes: ___ / 4 tests
- Pattern Cleanup: ___ / 4 tests
- Responsive: ___ / 12 tests
- Accessibility: ___ / 8 tests
- Performance: ___ / 7 tests
- Cross-Browser: ___ / 16 tests
- Integration: ___ / 6 tests
- Edge Cases: ___ / 7 tests

**Total**: ___ / 128 tests

---

## Quick Smoke Test (5 minutes)

If time is limited, test these critical items:

1. ✅ Sidenote appears in margin (desktop)
2. ✅ Footnote bidirectional linking works
3. ✅ Parallax section renders with image
4. ✅ Header doesn't overlap content
5. ✅ Footer spacing is tight on mobile
6. ✅ All blocks appear in inserter
7. ✅ No console errors

---

**Last Updated**: Version 3.9.0 - December 25, 2025

