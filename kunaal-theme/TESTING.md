# Testing Checklist - Version 4.2.0

**Release Date**: December 25, 2025  
**Version**: 4.2.0 (Major Release)  
**Testing Scope**: Bug Fixes, Chart Block, Epic 4 Enhanced, Epic 6 Complete

---

## Pre-Testing Setup

- [ ] Clear browser cache
- [ ] Test in incognito/private mode
- [ ] Ensure theme is updated to v4.2.0

---

## 0. CRITICAL FIXES (v4.2.0)

### 0.1 PDF Colors
- [ ] **PDF Export**: Colors are preserved (not black-and-white)
- [ ] **PDF Export**: Confidence meters show colored bars
- [ ] **PDF Export**: Status badges maintain colors
- [ ] **PDF Export**: Argument map columns show green/red tints
- [ ] **PDF Export**: Framework matrix cells show color gradients

### 0.2 Sidenote Font
- [ ] **Sidenote**: Font loads correctly (Caveat from Google Fonts)
- [ ] **Sidenote**: Font renders properly (not broken/fallback)
- [ ] **Sidenote**: Appears in margin on desktop
- [ ] **Sidenote**: Blue bullet marker (•) visible

### 0.3 Article Titles
- [ ] **Essay/Jotting Pages**: Title uses serif font (not sans-serif)
- [ ] **Essay/Jotting Pages**: Title color is softer (not pure black)
- [ ] **Essay/Jotting Pages**: Title size and spacing look good

### 0.4 Regex Bug Fix
- [ ] **PDF Export**: Accordions with `open` attribute don't duplicate
- [ ] **PDF Export**: No invalid HTML like `<details open open>`

---

## 1. NEW BLOCK - Chart (Epic 4)

---

## 1. EPIC 5 - MOTION PRIMITIVES (New CSS Utilities)

### 1.1 Animation Classes
- [ ] `.motion-fade-in` - Element fades in
- [ ] `.motion-fade-up` - Element fades up from below
- [ ] `.motion-fade-down` - Element fades down from above
- [ ] `.motion-fade-left` - Element fades in from right
- [ ] `.motion-fade-right` - Element fades in from left
- [ ] `.motion-scale-in` - Element scales in
- [ ] `.motion-scale-up` - Element scales up with translate

### 1.2 Stagger Classes
- [ ] `.stagger-1` through `.stagger-8` work with delays

### 1.3 Scroll Reveal Classes
- [ ] `.scroll-reveal` - Element reveals on scroll
- [ ] `.scroll-reveal-left` - Element reveals from left
- [ ] `.scroll-reveal-right` - Element reveals from right
- [ ] `.scroll-reveal-scale` - Element scales in on scroll
- [ ] JavaScript adds `.is-visible` on scroll

### 1.4 Reduced Motion
- [ ] All animations disabled when `prefers-reduced-motion` is set

---

## 2. EPIC 3 - ANALYSIS BLOCKS

### 2.1 Assumptions Register
- [ ] **Editor**: Can add assumptions with text, confidence, status
- [ ] **Editor**: Can remove assumptions
- [ ] **Frontend**: Table renders correctly
- [ ] **Frontend**: Confidence indicators (green/yellow/red dots)
- [ ] **Frontend**: Status badges (Untested/Validated/Invalidated/Partial)
- [ ] **Frontend**: Row highlighting for validated/invalidated
- [ ] **Visual**: Clean table styling

### 2.2 Confidence Meter
- [ ] **Editor**: Label field is editable
- [ ] **Editor**: Confidence slider (0-100) works
- [ ] **Editor**: Description field works
- [ ] **Editor**: Toggle for showing percentage
- [ ] **Frontend**: Progress bar renders
- [ ] **Frontend**: Color changes based on level (green/yellow/red)
- [ ] **Frontend**: Percentage displays if enabled
- [ ] **Visual**: Smooth bar animation

