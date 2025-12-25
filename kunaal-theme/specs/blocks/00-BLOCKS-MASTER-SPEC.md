# Advanced Gutenberg Blocks - Master Specification

> **Version:** 2.0.0  
> **Status:** Draft  
> **Last Updated:** 2025-12-26  
> **Follows:** Kunaal Theme Design System (see `/specs/00-MASTER-SPEC.md`)

---

## 1. Vision & Philosophy

### 1.1 Core Principles

These blocks embody the same design philosophy as the About Page:
- **Editorial elegance** — Every chart tells a story, not just displays data
- **Organic sophistication** — Warm, layered, never sterile or "dashboardy"
- **Accessibility-first** — All visualizations have text alternatives
- **Performance-conscious** — Lazy loading, progressive enhancement
- **Customization without complexity** — Rich options, zero JSON

### 1.2 Design Language Inheritance

All blocks inherit from the theme's established system:

| Element | Value | Notes |
|---------|-------|-------|
| **Primary Font** | `var(--serif)` Newsreader | Chart titles, axis labels |
| **Secondary Font** | `var(--sans)` Inter | Data labels, legends |
| **Mono Font** | `var(--mono)` ui-monospace | Numeric values, code |
| **Handwritten** | `var(--handwritten)` Caveat | Annotations |
| **Brown Accent** | `#7D6B5D` / `var(--warm)` | Primary accent (3x usage) |
| **Blue Accent** | `#4A90A4` / `var(--blue)` | Links, focus rings only |
| **Background** | `#FAF8F5` / `var(--bg)` | Canvas/chart background |
| **Ink** | `#1A1A1A` / `var(--ink)` | Primary text, axis lines |

### 1.3 Block Architecture

All advanced blocks follow a consistent architecture:

```
kunaal-theme/blocks/{block-name}/
├── block.json          # Block registration, attributes, supports
├── edit.js             # Editor component (React)
├── render.php          # Frontend PHP render
├── style.css           # Frontend styles
├── editor.css          # Editor-only styles (optional)
├── view.js             # Frontend JS (optional, for interactivity)
└── README.md           # Block documentation
```

---

## 2. Block Categories

### 2.1 Existing Categories (Extend)
- `kunaal-data` — Charts, visualizations, maps
- `kunaal-editorial` — Typography, footnotes, annotations

### 2.2 New Category
- `kunaal-advanced-viz` — Network graphs, Sankey, complex layouts

---

## 3. Shared Infrastructure

### 3.1 Color System for Charts

#### 3.1.1 Default Palette (Warm + Neutral)
Charts use a warm, editorial palette by default:

```css
--chart-1: #7D6B5D;   /* Brown (primary) */
--chart-2: #B8A99A;   /* Warm Light */
--chart-3: #C9553D;   /* Terracotta */
--chart-4: #8B7355;   /* Sienna */
--chart-5: #D4C4B5;   /* Champagne */
--chart-6: #6B5B4F;   /* Dark Brown */
--chart-7: #A08B7A;   /* Taupe */
--chart-8: #4A90A4;   /* Blue (sparingly) */
```

#### 3.1.2 Custom Color Picker Component
- Users can override default palette per-chart
- Picker shows theme colors first, custom palette second
- Color contrast warnings for accessibility

### 3.2 Responsive Breakpoints
```css
--bp-mobile: 480px;
--bp-tablet: 768px;
--bp-desktop: 1024px;
--bp-wide: 1280px;
```

### 3.3 Animation Timing
```css
--ease-out: cubic-bezier(0.16, 1, 0.3, 1);
--duration-fast: 200ms;
--duration-medium: 400ms;
--duration-slow: 800ms;
```

---

## 4. Performance Requirements

### 4.1 Lazy Loading
- All charts with data > 50 points lazy load
- Charts render placeholder skeleton until in viewport
- IntersectionObserver with 200px root margin

### 4.2 Code Splitting
- D3.js loaded only when D3-dependent block exists
- Leaflet loaded only for map blocks
- Chart.js loaded only for basic charts

