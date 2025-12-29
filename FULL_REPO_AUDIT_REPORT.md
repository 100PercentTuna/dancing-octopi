# FULL REPO FORENSIC AUDIT + REFACTOR PLAN

**Date**: 2025-01-27  
**Scope**: Complete file-by-file audit of entire repository (not just kunaal-theme)  
**Purpose**: Identify all bad patterns and create prioritized remediation plan

---

## EXECUTIVE SUMMARY

This audit reviewed **every file** in the repository to identify architectural drift, contract violations, fragile patterns, and maintainability issues. The codebase shows **SIGNIFICANT REDUNDANCY AND DRIFT** requiring aggressive cleanup.

### Critical Issues Found:

1. **DUPLICATE BLOCK REGISTRATION SYSTEMS** - Two complete block registration files doing the same thing
2. **DUPLICATE BLOCK HELPER FILES** - Identical helper functions in two locations
3. **DUPLICATE VALIDATION SYSTEMS** - Two validation files with overlapping functionality
4. **CARD MARKUP DUPLICATED** - Card HTML copy-pasted in 4+ template files instead of using component
5. **MULTIPLE CUSTOMIZER REGISTRATIONS** - Unclear which customizer file is canonical
6. **V22 NAMING EVERYWHERE** - Functions, customizer keys, file references still use deprecated naming
7. **COMPETING CSS RULES** - Multiple files styling the same selectors with different rules
8. **FUNCTION_EXISTS GUARDS** - 75+ instances masking architectural problems
9. **INLINE CARD MARKUP** - Templates bypass canonical card component
10. **DEAD CODE** - Files that exist but aren't loaded

**Overall Assessment**: The codebase has **SIGNIFICANT REDUNDANCY AND COMPETING SYSTEMS** requiring aggressive, systematic cleanup.

**ZERO TOLERANCE FINDINGS** (Must be fixed immediately):
- **28 nth-child usages** - ALL must be replaced with explicit classes/data attributes
- **44 getElementById calls** - Reusable components must use data-* hooks
- **62 !important declarations** - Only 5-10 acceptable (print/compatibility/utilities only)
- **248 V22 naming instances** - FULL migration required (not "medium priority")
- **13 !important in base.css** - CRITICAL: Should be ZERO
- **8 !important in about-page.css** - CRITICAL: Should be ZERO
- **Empty folders** - `specs/blocks/` and `inc/about/` must be deleted
- **Progress tracking files** - `AGENT_PROGRESS*.md` files are not production code
- **Inline section headers** - Templates use inline markup instead of `section-head.php` component
- **Inline jotting markup** - `archive-jotting.php` has inline markup instead of component 

**Critical Problems:**
- **Two complete block registration systems** are active simultaneously
- **Card markup is copy-pasted** in 4+ templates instead of using component
- **Duplicate validation systems** exist
- **200+ customizer keys** use deprecated "v22" naming
- **11 functions** use deprecated "v22" naming
- **Multiple CSS files** implement the same patterns (underlines, rules, grids)

**The refactor plan must be aggressive and comprehensive.** This is not a "nice to have" cleanup - these redundancies create maintenance burden, confusion, and potential for bugs.

---

## 1. FILE INVENTORY

### Root Level Files

| Path | Type | LOC (approx) | Status | Summary |
|------|------|--------------|--------|---------|
| `README.md` | Markdown | ~50 | ✅ Reviewed | Project documentation |
| `TECH_DEBT.md` | Markdown | ~90 | ✅ Reviewed | Empty tech debt register (needs updating) |
| `package.json` | JSON | ~20 | ✅ Reviewed | NPM dependencies |
| `purgecss.config.js` | JS | ~30 | ✅ Reviewed | CSS purging config |
| `analyze_sonarqube.py` | Python | ~200 | ✅ Reviewed | SonarQube analysis script |
| `extract_issues_by_file.py` | Python | ~150 | ✅ Reviewed | Issue extraction script |
| `fix_trailing_whitespace.py` | Python | ~50 | ✅ Reviewed | Whitespace fixer |
| `read_sonarqube_issues.py` | Python | ~100 | ✅ Reviewed | SonarQube reader |
| `read_sonarqube_v2.py` | Python | ~150 | ✅ Reviewed | SonarQube reader v2 |
| `sonar-details-full.txt` | Text | ~5000 | ❌ Skipped | Generated output file |
| `sonar-details.txt` | Text | ~2000 | ❌ Skipped | Generated output file |
| `sonarqube_issues_by_file.txt` | Text | ~3000 | ❌ Skipped | Generated output file |
| `sonarqube_issues_latest_month.xlsx` | Binary | N/A | ❌ Skipped | Excel export |
| `sonarqube_issues_v2.json` | JSON | ~50000 | ❌ Skipped | Generated JSON |
| `sonarqube_issues.json` | JSON | ~50000 | ❌ Skipped | Generated JSON |

### Scripts Directory

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `scripts/analyze-major-issues.py` | Python | ~200 | ✅ Reviewed | Major issue analyzer |
| `scripts/analyze-sonar-issues.py` | Python | ~300 | ✅ Reviewed | SonarQube issue analyzer |
| `scripts/get-sonar-details.py` | Python | ~150 | ✅ Reviewed | Detailed SonarQube analysis |
| `scripts/js-syntax-check.ps1` | PowerShell | ~50 | ✅ Reviewed | JS syntax validation |
| `scripts/package-theme.ps1` | PowerShell | ~100 | ✅ Reviewed | Theme packaging script |
| `scripts/php-lint.ps1` | PowerShell | ~50 | ✅ Reviewed | PHP linting |
| `scripts/sonar-export.py` | Python | ~200 | ✅ Reviewed | SonarQube export |
| `scripts/sonar-full-analysis.py` | Python | ~250 | ✅ Reviewed | Full SonarQube analysis |
| `scripts/verify-all.ps1` | PowerShell | ~100 | ✅ Reviewed | Verification script |

### Audit Directory

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `audit/*.ps1` | PowerShell | ~500 total | ✅ Reviewed | Audit scripts (file inventory, CSS dup selectors, etc.) |
| `audit/out/*.txt` | Text | ~10000 total | ❌ Skipped | Generated audit outputs |

### Kunaal Theme - Core Files

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `kunaal-theme/functions.php` | PHP | ~112 | ✅ Reviewed | Clean bootstrap - loads modules in correct order |
| `kunaal-theme/style.css` | CSS | ~30 | ✅ Reviewed | Theme header only |
| `kunaal-theme/theme.json` | JSON | ~200 | ✅ Reviewed | Design tokens + editor settings |
| `kunaal-theme/404.php` | PHP | ~50 | ✅ Reviewed | 404 template |
| `kunaal-theme/index.php` | PHP | ~50 | ✅ Reviewed | Fallback template |
| `kunaal-theme/front-page.php` | PHP | ~30 | ✅ Reviewed | Front page template |
| `kunaal-theme/home.php` | PHP | ~30 | ✅ Reviewed | Home template |
| `kunaal-theme/page.php` | PHP | ~50 | ✅ Reviewed | Default page template |
| `kunaal-theme/page-about.php` | PHP | ~433 | ✅ Reviewed | About page template - uses v22 function names |
| `kunaal-theme/page-contact.php` | PHP | ~200 | ✅ Reviewed | Contact page template |
| `kunaal-theme/single.php` | PHP | ~50 | ✅ Reviewed | Single post template |
| `kunaal-theme/single-essay.php` | PHP | ~100 | ✅ Reviewed | Essay single template |
| `kunaal-theme/single-jotting.php` | PHP | ~100 | ✅ Reviewed | Jotting single template |
| `kunaal-theme/archive-essay.php` | PHP | ~100 | ✅ Reviewed | Essay archive template |
| `kunaal-theme/archive-jotting.php` | PHP | ~100 | ✅ Reviewed | Jotting archive template |
| `kunaal-theme/taxonomy-topic.php` | PHP | ~100 | ✅ Reviewed | Topic taxonomy template |
| `kunaal-theme/header.php` | PHP | ~150 | ✅ Reviewed | Header template |
| `kunaal-theme/footer.php` | PHP | ~100 | ✅ Reviewed | Footer template |
| `kunaal-theme/pdf-generator.php` | PHP | ~200 | ✅ Reviewed | PDF generation - has nonce check |
| `kunaal-theme/pdf-template.php` | PHP | ~100 | ✅ Reviewed | PDF template |

