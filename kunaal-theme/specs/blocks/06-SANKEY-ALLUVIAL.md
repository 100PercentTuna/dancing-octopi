# Sankey & Alluvial Diagram Block Specification

> **Block Name:** `kunaal/flow-diagram`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** D3.js, d3-sankey

---

## 1. Overview

Flow diagrams visualize how quantities move between categories:
- **Sankey Diagram:** Shows flow quantities with varying link widths. Links can merge and split. Used for energy flows, budget allocations, process flows.
- **Alluvial Diagram:** Special case where nodes are arranged in parallel columns (time steps, categories). Shows how membership changes across stages.

### 1.1 Use Cases
- Energy production → consumption flow
- Website traffic source → page → conversion
- Budget allocation across departments
- Customer journey stages
- Voting changes between elections (alluvial)

---

## 2. Visual Design

### 2.1 Sankey Layout
```
┌─────────────────────────────────────────────────────────────────┐
│  [Title]                                                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ┌──────┐                                     ┌──────────┐    │
│   │ Coal ├─────────────╮                 ╭─────┤ Industry │    │
│   └──────┘             │      ┌──────┐   │     └──────────┘    │
│                        ├──────┤Elec. ├───┤                      │
│   ┌──────┐             │      │Power │   │     ┌──────────┐    │
│   │ Gas  ├─────────────╯      └──────┘   ╰─────┤Residential│   │
│   └──────┘                                     └──────────┘    │
│                                                                 │
│   ┌──────┐                                     ┌──────────┐    │
│   │ Oil  ├─────────────────────────────────────┤Transport │    │
│   └──────┘                                     └──────────┘    │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  [Source]                                                       │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Alluvial Layout
```
┌─────────────────────────────────────────────────────────────────┐
│  [Title]                                                        │
├─────────────────────────────────────────────────────────────────┤
│    2020         2022         2024                              │
│   ┌─────┐      ┌─────┐      ┌─────┐                            │
│   │Party│      │Party│      │Party│                            │
│   │  A  │══════│  A  │══════│  A  │                            │
│   │     │      │     │      │     │                            │
│   └─────┘      └─────┘      └─────┘                            │
│       ╲          ╱                                              │
│        ╲        ╱ (flow between)                                │
│         ╲      ╱                                                │
│   ┌─────┐      ┌─────┐      ┌─────┐                            │
│   │Party│      │Party│      │Party│                            │
│   │  B  │══════│  B  │══════│  B  │                            │
│   │     │      │     │      │     │                            │
│   └─────┘      └─────┘      └─────┘                            │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 2.3 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Title | Newsreader | 24px | 500 | `--ink` |
| Subtitle | Inter | 14px | 400 | `--muted` |
| Node labels | Inter | 12px | 500 | `--ink` |
| Node values | ui-monospace | 11px | 400 | `--muted` |
| Link tooltip | Inter | 12px | 400 | `--ink` |
| Column headers (alluvial) | Inter | 13px | 600 | `--ink` |

### 2.4 Colors

#### Node Colors
Assigned from theme palette in order:
```
--flow-1: #7D6B5D   (Brown)
--flow-2: #B8A99A   (Warm Light)  
--flow-3: #C9553D   (Terracotta)
--flow-4: #8B7355   (Sienna)
--flow-5: #D4C4B5   (Champagne)
--flow-6: #6B5B4F   (Dark Brown)
--flow-7: #A08B7A   (Taupe)
--flow-8: #4A90A4   (Blue - sparingly)
```

#### Link Colors
- **By Source:** Link inherits source node color at 40% opacity
- **By Target:** Link inherits target node color at 40% opacity
- **Gradient:** Gradient from source to target color
- **Single Color:** All links same color

### 2.5 Dimensions
- **Min width:** 500px
- **Max width:** 1000px
- **Min height:** 300px
- **Node width:** 20px
- **Node padding:** 8px (vertical gap between nodes)
- **Link curvature:** 0.5 (Bézier control point)

