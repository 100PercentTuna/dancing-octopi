# 05 - WORLD MAP SECTION
## Complete Specification

---

## OVERVIEW

The world map is an **interactive scratch-map style visualization** showing countries the person has visited, lived in, and their current location. Countries are **shaded** (not pins), tooltips show stories, and the experience is touch-friendly.

**Key Requirements:**
- Use a proper SVG world map library (not custom-built)
- Countries shaded by status (visited, lived, current)
- Hover/touch reveals story tooltip
- Tooltip can be closed (X button, tap-outside)
- Colors follow theme palette (browns, NOT blue for countries)

---

## RECOMMENDED LIBRARIES

### Option 1: jVectorMap (Recommended)
```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/jvectormap/2.0.5/jquery-jvectormap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jvectormap/2.0.5/jquery-jvectormap-world-mill.min.js"></script>
```

### Option 2: Leaflet with GeoJSON
```html
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Plus custom GeoJSON for country borders -->
```

### Option 3: Simple SVG Map
Use a pre-made SVG world map with country paths identified by ISO codes.

---

## VISUAL LAYOUT

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   02                                           ← Mono, 11px, muted2         │
│   ──                                           ← Brown underline            │
│   PLACES I'VE CALLED HOME                      ← Customizable label         │
│                                                                             │
│   ┌─────────────────────────────────────────────────────────────────────┐   │
│   │                                                                     │   │
│   │     ██████                                    ████████              │   │
│   │   ████████████                               ██████████             │   │
│   │  ██████████████  ●←current               ████████████████           │   │
│   │ ████░░░░████████                          ░░░░░████████             │   │
│   │  ██░░░░░░░░████                           ░░░░░░░░████              │   │
│   │    ░░░░░░░░░░                               ░░░░░░░░                │   │
│   │      ░░░░░░░                                  ░░░░                  │   │
│   │        ░░░░                                                         │   │
│   │          ░░░░░░░░                                    ░░░            │   │
│   │                   ░░░░░░░░░░░░                    ░░░░░░░░          │   │
│   │                                                                     │   │
│   └─────────────────────────────────────────────────────────────────────┘   │
│                                                                             │
│   ○ Current   ░ Lived   █ Visited   · Not visited                           │
│                                                                             │
│   Tooltip appears on hover/tap:                                             │
│   ┌─────────────────────────────────┐                                       │
│   │ INDIA                      ✕   │                                       │
│   │ 2020 - Present                  │                                       │
│   │                                 │                                       │
│   │ "Returned home during the      │                                       │
│   │ pandemic, rediscovering the    │                                       │
│   │ country I grew up in."         │                                       │
│   └─────────────────────────────────┘                                       │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## COLOR SCHEME

```css
:root {
  /* Map-specific colors from master spec */
  --map-default: #E8E8E8;        /* Unvisited - light gray */
  --map-visited: #B8A99A;        /* Visited - warmLight (light tan) */
  --map-lived: #7D6B5D;          /* Lived - warm (dark brown) */
  --map-current: #C9553D;        /* Current - terracotta (NOT blue) */
  --map-hover: #D9C9BA;          /* Hover state - slightly lighter */
  --map-border: #FFFFFF;         /* Country borders - white */
}
```

**Color Rules:**
- NO BLUE for map highlights (blue is for links only)
- Browns create warmth and match overall theme
- Terracotta stands out for current location
- Gray for unvisited maintains clean look

---

## CSS IMPLEMENTATION

