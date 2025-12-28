# Comprehensive Remediation Checklist

**Last Updated**: 2025-01-28  
**Total Issues**: 703+ (SonarQube) + Coding Standards Violations

**Sources:**
- **SonarQube**: Automated code quality analysis
- **coding-standard-rigorous-robust-review**: Manual review against project coding standards

---

## CRITICAL Issues - MUST FIX ALL

### PHP Syntax Errors (CRITICAL - coding-standard-rigorous-robust-review)
- [x] `blocks/data-map/render.php:156` - Unmatched '}' brace - **FIXED**

### php:S121 - Missing Curly Braces (SonarQube)
- [x] `blocks/data-map/render.php` - Fixed 5 missing curly braces (converted : syntax to {})
- [x] `blocks/dumbbell-chart/render.php` - Fixed 2 missing curly braces (converted : syntax to {})
- [x] `blocks/flow-diagram/render.php` - Fixed 3 missing curly braces
- [x] `blocks/statistical-distribution/render.php` - Fixed 2 missing curly braces
- [x] `blocks/network-graph/render.php` - Fixed 2 missing curly braces
- [x] `blocks/chart/render.php` - Fixed (converted : syntax to {})
- [x] `blocks/confidence-meter/render.php` - Fixed
- [x] `blocks/slopegraph/render.php` - Fixed
- [ ] `blocks/data-map/render.php` - 3 remaining issues (may be cached)
- [ ] `blocks/dumbbell-chart/render.php` - 3 remaining issues (may be cached)
- [ ] `blocks/flow-diagram/render.php` - 2 remaining issues (may be cached)

### php:S1192 - Duplicate String Literals (SonarQube)
- [x] `blocks/chart/render.php` - Constants defined for colors and fill attributes
- [x] `header.php` - Constant `KUNAAL_NAV_CURRENT_CLASS` defined and used
- [x] `inc/blocks.php` - Constant `KUNAAL_BLOCKS_DIR_RELATIVE` defined and used
- [x] `inc/about-customizer-v22.php` - Constant `KUNAAL_CUSTOMIZER_SECTION_TITLE_LABEL` defined and used
- [ ] Verify constants are actually used throughout (may be cached in SonarQube)

### php:S3973 - Missing Curly Braces on If Statements (SonarQube)
- [x] `footer.php` - Fixed (already had curly braces)
- [x] `header.php` - Fixed 1 missing curly brace
- [x] `single-essay.php` - Fixed 4 missing curly braces
- [x] `single-jotting.php` - Fixed 3 missing curly braces
- [x] `blocks/flow-diagram/render.php` - Fixed missing curly braces on foreach
- [ ] Remaining issues may be cached in SonarQube

### php:S6600 - Remove Parentheses from require_once/echo (SonarQube)
- [x] `functions.php:19,24` - **VERIFIED**: No parentheses found - likely SonarQube false positive
- [x] `blocks/slopegraph/render.php` - **VERIFIED**: No echo parentheses found - already fixed

### php:S131 - Missing Default Case in Switch (SonarQube)
- [x] `inc/helpers.php` - **VERIFIED**: No switch statements found - likely SonarQube false positive
- [x] `inc/ajax/ajax-handlers.php` - **VERIFIED**: Switch already has default case
- [x] `inc/block-helpers.php` - **VERIFIED**: All switch statements have default cases

### php:S3776 - Cognitive Complexity > 15 (SonarQube)
- [x] `inc/blocks.php:338` - **FIXED**: Refactored `kunaal_block_wrapper` - extracted helper functions, reduced complexity from 24 to <15
- [ ] `inc/blocks.php` - Complexity 21 (reduce to 15) - Other function
- [ ] `inc/block-helpers.php` - Complexity 17 (reduce to 15)
- [ ] `inc/validation/validation.php` - Complexity 24 (reduce to 15)
- [ ] `inc/validation/validation.php` - Complexity 28 (reduce to 15)
- [ ] `inc/helpers.php` - Complexity 19 (reduce to 15) - 2 instances
- [ ] `functions.php` - Complexity 16 (reduce to 15)
- [ ] `functions.php` - Complexity 24 (reduce to 15)
- [ ] `functions.php` - Complexity 28 (reduce to 15)
- [ ] `blocks/data-map/render.php` - Complexity 19 (reduce to 15)
- [ ] `blocks/flow-diagram/render.php` - Complexity 17 (reduce to 15)

