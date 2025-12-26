# User Stories Index - Advanced Blocks

> **Version:** 2.0.0  
> **Total User Stories:** 68  
> **Organized by:** Epic → Feature → Story

---

## Epic 1: Advanced Chart Visualizations

### Feature: Small Multiples (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-SM-01 | Basic Creation - Create small multiples chart from data | P1 |
| US-SM-02 | Layout Customization - Control grid layout | P1 |
| US-SM-03 | Chart Type Selection - Choose different chart types | P1 |
| US-SM-04 | Accessibility - Screen reader support with data table | P1 |

### Feature: Slopegraph (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-SG-01 | Basic Slopegraph - Create chart comparing two time periods | P1 |
| US-SG-02 | Formatting Values - Format as currency, percentages, etc. | P2 |
| US-SG-03 | Highlighting Key Rows - Custom color highlights | P2 |
| US-SG-04 | Mobile Experience - Readable stacked layout on mobile | P1 |

### Feature: Dumbbell Chart (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-DB-01 | Create Pay Gap Chart - Show gaps between two values | P1 |
| US-DB-02 | Format Currency Values - Compact notation support | P2 |
| US-DB-03 | Direction-Based Colors - Positive/negative coloring | P2 |
| US-DB-04 | Mobile Usability - Card format on small screens | P1 |

### Feature: Heatmap (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-HM-01 | Create Activity Heatmap - Show patterns across grid | P1 |
| US-HM-02 | Paste Spreadsheet Data - Import from Excel/Sheets | P1 |
| US-HM-03 | Custom Color Scale - Match brand colors | P2 |
| US-HM-04 | Diverging Values - Positive/negative with colors | P2 |

### Feature: Box Plot & Violin (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-BV-01 | Create Box Plot - Visualize distribution from data | P1 |
| US-BV-02 | Violin Plot Distribution - Show full distribution shape | P2 |
| US-BV-03 | Precomputed Statistics - Enter summary stats directly | P2 |
| US-BV-04 | Compare Groups - Side-by-side distributions | P1 |

---

## Epic 2: Flow & Network Diagrams

### Feature: Sankey & Alluvial (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-FL-01 | Create Energy Sankey - Show flow between nodes | P1 |
| US-FL-02 | Alluvial Voting Changes - Parallel column flow | P2 |
| US-FL-03 | Custom Node Colors - Assign specific colors | P2 |
| US-FL-04 | Value Formatting - Units and currency support | P2 |

### Feature: Network Graph (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-NG-01 | Create Social Network - Visualize connections | P1 |
| US-NG-02 | Group by Category - Color nodes by category | P2 |
| US-NG-03 | Interactive Exploration - Zoom, pan, select | P1 |
| US-NG-04 | Accessible Alternative - List fallback for screen readers | P1 |

---

## Epic 3: Map Visualizations

### Feature: Data Maps (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-MP-01 | Create Choropleth Map - Values by country/region | P1 |
| US-MP-02 | Proportional Symbol Map - Dots sized by value | P1 |
| US-MP-03 | Gradient Color Dots - Temperature-style coloring | P2 |
| US-MP-04 | Pick Location on Map - Click to add points | P2 |

---

## Epic 4: Theme Features

### Feature: Dark Mode (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-DM-01 | Follow System Preference - Auto-detect OS setting | P1 |
| US-DM-02 | Manual Toggle - Override with button | P1 |
| US-DM-03 | Chart Readability - Appropriate colors in dark mode | P1 |
| US-DM-04 | Map Tiles - Dark-styled map tiles | P2 |

### Feature: Color Picker (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-CP-01 | Select Theme Color - Quick selection from palette | P1 |
| US-CP-02 | Custom Color Input - Hex code entry | P1 |
| US-CP-03 | Gradient for Data - Create color scale | P1 |
| US-CP-04 | Contrast Warning - Accessibility check | P2 |

### Feature: Customization System (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-CU-01 | Apply Preset Style - Quick styling | P1 |
| US-CU-02 | Save Custom Preset - Reuse settings | P2 |
| US-CU-03 | Site-Wide Defaults - Customizer integration | P2 |
| US-CU-04 | Copy Settings Between Blocks - Consistency | P3 |

