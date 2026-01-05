# Tech Debt Register

This document tracks known technical debt in the codebase. Review this before starting work and update it with every commit.

**Last Updated**: 2025-01-27

---

## How to Use This Document

### Before Coding
1. Check if any items relate to the area you're working in
2. Plan to resolve related items as part of your work

### After Coding
1. Add any new tech debt discovered or created
2. Remove items you've resolved
3. Update the "Last Updated" date

### Priority Levels
- **Critical**: Security issues, data loss risks, blocking bugs — resolve immediately
- **High**: Significant performance issues, major code smells, frequent pain points — resolve within current sprint
- **Medium**: Code quality issues, minor performance concerns — resolve when working in the area
- **Low**: Nice-to-have improvements, minor inconsistencies — resolve opportunistically

---

## Critical Priority

_None currently logged._

---

## High Priority

### [SonarCloud] Exported issues include files that do not exist (stale / wrong-branch analysis)
- **Added**: 2026-01-05
- **Severity**: High
- **Impact**: Sonar remediation loop cannot be completed deterministically; exports list CRITICAL/MAJOR issues for files that are absent locally (e.g. `kunaal-theme/inc/validation/validation.php`, `homepage-test.html`).
- **Evidence**: `python scripts/sonar-export.py` + `python scripts/analyze-sonar-issues.py` reports 101 CRITICAL, including missing paths.
- **Suggested fix**:
  - Confirm SonarCloud project’s main branch / analysis target matches `main`.
  - Trigger a fresh analysis for `main` and verify issues for removed files disappear.
  - Update `scripts/sonar-export.py` / analyzers to filter to `main` branch and/or “new code” period, and to flag missing paths as “stale” instead of treating as actionable.
  - Optionally add a CI gate: fail if exported issues reference non-existent files (signals stale export).

<!-- Example entry:
### [Hardcoding] Hardcoded site URLs
- **Location**: `functions.php:45`, `inc/utilities.php:120`
- **Added**: YYYY-MM-DD
- **Description**: Site URLs are hardcoded instead of using `home_url()` or `site_url()`. This will break in staging/local environments.
- **Suggested fix**: Replace all hardcoded URLs with appropriate WordPress functions.
-->

---

## Medium Priority

_None currently logged._

<!-- Example entry:
### [Duplication] Multiple mobile menu implementations
- **Location**: `assets/js/navigation.js`, `assets/js/header.js`
- **Added**: YYYY-MM-DD
- **Description**: Two different scripts handle mobile menu functionality with slightly different approaches.
- **Suggested fix**: Consolidate into a single, configurable module.
-->

---

## Low Priority

_None currently logged._

<!-- Example entry:
### [Naming] Inconsistent function prefixes
- **Location**: Various files in `/inc`
- **Added**: YYYY-MM-DD
- **Description**: Some functions use `theme_` prefix, others use `mytheme_`, some have no prefix.
- **Suggested fix**: Standardize all function prefixes to `theme_slug_` during refactoring.
-->

---

## Resolved Items

Track recently resolved items here for reference, then archive monthly.

| Item | Location | Resolved Date | Resolution |
|------|----------|---------------|------------|
| Duplicate validation file | `inc/validation/validation.php` | 2025-01-27 | Deleted duplicate - `inc/Support/validation.php` is canonical |
| Inline jotting markup | `archive-jotting.php` | 2025-01-27 | Replaced with `kunaal_render_jotting_row()` helper function |
| Inline card markup | `index.php`, `taxonomy-topic.php` | 2025-01-27 | Replaced with `kunaal_render_essay_card()` helper function |
| Duplicate CSS underline implementations | `contact-page.css`, `editor-style.css` | 2025-01-27 | Removed duplicates - now uses canonical pattern from `utilities.css` |
| V22 function naming | `inc/Features/About/data.php`, `customizer.php`, `page-about.php`, `enqueue.php`, `customizer-sections.php` | 2025-01-27 | Renamed all 11 functions to remove `_v22` suffix |

---

## Notes

- When resolving debt, update the commit message to reference this document
- If a debt item is too large for a single commit, break it into sub-tasks and track progress here
- Don't let this document become stale — it's only useful if it's accurate
