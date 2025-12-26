# Slopegraph Block Specification

> **Block Name:** `kunaal/slopegraph`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** D3.js (optional, can be pure SVG)

---

## 1. Overview

A slopegraph is a minimalist chart that shows change between exactly two points in time (or two conditions). Lines connect values, with slope indicating direction and magnitude of change. Ideal for showing before/after, year-over-year, or A/B comparisons.

### 1.1 Use Cases
- Year-over-year ranking changes
- Before/after intervention comparison
- A/B test results
- Policy impact visualization
- Budget allocation shifts

---

## 2. Visual Design

### 2.1 Layout
```
┌─────────────────────────────────────────────────────────────────┐
│  [Optional Title]                                               │
│  [Optional Subtitle]                                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│     2020                                             2024       │
│    ──────                                           ──────      │
│                                                                 │
│     Germany ●───────────────────────────────────────● Germany   │
│              89.2                                    94.1       │
│                                                                 │
│     France  ●─────────────────────────────● France              │
│              78.5                          75.2    ↓            │
│                                                                 │
│     Italy   ●───────────────────● Italy                         │
│              65.3                 62.1                          │
│                                                                 │
│     Spain   ●───────────────────────────────────────● Spain     │
│              52.1                                    58.9       │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  [Source note]                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Title | Newsreader | 24px | 500 | `--ink` |
| Subtitle | Inter | 14px | 400 | `--muted` |
| Column headers | Inter | 14px | 600 | `--ink` |
| Left labels | Inter | 13px | 500 | `--ink` |
| Right labels | Inter | 13px | 500 | `--ink` |
| Values | ui-monospace | 12px | 400 | `--muted` |
| Source | ui-monospace | 11px | 400 | `--muted` |

### 2.3 Dimensions
- **Min width:** 300px
- **Max width:** 800px
- **Row height:** 48px (adjustable: 36px compact, 60px spacious)
- **Dot radius:** 5px
- **Line thickness:** 2px
- **Left/Right padding:** 120px (for labels)

### 2.4 Color Logic

| Condition | Line Color | Dot Color |
|-----------|------------|-----------|
| Increase (positive) | `--warm` (#7D6B5D) | `--warm` |
| Decrease (negative) | `--terracotta` (#C9553D) | `--terracotta` |
| No change (< 0.5%) | `--muted` (#666) | `--muted` |
| Highlighted row | User-selected color | User-selected |

### 2.5 Hover State
- Line thickens to 3px
- Dots scale to 7px
- Tooltip shows: Label, Start Value → End Value, % Change
- Other lines fade to 30% opacity

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
  "leftColumnLabel": {
    "type": "string",
    "default": "Before"
  },
  "rightColumnLabel": {
    "type": "string",
    "default": "After"
  },
  "rowHeight": {
    "type": "string",
    "enum": ["compact", "normal", "spacious"],
    "default": "normal"
  },
  "showPercentChange": {
    "type": "boolean",
    "default": true
  },
  "showDirectionArrows": {
    "type": "boolean",
    "default": false
  },
  "valueFormat": {
    "type": "string",
    "enum": ["number", "percent", "currency", "decimal1", "decimal2"],
    "default": "number"
  },
  "currencySymbol": {
    "type": "string",
    "default": "$"
  },
  "sortBy": {
    "type": "string",
    "enum": ["none", "leftValue", "rightValue", "change", "alphabetical"],
    "default": "none"
  },
  "sortOrder": {
    "type": "string",
    "enum": ["asc", "desc"],
    "default": "desc"
  },
  "highlightedRows": {
    "type": "array",
    "default": [],
    "items": { "type": "number" }
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
        "leftValue": { "type": "number" },
        "rightValue": { "type": "number" }
      }
    }
  }
}
```

---

## 4. Editor Interface

### 4.1 Toolbar Controls
- **Sort:** Dropdown (None, By Start, By End, By Change, Alphabetical)
- **Value Format:** Dropdown (Number, %, $, 1 decimal, 2 decimals)
- **Row Density:** Button group (Compact / Normal / Spacious)

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input

#### Column Labels
- **Left Column** — Text input (e.g., "2020", "Before", "Control")
- **Right Column** — Text input (e.g., "2024", "After", "Treatment")

#### Display Options
- **Row Height** — Radio (Compact / Normal / Spacious)
- **Show % Change** — Toggle
- **Show Direction Arrows** — Toggle (↑ ↓ next to right values)
- **Value Format** — Dropdown
- **Currency Symbol** — Text (visible when currency selected)

#### Sorting
- **Sort By** — Dropdown
- **Sort Order** — Radio (Ascending / Descending)

#### Data Entry
- **Rows** — Table UI:
  ```
  ┌──────────────┬────────────┬─────────────┬───────────┐
  │  Label       │  Left Val  │  Right Val  │  Actions  │
  ├──────────────┼────────────┼─────────────┼───────────┤
  │  Germany     │  89.2      │  94.1       │  🎨 🗑️     │
  ├──────────────┼────────────┼─────────────┼───────────┤
  │  France      │  78.5      │  75.2       │  🎨 🗑️     │
  └──────────────┴────────────┴─────────────┴───────────┘
  │  [+ Add Row]                                        │
  └─────────────────────────────────────────────────────┘
  ```
  - 🎨 = Color picker for row highlight
  - 🗑️ = Delete row
  - Drag handle for reordering

---

## 5. Responsive Behavior