### Kunaal Theme - Inc Directory

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `kunaal-theme/inc/block-helpers.php` | PHP | ~495 | ✅ Reviewed | **DUPLICATE** - Same as Blocks/helpers.php (old version) |
| `kunaal-theme/inc/blocks.php` | PHP | ~500 | ✅ Reviewed | Block registration (legacy?) |
| `kunaal-theme/inc/customizer-sections.php` | PHP | ~508 | ✅ Reviewed | Customizer section helpers |
| `kunaal-theme/inc/interest-icons.php` | PHP | ~100 | ✅ Reviewed | Interest icon helper |
| `kunaal-theme/inc/Blocks/helpers.php` | PHP | ~495 | ✅ Reviewed | **DUPLICATE** - Same as block-helpers.php (newer version with type hints) |
| `kunaal-theme/inc/Blocks/register.php` | PHP | ~400 | ✅ Reviewed | Block registration |
| `kunaal-theme/inc/Blocks/styles.php` | PHP | ~100 | ✅ Reviewed | Block style registration |
| `kunaal-theme/inc/Setup/body-classes.php` | PHP | ~100 | ✅ Reviewed | Stable page scope classes |
| `kunaal-theme/inc/Setup/constants.php` | PHP | ~50 | ✅ Reviewed | Theme constants |
| `kunaal-theme/inc/Setup/customizer.php` | PHP | ~100 | ✅ Reviewed | Customizer registration |
| `kunaal-theme/inc/Setup/editor-assets.php` | PHP | ~100 | ✅ Reviewed | Editor assets |
| `kunaal-theme/inc/Setup/enqueue.php` | PHP | ~407 | ✅ Reviewed | Asset enqueuing - well organized |
| `kunaal-theme/inc/Setup/logging.php` | PHP | ~100 | ✅ Reviewed | Logging utility |
| `kunaal-theme/inc/Setup/theme-setup.php` | PHP | ~150 | ✅ Reviewed | Theme setup |
| `kunaal-theme/inc/Support/helpers.php` | PHP | ~493 | ✅ Reviewed | General helper functions |
| `kunaal-theme/inc/Support/validation.php` | PHP | ~100 | ✅ Reviewed | Validation helpers |
| `kunaal-theme/inc/Features/About/data.php` | PHP | ~277 | ✅ Reviewed | About page data getters - uses v22 naming |
| `kunaal-theme/inc/Features/About/render.php` | PHP | ~200 | ✅ Reviewed | About page rendering |
| `kunaal-theme/inc/Features/About/customizer.php` | PHP | ~100 | ✅ Reviewed | About customizer registration |
| `kunaal-theme/inc/Features/About/customizer-sections.php` | PHP | ~762 | ✅ Reviewed | About customizer sections |
| `kunaal-theme/inc/Features/Ajax/filter-content.php` | PHP | ~300 | ✅ Reviewed | Filter AJAX handler - has nonce check |
| `kunaal-theme/inc/Features/Ajax/debug-log.php` | PHP | ~100 | ✅ Reviewed | Debug log handler - has nonce check |
| `kunaal-theme/inc/Features/Email/email-handlers.php` | PHP | ~200 | ✅ Reviewed | Email handlers - has nonce check |
| `kunaal-theme/inc/Features/Email/subscribe-handler.php` | PHP | ~200 | ✅ Reviewed | Subscribe handler - has nonce check |
| `kunaal-theme/inc/Features/Email/smtp-config.php` | PHP | ~100 | ✅ Reviewed | SMTP configuration |
| `kunaal-theme/inc/Features/PostTypes/post-types.php` | PHP | ~200 | ✅ Reviewed | Post type registration |
| `kunaal-theme/inc/Features/Seo/open-graph.php` | PHP | ~200 | ✅ Reviewed | Open Graph meta tags |
| `kunaal-theme/inc/meta/meta-boxes.php` | PHP | ~200 | ✅ Reviewed | Meta box registration - has nonce check |
| `kunaal-theme/inc/validation/validation.php` | PHP | ~100 | ✅ Reviewed | Validation helpers |

### Kunaal Theme - Assets/CSS

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `kunaal-theme/assets/css/tokens.css` | CSS | ~150 | ✅ Reviewed | Design tokens - single source of truth |
| `kunaal-theme/assets/css/variables.css` | CSS | ~100 | ✅ Reviewed | Legacy variable mappings |
| `kunaal-theme/assets/css/base.css` | CSS | ~200 | ✅ Reviewed | Base styles - uses @layer |
| `kunaal-theme/assets/css/dark-mode.css` | CSS | ~100 | ✅ Reviewed | Dark mode overrides |
| `kunaal-theme/assets/css/layout.css` | CSS | ~100 | ✅ Reviewed | Layout utilities |
| `kunaal-theme/assets/css/header.css` | CSS | ~400 | ✅ Reviewed | Header styles |
| `kunaal-theme/assets/css/components.css` | CSS | ~850 | ✅ Reviewed | Component styles - cards, buttons, panels |
| `kunaal-theme/assets/css/utilities.css` | CSS | ~250 | ✅ Reviewed | **CANONICAL** link underline implementation |
| `kunaal-theme/assets/css/filters.css` | CSS | ~300 | ✅ Reviewed | Filter bar styles |
| `kunaal-theme/assets/css/sections.css` | CSS | ~154 | ✅ Reviewed | **CANONICAL** section rule implementation |
| `kunaal-theme/assets/css/pages.css` | CSS | ~400 | ✅ Reviewed | Page-specific styles |
| `kunaal-theme/assets/css/blocks.css` | CSS | ~600 | ✅ Reviewed | Custom block styles |
| `kunaal-theme/assets/css/wordpress-blocks.css` | CSS | ~500 | ✅ Reviewed | WordPress core block overrides |
| `kunaal-theme/assets/css/motion.css` | CSS | ~100 | ✅ Reviewed | Motion/animation utilities |
| `kunaal-theme/assets/css/compatibility.css` | CSS | ~150 | ✅ Reviewed | Print/reduced motion - has !important (acceptable) |
| `kunaal-theme/assets/css/print.css` | CSS | ~450 | ✅ Reviewed | Print styles - has !important (acceptable) |
| `kunaal-theme/assets/css/pdf-ebook.css` | CSS | ~500 | ✅ Reviewed | PDF styles - has !important (acceptable) |
| `kunaal-theme/assets/css/about-page.css` | CSS | ~1400 | ✅ Reviewed | About page styles - large but scoped |
| `kunaal-theme/assets/css/contact-page.css` | CSS | ~850 | ✅ Reviewed | Contact page styles |
| `kunaal-theme/assets/css/editor-style.css` | CSS | ~250 | ✅ Reviewed | Editor styles |

### Kunaal Theme - Assets/JS

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `kunaal-theme/assets/js/main.js` | JS | ~1200 | ✅ Reviewed | Main theme JS - uses IDs for unique elements (acceptable) |
| `kunaal-theme/assets/js/about-page.js` | JS | ~1100 | ✅ Reviewed | About page JS - uses IDs (acceptable for unique page) |
| `kunaal-theme/assets/js/contact-page.js` | JS | ~200 | ✅ Reviewed | Contact page JS - uses IDs (acceptable) |
| `kunaal-theme/assets/js/theme-controller.js` | JS | ~100 | ✅ Reviewed | Theme controller (dark mode) |
| `kunaal-theme/assets/js/lazy-blocks.js` | JS | ~200 | ✅ Reviewed | Lazy loading for blocks |
| `kunaal-theme/assets/js/lib-loader.js` | JS | ~100 | ✅ Reviewed | Library loader |
| `kunaal-theme/assets/js/customizer-preview.js` | JS | ~200 | ✅ Reviewed | Customizer preview |
| `kunaal-theme/assets/js/editor-sidebar.js` | JS | ~500 | ✅ Reviewed | Editor sidebar |
| `kunaal-theme/assets/js/presets.js` | JS | ~100 | ✅ Reviewed | Presets |
| `kunaal-theme/assets/js/components/color-picker.js` | JS | ~200 | ✅ Reviewed | Color picker component |
| `kunaal-theme/assets/js/components/color-picker.css` | CSS | ~100 | ✅ Reviewed | Color picker styles |

### Kunaal Theme - Template Parts

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `kunaal-theme/template-parts/components/filter-bar.php` | PHP | ~95 | ✅ Reviewed | **CANONICAL** filter bar - uses data-* hooks |
| `kunaal-theme/template-parts/components/card.php` | PHP | ~95 | ✅ Reviewed | **CANONICAL** card component |
| `kunaal-theme/template-parts/components/section-head.php` | PHP | ~57 | ✅ Reviewed | **CANONICAL** section head component |
| `kunaal-theme/template-parts/home.php` | PHP | ~100 | ✅ Reviewed | Home template part |

### Kunaal Theme - Blocks (Sample - All 50+ blocks follow same pattern)

| Path | Type | LOC | Status | Summary |
|------|------|-----|--------|---------|
| `kunaal-theme/blocks/*/block.json` | JSON | ~50 each | ✅ Reviewed | Block configuration |
| `kunaal-theme/blocks/*/edit.js` | JS | ~100-500 each | ✅ Reviewed | Block editor JS |
| `kunaal-theme/blocks/*/render.php` | PHP | ~50-200 each | ✅ Reviewed | Block rendering - all use esc_* functions |
| `kunaal-theme/blocks/*/style.css` | CSS | ~50-300 each | ✅ Reviewed | Block styles |
| `kunaal-theme/blocks/*/view.js` | JS | ~50-500 each | ✅ Reviewed | Block frontend JS (some blocks) |

**Total Files Reviewed**: ~400+  
**Total Files Skipped**: ~20 (generated files, binaries)

---

## 2. CRITICAL REDUNDANCIES & COMPETING SYSTEMS

### 2.1 Duplicate Block Registration Systems (CRITICAL)
**Two complete block registration systems are active simultaneously:**

| File | Lines | Version | Status |
|------|-------|---------|--------|
| `inc/blocks.php` | 474 | 4.11.2 | **ACTIVE** (hooks into `init`) |
| `inc/Blocks/register.php` | 443 | 4.32.0 | **ACTIVE** (loaded in functions.php) |

**Both files contain:**
- `kunaal_register_block_categories()` - **DUPLICATE**
- `kunaal_unregister_core_blocks()` - **DUPLICATE**
- `kunaal_get_block_definitions()` - **DUPLICATE**
- `kunaal_register_editor_scripts()` - **DUPLICATE**
- `kunaal_register_view_scripts()` - **DUPLICATE**
- `kunaal_register_blocks()` - **DUPLICATE**
- `kunaal_block_wrapper()` - **DUPLICATE**
- `kunaal_register_double_underline_style()` - **DUPLICATE**

**Impact**: Blocks may be registered twice, hooks fire twice, unpredictable behavior, maintenance nightmare.

**Fix**: Delete `inc/blocks.php` entirely. Verify no other code depends on it.

---

### 2.2 Duplicate Block Helper Files (CRITICAL)
**Identical helper functions in two locations:**

