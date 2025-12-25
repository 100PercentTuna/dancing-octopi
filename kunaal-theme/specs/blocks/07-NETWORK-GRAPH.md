# Network Graph Block Specification

> **Block Name:** `kunaal/network-graph`  
> **Category:** `kunaal-advanced-viz`  
> **Dependencies:** D3.js, d3-force

---

## 1. Overview

A network graph (force-directed layout) visualizes relationships between entities as nodes connected by edges. Nodes are positioned by simulated physical forces, clustering related items together.

### 1.1 Use Cases
- Social networks and connections
- Citation networks
- Concept maps
- Organizational relationships
- Topic/keyword relationships
- Character relationships in narratives

---

## 2. Visual Design

### 2.1 Layout
```
┌─────────────────────────────────────────────────────────────────┐
│  [Title]                                                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│              ○ Node C                                           │
│             ╱   ╲                                               │
│            ╱     ╲                                              │
│       ○───●───────○                                             │
│      Node A  Node B  Node D                                     │
│         │      ╲                                                │
│         │       ╲                                               │
│         ○        ○                                              │
│       Node E   Node F                                           │
│                                                                 │
│  Legend: ● Primary  ○ Secondary  ─── Strong link  --- Weak     │
├─────────────────────────────────────────────────────────────────┤
│  [Source]                                                       │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Node Types

| Type | Size | Color | Border |
|------|------|-------|--------|
| Primary | 24px | `--warm` | 3px `--ink` |
| Secondary | 16px | `--warmLight` | 2px `--muted` |
| Tertiary | 10px | `--champagne` | 1px `--muted` |

### 2.3 Edge Types

| Type | Stroke | Width | Style |
|------|--------|-------|-------|
| Strong | `--warm` | 3px | solid |
| Medium | `--muted` | 2px | solid |
| Weak | `--muted` | 1px | dashed |

### 2.4 Typography
| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Title | Newsreader | 24px | 500 | `--ink` |
| Node labels | Inter | 11px | 500 | `--ink` |
| Tooltip title | Inter | 13px | 600 | `--ink` |
| Tooltip body | Inter | 12px | 400 | `--muted` |

### 2.5 States
- **Default:** Nodes at base opacity, edges visible
- **Hover (node):** Node glows, connected edges highlight, connected nodes highlight, others fade to 20%
- **Selected:** Node has blue outline, info panel shown
- **Dragging:** Node follows cursor, simulation recalculates
- **Zoomed:** Labels appear/disappear based on zoom level

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
  "nodes": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "id": { "type": "string" },
        "label": { "type": "string" },
        "group": { "type": "string" },
        "size": { "type": "string", "enum": ["small", "medium", "large"] },
        "color": { "type": "string" },
        "description": { "type": "string" },
        "url": { "type": "string" }
      }
    }
  },
  "edges": {
    "type": "array",
    "default": [],
    "items": {
      "type": "object",
      "properties": {
        "source": { "type": "string" },
        "target": { "type": "string" },
        "weight": { "type": "number" },
        "label": { "type": "string" }
      }
    }
  },
  "layout": {
    "type": "string",
    "enum": ["force", "radial", "hierarchical"],
    "default": "force"
  },
  "showLabels": {
    "type": "boolean",
    "default": true
  },
  "labelThreshold": {
    "type": "number",
    "default": 0.5,
    "description": "Zoom level below which labels hide"
  },
  "enableZoom": {
    "type": "boolean",
    "default": true
  },
  "enableDrag": {
    "type": "boolean",
    "default": true
  },
  "enablePhysics": {
    "type": "boolean",
    "default": true
  },
  "chargeStrength": {
    "type": "number",
    "default": -300,
    "description": "Repulsion force between nodes"
  },
  "linkDistance": {
    "type": "number",
    "default": 100
  },
  "colorByGroup": {
    "type": "boolean",
    "default": true
  },
  "groupColors": {
    "type": "object",
    "default": {}
  },
  "showLegend": {
    "type": "boolean",
    "default": true
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
- **Layout:** Dropdown (Force / Radial / Hierarchical)
- **Show Labels:** Toggle
- **Enable Physics:** Toggle

### 4.2 Inspector Panel

#### General Settings
- **Title** — Text input
- **Subtitle** — Text input
- **Source Note** — Text input
- **Chart Height** — Number input (300-800px)

#### Layout
- **Layout Algorithm** — Radio with previews:
  - **Force-Directed** — Natural clustering
  - **Radial** — Nodes arranged in circles from center
  - **Hierarchical** — Tree-like layout
  
#### Force Parameters (when Force selected)
- **Charge Strength** — Slider (-500 to -100)
  - Tooltip: "How much nodes repel each other"
- **Link Distance** — Slider (50 to 200)
  - Tooltip: "Ideal distance between connected nodes"
- **Center Gravity** — Slider (0 to 1)

#### Interaction
- **Enable Zoom** — Toggle
- **Enable Drag** — Toggle
- **Enable Physics** — Toggle (when off, nodes stay fixed)

#### Labels
- **Show Labels** — Toggle
- **Label Position** — Dropdown (Right, Below, Inside)
- **Hide Labels at Zoom** — Slider (zoom threshold)

#### Nodes
```
┌─────────────────────────────────────────────────────────────────┐
│  📍 Nodes                                                       │
├─────────────────────────────────────────────────────────────────┤
│  🔵 Author A                                        [Edit] [🗑️] │
│     Group: Fiction │ Size: Large │ Links: 5                    │
├─────────────────────────────────────────────────────────────────┤
│  🟤 Author B                                        [Edit] [🗑️] │
│     Group: Non-fiction │ Size: Medium │ Links: 3               │
├─────────────────────────────────────────────────────────────────┤
│  [+ Add Node]                                                   │
└─────────────────────────────────────────────────────────────────┘
```

Node Edit Modal:
```
┌────────────────────────────────────────┐
│  Edit Node                         [×] │
├────────────────────────────────────────┤
│  Label:        [Author A          ]    │
│  Group:        [Fiction       ▼]       │
│  Size:         ○ Small ● Medium ○ Large│
│  Color:        [Override] 🟤           │
│  Description:  [Prolific writer...]    │
│  Link URL:     [https://...]           │
├────────────────────────────────────────┤
│           [Cancel]    [Save]           │
└────────────────────────────────────────┘
```

#### Edges
```
┌─────────────────────────────────────────────────────────────────┐
│  🔗 Connections                                                 │
├──────────────────┬──────────────────┬───────────┬───────────────┤
│  From            │  To              │  Weight   │  Label        │
├──────────────────┼──────────────────┼───────────┼───────────────┤
│  [Author A ▼]    │  [Author B ▼]    │  [Strong▼]│  [collaborated]│
├──────────────────┼──────────────────┼───────────┼───────────────┤
│  [Author A ▼]    │  [Author C ▼]    │  [Weak  ▼]│  [cited]      │
└──────────────────┴──────────────────┴───────────┴───────────────┘
│  [+ Add Connection]                                             │
└─────────────────────────────────────────────────────────────────┘
```

#### Groups & Colors
- **Color by Group** — Toggle
- **Group Colors** — List of group names with color pickers
```
┌───────────────────────────────────┐
│  Group Colors                     │
│  Fiction:       🟤 [color picker] │
│  Non-fiction:   🟡 [color picker] │
│  Poetry:        🔵 [color picker] │
│  [+ Add Group]                    │
└───────────────────────────────────┘
```

#### Legend
- **Show Legend** — Toggle
- **Legend Position** — Dropdown (Top-right, Bottom, Below chart)

---

## 5. Responsive Behavior

| Breakpoint | Changes |
|------------|---------|
| < 480px | Fixed height 300px, zoom disabled, tap to select |
| 480-768px | Height 400px, limited zoom |
| > 768px | Full interactivity |

### 5.1 Mobile Touch Behavior
- Tap node to select (shows tooltip)
- Tap elsewhere to deselect
- Pinch to zoom
- Two-finger pan

---

## 6. Accessibility

### 6.1 ARIA Structure
```html
<figure class="wp-block-kunaal-network-graph" role="img"
        aria-labelledby="net-title-{id}" aria-describedby="net-desc-{id}">
  <figcaption id="net-title-{id}">[Title]</figcaption>
  <div id="net-desc-{id}" class="sr-only">
    Network graph with [N] nodes in [M] groups, connected by [E] edges.
    Most connected node: [Label] with [X] connections.
  </div>
  <svg aria-hidden="true"><!-- Visual --></svg>
</figure>
```

### 6.2 Keyboard Navigation
- Tab to focus graph container
- Arrow keys to navigate between nodes
- Enter to select/expand node
- Escape to deselect
- +/- to zoom

### 6.3 Node List Fallback
```html
<details class="network-node-list">
  <summary>View as list</summary>
  <ul>
    <li>
      <strong>Author A</strong> (Fiction)
      <ul>
        <li>Connected to: Author B (collaborated)</li>
        <li>Connected to: Author C (cited)</li>
      </ul>
    </li>
  </ul>
</details>
```

---

## 7. Animations

### 7.1 Entry Animation
- Nodes fade in from center
- Physics simulation runs, nodes spread out
- Duration: ~2 seconds until settled

### 7.2 Interaction Animations
- Hover highlight: 200ms fade
- Drag: Real-time physics update
- Zoom: Smooth 300ms transition

### 7.3 Reduced Motion
- Skip entry animation
- Instant state changes
- No physics movement (static layout)

---

## 8. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No nodes | Placeholder "Add nodes to create network" |
| Single node | Show centered, no edges |
| No edges | Show nodes without connections |
| Disconnected subgraphs | Each cluster positions separately |
| Self-loop edge | Show curved arrow back to same node |
| Many nodes (>100) | Enable clustering, reduce detail |
| Very dense connections | Edge bundling option |
| Long labels | Truncate, full in tooltip |
| Overlapping nodes | Physics prevents, or manual adjust |

---

## 9. Example Output (HTML Structure)

```html
<figure class="wp-block-kunaal-network-graph">
  <header class="network-header">
    <h3 class="network-title">Author Collaboration Network</h3>
    <p class="network-subtitle">Based on co-authored publications</p>
  </header>
  
  <div class="network-container" style="height: 500px;">
    <svg class="network-svg" viewBox="0 0 800 500">
      <!-- Edges (behind nodes) -->
      <g class="network-edges">
        <line class="network-edge" 
              x1="100" y1="150" x2="300" y2="200"
              data-source="author-a" data-target="author-b"
              stroke-width="3">
          <title>Author A → Author B: collaborated</title>
        </line>
        <!-- More edges... -->
      </g>
      
      <!-- Nodes -->
      <g class="network-nodes">
        <g class="network-node" data-id="author-a" data-group="fiction" 
           tabindex="0" transform="translate(100, 150)">
          <circle r="12" class="node-circle" />
          <text class="node-label" dy="25">Author A</text>
        </g>
        <!-- More nodes... -->
      </g>
    </svg>
    
    <!-- Zoom controls -->
    <div class="network-controls">
      <button class="network-zoom-in" aria-label="Zoom in">+</button>
      <button class="network-zoom-out" aria-label="Zoom out">−</button>
      <button class="network-reset" aria-label="Reset view">⟲</button>
    </div>
    
    <!-- Tooltip (positioned dynamically) -->
    <div class="network-tooltip" role="tooltip" hidden>
      <h4 class="tooltip-title"></h4>
      <p class="tooltip-group"></p>
      <p class="tooltip-description"></p>
      <ul class="tooltip-connections"></ul>
    </div>
  </div>
  
  <footer class="network-footer">
    <div class="network-legend">
      <span class="legend-item"><span class="legend-dot" style="background: #7D6B5D"></span> Fiction</span>
      <span class="legend-item"><span class="legend-dot" style="background: #B8A99A"></span> Non-fiction</span>
    </div>
    <p class="network-source">Source: Publication records</p>
  </footer>
  
  <details class="network-list-fallback">
    <summary>View as list</summary>
    <ul><!-- Accessible list --></ul>
  </details>
</figure>
```

---

## 10. User Stories

### US-NG-01: Create Social Network
**As a** content author  
**I want to** visualize connections between people  
**So that** readers can understand relationships  

**Acceptance Criteria:**
- [ ] Can add nodes with labels
- [ ] Can create edges between nodes
- [ ] Force layout clusters related nodes
- [ ] Hover shows connections

### US-NG-02: Group by Category
**As a** content author  
**I want to** color nodes by their category  
**So that** readers can identify groups visually  

**Acceptance Criteria:**
- [ ] Can assign groups to nodes
- [ ] Groups get distinct colors
- [ ] Legend shows group colors
- [ ] Can customize group colors

### US-NG-03: Interactive Exploration
**As a** reader  
**I want to** zoom and pan the network  
**So that** I can explore large graphs  

**Acceptance Criteria:**
- [ ] Scroll/pinch to zoom
- [ ] Drag to pan
- [ ] Click node for details
- [ ] Reset button available

### US-NG-04: Accessible Alternative
**As a** screen reader user  
**I want to** understand the network relationships  
**So that** I can access the same information  

**Acceptance Criteria:**
- [ ] ARIA description summarizes network
- [ ] Node list available as fallback
- [ ] Keyboard navigation works
- [ ] Connections listed per node

