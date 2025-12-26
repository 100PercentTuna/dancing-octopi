# Advanced Map Visualizations Block Specification

> **Block Name:** `kunaal/data-map`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** Leaflet.js, D3.js (for gradients)

---

## 1. Overview

Advanced map visualizations display geographic data with various encoding methods. This block extends the basic map with data visualization capabilities.

### 1.1 Visualization Types

| Type | Description | Use Case |
|------|-------------|----------|
| **Choropleth** | Countries/regions filled by value | GDP by country, election results |
| **Proportional Symbols (Dots)** | Circles sized by value | City populations, event counts |
| **Graduated Colors (Dot Gradient)** | Dots colored by value gradient | Temperature readings, ratings |
| **Combined** | Choropleth + dots | Multiple metrics overlay |

### 1.2 Use Cases
- Population density by region
- Sales by city (dot size = revenue)
- Temperature readings across locations (gradient colors)
- Multi-variable: GDP (choropleth) + capitals (dots)

---

## 2. Visual Design

### 2.1 Choropleth Map
```
┌─────────────────────────────────────────────────────────────────┐
│  [Title]                                                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│     ┌─────────────────────────────────────────┐                │
│     │   ░░░░░░░░     ▓▓▓▓▓▓▓     ████████    │                │
│     │  ░░░ Low ░░░   ▓▓▓Med▓▓▓   ████High████ │                │
│     │   ░░░░░░░░     ▓▓▓▓▓▓▓     ████████    │                │
│     └─────────────────────────────────────────┘                │
│                                                                 │
│  Legend: ░ 0-33%  ▒▒ 34-66%  ▓▓▓ 67-100%                       │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Proportional Symbols (Dots)
```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│     ┌─────────────────────────────────────────┐                │
│     │                  ●                       │                │
│     │        ●           Large (NYC)          │                │
│     │   ●                    ●                │                │
│     │  Small   Medium          ●              │                │
│     │   (x)      (y)                          │                │
│     └─────────────────────────────────────────┘                │
│                                                                 │
│  ● = Population:  ○ <1M  ● 1-5M  ● >5M                         │
└─────────────────────────────────────────────────────────────────┘
```

### 2.3 Graduated Dot Colors
```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│     ┌─────────────────────────────────────────┐                │
│     │         🔵                               │                │
│     │    🟢      (Cold)                        │                │
│     │                    🟡                    │                │
│     │  (Medium)              🟠                │                │
│     │                            🔴 (Hot)      │                │
│     └─────────────────────────────────────────┘                │
│                                                                 │
│  Temperature: 🔵 < 0°  🟢 0-15°  🟡 15-25°  🟠 25-35°  🔴 > 35° │
└─────────────────────────────────────────────────────────────────┘
```

### 2.4 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Title | Newsreader | 24px | 500 | `--ink` |
| Subtitle | Inter | 14px | 400 | `--muted` |
| Location labels | Inter | 11px | 500 | `--ink` |
| Tooltip title | Inter | 13px | 600 | `--ink` |
| Tooltip value | ui-monospace | 14px | 500 | `--warm` |
| Legend labels | Inter | 11px | 400 | `--muted` |

### 2.5 Color Palettes

#### Sequential (Single Hue) - Theme Default
```
--map-seq-1: #F5F0EB  (lightest)
--map-seq-2: #E8DFD5
--map-seq-3: #D4C4B5
--map-seq-4: #B8A99A
--map-seq-5: #8B7355
--map-seq-6: #7D6B5D
--map-seq-7: #5C4A3D  (darkest)
```

#### Diverging (Two Hues)
```
Negative          Neutral           Positive
#C9553D ─── #D4887A ─── #F5F0EB ─── #A08B7A ─── #7D6B5D
(terracotta)                                    (brown)
```

#### Categorical (For discrete categories)
```
--map-cat-1: #7D6B5D   (Brown)
--map-cat-2: #4A90A4   (Blue)
--map-cat-3: #C9553D   (Terracotta)
--map-cat-4: #8B7355   (Sienna)
--map-cat-5: #6B8F71   (Sage)
--map-cat-6: #9B8AA6   (Mauve)
```

### 2.6 Dot Sizing Scale
```javascript
// Min and max dot radius based on value range
const minRadius = 4;   // px
const maxRadius = 40;  // px

