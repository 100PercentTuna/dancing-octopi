# Heatmap Block Specification

> **Block Name:** `kunaal/heatmap`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** None (pure CSS/SVG) or D3.js for advanced features

---

## 1. Overview

A heatmap displays data as a matrix of cells, where color intensity represents value magnitude. Ideal for revealing patterns across two dimensions (rows and columns).

### 1.1 Use Cases
- Correlation matrices
- Activity over time (hours × days)
- Performance across segments
- Survey response distributions
- Geographic patterns (regions × metrics)

---

## 2. Visual Design

### 2.1 Layout
```
┌─────────────────────────────────────────────────────────────────┐
│  [Title]                                                        │
│  [Subtitle]                                                     │
├─────────────────────────────────────────────────────────────────┤
│           │  Mon  │  Tue  │  Wed  │  Thu  │  Fri  │  Sat  │ Sun │
│  ─────────┼───────┼───────┼───────┼───────┼───────┼───────┼─────│
│   6 AM    │ ░░░░░ │ ░░░░░ │ ▒▒▒▒▒ │ ░░░░░ │ ▒▒▒▒▒ │ ▓▓▓▓▓ │ ▓▓▓ │
│   7 AM    │ ▒▒▒▒▒ │ ▒▒▒▒▒ │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ █████ │ ▓▓▓ │
│   8 AM    │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ █████ │ █████ │ █████ │ ▓▓▓▓▓ │ ▒▒▒ │
│   9 AM    │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ ▒▒▒▒▒ │ ░░░ │
│  10 AM    │ ▒▒▒▒▒ │ ▓▓▓▓▓ │ ▓▓▓▓▓ │ ▒▒▒▒▒ │ ▒▒▒▒▒ │ ░░░░░ │ ░░░ │
│  ─────────┴───────┴───────┴───────┴───────┴───────┴───────┴─────│
│                                                                 │
│  Legend: ░ Low  ▒▒ Medium  ▓▓▓ High  ████ Very High            │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  [Source]                                                       │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Title | Newsreader | 24px | 500 | `--ink` |
| Subtitle | Inter | 14px | 400 | `--muted` |
| Row labels | Inter | 12px | 500 | `--ink` |
| Column labels | Inter | 12px | 500 | `--ink` |
| Cell values | ui-monospace | 11px | 400 | Dynamic (contrast) |
| Legend labels | Inter | 11px | 400 | `--muted` |

### 2.3 Dimensions
- **Cell min-size:** 32px × 32px
- **Cell max-size:** 64px × 64px
- **Cell gap:** 2px
- **Cell border-radius:** 3px
- **Row label width:** 80px (adjustable)
- **Column label height:** 40px (rotated: 80px width)

### 2.4 Color Scales

#### Theme Palette (Warm)
Sequential scale from light to dark brown:
```
Lowest:   #F5F0EB (almost white)
Low:      #E8DFD5 (champagne)
Medium:   #D4C4B5 (warm light)
High:     #B8A99A (taupe)
Higher:   #8B7355 (sienna)
Highest:  #7D6B5D (warm)
Peak:     #5C4A3D (dark brown)
```

#### Diverging Palette (for +/- values)
```
Negative: #C9553D (terracotta) → #F5F0EB (neutral) → #7D6B5D (brown) Positive
```

#### Custom Palette
User can define:
- **Low color** (min value)
- **High color** (max value)
- **Mid color** (optional, for diverging)

### 2.5 Cell States
- **Default:** Solid color fill
- **Hover:** Slight border, tooltip appears
- **Focus:** 2px blue outline
- **Empty/No data:** Diagonal stripes pattern

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
  "rowLabels": {
    "type": "array",
    "default": [],
    "items": { "type": "string" }
  },
  "columnLabels": {
    "type": "array",
    "default": [],
    "items": { "type": "string" }
  },
  "data": {
    "type": "array",
    "default": [],
    "items": {
      "type": "array",
      "items": { "type": "number" }
    }
  },
  "colorScale": {
    "type": "string",
    "enum": ["theme", "diverging", "custom"],
    "default": "theme"
  },
  "customColorLow": {
    "type": "string",
    "default": "#F5F0EB"
  },
  "customColorHigh": {
    "type": "string",
    "default": "#7D6B5D"
  },
  "customColorMid": {
    "type": "string",
    "default": ""
  },
  "showValues": {
    "type": "boolean",
    "default": false
  },
  "valueFormat": {
    "type": "string",
    "enum": ["number", "percent", "decimal1", "decimal2"],
    "default": "number"
  },
  "showLegend": {
    "type": "boolean",
    "default": true
  },
  "legendPosition": {
    "type": "string",
    "enum": ["bottom", "right"],
    "default": "bottom"
  },
  "cellSize": {
    "type": "string",
    "enum": ["auto", "small", "medium", "large"],
    "default": "auto"
  },
  "rotateColumnLabels": {
    "type": "boolean",
    "default": false
  },
  "clusterRows": {
    "type": "boolean",
    "default": false
  },
  "clusterColumns": {
    "type": "boolean",
    "default": false
  },
  "sourceNote": {
    "type": "string",
    "default": ""
  }
}
```