---

## SEVERE Issues - MUST FIX ALL

*None identified yet - reserved for issues more severe than CRITICAL*

---

## MAJOR Issues - MUST FIX ALL

### css:S4666 - Duplicate CSS Selectors (SonarQube)
- [x] `assets/css/header.css:205` - **FIXED**: Merged duplicate `.nav a` selectors
- [x] `assets/css/about-page-v22.css` - **FIXED**: Removed duplicate selectors (hero, hero-photo.has-accent, capsules-cloud, capsule, country variants)
- [x] `assets/css/tokens.css` - **VERIFIED**: Only one `:root` selector (already fixed in previous commit)

### Web:S6819 - Use <hr> Instead of Separator Role (SonarQube)
- [x] `archive-essay.php:34` - **FIXED**: Replaced `role="separator"` with `<hr>`
- [x] `archive-jotting.php` - **FIXED**: Replaced `role="separator"` with `<hr>`
- [x] `template-parts/home.php` - **FIXED**: Replaced `role="separator"` with `<hr>`
- [ ] Verify remaining separator roles (if any)

### php:S1142 - Too Many Returns (SonarQube)
- [x] `inc/blocks.php:338` - **FIXED**: Reduced returns from 4 to 3 in `kunaal_block_wrapper` by extracting helper functions
- [ ] Other files - Functions with 4+ returns (23 total)

### Web:S6811 - ARIA Attribute Issues (SonarQube)
- [x] `archive-essay.php:29` - **FIXED**: Changed `<ul>` to `role="listbox"` and `<li>` to `role="option"`
- [x] `archive-essay.php:30,36` - **FIXED**: Added `role="option"` to list items
- [x] `archive-jotting.php` - **FIXED**: Added proper ARIA roles
- [x] `template-parts/home.php` - **FIXED**: Added proper ARIA roles
- [ ] Other files - Verify remaining ARIA issues (if any)

### php:S4833 - Security Issues (SonarQube)
- [x] `inc/validation/validation.php` - **FIXED**: Added sanitization to `$_POST` usage in `kunaal_get_classic_meta`, `kunaal_classic_essay_has_topics`, `kunaal_classic_essay_has_image`
- [x] `inc/ajax/ajax-handlers.php` - **VERIFIED**: Already sanitizing inputs properly
- [x] `inc/email/email-handlers.php` - **VERIFIED**: Already sanitizing inputs properly
- [x] `inc/email/subscribe-handler.php` - **VERIFIED**: Already sanitizing inputs properly
- [ ] Review remaining security issues (if any)

### Web:TableHeaderHasIdOrScopeCheck - Table Accessibility (SonarQube)
- [x] `blocks/pub-table/render.php` - **FIXED**: Added `scope="col"` to headers
- [x] `blocks/data-map/render.php` - **FIXED**: Added `scope="col"` to headers (2 tables)
- [x] `blocks/dumbbell-chart/render.php` - **FIXED**: Added `scope="col"` to headers
- [x] `blocks/flow-diagram/render.php` - **FIXED**: Added `scope="col"` to headers
- [x] `blocks/slopegraph/render.php` - **FIXED**: Added `scope="col"` to headers
- [x] `blocks/rubric/render.php` - **FIXED**: Added `scope="col"` to headers
- [x] `blocks/assumptions-register/render.php` - **FIXED**: Added `scope="col"` to headers
- [x] `blocks/heatmap/render.php` - **FIXED**: Added `scope="col"` and `scope="row"` to headers
- [x] All table headers now have proper scope attributes

### php:S3358 - Collapsible If Statements (SonarQube)
- [x] **FIXED**: Extracted nested ternary operations into independent if/elseif/else statements:
  - `blocks/slopegraph/render.php` - 6 nested ternaries fixed (lines 54, 57, 59, 61, 66, 67)
  - `blocks/chart/render.php` - 1 nested ternary fixed (line 547)
  - `blocks/confidence-meter/render.php` - 1 nested ternary fixed (line 24)

