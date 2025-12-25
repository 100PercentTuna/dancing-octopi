# Box Plot & Violin Chart Block Specification

> **Block Name:** `kunaal/statistical-distribution`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** D3.js

---

## 1. Overview

This block provides two related statistical visualization types:
- **Box Plot (Box-and-Whisker):** Shows median, quartiles, and outliers
- **Violin Plot:** Shows full distribution shape with kernel density estimation

Both can be displayed together as a "combo" view.

### 1.1 Use Cases
- Salary distributions across departments
- Test score spreads across schools
- Response time distributions
- Scientific measurement variability
- Survey rating distributions

---

## 2. Visual Design

### 2.1 Box Plot Layout
```
                    Min    Q1   Median  Q3    Max
                     │      │      │     │      │
                     ├──────┼──────┼─────┼──────┤
    Engineering   ─────┬────[========|========]────┬─────  ○  ○
                       │                          │      outliers
                       whisker                    whisker

    Design        ───┬────[====|=============]────────┬───

    Marketing     ─────┬──[=|====]──┬─────
```

### 2.2 Violin Plot Layout
```
    Engineering   ╱╲         Distribution shape
                 ╱  ╲        (kernel density)
                ╱    ╲
               │      │
               │  ●   │      ← Median dot
               │      │
                ╲    ╱
                 ╲  ╱
                  ╲╱

    Design       ╱╲
               ╱    ╲
              │      │
              │  ●   │
               ╲    ╱
                ╲╱
```

### 2.3 Combo View
```
    Engineering   ╱╲
                 ╱ [|] ╲      ← Box inside violin
                ╱ [===] ╲
               │   │    │
                ╲ [===] ╱
                 ╲ [|] ╱
                  ╲╱
```

### 2.4 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Title | Newsreader | 24px | 500 | `--ink` |
| Subtitle | Inter | 14px | 400 | `--muted` |
| Category labels | Inter | 13px | 500 | `--ink` |
| Axis labels | Inter | 11px | 400 | `--muted` |
| Statistics | ui-monospace | 11px | 400 | `--muted` |

### 2.5 Colors

