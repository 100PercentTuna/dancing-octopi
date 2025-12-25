# Changelog

All notable changes to the Kunaal Theme will be documented in this file.

---

## [4.2.0] - December 25, 2025

### Added
- **Chart Block**: Comprehensive chart block with bar (vertical/horizontal), line, and pie chart types
  - User-friendly data entry interface (comma-separated values)
  - Customizable labels, colors, and styling
  - Source and caption support
  - Theme color palette integration

### Fixed
- **PDF Colors**: Colors now preserved in PDF output (not black-and-white)
- **Sidenote Font**: Changed from Garfield Signature to Caveat (Google Fonts) for better rendering
- **Article Titles**: Essay/Jotting titles now use serif font and softer color (not pure black)
- **Regex Bug**: Fixed duplicate `open` attribute bug in PDF accordion expansion

### Enhanced
- **Share Dock**: Added PDF download button to share panel
- **PDF Generation**: Improved accordion handling to prevent duplicate attributes

---

## [4.1.0] - December 25, 2025

### Added
- **Epic 5 Complete**: Theme-level motion primitives (CSS animation utilities)
- **Epic 3 Complete**: All Analysis blocks (11 blocks)
  - Assumptions Register
  - Confidence Meter
  - Scenario Comparison
  - Decision Log + Entry
  - Framework Matrix (2x2/3x3)
  - Causal Loop Diagram
  - Evaluation Rubric + Row
  - Debate (Steelman) + Side
- **Epic 4 Started**: Data visualization blocks
  - Publication Table
  - Flow Chart + Step

### Total Blocks: 42

---

## [4.0.0] - December 25, 2025

### Added
- **Epic 2 Complete**: All Editorial blocks (13 blocks)
  - Magazine Figure
  - Lede Package
  - Timeline + Timeline Item
  - Glossary + Glossary Term
  - Inline Annotation
  - Argument Map
  - What We Know / Don't Know
  - Primary Source Excerpt
  - Context Panel
  - Related Reading + Related Link

### Fixed
- Sidenote font loading (now via PHP inline style)
- Sidenote marker changed from numbers to blue bullet (distinct from footnotes)

---

## [3.9.0] - December 25, 2025

### Added
- Footnote + Footnotes Section blocks
- Parallax Section block
- Scrollytelling + Scrolly Step blocks
- Reveal Wrapper block

### Fixed
- Header z-index (content no longer overlaps)
- Footer spacing (tighter on mobile)

---

## [3.8.0] and earlier

- Initial blocks (Insight, Pullquote, Accordion, Sidenote)
- PDF export with DOMPDF
- Custom post types (Essays, Jottings)
- Homepage and templates