```css
/* === MAP CONTAINER === */
.about-map {
  max-width: var(--wide);
  margin: var(--space-15) auto;
  padding: 0 var(--space-4);
}

.about-map-label {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted2);
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  margin-bottom: var(--space-8);
  text-align: center;
}

.about-map-label::before {
  content: '02';
  display: block;
}

.about-map-label::after {
  content: '';
  width: 24px;
  height: 2px;
  background: var(--warm);
  margin: var(--space-1) auto;
}

/* === MAP WRAPPER === */
.map-container {
  position: relative;
  width: 100%;
  max-width: 900px;
  margin: 0 auto;
  aspect-ratio: 2/1;
  border-radius: 8px;
  overflow: hidden;
  background: var(--bgAlt);
}

/* === SVG MAP STYLES === */
.map-svg {
  width: 100%;
  height: 100%;
}

.map-country {
  fill: var(--map-default);
  stroke: var(--map-border);
  stroke-width: 0.5;
  transition: 
    fill 300ms ease,
    opacity 300ms ease;
  cursor: default;
}

/* Status-based coloring */
.map-country--visited {
  fill: var(--map-visited);
  cursor: pointer;
}

.map-country--lived {
  fill: var(--map-lived);
  cursor: pointer;
}

.map-country--current {
  fill: var(--map-current);
  cursor: pointer;
}

/* Hover states (only for interactive countries) */
.map-country--visited:hover,
.map-country--lived:hover,
.map-country--current:hover {
  fill: var(--map-hover);
  opacity: 0.9;
}

/* === CURRENT LOCATION MARKER === */
.map-current-marker {
  position: absolute;
  pointer-events: none;
}

.map-current-marker__dot {
  width: 12px;
  height: 12px;
  background: var(--map-current);
  border-radius: 50%;
  border: 2px solid white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.map-current-marker__pulse {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 24px;
  height: 24px;
  border: 2px solid var(--map-current);
  border-radius: 50%;
  animation: mapPulse 2s ease-out infinite;
}

@keyframes mapPulse {
  0% {
    transform: translate(-50%, -50%) scale(0.5);
    opacity: 1;
  }
  100% {
    transform: translate(-50%, -50%) scale(2);
    opacity: 0;
  }
}

/* === TOOLTIP === */
.map-tooltip {
  position: absolute;
  min-width: 200px;
  max-width: 280px;
  background: white;
  border: 1px solid var(--hair);
  border-radius: 8px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
  padding: var(--space-4);
  z-index: 100;
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: 
    opacity 200ms ease,
    visibility 200ms ease,
    transform 200ms ease;
}

.map-tooltip.is-visible {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

/* Tooltip positioning */
.map-tooltip--top {
  bottom: 100%;
  margin-bottom: 10px;
}

.map-tooltip--bottom {
  top: 100%;
  margin-top: 10px;
}

.map-tooltip--left {
  right: 0;
}

.map-tooltip--right {
  left: 0;
}

/* Tooltip header */
.map-tooltip__header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: var(--space-2);
}

.map-tooltip__country {
  font-family: var(--mono);
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--ink);
}

.map-tooltip__years {
  font-family: var(--mono);
  font-size: 11px;
  color: var(--muted2);
  margin-top: 2px;
}

.map-tooltip__close {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: transparent;
  border: none;
  cursor: pointer;
  color: var(--muted2);
  transition: color 200ms ease;
  padding: 0;
  margin: -4px -4px 0 0;
}

.map-tooltip__close:hover {
  color: var(--ink);
}

.map-tooltip__close svg {
  width: 16px;
  height: 16px;
}

/* Tooltip content */
.map-tooltip__story {
  font-family: var(--serif);
  font-size: 14px;
  line-height: 1.6;
  color: var(--muted);
  font-style: italic;
}

/* === LEGEND === */
.map-legend {
  display: flex;
  justify-content: center;
  gap: var(--space-5);
  margin-top: var(--space-6);
  flex-wrap: wrap;
}

.map-legend__item {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.map-legend__color {
  width: 14px;
  height: 14px;
  border-radius: 3px;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.map-legend__color--current {
  background: var(--map-current);
  border-radius: 50%;
}

.map-legend__color--lived {
  background: var(--map-lived);
}

.map-legend__color--visited {
  background: var(--map-visited);
}

.map-legend__color--none {
  background: var(--map-default);
}

.map-legend__label {
  font-family: var(--mono);
  font-size: 11px;
  color: var(--muted2);
}
```

