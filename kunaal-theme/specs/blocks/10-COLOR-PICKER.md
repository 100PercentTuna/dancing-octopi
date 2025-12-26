# Custom Color Picker Component Specification

> **Component:** `KunaalColorPicker`  
> **Type:** Reusable Gutenberg component  
> **Dependencies:** @wordpress/components

---

## 1. Overview

A custom color picker component designed for chart and visualization blocks. It provides theme-aware color selection, palette presets, gradient creation, and accessibility features.

### 1.1 Requirements
- Theme colors prominently displayed
- Custom color input option
- Color palette presets for different visualization types
- Gradient picker for sequential data
- Accessibility (color blind safe palettes)
- Contrast checking against background

---

## 2. Visual Design

### 2.1 Compact Mode (Default)
```
┌─────────────────────────────────────────┐
│  Color                                  │
│  ┌───────────────────────────────────┐  │
│  │ [🟤] [🟡] [🟠] [🔵] [🟢] [⚪]     │  │
│  │  ↑ Theme Colors                   │  │
│  └───────────────────────────────────┘  │
│  ● Theme  ○ Custom  ○ Gradient          │
└─────────────────────────────────────────┘
```

### 2.2 Expanded Mode (Custom)
```
┌─────────────────────────────────────────┐
│  Color                                  │
│  ┌───────────────────────────────────┐  │
│  │        Theme Colors               │  │
│  │ [🟤] [🟡] [🟠] [🔵] [🟢] [⚪]     │  │
│  ├───────────────────────────────────┤  │
│  │        Custom Color               │  │
│  │ ┌─────────────────────────────┐   │  │
│  │ │                             │   │  │
│  │ │     [Color Spectrum]        │   │  │
│  │ │                             │   │  │
│  │ └─────────────────────────────┘   │  │
│  │ ┌─────────────────────────────┐   │  │
│  │ │     [Lightness Slider]      │   │  │
│  │ └─────────────────────────────┘   │  │
│  │                                   │  │
│  │ HEX: [#7D6B5D    ]               │  │
│  │ RGB: [125] [107] [93]            │  │
│  │                                   │  │
│  │ ⚠️ Low contrast with background  │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

### 2.3 Gradient Mode
```
┌─────────────────────────────────────────┐
│  Color Scale                            │
│  ┌───────────────────────────────────┐  │
│  │  Presets:                         │  │
│  │  [Sequential] [Diverging] [Custom]│  │
│  ├───────────────────────────────────┤  │
│  │  ┌─────────────────────────────┐  │  │
│  │  │░░░░░▒▒▒▒▒▓▓▓▓▓█████████│  │  │
│  │  └─────────────────────────────┘  │  │
│  │    ▲                         ▲    │  │
│  │  [Low]                    [High]  │  │
│  ├───────────────────────────────────┤  │
│  │  Low Color:  [🟤] #F5F0EB         │  │
│  │  High Color: [🟤] #7D6B5D         │  │
│  │  Steps: [○5 ●7 ○9]                │  │
│  └───────────────────────────────────┘  │
└─────────────────────────────────────────┘
```

---

## 3. Component API

### 3.1 Props

```typescript
interface KunaalColorPickerProps {
  // Current value
  value: string;
  onChange: (color: string) => void;
  
  // Mode
  mode?: 'single' | 'gradient' | 'palette';
  
  // For gradient mode
  gradientValue?: {
    low: string;
    high: string;
    mid?: string;
    steps?: number;
  };
  onGradientChange?: (gradient: GradientValue) => void;
  
  // For palette mode
  paletteValue?: string[];
  onPaletteChange?: (colors: string[]) => void;
  paletteSize?: number;
  
  // Presets
  showPresets?: boolean;
  presets?: ColorPreset[];
  
  // Features
  showContrastWarning?: boolean;
  contrastBackground?: string;
  showColorBlindSafe?: boolean;
  
  // Display
  label?: string;
  help?: string;
  size?: 'small' | 'default' | 'large';
}

interface ColorPreset {
  name: string;
  colors: string[];
  type: 'sequential' | 'diverging' | 'categorical';
}

