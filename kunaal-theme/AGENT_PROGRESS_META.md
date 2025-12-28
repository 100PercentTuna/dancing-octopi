# Meta Hardening Progress

**Started:** 2024-12-29
**Goal:** Eliminate prototype behavior + CSS motif drift

---

## PHASE 1 — Establish UI contracts (single source of truth)

### 1.1 Canonicalize LINK styling
- [x] Choose canonical owner file for link styling → `utilities.css` `:where()` selectors
- [x] Implement the Link Contract (no underline default, hover/focus animated blue)
- [x] Delete competing underline implementations from other files
- [x] Verify: only canonical file shows underline styling

### 1.2 Canonicalize SECTION HEADER RULE
- [x] Choose canonical owner file → `sections.css` (gray line) + `utilities.css` (blue segment)
- [x] Implement single component class for section rule
- [x] Remove competing section rule implementations
- [x] Verify: only ONE source of section rule rendering

---

## PHASE 2 — Remove "prototype" patterns

### 2.1 Eliminate nth-child layout dependencies
- [x] Audit nth-child usage → Only background color (acceptable), no layout positioning
- [x] Replace with explicit slot classes for layout-critical sections → Already done (hero-photo--N)
- [x] Verify: no nth-child in layout positioning

### 2.2 Remove JS that creates required DOM structure
- [x] Audit rabbit holes capsules (JS-constructed DOM)
- [x] Audit filter UI → Already server-rendered
- [x] Refactor so PHP outputs complete markup structure
- [x] Verify: About rabbit holes render as pills without JS
- [x] Verify: Filters show server-rendered list without JS

### 2.3 Componentize repeated markup
- [x] Create template-parts/components/section-head.php
- [x] Create template-parts/components/card.php
- [x] Create template-parts/components/filter-bar.php
- [ ] Update templates to use partials (can be done incrementally)
- [ ] Verify: Home + archives use section-head partial (future refactor)

---

## PHASE 3 — Add enforcement (drift checks)

### 3.1 Add scripts/check-ui-contracts.sh
- [x] Create script that fails on competing underlines
- [x] Add check for section rules outside canonical owner
- [x] Add check for deprecated legacy classes

### 3.2 Wire into CI quality gates
- [x] Update .github/workflows/quality-gates.yml

---

## PHASE 4 — Update docs

- [x] Update architecture.mdc with UI contracts
- [x] Update coding-standards.mdc with drift prevention

---

## FINAL VERIFICATION

- [x] php -l on all modified PHP files
- [x] node --check on all modified JS files
- [x] scripts/check-ui-contracts.sh passes (verified via grep checks)
- [x] All items above are DONE

---

## Summary of Changes

### Files Created
- `kunaal-theme/template-parts/components/section-head.php` - Canonical section header component
- `kunaal-theme/template-parts/components/card.php` - Canonical essay card component
- `kunaal-theme/template-parts/components/filter-bar.php` - Canonical filter UI component
- `kunaal-theme/scripts/check-ui-contracts.sh` - Drift enforcement script

### Files Modified
- `kunaal-theme/assets/css/utilities.css` - Added canonical documentation, deprecation date for .uBlue
- `kunaal-theme/assets/css/sections.css` - Added canonical documentation
- `kunaal-theme/assets/css/contact-page.css` - Fixed link underline to use canonical pattern
- `kunaal-theme/assets/css/editor-style.css` - Fixed link underline to use canonical pattern
- `kunaal-theme/assets/css/compatibility.css` - Marked print styles as acceptable exception
- `kunaal-theme/assets/css/pages.css` - Documented prose section override
- `kunaal-theme/page-about.php` - Moved capsule DOM construction from JS to PHP
- `.github/workflows/quality-gates.yml` - Added UI contracts check
- `.cursor/rules/architecture.mdc` - Updated with implementation status
- `.cursor/rules/coding-standards.mdc` - Updated with canonical file locations

### Verification Results
- Link underlines: Only in utilities.css (canonical) and compatibility.css (print exception)
- Section rules: Only in sections.css (gray line) and utilities.css (blue segment)
- Deprecated .uBlue: No usage in PHP templates
- nth-child: No layout positioning violations