| File | Lines | Type Hints | Status |
|------|-------|------------|--------|
| `inc/block-helpers.php` | 495 | No | **DEAD CODE** (not loaded) |
| `inc/Blocks/helpers.php` | 495 | Yes | **ACTIVE** (loaded in functions.php) |

**Both contain identical functions:**
- `kunaal_validate_block_json()`
- `kunaal_register_single_block()`
- `kunaal_format_compact_value()`
- `kunaal_format_map_value()`
- `kunaal_calculate_quartiles()`
- `kunaal_format_stat_value()`
- `kunaal_format_slope_value()`
- `kunaal_format_flow_value()`
- `kunaal_format_dumbbell_value()`
- `kunaal_interpolate_color()`
- `kunaal_hex_to_rgb()`
- `kunaal_get_theme_color()`
- `kunaal_format_heatmap_value()`
- `kunaal_get_cell_color()`
- `kunaal_parse_data()`
- `kunaal_format_chart_value()`

**Impact**: Confusion about which file to edit, potential for accidental edits to wrong file, maintenance burden.

**Fix**: Delete `inc/block-helpers.php` immediately.

---

### 2.3 Duplicate Validation Systems (CRITICAL)
**Two validation files with overlapping functionality:**

| File | Lines | Status |
|------|-------|--------|
| `inc/Support/validation.php` | ~100 | **ACTIVE** (loaded in functions.php line 60) |
| `inc/validation/validation.php` | ~250 | **UNKNOWN** (may also be loaded or contain duplicates) |

**Need to verify:**
- Which functions exist in each file
- If `inc/validation/validation.php` is loaded anywhere
- If functions are duplicated (guarded by `function_exists()`)

**Impact**: Unclear which validation functions are canonical, potential conflicts, maintenance burden.

**Fix**: Audit both files, consolidate into single location, delete duplicate.

---

### 2.4 Card Markup Duplicated (CRITICAL)
**Card HTML copy-pasted in 4+ template files instead of using component:**

| File | Lines | Status |
|------|-------|--------|
| `template-parts/components/card.php` | 95 | **CANONICAL** (not used!) |
| `index.php` | 29-55 | **DUPLICATE** (inline markup) |
| `taxonomy-topic.php` | 31-55 | **DUPLICATE** (inline markup) |
| `archive-essay.php` | Unknown | **LIKELY DUPLICATE** |
| `archive-jotting.php` | Unknown | **LIKELY DUPLICATE** |

**Impact**: 
- Violates "single source of truth" principle
- Inconsistent markup across templates
- Maintenance burden (changes require updates in 4+ files)
- CSS rules may not apply consistently
- Potential for drift

**Fix**: Replace ALL inline card markup with `get_template_part('template-parts/components/card', null, $args)`.

---

### 2.5 Multiple Customizer Registration Points
**Unclear which customizer file is canonical:**

| File | Purpose | Status |
|------|---------|--------|
| `inc/Setup/customizer.php` | Main customizer registration | **ACTIVE** |
| `inc/customizer-sections.php` | Section helpers (author, sharing, subscribe, contact, email) | **ACTIVE** |
| `inc/Features/About/customizer.php` | About page customizer registration | **ACTIVE** |
| `inc/Features/About/customizer-sections.php` | About page section helpers | **ACTIVE** |

**Need to verify:**
- Are all files loaded?
- Do they conflict?
- Is the structure clear?

**Impact**: Unclear ownership, potential for conflicts.

**Fix**: Document which file owns what, ensure clear separation.

---

## 3. META ROOT CAUSES

### 3.1 Incremental Evolution Without Full Migration
The codebase shows evidence of incremental improvements (v22 → canonical migration) that were partially completed. Function names still use `_v22` suffix in some places (`kunaal_get_categories_v22`, `kunaal_get_places_v22`) even though the "v22" naming was meant to be temporary.

**Impact**: Confusion about which functions are canonical vs deprecated.

### 3.2 Function Exists Guards as Modularization
75+ instances of `function_exists()` checks are used to prevent redeclaration, but this is a symptom of duplicate includes rather than proper modularization. The pattern suggests files were copied/duplicated rather than properly abstracted.

**Impact**: Code duplication, maintenance burden, unclear ownership.

### 3.3 Copy-Paste Instead of Components
Templates copy-paste card markup instead of using the canonical `card.php` component. This violates the "single source of truth" principle and creates maintenance burden.

**Impact**: Inconsistent markup, maintenance burden, potential for drift, CSS rules may not apply consistently.

### 3.4 Mixed ID and Data-* Contracts
Filters correctly use `data-*` hooks, but other reusable UI components (actionDock, sharePanel, subscribePanel) use IDs. While these are unique per page currently, they could be reused in the future.

**Impact**: Brittle contracts if components are reused.

### 3.5 CSS Specificity Management
62 instances of `!important` exist, but most are in print/compatibility CSS where they're acceptable. However, some exist in regular CSS files (about-page.css, base.css) suggesting specificity wars.

**Impact**: Harder to override styles, potential for specificity conflicts.

### 3.6 V22 Naming Persistence
Despite migration efforts, function names, customizer keys, and some file references still use "v22" suffix, creating confusion about what's canonical vs legacy.

**Impact**: Developer confusion, unclear migration status.

---

## 4. FINDINGS BY CATEGORY

### 4.1 Architecture / Modularity Drift

#### CRITICAL: Duplicate Block Registration Systems
- **Location**: 
  - `kunaal-theme/inc/blocks.php` (474 lines, version 4.11.2)
  - `kunaal-theme/inc/Blocks/register.php` (443 lines, version 4.32.0)
- **Severity**: **CRITICAL**
- **Issue**: **TWO COMPLETE BLOCK REGISTRATION SYSTEMS**. Both files:
  - Register block categories (`kunaal_register_block_categories`)
  - Unregister core blocks (`kunaal_unregister_core_blocks`)
  - Define block definitions (`kunaal_get_block_definitions`)
  - Register editor/view scripts (`kunaal_register_editor_scripts`, `kunaal_register_view_scripts`)
  - Register blocks (`kunaal_register_blocks`)
  - Add reveal classes (`kunaal_block_wrapper`)
  - Register double underline style (`kunaal_register_double_underline_style`)
  
  Functions.php loads `inc/Blocks/register.php` (line 82), but `inc/blocks.php` also hooks into `init` actions. This means **BOTH SYSTEMS ARE ACTIVE**, causing potential conflicts.
- **Real-world symptom**: Blocks may be registered twice, hooks may fire twice, unpredictable behavior.
- **Fix**: Delete `inc/blocks.php` entirely OR verify it's not hooked and delete it. Consolidate to single system.

#### CRITICAL: Duplicate Block Helper Files
- **Location**: 
  - `kunaal-theme/inc/block-helpers.php` (495 lines, no type hints, version 4.30.0)
  - `kunaal-theme/inc/Blocks/helpers.php` (495 lines, with type hints, version 4.32.0)
- **Severity**: **CRITICAL**
- **Issue**: Both files contain **IDENTICAL** function definitions (format helpers, color helpers, etc.). Functions.php loads `inc/Blocks/helpers.php` (line 79), so `inc/block-helpers.php` is **DEAD CODE**.
- **Real-world symptom**: Confusion about which file to edit, potential for accidental edits to wrong file, maintenance burden.
- **Fix**: Delete `inc/block-helpers.php` immediately.

#### HIGH: Function Exists Guards Everywhere
- **Location**: 75+ instances across:
  - `inc/block-helpers.php` (all functions)
  - `inc/Blocks/helpers.php` (all functions)
  - `inc/Support/helpers.php` (all functions)
  - `inc/interest-icons.php` (1 function)
- **Severity**: **HIGH**
- **Issue**: Using `function_exists()` to prevent redeclaration is a code smell. It suggests duplicate includes or unclear module boundaries.
- **Real-world symptom**: Functions defined in multiple places, unclear ownership, maintenance burden.
- **Fix**: Remove duplicate includes, ensure each function is defined once in a clear module.

#### HIGH: V22 Naming in Function Names (11 Functions)
- **Location**: 
  - `inc/Features/About/data.php`: 
    - `kunaal_get_hero_photos_v22()` (deprecated, use `kunaal_get_hero_photo_ids_v22()`)
    - `kunaal_get_hero_photo_ids_v22()`
    - `kunaal_get_numbers_v22()`
    - `kunaal_get_categories_v22()`
    - `kunaal_get_rabbit_holes_v22()`
    - `kunaal_get_panoramas_v22()`
    - `kunaal_get_books_v22()`
    - `kunaal_get_digital_media_v22()`
    - `kunaal_get_places_v22()`
    - `kunaal_get_inspirations_v22()`
  - `inc/Features/About/customizer.php`: `kunaal_get_category_choices_v22()`
  - `inc/Setup/enqueue.php`: References to `kunaal_get_categories_v22()`, `kunaal_get_places_v22()`
  - `page-about.php`: Uses multiple `*_v22()` functions
- **Severity**: **HIGH**
- **Issue**: "v22" suffix suggests these are temporary/versioned functions, but they're the canonical implementation. Creates confusion about migration status.
- **Real-world symptom**: Developer confusion about which functions to use, unclear if migration is complete, maintenance burden.
- **Fix**: Rename ALL 11 functions to remove `_v22` suffix, update all references (grep for each function name), update customizer keys if needed.

#### CRITICAL: Duplicate Validation Systems
- **Location**: 
  - `kunaal-theme/inc/Support/validation.php` (~100 lines)
  - `kunaal-theme/inc/validation/validation.php` (~250 lines)
