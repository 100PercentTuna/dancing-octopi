# Testing Checklist - Version 4.0.0

**Release Date**: December 25, 2025  
**Version**: 4.0.0 (Major Release)  
**Testing Scope**: Epic 2 Complete (All Editorial Blocks), Sidenote Fix

---

## Pre-Testing Setup

- [ ] Clear browser cache
- [ ] Test in incognito/private mode
- [ ] Test on multiple browsers (Chrome, Safari, Firefox, Edge)
- [ ] Test on multiple devices (Desktop, Tablet, Mobile)
- [ ] Check WordPress admin for any errors/warnings
- [ ] Ensure theme is updated to v4.0.0

---

## 1. SIDENOTE FIX (Critical)

### 1.1 Font Loading
- [ ] Garfield Signature font loads correctly
- [ ] Font appears on desktop view
- [ ] Font appears after page refresh
- [ ] No console errors about font loading

### 1.2 Marker Change (Number → Blue Bullet)
- [ ] Sidenote marker is now a blue bullet (•) NOT a number
- [ ] Bullet is visually distinct from footnote numbers
- [ ] Bullet color is theme blue (#1E5AFF)
- [ ] Hover effect works (slight scale/color change)

### 1.3 Position & Behavior
- [ ] Sidenote appears in right margin on desktop
- [ ] Sidenote expands inline on mobile/tablet
- [ ] Click/tap to show sidenote works on mobile
- [ ] Multiple sidenotes on same page work correctly

---

## 2. NEW EPIC 2 BLOCKS - Magazine Figure

### 2.1 Magazine Figure Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Can select/upload image
- [ ] **Editor**: Can change image
- [ ] **Editor**: Caption field is editable (RichText)
- [ ] **Editor**: Photo credit field works
- [ ] **Editor**: Size selector works (default, wide, full)
- [ ] **Frontend**: Image renders correctly
- [ ] **Frontend**: Caption appears below image
- [ ] **Frontend**: Credit appears right-aligned
- [ ] **Frontend**: Wide/full alignments work
- [ ] **Visual**: Border-top separator on caption
- [ ] **Visual**: Italic caption, mono credit styling

---

## 3. NEW EPIC 2 BLOCKS - Lede Package

### 3.1 Lede Package Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Can select background image
- [ ] **Editor**: Headline field is editable
- [ ] **Editor**: Dek/subheadline field is editable
- [ ] **Editor**: Layout selector works (overlay, below, split)
- [ ] **Editor**: Photo credit field works
- [ ] **Frontend**: Overlay layout - text over image with gradient
- [ ] **Frontend**: Below layout - image above, centered text below
- [ ] **Frontend**: Split layout - image left, text right
- [ ] **Frontend**: Credit appears in correct position per layout
- [ ] **Visual**: Responsive behavior on mobile
- [ ] **Visual**: Typography scales correctly (clamp)

---

## 4. NEW EPIC 2 BLOCKS - Timeline

### 4.1 Timeline Block (Parent)
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Title field is optional and works
- [ ] **Editor**: Orientation selector (vertical/horizontal) works
- [ ] **Editor**: Can add timeline-item child blocks
- [ ] **Frontend**: Title renders if set
- [ ] **Frontend**: Timeline line appears (vertical or horizontal)
- [ ] **Visual**: Proper spacing and alignment

### 4.2 Timeline Item Block (Child)
- [ ] **Editor**: Can only add inside timeline block
- [ ] **Editor**: Date field is editable
- [ ] **Editor**: Title field is editable
- [ ] **Editor**: Description field is editable
- [ ] **Frontend**: Marker (blue dot) appears on timeline
- [ ] **Frontend**: Date appears as accent label
- [ ] **Frontend**: Title and description render correctly
- [ ] **Visual**: Hover state on markers
- [ ] **Visual**: Mobile stacking works

---

## 5. NEW EPIC 2 BLOCKS - Glossary

### 5.1 Glossary Block (Parent)
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Title field works (default: "Key Terms")
- [ ] **Editor**: Can add glossary-term child blocks
- [ ] **Frontend**: Title renders correctly
- [ ] **Frontend**: Warm background with blue left border
- [ ] **Visual**: Proper styling and spacing

### 5.2 Glossary Term Block (Child)
- [ ] **Editor**: Can only add inside glossary block
- [ ] **Editor**: Term field is editable
- [ ] **Editor**: Definition field is editable
- [ ] **Frontend**: Term appears as heading
- [ ] **Frontend**: Definition appears below
- [ ] **Frontend**: Separator between terms
- [ ] **Visual**: Serif term, muted definition

---

## 6. NEW EPIC 2 BLOCKS - Inline Annotation

### 6.1 Annotation Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Highlighted text field is editable
- [ ] **Editor**: Annotation note field works
- [ ] **Editor**: Highlight color selector works (yellow, blue, green, pink)
- [ ] **Frontend**: Text appears highlighted with correct color
- [ ] **Frontend**: Tooltip appears on hover
- [ ] **Frontend**: Tooltip appears on focus (keyboard)
- [ ] **Frontend**: Mobile tap to show/hide works
- [ ] **Visual**: Smooth tooltip animation
- [ ] **Visual**: Arrow pointing to highlighted text
- [ ] **Accessibility**: Proper aria attributes

---

## 7. NEW EPIC 2 BLOCKS - Argument Map

### 7.1 Argument Map Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Claim field is editable (RichText)
- [ ] **Editor**: Can add supporting points
- [ ] **Editor**: Can add counter-arguments
- [ ] **Editor**: Can remove points/arguments
- [ ] **Frontend**: Claim appears at top with dark background
- [ ] **Frontend**: Supporting column (green tint) renders
- [ ] **Frontend**: Opposing column (red tint) renders
- [ ] **Frontend**: Two-column layout on desktop
- [ ] **Frontend**: Stacks on mobile
- [ ] **Visual**: Green/red markers for each column
- [ ] **Visual**: Proper typography and spacing

---

## 8. NEW EPIC 2 BLOCKS - What We Know / Don't Know

### 8.1 Know/Don't Know Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Can add "What We Know" items
- [ ] **Editor**: Can add "What We Don't Know" items
- [ ] **Editor**: Can remove items
- [ ] **Frontend**: Two columns with distinct styling
- [ ] **Frontend**: Green "Know" column with checkmarks
- [ ] **Frontend**: Orange "Don't Know" column with question marks
- [ ] **Frontend**: Stacks on mobile
- [ ] **Visual**: Border colors match column themes
- [ ] **Visual**: Icons in list items

---

## 9. NEW EPIC 2 BLOCKS - Primary Source Excerpt

### 9.1 Source Excerpt Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Content field is editable (RichText)
- [ ] **Editor**: Source type selector works (document, letter, transcript, report, legal)
- [ ] **Editor**: Source name field works
- [ ] **Editor**: Date field works
- [ ] **Editor**: Source URL field works
- [ ] **Frontend**: Type label appears at top
- [ ] **Frontend**: Content renders with document styling
- [ ] **Frontend**: Attribution footer with source and date
- [ ] **Frontend**: Source links correctly if URL provided
- [ ] **Visual**: Parchment-like background (yellow tint)
- [ ] **Visual**: Decorative corner borders
- [ ] **Visual**: Serif typography

---

## 10. NEW EPIC 2 BLOCKS - Context Panel

### 10.1 Context Panel Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Label selector works (Context, Background, Why It Matters, etc.)
- [ ] **Editor**: Custom label appears when "Custom" selected
- [ ] **Editor**: InnerBlocks allow adding content
- [ ] **Frontend**: Label appears at top (blue, uppercase)
- [ ] **Frontend**: Content renders correctly
- [ ] **Frontend**: Blue left border
- [ ] **Visual**: Gradient background (subtle blue tint)
- [ ] **Visual**: Proper spacing for nested paragraphs/lists

---

## 11. NEW EPIC 2 BLOCKS - Related Reading

### 11.1 Related Reading Block (Parent)
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Title field works (default: "Further Reading")
- [ ] **Editor**: Can add related-link child blocks
- [ ] **Frontend**: Title renders as section header
- [ ] **Frontend**: Warm background with top/bottom borders
- [ ] **Visual**: Proper spacing between items

### 11.2 Related Link Block (Child)
- [ ] **Editor**: Can only add inside related-reading block
- [ ] **Editor**: URL field works
- [ ] **Editor**: Title field is editable
- [ ] **Editor**: Source field is editable
- [ ] **Editor**: Description field is editable (optional)
- [ ] **Frontend**: Arrow icon appears
- [ ] **Frontend**: Title links correctly (if URL provided)
- [ ] **Frontend**: Source and description render
- [ ] **Frontend**: Hover state with arrow animation
- [ ] **Visual**: Mono source styling

---

## 12. EXISTING BLOCKS VERIFICATION

### 12.1 Previous v3.9.0 Blocks Still Work
- [ ] Insight block renders correctly
- [ ] Pullquote block renders correctly
- [ ] Accordion block opens/closes with animation
- [ ] Footnote block links work bidirectionally
- [ ] Footnotes Section collects all footnotes
- [ ] Parallax Section has parallax effect
- [ ] Scrollytelling sticky panel works
- [ ] Reveal Wrapper animations trigger on scroll
- [ ] Takeaways block shows numbered items
- [ ] Citation block renders centered
- [ ] Aside block shows label and outcome
- [ ] Section Header block renders correctly

---

## 13. RESPONSIVE DESIGN

### Desktop (1280px+)
- [ ] All blocks render correctly
- [ ] Sidenotes appear in margin
- [ ] Two-column blocks display columns
- [ ] Full-width alignments work

### Tablet (768px - 1279px)
- [ ] Blocks adapt to narrower width
- [ ] Sidenotes become expandable inline
- [ ] Two-column blocks may stack or shrink

### Mobile (< 768px)
- [ ] All blocks stack vertically
- [ ] Text is readable
- [ ] Touch interactions work
- [ ] No horizontal scrolling
- [ ] Timeline stacks correctly
- [ ] Argument Map stacks correctly

---

## 14. ACCESSIBILITY

- [ ] **Keyboard**: All interactive elements are focusable
- [ ] **Keyboard**: Tab order is logical
- [ ] **Screen Reader**: Proper heading hierarchy
- [ ] **Screen Reader**: Annotation tooltips have aria attributes
- [ ] **Motion**: prefers-reduced-motion honored for animations
- [ ] **Contrast**: Text is readable on all backgrounds
- [ ] **Focus**: Visible focus indicators

---

## 15. PERFORMANCE

- [ ] **No console errors**: Check browser console
- [ ] **No PHP errors**: Check WordPress debug log
- [ ] **Fast load**: Page loads within 3 seconds
- [ ] **Font loading**: Garfield Signature loads via PHP inline style
- [ ] **Asset loading**: Block styles only load when block is used

---

## 16. CROSS-BROWSER

### Chrome (latest)
- [ ] All blocks render correctly
- [ ] Animations work
- [ ] Fonts load

### Safari (latest)
- [ ] All blocks render correctly
- [ ] Animations work (webkit prefixes)
- [ ] Fonts load

### Firefox (latest)
- [ ] All blocks render correctly
- [ ] Animations work
- [ ] Fonts load

### Edge (latest)
- [ ] All blocks render correctly
- [ ] Animations work
- [ ] Fonts load

---

## 17. PDF EXPORT

- [ ] New blocks render in PDF correctly
- [ ] Argument Map columns visible
- [ ] Timeline renders (vertical only recommended)
- [ ] Glossary terms display
- [ ] Source Excerpt styling preserved
- [ ] Sidenotes simplified for print

---

## 18. BLOCK CATEGORIES

- [ ] All new blocks appear under "Kunaal — Editorial"
- [ ] Block icons are appropriate
- [ ] Block descriptions are helpful
- [ ] Search finds blocks by keywords

---

## 19. EDGE CASES

- [ ] **Empty States**: Blocks handle empty content gracefully
- [ ] **Long Content**: Blocks handle long text without breaking
- [ ] **Special Characters**: HTML entities render correctly
- [ ] **Multiple Instances**: Multiple of same block on page work
- [ ] **Nested Blocks**: InnerBlocks work correctly
- [ ] **Old Content**: Existing posts still render correctly

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
- Sidenote Fix: ___ / 12 tests
- Magazine Figure: ___ / 12 tests
- Lede Package: ___ / 12 tests
- Timeline: ___ / 18 tests
- Glossary: ___ / 14 tests
- Annotation: ___ / 12 tests
- Argument Map: ___ / 12 tests
- Know/Don't Know: ___ / 10 tests
- Source Excerpt: ___ / 12 tests
- Context Panel: ___ / 9 tests
- Related Reading: ___ / 16 tests
- Existing Blocks: ___ / 12 tests
- Responsive: ___ / 16 tests
- Accessibility: ___ / 7 tests
- Performance: ___ / 5 tests
- Cross-Browser: ___ / 12 tests
- PDF Export: ___ / 6 tests
- Block Categories: ___ / 4 tests
- Edge Cases: ___ / 6 tests

**Total**: ___ / 181 tests

---

## Quick Smoke Test (10 minutes)

If time is limited, test these critical items:

1. ✅ Sidenote appears with blue bullet marker (not number)
2. ✅ Sidenote font (Garfield Signature) loads
3. ✅ Timeline block renders with blue markers
4. ✅ Argument Map shows two columns
5. ✅ Annotation tooltip appears on hover
6. ✅ Source Excerpt has parchment styling
7. ✅ Related Reading links work
8. ✅ All blocks appear in inserter under "Kunaal — Editorial"
9. ✅ Mobile view stacks correctly
10. ✅ No console errors

---

## New Blocks Summary (v4.0.0)

| Block | Category | Purpose |
|-------|----------|---------|
| Magazine Figure | Editorial | Image + caption + credit |
| Lede Package | Editorial | Hero image + headline + dek |
| Timeline | Editorial | Chronological events |
| Timeline Item | Editorial | Single timeline event |
| Glossary | Editorial | Term definitions |
| Glossary Term | Editorial | Single term + definition |
| Annotation | Editorial | Inline highlight + tooltip |
| Argument Map | Editorial | Claims + evidence + counter |
| Know/Don't Know | Editorial | Two-column certainty |
| Source Excerpt | Editorial | Primary source styling |
| Context Panel | Editorial | Contextual information |
| Related Reading | Editorial | Further reading section |
| Related Link | Editorial | Single reading link |

**Total New Blocks in v4.0.0**: 13  
**Total Blocks in Theme**: 28

---

**Last Updated**: Version 4.0.0 - December 25, 2025
