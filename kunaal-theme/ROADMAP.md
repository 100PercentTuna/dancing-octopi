# Theme Development Roadmap

**Last Updated**: Version 4.0.0 - December 25, 2025

---

## ✅ COMPLETED

### Epic 0 — QA Baseline
- ✅ Full regression QA of existing features
- ✅ Testing checklist created (`TESTING.md`)

### Epic 1 — Convert Patterns to Blocks
- ✅ Insight Box → `kunaal/insight` block
- ✅ Pull Quote → `kunaal/pullquote` block  
- ✅ Accordion → `kunaal/accordion` block
- ✅ Sidenote → `kunaal/sidenote` block (redesigned)
- ✅ Section Header → `kunaal/section-header` block
- ✅ Takeaways → `kunaal/takeaways` block (redesigned)
- ✅ Citation → `kunaal/citation` block (redesigned)
- ✅ Aside → `kunaal/aside` block (redesigned)
- ✅ Pattern cleanup (unregistered deprecated patterns)

### Epic 2 — Editorial Blocks ✅ COMPLETE
- ✅ Footnotes/Endnotes → `kunaal/footnote` + `kunaal/footnotes-section`
- ✅ Sidenotes/Marginalia → `kunaal/sidenote` (blue bullet, margin position)
- ✅ Magazine Figure System → `kunaal/magazine-figure`
- ✅ Lede Package → `kunaal/lede-package`
- ✅ Argument Map → `kunaal/argument-map`
- ✅ Inline Annotation → `kunaal/annotation`
- ✅ Timeline/Chronology → `kunaal/timeline` + `kunaal/timeline-item`
- ✅ Glossary/Concepts → `kunaal/glossary` + `kunaal/glossary-term`
- ✅ "What We Know / What We Don't" → `kunaal/know-dont-know`
- ✅ Primary Source Excerpt → `kunaal/source-excerpt`
- ✅ Context Panel → `kunaal/context-panel`
- ✅ Related Reading → `kunaal/related-reading` + `kunaal/related-link`

### Epic 5 — Parallax + Scrollytelling (Partial)
- ✅ Parallax Section → `kunaal/parallax-section`
- ✅ Scrollytelling/Stepper → `kunaal/scrollytelling` + `kunaal/scrolly-step`
- ✅ Reveal Animation Wrapper → `kunaal/reveal-wrapper`

### Epic 7 — PDF Export
- ✅ Reader-native PDF layout
- ✅ Custom filename format
- ✅ Header/footer requirements
- ✅ PDF reliability

### Bug Fixes (v3.9.0 - v4.0.0)
- ✅ Header z-index (content no longer overlaps)
- ✅ Footer spacing (tighter on mobile)
- ✅ Sidenote font loading (now via PHP inline style)
- ✅ Sidenote marker (blue bullet instead of number)

---

## 🚧 REMAINING WORK

### Epic 3 — Analysis Blocks
- [ ] **Assumptions Register** - List of assumptions with confidence levels
- [ ] **Uncertainty/Confidence** - Visual confidence indicators
- [ ] **Scenario/Sensitivity** - Multiple scenario comparison
- [ ] **Decision Log** - Decision tracking with rationale
- [ ] **Framework (2x2/3x3)** - Matrix visualization blocks
- [ ] **Causal Loop/Systems Map** - Systems thinking diagrams
- [ ] **Rubric** - Evaluation criteria table
- [ ] **Debate (Steelman vs Steelman)** - Dual-perspective argument structure

### Epic 4 — Data Visualization Blocks
- [ ] **Unified Chart Block** - Single block with chart type selector
  - Type selector (bar, line, pie, etc.)
  - Data entry interface
  - Theme palette integration
  - Annotations support
  - Sources/citations
  - Responsive behavior
- [ ] **Chart Types**:
  - Stacked bars, Clustered bars
  - Build-up/down waterfall, Variwide
  - Bubble chart, Small multiples
  - Slopegraph, Dumbbell, Heatmap
  - Box/violin, Ridgeline
  - Sankey/alluvial, Chord, Network graph
  - Map visualizations
- [ ] **Publication Table** - Styled data table
- [ ] **Flow Chart + Chevrons** - Process diagrams

