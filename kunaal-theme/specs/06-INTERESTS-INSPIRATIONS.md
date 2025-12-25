# 06 - INTERESTS CLOUD & INSPIRATIONS
## Complete Specification

---

## INTERESTS CLOUD

### Overview
An organic, cloud-like arrangement of interests with circular images that transition from grayscale to color on hover. NOT a rigid grid.

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   THINGS THAT FASCINATE ME                   ← Mono, 11px, uppercase        │
│                                                                             │
│                    ○ ramen            ○ ww2                                 │
│        ○ geopolitics         ○ tacos           ○ jazz                      │
│               ○ typography        ○ maps                                   │
│        ○ architecture     ○ novels       ○ hiking                          │
│                     ○ coffee          ○ design                             │
│                                                                             │
│   Each ○ is a 56px circular image, grayscale by default                    │
│   On hover: image becomes color, text becomes black                         │
│   NO blue outline on hover (interests aren't links)                         │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### CSS Implementation

```css
.about-interests {
  max-width: var(--wide);
  margin: var(--space-15) auto;
  padding: 0 var(--space-4);
}

.about-interests-label {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted2);
  text-align: center;
  margin-bottom: var(--space-6);
}

.interests-cloud {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: var(--space-4) var(--space-5);
  max-width: 700px;
  margin: 0 auto;
}

.interest-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: var(--space-1);
  /* Organic positioning via random vertical offset */
  transform: translateY(var(--offset, 0));
}

/* Set random offsets via nth-child */
.interest-item:nth-child(2n) { --offset: -6px; }
.interest-item:nth-child(3n) { --offset: 4px; }
.interest-item:nth-child(5n) { --offset: -8px; }
.interest-item:nth-child(7n) { --offset: 6px; }

.interest-image {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  overflow: hidden;
  /* NO blue border - interests aren't links */
  border: 2px solid transparent;
  transition: 
    transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1),
    border-color 300ms ease;
}

.interest-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: grayscale(100%) sepia(10%);
  transition: filter 400ms ease;
}

.interest-item:hover .interest-image {
  transform: scale(1.08);
  /* Brown border on hover, NOT blue */
  border-color: var(--warmLight);
}

.interest-item:hover .interest-image img {
  filter: grayscale(0%) sepia(0%);
}

.interest-label {
  font-family: var(--mono);
  font-size: 11px;
  color: var(--muted2);
  transition: color 300ms ease;
}

.interest-item:hover .interest-label {
  color: var(--ink); /* Gray to black, NOT blue */
}
```

---

## INSPIRATIONS GRID

### Overview
A grid of people cards with photos, names, roles, and optional notes. Cards ARE links, so they get blue hover styling.

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   PEOPLE WHO INSPIRE ME                      ← Mono, 11px, uppercase        │
│                                                                             │
│   ┌───────────────┐  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐│
│   │     ○         │  │     ○         │  │     ○         │  │     ○         ││
│   │   (photo)     │  │   (photo)     │  │   (photo)     │  │   (photo)     ││
│   │               │  │               │  │               │  │               ││
│   │  Name         │  │  Name         │  │  Name         │  │  Name         ││
│   │  Role/Title   │  │  Role/Title   │  │  Role/Title   │  │  Role/Title   ││
│   │  "A note..."  │  │  "A note..."  │  │  "A note..."  │  │  "A note..."  ││
│   └───────────────┘  └───────────────┘  └───────────────┘  └───────────────┘│
│                                                                             │
│   Photos are grayscale by default, color on hover                          │
│   Cards get blue border + tint on hover (they're links)                    │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### CSS Implementation

```css
.about-inspirations {
  max-width: var(--wide);
  margin: var(--space-15) auto;
  padding: 0 var(--space-4);
}

.about-inspirations-label {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted2);
  text-align: center;
  margin-bottom: var(--space-6);
}

.inspirations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: var(--space-4);
  max-width: 900px;
  margin: 0 auto;
}

.inspiration-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: var(--space-4);
  border: 1px solid transparent;
  border-radius: 8px;
  text-decoration: none;
  transition: 
    border-color 300ms ease,
    background-color 300ms ease;
}

/* Cards ARE links - blue hover is appropriate */
.inspiration-card:hover {
  border-color: var(--blue);
  background-color: var(--blueTint);
}

.inspiration-photo {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: var(--space-3);
}

.inspiration-photo img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: grayscale(100%) sepia(10%);
  transition: filter 400ms ease;
}

.inspiration-card:hover .inspiration-photo img {
  filter: grayscale(0%) sepia(0%);
}

.inspiration-name {
  font-family: var(--serif);
  font-size: 17px;
  color: var(--muted);
  transition: color 300ms ease;
}

.inspiration-card:hover .inspiration-name {
  color: var(--ink);
}

.inspiration-role {
  font-family: var(--sans);
  font-size: 13px;
  color: var(--muted2);
  margin-top: 2px;
}

.inspiration-note {
  font-family: var(--serif);
  font-size: 14px;
  font-style: italic;
  color: var(--muted2);
  margin-top: var(--space-2);
  max-width: 180px;
  transition: color 300ms ease;
}

.inspiration-card:hover .inspiration-note {
  color: var(--muted);
}
```

---

## USER STORIES

### Interests

**US-INT-001: Cloud Layout**
- [ ] Flex wrap layout
- [ ] Varied vertical offsets (-8px to +8px)
- [ ] Not a rigid grid

**US-INT-002: Interest Image**
- [ ] Circular images (56px)
- [ ] Admin uploads
- [ ] Grayscale default

**US-INT-003: Interest Hover**
- [ ] Grayscale → color
- [ ] Scale: 1.08
- [ ] Brown border (NOT blue)

**US-INT-004: Interest Label**
- [ ] Mono, 11px
- [ ] Gray → black on hover
- [ ] Below image

### Inspirations

**US-INSP-001: Card Grid**
- [ ] Auto-fill grid
- [ ] Min 200px per card
- [ ] Responsive columns

**US-INSP-002: Photo Display**
- [ ] Circular, 80px
- [ ] Grayscale default
- [ ] Color on hover

**US-INSP-003: Card Hover**
- [ ] Blue border (it's a link)
- [ ] Blue tint background
- [ ] Photo colors

**US-INSP-004: Person Info**
- [ ] Name: serif, 17px
- [ ] Role: sans, 13px
- [ ] Note: serif italic, 14px

**US-INSP-005: Text States**
- [ ] Gray default
- [ ] Black on hover
- [ ] Matches photo transition

---

## CUSTOMIZER FIELDS

```php
// Interests Section
$wp_customize->add_section('kunaal_about_interests', array(
    'title' => 'About Page: Interests',
    'priority' => 60,
));

// Toggle
$wp_customize->add_setting('kunaal_about_interests_show', array(
    'default' => true,
));

// Section label
$wp_customize->add_setting('kunaal_about_interests_label', array(
    'default' => 'Things That Fascinate Me',
));

// Up to 20 interests
for ($i = 1; $i <= 20; $i++) {
    // Interest name
    $wp_customize->add_setting("kunaal_interest_{$i}_name", array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    // Interest image
    $wp_customize->add_setting("kunaal_interest_{$i}_image", array(
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control(...));
}

// Inspirations Section
$wp_customize->add_section('kunaal_about_inspirations', array(
    'title' => 'About Page: Inspirations',
    'priority' => 70,
));

// Toggle
$wp_customize->add_setting('kunaal_about_inspirations_show', array(
    'default' => true,
));

// Up to 8 inspirations
for ($i = 1; $i <= 8; $i++) {
    // Photo
    $wp_customize->add_setting("kunaal_inspiration_{$i}_photo");
    
    // Name
    $wp_customize->add_setting("kunaal_inspiration_{$i}_name");
    
    // Role
    $wp_customize->add_setting("kunaal_inspiration_{$i}_role");
    
    // Note
    $wp_customize->add_setting("kunaal_inspiration_{$i}_note");
    
    // URL
    $wp_customize->add_setting("kunaal_inspiration_{$i}_url");
}
```

---

## EDGE CASES

### E-INT-001: No Interests
- Hide entire section
- Smooth flow continues

### E-INT-002: Few Interests (1-3)
- Center small group
- Still looks balanced

### E-INT-003: Many Interests (15+)
- Wrap to multiple rows
- Maintain organic feel

### E-INT-004: Missing Interest Image
- Show placeholder or initials
- Label still visible

### E-INSP-001: No Inspirations
- Hide entire section

### E-INSP-002: Missing Photo
- Show initials in circle
- Card still works

### E-INSP-003: No URL
- Card is not clickable
- No blue hover (just image color)

---

## KEY DISTINCTION

**INTERESTS:** NOT links
- NO blue on hover
- Brown/warm accent on hover
- Grayscale → color transition
- Text: gray → black (NOT blue)

**INSPIRATIONS:** ARE links
- Blue border on hover
- Blue tint background
- Grayscale → color transition
- Text: gray → black

---

## FINAL CHECKLIST

### Interests Cloud
- [ ] Organic flex layout with offsets
- [ ] 56px circular images
- [ ] Grayscale default
- [ ] Color + scale on hover
- [ ] Brown border on hover (NOT blue)
- [ ] Text: gray → black
- [ ] Admin: 20 slots

### Inspirations Grid
- [ ] Auto-fill responsive grid
- [ ] 80px circular photos
- [ ] Grayscale default
- [ ] Color on hover
- [ ] Blue border + tint (they're links)
- [ ] Name, role, note displayed
- [ ] Admin: 8 slots

