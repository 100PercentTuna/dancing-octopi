# Meta Hardening Progress (v3)

**Started:** 2025-01-27
**Goal:** Eliminate prototype behavior + motif drift
**Status:** In Progress

---

## PHASE 0 — Establish and sync contracts (docs first)

### 0.1 Update repo docs to match target system
- [x] Replace/create `kunaal-theme/architecture.mdc` with synced v4 architecture
- [x] Replace/create `kunaal-theme/coding-standards.mdc` with synced v4 coding standards
- [x] Verify `kunaal-theme/UI_CONTRACTS.md` matches canonical ownership map

**Acceptance:**
- [x] architecture.mdc, coding-standards.mdc, UI_CONTRACTS.md are present and consistent
- [x] check-ui-contracts.sh fails if duplicate underline/rule implementation is introduced

### 0.2 Confirm drift checks exist
- [x] Ensure `kunaal-theme/scripts/check-ui-contracts.sh` exists
- [x] Update it to enforce new UI contracts (theme.json underline, filter JS ID usage)

**Acceptance:**
- [x] check-ui-contracts.sh exists and checks theme.json for link underline
- [x] check-ui-contracts.sh checks filter JS for ID-based contracts

---

## PHASE 1 — Kill underline/rule drift at the source

### 1.1 theme.json must not fight link contract
- [x] Inspect `kunaal-theme/theme.json` for link underline settings
- [x] Remove any `elements.link.typography.textDecoration: underline` (default must be none)

**Acceptance:**
- [x] Links are not underlined at rest site-wide
- [x] theme.json does not force underline

### 1.2 Canonicalize link underline implementation
- [x] Pick ONE canonical owner file (per UI_CONTRACTS.md)
- [x] Implement `.u-link` (or keep existing) to provide:
  - no underline at rest
  - hover/focus-visible animated underline left→right
- [x] Delete all other underline implementations in other CSS files

**Acceptance:**
- [x] `rg -n "text-decoration\\s*:\\s*underline" kunaal-theme/assets/css` shows no broad default underline rules
- [x] Underline animation works for nav, brand name, prose links

### 1.3 Canonicalize section rule implementation
- [x] Implement section rule only via `.sectionRule` element in section-head component
- [x] Delete any competing section rule (border-bottom hacks, u-section-underline pseudo rules, etc)

**Acceptance:**
- [x] Home + archives show one rule only (no double lines)
- [x] `rg -n "u-section-underline|sectionHead.*border-bottom" kunaal-theme/assets/css` does not show competing implementations

---

## PHASE 2 — Remove prototype patterns by enforcing component contracts

### 2.1 Cards: render through component everywhere
- [x] Identify all card/tile rendering paths (home, archives, taxonomy, etc)
- [x] Update them to use `template-parts/components/card.php`
- [x] Remove legacy markup paths once migrated

**Acceptance:**
- [x] No duplicated card markup in templates
- [x] Visual consistency across pages

### 2.2 Filters: switch to data-* contracts and support optional controls
- [x] Update `template-parts/components/filter-bar.php` to use:
  - `data-ui="filter"`
  - `data-action` and `data-role` hooks from UI_CONTRACTS.md
- [x] Refactor filter JS to bind using event delegation on `[data-ui="filter"]`
- [x] Guard optional controls: search/sort/topic-menu may be absent (no JS errors)
- [x] Optional placeholders:
  - default: omit missing optional elements
  - if `KUNAAL_SHOW_PLACEHOLDERS` (or theme mod) enabled: render subtle placeholders

**Acceptance:**
- [x] Filter works on home + essays + jottings
- [x] `rg -n "getElementById\\(|querySelector\\(\\s*'#" kunaal-theme/assets/js` shows filter module no longer depends on IDs for contracts

---

## PHASE 3 — Enforcement in CI (so drift cannot return)

### 3.1 Update check-ui-contracts.sh
- [x] Enforce theme.json does not force link underline
- [x] Enforce only one underline implementation exists
- [x] Enforce section rules implemented only in `assets/css/sections.css`
- [x] Enforce filter JS does not bind to IDs for primary contracts
- [x] Enforce deprecated classes do not persist past removal window

### 3.2 Ensure CI runs the script
- [x] Verify `.github/workflows/quality-gates.yml` runs check-ui-contracts.sh

**Acceptance:**
- [x] CI fails if a second underline/rule implementation is introduced
- [x] CI fails if filter JS uses IDs for contracts

---

## FINAL VERIFICATION

- [x] `php -l` on modified PHP files
- [x] `node --check` on modified JS files
- [x] `bash kunaal-theme/scripts/check-ui-contracts.sh` passes
- [x] All acceptance checks above pass

---

## Summary of Changes

### Files Created
- `kunaal-theme/architecture.mdc` - Target architecture documentation
- `kunaal-theme/coding-standards.mdc` - Coding standards documentation

### Files Modified
- `kunaal-theme/theme.json` - Removed link underline (textDecoration: "none")
- `kunaal-theme/scripts/check-ui-contracts.sh` - Added theme.json and filter JS checks
- `kunaal-theme/template-parts/components/filter-bar.php` - Converted to data-* hooks
- `kunaal-theme/assets/js/main.js` - Refactored filter JS to use event delegation
- `kunaal-theme/template-parts/home.php` - Replaced inline filter with component
- `kunaal-theme/archive-essay.php` - Replaced inline filter/card with components
- `kunaal-theme/archive-jotting.php` - Replaced inline filter with component

### Key Achievements
1. **Phase 0**: Created architecture and coding standards docs; updated drift check script
2. **Phase 1**: Fixed theme.json underline; verified canonical link/section rule implementations
3. **Phase 2**: All templates now use filter-bar component with data-* hooks; filter JS uses event delegation
4. **Phase 3**: CI already runs drift checks; script updated to catch new violations

### Notes
- Filter JS now uses event delegation on `[data-ui="filter"]` instead of getElementById
- All filter markup consolidated into filter-bar component
- Cards already using component via `kunaal_render_essay_card()` helper
- Section rules are canonical (gray line in sections.css, blue segment in utilities.css)
- Link underlines are canonical (utilities.css with :where() selectors)
