# Dumbbell Chart Block Specification

> **Block Name:** `kunaal/dumbbell-chart`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** D3.js or pure SVG

---

## 1. Overview

A dumbbell chart (also called a dot plot or gap chart) shows the difference between two values for each category. Unlike a slopegraph which emphasizes direction, a dumbbell chart emphasizes the gap magnitude and makes it easy to compare gaps across categories.

### 1.1 Use Cases
- Gender pay gaps across industries
- Before/after measurements
- Target vs actual performance
- Min/max ranges
- Survey response differences between groups

---

## 2. Visual Design

### 2.1 Layout
```
┌─────────────────────────────────────────────────────────────────┐
│  [Title]                                                        │
│  [Subtitle]                                                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Technology    ●══════════════════════════════════════●   $42K  │
│                $85K                                    $127K    │
│                                                                 │
│  Finance       ●════════════════════════════●             $38K  │
│                $72K                          $110K              │
│                                                                 │
│  Healthcare    ●══════════════════●                       $28K  │
│                $65K                $93K                         │
│                                                                 │
│  Retail        ●═══════●                                  $12K  │
│                $42K     $54K                                    │
│                                                                 │
│  ├─────────────┼─────────────┼─────────────┼─────────────┤     │
│  0            50K          100K          150K          200K     │
│                                                                 │
│  Legend: ● Start Value  ● End Value  ═══ Gap                   │
├─────────────────────────────────────────────────────────────────┤
│  [Source]                                                       │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Title | Newsreader | 24px | 500 | `--ink` |
| Subtitle | Inter | 14px | 400 | `--muted` |
| Category labels | Inter | 13px | 500 | `--ink` |
| Values | ui-monospace | 12px | 400 | `--muted` |
| Gap annotation | Inter | 12px | 600 | `--warm` |
| Axis labels | Inter | 11px | 400 | `--muted` |
| Legend | Inter | 12px | 400 | `--ink` |

### 2.3 Dimensions
- **Min width:** 400px
- **Max width:** 900px
- **Row height:** 56px (compact: 44px, spacious: 68px)
- **Dot radius:** 8px
- **Connector line thickness:** 4px
- **Left label width:** 140px
- **Right gap annotation width:** 60px

### 2.4 Color System

#### Default Palette
| Element | Color | Notes |
|---------|-------|-------|
| Start dot | `--warm` (#7D6B5D) | Primary brown |
| End dot | `--warmLight` (#B8A99A) | Lighter brown |
| Connector | Linear gradient | From start to end color |
| Gap text | `--warm` | Bold, right-aligned |
| Axis | `--muted` | Subtle |

#### Alternative Modes
- **Positive/Negative:** Connector color changes based on direction
- **Custom Colors:** User picks start/end colors
- **Gradient Fill:** Connector shows gradient based on gap size

### 2.5 Hover State
- Row background highlights (`--warmLight` at 10% opacity)
- Dots scale to 10px
- Tooltip shows: Category, Start Label: Value, End Label: Value, Gap: Value

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
  "startLabel": {
    "type": "string",
    "default": "Start"
  },
  "endLabel": {
    "type": "string",
    "default": "End"
  },
  "showGapAnnotation": {
    "type": "boolean",
    "default": true
  },
  "gapPrefix": {
    "type": "string",
    "default": ""
  },
  "gapSuffix": {
    "type": "string",
    "default": ""
  },
  "showAxis": {
    "type": "boolean",
    "default": true
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
    "enum": ["number", "percent", "currency", "decimal1", "decimal2", "compact"],
    "default": "number"
  },
  "currencySymbol": {
    "type": "string",
    "default": "$"
  },
  "sortBy": {
    "type": "string",
    "enum": ["none", "startValue", "endValue", "gap", "alphabetical"],
    "default": "gap"
  },
  "sortOrder": {
    "type": "string",
    "enum": ["asc", "desc"],
    "default": "desc"
  },
  "rowHeight": {
    "type": "string",
    "enum": ["compact", "normal", "spacious"],
    "default": "normal"
  },
  "colorMode": {
    "type": "string",
    "enum": ["theme", "direction", "custom"],
    "default": "theme"
  },
  "startColor": {
    "type": "string",
    "default": "#7D6B5D"
  },
  "endColor": {
    "type": "string",
    "default": "#B8A99A"
  },
  "showLegend": {
    "type": "boolean",
    "default": true
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
        "category": { "type": "string" },
        "startValue": { "type": "number" },
        "endValue": { "type": "number" }
      }
    }
  }
}
```

