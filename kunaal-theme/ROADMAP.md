# Theme Development Roadmap

**Last Updated**: Version 3.9.0 - December 25, 2025

---

## ✅ COMPLETED (v3.9.0)

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

### Epic 2 — Editorial Blocks (Partial)
- ✅ Footnotes/Endnotes → `kunaal/footnote` + `kunaal/footnotes-section` blocks
- ✅ Sidenotes/Marginalia → `kunaal/sidenote` block (complete redesign)
- ✅ Case Study → `kunaal/aside` block (with label selector)
- ✅ Structured Takeaways → `kunaal/takeaways` block (redesigned)

### Epic 5 — Parallax + Scrollytelling (Partial)
- ✅ Parallax Section → `kunaal/parallax-section` block
- ✅ Scrollytelling/Stepper → `kunaal/scrollytelling` + `kunaal/scrolly-step` blocks
- ✅ Reveal Animation Wrapper → `kunaal/reveal-wrapper` block

### Epic 7 — PDF Export
- ✅ Reader-native PDF layout
- ✅ Custom filename format
- ✅ Header/footer requirements
- ✅ PDF reliability

### Bug Fixes
- ✅ Header z-index (content no longer overlaps)
- ✅ Footer spacing (tighter on mobile)

---

## 🚧 IN PROGRESS / NEXT PRIORITY

### Epic 2 — More Editorial Blocks (Remaining)
- [ ] **Magazine Figure System** - Image with caption, credit, optional full-width
- [ ] **Lede Package** - Opening image + headline + dek combination
- [ ] **Argument Map** - Visual argument structure (pros/cons, evidence)
- [ ] **Inline Annotation/Highlight + Note** - Text highlighting with popup notes
- [ ] **Timeline/Chronology** - Vertical or horizontal timeline
- [ ] **Glossary/Concepts** - Expandable term definitions
- [ ] **"What We Know / What We Don't"** - Two-column knowledge status
- [ ] **Primary Source/Document Excerpt** - Styled document quotes
- [ ] **Context Panel** - Sidebar context information
- [ ] **Related Reading** - Curated links section

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
  - [ ] Type selector (bar, line, pie, etc.)
  - [ ] Data entry interface
  - [ ] Theme palette integration
  - [ ] Annotations support
  - [ ] Sources/citations
  - [ ] Responsive behavior
- [ ] **Must-Have Chart Types**:
  - [ ] Stacked bars
  - [ ] Clustered bars
  - [ ] Build-up/down waterfall
  - [ ] Variwide
  - [ ] Bubble chart
- [ ] **Advanced Chart Types**:
  - [ ] Small multiples
  - [ ] Slopegraph
  - [ ] Dumbbell
  - [ ] Heatmap
  - [ ] Box/violin plots
  - [ ] Ridgeline
  - [ ] Sankey/alluvial
  - [ ] Chord diagram
  - [ ] Network graph
  - [ ] Map visualizations
- [ ] **Publication Table** - Styled data table
- [ ] **Flow Chart + Chevrons** - Process diagrams
- [ ] **Advanced Visualization Embed** - External viz integration
- [ ] **Interactive/Static Dual-Mode** - Toggle between modes

### Epic 5 — Parallax + Scrollytelling (Remaining)
- [ ] **Parallax Split Panel** - Side-by-side parallax (can use parallax-section)
- [ ] **Theme-level Motion Primitives** - Reusable animation utilities

### Epic 6 — Sharing + Subscribe Polish
- [ ] **Share Dock** - Enhanced share panel with PDF download icon
- [ ] **Subscribe Dock** - Left-side subscription element

### Epic 8 — Cross-Browser & Responsive Quality
- [ ] Full QA across browser/device matrix
- [ ] Fix any discovered issues

### Epic 9 — Cleanup/Refactor
- [ ] Remove duplication
- [ ] Tighten architecture
- [ ] Load assets only when used
- [ ] Optimize performance

### Epic 10 — Documentation Discipline
- [ ] **Theme Guide** - Comprehensive block documentation
- [ ] **README** - Updated with all blocks
- [ ] **Changelog** - Maintained for each version
- [ ] **Block Documentation** - Individual block docs

---

## 📋 RECOMMENDED NEXT STEPS

### Phase 1: High-Value Editorial Blocks (v3.10.0)
**Priority**: High - Most useful for essay writing

1. **Timeline/Chronology Block** - Very common in essays
2. **Magazine Figure System** - Essential for visual essays
3. **Glossary/Concepts Block** - Useful for technical content
4. **Related Reading Block** - Standard feature

**Estimated Time**: 1-2 weeks

### Phase 2: Analysis Blocks (v3.11.0)
**Priority**: Medium - For analytical essays

1. **Framework (2x2 Matrix)** - Most common analysis tool
2. **Assumptions Register** - Useful for critical thinking
3. **Decision Log** - For process documentation

**Estimated Time**: 1-2 weeks

### Phase 3: Data Visualization (v3.12.0+)
**Priority**: Medium-High - Complex but powerful

1. **Unified Chart Block** - Foundation for all charts
2. **Must-Have Chart Types** - Start with most common
3. **Publication Table** - Simpler than charts

**Estimated Time**: 2-3 weeks

### Phase 4: Polish & Documentation (v3.13.0)
**Priority**: High - Professional finish

1. **Share/Subscribe Docks** - User experience
2. **Cross-Browser QA** - Quality assurance
3. **Theme Guide** - Documentation
4. **Performance Optimization** - Cleanup/refactor

**Estimated Time**: 1-2 weeks

---

## 🎯 IMMEDIATE NEXT (v3.10.0)

Based on user needs and original brief, the next logical step is:

### **Timeline/Chronology Block**
- Very common in essays
- Relatively straightforward to implement
- High visual impact
- Can be vertical or horizontal

### **Magazine Figure System**
- Essential for visual storytelling
- Image + caption + credit + optional full-width
- Foundation for other image blocks

### **Glossary/Concepts Block**
- Useful for technical essays
- Expandable definitions
- Can link from inline text

---

## 📊 PROGRESS METRICS

**Blocks Completed**: 15 / ~50+ (estimated)
- Editorial: 9 blocks
- Interactive: 3 blocks
- Data: 0 blocks (not started)
- Analysis: 0 blocks (not started)

**Epic Completion**:
- Epic 0: ✅ 100%
- Epic 1: ✅ 100%
- Epic 2: 🟡 30% (4/13 blocks)
- Epic 3: ⚪ 0% (0/8 blocks)
- Epic 4: ⚪ 0% (0/15+ blocks)
- Epic 5: 🟡 60% (3/5 features)
- Epic 6: ⚪ 0% (0/2 features)
- Epic 7: ✅ 100%
- Epic 8: 🟡 50% (testing framework ready)
- Epic 9: ⚪ 0% (not started)
- Epic 10: 🟡 20% (testing docs, needs theme guide)

**Overall Progress**: ~35% of planned features

---

## 🔄 WORKFLOW NOTES

- **Testing**: Every release includes `TESTING.md` checklist
- **Versioning**: Semantic versioning (MAJOR.MINOR.PATCH)
- **Documentation**: Update README and changelog with each release
- **Git**: Commit with descriptive messages, push to trigger auto-deploy

---

**Questions?** Review the original brief or check `TESTING.md` for current release details.