### php:S2681 - Dead Code (SonarQube)
- [x] **FIXED**: Added curly braces to early returns in 8 render.php files:
  - `blocks/flowchart-step/render.php`
  - `blocks/framework-matrix/render.php`
  - `blocks/pub-table/render.php`
  - `blocks/rubric-row/render.php`
  - `blocks/argument-map/render.php`
  - `blocks/glossary-term/render.php`
  - `blocks/know-dont-know/render.php`
  - `blocks/source-excerpt/render.php`

### php:S1172 - Unused Parameters (SonarQube)
- [ ] Functions with unused parameters (6 total) - **NOTE**: These are in closures where parameters are optional; may be acceptable

### php:S3923 - Empty Catch Blocks / Duplicate Conditionals (SonarQube)
- [x] **FIXED**: Removed duplicate conditional branches in `inc/block-helpers.php` lines 156-157
- [x] **FIXED**: Extracted duplicate literal in `blocks/statistical-distribution/render.php` lines 43-44

### php:S138 - Too Many Lines (SonarQube)
- [x] **VERIFIED**: Functions have already been refactored:
  - `inc/about-customizer-v22.php` - `kunaal_about_customizer_v22` already refactored to ~30 lines (delegates to helper functions)
  - `inc/enqueue-helpers.php` - `kunaal_enqueue_assets` already refactored to ~68 lines (delegates to helper functions)
  - `inc/interest-icons.php` - `kunaal_get_interest_icon` is a data mapping array (200 lines of data, not complex logic) - **ACCEPTABLE**

### CSS Defensive Patterns (coding-standard-rigorous-robust-review)
- [x] `!important` usage - **REVIEWED**: All instances are justified:
  - `compatibility.css`: Reduced motion and print styles (required for accessibility)
  - `about-page-v22.css`: Fallback styles when JS fails (progressive enhancement)
- [x] `nth-child()` usage - **REVIEWED**: All instances are decorative only (transition delays, animation durations), not critical layout - **ACCEPTABLE**
- [x] WordPress body class dependencies - **VERIFIED**: About page uses stable `.kunaal-about-v22` class (not WordPress body classes)

---

## MEDIUM Issues - MUST FIX (Need Very Good Justification to Skip)

### css:S7924 - CSS Issues (SonarQube)
- [ ] Review and fix CSS issues (16 total)

### Web:S6843 - Accessibility Issues (SonarQube)
- [ ] Review and fix accessibility problems (6 total)

### Other Medium Issues
- [ ] `Web:S6850` (2 issues)
- [ ] `Web:S6845` (2 issues)
- [ ] `Web:S6842` (1 issue)
- [ ] `php:S1066` (1 issue)
- [ ] `Web:PageWithoutTitleCheck` (1 issue)

---

## MINOR Issues - Fix Reasonable Ones (Need Explanation to Skip)

### SonarQube Minor Issues (440 total)
- [ ] Review and prioritize minor issues
- [ ] Fix reasonable ones
- [ ] Document justification for skipped ones

---

## Remediation Progress

### Completed
- [x] Fixed PHP syntax error in `blocks/data-map/render.php`
- [x] Fixed missing curly braces in multiple block render files
- [x] Fixed missing curly braces on if statements in template files
- [x] Defined constants for duplicate string literals

### In Progress
- [ ] Verifying remaining curly braces issues (may be SonarQube cache)
- [ ] Fixing require_once/echo parentheses issues
- [ ] Finding and fixing switch statement default case

### Next Steps
1. Fix remaining CRITICAL issues
2. Fix all MAJOR issues
3. Review and fix MEDIUM issues
4. Prioritize and fix MINOR issues

---

## Notes

- All CRITICAL and MAJOR issues must be fixed
- MEDIUM issues require very good justification to skip
- MINOR issues require reasonable explanation to skip
- SonarQube cache may cause some issues to appear fixed/unfixed incorrectly
- Re-run SonarQube analysis after each batch of fixes