---

## 4. Editor Interface

### 4.1 Toolbar Controls
- **Sort:** Dropdown (By Gap ↓, By Gap ↑, By Start, By End, Alphabetical, Manual)
- **Format:** Dropdown (Number, %, $, Compact)
- **Show Axis:** Toggle button
- **Show Legend:** Toggle button

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input

#### Labels
- **Start Point Label** — Text input (e.g., "Female", "2020", "Budget")
- **End Point Label** — Text input (e.g., "Male", "2024", "Actual")

#### Gap Display
- **Show Gap Value** — Toggle
- **Gap Prefix** — Text (e.g., "$", "+")
- **Gap Suffix** — Text (e.g., "K", "%", "pts")

#### Axis Configuration
- **Show Axis** — Toggle
- **Min Value** — Number (leave empty for auto)
- **Max Value** — Number (leave empty for auto)
- **Value Format** — Dropdown
- **Currency Symbol** — Text (when currency selected)

#### Appearance
- **Row Height** — Radio (Compact / Normal / Spacious)
- **Color Mode** — Radio:
  - **Theme** — Uses default brown palette
  - **Direction** — Green for positive gap, red for negative
  - **Custom** — Shows two color pickers
- **Start Color** — Color picker (visible when Custom)
- **End Color** — Color picker (visible when Custom)
- **Show Legend** — Toggle

#### Data Entry
```
┌──────────────────┬─────────────┬─────────────┬──────────┐
│  Category        │  [Start]    │  [End]      │  Actions │
├──────────────────┼─────────────┼─────────────┼──────────┤
│  Technology      │  85000      │  127000     │  🗑️       │
├──────────────────┼─────────────┼─────────────┼──────────┤
│  Finance         │  72000      │  110000     │  🗑️       │
├──────────────────┼─────────────┼─────────────┼──────────┤
│  Healthcare      │  65000      │  93000      │  🗑️       │
└──────────────────┴─────────────┴─────────────┴──────────┘
│  [+ Add Row]  [Paste from Spreadsheet]                  │
└─────────────────────────────────────────────────────────┘
```

---

## 5. Responsive Behavior

| Breakpoint | Layout Changes |
|------------|----------------|
| < 480px | Stacked cards, horizontal mini-dumbbells |
| 480-768px | Gap annotation hidden, values shown on hover |
| 768-1024px | Compressed padding |
| > 1024px | Full layout |

### 5.1 Mobile Card Layout
```
┌───────────────────────────────────┐
│  Technology                       │
│  ●════════════════════════════●   │
│  Female: $85K      Male: $127K    │
│  Gap: $42K                        │
├───────────────────────────────────┤
│  Finance                          │
│  ●════════════════════●           │
│  Female: $72K      Male: $110K    │
│  Gap: $38K                        │
└───────────────────────────────────┘
```

---

## 6. Accessibility

### 6.1 ARIA Structure
```html
<figure class="wp-block-kunaal-dumbbell-chart" role="img" aria-labelledby="dumb-title-{id}">
  <figcaption id="dumb-title-{id}">[Title]</figcaption>
  <div class="dumbbell-chart" role="list" aria-label="Comparison showing gaps">
    <div class="dumbbell-row" role="listitem" 
         aria-label="Technology: Female $85,000, Male $127,000, gap of $42,000">
      <!-- SVG -->
    </div>
  </div>
</figure>
```

### 6.2 Keyboard Navigation
- Tab navigates between rows
- Enter/Space activates tooltip
- Arrow keys move focus up/down within chart

### 6.3 Screen Reader Summary
```html
<div class="sr-only">
  Dumbbell chart comparing [Start Label] vs [End Label] across [N] categories.
  Largest gap: [Category] at [Gap Value].
  Smallest gap: [Category] at [Gap Value].
</div>
```

---

## 7. Animations