---

## 4. Editor Interface

### 4.1 Toolbar Controls
- **Show Values:** Toggle
- **Cell Size:** Dropdown (Auto, Small, Medium, Large)
- **Legend:** Toggle

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input

#### Data Entry
- **Mode:** Radio (Table Entry / Paste Data)

##### Table Entry Mode
```
┌────────────┬───────┬───────┬───────┬───────┬─────────────┐
│            │  Mon  │  Tue  │  Wed  │  Thu  │ [+ Column]  │
├────────────┼───────┼───────┼───────┼───────┼─────────────┤
│  6 AM      │  12   │  8    │  15   │  22   │             │
├────────────┼───────┼───────┼───────┼───────┼─────────────┤
│  7 AM      │  45   │  52   │  61   │  58   │             │
├────────────┼───────┼───────┼───────┼───────┼─────────────┤
│  [+ Row]   │       │       │       │       │             │
└────────────┴───────┴───────┴───────┴───────┴─────────────┘
```

##### Paste Mode
- Large textarea
- Instructions: "Paste tab-delimited data. First row = column headers. First column = row labels."

#### Color Settings
- **Color Scale** — Radio:
  - **Theme (Brown)** — Uses warm palette
  - **Diverging** — Shows low/mid/high pickers
  - **Custom** — Shows low/high pickers
- **Low Value Color** — Color picker
- **High Value Color** — Color picker
- **Mid Value Color** — Color picker (diverging only)

#### Display Options
- **Show Values in Cells** — Toggle
- **Value Format** — Dropdown (when Show Values is on)
- **Rotate Column Labels** — Toggle (for long labels)
- **Cell Size** — Dropdown

#### Legend
- **Show Legend** — Toggle
- **Legend Position** — Radio (Bottom / Right)

#### Advanced
- **Cluster Rows** — Toggle (reorder rows by similarity)
- **Cluster Columns** — Toggle (reorder columns by similarity)

---

## 5. Responsive Behavior

| Breakpoint | Behavior |
|------------|----------|
| < 480px | Horizontal scroll, fixed row labels |
| 480-768px | Smaller cells, value labels hidden |
| 768-1024px | Medium cells |
| > 1024px | Full size cells |

### 5.1 Mobile Scroll Behavior
- Row labels fixed on left
- Column headers scroll with data
- Touch-friendly horizontal scroll
- Scroll indicator shown

---

## 6. Accessibility

### 6.1 ARIA Structure
```html
<figure class="wp-block-kunaal-heatmap" role="img" aria-labelledby="hm-title-{id}">
  <figcaption id="hm-title-{id}">[Title]</figcaption>
  <div class="heatmap-wrapper">
    <table role="grid" aria-label="Heatmap data">
      <thead>
        <tr><th></th><th>Mon</th><th>Tue</th>...</tr>
      </thead>
      <tbody>
        <tr>
          <th>6 AM</th>
          <td role="gridcell" aria-label="6 AM, Monday: 12" tabindex="0">12</td>
          ...
        </tr>
      </tbody>
    </table>
  </div>
</figure>
```

