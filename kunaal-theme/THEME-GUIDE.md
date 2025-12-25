# Kunaal Theme Guide

**Version**: 4.3.0  
**Last Updated**: December 25, 2025

---

## 🎨 Design Philosophy

This theme embodies a **minimalistic but expensive** aesthetic, inspired by Sotheby's modern art catalogs and sophisticated editorial publications like The New Yorker and The Atlantic.

### Visual Identity
- **Gallery minimalism**: Clean, uncluttered layouts with generous whitespace
- **Typography-first**: Content takes center stage with carefully selected fonts
- **Luxury-muted palette**: Warm backgrounds with blue as the statement accent color
- **Editorial sophistication**: Magazine-quality presentation for written content

### Typography
- **Sans-serif** (Inter): Navigation, UI elements, labels
- **Serif** (Newsreader): Body copy, article titles, headings
- **Monospace**: Code, technical content, sources
- **Handwritten** (Caveat): Sidenotes and marginalia

---

## 📦 Block Library

The theme includes 45+ custom Gutenberg blocks organized into categories:

### Editorial Blocks (`kunaal-editorial`)
| Block | Description | Key Features |
|-------|-------------|--------------|
| **Insight Box** | Highlighted insight callout | Warm background, custom label |
| **Pull Quote** | Blue-accented quote | Single border, citation support |
| **Accordion** | Expandable section | Smooth animation, elegant marker |
| **Sidenote** | Marginal annotation | Caveat font, blue bullet marker |
| **Section Header** | Numbered section heading | Auto-numbering option |
| **Magazine Figure** | Editorial image | Multiple sizes, captions |
| **Lede Package** | Article opener | Hero image, subtitle, byline |
| **Timeline** | Chronological events | Vertical line, markers |
| **Glossary** | Term definitions | Alphabetical organization |
| **Annotation** | Inline highlight | Tooltip notes |
| **Argument Map** | Visual argument structure | Claims, evidence, warrants |
| **Know/Don't Know** | Knowledge summary | Two-column layout |
| **Source Excerpt** | Document quotes | Citation styling |
| **Context Panel** | Background info | Collapsible, labeled |
| **Related Reading** | Link collection | Titles, descriptions |
| **Footnote** | Reference marker | Auto-numbered, linked |
| **Footnotes Section** | End notes | Bidirectional links |
| **Takeaways** | Key points | Large accent numbers |
| **Citation** | Formal citation | Centered, elegant |
| **Aside** | Case study/sidebar | Label, outcome highlight |

### Analysis Blocks (`kunaal-analysis`)
| Block | Description | Key Features |
|-------|-------------|--------------|
| **Assumptions Register** | Track assumptions | Confidence, status indicators |
| **Confidence Meter** | Visual confidence | Progress bar, colors |
| **Scenario Comparison** | Multi-scenario | Side-by-side cards |
| **Decision Log** | Decision timeline | Date, rationale, outcome |
| **Framework Matrix** | 2x2/3x3 analysis | Custom labels, cell content |
| **Causal Loop** | Systems diagram | Variables, +/- connections |
| **Rubric** | Evaluation table | Criteria, scoring |
| **Debate** | Steelman arguments | Two perspectives |

### Data Blocks (`kunaal-data`)
| Block | Description | Key Features |
|-------|-------------|--------------|
| **Chart** | Data visualization | Bar, line, pie, donut, stacked, clustered, waterfall |
| **Publication Table** | Styled table | Headers, source, caption |
| **Flowchart** | Process diagram | Steps, connectors |

### Interactive Blocks (`kunaal-interactive`)
| Block | Description | Key Features |
|-------|-------------|--------------|
| **Parallax Section** | Parallax background | Full-width, overlay content |
| **Scrollytelling** | Pinned scene | Steps, dynamic content |
| **Reveal Wrapper** | Scroll animation | Fade, slide, scale effects |

---

## 📊 Using the Chart Block

The Chart block supports multiple visualization types:

### Chart Types
1. **Bar Chart** - Vertical or horizontal
2. **Stacked Bar** - Multiple series stacked
3. **Clustered Bar** - Multiple series side-by-side
4. **Line Chart** - Trend lines with optional multiple series
5. **Pie Chart** - Proportional distribution
6. **Donut Chart** - Pie with center cutout
7. **Waterfall** - Cumulative changes (financial analysis)