### 7.1 Entry Animation
1. Category labels slide in from left (staggered)
2. Start dots appear
3. Connector bar grows from start to end
4. End dots pop in
5. Gap annotations fade in

### 7.2 Hover Animation
- 200ms transition
- Row background fade
- Dot scale with ease-out

---

## 8. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No data | Show placeholder |
| Single row | Render full width |
| Zero gap | Dots overlap, no connector shown |
| Negative gap (start > end) | Show correctly, connector goes left |
| Very large gap differences | Scale appropriately |
| Missing value | Show single dot with "N/A" |
| Many rows (>20) | Enable virtual scrolling |
| Very long category names | Truncate at 25 chars, full in tooltip |

---

## 9. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-dumbbell-chart" role="img" aria-labelledby="dumb-456-title">
  <header class="dumbbell-header">
    <h3 id="dumb-456-title" class="dumbbell-title">Gender Pay Gap by Industry</h3>
    <p class="dumbbell-subtitle">Median annual salary, 2024</p>
  </header>
  
  <div class="dumbbell-chart">
    <svg class="dumbbell-visual" viewBox="0 0 800 400">
      <!-- Axis -->
      <g class="dumbbell-axis">
        <line x1="140" y1="380" x2="700" y2="380" />
        <text x="140" y="395">$0</text>
        <text x="280" y="395">$50K</text>
        <!-- ... -->
      </g>
      
      <!-- Rows -->
      <g class="dumbbell-row" data-category="technology" tabindex="0">
        <text class="dumbbell-label" x="10" y="45">Technology</text>
        <circle class="dumbbell-dot dumbbell-dot-start" cx="270" cy="40" r="8" />
        <rect class="dumbbell-connector" x="270" y="38" width="230" height="4" rx="2" />
        <circle class="dumbbell-dot dumbbell-dot-end" cx="500" cy="40" r="8" />
        <text class="dumbbell-value dumbbell-value-start" x="260" y="60">$85K</text>
        <text class="dumbbell-value dumbbell-value-end" x="510" y="60">$127K</text>
        <text class="dumbbell-gap" x="720" y="45">$42K</text>
      </g>
      <!-- More rows... -->
    </svg>
  </div>
  
  <footer class="dumbbell-footer">
    <div class="dumbbell-legend">
      <span class="legend-item"><span class="legend-dot legend-dot-start"></span> Female</span>
      <span class="legend-item"><span class="legend-dot legend-dot-end"></span> Male</span>
    </div>
    <p class="dumbbell-source">Source: Bureau of Labor Statistics</p>
  </footer>
  
  <details class="dumbbell-data-table">
    <summary>View data table</summary>
    <table><!-- ... --></table>
  </details>
</figure>
```

---

## 10. User Stories

### US-DB-01: Create Pay Gap Chart
**As a** content author  
**I want to** create a dumbbell chart showing salary gaps  
**So that** readers can compare differences across categories  

**Acceptance Criteria:**
- [ ] Can add block and enter data
- [ ] Dots and connectors render correctly
- [ ] Gap values display on right
- [ ] Sorting works (by gap, alphabetical, etc.)

### US-DB-02: Format Currency Values
**As a** content author  
**I want to** display values as currency with compact notation  
**So that** large numbers are readable ($127K instead of $127,000)  

**Acceptance Criteria:**
- [ ] Currency symbol applies
- [ ] Compact notation works (K, M, B)
- [ ] Gap value matches format
- [ ] Axis labels match format

### US-DB-03: Direction-Based Colors
**As a** content author  
**I want to** color connectors based on whether the gap is positive or negative  
**So that** readers can quickly identify direction of difference  

**Acceptance Criteria:**
- [ ] Positive gaps show one color
- [ ] Negative gaps show another color
- [ ] Legend updates to reflect colors
- [ ] Colors are accessible (contrast ratio)

### US-DB-04: Mobile Usability
**As a** mobile reader  
**I want to** see the dumbbell chart in a readable card format  
**So that** I can understand the data without horizontal scrolling  

**Acceptance Criteria:**
- [ ] Cards stack vertically on mobile
- [ ] All data visible per card
- [ ] Mini horizontal dumbbell in each card
- [ ] Gap clearly labeled