### 4.3 Bundle Sizes (Target)
| Module | Max Size |
|--------|----------|
| Basic charts (Chart.js) | 40KB gzipped |
| Advanced viz (D3.js core) | 35KB gzipped |
| Maps (Leaflet) | 45KB gzipped |
| Per-block JS | 10KB gzipped |

---

## 5. Accessibility Requirements (WCAG 2.1 AA)

### 5.1 All Charts Must Have
- `role="img"` with `aria-label` describing the chart
- Accompanying data table (visually hidden or expandable)
- Color contrast ≥ 4.5:1 for text, ≥ 3:1 for graphical elements
- Non-color indicators (patterns, shapes) as option

### 5.2 Interactive Elements Must Have
- Keyboard navigation (Tab, Arrow keys)
- Focus rings (2px solid blue, 2px offset)
- Screen reader announcements for tooltips

---

## 6. Spec File Index

| File | Contents |
|------|----------|
| `01-SMALL-MULTIPLES.md` | Small multiples chart spec |
| `02-SLOPEGRAPH.md` | Slopegraph chart spec |
| `03-DUMBBELL.md` | Dumbbell chart spec |
| `04-HEATMAP.md` | Heatmap chart spec |
| `05-BOX-VIOLIN.md` | Box plot & violin chart specs |
| `06-SANKEY-ALLUVIAL.md` | Flow diagrams spec |
| `07-NETWORK-GRAPH.md` | Network/relationship graph spec |
| `08-MAP-VISUALIZATIONS.md` | Advanced map visualizations spec |
| `09-DARK-MODE.md` | Dark mode implementation spec |
| `10-COLOR-PICKER.md` | Custom color picker component spec |
| `11-CUSTOMIZATION.md` | Advanced customization system spec |
| `12-FOOTNOTES.md` | Footnotes & endnotes system spec |
| `13-PERFORMANCE.md` | Performance optimization spec |
| `14-USER-STORIES.md` | User stories for all features |

---

## 7. Implementation Order

### Phase 1: Foundation (Week 1-2)
1. Dark mode infrastructure
2. Custom color picker component
3. Lazy loading system
4. Enhanced footnotes/endnotes

### Phase 2: Basic Advanced Charts (Week 3-4)
5. Heatmap
6. Dumbbell chart
7. Slopegraph
8. Small multiples

### Phase 3: Statistical Charts (Week 5)
9. Box plot
10. Violin plot

### Phase 4: Flow & Network (Week 6-7)
11. Sankey diagram
12. Alluvial diagram
13. Network graph

### Phase 5: Map Enhancements (Week 8)
14. Choropleth improvements
15. Proportional symbols (dots with gradient)
16. Multi-layer map support

### Phase 6: Polish & Performance (Week 9)
17. Code splitting optimization
18. Image optimization pipeline
19. Final accessibility audit

---

## 8. Technical Dependencies

### 8.1 NPM Packages (Production)
```json
{
  "chart.js": "^4.4.0",
  "d3": "^7.8.0",
  "leaflet": "^1.9.4",
  "d3-sankey": "^0.12.3",
  "d3-force": "^3.0.0"
}
```

### 8.2 WordPress Dependencies
- `@wordpress/blocks`
- `@wordpress/block-editor`
- `@wordpress/components`
- `@wordpress/data`
- `@wordpress/element`

---

## 9. Quality Gates

Before any block is considered complete:

- [ ] All acceptance criteria from user stories met
- [ ] Works in Gutenberg editor (visual preview accurate)
- [ ] Works on frontend (all browsers: Chrome, Firefox, Safari, Edge)
- [ ] Responsive (mobile, tablet, desktop)
- [ ] Accessible (keyboard, screen reader tested)
- [ ] Performance (lazy loads, bundle size within limits)
- [ ] Dark mode compatible
- [ ] Color customization works
- [ ] Data table alternative available
- [ ] Documentation complete

