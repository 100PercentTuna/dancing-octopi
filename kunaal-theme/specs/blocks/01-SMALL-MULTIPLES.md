# Small Multiples Block Specification

> **Block Name:** `kunaal/small-multiples`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** Chart.js or D3.js

---

## 1. Overview

Small multiples display the same chart type repeated across a grid, each showing a different subset of data. This enables comparison across categories, time periods, or segments without visual clutter.

### 1.1 Use Cases
- Compare trends across multiple countries/regions
- Show metric performance across departments
- Display seasonal patterns across years
- Compare distributions across categories

---

## 2. Visual Design

### 2.1 Layout
```
┌─────────────────────────────────────────────────────────┐
│  [Optional Title - Newsreader 24px]                     │
│  [Optional Subtitle - Inter 14px muted]                 │
├─────────┬─────────┬─────────┬─────────┬─────────────────┤
│  ┌───┐  │  ┌───┐  │  ┌───┐  │  ┌───┐  │                 │
│  │USA│  │  │UK │  │  │DE │  │  │FR │  │  ... (wraps)    │
│  │📈 │  │  │📈 │  │  │📈 │  │  │📈 │  │                 │
│  └───┘  │  └───┘  │  └───┘  │  └───┘  │                 │
│  Label  │  Label  │  Label  │  Label  │                 │
├─────────┴─────────┴─────────┴─────────┴─────────────────┤
│  [Shared Legend - if applicable]                        │
│  [Source note - mono 12px]                              │
└─────────────────────────────────────────────────────────┘
```

### 2.2 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Block title | Newsreader | 24px | 500 | `--ink` |
| Block subtitle | Inter | 14px | 400 | `--muted` |
| Cell label | Inter | 13px | 600 | `--ink` |
| Axis labels | Inter | 10px | 400 | `--muted` |
| Source | ui-monospace | 12px | 400 | `--muted` |

### 2.3 Dimensions
- **Cell min-width:** 120px
- **Cell max-width:** 200px
- **Cell aspect ratio:** 4:3 (default), customizable
- **Gap between cells:** 16px (mobile: 12px)
- **Outer padding:** 24px

### 2.4 Styling
- **Cell border:** 1px solid `--warmLight`
- **Cell background:** `--bg`
- **Hover state:** Border becomes `--warm`, subtle shadow
- **Axes:** Thin (0.5px) `--muted` lines, minimal ticks

---

## 3. Block Attributes

```json
{
  "title": {
    "type": "string",
    "default": ""
  },
  "subtitle": {
    "type": "string",
    "default": ""
  },
  "chartType": {
    "type": "string",
    "enum": ["line", "bar", "area", "sparkline"],
    "default": "line"
  },
  "columns": {
    "type": "number",
    "default": 4,
    "minimum": 2,
    "maximum": 8
  },
  "cellAspectRatio": {
    "type": "string",
    "enum": ["4:3", "1:1", "16:9", "3:2"],
    "default": "4:3"
  },
  "showAxes": {
    "type": "boolean",
    "default": false
  },
  "sharedYScale": {
    "type": "boolean",
    "default": true
  },
  "showLegend": {
    "type": "boolean",
    "default": false
  },
  "highlightMax": {
    "type": "boolean",
    "default": false
  },
  "highlightMin": {
    "type": "boolean",
    "default": false
  },
  "sourceNote": {
    "type": "string",
    "default": ""
  },
  "dataRows": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "label": { "type": "string" },
        "values": { 
          "type": "array",
          "items": { "type": "number" }
        },
        "color": { "type": "string" }
      }
    }
  },
  "xLabels": {
    "type": "array",
    "default": [],
    "items": { "type": "string" }
  },
  "colorPalette": {
    "type": "string",
    "enum": ["theme", "custom"],
    "default": "theme"
  },
  "customColors": {
    "type": "array",
    "default": []
  }
}
```

---

## 4. Editor Interface

### 4.1 Toolbar Controls
- **Chart Type:** Dropdown (Line, Bar, Area, Sparkline)
- **Columns:** Number input (2-8)
- **Toggle Axes:** Button
- **Toggle Legend:** Button

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input

#### Layout Settings
- **Columns** — Range slider (2-8)
- **Aspect Ratio** — Dropdown (4:3, 1:1, 16:9, 3:2)
- **Gap Size** — Dropdown (Compact, Normal, Spacious)

#### Chart Settings
- **Chart Type** — Button group with icons
- **Shared Y-Scale** — Toggle (When ON, all cells use same scale for comparison)
- **Show Axes** — Toggle
- **Show Grid Lines** — Toggle

#### Highlighting
- **Highlight Max Value** — Toggle (adds dot + different color)
- **Highlight Min Value** — Toggle
- **Highlight Color** — Color picker (defaults to terracotta)

#### Data Entry
- **X-Axis Labels** — Comma-separated text input
- **Data Rows** — Repeater with:
  - Label (text)
  - Values (comma-separated numbers)
  - Color override (color picker, optional)

#### Colors
- **Palette** — Radio (Theme / Custom)
- **Custom Colors** — Up to 8 color pickers (visible when Custom selected)

---

## 5. Data Entry UI