// Square root scale for perceptual accuracy
radius = minRadius + Math.sqrt(normalizedValue) * (maxRadius - minRadius);
```

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
  "mapType": {
    "type": "string",
    "enum": ["choropleth", "dots", "gradient-dots", "combined"],
    "default": "choropleth"
  },
  "baseMap": {
    "type": "string",
    "enum": ["world", "usa", "europe", "custom-geojson"],
    "default": "world"
  },
  "customGeoJSON": {
    "type": "string",
    "default": ""
  },
  "centerLat": {
    "type": "number",
    "default": 25
  },
  "centerLng": {
    "type": "number",
    "default": 0
  },
  "initialZoom": {
    "type": "number",
    "default": 2
  },
  "enableZoom": {
    "type": "boolean",
    "default": true
  },
  "enablePan": {
    "type": "boolean",
    "default": true
  },
  "showLabels": {
    "type": "boolean",
    "default": false
  },
  "regionData": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "code": { "type": "string" },
        "value": { "type": "number" },
        "label": { "type": "string" }
      }
    }
  },
  "pointData": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "lat": { "type": "number" },
        "lng": { "type": "number" },
        "value": { "type": "number" },
        "label": { "type": "string" },
        "category": { "type": "string" }
      }
    }
  },
  "colorScale": {
    "type": "string",
    "enum": ["sequential", "diverging", "categorical"],
    "default": "sequential"
  },
  "colorLow": {
    "type": "string",
    "default": "#F5F0EB"
  },
  "colorHigh": {
    "type": "string",
    "default": "#7D6B5D"
  },
  "colorMid": {
    "type": "string",
    "default": "#F5F0EB"
  },
  "colorNegative": {
    "type": "string",
    "default": "#C9553D"
  },
  "dotSizeMin": {
    "type": "number",
    "default": 4
  },
  "dotSizeMax": {
    "type": "number",
    "default": 40
  },
  "dotOpacity": {
    "type": "number",
    "default": 0.7
  },
  "dotBorderColor": {
    "type": "string",
    "default": "#FFFFFF"
  },
  "valueLabel": {
    "type": "string",
    "default": "Value"
  },
  "valueFormat": {
    "type": "string",
    "enum": ["number", "percent", "currency", "compact", "decimal1"],
    "default": "number"
  },
  "currencySymbol": {
    "type": "string",
    "default": "$"
  },
  "valueSuffix": {
    "type": "string",
    "default": ""
  },
  "showLegend": {
    "type": "boolean",
    "default": true
  },
  "legendPosition": {
    "type": "string",
    "enum": ["bottom-left", "bottom-right", "top-left", "top-right"],
    "default": "bottom-right"
  },
  "legendTitle": {
    "type": "string",
    "default": ""
  },
  "height": {
    "type": "number",
    "default": 500
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
- **Map Type:** Button group (Choropleth / Dots / Gradient / Combined)
- **Base Map:** Dropdown (World, USA, Europe, Custom)
- **Show Labels:** Toggle
- **Show Legend:** Toggle

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input
- **Height** — Number (300-800px)

#### Map Type
- **Visualization** — Radio with visual previews:
  - **Choropleth** — Regions filled by value
  - **Proportional Dots** — Circles sized by value
  - **Gradient Dots** — Circles colored by value (fixed size or sized)
  - **Combined** — Choropleth + dots

#### Base Map
- **Region** — Dropdown (World, USA states, Europe, Custom GeoJSON)
- **Custom GeoJSON URL** — Text (visible when Custom selected)
- **Initial Center** — Lat/Lng inputs
- **Initial Zoom** — Slider (1-10)

#### Interaction
- **Enable Zoom** — Toggle
- **Enable Pan** — Toggle

#### Data Entry - Choropleth
```
┌─────────────────────────────────────────────────────────────────┐
│  📊 Region Data                                                 │
├──────────────────┬─────────────────┬────────────────────────────┤
│  Region Code     │  Value          │  Label (optional)          │
├──────────────────┼─────────────────┼────────────────────────────┤
│  US              │  320000000      │  United States             │
├──────────────────┼─────────────────┼────────────────────────────┤
│  DE              │  83000000       │  Germany                   │
├──────────────────┼─────────────────┼────────────────────────────┤
│  [+ Add Region]                                                 │
└─────────────────────────────────────────────────────────────────┘
│  [Paste from Spreadsheet]  [Import CSV]                         │
└─────────────────────────────────────────────────────────────────┘
```

#### Data Entry - Points
```
┌─────────────────────────────────────────────────────────────────┐
│  📍 Point Data                                                  │
├───────────┬───────────┬───────────┬─────────────┬───────────────┤
│  Lat      │  Lng      │  Value    │  Label      │  Category     │
├───────────┼───────────┼───────────┼─────────────┼───────────────┤
│  40.7128  │  -74.0060 │  8400000  │  New York   │  [Major ▼]    │
├───────────┼───────────┼───────────┼─────────────┼───────────────┤
│  34.0522  │  -118.244 │  3900000  │  Los Angeles│  [Major ▼]    │
├───────────┼───────────┼───────────┼─────────────┼───────────────┤
│  [+ Add Point]  [📍 Pick on Map]                                │
└─────────────────────────────────────────────────────────────────┘
```
- "Pick on Map" opens interactive map picker

#### Color Configuration
- **Color Scale** — Radio (Sequential / Diverging / Categorical)

**Sequential:**
- Low Color — Color picker
- High Color — Color picker
- Steps — Number (3-9)

**Diverging:**
- Negative Color — Color picker
- Neutral Color — Color picker
- Positive Color — Color picker
- Midpoint Value — Number (auto = 0)

**Categorical:**
- Category colors — Dynamic list based on unique categories in data

#### Dot Settings (when applicable)
- **Min Size** — Slider (2-20px)
- **Max Size** — Slider (20-60px)
- **Opacity** — Slider (0.3-1.0)
- **Border Color** — Color picker (default white)
- **Border Width** — Slider (0-3px)

#### Value Display
- **Value Label** — Text (appears in tooltip as "Population: 8.4M")
- **Format** — Dropdown (Number, %, $, Compact, 1 decimal)
- **Currency Symbol** — Text (when currency)
- **Suffix** — Text (e.g., "people", "°C")

#### Legend
- **Show Legend** — Toggle
- **Position** — Dropdown (bottom-left, bottom-right, etc.)
- **Title** — Text
- **Show Ticks** — Toggle

---

## 5. Legend Designs

### 5.1 Choropleth Legend (Continuous)
```
┌──────────────────────────────────────┐
│  GDP per Capita                      │
│  ╔════════════════════════════════╗  │
│  ║ ░░░░▒▒▒▒▓▓▓▓████ ║  │
│  ╚════════════════════════════════╝  │
│  $1K              $50K           $100K│
└──────────────────────────────────────┘
```

### 5.2 Choropleth Legend (Discrete Steps)
```
┌──────────────────────────┐
│  Population Density      │
│  ░ < 50                  │
│  ▒ 50 - 100             │
│  ▓ 100 - 500            │
│  █ > 500                │
└──────────────────────────┘
```

### 5.3 Dot Size Legend
```
┌──────────────────────────┐
│  City Population         │
│  ○  1M                   │
│  ●  5M                   │
│  ●  10M+                 │
└──────────────────────────┘
```

### 5.4 Gradient Dot Legend
```
┌──────────────────────────┐
│  Temperature (°C)        │
│  🔵 < 0                  │
│  🟢 0-15                 │
│  🟡 15-25                │
│  🟠 25-35                │
│  🔴 > 35                 │
└──────────────────────────┘
```

---

## 6. Responsive Behavior

| Breakpoint | Changes |
|------------|---------|
| < 480px | Height 300px, legend below map, simplified controls |
| 480-768px | Height 400px, legend inside |
| > 768px | Full height, all features |

---

## 7. Accessibility

### 7.1 ARIA Structure
```html
<figure class="wp-block-kunaal-data-map" role="img"
        aria-labelledby="map-title-{id}" aria-describedby="map-desc-{id}">
  <figcaption id="map-title-{id}">[Title]</figcaption>
  <div id="map-desc-{id}" class="sr-only">
    Choropleth map showing [metric] across [N] regions.
    Highest: [Region] at [Value].
    Lowest: [Region] at [Value].
  </div>
  <div class="map-container" aria-hidden="true">
    <!-- Leaflet map -->
  </div>