interface GradientValue {
  low: string;
  high: string;
  mid?: string;
  steps: number;
}
```

### 3.2 Usage Examples

```jsx
// Single color
<KunaalColorPicker
  label="Bar Color"
  value={attributes.barColor}
  onChange={(color) => setAttributes({ barColor: color })}
  showContrastWarning
  contrastBackground="#FAF8F5"
/>

// Gradient for choropleth
<KunaalColorPicker
  label="Color Scale"
  mode="gradient"
  gradientValue={{
    low: attributes.colorLow,
    high: attributes.colorHigh,
    steps: attributes.colorSteps
  }}
  onGradientChange={(g) => setAttributes({
    colorLow: g.low,
    colorHigh: g.high,
    colorSteps: g.steps
  })}
  showPresets
  presets={SEQUENTIAL_PRESETS}
/>

// Category palette
<KunaalColorPicker
  label="Series Colors"
  mode="palette"
  paletteValue={attributes.seriesColors}
  onPaletteChange={(colors) => setAttributes({ seriesColors: colors })}
  paletteSize={attributes.seriesCount}
/>
```

---

## 4. Theme Color Palette

### 4.1 Standard Theme Colors

```javascript
const THEME_COLORS = [
  { name: 'Brown', color: '#7D6B5D', slug: 'warm' },
  { name: 'Light Brown', color: '#B8A99A', slug: 'warm-light' },
  { name: 'Champagne', color: '#D4C4B5', slug: 'champagne' },
  { name: 'Blue', color: '#4A90A4', slug: 'blue' },
  { name: 'Terracotta', color: '#C9553D', slug: 'terracotta' },
  { name: 'Sienna', color: '#8B7355', slug: 'sienna' },
  { name: 'Ink', color: '#1A1A1A', slug: 'ink' },
  { name: 'Muted', color: '#666666', slug: 'muted' },
  { name: 'Background', color: '#FAF8F5', slug: 'bg' },
];
```

### 4.2 Chart-Specific Palettes

```javascript
const CHART_PALETTES = {
  categorical: [
    '#7D6B5D', '#4A90A4', '#C9553D', '#8B7355', 
    '#6B8F71', '#9B8AA6', '#B8A99A', '#D4887A'
  ],
  sequential: {
    brown: ['#F5F0EB', '#E8DFD5', '#D4C4B5', '#B8A99A', '#8B7355', '#7D6B5D', '#5C4A3D'],
    blue: ['#E8F4F8', '#C5E3ED', '#8BC9DB', '#4A90A4', '#3A7A8E', '#2A6A7E', '#1A5A6E'],
  },
  diverging: {
    brownTerracotta: ['#C9553D', '#D4887A', '#E8C4BC', '#F5F0EB', '#D4C4B5', '#B8A99A', '#7D6B5D'],
    blueOrange: ['#4A90A4', '#8BC9DB', '#C5E3ED', '#F5F0EB', '#FFDAB9', '#E8956E', '#C9553D'],
  }
};
```

---

## 5. Accessibility Features

### 5.1 Contrast Checker

```javascript
function checkContrast(foreground, background) {
  const l1 = getLuminance(foreground);
  const l2 = getLuminance(background);
  const ratio = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
  
  return {
    ratio: ratio.toFixed(2),
    passesAA: ratio >= 4.5,
    passesAALarge: ratio >= 3,
    passesAAA: ratio >= 7,
  };
}
```

### 5.2 Color Blind Safe Palettes

```javascript
const COLOR_BLIND_SAFE = {
  // Okabe-Ito palette
  categorical: ['#E69F00', '#56B4E9', '#009E73', '#F0E442', '#0072B2', '#D55E00', '#CC79A7'],
  
  // Safe sequential (single hue)
  sequential: ['#f7fcf5', '#e5f5e0', '#c7e9c0', '#a1d99b', '#74c476', '#41ab5d', '#238b45'],
  
  // Safe diverging (blue-orange, avoid red-green)
  diverging: ['#2166ac', '#67a9cf', '#d1e5f0', '#f7f7f7', '#fddbc7', '#ef8a62', '#b2182b'],
};
```

### 5.3 Color Blind Simulation Toggle

```jsx
// In picker UI
<ToggleControl
  label="Preview for color blindness"
  checked={simulateColorBlind}
  onChange={setSimulateColorBlind}
