# Advanced Customization System Specification

> **Feature:** Block customization infrastructure  
> **Scope:** All chart and visualization blocks  
> **Goal:** Maximum flexibility with user-friendly interface

---

## 1. Overview

This specification defines a comprehensive customization system that allows users to deeply customize every aspect of charts and blocks while maintaining an intuitive, non-JSON interface.

### 1.1 Design Principles
- **Progressive Disclosure:** Simple options first, advanced hidden by default
- **Sensible Defaults:** Works beautifully out of the box
- **No JSON:** All configuration via native WordPress controls
- **Presets:** Common configurations saveable and reusable
- **Live Preview:** All changes reflected immediately
- **Reset to Default:** Easy way to undo customizations

---

## 2. Customization Layers

### 2.1 Layer Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│  Layer 4: Block Instance Overrides                              │
│  (Highest priority - per-block settings)                        │
├─────────────────────────────────────────────────────────────────┤
│  Layer 3: Custom Presets                                        │
│  (User-created presets saved to theme)                          │
├─────────────────────────────────────────────────────────────────┤
│  Layer 2: Theme Customizer Settings                             │
│  (Site-wide defaults via Customizer)                            │
├─────────────────────────────────────────────────────────────────┤
│  Layer 1: Theme Defaults                                        │
│  (Baked into theme, follows design system)                      │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Resolution Order

```javascript
function getBlockSetting(settingKey, blockAttributes) {
  // 1. Check block instance override
  if (blockAttributes[settingKey] !== undefined) {
    return blockAttributes[settingKey];
  }
  
  // 2. Check applied preset
  if (blockAttributes.preset) {
    const preset = getPreset(blockAttributes.preset);
    if (preset[settingKey] !== undefined) {
      return preset[settingKey];
    }
  }
  
  // 3. Check theme customizer
  const customizerValue = wp.customize(settingKey)?.get();
  if (customizerValue !== undefined) {
    return customizerValue;
  }
  
  // 4. Return theme default
  return THEME_DEFAULTS[settingKey];
}
```

---

## 3. Inspector Panel Organization

### 3.1 Collapsible Sections

All blocks use consistent panel organization:

```
┌─────────────────────────────────────────┐
│  📊 Chart: Bar Chart                    │
├─────────────────────────────────────────┤
│  ▼ Data                                 │
│    [Data entry controls...]             │
├─────────────────────────────────────────┤
│  ▼ Appearance                           │
│    [Colors, sizes, etc...]              │
├─────────────────────────────────────────┤
│  ▶ Typography (collapsed)               │
├─────────────────────────────────────────┤
│  ▶ Axes & Grid (collapsed)              │
├─────────────────────────────────────────┤
│  ▶ Legend (collapsed)                   │
├─────────────────────────────────────────┤
│  ▶ Animation (collapsed)                │
├─────────────────────────────────────────┤
│  ▶ Accessibility (collapsed)            │
├─────────────────────────────────────────┤
│  ▶ Presets                              │
│    [Apply Preset ▼]  [Save Current]     │
└─────────────────────────────────────────┘
```

### 3.2 Panel Components

```jsx
// Reusable panel component
function KunaalPanel({ 
  title, 
  initialOpen = false, 
  icon,
  badge,
  children 
}) {
  return (
    <PanelBody 
      title={
        <span className="kunaal-panel-title">
          {icon && <span className="kunaal-panel-icon">{icon}</span>}
          {title}
          {badge && <span className="kunaal-panel-badge">{badge}</span>}
        </span>
      }
      initialOpen={initialOpen}
      className="kunaal-panel"
    >
      {children}
    </PanelBody>
  );
}
```

---

## 4. Common Customization Options

### 4.1 Typography Panel

```jsx
<KunaalPanel title="Typography" icon="🔤">
  {/* Title */}
  <ToggleControl
    label="Show Title"
    checked={showTitle}
  />
  {showTitle && (
    <>
      <FontFamilyControl
        label="Title Font"
        value={titleFont}
        options={[
          { label: 'Newsreader (Serif)', value: 'var(--serif)' },
          { label: 'Inter (Sans)', value: 'var(--sans)' },
          { label: 'Theme Default', value: 'inherit' },
        ]}
      />
      <FontSizeControl
        label="Title Size"
        value={titleSize}
        min={16}
        max={48}
        step={2}
      />
      <ColorControl
        label="Title Color"
        value={titleColor}
      />
    </>
  )}
  
  {/* Similar for subtitle, labels, values, etc. */}
</KunaalPanel>
```

