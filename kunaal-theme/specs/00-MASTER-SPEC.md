# THE LAYERED EXHIBITION - MASTER SPECIFICATION
## Complete Implementation Guide for the About Page

**Version:** 3.0 - Full Depth (200+ User Stories)  
**Status:** Planning Phase  
**Last Updated:** December 2024

---

## DOCUMENT INDEX

This specification is broken into multiple files for manageability:

| File | Contents |
|------|----------|
| [00-MASTER-SPEC.md](00-MASTER-SPEC.md) | This file - overview, principles, design system |
| [01-HERO-COLLAGE.md](01-HERO-COLLAGE.md) | Hero section with photo collage algorithm |
| [02-ORGANIC-FLOW.md](02-ORGANIC-FLOW.md) | How sections blend, rhythm, pacing |
| [03-ATMOSPHERIC-IMAGES.md](03-ATMOSPHERIC-IMAGES.md) | Window motif, layered depth, image strips |
| [04-BIO-BOOKSHELF.md](04-BIO-BOOKSHELF.md) | Bio section, pull quotes, bookshelf |
| [05-WORLD-MAP.md](05-WORLD-MAP.md) | Interactive map, country shading, stories |
| [06-INTERESTS-INSPIRATIONS.md](06-INTERESTS-INSPIRATIONS.md) | Cloud layout, inspiration cards |
| [07-ANIMATIONS.md](07-ANIMATIONS.md) | All animation specs, physics, choreography |
| [08-STATES-EDGE-CASES.md](08-STATES-EDGE-CASES.md) | Empty states, fallbacks, error handling |
| [09-ACCESSIBILITY.md](09-ACCESSIBILITY.md) | ARIA, keyboard nav, screen readers |
| [10-RESPONSIVE.md](10-RESPONSIVE.md) | Breakpoints, mobile adaptations |
| [11-ADMIN-CUSTOMIZER.md](11-ADMIN-CUSTOMIZER.md) | All Customizer fields, NO JSON |
| [12-USER-STORIES.md](12-USER-STORIES.md) | Complete user story list with acceptance criteria |

---

## PART 1: THE VISION

### 1.1 The Metaphor
Imagine walking through a **contemporary photography exhibition** at a world-class gallery. Not a traditional museum with discrete rooms and wall labels, but an **immersive installation** where:

- Photographs exist at **different depths** — some mounted on walls, some floating in space, some glimpsed through architectural cutouts
- As you walk (scroll), **lighting changes** — images that seemed grayscale slowly reveal their true colors
- The experience is **one continuous journey**, not discrete stops
- Every element feels **intentionally placed**, yet the arrangement feels **organic**, not rigid
- The palette is **restrained** — warm grays, cream, with **carefully placed color accents**
- Typography is **museum-quality** — understated, elegant, never shouty

### 1.2 Keywords That Define This
- **LAYERED** — content exists at multiple z-depths
- **ORGANIC** — flows naturally, not boxed sections
- **ELEGANT** — sophisticated, masculine, not cute
- **RICH YET CLEAN** — dense with content but breathing room
- **GRAYSCALE WITH COLOR POPS** — muted palette with reveals
- **EDITORIAL** — like a high-end magazine or catalog
- **SCROLLYTELLING** — narrative unfolds as you scroll

### 1.3 What This Is NOT
- NOT a typical "About Me" page with a photo and bio paragraph
- NOT a LinkedIn profile
- NOT a resume/CV format
- NOT discrete sections with headers
- NOT self-aggrandizing or boastful
- NOT generic or template-like
- NOT "live, laugh, love" vibes (minimal handwritten text)
- NOT predominantly blue — browns are PRIMARY accent

---

## PART 2: DESIGN SYSTEM (STRICT)

### 2.1 Typography

**Font Stack (from theme — NO DEVIATIONS):**
```css
--sans: "Inter", ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
--serif: "Newsreader", ui-serif, Georgia, serif;
--mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
--hand: "Caveat", cursive; /* EXTREMELY SPARSE USE */
```

**Usage Rules:**

| Element Type | Font | Size | Weight | Spacing | Usage |
|-------------|------|------|--------|---------|-------|
| Page title | --serif | clamp(28px, 5vw, 48px) | 400 | -0.01em | Hero name only |
| Section labels | --mono | 11px | 400 | 0.1em uppercase | "01 ABOUT" style |
| Body text | --serif | 19px | 400 | normal | Bio, descriptions |
| UI text | --sans | 14-16px | 400-500 | normal | Buttons, links |
| Captions | --mono | 11px | 400 | 0.05em | Image captions |
| Annotations | --hand | 18-22px | 400 | normal | MAX 2 USES ON PAGE |

**Handwritten Font Rules:**
- Use **ONLY** for: (1) Hero annotation, (2) One other small annotation
- **NEVER** use for section titles or labels
- Prevents "live, laugh, love" aesthetic
- Always rotated slightly (-2° to -5°)
- Always in `--warm` color (brown), never blue

### 2.2 Color Palette