</figure>
```

### 7.2 Data Table Fallback
```html
<details class="map-data-table">
  <summary>View data table</summary>
  <table>
    <caption>GDP by Country</caption>
    <thead><tr><th>Country</th><th>GDP</th></tr></thead>
    <tbody>
      <tr><td>United States</td><td>$25.46T</td></tr>
      <!-- ... -->
    </tbody>
  </table>
</details>
```

---

## 8. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No data | Show base map with "No data" message |
| Region not found | Skip silently, log warning |
| Invalid coordinates | Skip point, show warning |
| All same values | Single color, note in legend |
| Extreme outliers | Option to cap/exclude |
| Overlapping dots | Cluster or offset |
| Missing values | Show as "No data" with pattern fill |

---

## 9. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-data-map map--choropleth">
  <header class="map-header">
    <h3 class="map-title">Global GDP Distribution</h3>
    <p class="map-subtitle">GDP in billions USD, 2024</p>
  </header>
  
  <div class="map-container" style="height: 500px;">
    <div id="data-map-{id}" 
         data-type="choropleth"
         data-region="world"
         data-values='[{"code":"US","value":25460,"label":"United States"}]'
         data-color-low="#F5F0EB"
         data-color-high="#7D6B5D">
    </div>
    
    <div class="map-legend map-legend--bottom-right">
      <h4 class="legend-title">GDP (Billions USD)</h4>
      <div class="legend-gradient">
        <div class="legend-bar"></div>
        <div class="legend-labels">
          <span>$0</span>
          <span>$25T</span>
        </div>
      </div>
    </div>
    
    <div class="map-tooltip" role="tooltip" hidden>
      <h4 class="tooltip-region"></h4>
      <p class="tooltip-value"></p>
    </div>
  </div>
  
  <footer class="map-footer">
    <p class="map-source">Source: World Bank</p>
  </footer>
  
  <details class="map-data-table">
    <summary>View data table</summary>
    <table><!-- ... --></table>
  </details>
</figure>
```