---

## JAVASCRIPT IMPLEMENTATION

```javascript
class WorldMap {
  constructor(container) {
    this.container = container;
    this.svg = container.querySelector('.map-svg');
    this.tooltip = container.querySelector('.map-tooltip');
    this.countries = {};
    this.activeTooltip = null;
    
    this.init();
  }
  
  init() {
    this.loadCountryData();
    this.bindEvents();
    this.colorizeCountries();
    this.addCurrentMarker();
  }
  
  loadCountryData() {
    // Data from PHP/Customizer
    this.countries = window.aboutMapData || {
      visited: [],   // ['US', 'GB', 'FR', ...]
      lived: [],     // ['IN', 'AE', ...]
      current: '',   // 'IN'
      stories: {}    // { 'IN': { years: '2020-Present', story: '...' } }
    };
  }
  
  colorizeCountries() {
    const allCountryPaths = this.svg.querySelectorAll('path[data-country]');
    
    allCountryPaths.forEach(path => {
      const code = path.dataset.country;
      
      // Remove existing classes
      path.classList.remove('map-country--visited', 'map-country--lived', 'map-country--current');
      
      // Add appropriate class
      if (code === this.countries.current) {
        path.classList.add('map-country--current');
      } else if (this.countries.lived.includes(code)) {
        path.classList.add('map-country--lived');
      } else if (this.countries.visited.includes(code)) {
        path.classList.add('map-country--visited');
      }
    });
  }
  
  addCurrentMarker() {
    if (!this.countries.current) return;
    
    const currentPath = this.svg.querySelector(`path[data-country="${this.countries.current}"]`);
    if (!currentPath) return;
    
    // Get center of country path
    const bbox = currentPath.getBBox();
    const centerX = bbox.x + bbox.width / 2;
    const centerY = bbox.y + bbox.height / 2;
    
    // Create marker
    const marker = document.createElement('div');
    marker.className = 'map-current-marker';
    marker.innerHTML = `
      <div class="map-current-marker__pulse"></div>
      <div class="map-current-marker__dot"></div>
    `;
    
    // Position marker (convert SVG coords to container coords)
    const svgRect = this.svg.getBoundingClientRect();
    const containerRect = this.container.getBoundingClientRect();
    
    // This needs adjustment based on SVG viewBox
    marker.style.left = `${(centerX / 1000) * 100}%`;
    marker.style.top = `${(centerY / 500) * 100}%`;
    
    this.container.querySelector('.map-container').appendChild(marker);
  }
  
  bindEvents() {
    // Click/tap on countries
    this.svg.addEventListener('click', (e) => {
      const country = e.target.closest('[data-country]');
      if (country && this.hasStory(country.dataset.country)) {
        this.showTooltip(country);
      }
    });
    
    // Desktop hover (show tooltip on hover for countries with stories)
    if (!('ontouchstart' in window)) {
      this.svg.addEventListener('mouseover', (e) => {
        const country = e.target.closest('[data-country]');
        if (country && this.hasStory(country.dataset.country)) {
          this.showTooltip(country);
        }
      });
      
      this.svg.addEventListener('mouseout', (e) => {
        const country = e.target.closest('[data-country]');
        if (country && !this.tooltip.contains(e.relatedTarget)) {
          // Delay hide to allow moving to tooltip
          setTimeout(() => {
            if (!this.tooltip.matches(':hover')) {
              this.hideTooltip();
            }
          }, 100);
        }
      });
    }
    
    // Close button
    const closeBtn = this.tooltip.querySelector('.map-tooltip__close');
    closeBtn?.addEventListener('click', () => this.hideTooltip());
    
    // Click outside to close (touch)
    document.addEventListener('click', (e) => {
      if (this.activeTooltip && 
          !this.tooltip.contains(e.target) && 
          !e.target.closest('[data-country]')) {
        this.hideTooltip();
      }
    });
    
    // Escape to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && this.activeTooltip) {
        this.hideTooltip();
      }
    });
  }
  
  hasStory(countryCode) {
    return this.countries.stories && 
           this.countries.stories[countryCode] && 
           this.countries.stories[countryCode].story;
  }
  
  showTooltip(countryPath) {
    const code = countryPath.dataset.country;
    const story = this.countries.stories[code];
    
    if (!story) return;
    
    // Update tooltip content
    this.tooltip.querySelector('.map-tooltip__country').textContent = 
      this.getCountryName(code);
    this.tooltip.querySelector('.map-tooltip__years').textContent = 
      story.years || '';
    this.tooltip.querySelector('.map-tooltip__story').textContent = 
      story.story || '';
    
    // Position tooltip
    this.positionTooltip(countryPath);
    
    // Show tooltip
    this.tooltip.classList.add('is-visible');
    this.activeTooltip = code;
  }
  
  positionTooltip(countryPath) {
    const bbox = countryPath.getBoundingClientRect();
    const containerRect = this.container.getBoundingClientRect();
    const tooltipRect = this.tooltip.getBoundingClientRect();
    
    // Calculate position relative to container
    let left = bbox.left + bbox.width / 2 - containerRect.left - tooltipRect.width / 2;
    let top = bbox.top - containerRect.top - tooltipRect.height - 10;
    
    // Adjust if off-screen
    if (left < 10) left = 10;
    if (left + tooltipRect.width > containerRect.width - 10) {
      left = containerRect.width - tooltipRect.width - 10;
    }
    
    if (top < 10) {
      top = bbox.bottom - containerRect.top + 10;
      this.tooltip.classList.remove('map-tooltip--top');
      this.tooltip.classList.add('map-tooltip--bottom');
    } else {
      this.tooltip.classList.add('map-tooltip--top');
      this.tooltip.classList.remove('map-tooltip--bottom');
    }
    
    this.tooltip.style.left = `${left}px`;
    this.tooltip.style.top = `${top}px`;
  }
  
  hideTooltip() {
    this.tooltip.classList.remove('is-visible');
    this.activeTooltip = null;
  }
  
  getCountryName(code) {
    const names = {
      'US': 'United States',
      'GB': 'United Kingdom',
      'IN': 'India',
      'AE': 'United Arab Emirates',
      'FR': 'France',
      'JP': 'Japan',
      'DE': 'Germany',
      'IT': 'Italy',
      'ES': 'Spain',
      'AU': 'Australia',
      // ... extend as needed
    };
    return names[code] || code;
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  const mapContainer = document.querySelector('.about-map');
  if (mapContainer) {
    new WorldMap(mapContainer);
  }
});
```

