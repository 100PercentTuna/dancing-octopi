# SonarQube Remediation Checklist

**Last Updated**: 2025-01-XX  
**Total Issues**: 703 (77 Critical, 186 Major, 440 Minor)

## CRITICAL Issues (77) - MUST FIX ALL

### php:S121 - Missing Curly Braces (39 issues)
- [x] `blocks/data-map/render.php` - Fixed 3 missing curly braces
- [x] `blocks/dumbbell-chart/render.php` - Fixed 3 missing curly braces
- [x] `blocks/flow-diagram/render.php` - Fixed 3 missing curly braces
- [x] `blocks/statistical-distribution/render.php` - Fixed 2 missing curly braces
- [x] `blocks/network-graph/render.php` - Fixed 2 missing curly braces
- [ ] `blocks/chart/render.php` - Multiple missing curly braces (needs conversion from : syntax)
- [ ] `blocks/confidence-meter/render.php` - 3 missing curly braces
- [ ] `blocks/slopegraph/render.php` - 1 missing curly brace
- [ ] Other files - remaining issues

### php:S1192 - Duplicate String Literals (13 issues)
- [ ] `blocks/chart/render.php:117` - `"" fill=""` duplicated 8 times
- [ ] `blocks/chart/render.php` - `"#3B82F6"` duplicated 3 times
- [ ] `blocks/chart/render.php` - `"#10B981"` duplicated 3 times
- [ ] `header.php` - `" current"` duplicated 4 times
- [ ] `inc/blocks.php` - `"/blocks"` duplicated 3 times
- [ ] `inc/about-customizer-v22.php` - `"Section Title"` duplicated 3 times

### php:S3776 - Cognitive Complexity > 15 (12 issues)
- [ ] `inc/blocks.php:341` - Complexity 24 (reduce to 15)
- [ ] `inc/blocks.php` - Complexity 21 (reduce to 15)
- [ ] `inc/block-helpers.php` - Complexity 17 (reduce to 15)
- [ ] `inc/validation/validation.php` - Complexity 24 (reduce to 15)
- [ ] `inc/validation/validation.php` - Complexity 28 (reduce to 15)
- [ ] `inc/helpers.php` - Complexity 19 (reduce to 15) - 2 instances
- [ ] `functions.php` - Complexity 16 (reduce to 15)
- [ ] `functions.php` - Complexity 24 (reduce to 15)
- [ ] `functions.php` - Complexity 28 (reduce to 15)
- [ ] `blocks/data-map/render.php` - Complexity 19 (reduce to 15)
- [ ] `blocks/flow-diagram/render.php` - Complexity 17 (reduce to 15)

### php:S3973 - Missing Curly Braces on If Statements (9 issues)
- [ ] `footer.php` - Missing curly braces
- [ ] `header.php` - 2 missing curly braces
- [ ] `single-essay.php` - 2 missing curly braces
- [ ] `single-jotting.php` - 2 missing curly braces
- [ ] `blocks/flow-diagram/render.php` - Missing curly braces on foreach

### php:S6600 - Remove Parentheses from require_once/echo (3 issues)
- [ ] `functions.php` - 2 require_once parentheses
- [ ] `blocks/slopegraph/render.php` - echo parentheses

### php:S131 - Missing Default Case in Switch (1 issue)
- [ ] `inc/helpers.php` - Add default case to switch statement

---

## MAJOR Issues (186) - MUST FIX ALL

### css:S4666 - Duplicate CSS Selectors (40 issues)
- [ ] `assets/css/header.css:205` - Duplicate `.nav a` selector
- [ ] `assets/css/about-page-v22.css` - Multiple duplicate selectors:
  - `.kunaal-about-v22 .hero` (line 223, first at 40)
  - `.kunaal-about-v22 .hero-photo.has-accent` (lines 309, 343, first at 308)
  - `.kunaal-about-v22 .capsules-cloud` (line 741, first at 740)
  - `.kunaal-about-v22 .capsules-cloud::before` (line 751, first at 750)
  - `.kunaal-about-v22 .capsule` (line 758, first at 757)
  - `.kunaal-about-v22 .capsule:hover` (line 771, first at 770)
  - `.kunaal-about-v22 .capsule::after` (line 823, first at 822)
  - `.kunaal-about-v22 .country` (line 1137, first at 1136)
  - `.kunaal-about-v22 .country.visited` (line 1144, first at 1143)
  - `.kunaal-about-v22 .country.lived` (line 1148, first at 1147)
  - `.kunaal-about-v22 .country.current` (line 1152, first at 1151)
  - `.kunaal-about-v22 .country.visited:hover` (line 1157, first at 1156)
  - `.kunaal-about-v22 .country.lived:hover` (line 1161, first at 1160)
  - Dark theme variants (lines 1167-1188)
  - `.kunaal-about-v22 .map-legend-dot.current` (line 1256, first at 1255)
- [ ] `assets/css/tokens.css` - Duplicate `:root` selector

### Web:S6819 - Use <hr> Instead of Separator Role (35 issues)
- [ ] `archive-essay.php:34` - Replace separator role with `<hr>`
- [ ] Multiple other files - Replace separator roles

### php:S1142 - Too Many Returns (23 issues)
- [ ] `inc/blocks.php:341` - 4 returns (max 3 allowed)
- [ ] Other files - Functions with 4+ returns

### css:S7924 - CSS Issues (16 issues)
- [ ] Review and fix CSS issues

### Web:TableHeaderHasIdOrScopeCheck - Table Accessibility (10 issues)
- [ ] Tables missing `id` or `scope` attributes on headers

### php:S4833 - Security Issues (10 issues)
- [ ] Review and fix security vulnerabilities

### Web:S6811 - ARIA Attribute Issues (9 issues)
- [ ] `archive-essay.php:29` - `aria-multiselectable` not supported by list role
- [ ] `archive-essay.php:30` - `aria-selected` not supported by listitem role
- [ ] `archive-essay.php:36` - `aria-selected` not supported by listitem role
- [ ] `archive-jotting.php` - Similar ARIA issues
- [ ] Other files - ARIA attribute issues

### php:S3358 - Collapsible If Statements (8 issues)
- [ ] Files with nested ifs that can be combined

### php:S2681 - Dead Code (8 issues)
- [ ] Remove unreachable code

### php:S1172 - Unused Parameters (6 issues)
- [ ] Functions with unused parameters

### Web:S6843 - Accessibility Issues (6 issues)
- [ ] Review and fix accessibility problems

### php:S3923 - Empty Catch Blocks (4 issues)
- [ ] Add error handling to empty catch blocks

### php:S138 - Too Many Lines (3 issues)
- [ ] Functions exceeding line limits

### Other Major Issues
- [ ] `Web:S6850` (2 issues)
- [ ] `Web:S6845` (2 issues)
- [ ] `python:S3457` (1 issue)
- [ ] `Web:S6842` (1 issue)
- [ ] `php:S1066` (1 issue)
- [ ] `Web:PageWithoutTitleCheck` (1 issue)

---

## MEDIUM Issues - MUST FIX (Need Very Good Justification to Skip)

*To be populated after reviewing full report*

---

## MINOR Issues (440) - Fix Reasonable Ones (Need Explanation to Skip)

*To be populated after reviewing full report*

---

## Remediation Progress

### Completed
- None yet

### In Progress
- Starting with CRITICAL issues

### Notes
- All CRITICAL and MAJOR issues must be fixed
- MEDIUM issues require very good justification to skip
- MINOR issues require reasonable explanation to skip

