# Kunaal Theme

A sophisticated WordPress theme for [kunaalwadhwa.com](https://kunaalwadhwa.com) — featuring magazine-quality editorial design with enterprise-grade Gutenberg authoring.

**Version**: 4.5.0  
**Requires WordPress**: 6.0+  
**Requires PHP**: 8.0+

---

## ✨ Features

### Design Philosophy
- **Minimalistic but expensive** — Sotheby's catalog meets sophisticated editorial
- **Typography-first** — Inter, Newsreader, and Caveat fonts
- **Gallery minimalism** — Clean layouts with generous whitespace
- **Blue accent** — Single statement color for focus and interaction

### Content Types
- **Essays** — Long-form articles with full editorial blocks
- **Jottings** — Quick thoughts and observations

### 45+ Custom Gutenberg Blocks
- **Editorial**: Insight Box, Pull Quote, Accordion, Sidenote, Timeline, Glossary, Footnotes...
- **Analysis**: Assumptions Register, Confidence Meter, Framework Matrix, Decision Log, Debate...
- **Data**: Chart Block (bar, line, pie, donut, stacked, clustered, waterfall), Publication Table, Flowchart
- **Interactive**: Parallax Section, Scrollytelling, Reveal Wrapper

### ✏️ Inline Text Formats (NEW in v4.5.0)
Rich text formats for sophisticated essay writing:
- **Sidenote** — Blue bullet marker, margin note on hover
- **Highlight** — Warm yellow highlight with optional annotation
- **Definition** — Dotted underline, shows definition tooltip
- **Key Term** — Subtle blue emphasis for important concepts
- **Data Reference** — Style statistics with source citation

### Chart Block v2.0
Comprehensive data visualization with:
- 7 chart types (bar, stacked, clustered, line, pie, donut, waterfall)
- Multiple color schemes
- User-friendly sidebar controls
- Responsive SVG rendering

### Motion & Animation
- Scroll-reveal animations
- Parallax backgrounds
- Scrollytelling/stepper sections
- Respects `prefers-reduced-motion`

### PDF Export
- DOMPDF integration for professional PDF generation
- Custom journal-paper layout
- Color preservation
- Automatic filename: `"Title - by Author.pdf"`

---

## 🚀 Installation

### Basic Installation
1. Download the theme
2. Upload to `/wp-content/themes/`
3. Activate via Appearance > Themes

### PDF Support (Optional)
```bash
cd wp-content/themes/kunaal-theme
composer install
```

---

## 📖 Documentation

| File | Description |
|------|-------------|
| `THEME-GUIDE.md` | Comprehensive usage guide |
| `CHANGELOG.md` | Version history |
| `TESTING.md` | Testing checklist |
| `BROWSER-QA.md` | Cross-browser compatibility |
| `ROADMAP.md` | Development roadmap |

---

## 🎨 Design System

### Colors
```css
--bg: #FDFCFA;        /* Background */
--ink: #0b1220;       /* Primary text */
--blue: #1E5AFF;      /* Accent */
--warm: #7D6B5D;      /* Warm accent */
```

### Typography
- **Sans**: Inter (UI, navigation)
- **Serif**: Newsreader (body, headings)
- **Mono**: System monospace (code, sources)
- **Handwritten**: Caveat (sidenotes)

### Spacing
8px-based scale: `--space-1` (8px) through `--space-20` (160px)

---

## 📦 Block Library Summary

### Editorial (22 blocks)
Insight Box, Pull Quote, Accordion, Sidenote, Section Header, Magazine Figure, Lede Package, Timeline, Glossary, Annotation, Argument Map, Know/Don't Know, Source Excerpt, Context Panel, Related Reading, Footnote, Footnotes Section, Takeaways, Citation, Aside

### Analysis (11 blocks)
Assumptions Register, Confidence Meter, Scenario Comparison, Decision Log, Decision Entry, Framework Matrix, Causal Loop, Rubric, Rubric Row, Debate, Debate Side

### Data (5 blocks)
Chart (7 types), Publication Table, Flowchart, Flowchart Step

### Interactive (3 blocks)
Parallax Section, Scrollytelling, Scrolly Step, Reveal Wrapper

---

## 🔧 Development

### Requirements
- WordPress 6.0+
- PHP 8.0+
- Composer (for PDF support)

### File Structure
```
kunaal-theme/
├── blocks/           # 45+ custom Gutenberg blocks
├── assets/          
│   ├── css/         # Stylesheets
│   └── js/          # Scripts
├── inc/             # PHP includes
├── templates/       # Page templates
├── functions.php    # Theme setup
├── style.css        # Main stylesheet
└── theme.json       # Block editor config
```

### No Build Step Required
Block editor scripts use WordPress globals (`window.wp.*`), eliminating the need for npm/webpack.

---

## 📄 License

GPL-2.0-or-later

---

## 🙏 Credits

- Design inspiration: Sotheby's, The New Yorker, The Atlantic
- Fonts: Google Fonts (Inter, Newsreader, Caveat)
- PDF: DOMPDF library
