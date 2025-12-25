# Theme Development Roadmap

**Last Updated**: Version 4.1.0 - December 25, 2025

---

## ✅ COMPLETED

### Epic 0 — QA Baseline ✅
- ✅ Full regression QA
- ✅ Testing checklist (`TESTING.md`)

### Epic 1 — Convert Patterns to Blocks ✅
- ✅ All patterns converted to proper Gutenberg blocks

### Epic 2 — Editorial Blocks ✅ COMPLETE
- ✅ 13 blocks: Magazine Figure, Lede Package, Timeline, Glossary, Annotation, Argument Map, Know/Don't Know, Source Excerpt, Context Panel, Related Reading, etc.

### Epic 3 — Analysis Blocks ✅ COMPLETE
- ✅ Assumptions Register - Track assumptions with confidence levels
- ✅ Confidence Meter - Visual confidence indicator
- ✅ Scenario Comparison - Compare multiple scenarios
- ✅ Decision Log + Entry - Track decisions with rationale
- ✅ Framework Matrix - 2x2/3x3 strategic analysis
- ✅ Causal Loop Diagram - Systems thinking visualization
- ✅ Evaluation Rubric + Row - Criteria scoring table
- ✅ Debate (Steelman) + Side - Dual-perspective arguments

### Epic 5 — Parallax + Scrollytelling ✅ COMPLETE
- ✅ Parallax Section block
- ✅ Scrollytelling + Scrolly Step blocks
- ✅ Reveal Wrapper block
- ✅ Theme-level motion primitives (CSS utilities)

### Epic 7 — PDF Export ✅
- ✅ DOMPDF integration
- ✅ Custom layout and styling

---

## 🚧 IN PROGRESS

### Epic 4 — Data Visualization Blocks (Started)
**Completed:**
- ✅ Publication Table - Styled data table with source/caption
- ✅ Flow Chart + Step - Process diagram with step types

**Remaining:**
- [ ] Unified Chart Block - Chart type selector with data entry
- [ ] Chart Types: Bar, line, pie, stacked, waterfall, bubble
- [ ] Advanced: Heatmap, sankey, network graph
- [ ] Map visualizations

---

## 🔮 REMAINING WORK

### Epic 4 — Data Visualization (Remaining)
- [ ] Chart block with multiple types
- [ ] Data entry interface
- [ ] Theme palette integration
- [ ] Annotations and sources
- [ ] Advanced chart types

### Epic 6 — Sharing + Subscribe
- [ ] Share Dock with PDF download
- [ ] Subscribe Dock (left-side)

### Epic 8 — Cross-Browser QA
- [ ] Full browser/device matrix testing

### Epic 9 — Cleanup/Refactor
- [ ] Remove duplication
- [ ] Asset optimization

### Epic 10 — Documentation
- [ ] Theme Guide
- [ ] Block documentation

---

## 📊 PROGRESS METRICS

**Blocks Completed**: 42
- Editorial: 22 blocks
- Analysis: 11 blocks
- Interactive: 3 blocks
- Data: 4 blocks (started)

**Epic Completion**:
- Epic 0: ✅ 100%
- Epic 1: ✅ 100%
- Epic 2: ✅ 100%
- Epic 3: ✅ 100% ⭐ NEW
- Epic 4: 🟡 25% (4/15+ blocks)
- Epic 5: ✅ 100% ⭐ NEW
- Epic 6: ⚪ 0%
- Epic 7: ✅ 100%
- Epic 8: 🟡 50%
- Epic 9: ⚪ 0%
- Epic 10: 🟡 30%

**Overall Progress**: ~70% of planned features

---

## 📝 CHANGELOG

### v4.1.0 (December 25, 2025) - Major Release
**Epic 5 Complete + Epic 3 Complete + Epic 4 Started**

**Motion Primitives (Epic 5)**:
- CSS animation classes: `motion-fade-*`, `motion-scale-*`
- Stagger utilities: `stagger-1` through `stagger-8`
- Scroll reveal classes: `scroll-reveal`, `scroll-reveal-*`
- Respects `prefers-reduced-motion`

**Analysis Blocks (Epic 3) - 11 blocks**:
- Assumptions Register - Table with confidence/status tracking
- Confidence Meter - Visual progress bar with colors
- Scenario Comparison - Multi-column scenario cards
- Decision Log + Entry - Timeline of decisions
- Framework Matrix - 2x2/3x3 grid analysis
- Causal Loop Diagram - Systems thinking with +/- effects
- Evaluation Rubric + Row - Scoring criteria table
- Debate + Side - Steelman vs steelman format

**Data Blocks (Epic 4) - 4 blocks**:
- Publication Table - Professional data table
- Flow Chart + Step - Process diagram

**Total Blocks**: 42

### v4.0.0 (December 25, 2025)
- Epic 2 complete (13 editorial blocks)
- Sidenote fix (font loading, blue bullet marker)

### v3.9.0 (December 25, 2025)
- Footnotes, Parallax, Scrollytelling, Reveal blocks
- Homepage bug fixes

---

## 📋 RECOMMENDED NEXT STEPS

### Phase 1: Chart Block (v4.2.0)
- Unified chart block with type selector
- Bar, line, pie chart types
- Data entry interface
- Theme palette integration

### Phase 2: Advanced Data Viz (v4.3.0)
- Stacked/clustered bars
- Waterfall chart
- Heatmap

### Phase 3: Polish (v4.4.0)
- Share/Subscribe docks
- Cross-browser QA
- Documentation

---

**Questions?** Check `TESTING.md` for comprehensive testing checklist.