### 6.2 Keyboard Navigation
- Arrow keys navigate between cells
- Tab moves to next row
- Shift+Tab moves to previous row
- Enter announces cell value

### 6.3 Color Accessibility
- Color scale must have sufficient contrast between steps
- Pattern option for additional encoding (dots, lines)
- High-contrast mode respects OS preference

---

## 7. Animations

### 7.1 Entry Animation
- Cells fade in with stagger (5ms per cell)
- Color transitions from neutral to final color

### 7.2 Hover Animation
- Border appears (150ms)
- Tooltip fades in (150ms)

---

## 8. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No data | Placeholder with instructions |
| Single row/column | Render as colored bar chart |
| Missing values | Striped pattern, "N/A" in tooltip |
| All same values | Single color, legend shows "All values: X" |
| Negative values | Use diverging scale automatically |
| Very large grid (>50×50) | Enable virtualization |
| Long labels | Truncate, show full in tooltip |

---

## 9. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-heatmap" role="img" aria-labelledby="hm-321-title">
  <header class="heatmap-header">
    <h3 id="hm-321-title" class="heatmap-title">Website Traffic by Hour</h3>
    <p class="heatmap-subtitle">Average visitors per hour, last 30 days</p>
  </header>
  
  <div class="heatmap-wrapper" style="--cell-size: 40px;">
    <table class="heatmap-grid" role="grid">
      <thead>
        <tr>
          <th class="heatmap-corner"></th>
          <th class="heatmap-col-header">Mon</th>
          <th class="heatmap-col-header">Tue</th>
          <!-- ... -->
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="heatmap-row-header">6 AM</th>
          <td class="heatmap-cell" style="--cell-value: 0.15;" 
              role="gridcell" tabindex="0" aria-label="6 AM Monday: 12 visitors">
            <span class="heatmap-cell-value">12</span>
          </td>
          <!-- ... -->
        </tr>
      </tbody>
    </table>
  </div>
  
  <footer class="heatmap-footer">
    <div class="heatmap-legend">
      <span class="legend-min">0</span>
      <div class="legend-gradient"></div>
      <span class="legend-max">500+</span>
    </div>
    <p class="heatmap-source">Source: Google Analytics</p>
  </footer>
</figure>
```

### 9.1 CSS Custom Properties
```css
.heatmap-cell {
  background-color: color-mix(
    in oklch,
    var(--heatmap-color-low) calc((1 - var(--cell-value)) * 100%),
    var(--heatmap-color-high) calc(var(--cell-value) * 100%)
  );
}
```

---

## 10. User Stories

### US-HM-01: Create Activity Heatmap
**As a** content author  
**I want to** create a heatmap showing activity patterns  
**So that** readers can identify peak times at a glance  

**Acceptance Criteria:**
- [ ] Can enter rows (times) and columns (days)
- [ ] Can enter numeric values
- [ ] Colors reflect value intensity
- [ ] Legend shows scale

### US-HM-02: Paste Spreadsheet Data
**As a** content author  
**I want to** paste data from Excel/Google Sheets  
**So that** I don't have to manually re-enter large datasets  

**Acceptance Criteria:**
- [ ] Paste area accepts tab-delimited data
- [ ] Row/column labels extracted from first row/column
- [ ] Data populates correctly
- [ ] Errors shown for malformed data

### US-HM-03: Custom Color Scale
**As a** content author  
**I want to** customize the color scale  
**So that** I can match my brand or highlight specific patterns  

**Acceptance Criteria:**
- [ ] Can select custom color mode
- [ ] Can pick low and high colors
- [ ] Gradient interpolates smoothly
- [ ] Legend updates to show custom colors

### US-HM-04: Diverging Values
**As a** content author  
**I want to** show positive and negative values with different colors  
**So that** readers can distinguish gains from losses  

**Acceptance Criteria:**
- [ ] Diverging color mode available
- [ ] Can set midpoint color (neutral)
- [ ] Negative values use one color
- [ ] Positive values use another color
- [ ] Zero/midpoint is neutral