### 2.6 States
- **Default:** Nodes and links at base opacity
- **Hover (node):** Node glows, connected links highlight, unrelated links fade to 10%
- **Hover (link):** Link highlights, source and target nodes glow
- **Focus:** Blue outline on interactive elements

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
  "diagramType": {
    "type": "string",
    "enum": ["sankey", "alluvial"],
    "default": "sankey"
  },
  "nodes": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "id": { "type": "string" },
        "label": { "type": "string" },
        "column": { "type": "number" },
        "color": { "type": "string" }
      }
    }
  },
  "links": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "source": { "type": "string" },
        "target": { "type": "string" },
        "value": { "type": "number" }
      }
    }
  },
  "nodeWidth": {
    "type": "number",
    "default": 20
  },
  "nodePadding": {
    "type": "number",
    "default": 8
  },
  "linkColorMode": {
    "type": "string",
    "enum": ["source", "target", "gradient", "single"],
    "default": "source"
  },
  "singleLinkColor": {
    "type": "string",
    "default": "#B8A99A"
  },
  "showValues": {
    "type": "boolean",
    "default": true
  },
  "valueFormat": {
    "type": "string",
    "enum": ["number", "percent", "currency", "compact"],
    "default": "number"
  },
  "currencySymbol": {
    "type": "string",
    "default": "$"
  },
  "valueUnit": {
    "type": "string",
    "default": ""
  },
  "columnLabels": {
    "type": "array",
    "default": [],
    "items": { "type": "string" }
  },
  "nodeAlignment": {
    "type": "string",
    "enum": ["justify", "left", "right", "center"],
    "default": "justify"
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
- **Diagram Type:** Toggle (Sankey / Alluvial)
- **Link Color:** Dropdown (Source / Target / Gradient / Single)
- **Show Values:** Toggle

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input

#### Diagram Type
- **Type** — Radio with icons:
  - **Sankey** — Free-form node placement
  - **Alluvial** — Parallel columns

#### Node Configuration
```
┌─────────────────────────────────────────────────────────────────┐
│  📦 Nodes                                                       │
├──────────────────┬────────────────┬──────────┬──────────────────┤
│  ID (internal)   │  Label         │  Column  │  Color           │
├──────────────────┼────────────────┼──────────┼──────────────────┤
│  coal            │  Coal          │  0       │  🟤 [picker]     │
├──────────────────┼────────────────┼──────────┼──────────────────┤
│  electricity     │  Electricity   │  1       │  🟡 [picker]     │
├──────────────────┼────────────────┼──────────┼──────────────────┤
│  industry        │  Industry      │  2       │  🟠 [picker]     │
└──────────────────┴────────────────┴──────────┴──────────────────┘
│  [+ Add Node]                                                   │
└─────────────────────────────────────────────────────────────────┘
```
- Column: Only shown for alluvial mode
- Color: Optional override; auto-assigned if empty

#### Link Configuration
```
┌─────────────────────────────────────────────────────────────────┐
│  🔗 Links (Flows)                                               │
├───────────────────────┬───────────────────────┬─────────────────┤
│  From Node            │  To Node              │  Value          │
├───────────────────────┼───────────────────────┼─────────────────┤
│  [Coal ▼]             │  [Electricity ▼]      │  100            │
├───────────────────────┼───────────────────────┼─────────────────┤
│  [Electricity ▼]      │  [Industry ▼]         │  60             │
├───────────────────────┼───────────────────────┼─────────────────┤
│  [Electricity ▼]      │  [Residential ▼]      │  40             │
└───────────────────────┴───────────────────────┴─────────────────┘
│  [+ Add Link]                                                   │
└─────────────────────────────────────────────────────────────────┘
```
- Dropdowns populate from nodes list
- Validation: source ≠ target, no cycles

#### Display Options
- **Show Values on Nodes** — Toggle
- **Value Format** — Dropdown
- **Currency Symbol** — Text (when currency)
- **Unit Suffix** — Text (e.g., "TWh", "M users")

#### Link Styling
- **Link Color Mode** — Radio
- **Single Color** — Color picker (when single mode)
- **Link Opacity** — Slider (0.2 - 0.8)

#### Layout (Advanced)
- **Node Width** — Slider (10 - 40px)
- **Node Padding** — Slider (4 - 20px)
- **Node Alignment** — Dropdown (Justify, Left, Right, Center)

#### Alluvial Only
- **Column Labels** — Comma-separated text (e.g., "2020, 2022, 2024")

---

## 5. Responsive Behavior

| Breakpoint | Changes |
|------------|---------|
| < 480px | Vertical layout, nodes stack |
| 480-768px | Compressed horizontal, smaller text |
| > 768px | Full layout |

### 5.1 Mobile Vertical Layout
For Sankey on mobile, consider a table fallback:
```
┌─────────────────────────────────────┐
│  Coal → Electricity    │  100 TWh  │
│  Gas → Electricity     │   80 TWh  │
│  Electricity → Industry│   60 TWh  │
│  Electricity → Homes   │   40 TWh  │
└─────────────────────────────────────┘
```

---

## 6. Accessibility

### 6.1 ARIA Structure
```html
<figure class="wp-block-kunaal-flow-diagram" role="img" 
        aria-labelledby="flow-title-{id}" aria-describedby="flow-desc-{id}">
  <figcaption id="flow-title-{id}">[Title]</figcaption>
  <div id="flow-desc-{id}" class="sr-only">
    Flow diagram showing [N] flows between [M] nodes.
    Largest flow: [Source] to [Target] at [Value].
  </div>
  <svg aria-hidden="true"><!-- Visual --></svg>
</figure>
```

### 6.2 Data Table Fallback
Always include expandable data table:
```html
<details class="flow-data-table">
  <summary>View flow data</summary>
  <table>
    <thead><tr><th>From</th><th>To</th><th>Value</th></tr></thead>
    <tbody>
      <tr><td>Coal</td><td>Electricity</td><td>100 TWh</td></tr>
      <!-- ... -->
    </tbody>
  </table>
</details>
```

### 6.3 Keyboard Navigation
- Tab through nodes
- Enter/Space to highlight connected flows
- Arrow keys to navigate between connected nodes

---

## 7. Animations

### 7.1 Entry Animation
1. Nodes appear (staggered by column)
2. Links draw from source to target
3. Values fade in

### 7.2 Hover Animation
- Connected elements highlight
- Unconnected fade to 10% opacity
- Transition duration: 300ms

---

## 8. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No nodes | Placeholder "Add nodes to start" |
| Nodes with no links | Show orphan nodes with warning |
| Circular reference | Validation error, prevent save |
| Single link | Render simple source → target |
| Very small flows | Min link width of 2px |
| Many nodes (>20) | Enable scrolling or limit |
| Long labels | Truncate, full in tooltip |
| Zero value links | Hide or show dashed line (option) |

---

## 9. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-flow-diagram flow-diagram--sankey">
  <header class="flow-header">
    <h3 class="flow-title">Energy Flow: Production to Consumption</h3>
    <p class="flow-subtitle">United States, 2024 (in TWh)</p>
  </header>
  
  <div class="flow-chart">
    <svg viewBox="0 0 800 500" class="flow-svg">
      <defs>
        <linearGradient id="grad-coal-elec">
          <stop offset="0%" stop-color="#7D6B5D" />
          <stop offset="100%" stop-color="#B8A99A" />
        </linearGradient>
      </defs>
      
      <!-- Links (rendered first, behind nodes) -->
      <g class="flow-links">
        <path class="flow-link" 
              d="M20,50 C200,50 200,100 400,100" 
              fill="url(#grad-coal-elec)" 
              opacity="0.4"
              data-source="coal" data-target="electricity" data-value="100">
          <title>Coal → Electricity: 100 TWh</title>
        </path>
        <!-- More links... -->
      </g>
      
      <!-- Nodes -->
      <g class="flow-nodes">
        <g class="flow-node" data-id="coal" tabindex="0">
          <rect x="0" y="30" width="20" height="60" fill="#7D6B5D" rx="3" />
          <text class="flow-node-label" x="-5" y="60" text-anchor="end">Coal</text>
          <text class="flow-node-value" x="-5" y="75" text-anchor="end">180 TWh</text>
        </g>
        <!-- More nodes... -->
      </g>
    </svg>
  </div>
  
  <footer class="flow-footer">
    <p class="flow-source">Source: EIA</p>
  </footer>
  
  <details class="flow-data-table">
    <summary>View flow data</summary>
    <table><!-- ... --></table>
  </details>
</figure>
```

---

## 10. User Stories

### US-FL-01: Create Energy Sankey
**As a** content author  
**I want to** create a Sankey diagram showing energy flows  
**So that** readers understand how energy moves from production to consumption  

**Acceptance Criteria:**
- [ ] Can add source and target nodes
- [ ] Can define flow values between nodes
- [ ] Diagram renders with proportional link widths
- [ ] Hover highlights connected paths

### US-FL-02: Alluvial Voting Changes
**As a** content author  
**I want to** show how voters moved between parties across elections  
**So that** readers understand political shifts  

**Acceptance Criteria:**
- [ ] Can create nodes in multiple columns
- [ ] Columns have headers (years)
- [ ] Flows connect same-row or different-row nodes
- [ ] Clear visualization of "defections"

### US-FL-03: Custom Node Colors
**As a** content author  
**I want to** assign specific colors to nodes  
**So that** they match established brand/political colors  

**Acceptance Criteria:**
- [ ] Can override auto color for each node
- [ ] Custom colors persist
- [ ] Links inherit or gradient between colors
- [ ] Contrast validation for text

### US-FL-04: Value Formatting
**As a** content author  
**I want to** format values with units  
**So that** readers understand the scale  

**Acceptance Criteria:**
- [ ] Can add unit suffix (TWh, M, etc.)
- [ ] Currency format works
- [ ] Compact notation works (1.2M vs 1,200,000)
- [ ] Values display on nodes