---

## USER STORIES

### Country Display

**US-MAP-001: SVG World Map**
- [ ] Full world map visible
- [ ] All countries identifiable
- [ ] Clean, minimal style

**US-MAP-002: Country Paths**
- [ ] Each country has data-country attribute
- [ ] ISO 2-letter codes used
- [ ] Paths are selectable

**US-MAP-003: Default Country Color**
- [ ] Unvisited: #E8E8E8 (light gray)
- [ ] Clean, neutral appearance
- [ ] White borders between

**US-MAP-004: Visited Country Color**
- [ ] Visited: var(--map-visited)
- [ ] Light tan (warmLight)
- [ ] Clear difference from default

**US-MAP-005: Lived Country Color**
- [ ] Lived: var(--map-lived)
- [ ] Dark brown (warm)
- [ ] More prominent than visited

**US-MAP-006: Current Country Color**
- [ ] Current: var(--map-current)
- [ ] Terracotta (NOT blue)
- [ ] Most prominent color

**US-MAP-007: Hover States**
- [ ] Interactive countries only
- [ ] Slight color change on hover
- [ ] Cursor changes to pointer

**US-MAP-008: Country Borders**
- [ ] White stroke between countries
- [ ] 0.5px stroke width
- [ ] Clean separation

### Current Location Marker

