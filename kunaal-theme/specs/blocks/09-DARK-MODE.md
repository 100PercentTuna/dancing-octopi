# Dark Mode Implementation Specification

> **Feature:** Theme-wide dark mode support  
> **Scope:** All blocks, pages, and components  
> **Priority:** High (Foundation)

---

## 1. Overview

Dark mode provides an alternative color scheme that reduces eye strain in low-light conditions, saves battery on OLED screens, and offers user preference accommodation. Implementation follows system preferences with manual override option.

### 1.1 Requirements
- Respect `prefers-color-scheme` media query
- Manual toggle in header/footer
- Persist user preference (localStorage)
- Smooth transition between modes
- All blocks must support both modes
- Charts and visualizations adapt intelligently

---

## 2. Color System

### 2.1 Light Mode Palette (Current)

```css
:root {
  --bg: #FAF8F5;
  --bg-alt: #F5F0EB;
  --ink: #1A1A1A;
  --ink-muted: #666666;
  --warm: #7D6B5D;
  --warmLight: #B8A99A;
  --warmLighter: #D4C4B5;
  --warmLightest: #E8DFD5;
  --blue: #4A90A4;
  --blueTint: rgba(74, 144, 164, 0.1);
  --terracotta: #C9553D;
  --border: #E5E5E5;
  --shadow: rgba(0, 0, 0, 0.1);
}
```

### 2.2 Dark Mode Palette

```css
:root[data-theme="dark"] {
  --bg: #1A1A1A;
  --bg-alt: #252525;
  --ink: #F5F0EB;
  --ink-muted: #A0A0A0;
  --warm: #C9B8A8;           /* Lighter brown for dark bg */
  --warmLight: #8B7D6F;
  --warmLighter: #5C5248;
  --warmLightest: #3D3830;
  --blue: #6BB3C9;           /* Brighter blue for dark bg */
  --blueTint: rgba(107, 179, 201, 0.15);
  --terracotta: #E07A62;     /* Brighter terracotta */
  --border: #3D3D3D;
  --shadow: rgba(0, 0, 0, 0.4);
}
```

### 2.3 Semantic Color Tokens

```css
/* Both modes use same semantic names */
:root {
  /* Backgrounds */
  --color-bg-primary: var(--bg);
  --color-bg-secondary: var(--bg-alt);
  --color-bg-elevated: var(--bg);
  
  /* Text */
  --color-text-primary: var(--ink);
  --color-text-secondary: var(--ink-muted);
  
  /* Accents */
  --color-accent-primary: var(--warm);
  --color-accent-secondary: var(--blue);
  --color-accent-danger: var(--terracotta);
  
  /* Borders & Shadows */
  --color-border: var(--border);
  --color-shadow: var(--shadow);
}

:root[data-theme="dark"] {
  --color-bg-elevated: #2A2A2A;  /* Slightly lighter for cards */
}
```

---

## 3. Implementation

### 3.1 HTML Structure

```html
<html lang="en" data-theme="light">
  <!-- Theme attribute controls all styling -->
</html>
```

### 3.2 JavaScript Controller

```javascript
// theme-controller.js
class ThemeController {
  constructor() {
    this.storageKey = 'kunaal-theme-preference';
    this.init();
  }
  
  init() {
    // Check for saved preference
    const saved = localStorage.getItem(this.storageKey);
    
    if (saved) {
      this.setTheme(saved);
    } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
      this.setTheme('dark');
    } else {
      this.setTheme('light');
    }
    
    // Listen for system preference changes
    window.matchMedia('(prefers-color-scheme: dark)')
      .addEventListener('change', (e) => {
        if (!localStorage.getItem(this.storageKey)) {
          this.setTheme(e.matches ? 'dark' : 'light');
        }
      });
  }
  
  setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    this.updateToggle(theme);
    
    // Dispatch event for components that need to react
    window.dispatchEvent(new CustomEvent('themechange', { detail: { theme } }));
  }
  
  toggle() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    this.setTheme(next);
    localStorage.setItem(this.storageKey, next);
  }
  
  updateToggle(theme) {
    const toggle = document.querySelector('.theme-toggle');
    if (toggle) {
      toggle.setAttribute('aria-pressed', theme === 'dark');
      toggle.querySelector('.theme-toggle-icon').textContent = 
        theme === 'dark' ? '☀️' : '🌙';
    }
  }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
  window.themeController = new ThemeController();
});
```