### Epic 5 — Parallax + Scrollytelling (Remaining)
- [ ] Theme-level motion primitives - Reusable animation utilities

### Epic 6 — Sharing + Subscribe Polish
- [ ] Share Dock - Enhanced share panel with PDF download icon
- [ ] Subscribe Dock - Left-side subscription element

### Epic 8 — Cross-Browser & Responsive Quality
- [ ] Full QA across browser/device matrix
- [ ] Fix any discovered issues

### Epic 9 — Cleanup/Refactor
- [ ] Remove duplication
- [ ] Tighten architecture
- [ ] Load assets only when used
- [ ] Optimize performance

### Epic 10 — Documentation Discipline
- [ ] Theme Guide - Comprehensive block documentation
- [ ] README - Updated with all blocks
- [ ] Changelog - Maintained for each version

---

## 📊 PROGRESS METRICS

**Blocks Completed**: 28
- Editorial: 22 blocks ✅
- Interactive: 3 blocks
- Data: 0 blocks (not started)
- Analysis: 0 blocks (not started)

**Epic Completion**:
- Epic 0: ✅ 100%
- Epic 1: ✅ 100%
- Epic 2: ✅ 100% (Complete!)
- Epic 3: ⚪ 0% (0/8 blocks)
- Epic 4: ⚪ 0% (0/15+ blocks)
- Epic 5: 🟡 75% (4/5 features)
- Epic 6: ⚪ 0% (0/2 features)
- Epic 7: ✅ 100%
- Epic 8: 🟡 50%
- Epic 9: ⚪ 0%
- Epic 10: 🟡 30%

**Overall Progress**: ~55% of planned features

---

## 📋 RECOMMENDED NEXT STEPS

### Phase 1: Analysis Blocks (v4.1.0)
**Priority**: Medium - For analytical essays

1. **Framework (2x2 Matrix)** - Most common analysis tool
2. **Assumptions Register** - Useful for critical thinking
3. **Decision Log** - For process documentation
4. **Debate (Steelman)** - For balanced arguments

**Estimated Time**: 1-2 weeks

### Phase 2: Data Visualization (v4.2.0+)
**Priority**: High - Complex but powerful

1. **Unified Chart Block** - Foundation for all charts
2. **Publication Table** - Simpler than charts
3. **Flow Chart + Chevrons** - Process diagrams

**Estimated Time**: 2-3 weeks

### Phase 3: Polish & Documentation (v4.3.0)
**Priority**: High - Professional finish

1. **Share/Subscribe Docks** - User experience
2. **Cross-Browser QA** - Quality assurance
3. **Theme Guide** - Documentation
4. **Performance Optimization** - Cleanup/refactor

**Estimated Time**: 1-2 weeks

---

## 📝 CHANGELOG

### v4.0.0 (December 25, 2025) - Major Release
**Epic 2 Complete: All Editorial Blocks**

**New Blocks (13)**:
- Magazine Figure - Image + caption + credit
- Lede Package - Hero opening with multiple layouts
- Timeline + Timeline Item - Chronological events
- Glossary + Glossary Term - Term definitions
- Inline Annotation - Highlighted text with tooltip
- Argument Map - Claims + evidence + counter-arguments
- What We Know / Don't Know - Two-column certainty
- Primary Source Excerpt - Document styling
- Context Panel - Contextual information box
- Related Reading + Related Link - Further reading section

**Fixes**:
- Sidenote font now loads via PHP inline style (was broken)
- Sidenote marker changed from numbers to blue bullet (•)
- Distinct from footnotes for clarity

**Total Blocks**: 28

### v3.9.0 (December 25, 2025)
- Footnote + Footnotes Section blocks
- Parallax Section block
- Scrollytelling + Scrolly Step blocks
- Reveal Wrapper block
- Redesigned: Sidenote, Takeaways, Citation, Aside
- Header z-index fix
- Footer spacing fix on mobile

### v3.8.0 and earlier
- Initial blocks (Insight, Pullquote, Accordion, Sidenote)
- PDF export with DOMPDF
- Custom post types (Essays, Jottings)
- Homepage and templates

---

**Questions?** Check `TESTING.md` for comprehensive testing checklist.