#### Box Plot
| Element | Color |
|---------|-------|
| Box fill | `--warmLight` (#B8A99A) at 60% opacity |
| Box stroke | `--warm` (#7D6B5D) |
| Median line | `--ink` (2px) |
| Whiskers | `--muted` |
| Outliers | `--terracotta` (#C9553D) |
| Mean marker (optional) | `--blue` diamond |

#### Violin Plot
| Element | Color |
|---------|-------|
| Violin fill | `--warmLight` at 40% opacity |
| Violin stroke | `--warm` |
| Median dot | `--ink` |
| Quartile lines (optional) | `--muted` dashed |

### 2.6 Dimensions
- **Plot height:** 60px per category (adjustable)
- **Max violin width:** 80px
- **Outlier dot radius:** 4px
- **Median line thickness:** 2px

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
    "enum": ["box", "violin", "combo"],
    "default": "box"
  },
  "orientation": {
    "type": "string",
    "enum": ["horizontal", "vertical"],
    "default": "horizontal"
  },
  "showMean": {
    "type": "boolean",
    "default": false
  },
  "showOutliers": {
    "type": "boolean",
    "default": true
  },
  "showDataPoints": {
    "type": "boolean",
    "default": false
  },
  "showStatistics": {
    "type": "boolean",
    "default": false
  },
  "axisMin": {
    "type": "number",
    "default": null
  },
  "axisMax": {
    "type": "number",
    "default": null
  },
  "valueFormat": {
    "type": "string",
    "enum": ["number", "currency", "percent", "decimal1", "decimal2"],
    "default": "number"
  },
  "currencySymbol": {
    "type": "string",
    "default": "$"
  },
  "dataGroups": {
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
  "precomputedStats": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "label": { "type": "string" },
        "min": { "type": "number" },
        "q1": { "type": "number" },
        "median": { "type": "number" },
        "q3": { "type": "number" },
        "max": { "type": "number" },
        "mean": { "type": "number" },
        "outliers": { 
          "type": "array",
          "items": { "type": "number" }
        }
      }
    }
  },
  "dataMode": {
    "type": "string",
    "enum": ["raw", "precomputed"],
    "default": "raw"
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
- **Chart Type:** Button group (Box / Violin / Combo)
- **Orientation:** Button group (Horizontal / Vertical)
- **Show Mean:** Toggle
- **Show Outliers:** Toggle

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input

#### Chart Type
- **Type** — Radio with visual preview:
  - **Box Plot** — Traditional box-and-whisker
  - **Violin Plot** — Distribution shape
  - **Combo** — Violin with box inside

#### Orientation
- **Orientation** — Radio (Horizontal / Vertical)

#### Data Entry
- **Input Mode** — Radio:
  - **Raw Values** — Enter all data points
  - **Precomputed Stats** — Enter summary statistics

##### Raw Values Mode
```
┌─────────────────────────────────────────────────────────┐
│  📊 Data Groups                                         │
├─────────────────────────────────────────────────────────┤
│  Group: Engineering                              [🗑️]   │
│  Values: 75000, 82000, 91000, 88000, 95000, 120000...  │
│  [Paste values]                                        │
├─────────────────────────────────────────────────────────┤
│  Group: Design                                   [🗑️]   │
│  Values: 68000, 72000, 75000, 71000, 80000...          │
│  [Paste values]                                        │
├─────────────────────────────────────────────────────────┤
│  [+ Add Group]                                         │
└─────────────────────────────────────────────────────────┘
```

##### Precomputed Stats Mode
```
┌───────────────┬────────┬────────┬────────┬────────┬────────┐
│  Label        │  Min   │   Q1   │ Median │   Q3   │  Max   │
├───────────────┼────────┼────────┼────────┼────────┼────────┤
│  Engineering  │ 75000  │ 82000  │ 88000  │ 95000  │ 140000 │
├───────────────┼────────┼────────┼────────┼────────┼────────┤
│  Design       │ 68000  │ 71000  │ 75000  │ 80000  │ 92000  │
└───────────────┴────────┴────────┴────────┴────────┴────────┘
│  [+ Add Row]                                               │
└────────────────────────────────────────────────────────────┘
```

#### Display Options
- **Show Mean** — Toggle (shows diamond marker)
- **Show Outliers** — Toggle
- **Show Individual Data Points** — Toggle (jittered dots)
- **Show Statistics Panel** — Toggle (summary stats below chart)

#### Axis Settings
- **Min Value** — Number input (auto if empty)
- **Max Value** — Number input (auto if empty)
- **Value Format** — Dropdown
- **Currency Symbol** — Text (when currency selected)

#### Colors
- **Color per Group** — Color pickers for each group (optional override)

---

## 5. Statistical Calculations

### 5.1 Box Plot Statistics
```javascript
// For raw data, calculate:
const sorted = values.sort((a, b) => a - b);
const n = sorted.length;

const median = percentile(sorted, 50);
const q1 = percentile(sorted, 25);
const q3 = percentile(sorted, 75);
const iqr = q3 - q1;

const lowerFence = q1 - 1.5 * iqr;
const upperFence = q3 + 1.5 * iqr;

const min = sorted.find(v => v >= lowerFence) || sorted[0];
const max = sorted.findLast(v => v <= upperFence) || sorted[n-1];

const outliers = sorted.filter(v => v < lowerFence || v > upperFence);
const mean = sorted.reduce((a, b) => a + b, 0) / n;
```

### 5.2 Violin Plot Kernel Density
```javascript
// Gaussian kernel density estimation
function kde(data, bandwidth) {
  const kernel = (x) => Math.exp(-0.5 * x * x) / Math.sqrt(2 * Math.PI);
  
  return function(x) {
    return data.reduce((sum, xi) => {
      return sum + kernel((x - xi) / bandwidth);
    }, 0) / (data.length * bandwidth);
  };
}
```

---

## 6. Responsive Behavior

| Breakpoint | Box Plot | Violin Plot |
|------------|----------|-------------|
| < 480px | Vertical only, compact | Simplified, no inner marks |
| 480-768px | Either orientation | Standard |
| > 768px | Full features | Full features |

### 6.1 Mobile Layout (Vertical)
```
        Engineering
      ┌─────────────┐
      │    ┌───┐    │
      │    │   │    │
      │    │ ● │    │
      │    │   │    │
      │    └───┘    │
      │      │      │
   ○  │      │      │
      └─────────────┘
   
        Design
      ┌─────────────┐
      ...
```

---

## 7. Accessibility

### 7.1 ARIA Structure
```html
<figure class="wp-block-kunaal-statistical-distribution" role="img" 
        aria-labelledby="stat-title-{id}" aria-describedby="stat-desc-{id}">
  <figcaption id="stat-title-{id}">[Title]</figcaption>
  <div id="stat-desc-{id}" class="sr-only">
    Distribution chart showing [N] groups.
    Engineering: median $88,000, range $75,000 to $140,000.
    Design: median $75,000, range $68,000 to $92,000.
  </div>
  <svg class="stat-chart"><!-- Chart --></svg>
</figure>
```

### 7.2 Statistics Panel
When enabled, provides screen-reader-friendly summary:
```html
<table class="stat-summary" role="table">
  <caption class="sr-only">Summary statistics</caption>
  <thead><tr><th>Group</th><th>Min</th><th>Q1</th><th>Median</th><th>Q3</th><th>Max</th></tr></thead>
  <tbody>
    <tr><td>Engineering</td><td>$75K</td><td>$82K</td><td>$88K</td><td>$95K</td><td>$140K</td></tr>
  </tbody>
</table>
```

---

## 8. Animations

### 8.1 Entry Animation
- Box: Grows from median outward
- Whiskers: Extend from box edges
- Violin: Morphs from line to full shape
- Outliers: Pop in with scale animation

### 8.2 Hover Animation
- Box/violin highlights (stroke thickens)
- Tooltip appears with statistics
- Other groups fade slightly

---

## 9. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No data | Placeholder message |
| Single group | Render centered |
| < 5 data points | Show warning, still render |
| No outliers | Don't show outlier markers |
| All same values | Show single line |
| Negative values | Extend axis appropriately |
| Very different scales | Option to normalize or show warning |

---

## 10. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-statistical-distribution stat-dist--box stat-dist--horizontal">
  <header class="stat-dist-header">
    <h3 class="stat-dist-title">Salary Distribution by Department</h3>
    <p class="stat-dist-subtitle">Annual compensation, 2024</p>
  </header>
  
  <div class="stat-dist-chart">
    <svg viewBox="0 0 600 300">
      <!-- Axis -->
      <g class="stat-axis stat-axis-x">
        <line x1="100" y1="280" x2="580" y2="280" />
        <text x="100" y="295">$60K</text>
        <!-- ... -->
      </g>
      
      <!-- Box plot group -->
      <g class="stat-group" data-group="engineering" transform="translate(0, 50)">
        <text class="stat-label" x="10" y="25">Engineering</text>
        
        <!-- Whiskers -->
        <line class="stat-whisker stat-whisker-left" x1="150" y1="20" x2="180" y2="20" />
        <line class="stat-whisker stat-whisker-right" x1="350" y1="20" x2="380" y2="20" />
        <line class="stat-whisker-connector" x1="150" y1="20" x2="380" y2="20" stroke-dasharray="2,2" />
        
        <!-- Box -->
        <rect class="stat-box" x="180" y="10" width="170" height="20" rx="3" />
        
        <!-- Median -->
        <line class="stat-median" x1="260" y1="8" x2="260" y2="32" />
        
        <!-- Mean (optional) -->
        <polygon class="stat-mean" points="290,20 296,14 302,20 296,26" />
        
        <!-- Outliers -->
        <circle class="stat-outlier" cx="420" cy="20" r="4" />
        <circle class="stat-outlier" cx="450" cy="20" r="4" />
      </g>
      <!-- More groups... -->
    </svg>
  </div>
  
  <footer class="stat-dist-footer">
    <p class="stat-dist-source">Source: HR database</p>
  </footer>
  
  <details class="stat-dist-data">
    <summary>View statistics</summary>
    <table><!-- Full stats table --></table>
  </details>
</figure>
```

---

## 11. User Stories

### US-BV-01: Create Box Plot
**As a** content author  
**I want to** create a box plot from raw data values  
**So that** readers can see the distribution of data  

**Acceptance Criteria:**
- [ ] Can enter comma-separated values per group
- [ ] Box plot renders with correct quartiles
- [ ] Whiskers extend to appropriate values
- [ ] Outliers displayed correctly

### US-BV-02: Violin Plot Distribution
**As a** content author  
**I want to** show the full distribution shape  
**So that** readers can see data density  

**Acceptance Criteria:**
- [ ] Violin shape reflects data distribution
- [ ] Bimodal distributions show two peaks
- [ ] Median marker visible
- [ ] Smooth, aesthetic curves

### US-BV-03: Precomputed Statistics
**As a** content author  
**I want to** enter summary statistics directly  
**So that** I can display data without sharing raw values  

**Acceptance Criteria:**
- [ ] Can switch to precomputed mode
- [ ] Can enter min, Q1, median, Q3, max
- [ ] Box plot renders from stats
- [ ] Validation for Q1 < median < Q3

### US-BV-04: Compare Groups
**As a** reader  
**I want to** compare distributions across groups  
**So that** I can identify differences  

**Acceptance Criteria:**
- [ ] Multiple groups displayed aligned
- [ ] Same scale for all groups
- [ ] Hover highlights single group
- [ ] Tooltip shows group statistics