### 2.3 Scenario Comparison
- [ ] **Editor**: Can add multiple scenarios
- [ ] **Editor**: Scenario name, probability, description, outcome fields
- [ ] **Editor**: Probability selector (High/Medium/Low)
- [ ] **Frontend**: Grid layout for scenarios
- [ ] **Frontend**: Color-coded top borders
- [ ] **Frontend**: Probability badges
- [ ] **Frontend**: Stacks on mobile
- [ ] **Visual**: Clean card styling

### 2.4 Decision Log + Entry
- [ ] **Editor**: Title editable
- [ ] **Editor**: Can add decision-entry child blocks
- [ ] **Editor**: Decision, date, rationale, status, outcome fields
- [ ] **Frontend**: Timeline-like layout with markers
- [ ] **Frontend**: Status indicator (Active/Superseded/Reversed)
- [ ] **Frontend**: Strikethrough for superseded/reversed
- [ ] **Frontend**: Outcome section renders
- [ ] **Visual**: Color-coded status badges

### 2.5 Framework Matrix (2x2/3x3)
- [ ] **Editor**: Can select 2x2 or 3x3 size
- [ ] **Editor**: X and Y axis labels editable
- [ ] **Editor**: Each cell has label and content
- [ ] **Frontend**: Grid renders correctly
- [ ] **Frontend**: Axis labels positioned correctly
- [ ] **Frontend**: Color gradient across cells
- [ ] **Visual**: Clean matrix styling
- [ ] **Responsive**: Works on mobile

### 2.6 Causal Loop Diagram
- [ ] **Editor**: Can add variable nodes
- [ ] **Editor**: Effect selector (positive/negative)
- [ ] **Editor**: Description field
- [ ] **Frontend**: Nodes render with arrows
- [ ] **Frontend**: Loop type indicator (R/B)
- [ ] **Frontend**: Color-coded effects (+/−)
- [ ] **Frontend**: Loop-back visualization
- [ ] **Visual**: Clean systems diagram

### 2.7 Evaluation Rubric + Row
- [ ] **Editor**: Title and column headers editable
- [ ] **Editor**: Can add rubric-row child blocks
- [ ] **Editor**: Each row has criteria and levels
- [ ] **Frontend**: Table renders correctly
- [ ] **Frontend**: Column header colors (red→green)
- [ ] **Frontend**: Zebra striping
- [ ] **Visual**: Publication-quality table
- [ ] **Responsive**: Scrolls on mobile

### 2.8 Debate (Steelman)
- [ ] **Editor**: Question field editable
- [ ] **Editor**: Two debate-side blocks (locked)
- [ ] **Editor**: Each side has label, argument, points
- [ ] **Frontend**: Two-column layout
- [ ] **Frontend**: Color-coded sides (green for, red against)
- [ ] **Frontend**: Points list with arrows
- [ ] **Frontend**: Stacks on mobile
- [ ] **Visual**: Clear debate presentation

---

## 3. EPIC 4 - DATA BLOCKS

### 3.1 Publication Table
- [ ] **Editor**: Title, source, caption fields
- [ ] **Editor**: Can add columns and rows
- [ ] **Editor**: Toggle for first column highlight
- [ ] **Frontend**: Table with dark header
- [ ] **Frontend**: Zebra striping
- [ ] **Frontend**: Source and caption footer
- [ ] **Frontend**: First column highlight if enabled
- [ ] **Visual**: Professional table styling
- [ ] **Responsive**: Scrolls on mobile

### 3.2 Flow Chart + Step
- [ ] **Editor**: Title editable
- [ ] **Editor**: Orientation selector (horizontal/vertical)
- [ ] **Editor**: Can add flowchart-step child blocks
- [ ] **Editor**: Step type selector (process/decision/terminal/io)
- [ ] **Frontend**: Steps connected with arrows
- [ ] **Frontend**: Different shapes for step types
- [ ] **Frontend**: Terminal steps have rounded/dark styling
- [ ] **Frontend**: Arrow hidden on last step
- [ ] **Visual**: Clean process diagram
- [ ] **Responsive**: Stacks vertically on mobile

---

## 4. EXISTING BLOCKS VERIFICATION

- [ ] Sidenote: Blue bullet marker (not numbers)
- [ ] Sidenote: Garfield Signature font loads
- [ ] All Epic 2 blocks still work
- [ ] All previous blocks render correctly