### 5.1 Table-Based Entry (Preferred)
```
┌─────────────────────────────────────────────────────────┐
│  📊 Small Multiples Data                                │
├─────────┬───────┬───────┬───────┬───────┬───────────────┤
│  Label  │  Q1   │  Q2   │  Q3   │  Q4   │  [+ Column]   │
├─────────┼───────┼───────┼───────┼───────┼───────────────┤
│  USA    │  42   │  45   │  48   │  52   │               │
├─────────┼───────┼───────┼───────┼───────┼───────────────┤
│  UK     │  38   │  36   │  40   │  41   │               │
├─────────┼───────┼───────┼───────┼───────┼───────────────┤
│  [+ Row]│       │       │       │       │               │
└─────────┴───────┴───────┴───────┴───────┴───────────────┘
│  [Paste from Spreadsheet]                               │
└─────────────────────────────────────────────────────────┘
```

### 5.2 Features
- Click cell to edit
- Tab/Enter navigation
- Paste from Excel/Google Sheets (tab-delimited)
- Drag rows to reorder
- Delete row (trash icon)
- Color picker per row (optional override)

---

## 6. Responsive Behavior

| Breakpoint | Columns | Cell Size |
|------------|---------|-----------|
| < 480px | 2 (forced) | Full width / 2 |
| 480-768px | min(3, setting) | Calculated |
| 768-1024px | min(4, setting) | Calculated |
| > 1024px | As configured | Max 200px each |

---

## 7. Accessibility

### 7.1 ARIA Structure
```html
<figure role="img" aria-labelledby="sm-title-{id}" aria-describedby="sm-desc-{id}">
  <figcaption id="sm-title-{id}">[Title]</figcaption>
  <div id="sm-desc-{id}" class="sr-only">
    Small multiples chart comparing [metric] across [n] categories.
    [Summary of key findings]
  </div>
  <div class="small-multiples-grid" role="list">
    <div class="small-multiples-cell" role="listitem" aria-label="[Label]: [summary]">
      <!-- Chart SVG/Canvas -->
    </div>
    <!-- ... -->
  </div>
</figure>
```

### 7.2 Data Table
- Expandable data table below chart
- `<details>` with `<summary>` "View data table"
- Full accessible `<table>` with headers

---

## 8. Animations

### 8.1 Entry Animation (when scrolled into view)
- Cells stagger in (50ms delay each)
- Lines/bars draw from left to right (400ms)
- Respects `prefers-reduced-motion`

### 8.2 Hover Animation
- Cell border color transition (200ms)
- Subtle lift (translateY -2px, shadow increase)

---

## 9. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No data | Show placeholder with "Add data" prompt |
| Single row | Show single large cell, center aligned |
| 2 rows | 2-column layout regardless of setting |
| Missing values in row | Gap in line, skip bar |
| Very long labels | Truncate with ellipsis, full on hover |
| Negative values | Support negative axis, different color |

---

## 10. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-small-multiples" role="img" aria-labelledby="sm-123-title">
  <figcaption class="small-multiples-header">
    <h3 id="sm-123-title" class="small-multiples-title">Quarterly Revenue by Region</h3>
    <p class="small-multiples-subtitle">FY 2024 in millions USD</p>
  </figcaption>
  
  <div class="small-multiples-grid" style="--columns: 4" role="list">
    <article class="small-multiples-cell" role="listitem" aria-label="North America: Q1 42M, Q2 45M, Q3 48M, Q4 52M">
      <div class="cell-chart">
        <canvas data-values="42,45,48,52" data-labels="Q1,Q2,Q3,Q4"></canvas>
      </div>
      <p class="cell-label">North America</p>
    </article>
    <!-- More cells... -->
  </div>
  
  <footer class="small-multiples-footer">
    <div class="small-multiples-legend"><!-- If enabled --></div>
    <p class="small-multiples-source">Source: Company financials</p>
  </footer>
  
  <details class="small-multiples-data-table">
    <summary>View data table</summary>
    <table><!-- Accessible data table --></table>
  </details>
</figure>
```

---

## 11. User Stories

### US-SM-01: Basic Creation
**As a** content author  
**I want to** create a small multiples chart from my data  
**So that** I can show comparisons across categories visually  

**Acceptance Criteria:**
- [ ] Can add block from inserter
- [ ] Can enter data via table UI
- [ ] Can paste from spreadsheet
- [ ] Chart renders correctly in editor
- [ ] Chart renders correctly on frontend

### US-SM-02: Layout Customization
**As a** content author  
**I want to** control the grid layout  
**So that** the chart fits my content width  

**Acceptance Criteria:**
- [ ] Can change number of columns (2-8)
- [ ] Can change aspect ratio
- [ ] Layout reflows responsively
- [ ] Gap size is adjustable

### US-SM-03: Chart Type Selection
**As a** content author  
**I want to** choose different chart types  
**So that** I can best represent my data  

**Acceptance Criteria:**
- [ ] Line charts work
- [ ] Bar charts work
- [ ] Area charts work
- [ ] Sparklines work (minimal, no axes)

### US-SM-04: Accessibility
**As a** user with a screen reader  
**I want to** understand the data in the chart  
**So that** I'm not excluded from the content  

**Acceptance Criteria:**
- [ ] ARIA roles and labels present
- [ ] Data table available
- [ ] Keyboard navigation works
- [ ] Focus visible on interactive elements