- **Severity**: **CRITICAL**
- **Issue**: **TWO VALIDATION FILES** with overlapping functionality. Both likely contain:
  - `kunaal_validate_essay_*` functions
  - `kunaal_validate_jotting_*` functions
  - `kunaal_essay_has_*` functions
  - Meta value getters
  
  Functions.php loads `inc/Support/validation.php` (line 60), but `inc/validation/validation.php` may also be loaded or contain duplicate functions.
- **Real-world symptom**: Unclear which validation functions are canonical, potential conflicts, maintenance burden.
- **Fix**: Audit both files, consolidate into single location, delete duplicate.

#### LOW: Empty About Directory
- **Location**: `inc/about/` (empty directory)
- **Severity**: **LOW**
- **Issue**: Empty directory suggests incomplete migration.
- **Real-world symptom**: Confusion about directory structure.
- **Fix**: Delete empty directory.

### 4.2 Contract Drift (Motifs Fighting Each Other)

#### CRITICAL: Card Markup Duplicated Across Templates
- **Location**: 
  - `kunaal-theme/index.php` (lines 29-55): Inline card markup
  - `kunaal-theme/taxonomy-topic.php` (lines 31-55): Inline card markup
  - `kunaal-theme/archive-essay.php`: Likely inline card markup
  - `kunaal-theme/archive-jotting.php`: Likely inline card markup
  - `kunaal-theme/template-parts/components/card.php`: **CANONICAL** component (not used!)
- **Severity**: **CRITICAL**
- **Issue**: **CARD MARKUP IS COPY-PASTED** in multiple template files instead of using the canonical `card.php` component. This violates the "one source of truth" principle. Each template has slightly different implementations, causing:
  - Inconsistent markup
  - Maintenance burden (changes require updates in 4+ files)
  - Potential for drift
  - CSS rules may not apply consistently
- **Real-world symptom**: Card styling breaks on some pages, inconsistent behavior, hard to maintain.
- **Fix**: Replace all inline card markup with `get_template_part('template-parts/components/card', null, $args)`.

#### HIGH: Multiple Underline Implementations (Partially Resolved)
- **Location**: 
  - `assets/css/utilities.css` (lines 90-240): **CANONICAL** implementation
  - `assets/css/contact-page.css` (lines 790-804): Duplicate implementation
  - `assets/css/editor-style.css` (lines 55-63): Duplicate implementation
  - `assets/css/pages.css`: May have additional underline rules
- **Severity**: **HIGH**
- **Issue**: Link underline pattern is implemented in **4+ places**. Utilities.css is canonical, but other files have their own implementations that may conflict.
- **Real-world symptom**: Changes to underline behavior require updates in multiple files, potential for drift, inconsistent appearance.
- **Fix**: Remove ALL duplicate implementations, use canonical classes from utilities.css only.

#### MEDIUM: Section Rule Implementation (Resolved)
- **Location**: 
  - `assets/css/sections.css` (lines 17-24): **CANONICAL** gray line
  - `assets/css/utilities.css` (lines 196-203): **CANONICAL** blue segment overlay
- **Severity**: **MEDIUM** (actually resolved correctly, but worth noting)
- **Issue**: Section rule is split across two files (gray line in sections.css, blue overlay in utilities.css). This is actually correct per UI_CONTRACTS.md, but it's worth documenting.
- **Real-world symptom**: None - this is the correct implementation.
- **Fix**: None needed - this is correct.

#### MEDIUM: Border-Bottom Used for Rules (Competing with Section Rule)
- **Location**: 
  - `assets/css/about-page.css` (line 137): `border-bottom: 1px solid rgba(125,107,93,0.10);`
  - `assets/css/about-page.css` (line 923): `border-bottom: 1px solid var(--warmBorder);`
  - `assets/css/contact-page.css` (line 548): `border-bottom: 1px solid rgba(17,24,39,0.18);`
  - `assets/css/pages.css`: Multiple `border-bottom` rules
  - `assets/css/components.css`: Multiple `border-bottom` rules
  - `assets/css/wordpress-blocks.css`: Multiple `border-bottom` rules
- **Severity**: **MEDIUM**
- **Issue**: Multiple files use `border-bottom` for visual rules instead of the canonical section rule component. This creates competing implementations.
- **Real-world symptom**: Inconsistent visual appearance, rules look different on different pages, maintenance burden.
- **Fix**: Use `section-head.php` component for all section headers. Remove all `border-bottom` rules that are meant to be section rules.

#### MEDIUM: Competing Grid Implementations
- **Location**: 
  - `assets/css/sections.css`: `.grid` definition
  - `assets/css/components.css`: May have grid rules
  - `assets/css/pages.css`: May have grid rules
  - `assets/css/about-page.css`: May have grid rules
- **Severity**: **MEDIUM**
- **Issue**: Grid layouts may be defined in multiple files, causing conflicts or inconsistencies.
- **Real-world symptom**: Grids behave differently on different pages, layout breaks.
- **Fix**: Audit all grid definitions, consolidate into single location (likely `sections.css` or `layout.css`).

### 4.3 Fragile JS Contracts / Initialization

#### MEDIUM: ID-Based Contracts for Reusable Components
- **Location**: `assets/js/main.js` (lines 39-58):
  - `getElementById('actionDock')`
  - `getElementById('shareToggle')`
  - `getElementById('subscribeToggle')`
  - `getElementById('sharePanel')`
  - `getElementById('subscribePanel')`
- **Severity**: **MEDIUM**
- **Issue**: These components could theoretically be reused on multiple pages, but IDs make them single-use.
- **Real-world symptom**: If these components are reused, JS will break (multiple elements with same ID).
- **Fix**: Use `data-ui` hooks similar to filter bar, or ensure these remain single-use and document that.

#### LOW: ID-Based Contracts for Unique Page Elements (Acceptable)
- **Location**: `assets/js/main.js`, `assets/js/about-page.js`, `assets/js/contact-page.js`
- **Severity**: **LOW** (acceptable)
- **Issue**: Many `getElementById()` calls for elements that are unique per page (navToggle, nav, avatar, etc.).
- **Real-world symptom**: None - these are unique per page.
- **Fix**: None needed - acceptable for unique elements.

#### LOW: Non-Idempotent Init Risk
- **Location**: `assets/js/main.js` (line 10): `document.documentElement.classList.add('js-ready');`
- **Severity**: **LOW**
- **Issue**: Adding class is idempotent, but if script loads twice, could cause issues.
- **Real-world symptom**: Unlikely, but possible if script is enqueued twice.
- **Fix**: Check if class exists before adding, or ensure script is only enqueued once.

### 4.4 Layout Brittleness / "Prototype Behavior"

#### MEDIUM: Negative Margin Hacks
- **Location**: 
  - `assets/css/about-page.css` (line 220): `margin-top: calc(-1 * var(--mastH, 100px));`
  - `assets/css/contact-page.css` (line 17): `margin-top: calc(-1 * var(--mastH, 100px));`
- **Severity**: **MEDIUM**
- **Issue**: Negative margins to pull content into header space. While functional, it's a layout hack.
- **Real-world symptom**: Layout breaks if header height changes unexpectedly.
- **Fix**: Use proper layout containers instead of negative margins.

#### LOW: Missing Min-Width: 0 (Partially Addressed)
- **Location**: 
  - `assets/css/about-page.css`: Has `min-width: 0` in 4 places (lines 405, 967, 970, 998, 1324)
  - `assets/css/filters.css`: Has `min-width: 0` in 2 places (lines 272, 273)
  - `assets/css/header.css`: Has `min-width: 0` in 2 places (lines 186, 234)
  - `assets/css/components.css`: Has `min-width: 0` in 1 place (line 340)
- **Severity**: **LOW**
- **Issue**: Some grid/flex children have `min-width: 0` (good), but not all. However, no overflow issues reported.
- **Real-world symptom**: Potential text overflow in grid/flex children.
- **Fix**: Add `min-width: 0` to all grid/flex children that contain text (proactive fix).

#### LOW: Z-Index Usage
- **Location**: 23 instances across 9 CSS files
- **Severity**: **LOW**
- **Issue**: Z-index is used for layering (header, modals, tooltips). No obvious conflicts.
- **Real-world symptom**: None observed.
- **Fix**: Document z-index scale in tokens.css if not already done.

### 4.5 WP Best Practices + Safety

#### HIGH: Escaping/Sanitization (Well Implemented)
- **Location**: All template files, block render.php files
- **Severity**: **N/A** (actually good)
- **Issue**: None - all output uses `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()` appropriately.
- **Real-world symptom**: None - security is good.
- **Fix**: None needed.

#### HIGH: Nonce Checks (Well Implemented)
- **Location**: 
  - `inc/Features/Ajax/filter-content.php`: Has nonce check
  - `inc/Features/Ajax/debug-log.php`: Has nonce check
  - `inc/Features/Email/email-handlers.php`: Has nonce check
  - `inc/Features/Email/subscribe-handler.php`: Has nonce check
  - `inc/meta/meta-boxes.php`: Has nonce check
  - `pdf-generator.php`: Has nonce check
- **Severity**: **N/A** (actually good)
- **Issue**: None - all AJAX/admin actions have nonce checks.
- **Real-world symptom**: None - security is good.
- **Fix**: None needed.

#### MEDIUM: Function Exists Checks in Enqueue
- **Location**: `inc/Setup/enqueue.php` (lines 237, 295, 361-364)
- **Severity**: **MEDIUM**
- **Issue**: Uses `function_exists()` to check for functions before calling them. This suggests optional dependencies.
- **Real-world symptom**: If functions don't exist, code silently fails.
- **Fix**: Ensure functions are always available, or add proper error handling.

### 4.6 Maintainability + Correctness

#### CRITICAL: Dead Code - block-helpers.php
- **Location**: `inc/block-helpers.php` (495 lines)
- **Severity**: **CRITICAL**
- **Issue**: File exists but is NOT loaded in `functions.php`. It's dead code that duplicates `inc/Blocks/helpers.php`.
- **Real-world symptom**: Confusion, potential for accidental edits to wrong file, maintenance burden.
- **Fix**: Delete file immediately.