**Core Colors (from theme):**
```css
/* === BACKGROUNDS === */
--bg: #FDFCFA;           /* Primary - warm white, not pure white */
--bg-alt: #F8F6F3;       /* Secondary panels */
--bgWarm: #F9F7F4;       /* Warm tint areas */
--bgAlt: #F5F3EF;        /* Contrast sections */

/* === BROWNS (PRIMARY ACCENT - use MORE than blue) === */
--warm: #7D6B5D;         /* Primary brown - borders, accents, lived countries */
--warmLight: #B8A99A;    /* Light brown - visited countries, subtle accents */
--warmTint: rgba(125,107,93,0.08);  /* Overlays, tints */
--warmDark: #6B5344;     /* Deep brown - bookshelf, shadows */

/* === BLUE (SECONDARY ACCENT - use SPARINGLY) === */
--blue: #1E5AFF;         /* ONLY for: links, focus rings, interactive feedback */
--blueTint: rgba(30,90,255,0.04);   /* Hover backgrounds */
--blueRing: rgba(30,90,255,0.22);   /* Focus outlines */

/* === TEXT === */
--ink: #0b1220;          /* Primary text - near black */
--muted: rgba(11,18,32,.66);    /* Secondary text */
--muted2: rgba(11,18,32,.46);   /* Tertiary text, labels */

/* === MAP SPECIFIC === */
--map-default: #E8E8E8;        /* Unvisited countries - light gray */
--map-visited: #B8A99A;        /* Visited - light tan (warmLight) */
--map-lived: #7D6B5D;          /* Lived - dark brown (warm) */
--map-current: #C9553D;        /* Current location - terracotta (NOT blue) */

/* === GRAYSCALE FOR IMAGES === */
--img-grayscale: grayscale(100%) sepia(10%);  /* Warm grayscale */
--img-color: grayscale(0%) sepia(0%);         /* Full color */
```

**Color Usage Rules:**
- Brown (--warm, --warmLight) should appear **3x more often** than blue
- Blue is **ONLY** for:
  - Clickable links (text underlines)
  - Focus rings on interactive elements
  - Inspiration card hover (because they're links)
- **NEVER** use blue for:
  - Section accents or borders
  - Map highlights
  - Decorative elements
  - Interest hover states (use grayscale→color instead)

### 2.3 Spacing System

**Base: 8px**
```css
--space-1: 8px;
--space-2: 16px;
--space-3: 24px;
--space-4: 32px;
--space-5: 40px;
--space-6: 48px;
--space-8: 64px;
--space-10: 80px;
--space-12: 96px;
--space-15: 120px;
--space-20: 160px;
```

**Layout Widths:**
```css
--max: 1140px;   /* Maximum content width */
--wide: 880px;   /* Wide content (map, grids) */
--prose: 620px;  /* Reading width (bio text) */
```

### 2.4 The Layer Model

The page exists in **4 z-depth layers**:

```
LAYER 3 (z-index: 30-39) — FOREGROUND
├── Floating annotations
├── Navigation elements
├── Tooltips
└── Close buttons

LAYER 2 (z-index: 20-29) — CONTENT
├── Text blocks
├── Cards (books, interests, inspirations)
├── Interactive elements
└── Map

LAYER 1 (z-index: 10-19) — MID-GROUND
├── Atmospheric image strips
├── Photo collage images
├── Window cutouts
└── Decorative panels

LAYER 0 (z-index: 0-9) — BACKGROUND
├── Page background color
├── Full-bleed background images
└── Gradient overlays
```

### 2.5 Parallax Speeds

```css
--parallax-bg: 0.2;      /* Background images - very slow */
--parallax-mid: 0.4;     /* Atmospheric strips */
--parallax-accent: 0.6;  /* Floating elements */
--parallax-content: 1.0; /* Normal scroll */
```

**Parallax Formula:**
```javascript
offset = (elementCenterY - viewportCenterY) * speed * direction;
transform: translateY(${offset}px);
```

---

## PART 3: THE ORGANIC FLOW

### 3.1 The Problem with "Sections"
Traditional web pages have discrete sections:
```
[SECTION A]
────────────
[SECTION B]
────────────
[SECTION C]
```

This feels **choppy** and **web-template-y**. We want:
```
[SECTION A content...]
    [atmospheric image bleeding through]
        [...SECTION B content overlapping image]
            [SECTION C starting while B fades...]
```

### 3.2 The Flow Rhythm

The page follows a **visual rhythm** based on scroll distance:

```
SCROLL POSITION    WHAT'S HAPPENING
─────────────────────────────────────────────────────────
0vh                Hero collage visible, images grayscale
30vh               Collage images transition to color
50vh               First atmospheric strip begins entering
80vh               Strip fully visible, bio text appears above
100vh              Bio text fully visible
130vh              Atmospheric window enters
160vh              Map section begins
200vh              Interests cloud enters
240vh              Inspirations grid
280vh              Stats counters animate
300vh              Connect section
```

### 3.3 Overlap Zones

Content from adjacent "sections" overlaps by **40-80px** creating seamless transitions.

**Each atmospheric image can be:**
- **Full-bleed strip**: 100vw × 200-400px, clipped at angles
- **Window**: Content foreground with cutout revealing image
- **Dual**: Two images side-by-side with overlap
- **Background**: Behind content, parallax movement

---

## PART 4: MASTER USER STORY LIST

See [12-USER-STORIES.md](12-USER-STORIES.md) for complete list with acceptance criteria.

**Summary Counts:**
- Hero Section: 24 user stories
- Organic Flow: 18 user stories
- Atmospheric Images: 32 user stories
- Bio & Bookshelf: 28 user stories
- World Map: 36 user stories
- Interests Cloud: 22 user stories
- Inspirations: 20 user stories
- Stats & Connect: 12 user stories
- Animations: 26 user stories
- Edge Cases: 18 user stories
- Accessibility: 16 user stories
- Admin/Customizer: 22 user stories

**TOTAL: 274 User Stories**

---

## NEXT FILES TO READ

1. **[01-HERO-COLLAGE.md](01-HERO-COLLAGE.md)** — Detailed hero section spec
2. **[07-ANIMATIONS.md](07-ANIMATIONS.md)** — All animation choreography
3. **[05-WORLD-MAP.md](05-WORLD-MAP.md)** — Map interaction details