### 4.2 Colors Panel

```jsx
<KunaalPanel title="Colors" icon="🎨">
  {/* Palette selection */}
  <RadioControl
    label="Color Palette"
    selected={colorPalette}
    options={[
      { label: 'Theme (Brown)', value: 'theme' },
      { label: 'Categorical', value: 'categorical' },
      { label: 'Color Blind Safe', value: 'colorblind' },
      { label: 'Custom', value: 'custom' },
    ]}
  />
  
  {colorPalette === 'custom' && (
    <KunaalColorPicker
      mode="palette"
      paletteValue={customColors}
      onChange={setCustomColors}
    />
  )}
  
  {/* Background */}
  <ColorControl
    label="Background"
    value={backgroundColor}
  />
  
  {/* Border */}
  <ColorControl
    label="Border"
    value={borderColor}
  />
</KunaalPanel>
```

### 4.3 Axes & Grid Panel

```jsx
<KunaalPanel title="Axes & Grid" icon="📐">
  {/* X Axis */}
  <h4>X Axis</h4>
  <ToggleControl label="Show X Axis" checked={showXAxis} />
  <ToggleControl label="Show X Labels" checked={showXLabels} />
  <RangeControl 
    label="Label Rotation" 
    value={xLabelRotation}
    min={0}
    max={90}
  />
  
  {/* Y Axis */}
  <h4>Y Axis</h4>
  <ToggleControl label="Show Y Axis" checked={showYAxis} />
  <ToggleControl label="Show Y Labels" checked={showYLabels} />
  <TextControl label="Min Value" value={yMin} />
  <TextControl label="Max Value" value={yMax} />
  
  {/* Grid */}
  <h4>Grid Lines</h4>
  <ToggleControl label="Show Grid" checked={showGrid} />
  <SelectControl
    label="Grid Style"
    value={gridStyle}
    options={[
      { label: 'Solid', value: 'solid' },
      { label: 'Dashed', value: 'dashed' },
      { label: 'Dotted', value: 'dotted' },
    ]}
  />
  <RangeControl 
    label="Grid Opacity" 
    value={gridOpacity}
    min={0}
    max={1}
    step={0.1}
  />
</KunaalPanel>
```

### 4.4 Animation Panel

```jsx
<KunaalPanel title="Animation" icon="✨">
  <ToggleControl
    label="Animate on Load"
    checked={animateOnLoad}
    help="Animate chart when scrolled into view"
  />
  
  {animateOnLoad && (
    <>
      <SelectControl
        label="Animation Type"
        value={animationType}
        options={[
          { label: 'Fade In', value: 'fade' },
          { label: 'Grow', value: 'grow' },
          { label: 'Draw', value: 'draw' },
          { label: 'Stagger', value: 'stagger' },
        ]}
      />
      
      <RangeControl
        label="Duration (ms)"
        value={animationDuration}
        min={200}
        max={2000}
        step={100}
      />
      
      <SelectControl
        label="Easing"
        value={animationEasing}
        options={[
          { label: 'Ease Out', value: 'ease-out' },
          { label: 'Ease In Out', value: 'ease-in-out' },
          { label: 'Bounce', value: 'bounce' },
          { label: 'Linear', value: 'linear' },
        ]}
      />
      
      <RangeControl
        label="Stagger Delay (ms)"
        value={staggerDelay}
        min={0}
        max={200}
        step={10}
        help="Delay between each element"
      />
    </>
  )}
  
  <ToggleControl
    label="Animate on Hover"
    checked={animateOnHover}
  />
  
  <Notice isDismissible={false}>
    Animations respect user's "reduce motion" preference.
  </Notice>
</KunaalPanel>
```

### 4.5 Accessibility Panel