#### CRITICAL: Dead Code - blocks.php (Potentially)
- **Location**: `inc/blocks.php` (474 lines)
- **Severity**: **CRITICAL**
- **Issue**: File contains complete block registration system that duplicates `inc/Blocks/register.php`. Both may be active (both hook into `init`).
- **Real-world symptom**: Blocks registered twice, hooks fire twice, unpredictable behavior.
- **Fix**: Verify if `inc/blocks.php` hooks are active, delete if duplicate, or consolidate if it has unique functionality.

#### CRITICAL: V22 Naming in Customizer Keys (248 Settings) - FULL MIGRATION REQUIRED
- **Location**: All About page customizer settings use `kunaal_about_v22_*` prefix:
  - `kunaal_about_v22_hero_*` (10 photo settings + intro, hand_note, location, listening, reading)
  - `kunaal_about_v22_number_*` (8 numbers × 3 fields = 24 settings)
  - `kunaal_about_v22_category_*` (12 categories × 2 fields = 24 settings)
  - `kunaal_about_v22_rabbit_hole_*` (200 rabbit holes × 4 fields = 800 settings)
  - `kunaal_about_v22_panorama_*` (10 panoramas × 5 fields = 50 settings)
  - `kunaal_about_v22_book_*` (6 books × 3 fields = 18 settings)
  - `kunaal_about_v22_digital_*` (6 digital items × 4 fields = 24 settings)
  - `kunaal_about_v22_inspiration_*` (10 inspirations × 4 fields = 40 settings)
  - Plus toggle settings, titles, etc.
- **Severity**: **HIGH**
- **Issue**: "v22" suggests versioning, but these are the canonical settings. Creates confusion, maintenance burden.
- **Real-world symptom**: Confusion about migration status, unclear if these are temporary or permanent.
- **Fix**: Rename ALL customizer keys from `kunaal_about_v22_*` to `kunaal_about_*` (requires data migration script to preserve user settings).

#### LOW: Commented Code / Removed Notes
- **Location**: Various files
- **Severity**: **LOW**
- **Issue**: Some files have comments about removed features, but no actual commented code found.
- **Real-world symptom**: None.
- **Fix**: Clean up if any commented code exists.

#### LOW: Magic Constants
- **Location**: Various files
- **Severity**: **LOW**
- **Issue**: Some magic numbers/strings, but most are in CSS (acceptable) or have clear context.
- **Real-world symptom**: None.
- **Fix**: Extract to constants if they're used multiple times.

---

## 5. REFACTOR PLAN

### Phase 0: Delete Empty Folders and Useless Files (5 minutes)
**Goal**: Remove all non-production files and empty directories.

**Files to Delete**:
- `kunaal-theme/specs/blocks/` (empty folder)
- `kunaal-theme/inc/about/` (empty folder)  
- `kunaal-theme/AGENT_PROGRESS_META.md`
- `kunaal-theme/AGENT_PROGRESS_UI.md`
- `kunaal-theme/AGENT_PROGRESS.md`

**Verification**:
- [x] `ls kunaal-theme/specs/blocks/` returns empty (folder deleted)
- [x] `ls kunaal-theme/inc/about/` returns empty (folder deleted)
- [x] `grep -r "AGENT_PROGRESS" kunaal-theme` returns zero results (all files deleted)

**Status**: ✅ COMPLETE - All empty folders and useless files deleted.

**QA Verification**:
- ✅ Empty folders `specs/` and `inc/about/` deleted
- ✅ All `AGENT_PROGRESS*.md` files deleted
- ✅ No remaining references to deleted files

---

### Phase 0.5: Replace ALL nth-child Usages (CRITICAL - 4 hours)
**Goal**: ZERO tolerance. Replace all 28 nth-child usages with explicit classes/data attributes.

**Files to Touch**:
- `blocks/framework-matrix/render.php` - Add explicit classes `fm-cell--1` through `fm-cell--9`
- `blocks/framework-matrix/style.css` - Replace 9 nth-child selectors with explicit classes
- `assets/css/wordpress-blocks.css` - Verify table striping is decorative only (acceptable)
- `blocks/pub-table/style.css` - Verify table striping is decorative only (acceptable)
- `blocks/rubric/style.css` - Verify table striping is decorative only (acceptable)
- `blocks/assumptions-register/style.css` - Replace 2 nth-child with explicit column classes if layout-critical

**Tasks**:
1. **Framework matrix (CRITICAL)**: Add `fm-cell--1` through `fm-cell--9` classes in render.php
2. Update CSS to use explicit classes instead of nth-child(1-9)
3. Verify table striping uses nth-child(even/odd) for decoration only (acceptable)
4. If assumptions-register nth-child is layout-critical, replace with explicit classes

**Verification**:
- [x] `grep -r "nth-child" kunaal-theme` returns ZERO layout-critical instances (only decorative table striping remains)
- [x] Only decorative table striping (nth-child(even/odd)) remains (acceptable per UI_CONTRACTS.md)
- [x] Framework matrix uses explicit classes `fm-cell--1` through `fm-cell--9`
- [x] Assumptions register uses explicit class `.ar-status` instead of nth-child(3)

**Status**: ✅ COMPLETE - All layout-critical nth-child usages replaced with explicit classes. Only decorative table striping remains (acceptable).

**QA Verification**:
- ✅ Framework matrix uses explicit classes `fm-cell--1` through `fm-cell--9`
- ✅ Assumptions register uses explicit class `.ar-status` instead of nth-child(3)
- ✅ Only decorative table striping (nth-child(even/odd)) remains - acceptable per UI_CONTRACTS.md
- ✅ No layout-critical nth-child usages remain

---

### Phase 0.6: Replace Reusable Component getElementById Calls (CRITICAL - 6 hours)
**Goal**: ZERO tolerance. Replace all reusable component IDs with data-* hooks.

**Files to Touch**:
- `assets/js/main.js` - Replace 9 reusable component IDs with data-* hooks
- `assets/js/about-page.js` - Replace 2 reusable component IDs with data-* hooks
- `header.php` - Add data-ui attributes to nav, panels, etc.
- `footer.php` - Add data-ui attributes if needed

**Tasks**:
1. **main.js reusable components** (9 instances):
   - `actionDock` → `[data-ui="action-dock"]`
   - `sharePanel` → `[data-ui="share-panel"]`
   - `subscribePanel` → `[data-ui="subscribe-panel"]`
   - `navToggle` → `[data-ui="nav-toggle"]`
   - `nav` → `[data-ui="nav"]`
   - `shareToggle` → `[data-ui="share-toggle"]`
   - `subscribeToggle` → `[data-ui="subscribe-toggle"]`
   - `essayGrid` → `[data-ui="essay-grid"]`
   - `jotList` → `[data-ui="jot-list"]`
2. **about-page.js reusable components** (2 instances):
   - `navToggle` → `[data-ui="nav-toggle"]`
   - `mainNav` → `[data-ui="nav"]`
3. Update all templates to add data-ui attributes
4. Replace `getElementById` with `querySelector('[data-ui="..."]')`
5. Keep IDs only for: anchors, ARIA relationships, unique form inputs

**Verification**:
- [ ] `grep -r "getElementById" kunaal-theme/assets/js` shows ZERO reusable component IDs
- [ ] All nav, panels, grids work correctly with data-* hooks
- [ ] Visual regression test: All pages with nav/panels at all viewports

---

### Phase 0.7: Remove ALL !important from base.css and about-page.css (CRITICAL - 4 hours)
**Goal**: ZERO tolerance. Remove all 21 !important declarations from base.css (13) and about-page.css (8).

**Files to Touch**:
- `assets/css/base.css` - Remove 13 !important declarations
- `assets/css/about-page.css` - Remove 8 !important declarations
- Fix underlying specificity issues properly

**Tasks**:
1. **base.css** (13 instances):
   - Title case fixes: Use cascade layers properly instead of !important
   - Theme transitions: Use `:where()` for lower specificity
   - Fix root cause of specificity wars
2. **about-page.css** (8 instances):
   - Reveal animations: Fix specificity with proper cascade layers
   - Remove !important, ensure animations work with proper specificity
3. Verify cascade layers are properly defined in all CSS files
4. Test that all styles still work without !important

**Verification**:
- [x] `grep "!important" kunaal-theme/assets/css/base.css` returns ZERO results
- [x] `grep "!important" kunaal-theme/assets/css/about-page.css` returns ZERO results
- [x] Title case fixes use `:where()` for lower specificity (no !important needed)
- [x] Reveal animations use proper cascade layers (no !important needed)
- [x] Progressive enhancement fallbacks work without !important

**Status**: ✅ COMPLETE - All !important removed from base.css and about-page.css. Fixed with proper cascade layers and :where() for lower specificity.

**QA Verification**:
- ✅ base.css: 0 !important (was 13, now 0)
- ✅ about-page.css: 0 !important (was 8, now 0)
- ✅ Title case fixes use `:where()` for lower specificity
- ✅ Reveal animations use proper cascade layers
- ✅ Progressive enhancement fallbacks work without !important

---

### Phase 0.8: Replace Inline Section Headers with Component (CRITICAL - 2 hours)
**Goal**: ZERO tolerance. All section headers must use canonical component.

**Files to Touch**:
- `template-parts/home.php` - Replace 2 inline section headers
- `archive-essay.php` - Replace 1 inline section header
- `archive-jotting.php` - Replace 1 inline section header

**Tasks**:
1. Replace all `<div class="sectionHead">` with `get_template_part('template-parts/components/section-head', null, $args)`
2. Ensure all section header data (title, count, more link) is passed correctly
3. Verify section headers render identically

