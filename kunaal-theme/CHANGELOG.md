# Changelog

All notable changes to the Kunaal Theme.

---

## [4.7.0] - 2025-12-25

### 🔧 Critical Bug Fixes

Fixed three fatal error bugs that were causing "There has been a critical error on this website" on WordPress:

#### Fixed
- **functions.php**: Fixed `get_current_screen()->post_type ?? ''` crash when `get_current_screen()` returns null (AJAX, REST API, cron contexts)
- **pdf-generator.php**: Fixed top-level `use Dompdf\Dompdf;` statement causing fatal error when Composer dependencies aren't installed. Moved to fully qualified class names inside function.
- **inc/blocks.php**: Fixed `unregister_block_type('core/pullquote')` fatal error when block isn't registered. Added safety check with `WP_Block_Type_Registry`.

### Documentation
- Updated ERROR-HISTORY.md with detailed bug analysis and resolutions

---

## [4.3.0] - 2025-12-25

### 🎉 Major Release - All Epics Complete

#### Epic 4 - Data Visualization (Complete)
- **Chart Block v2.0**: Comprehensive rewrite with 7 chart types
  - Bar (vertical/horizontal)
  - Stacked Bar
  - Clustered Bar
  - Line Chart (multi-series support)
  - Pie Chart
  - Donut Chart
  - Waterfall (build-up/build-down)
- User-friendly sidebar controls for all data entry
- Multiple color schemes (theme, blue, warm, green, mono, rainbow)
- Value formatting with units (prefix/suffix)
- Legend and grid line toggles
- Responsive SVG rendering

#### Epic 8 - Cross-Browser QA
- Added comprehensive `BROWSER-QA.md` documentation
- Testing matrix for Chrome, Firefox, Safari, Edge
- Mobile device testing (iOS Safari, Android Chrome/Firefox)
- Responsive breakpoint verification
- Accessibility testing checklist
- Performance benchmarks

#### Epic 9 - Cleanup/Refactor
- Removed empty chart-* directories
- Expanded deprecated patterns list (40+ patterns now unregistered)
- Cleaned up duplicate pattern registrations
- Optimized block registration

#### Epic 10 - Documentation
- Added comprehensive `THEME-GUIDE.md`
- Block library documentation (45+ blocks)
- Chart block usage guide
- Motion/animation reference
- Color palette documentation
- File structure overview

### Fixed
- PDF colors now preserved (not black-and-white)
- Sidenote font loads correctly (Caveat from Google Fonts)
- Essay/Jotting titles now serif with softer color
- Regex bug for duplicate 'open' attributes in PDF fixed

---

## [4.2.0] - 2025-12-25

### Added
- Basic Chart Block (bar, line, pie)
- Share dock PDF download button
- README.md and initial CHANGELOG.md

### Fixed
- PDF color preservation
- Sidenote font loading (changed to Caveat)
- Article title styling (serif, softer color)
- Regex bug in PDF accordion handling

---

## [4.1.0] - 2025-12-25

### Epic 3 - Analysis Blocks (Complete)
- Assumptions Register
- Confidence Meter
- Scenario Comparison
- Decision Log + Entry
- Framework Matrix (2x2/3x3)
- Causal Loop Diagram
- Evaluation Rubric + Row
- Debate (Steelman vs Steelman)

### Epic 5 - Motion Primitives (Complete)
- CSS animation classes
- Stagger utilities
- Scroll reveal classes
- `prefers-reduced-motion` support

### Epic 4 - Data Blocks (Started)
- Publication Table
- Flow Chart + Step

---

## [4.0.0] - 2025-12-25

### Epic 2 - Editorial Blocks (Complete)
- 13 new editorial blocks
- Magazine Figure, Lede Package, Timeline
- Glossary, Annotation, Argument Map
- Know/Don't Know, Source Excerpt
- Context Panel, Related Reading

### Fixed
- Sidenote font loading
- Blue bullet marker for sidenotes

---

## [3.9.0] - 2025-12-25

### Added
- Footnote and Footnotes Section blocks
- Parallax Section block
- Scrollytelling + Scrolly Step blocks
- Reveal Wrapper block

### Fixed
- Homepage header z-index
- Footer mobile spacing

---

## [3.8.0] - 2025-12-25

### Epic 1 - Block Conversion (Complete)
- Converted all HTML patterns to proper Gutenberg blocks
- Insight Box, Pull Quote, Accordion, Sidenote
- Section Header, Takeaways, Citation, Aside

### Fixed
- Accordion design (restored elegant two-line style)
- Pullquote styling (single blue border)
- Block editor controls for all blocks

---

## [3.7.0] - 2025-12-25

### Added
- DOMPDF integration for PDF export
- Custom PDF layout (journal-paper style)
- Proper filename format

### Fixed
- Corrupted backdrop-filter CSS
- h3 uppercase styling removed

---

## Earlier Versions

See repository history for changes prior to v3.7.0.