**US-MAP-009: Marker Display**
- [ ] Dot on current country
- [ ] Centered on country
- [ ] White border for visibility

**US-MAP-010: Pulse Animation**
- [ ] Expanding circle animation
- [ ] 2s duration
- [ ] Infinite loop

**US-MAP-011: Marker Visibility**
- [ ] Above country fill
- [ ] Not clickable (pointer-events: none)
- [ ] Clear focal point

### Tooltip

**US-MAP-012: Tooltip Display**
- [ ] Appears on hover/tap
- [ ] Contains country name, years, story
- [ ] Positioned near country

**US-MAP-013: Tooltip Header**
- [ ] Country name (mono, uppercase)
- [ ] Years below name
- [ ] Close button (X)

**US-MAP-014: Tooltip Story**
- [ ] Italic serif font
- [ ] Max 200 characters
- [ ] Readable line height

**US-MAP-015: Tooltip Positioning**
- [ ] Above country by default
- [ ] Flips to below if near top
- [ ] Stays within container

**US-MAP-016: Tooltip Close Button**
- [ ] X icon in corner
- [ ] Clickable/tappable
- [ ] Visible on hover

**US-MAP-017: Tooltip Close Behavior**
- [ ] X button closes
- [ ] Click outside closes
- [ ] Escape key closes

**US-MAP-018: Tooltip Animation**
- [ ] Fade in (200ms)
- [ ] Slight translateY
- [ ] Smooth appearance

### Interactions

**US-MAP-019: Desktop Hover**
- [ ] Tooltip on mouseover
- [ ] Stays while hovering tooltip
- [ ] Hides on mouseout

**US-MAP-020: Touch Support**
- [ ] Tap to show tooltip
- [ ] Tap outside to close
- [ ] No hover states

**US-MAP-021: Keyboard Support**
- [ ] Escape closes tooltip
- [ ] Focus visible on close button
- [ ] Accessible interaction

**US-MAP-022: Countries Without Stories**
- [ ] Still show color
- [ ] No tooltip
- [ ] Cursor: default

### Legend

**US-MAP-023: Legend Display**
- [ ] Below map
- [ ] Centered
- [ ] All 4 states shown

**US-MAP-024: Legend Items**
- [ ] Color swatch
- [ ] Label text
- [ ] Mono font, 11px

**US-MAP-025: Legend Order**
- [ ] Current first
- [ ] Lived second
- [ ] Visited third
- [ ] Not visited last

**US-MAP-026: Legend Responsive**
- [ ] Wraps on mobile
- [ ] Spacing adjusts
- [ ] Readable at all sizes

### Section Label

**US-MAP-027: Label Display**
- [ ] "02" number
- [ ] Brown underline
- [ ] Customizable text

**US-MAP-028: Label Position**
- [ ] Above map
- [ ] Centered
- [ ] Proper spacing

### Responsive

**US-MAP-029: Map Scaling**
- [ ] Maintains aspect ratio
- [ ] Max-width: 900px
- [ ] Responsive on mobile

**US-MAP-030: Mobile Touch Targets**
- [ ] Countries tappable
- [ ] 44px minimum target
- [ ] Pinch-to-zoom optional

**US-MAP-031: Mobile Tooltip**
- [ ] Simplified on small screens
- [ ] Clear close button
- [ ] Readable text

### Performance

**US-MAP-032: SVG Optimization**
- [ ] Simplified paths
- [ ] Minimal file size
- [ ] Fast rendering

**US-MAP-033: Animation Performance**
- [ ] CSS animations for pulse
- [ ] No jank
- [ ] GPU accelerated