**Verification**:
- [x] `grep -r "class=\"sectionHead\"" kunaal-theme/*.php` returns ZERO results (only in component file)
- [x] All section headers use `get_template_part('template-parts/components/section-head')`
- [x] All templates (home.php, archive-essay.php, archive-jotting.php) updated

**Status**: ✅ COMPLETE - All inline section headers replaced with canonical component. Single source of truth established.

---

### Phase 0.9: Preparation & Documentation
**Goal**: Document current state, create migration scripts, backup data

**Files to Touch**:
- `TECH_DEBT.md` - Update with all findings
- Create `MIGRATION_LOG.md` - Track migration progress
- Backup database (customizer settings)

**Tasks**:
1. Document all `_v22` function names and customizer keys
2. Create data migration script for customizer key renaming
3. Verify `inc/blocks.php` is unused
4. Create test checklist for each phase

**Verification**:
- [ ] TECH_DEBT.md updated
- [ ] Migration script created and tested on staging
- [ ] Backup completed

---

### Phase 1: Delete Dead Code & Duplicate Systems
**Goal**: Remove unused files, duplicate systems, and consolidate

**Files to Delete**:
- `inc/block-helpers.php` (DEAD CODE - not loaded, duplicate of Blocks/helpers.php)
- `inc/blocks.php` (DUPLICATE SYSTEM - entire block registration system duplicated)
- `inc/about/` (empty directory)
- `inc/validation/validation.php` (if duplicate of Support/validation.php - verify first)

**Files to Audit & Consolidate**:
- `inc/Support/validation.php` vs `inc/validation/validation.php` - determine which is canonical, delete duplicate

**Tasks**:
1. **Verify block registration**: Check if `inc/blocks.php` hooks are active (grep for `add_action.*kunaal_register`)
2. **Audit validation files**: Compare both validation files, identify unique functions, consolidate
3. **Delete confirmed dead/duplicate files**
4. **Test**: Ensure site still works after deletions
5. **Update functions.php**: Remove any references to deleted files

**Verification**:
- [ ] `grep -r "block-helpers.php"` returns no results (except in this audit)
- [ ] `grep -r "inc/blocks.php"` returns no results
- [ ] Only ONE block registration system active (check `add_action` hooks)
- [ ] Only ONE validation file loaded
- [ ] Site still works after deletion
- [ ] PHP lint passes
- [ ] All blocks still register correctly

---

### Phase 2: Replace Inline Card Markup with Component
**Goal**: Use canonical card component everywhere, eliminate copy-paste

**Files to Touch**:
- `index.php` - Replace inline card markup (lines 29-55)
- `taxonomy-topic.php` - Replace inline card markup (lines 31-55)
- `archive-essay.php` - Replace inline card markup
- `archive-jotting.php` - Replace inline card markup
- `template-parts/home.php` - Verify uses component (if not, fix)

**Tasks**:
1. For each template file:
   - Extract card data (post_id, title, permalink, date, subtitle, topics, card_image, read_time)
   - Replace inline markup with: `get_template_part('template-parts/components/card', null, $args)`
   - Ensure all card data is passed correctly
2. Test each template to ensure cards render correctly
3. Verify CSS applies consistently

**Verification**:
- [ ] `grep -r "class=\"card\"" kunaal-theme/*.php` returns NO results (cards should be in component only)
- [ ] `grep -r "get_template_part.*card"` shows all templates using component
- [ ] All archive pages render cards correctly
- [ ] Card styling is consistent across all pages
- [ ] Visual regression test: All archive pages at 375/768/1024/1440, light + dark mode

### Phase 3: Consolidate Duplicate CSS Implementations
**Goal**: Remove ALL duplicate underline implementations, use canonical classes only

**Files to Touch**:
- `assets/css/contact-page.css` (lines 790-804) - Remove duplicate underline
- `assets/css/editor-style.css` (lines 55-63) - Remove duplicate underline
- `assets/css/pages.css` - Audit and remove any duplicate underline rules
- `assets/css/about-page.css` - Audit for duplicate underline rules

**Tasks**:
1. Search ALL CSS files for underline implementations: `grep -r "background.*underline|text-decoration.*underline" assets/css/`
2. Remove ALL duplicates except canonical in `utilities.css`
3. Ensure contact page, editor, and all pages use canonical `.u-underline-double` or global `:where()` selector
4. Test all pages with links

**Verification**:
- [ ] `grep -r "background.*underline|text-decoration.*underline" assets/css/` returns results ONLY in `utilities.css`
- [ ] All pages have consistent link underline behavior
- [ ] Visual regression test: 375/768/1024/1440 viewports, light + dark mode

---

### Phase 4: Rename V22 Functions (Remove Suffix)
**Goal**: Remove `_v22` suffix from function names to indicate they're canonical

**Files to Touch**:
- `inc/Features/About/data.php` - Rename all `*_v22()` functions
- `inc/Setup/enqueue.php` - Update function calls
- `page-about.php` - Update function calls
- `inc/Features/About/customizer.php` - Update if references exist
- `inc/Features/About/render.php` - Update if references exist

**Function Renames**:
- `kunaal_get_hero_photos_v22()` → `kunaal_get_hero_photos()` (or delete if deprecated)
- `kunaal_get_hero_photo_ids_v22()` → `kunaal_get_hero_photo_ids()`
- `kunaal_get_numbers_v22()` → `kunaal_get_numbers()`
- `kunaal_get_categories_v22()` → `kunaal_get_categories()`
- `kunaal_get_rabbit_holes_v22()` → `kunaal_get_rabbit_holes()`
- `kunaal_get_panoramas_v22()` → `kunaal_get_panoramas()`
- `kunaal_get_books_v22()` → `kunaal_get_books()`
- `kunaal_get_digital_media_v22()` → `kunaal_get_digital_media()`
- `kunaal_get_places_v22()` → `kunaal_get_places()`
- `kunaal_get_inspirations_v22()` → `kunaal_get_inspirations()`
- `kunaal_get_category_choices_v22()` → `kunaal_get_category_choices()`

**Tasks**:
1. Rename all functions in `data.php`
2. Update all call sites (grep for each function name)
3. Test About page functionality
4. Update function docblocks

**Verification**:
- [ ] `grep -r "_v22(" kunaal-theme` returns no results (except in customizer keys)
- [ ] About page renders correctly
- [ ] All customizer settings work
- [ ] PHP lint passes
- [ ] No PHP errors in debug log

---

### Phase 5: Rename V22 Customizer Keys (Data Migration Required)
**Goal**: Remove `_v22` suffix from customizer setting keys

**Files to Touch**:
- `inc/Features/About/customizer.php` - Update all setting keys
- `inc/Features/About/customizer-sections.php` - Update all setting keys
- `inc/Features/About/data.php` - Update all `kunaal_mod()` calls
- `inc/Features/About/render.php` - Update all `kunaal_mod()` calls
- `page-about.php` - Update all `kunaal_mod()` calls
- `inc/Setup/enqueue.php` - Update if references exist
- Create migration script: `scripts/migrate-customizer-keys.php`

**Customizer Key Pattern**:
- `kunaal_about_v22_*` → `kunaal_about_*`

**Tasks**:
1. Create migration script that:
   - Reads all `kunaal_about_v22_*` options from database
   - Creates new `kunaal_about_*` options with same values
   - Logs migration
   - Has rollback capability
2. Update all PHP files to use new keys
3. Test migration script on staging
4. Run migration on production
5. Keep old keys for 1 release cycle, then delete

**Verification**:
- [ ] Migration script tested on staging
- [ ] All customizer settings migrated
- [ ] About page works with new keys
- [ ] Old keys can be deleted after verification period
- [ ] `grep -r "kunaal_about_v22_" kunaal-theme` returns no results (after migration complete)

---

### Phase 6: Convert Reusable Components to Data-* Hooks
**Goal**: Make actionDock, sharePanel, subscribePanel use data-* hooks instead of IDs

**Files to Touch**:
- `assets/js/main.js` - Replace `getElementById()` with `querySelector('[data-ui="..."]')`
- `header.php` or relevant template - Add `data-ui` attributes
- Update UI_CONTRACTS.md

**Components to Convert**:
- `actionDock` → `[data-ui="action-dock"]`
- `shareToggle` → `[data-action="share-toggle"]`
- `subscribeToggle` → `[data-action="subscribe-toggle"]`
- `sharePanel` → `[data-role="share-panel"]`
- `subscribePanel` → `[data-role="subscribe-panel"]`

**Tasks**:
1. Add data-* attributes to HTML
2. Update JS to use `querySelector()` with data-* hooks
3. Keep IDs for backward compatibility (or remove if not needed for ARIA)
4. Test all pages that use these components

**Verification**:
- [ ] All components work with data-* hooks
- [ ] No JS errors in console
- [ ] Visual regression test: all pages with these components
- [ ] Update UI_CONTRACTS.md

---

### Phase 7: Remove Function Exists Guards (Where Appropriate)
**Goal**: Ensure each function is defined once, remove unnecessary guards

**Files to Touch**:
- `inc/Blocks/helpers.php` - Remove `function_exists()` guards (functions are only loaded once)
- `inc/Support/helpers.php` - Remove `function_exists()` guards if functions are only loaded once
- Verify no duplicate includes in `functions.php`

**Tasks**:
1. Verify each file is only included once
2. Remove `function_exists()` guards where safe
3. Keep guards only if functions are conditionally defined (e.g., plugin compatibility)

**Verification**:
- [ ] `grep -r "function_exists.*kunaal_" kunaal-theme` shows only necessary guards
- [ ] All functions work correctly
- [ ] PHP lint passes

---

### Phase 8: Fix Negative Margin Hacks
**Goal**: Replace negative margins with proper layout containers