```jsx
<KunaalPanel title="Accessibility" icon="♿">
  <ToggleControl
    label="Include Data Table"
    checked={includeDataTable}
    help="Provide data in accessible table format"
  />
  
  <ToggleControl
    label="Show Data Table by Default"
    checked={showDataTableExpanded}
  />
  
  <TextareaControl
    label="Chart Description"
    value={chartDescription}
    help="Describe what the chart shows for screen readers"
  />
  
  <ToggleControl
    label="Use Patterns (not just color)"
    checked={usePatterns}
    help="Add patterns to distinguish data series"
  />
  
  <ToggleControl
    label="High Contrast Mode"
    checked={highContrast}
    help="Use colors with maximum contrast"
  />
</KunaalPanel>
```

---

## 5. Presets System

### 5.1 Preset Data Structure

```javascript
const presetSchema = {
  id: 'string',           // Unique identifier
  name: 'string',         // Display name
  description: 'string',  // Optional description
  blockType: 'string',    // Which block type this applies to
  isDefault: 'boolean',   // Is this a theme-provided preset?
  settings: {
    // All customizable settings for the block
    colorPalette: 'theme',
    showLegend: true,
    // ... etc.
  },
  createdAt: 'timestamp',
  updatedAt: 'timestamp',
};
```

### 5.2 Built-in Presets

```javascript
const BUILT_IN_PRESETS = {
  'bar-chart': [
    {
      id: 'bar-minimal',
      name: 'Minimal',
      description: 'Clean, simple bar chart',
      settings: {
        showGrid: false,
        showXAxis: true,
        showYAxis: false,
        showLegend: false,
        colorPalette: 'theme',
      }
    },
    {
      id: 'bar-detailed',
      name: 'Detailed',
      description: 'Full axes and grid',
      settings: {
        showGrid: true,
        showXAxis: true,
        showYAxis: true,
        showLegend: true,
        showDataLabels: true,
      }
    },
    {
      id: 'bar-presentation',
      name: 'Presentation',
      description: 'Large text, high contrast',
      settings: {
        titleSize: 32,
        labelSize: 14,
        barWidth: 0.7,
        showDataLabels: true,
        highContrast: true,
      }
    },
  ],
  // ... presets for other block types
};
```

### 5.3 Preset UI Component

```jsx
<KunaalPanel title="Presets" icon="💾">
  {/* Apply preset dropdown */}
  <SelectControl
    label="Apply Preset"
    value={currentPreset}
    options={[
      { label: '— None —', value: '' },
      { label: '— Built-in —', disabled: true },
      ...builtInPresets.map(p => ({ label: p.name, value: p.id })),
      { label: '— Custom —', disabled: true },
      ...customPresets.map(p => ({ label: p.name, value: p.id })),
    ]}
    onChange={(presetId) => applyPreset(presetId)}
  />
  
  {/* Preset preview */}
  {hoveredPreset && (
    <div className="preset-preview">
      <p>{hoveredPreset.description}</p>
      <PresetThumbnail preset={hoveredPreset} />
    </div>
  )}
  
  <hr />
  
  {/* Save current as preset */}
  <h4>Save Current Settings</h4>
  <TextControl
    label="Preset Name"
    value={newPresetName}
    placeholder="My Custom Preset"
  />
  <Button 
    variant="secondary"
    onClick={saveAsPreset}
    disabled={!newPresetName}
  >
    Save as Preset
  </Button>
  
  {/* Reset to defaults */}
  <hr />
  <Button 
    variant="tertiary"
    isDestructive
    onClick={resetToDefaults}
  >
    Reset to Defaults
  </Button>
</KunaalPanel>
```

### 5.4 Preset Storage

```php
// Store presets as theme option
add_option('kunaal_block_presets', []);

// REST API endpoint for presets
register_rest_route('kunaal/v1', '/presets', [
    'methods' => ['GET', 'POST', 'DELETE'],
    'callback' => 'kunaal_handle_presets',
    'permission_callback' => function() {
        return current_user_can('edit_posts');
    }
]);
```

---

## 6. Theme Customizer Integration

### 6.1 Global Block Defaults