### Accessibility

**US-MAP-034: ARIA Labels**
- [ ] Map role="img"
- [ ] Country aria-labels
- [ ] Tooltip aria-live

**US-MAP-035: Screen Reader**
- [ ] Country names announced
- [ ] Story content accessible
- [ ] Navigation explained

**US-MAP-036: Reduced Motion**
- [ ] No pulse animation
- [ ] Static marker
- [ ] Instant tooltip

---

## CUSTOMIZER FIELDS

```php
// Already defined in 11-ADMIN-CUSTOMIZER.md
// World Map section:
// - kunaal_about_map_show (toggle)
// - kunaal_about_map_label (text - "Places I've Called Home")
// - kunaal_about_map_visited (text - comma-separated ISO codes)
// - kunaal_about_map_lived (text - comma-separated ISO codes)
// - kunaal_about_map_current (text - single ISO code)
// - kunaal_map_story_{1-10}_country (text)
// - kunaal_map_story_{1-10}_years (text)
// - kunaal_map_story_{1-10}_text (textarea - max 200 chars)
```

---

## DATA STRUCTURE (PHP TO JS)

```php
<?php
// In page-about.php or similar
$map_data = array(
    'visited' => array_filter(array_map('trim', explode(',', get_theme_mod('kunaal_about_map_visited', '')))),
    'lived' => array_filter(array_map('trim', explode(',', get_theme_mod('kunaal_about_map_lived', '')))),
    'current' => trim(get_theme_mod('kunaal_about_map_current', '')),
    'stories' => array()
);

for ($i = 1; $i <= 10; $i++) {
    $country = get_theme_mod("kunaal_map_story_{$i}_country", '');
    if ($country) {
        $map_data['stories'][$country] = array(
            'years' => get_theme_mod("kunaal_map_story_{$i}_years", ''),
            'story' => get_theme_mod("kunaal_map_story_{$i}_text", '')
        );
    }
}
?>

<script>
window.aboutMapData = <?php echo json_encode($map_data); ?>;
</script>
```

---

## EDGE CASES

### E-MAP-001: No Countries
- Hide map section entirely
- Smooth flow to next section

### E-MAP-002: Only Current Location
- Show map with one country
- Still display legend
- Marker visible

### E-MAP-003: Unknown Country Code
- Silently ignore
- Log warning in console
- Other countries still work

### E-MAP-004: Very Long Story
- Truncate at 200 chars
- Add ellipsis
- Tooltip doesn't overflow

### E-MAP-005: Mobile Very Small Screen
- Map still usable
- May need horizontal scroll
- Tooltip readable

### E-MAP-006: Story Without Years
- Show just country and story
- Years field hidden
- Still valid

---

## FINAL CHECKLIST

### Map Display
- [ ] SVG world map loaded
- [ ] Country paths with data-country
- [ ] Proper aspect ratio (2:1)
- [ ] Contained in max-width

### Colors
- [ ] Default: #E8E8E8
- [ ] Visited: warmLight
- [ ] Lived: warm
- [ ] Current: terracotta
- [ ] NO BLUE

### Tooltip
- [ ] Country name (mono, uppercase)
- [ ] Years (if provided)
- [ ] Story (italic, serif)
- [ ] Close button (X)
- [ ] Positioned correctly

### Interactions
- [ ] Hover shows tooltip (desktop)
- [ ] Tap shows tooltip (mobile)
- [ ] Click outside closes
- [ ] Escape closes
- [ ] Close button works

### Current Marker
- [ ] Dot on current country
- [ ] Pulse animation
- [ ] Correct positioning

### Legend
- [ ] All 4 categories
- [ ] Color swatches
- [ ] Mono labels
- [ ] Centered below map

### Accessibility
- [ ] ARIA labels
- [ ] Keyboard support
- [ ] Reduced motion
- [ ] Screen reader friendly