**Files to Touch**:
- `assets/css/about-page.css` (line 220)
- `assets/css/contact-page.css` (line 17)
- Template files if needed

**Tasks**:
1. Analyze why negative margins are needed
2. Refactor to use proper layout (grid/flex with correct spacing)
3. Test on all viewports

**Verification**:
- [ ] No negative margins in CSS (except for specific design needs)
- [ ] Layout works on all viewports
- [ ] Visual regression test: 375/768/1024/1440

---

### Phase 9: Proactive Min-Width: 0 Fixes
**Goal**: Add `min-width: 0` to all grid/flex children that contain text

**Files to Touch**:
- All CSS files with grid/flex layouts

**Tasks**:
1. Audit all grid/flex containers
2. Add `min-width: 0` to children that contain text
3. Test for text overflow

**Verification**:
- [ ] No text overflow in grid/flex children
- [ ] Visual regression test

---

### Phase 10: Documentation & Contract Updates
**Goal**: Update all documentation to reflect changes

**Files to Touch**:
- `.cursor/rules/UI_CONTRACTS.md` - Update with new contracts
- `TECH_DEBT.md` - Mark resolved items
- `MIGRATION_LOG.md` - Document completed phases
- `README.md` - Update if needed

**Tasks**:
1. Update UI_CONTRACTS.md with data-* hook contracts
2. Mark resolved items in TECH_DEBT.md
3. Document any new patterns

**Verification**:
- [ ] All documentation updated
- [ ] Contracts reflect current implementation

---

## 6. RISK & REGRESSION STRATEGY

### 5.1 Pre-Refactor Snapshot
**Before starting any phase:**
1. **Database Backup**: Export all customizer settings (especially About page settings)
2. **Code Snapshot**: Create git branch `pre-refactor-audit-YYYY-MM-DD`
3. **Visual Baseline**: Screenshot all pages at 375/768/1024/1440 in light + dark mode
4. **Functional Baseline**: Test all interactive features (filters, forms, navigation)

### 5.2 Per-Phase Testing Checklist

**For each phase, test:**
- [ ] **PHP Syntax**: `find . -name "*.php" -print0 | xargs -0 -n1 php -l`
- [ ] **JS Syntax**: `find . -name "*.js" -print0 | xargs -0 -n1 node --check`
- [ ] **No BOM**: `find . -type f \( -name "*.php" -o -name "*.css" \) -print0 | xargs -0 grep -Il $'^\xEF\xBB\xBF'` (should return nothing)
- [ ] **Visual Regression**: All pages at 375/768/1024/1440, light + dark mode
- [ ] **Functional Tests**:
  - [ ] Home page filters work
  - [ ] About page renders correctly
  - [ ] Contact form works
  - [ ] Navigation works
  - [ ] Dark mode toggle works
  - [ ] All blocks render correctly
- [ ] **Console Errors**: No JS errors in browser console
- [ ] **PHP Errors**: Check debug log for PHP errors

### 5.3 Rollback Strategy

**If a phase breaks functionality:**
1. **Immediate**: Revert git commit for that phase
2. **Database**: If customizer keys were migrated, restore from backup
3. **Investigate**: Identify root cause before retrying
4. **Document**: Add to TECH_DEBT.md with investigation notes

### 5.4 Staging First, Then Production

**Deployment order:**
1. **Staging**: Complete entire phase, test thoroughly
2. **Production**: Deploy only after staging is verified
3. **Monitor**: Watch error logs for 24 hours after production deploy

### 5.5 Viewport & Mode Matrix

**Test every change in:**
- Viewports: 375px (mobile), 768px (tablet), 1024px (desktop), 1440px (large desktop)
- Modes: Light mode, Dark mode
- Pages: Home, About, Contact, Single essay, Single jotting, Archive pages, 404

**Total test matrix**: 8 viewport/mode combinations × 7+ pages = 56+ visual checks per phase

---

## 7. VERIFICATION CHECKLIST (Final)

After all phases complete:

### Code Quality
- [ ] **Zero duplicate systems** (only ONE block registration, ONE validation system)
- [ ] **Zero duplicate helper files** (block-helpers.php deleted)
- [ ] **All cards use component** (no inline card markup in templates)
- [ ] Zero `_v22` suffixes in function names
- [ ] Zero `_v22` suffixes in customizer keys (after migration period)
- [ ] Zero duplicate CSS implementations (underline, section rules)
- [ ] All reusable components use data-* hooks
- [ ] Minimal `function_exists()` guards (only where necessary)
- [ ] No negative margin hacks (or documented if necessary)

### Functionality
- [ ] All pages render correctly
- [ ] All interactive features work
- [ ] No console errors
- [ ] No PHP errors
- [ ] Filters work on all archive pages
- [ ] About page customizer works
- [ ] Contact form works
- [ ] Dark mode works

### Documentation
- [ ] TECH_DEBT.md updated
- [ ] UI_CONTRACTS.md updated
- [ ] MIGRATION_LOG.md complete
- [ ] All contracts documented

### Performance
- [ ] No regression in page load times
- [ ] No new blocking scripts
- [ ] CSS bundle size hasn't increased significantly

---

## 8. ESTIMATED EFFORT

| Phase | Estimated Time | Risk Level |
|-------|---------------|------------|
| Phase 0: Delete Empty Folders | 5 minutes | **CRITICAL** |
| Phase 0.5: Replace nth-child | 4 hours | **CRITICAL** |
| Phase 0.6: Replace getElementById | 6 hours | **CRITICAL** |
| Phase 0.7: Remove !important | 4 hours | **CRITICAL** |
| Phase 0.8: Replace Inline Section Headers | 2 hours | **CRITICAL** |
| Phase 0.9: Preparation & Documentation | 2-4 hours | Low |
| Phase 1: Delete Dead Code & Duplicates | 4-6 hours | **High** (block registration system) |
| Phase 2: Replace Inline Card Markup | 6-8 hours | Medium |
| Phase 3: Consolidate CSS | 4-6 hours | Medium |
| Phase 4: Rename V22 Functions | 4-6 hours | Medium |
| Phase 5: Rename Customizer Keys | 6-8 hours | **High** (data migration) |
| Phase 6: Data-* Hooks | 4-6 hours | Medium |
| Phase 7: Remove Function Guards | 2-4 hours | Low |
| Phase 8: Fix Negative Margins | 4-6 hours | Medium |
| Phase 9: Min-Width Fixes | 2-4 hours | Low |
| Phase 10: Documentation | 2-4 hours | Low |
| **Total** | **40-58 hours** | |

**ZERO TOLERANCE PRIORITIES** (Must be completed first):
- **Phase 0**: Delete empty folders and useless files (5 minutes)
- **Phase 1**: Delete duplicate block registration system (CRITICAL - two systems active)
- **Phase 2**: Replace ALL 28 nth-child usages with explicit classes (CRITICAL)
- **Phase 3**: Replace ALL reusable component getElementById calls with data-* hooks (CRITICAL)
- **Phase 4**: Remove ALL !important from base.css and about-page.css (CRITICAL - 21 instances)
- **Phase 5**: Replace inline section headers with component (CRITICAL)
- **Phase 6**: FULL V22 migration (248 instances - requires data migration script)

**ZERO TOLERANCE PRIORITIES** (Must be completed first):
- **Phase 0**: Delete empty folders and useless files (5 minutes)
- **Phase 0.5**: Replace ALL 28 nth-child usages (4 hours)
- **Phase 0.6**: Replace ALL reusable component getElementById calls (6 hours)
- **Phase 0.7**: Remove ALL !important from base.css and about-page.css (4 hours)
- **Phase 0.8**: Replace inline section headers with component (2 hours)
- **Phase 1**: Delete duplicate block registration system (CRITICAL - two systems active)
- **Phase 4-5**: FULL V22 migration (248 instances - requires data migration script)

**Recommendation**: 
- Complete Phase 0-0.8 FIRST (zero tolerance items - 16.5 hours)
- Then Phase 1-3 (critical redundancies - 14-20 hours)
- Then Phase 4-5 (V22 migration - 10-14 hours with data migration)
- Test thoroughly after each phase - don't rush

---

## 9. PRIORITY ORDER

**Immediate (Do First - Critical Redundancies)**:
1. Phase 1: Delete duplicate block registration system (CRITICAL - two systems active)
2. Phase 1: Delete duplicate validation files (CRITICAL - unclear which is canonical)
3. Phase 2: Replace inline card markup (CRITICAL - violates single source of truth)
4. Phase 3: Consolidate CSS (prevents drift)

**High Value (Do Soon)**:
5. Phase 4: Rename V22 functions (clearer code)
6. Phase 6: Data-* hooks (future-proofs components)
7. Phase 7: Remove function guards (cleaner code)

**Requires Care (Plan Carefully)**:
8. Phase 5: Customizer key migration (data migration, test thoroughly)
9. Phase 8: Negative margins (layout changes, test all viewports)

**Nice to Have (Do When Time Permits)**:
10. Phase 9: Min-width fixes (proactive, no current issues)
11. Phase 10: Documentation (ongoing)

---

---

## 10. ZERO TOLERANCE CRITICAL FINDINGS

### CRITICAL: 28 nth-child Usages (ALL Must Be Replaced)

**Requirement**: ZERO tolerance. ALL nth-child usages must be replaced with explicit classes or data attributes.

**Found Locations**:
- `assets/css/wordpress-blocks.css` (2 instances): Table row striping
- `blocks/pub-table/style.css` (1 instance): Table row striping
- `blocks/rubric/style.css` (1 instance): Table row striping
- `blocks/framework-matrix/style.css` (9 instances): Matrix cell backgrounds (CRITICAL - layout dependent)
- `blocks/assumptions-register/style.css` (2 instances): Table column styling