/>

{simulateColorBlind && (
  <SelectControl
    label="Type"
    value={colorBlindType}
    options={[
      { label: 'Deuteranopia (green-blind)', value: 'deuteranopia' },
      { label: 'Protanopia (red-blind)', value: 'protanopia' },
      { label: 'Tritanopia (blue-blind)', value: 'tritanopia' },
    ]}
    onChange={setColorBlindType}
  />
)}
```

---

## 6. Gradient Generation

### 6.1 Interpolation Function

```javascript
function interpolateColors(color1, color2, steps) {
  const colors = [];
  
  for (let i = 0; i < steps; i++) {
    const t = i / (steps - 1);
    colors.push(interpolateColor(color1, color2, t));
  }
  
  return colors;
}

function interpolateColor(c1, c2, t) {
  // Use OKLCH for perceptually uniform interpolation
  const oklch1 = hexToOklch(c1);
  const oklch2 = hexToOklch(c2);
  
  return oklchToHex({
    l: oklch1.l + (oklch2.l - oklch1.l) * t,
    c: oklch1.c + (oklch2.c - oklch1.c) * t,
    h: interpolateHue(oklch1.h, oklch2.h, t),
  });
}
```

### 6.2 Diverging Gradient

```javascript
function generateDivergingScale(negative, neutral, positive, steps) {
  const halfSteps = Math.floor(steps / 2);
  const leftScale = interpolateColors(negative, neutral, halfSteps + 1);
  const rightScale = interpolateColors(neutral, positive, halfSteps + 1);
  
  // Remove duplicate neutral
  return [...leftScale.slice(0, -1), ...rightScale];
}
```

---

## 7. Component Implementation

### 7.1 Main Component Structure

```jsx
// KunaalColorPicker.js
import { useState } from '@wordpress/element';
import { 
  ColorPicker, 
  ColorPalette, 
  BaseControl,
  Button,
  ButtonGroup,
  RangeControl,
  Popover 
} from '@wordpress/components';

export function KunaalColorPicker({
  value,
  onChange,
  mode = 'single',
  gradientValue,
  onGradientChange,
  paletteValue,
  onPaletteChange,
  paletteSize = 5,
  showPresets = true,
  showContrastWarning = false,
  contrastBackground = '#FAF8F5',
  label,
  help,
}) {
  const [activeTab, setActiveTab] = useState('theme');
  const [isExpanded, setIsExpanded] = useState(false);
  
  if (mode === 'gradient') {
    return (
      <GradientPicker
        value={gradientValue}
        onChange={onGradientChange}
        showPresets={showPresets}
        label={label}
      />
    );
  }
  
  if (mode === 'palette') {
    return (
      <PalettePicker
        value={paletteValue}
        onChange={onPaletteChange}
        size={paletteSize}
        label={label}
      />
    );
  }
  
  return (
    <BaseControl label={label} help={help}>
      <div className="kunaal-color-picker">
        {/* Theme colors */}
        <ColorPalette
          colors={THEME_COLORS}
          value={value}
          onChange={onChange}
          clearable={false}
        />
        
        {/* Mode tabs */}
        <ButtonGroup className="kunaal-color-picker__tabs">
          <Button 
            isPressed={activeTab === 'theme'}
            onClick={() => setActiveTab('theme')}
          >
            Theme
          </Button>
          <Button 
            isPressed={activeTab === 'custom'}
            onClick={() => setActiveTab('custom')}
          >
            Custom
          </Button>
        </ButtonGroup>
        
        {/* Custom color picker */}
        {activeTab === 'custom' && (
          <ColorPicker
            color={value}
            onChange={onChange}
            enableAlpha={false}
          />
        )}
        
        {/* Contrast warning */}
        {showContrastWarning && (
          <ContrastWarning 
            color={value} 
            background={contrastBackground} 
          />
        )}
      </div>
    </BaseControl>
  );
}
```

### 7.2 Gradient Picker Sub-component

```jsx
function GradientPicker({ value, onChange, showPresets, label }) {
  const { low, high, mid, steps = 7 } = value || {};
  
  return (
    <BaseControl label={label}>
      <div className="kunaal-gradient-picker">
        {/* Preset buttons */}
        {showPresets && (
          <div className="kunaal-gradient-picker__presets">
            <Button onClick={() => onChange(PRESETS.brownSequential)}>
              Brown Sequential
            </Button>
            <Button onClick={() => onChange(PRESETS.brownDiverging)}>
              Brown Diverging
            </Button>
            <Button onClick={() => onChange(PRESETS.colorBlindSafe)}>
              Color Blind Safe
            </Button>
          </div>
        )}
        
        {/* Gradient preview */}
        <div className="kunaal-gradient-picker__preview"
             style={{
               background: `linear-gradient(to right, ${low}, ${high})`
             }} />
        
        {/* Color inputs */}
        <div className="kunaal-gradient-picker__inputs">
          <div className="kunaal-gradient-picker__input">
            <label>Low</label>
            <ColorIndicator colorValue={low} />
            <TextControl 
              value={low} 
              onChange={(v) => onChange({ ...value, low: v })} 
            />
          </div>
          
          <div className="kunaal-gradient-picker__input">
            <label>High</label>
            <ColorIndicator colorValue={high} />
            <TextControl 
              value={high} 
              onChange={(v) => onChange({ ...value, high: v })} 
            />
          </div>
        </div>
        
        {/* Steps control */}
        <RangeControl
          label="Steps"
          value={steps}
          onChange={(v) => onChange({ ...value, steps: v })}
          min={3}
          max={11}
          step={2}
        />
        
        {/* Generated palette preview */}
        <div className="kunaal-gradient-picker__generated">
          {interpolateColors(low, high, steps).map((color, i) => (
            <div 
              key={i} 
              className="kunaal-gradient-picker__step"
              style={{ backgroundColor: color }}
              title={color}
            />
          ))}
        </div>
      </div>
    </BaseControl>
  );
}
```

---

## 8. Styling

```css
.kunaal-color-picker {
  padding: 12px;
  border: 1px solid var(--border);
  border-radius: 4px;
  background: var(--bg);
}