```php
// Add section for chart defaults
$wp_customize->add_section('kunaal_chart_defaults', [
    'title' => 'Chart Defaults',
    'panel' => 'kunaal_blocks',
    'priority' => 10,
]);

// Default chart colors
$wp_customize->add_setting('kunaal_chart_color_palette', [
    'default' => 'theme',
    'sanitize_callback' => 'sanitize_text_field',
]);

$wp_customize->add_control('kunaal_chart_color_palette', [
    'type' => 'select',
    'section' => 'kunaal_chart_defaults',
    'label' => 'Default Color Palette',
    'choices' => [
        'theme' => 'Theme Colors',
        'categorical' => 'Categorical',
        'colorblind' => 'Color Blind Safe',
    ],
]);

// Default animation behavior
$wp_customize->add_setting('kunaal_chart_animate', [
    'default' => true,
    'sanitize_callback' => 'absint',
]);

$wp_customize->add_control('kunaal_chart_animate', [
    'type' => 'checkbox',
    'section' => 'kunaal_chart_defaults',
    'label' => 'Animate charts by default',
]);

// Similar for typography, grid, legend, etc.
```

---

## 7. Advanced Options Toggle

### 7.1 Show/Hide Advanced

```jsx
function BlockInspector({ attributes, setAttributes }) {
  const [showAdvanced, setShowAdvanced] = useState(false);
  
  return (
    <InspectorControls>
      {/* Basic options always visible */}
      <KunaalPanel title="Data" initialOpen>
        {/* ... */}
      </KunaalPanel>
      
      <KunaalPanel title="Appearance" initialOpen>
        {/* ... */}
      </KunaalPanel>
      
      {/* Toggle for advanced */}
      <div className="kunaal-advanced-toggle">
        <Button
          variant="link"
          onClick={() => setShowAdvanced(!showAdvanced)}
        >
          {showAdvanced ? 'Hide' : 'Show'} Advanced Options
        </Button>
      </div>
      
      {/* Advanced panels */}
      {showAdvanced && (
        <>
          <KunaalPanel title="Typography">
            {/* ... */}
          </KunaalPanel>
          
          <KunaalPanel title="Axes & Grid">
            {/* ... */}
          </KunaalPanel>
          
          <KunaalPanel title="Animation">
            {/* ... */}
          </KunaalPanel>
        </>
      )}
      
      {/* Accessibility and Presets always visible */}
      <KunaalPanel title="Accessibility">
        {/* ... */}
      </KunaalPanel>
      
      <KunaalPanel title="Presets">
        {/* ... */}
      </KunaalPanel>
    </InspectorControls>
  );
}
```

---

## 8. Copy/Paste Settings

### 8.1 Block Settings Clipboard

```jsx
// In block toolbar
<ToolbarDropdownMenu
  icon="admin-settings"
  label="Settings"
  controls={[
    {
      title: 'Copy Settings',
      onClick: () => {
        const settings = extractSettingsFromAttributes(attributes);
        navigator.clipboard.writeText(JSON.stringify(settings));
        showNotice('Settings copied');
      },
    },
    {
      title: 'Paste Settings',
      onClick: async () => {
        try {
          const text = await navigator.clipboard.readText();
          const settings = JSON.parse(text);
          applySettings(settings);
          showNotice('Settings applied');
        } catch (e) {
          showNotice('Invalid settings', 'error');
        }
      },
    },
  ]}
/>
```

---

## 9. User Stories

### US-CU-01: Apply Preset Style
**As a** content author  
**I want to** apply a pre-made style to my chart  
**So that** I can quickly match a desired look  

**Acceptance Criteria:**
- [ ] Presets listed in dropdown
- [ ] Hovering shows preview/description
- [ ] Selecting applies all settings
- [ ] Can revert to defaults

### US-CU-02: Save Custom Preset
**As a** content author  
**I want to** save my current chart settings as a preset  
**So that** I can reuse them on other charts  

**Acceptance Criteria:**
- [ ] Can name the preset
- [ ] Settings are saved
- [ ] Preset appears in dropdown
- [ ] Can delete custom presets

### US-CU-03: Site-Wide Defaults
**As a** site admin  
**I want to** set default chart styles in Customizer  
**So that** all new charts follow our brand guidelines  

**Acceptance Criteria:**
- [ ] Customizer has chart settings
- [ ] New blocks use these defaults
- [ ] Blocks can still override
- [ ] Changes apply to new blocks only

### US-CU-04: Copy Settings Between Blocks
**As a** content author  
**I want to** copy settings from one chart to another  
**So that** I can maintain consistency  

**Acceptance Criteria:**
- [ ] Copy button in toolbar
- [ ] Paste button in toolbar
- [ ] Settings transfer correctly
- [ ] Works across block types (where applicable)