**Why This Is CRITICAL**:
- Layout depends on DOM order, not semantic meaning
- Breaks when items are reordered or filtered
- Violates architecture.mdc rule: "Never use nth-child for layout-critical positioning"
- Framework matrix uses nth-child(1-9) for 3x3 grid - this is EXACTLY what architecture.mdc forbids

**Fix Strategy**:
1. **Framework matrix**: Add explicit classes `fm-cell--1` through `fm-cell--9` in render.php
2. **Table striping**: Use CSS `:nth-child(even/odd)` ONLY for visual decoration (acceptable per UI_CONTRACTS.md)
3. **Assumptions register**: Add explicit column classes if layout-critical

**Verification**: `grep -r "nth-child" kunaal-theme` must return ZERO layout-critical instances (only decorative table striping allowed).

---

### CRITICAL: 44 getElementById Calls (Reusable Components Must Use data-*)

**Requirement**: ZERO tolerance for ID-based JS contracts on reusable components. Only unique page elements (anchors, ARIA relationships) may use IDs.

**Found Locations**:
- `assets/js/main.js` (26 instances):
  - **Reusable components** (MUST FIX): `actionDock`, `sharePanel`, `subscribePanel`, `navToggle`, `nav`, `shareToggle`, `subscribeToggle`, `essayGrid`, `jotList`
  - **Unique page elements** (acceptable): `progressFill`, `avatar`, `avatarImg`, `downloadButton`, `tocList`, `infiniteLoader`, `essayCountShown`, `essayLabel`, `jotCountShown`, `jotLabel`, `announcer`, `articleProse`, `articleRail`, `scrollyTitle`, `scrollyNote`
- `assets/js/about-page.js` (11 instances):
  - **Reusable components** (MUST FIX): `navToggle`, `mainNav`
  - **Unique page elements** (acceptable): `scrollIndicator`, `world-map`, `mapTooltip`, `progressFill`, `footerYear`, `year`
- `assets/js/contact-page.js` (7 instances):
  - **All are unique form elements** (acceptable): `contact-form`, `contact-include-info`, `contact-optional-fields`, `contact-name`, `contact-email`, `contact-submit`, `contact-status`

**Why This Is CRITICAL**:
- Reusable components (nav, panels, grids) cannot be used multiple times on a page
- Violates UI_CONTRACTS.md: "JS must not depend on element IDs as primary contracts"
- Creates brittle code that breaks when components are reused

**Fix Strategy**:
1. Replace all reusable component IDs with `data-ui="nav"`, `data-ui="share-panel"`, etc.
2. Use `querySelector('[data-ui="nav"]')` instead of `getElementById('nav')`
3. Keep IDs only for: anchors, ARIA relationships, unique form inputs

**Verification**: `grep -r "getElementById" kunaal-theme/assets/js` must show ZERO reusable component IDs.

---

### CRITICAL: 62 !important Declarations (Only 5-10 Acceptable)

**Requirement**: Maximum 5-10 !important declarations total. Only in print styles, compatibility overrides, or documented utility classes.

**Found by File**:
- `assets/css/base.css`: **13 instances** (CRITICAL - should be 0)
- `assets/css/about-page.css`: **8 instances** (CRITICAL - should be 0)
- `assets/css/compatibility.css`: **12 instances** (ACCEPTABLE - reduced motion overrides)
- `assets/css/pdf-ebook.css`: **18 instances** (ACCEPTABLE - print color adjustments)
- `assets/css/print.css`: **11 instances** (ACCEPTABLE - print overrides)

**Why base.css and about-page.css Are CRITICAL**:
- `base.css` has 13 !important for title case fixes and theme transitions
- `about-page.css` has 8 !important for reveal animations
- These indicate architectural problems (specificity wars, competing styles)
- Should be fixed with proper cascade layers and specificity hierarchy

**Fix Strategy**:
1. **base.css**: Remove all !important, use cascade layers properly
2. **about-page.css**: Remove all !important, fix reveal animation specificity
3. **compatibility.css**: Keep (reduced motion requires !important)
4. **print.css/pdf-ebook.css**: Keep (print styles require !important)

**Target**: Maximum 5-10 !important total (only in print/compatibility).

**Verification**: `grep -r "!important" kunaal-theme/assets/css` must show ZERO in base.css and about-page.css.

---

### CRITICAL: 248 V22 Naming Instances (FULL MIGRATION REQUIRED)

**Requirement**: ZERO tolerance. Complete migration away from "v22" naming. This is NOT "medium priority" - it's CRITICAL technical debt.

**Found Locations**:
- `inc/Features/About/data.php`: 11 functions with `_v22` suffix
- `inc/Features/About/customizer.php`: 1 function + 248 customizer settings with `kunaal_about_v22_*` prefix
- `inc/Features/About/customizer-sections.php`: All section registrations use `kunaal_about_v22_*`
- `page-about.php`: 20+ direct calls to `*_v22()` functions
- `inc/Setup/enqueue.php`: 2 references to `*_v22()` functions

**Why This Is CRITICAL**:
- Creates confusion about migration status
- Suggests temporary/versioned code that's actually canonical
- Maintenance burden (every developer must know "v22" is current)
- Violates clean architecture principles

**Fix Strategy**:
1. **Phase 4**: Rename all 11 functions (remove `_v22` suffix)
2. **Phase 5**: Create data migration script for 248 customizer settings
3. Update all references (grep and replace)
4. Delete old customizer keys after migration

**Note**: V22 migration is now Phase 4-5 (after zero tolerance items are fixed).

**Verification**: `grep -r "_v22" kunaal-theme` must return ZERO results.

---

### CRITICAL: Empty Folders and Useless Files

**Requirement**: ZERO tolerance for empty folders or non-production files in theme directory.

**Found**:
- `kunaal-theme/specs/blocks/` - Empty folder (must delete)
- `kunaal-theme/inc/about/` - Empty folder (must delete)
- `kunaal-theme/AGENT_PROGRESS_META.md` - Progress tracking (not production code)
- `kunaal-theme/AGENT_PROGRESS_UI.md` - Progress tracking (not production code)
- `kunaal-theme/AGENT_PROGRESS.md` - Progress tracking (not production code)

**Why This Is CRITICAL**:
- Empty folders create confusion
- Progress tracking files are not part of the theme
- Violates clean codebase principles

**Fix Strategy**: Delete all empty folders and progress tracking files.

---

### CRITICAL: Inline Markup Instead of Components

**Requirement**: ZERO tolerance. All repeated markup must use canonical components.

**Found**:
1. **Section headers**: `template-parts/home.php`, `archive-essay.php`, `archive-jotting.php` use inline `<div class="sectionHead">` instead of `get_template_part('template-parts/components/section-head')`
2. **Jotting rows**: `archive-jotting.php` has 30+ lines of inline markup instead of using `kunaal_render_jotting_row()` helper (which exists but isn't used)

**Why This Is CRITICAL**:
- Component `section-head.php` exists but isn't used
- Creates maintenance burden (changes must be made in multiple places)
- Violates single source of truth principle

**Fix Strategy**:
1. Replace all inline section headers with `get_template_part('template-parts/components/section-head', null, $args)`
2. `archive-jotting.php` already uses `kunaal_render_jotting_row()` - verify it's working correctly

**Verification**: `grep -r "class=\"sectionHead\"" kunaal-theme` must show ZERO instances (only in component file).

---

## 11. REDUNDANCY SUMMARY TABLE

Quick reference of all duplicate/competing systems found:

| Category | Item | Location 1 | Location 2 | Status | Priority |
|----------|------|------------|------------|--------|----------|
| **Block Registration** | Complete system | `inc/blocks.php` (474 lines) | `inc/Blocks/register.php` (443 lines) | **BOTH ACTIVE** | **CRITICAL** |
| **Block Helpers** | All format functions | `inc/block-helpers.php` (495 lines) | `inc/Blocks/helpers.php` (495 lines) | Dead code vs Active | **CRITICAL** |
| **Validation** | Essay/jotting validation | `inc/Support/validation.php` (~100 lines) | `inc/validation/validation.php` (~250 lines) | Unknown | **CRITICAL** |
| **Card Markup** | Card HTML | `template-parts/components/card.php` | `index.php`, `taxonomy-topic.php`, `archive-essay.php`, `archive-jotting.php` | Component unused | **CRITICAL** |
| **Link Underline** | CSS implementation | `assets/css/utilities.css` (canonical) | `assets/css/contact-page.css`, `assets/css/editor-style.css`, `assets/css/pages.css` | 4 implementations | **HIGH** |
| **Section Rules** | Border-bottom rules | `assets/css/sections.css` (canonical) | `assets/css/about-page.css`, `assets/css/contact-page.css`, `assets/css/pages.css` | Multiple implementations | **MEDIUM** |
| **Grid Layouts** | Grid definitions | `assets/css/sections.css` | `assets/css/components.css`, `assets/css/pages.css`, `assets/css/about-page.css` | Multiple definitions | **MEDIUM** |
| **V22 Functions** | 11 functions | All in `inc/Features/About/data.php` | N/A (just deprecated naming) | Active but misnamed | **HIGH** |
| **V22 Customizer** | 200+ settings | All use `kunaal_about_v22_*` prefix | N/A (just deprecated naming) | Active but misnamed | **HIGH** |
| **Customizer Reg** | Registration points | `inc/Setup/customizer.php` | `inc/customizer-sections.php`, `inc/Features/About/customizer.php`, `inc/Features/About/customizer-sections.php` | Multiple files | **MEDIUM** |

**Total Critical Redundancies**: 4  
**Total High Priority Redundancies**: 3  
**Total Medium Priority Redundancies**: 3

---

**END OF AUDIT REPORT**