---

## 10. User Stories

### US-MP-01: Create Choropleth Map
**As a** content author  
**I want to** create a map showing values by country/region  
**So that** readers can see geographic patterns  

**Acceptance Criteria:**
- [ ] Can select base map (world, USA, etc.)
- [ ] Can enter region codes and values
- [ ] Colors interpolate correctly
- [ ] Legend shows scale

### US-MP-02: Proportional Symbol Map
**As a** content author  
**I want to** show cities as dots sized by population  
**So that** readers can compare city sizes  

**Acceptance Criteria:**
- [ ] Can add points with lat/lng/value
- [ ] Dots scale proportionally
- [ ] Hover shows details
- [ ] Legend shows size scale

### US-MP-03: Gradient Color Dots
**As a** content author  
**I want to** show temperature readings with color-coded dots  
**So that** readers can see temperature variation  

**Acceptance Criteria:**
- [ ] Dots colored by value gradient
- [ ] Min value = low color, max = high color
- [ ] Interpolation smooth
- [ ] Legend shows color scale

### US-MP-04: Pick Location on Map
**As a** content author  
**I want to** click on the map to add a point  
**So that** I don't have to look up coordinates  

**Acceptance Criteria:**
- [ ] "Pick on Map" button opens map picker
- [ ] Click on map adds point with coordinates
- [ ] Can edit label and value after placement
- [ ] Can drag to reposition


