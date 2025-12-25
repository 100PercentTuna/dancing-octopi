# Testing Checklist

**Theme Version**: 4.3.0  
**Last Updated**: December 25, 2025

---

## 🧪 v4.3.0 Testing

### Chart Block v2.0
- [ ] Bar chart (vertical) renders correctly
- [ ] Bar chart (horizontal) renders correctly
- [ ] Stacked bar chart works with multiple series
- [ ] Clustered bar chart displays series side-by-side
- [ ] Line chart with single series
- [ ] Line chart with multiple series
- [ ] Pie chart renders with percentages
- [ ] Donut chart with center total
- [ ] Waterfall chart shows positive (green) and negative (red) changes
- [ ] Waterfall connectors display correctly
- [ ] Color schemes apply correctly (theme, blue, warm, green, mono, rainbow)
- [ ] Value units display correctly (prefix: $10, suffix: 10%)
- [ ] Legend shows/hides based on setting
- [ ] Grid lines show/hide based on setting
- [ ] Source and caption display
- [ ] Responsive on mobile (horizontal scroll if needed)
- [ ] SVG renders in PDF export

### Cross-Browser (Smoke Test)
- [ ] Chrome: Homepage loads
- [ ] Chrome: Essay page loads
- [ ] Chrome: Charts render
- [ ] Firefox: Homepage loads
- [ ] Firefox: Essay page loads
- [ ] Safari: Homepage loads
- [ ] Safari: Essay page loads
- [ ] Mobile Safari: Touch interactions work
- [ ] Android Chrome: Layouts correct

### Documentation
- [ ] THEME-GUIDE.md is accurate
- [ ] CHANGELOG.md reflects all changes
- [ ] BROWSER-QA.md matrix is complete

---

## ✅ v4.2.0 Testing (Completed)

### Bug Fixes
- [x] PDF preserves colors (not black-and-white)
- [x] Sidenote font (Caveat) loads correctly
- [x] Essay/Jotting titles are serif and softer color
- [x] Regex bug fixed: no duplicate 'open' attributes in PDF

### Basic Chart Block
- [x] Bar chart renders
- [x] Line chart renders
- [x] Pie chart renders
- [x] Color schemes work
- [x] Source/caption display

### Share Dock
- [x] PDF download button works
- [x] Correct filename format

---

## ✅ v4.1.0 Testing (Completed)

### Analysis Blocks
- [x] Assumptions Register: Table renders, confidence colors
- [x] Confidence Meter: Progress bar, percentage display
- [x] Scenario Comparison: Multi-column cards
- [x] Decision Log: Timeline display
- [x] Framework Matrix: 2x2 grid renders
- [x] Causal Loop: Variables and connections
- [x] Rubric: Scoring table
- [x] Debate: Two-side layout

### Motion Primitives
- [x] `.motion-fade-in` animation works
- [x] Stagger classes delay correctly
- [x] `prefers-reduced-motion` disables animations

### Data Blocks
- [x] Publication Table: Headers, rows, source
- [x] Flow Chart: Steps connect properly

---

## ✅ v4.0.0 Testing (Completed)

### Editorial Blocks
- [x] Magazine Figure: Image sizes, captions
- [x] Lede Package: Hero layout
- [x] Timeline: Markers, connectors
- [x] Glossary: Term organization
- [x] Annotation: Highlights, tooltips
- [x] Argument Map: Structure displays
- [x] Know/Don't Know: Two columns
- [x] Source Excerpt: Citation styling
- [x] Context Panel: Collapsible
- [x] Related Reading: Link cards

### Sidenote Block
- [x] Caveat font renders
- [x] Blue bullet marker displays
- [x] Desktop: In margin
- [x] Mobile: Inline display

---

## ✅ v3.9.0 Testing (Completed)

### Footnotes
- [x] Footnote marker inserts
- [x] Footnotes Section renders at end
- [x] Bidirectional links work

### Parallax
- [x] Background image parallax effect
- [x] Content overlay readable
- [x] Mobile fallback (no parallax)

### Scrollytelling
- [x] Steps trigger correctly
- [x] Sticky content updates
- [x] Mobile behavior

### Bug Fixes
- [x] Header z-index correct
- [x] Footer spacing on mobile

---

## ✅ v3.8.0 Testing (Completed)

### Block Conversions
- [x] Insight Box: Warm background, label editable
- [x] Pull Quote: Single blue border, citation
- [x] Accordion: Smooth animation, elegant marker
- [x] Sidenote: Margin positioning
- [x] Section Header: Number editable
- [x] Takeaways: Large accent numbers
- [x] Citation: Centered, elegant
- [x] Aside: Label, outcome highlight

### Block Editor
- [x] All blocks appear in inserter
- [x] Inspector Controls work
- [x] Preview matches frontend

---

## 📋 General QA Checklist

### Visual
- [ ] Typography: Fonts load (Inter, Newsreader, Caveat)
- [ ] Colors: CSS variables resolve
- [ ] Spacing: Consistent margins/padding
- [ ] Images: Aspect ratios correct
- [ ] Icons: SVGs render

### Interactive
- [ ] Accordions: Expand/collapse smoothly
- [ ] Sidenotes: Show on hover/click
- [ ] Footnotes: Jump to/from work
- [ ] Share menu: Opens/closes
- [ ] Scroll animations: Trigger at correct position

### Responsive
- [ ] Desktop (1440px+): Full layout
- [ ] Desktop (1024px): Narrower margins
- [ ] Tablet (768px): Stacked elements
- [ ] Mobile (480px): Single column

### Accessibility
- [ ] Keyboard: Tab through all interactive elements
- [ ] Screen reader: Content announced correctly
- [ ] Focus: Indicators visible
- [ ] Motion: Respects user preference

### Performance
- [ ] Page load: Under 3 seconds
- [ ] No console errors
- [ ] Images: Lazy loaded
- [ ] Fonts: Not blocking render

### PDF Export
- [ ] Download triggers
- [ ] Filename correct
- [ ] Colors preserved
- [ ] Layout clean
- [ ] Images render
- [ ] Accordions expanded

---

## 🐛 Known Issues

### Safari/iOS
- Parallax: Falls back to static background
- Print: Some CSS variables may not resolve

### Edge (Legacy)
- Not supported (pre-Chromium)

### Workarounds
- Use DOMPDF for reliable PDF (not browser print)
- Test on actual devices, not just emulators

---

## 📝 Test Report Template

```
## Test Report - v[VERSION]

**Tester**: [Name]
**Date**: [Date]
**Browser**: [Browser/Version]
**Device**: [Device/OS]

### Pass/Fail Summary
- Total Tests: X
- Passed: X
- Failed: X
- Blocked: X

### Failed Tests
1. [Test name] - [Brief description of issue]

### Notes
[Any observations or recommendations]
```