### Configuration
- **Data**: Enter comma-separated values (e.g., `10, 20, 30, 40`)
- **Labels**: Enter comma-separated labels (e.g., `Q1, Q2, Q3, Q4`)
- **Multiple Series**: Use Data Series 2 and 3 for stacked/clustered/line
- **Colors**: Choose from theme, blue, warm, green, mono, rainbow
- **Units**: Add prefix (`$`) or suffix (`%`, `K`, `M`)

### Example: Creating a Waterfall Chart
1. Select "Waterfall" chart type
2. Enter starting value (e.g., `100`)
3. Enter changes: `30, -15, 20, -10` (positive = increase, negative = decrease)
4. Enter labels: `Start, Revenue, Costs, Growth, Losses, Total`
5. Add source and caption

---

## 🎭 Motion & Animation

### Scroll Reveal
Add the `.reveal` class to any element for scroll-triggered fade-in:
```html
<div class="reveal">Content appears on scroll</div>
```

### Motion Primitives (CSS Classes)
```css
.motion-fade-in      /* Fade in */
.motion-fade-up      /* Fade in from below */
.motion-fade-down    /* Fade in from above */
.motion-scale-in     /* Scale up from small */
.stagger-1 to .stagger-8  /* Stagger delay */
.scroll-reveal       /* JS-triggered reveal */
```

### Accessibility
All animations respect `prefers-reduced-motion`. Motion-sensitive users see instant reveals without animation.

---

## 📄 PDF Export

The theme includes DOMPDF integration for professional PDF export.

### Features
- Clean, journal-paper layout
- Proper page headers/footers
- Color preservation
- Expandable accordions auto-open
- Custom filename: `"Title - by Author.pdf"`

### Requirements
- Composer installed
- Run `composer install` in theme directory

### Fallback
If DOMPDF is not available, the download button triggers browser print with a helpful notice.

---

## 🎨 Color Palette

### CSS Variables
```css
--bg: #FDFCFA;        /* Page background */
--bgWarm: #F9F7F4;    /* Warm section background */
--ink: #0b1220;       /* Primary text */
--muted: rgba(11,18,32,.66);  /* Secondary text */
--blue: #1E5AFF;      /* Accent color */
--warm: #7D6B5D;      /* Warm accent */
```

### Chart Colors
```css
--chart-blue: #1E5AFF;
--chart-warm: #7D6B5D;
--chart-green: #2ECC71;
--chart-accent: #FF6B6B;
--chart-gray: #95A5A6;
```

---

## 📱 Responsive Behavior

### Breakpoints
- **Desktop Wide**: 1440px+ (full sidenotes in margin)
- **Desktop**: 1024px-1439px (narrower margins)
- **Tablet**: 768px-1023px (sidenotes inline)
- **Mobile**: <768px (single column, stacked footer)

### Sidenotes
- Desktop: Positioned in right margin
- Mobile: Displayed inline with blue bullet indicator

---

## 🔧 Customization

### Theme Customizer Options
- Author name and avatar
- Social links (Twitter, LinkedIn)
- Footer content
- Subscribe form integration

### Block Editor
All blocks have Inspector Controls (sidebar) for:
- Content editing
- Style options
- Display toggles
- Color overrides

---

## 📁 File Structure

```
kunaal-theme/
├── blocks/              # Custom Gutenberg blocks
│   ├── accordion/
│   ├── chart/
│   ├── insight/
│   └── ... (45+ blocks)
├── assets/
│   ├── css/
│   │   ├── editor-style.css
│   │   ├── pdf-ebook.css
│   │   └── print.css
│   └── js/
│       └── main.js
├── inc/
│   └── blocks.php       # Block registration
├── templates/
│   ├── single-essay.php
│   └── single-jotting.php
├── functions.php        # Theme setup
├── style.css           # Main stylesheet
├── theme.json          # Block editor config
└── pdf-generator.php   # DOMPDF integration
```

---

## 🚀 Getting Started

### Installation
1. Upload theme to `/wp-content/themes/`
2. Activate via Appearance > Themes
3. Run `composer install` for PDF support

### Creating Content
1. Add new Essay or Jotting post
2. Use block inserter to add custom blocks
3. Configure blocks via Inspector Controls
4. Preview and publish

### Best Practices
- Use Section Header blocks to organize long essays
- Add Sidenotes for supplementary information
- Include at least one image (Magazine Figure)
- End with Takeaways block for key points
- Add Related Reading for further exploration

---

## 📞 Support

For issues or feature requests, check:
- `TESTING.md` - Testing checklist
- `BROWSER-QA.md` - Cross-browser compatibility
- `ROADMAP.md` - Development roadmap
- `CHANGELOG.md` - Version history