### 3.3 Theme Toggle Component

```html
<button class="theme-toggle" 
        type="button"
        aria-label="Toggle dark mode"
        aria-pressed="false"
        onclick="themeController.toggle()">
  <span class="theme-toggle-icon" aria-hidden="true">🌙</span>
  <span class="sr-only">Toggle dark mode</span>
</button>
```

```css
.theme-toggle {
  background: transparent;
  border: 1px solid var(--border);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: border-color 200ms ease, background-color 200ms ease;
}

.theme-toggle:hover {
  border-color: var(--warm);
  background-color: var(--bg-alt);
}

.theme-toggle:focus-visible {
  outline: 2px solid var(--blue);
  outline-offset: 2px;
}

.theme-toggle-icon {
  font-size: 18px;
  line-height: 1;
}
```

---

## 4. Transition Animation

### 4.1 Smooth Color Transition

```css
/* Apply to root for global transition */
:root {
  transition: 
    background-color 300ms ease,
    color 300ms ease;
}

/* Opt-out for elements that shouldn't transition */
.no-theme-transition,
.chart-canvas,
.map-container {
  transition: none !important;
}

/* Reduced motion preference */
@media (prefers-reduced-motion: reduce) {
  :root {
    transition: none;
  }
}
```

### 4.2 Prevent Flash on Load

```html
<head>
  <!-- Inline script to set theme before render -->
  <script>
    (function() {
      const saved = localStorage.getItem('kunaal-theme-preference');
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      const theme = saved || (prefersDark ? 'dark' : 'light');
      document.documentElement.setAttribute('data-theme', theme);
    })();
  </script>
</head>
```

---

## 5. Component-Specific Adaptations

### 5.1 Charts & Visualizations

Charts need special handling because:
- Canvas elements don't inherit CSS colors
- SVG fills need explicit updates
- Gradients may need inversion

```javascript
// In chart initialization
function getChartColors() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  
  return {
    text: isDark ? '#F5F0EB' : '#1A1A1A',
    grid: isDark ? '#3D3D3D' : '#E5E5E5',
    primary: isDark ? '#C9B8A8' : '#7D6B5D',
    secondary: isDark ? '#6BB3C9' : '#4A90A4',
    background: isDark ? '#1A1A1A' : '#FAF8F5',
  };
}

// Listen for theme changes
window.addEventListener('themechange', (e) => {
  updateChartColors(e.detail.theme);
});
```

### 5.2 Maps

```javascript
// Leaflet tile layer for dark mode
const lightTiles = 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png';
const darkTiles = 'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png';

window.addEventListener('themechange', (e) => {
  const tiles = e.detail.theme === 'dark' ? darkTiles : lightTiles;
  tileLayer.setUrl(tiles);
});
```

### 5.3 Images

```css
/* Reduce image brightness in dark mode */
:root[data-theme="dark"] img:not(.no-dim) {
  filter: brightness(0.9);
}

/* Invert diagrams/illustrations if needed */
:root[data-theme="dark"] .invertible-image {
  filter: invert(1) hue-rotate(180deg);
}
```

### 5.4 Code Blocks

```css
/* Syntax highlighting adjusts */
:root[data-theme="dark"] pre,
:root[data-theme="dark"] code {
  background-color: #2A2A2A;
  border-color: var(--border);
}

:root[data-theme="dark"] .token.comment { color: #6B7280; }
:root[data-theme="dark"] .token.keyword { color: #C792EA; }
:root[data-theme="dark"] .token.string { color: #C3E88D; }
/* etc. */
```

---

## 6. Block-Specific Rules

### 6.1 All Chart Blocks

```css
:root[data-theme="dark"] .wp-block-kunaal-chart,
:root[data-theme="dark"] .wp-block-kunaal-heatmap,
:root[data-theme="dark"] .wp-block-kunaal-network-graph {
  --chart-bg: var(--bg-alt);
  --chart-border: var(--border);
  --chart-text: var(--ink);
  --chart-grid: var(--border);
}
```

### 6.2 Heatmap Adjustments

Heatmap colors need to be inverted or adjusted for visibility:

```css
:root[data-theme="dark"] .heatmap-cell {
  /* Invert the luminosity scale */
  --heatmap-color-low: #3D3830;
  --heatmap-color-high: #C9B8A8;
}
```

### 6.3 Cards and Elevated Elements

```css
:root[data-theme="dark"] .inspiration-card,
:root[data-theme="dark"] .book-slot,
:root[data-theme="dark"] .stat-block {
  background-color: var(--color-bg-elevated);
  border-color: var(--border);
  box-shadow: 0 4px 12px var(--shadow);
}
```

---

## 7. Customizer Integration

### 7.1 Theme Setting

Add to theme customizer:

```php
// Theme mode setting
$wp_customize->add_setting('kunaal_default_theme', array(
    'default' => 'system',
    'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control('kunaal_default_theme', array(
    'type' => 'select',
    'section' => 'kunaal_general',
    'label' => 'Default Color Scheme',
    'description' => 'Choose the default theme for new visitors',
    'choices' => array(
        'system' => 'Follow system preference',
        'light' => 'Always light',
        'dark' => 'Always dark',
    ),
));

// Show theme toggle
$wp_customize->add_setting('kunaal_show_theme_toggle', array(
    'default' => true,
    'sanitize_callback' => 'absint',
));

$wp_customize->add_control('kunaal_show_theme_toggle', array(
    'type' => 'checkbox',
    'section' => 'kunaal_general',
    'label' => 'Show theme toggle button',
));
```

---

## 8. Accessibility

### 8.1 Contrast Requirements

All color combinations must meet WCAG 2.1 AA:
- Normal text: 4.5:1 contrast ratio
- Large text (18px+): 3:1 contrast ratio
- UI components: 3:1 contrast ratio

### 8.2 Testing Matrix

| Element | Light Mode | Dark Mode | Ratio |
|---------|------------|-----------|-------|
| Body text on bg | #1A1A1A on #FAF8F5 | #F5F0EB on #1A1A1A | 15.5:1 |
| Muted text on bg | #666666 on #FAF8F5 | #A0A0A0 on #1A1A1A | 8.5:1 |
| Warm accent on bg | #7D6B5D on #FAF8F5 | #C9B8A8 on #1A1A1A | 6.2:1 |
| Blue on bg | #4A90A4 on #FAF8F5 | #6BB3C9 on #1A1A1A | 5.1:1 |

---

## 9. Testing Checklist

### 9.1 Visual Testing
- [ ] All pages render correctly in both modes
- [ ] Transitions are smooth (or instant with reduced-motion)
- [ ] No flash of wrong theme on load
- [ ] Images and media display appropriately

### 9.2 Functional Testing
- [ ] System preference detection works
- [ ] Manual toggle works
- [ ] Preference persists across sessions
- [ ] Toggle state syncs across tabs

### 9.3 Block Testing
- [ ] All chart blocks render correctly
- [ ] All map blocks adapt (tiles + colors)
- [ ] All interactive elements have proper states
- [ ] Hover/focus states visible in both modes

---

## 10. User Stories

### US-DM-01: Follow System Preference
**As a** user with system dark mode enabled  
**I want** the site to automatically use dark mode  
**So that** it matches my system preference  

**Acceptance Criteria:**
- [ ] Site detects `prefers-color-scheme`
- [ ] Dark mode applied automatically
- [ ] No flash of light mode

### US-DM-02: Manual Toggle
**As a** user  
**I want** to manually switch between light and dark mode  
**So that** I can override my system preference  

**Acceptance Criteria:**
- [ ] Toggle button visible in header
- [ ] Clicking toggles theme
- [ ] Preference saved for next visit
- [ ] Smooth transition animation

### US-DM-03: Chart Readability
**As a** reader viewing charts in dark mode  
**I want** charts to have appropriate colors  
**So that** data is still readable and clear  

**Acceptance Criteria:**
- [ ] Chart text is readable
- [ ] Grid lines visible but not distracting
- [ ] Data series colors contrast with background
- [ ] Tooltips match theme

### US-DM-04: Map Tiles
**As a** reader viewing maps in dark mode  
**I want** map tiles to use dark styling  
**So that** the map doesn't create a bright area  

**Acceptance Criteria:**
- [ ] Map tiles switch to dark variant
- [ ] Data overlays remain visible
- [ ] Legend adapts to theme
- [ ] Controls visible in both modes