---

## 5. BLOCK CATEGORIES

- [ ] "Kunaal — Analysis" category exists with all analysis blocks
- [ ] "Kunaal — Data" category exists with data blocks
- [ ] Block icons are appropriate
- [ ] Search finds blocks by keywords

---

## 6. RESPONSIVE DESIGN

- [ ] All new blocks stack on mobile
- [ ] Tables scroll horizontally when needed
- [ ] Grid layouts adapt to screen size
- [ ] Text remains readable

---

## 7. ACCESSIBILITY

- [ ] Animation respects `prefers-reduced-motion`
- [ ] Tables have proper structure
- [ ] Color is not the only indicator

---

## Quick Smoke Test (5 minutes)

1. ✅ Motion classes work (add `.motion-fade-up` to any element)
2. ✅ Assumptions Register shows table with indicators
3. ✅ Confidence Meter shows colored progress bar
4. ✅ Framework Matrix shows 2x2 grid with colors
5. ✅ Debate shows two-column layout
6. ✅ Publication Table has dark header
7. ✅ Flow Chart shows connected steps
8. ✅ All blocks appear in correct categories
9. ✅ Mobile view works
10. ✅ No console errors

---

## New Blocks Summary (v4.1.0)

### Epic 5 - Motion (CSS Utilities)
- Animation classes
- Stagger utilities
- Scroll reveal classes

### Epic 3 - Analysis (11 blocks)
| Block | Purpose |
|-------|---------|
| Assumptions Register | Track assumptions with confidence |
| Confidence Meter | Visual confidence indicator |
| Scenario Comparison | Compare multiple scenarios |
| Decision Log | Container for decisions |
| Decision Entry | Single decision with status |
| Framework Matrix | 2x2 or 3x3 analysis grid |
| Causal Loop Diagram | Systems thinking visual |
| Evaluation Rubric | Criteria table |
| Rubric Row | Single criterion row |
| Debate | Two-sided argument |
| Debate Side | One side of debate |

### Epic 4 - Data (4 blocks)
| Block | Purpose |
|-------|---------|
| Publication Table | Styled data table |
| Flow Chart | Process diagram |
| Flow Chart Step | Single step in flow |

**Total New Blocks**: 14  
**Total Blocks in Theme**: 42

---

## 4. NEW BLOCK - Chart (Epic 4)

### 4.1 Chart Block
- [ ] **Editor**: Can add block from inserter
- [ ] **Editor**: Chart type selector works (bar, line, pie)
- [ ] **Editor**: Orientation selector works for bar charts (vertical/horizontal)
- [ ] **Editor**: Data entry field works (comma-separated numbers)
- [ ] **Editor**: Labels field works (comma-separated text)
- [ ] **Editor**: Title, source, caption fields work
- [ ] **Editor**: Color scheme selector works
- [ ] **Editor**: Show legend toggle works
- [ ] **Frontend**: Bar chart renders (vertical)
- [ ] **Frontend**: Bar chart renders (horizontal/sideways)
- [ ] **Frontend**: Line chart renders with points
- [ ] **Frontend**: Pie chart renders with percentages
- [ ] **Frontend**: Labels appear correctly
- [ ] **Frontend**: Colors match selected scheme
- [ ] **Frontend**: Source and caption render
- [ ] **Visual**: SVG scales correctly
- [ ] **Responsive**: Chart adapts to screen size

---

## 5. EPIC 6 - SHARE & SUBSCRIBE DOCK

### 5.1 Share Dock
- [ ] **Action Dock**: Share button opens panel
- [ ] **Share Panel**: PDF download button appears
- [ ] **Share Panel**: PDF download button works
- [ ] **Share Panel**: LinkedIn, X, WhatsApp buttons work
- [ ] **Share Panel**: Closes when clicking outside

### 5.2 Subscribe Dock
- [ ] **Action Dock**: Subscribe button opens panel
- [ ] **Subscribe Panel**: Form works correctly
- [ ] **Subscribe Panel**: Closes when clicking outside

---

**Last Updated**: Version 4.2.0 - December 25, 2025