.kunaal-color-picker__tabs {
  margin: 12px 0;
}

.kunaal-gradient-picker__preview {
  height: 24px;
  border-radius: 4px;
  margin-bottom: 12px;
}

.kunaal-gradient-picker__generated {
  display: flex;
  gap: 2px;
  margin-top: 12px;
}

.kunaal-gradient-picker__step {
  flex: 1;
  height: 24px;
  border-radius: 2px;
  cursor: pointer;
  transition: transform 150ms ease;
}

.kunaal-gradient-picker__step:hover {
  transform: scaleY(1.2);
}

.kunaal-contrast-warning {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  margin-top: 8px;
  background: #FEF3C7;
  border-radius: 4px;
  font-size: 12px;
}

.kunaal-contrast-warning--fail {
  background: #FEE2E2;
}

.kunaal-contrast-warning__icon {
  font-size: 16px;
}
```

---

## 9. User Stories

### US-CP-01: Select Theme Color
**As a** content author  
**I want to** quickly select from theme colors  
**So that** my charts match the site design  

**Acceptance Criteria:**
- [ ] Theme colors displayed prominently
- [ ] Click to select
- [ ] Current selection highlighted
- [ ] Color name shown on hover

### US-CP-02: Custom Color Input
**As a** content author  
**I want to** enter a custom hex color  
**So that** I can match specific brand colors  

**Acceptance Criteria:**
- [ ] Can type hex code directly
- [ ] Can use color spectrum picker
- [ ] Can adjust lightness
- [ ] RGB values shown

### US-CP-03: Gradient for Data
**As a** content author  
**I want to** create a color gradient for choropleth maps  
**So that** values are encoded as a color scale  

**Acceptance Criteria:**
- [ ] Can select low and high colors
- [ ] Preview shows interpolated gradient
- [ ] Can choose number of steps
- [ ] Preset gradients available

### US-CP-04: Contrast Warning
**As a** content author  
**I want to** be warned if text color has low contrast  
**So that** I maintain accessibility  

**Acceptance Criteria:**
- [ ] Warning shown for low contrast
- [ ] Contrast ratio displayed
- [ ] Suggestions for better colors
- [ ] Can dismiss warning