---

## Epic 5: Editorial Features

### Feature: Footnotes & Endnotes (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-FN-01 | Add Footnote While Writing - Seamless workflow | P1 |
| US-FN-02 | Preview Footnote on Hover - Tooltip preview | P1 |
| US-FN-03 | Navigate to/from Footnotes - Click to jump | P1 |
| US-FN-04 | Automatic Renumbering - Dynamic numbering | P1 |

---

## Epic 6: Performance

### Feature: Performance Optimization (4 stories)
| ID | Story | Priority |
|----|-------|----------|
| US-PF-01 | Fast Initial Load - LCP < 2.5s | P1 |
| US-PF-02 | Lazy Load Charts - Load on scroll | P1 |
| US-PF-03 | Optimized Images - Responsive delivery | P1 |
| US-PF-04 | Smooth Animations - 60fps performance | P2 |

---

## Summary by Priority

| Priority | Count | Description |
|----------|-------|-------------|
| P1 (Critical) | 36 | Must have for MVP |
| P2 (High) | 26 | Important for complete experience |
| P3 (Medium) | 6 | Nice to have |

---

## Implementation Phases

### Phase 1: Foundation (Week 1-2)
- US-DM-01, US-DM-02, US-DM-03 — Dark mode core
- US-CP-01, US-CP-02, US-CP-03 — Color picker
- US-PF-01, US-PF-02 — Lazy loading
- US-FN-01, US-FN-02, US-FN-03, US-FN-04 — Footnotes

### Phase 2: Basic Advanced Charts (Week 3-4)
- US-HM-01, US-HM-02 — Heatmap core
- US-DB-01, US-DB-04 — Dumbbell core
- US-SG-01, US-SG-04 — Slopegraph core
- US-SM-01, US-SM-02, US-SM-03, US-SM-04 — Small multiples

### Phase 3: Statistical Charts (Week 5)
- US-BV-01, US-BV-04 — Box plot core
- US-BV-02 — Violin plot
- US-BV-03 — Precomputed stats

### Phase 4: Flow & Network (Week 6-7)
- US-FL-01, US-FL-02 — Sankey/Alluvial
- US-NG-01, US-NG-03, US-NG-04 — Network graph

### Phase 5: Maps (Week 8)
- US-MP-01, US-MP-02 — Choropleth and dots
- US-MP-03 — Gradient dots
- US-DM-04 — Dark map tiles

### Phase 6: Polish (Week 9)
- US-CU-01, US-CU-02 — Presets
- US-PF-03, US-PF-04 — Image optimization, animation polish
- All P2/P3 remaining items

---

## Acceptance Criteria Quick Reference

### All Chart Blocks Must Have
- [ ] Data entry via table UI (no JSON)
- [ ] Paste from spreadsheet support
- [ ] Responsive layout (mobile, tablet, desktop)
- [ ] Dark mode support
- [ ] Accessible data table alternative
- [ ] Keyboard navigation where applicable
- [ ] Animation on scroll into view
- [ ] Reduced motion support
- [ ] Source note field
- [ ] Legend (optional)

### All Interactive Blocks Must Have
- [ ] Focus ring (2px blue)
- [ ] Keyboard operability
- [ ] ARIA labels and roles
- [ ] Touch-friendly targets (44px minimum)
- [ ] Hover/focus states

### All Blocks Must Have
- [ ] Works in editor (visual preview)
- [ ] Works on frontend (all browsers)
- [ ] Documentation
- [ ] Customizer defaults support
- [ ] Preset support (where applicable)

---

## Definition of Done

A user story is considered DONE when:

1. **Code Complete**
   - All acceptance criteria met
   - No linting errors
   - No console errors
   - Code reviewed

2. **Tested**
   - Works in Chrome, Firefox, Safari, Edge
   - Works on mobile (iOS Safari, Chrome Android)
   - Accessibility audit passed (aXe, WAVE)
   - Performance within targets

3. **Documented**
   - Block has README.md
   - Customizer options documented
   - Changelog entry written

4. **Approved**
   - Demo to stakeholder
   - Any feedback addressed