| Breakpoint | Layout | Label Position |
|------------|--------|----------------|
| < 480px | Stacked cards | Labels above values |
| 480-768px | Compressed | Labels truncated, values below dots |
| > 768px | Full | Labels beside dots |

### 5.1 Mobile Stacked Layout
```
┌─────────────────────────┐
│  Germany                │
│  2020: 89.2 → 2024: 94.1│
│  ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░ +5.5%│
├─────────────────────────┤
│  France                 │
│  2020: 78.5 → 2024: 75.2│
│  ▓▓▓▓▓▓▓▓▓▓▓▓░░░░░ -4.2%│
└─────────────────────────┘
```

---

## 6. Accessibility

### 6.1 ARIA Structure
```html
<figure class="wp-block-kunaal-slopegraph" role="img" aria-labelledby="slope-title-{id}">
  <figcaption id="slope-title-{id}">[Title]</figcaption>
  <div class="slopegraph-chart" role="list" aria-label="Comparison data">
    <div role="listitem" aria-label="Germany: increased from 89.2 to 94.1, up 5.5%">
      <!-- SVG line -->
    </div>
  </div>
</figure>
```

### 6.2 Keyboard Navigation
- Tab through rows
- Enter/Space to expand tooltip
- Escape to close tooltip

### 6.3 Data Table
```html
<details class="slopegraph-data-table">
  <summary>View data table</summary>
  <table>
    <thead>
      <tr><th>Label</th><th>2020</th><th>2024</th><th>Change</th></tr>
    </thead>
    <tbody>
      <tr><td>Germany</td><td>89.2</td><td>94.1</td><td>+5.5%</td></tr>
      <!-- ... -->
    </tbody>
  </table>
</details>
```

---

## 7. Animations

### 7.1 Entry Animation
1. Left dots appear (staggered, 30ms each)
2. Lines draw from left to right (400ms each, staggered)
3. Right dots pop in
4. Labels fade in

### 7.2 Hover Animation
- 200ms transition for opacity, stroke-width
- Tooltip fades in 150ms

---

## 8. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No data | Show placeholder "Add at least 2 data points" |
| Single row | Render normally, no comparison |
| Missing left value | Start line from left edge, gray |
| Missing right value | End line at right edge, gray |
| Equal values | Horizontal line, muted color |
| Large value difference | Scale lines appropriately, no overlap |
| Many rows (>15) | Enable scrolling or pagination |
| Very long labels | Truncate at 20 chars, full in tooltip |
| Negative values | Support, extend axis as needed |

---

## 9. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-slopegraph" role="img" aria-labelledby="slope-789-title">
  <div class="slopegraph-header">
    <h3 id="slope-789-title" class="slopegraph-title">Market Share Shift</h3>
    <p class="slopegraph-subtitle">2020 vs 2024, percentage points</p>
  </div>
  
  <div class="slopegraph-chart">
    <div class="slopegraph-columns">
      <span class="slopegraph-column-label slopegraph-column-left">2020</span>
      <span class="slopegraph-column-label slopegraph-column-right">2024</span>
    </div>
    
    <svg class="slopegraph-lines" viewBox="0 0 600 400" preserveAspectRatio="xMidYMid meet">
      <g class="slopegraph-row" data-change="positive" tabindex="0">
        <circle class="dot dot-left" cx="120" cy="50" r="5" />
        <line class="slope-line" x1="120" y1="50" x2="480" y2="30" />
        <circle class="dot dot-right" cx="480" cy="30" r="5" />
        <text class="label label-left" x="10" y="55">Germany</text>
        <text class="value value-left" x="100" y="55">89.2</text>
        <text class="label label-right" x="490" y="35">Germany</text>
        <text class="value value-right" x="570" y="35">94.1</text>
      </g>
      <!-- More rows... -->
    </svg>
  </div>
  
  <footer class="slopegraph-footer">
    <p class="slopegraph-source">Source: Industry reports</p>
  </footer>
  
  <details class="slopegraph-data-table">
    <summary>View data table</summary>
    <table><!-- ... --></table>
  </details>
</figure>
```

---

## 10. User Stories

### US-SG-01: Basic Slopegraph
**As a** content author  
**I want to** create a slopegraph comparing two time periods  
**So that** readers can see which items improved or declined  

**Acceptance Criteria:**
- [ ] Can add block from inserter
- [ ] Can enter data with labels and two values
- [ ] Lines render with correct slope direction
- [ ] Colors indicate positive/negative change
- [ ] Works in editor preview

### US-SG-02: Formatting Values
**As a** content author  
**I want to** format values as currency, percentages, or decimals  
**So that** the data displays in the appropriate format  

**Acceptance Criteria:**
- [ ] Number format works
- [ ] Percent format adds %
- [ ] Currency format adds symbol prefix
- [ ] Decimal formats work (1dp, 2dp)

### US-SG-03: Highlighting Key Rows
**As a** content author  
**I want to** highlight specific rows with custom colors  
**So that** I can draw attention to key comparisons  

**Acceptance Criteria:**
- [ ] Can select row for highlighting
- [ ] Can choose highlight color
- [ ] Highlighted row stands out visually
- [ ] Multiple rows can be highlighted

### US-SG-04: Mobile Experience
**As a** mobile reader  
**I want to** see the data in a readable format on small screens  
**So that** I don't have to pinch and zoom  

**Acceptance Criteria:**
- [ ] Stacked layout on mobile
- [ ] All data visible
- [ ] Change direction clear
- [ ] Touch targets adequate


