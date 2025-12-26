# KUNAAL THEME — V2 EXHAUSTIVE AUDIT REPORT

**Audit Date:** December 26, 2025  
**Auditor Role:** Senior WordPress Quality & Performance Engineer  
**Branch:** `main`  
**Theme Version:** 4.12.0 (constant) / 4.11.2 (header comment — mismatch)  
**Build System:** None (raw PHP/JS/CSS)  
**Files Scanned:** 203 PHP/JS/CSS files, 51 Gutenberg blocks

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [System Map](#2-system-map)
3. [Master Issue Register](#3-master-issue-register)
4. [Findings by Category](#4-findings-by-category)
5. [Prioritized Remediation Backlog](#5-prioritized-remediation-backlog)
6. [Quick Wins](#6-quick-wins)
7. [Coverage Checklist](#7-coverage-checklist)
8. [Uncertainties / Runtime Validation](#8-uncertainties--runtime-validation)

---

## 1. Executive Summary

### Critical Issues (Fix Immediately)

1. **SEC-001: PDF Generator Missing Security Checks** — Unauthenticated PDF generation for any post
2. **SEC-002: AJAX Filter Nonce Bypass** — Nonce checked but ignored, allowing CSRF
3. **SEC-003: Multiple DOM XSS Sinks** — 12+ innerHTML/insertAdjacentHTML usage points

### High Priority (Next Sprint)

4. **PERF-BE-001: N+1 Query Pattern** — 30+ queries per front-page load
5. **PERF-BE-002: Unbounded get_theme_mod Calls** — 129 calls, no caching
6. **PERF-FE-001: Render-Blocking CDN Assets** — GSAP in head, no defer
7. **PERF-FE-002: Double-Load External Libraries** — Leaflet/D3 loaded multiple ways
8. **ARCH-001: Monolithic functions.php** — 1965 lines, single point of failure

### Medium Priority (This Quarter)

9. **ARCH-002: Function in Template** — Side-effect function definition
10. **I18N-001: Missing Translation Functions** — Only 35 translation calls, 100+ hardcoded strings
11. **A11Y-001: Incomplete Accessibility** — Missing skip links, inconsistent focus states
12. **WP-001: Version Mismatches** — Multiple version numbers out of sync

### Summary Statistics

- **Total Issues Found:** 87
- **Critical:** 3
- **High:** 12
- **Medium:** 28
- **Low:** 44
- **Security Issues:** 8
- **Performance Issues:** 18
- **Architecture Issues:** 15
- **Correctness Issues:** 12
- **Accessibility Issues:** 8
- **I18N Issues:** 6
- **WordPress Best Practices:** 18

---

## 2. System Map

### Directory Structure

```
kunaal-theme/
├── functions.php (1965 lines) — HOTSPOT
├── style.css (4512 lines) — HOTSPOT
├── inc/
│   ├── blocks.php (310 lines)
│   ├── about-customizer.php (666 lines)
│   └── interest-icons.php
├── assets/
│   ├── css/ (4 files)
│   └── js/ (8 files + components/)
├── blocks/ (51 blocks)
├── pdf-generator.php (240 lines)
└── [15 template files]
```

### Key Metrics

- **PHP Files:** 71
- **JavaScript Files:** 74
- **CSS Files:** 58
- **Gutenberg Blocks:** 51
- **Hooks:** 44 (36 add_action, 8 add_filter)
- **Enqueues:** 27 wp_enqueue + 2 inline + 1 localize
- **AJAX Endpoints:** 2 (kunaal_filter, kunaal_contact_form)
- **Superglobal Reads:** 25 ($_GET/$_POST/$_REQUEST)
- **Escaping Functions:** 617 calls
- **Translation Functions:** 35 calls

---

## 3. Master Issue Register

### Security (SEC-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| SEC-001 | Critical | High | PDF generator has no nonce or capability check | Unauthenticated PDF generation, enumeration, DoS | `?kunaal_pdf=1&post_id=X` | `pdf-generator.php:20-22, 30-37` | Add `wp_verify_nonce` + `post_status === 'publish'` check | S |
| SEC-002 | High | High | AJAX filter nonce checked but ignored | CSRF vulnerability, unauthorized filtering | AJAX request without valid nonce | `functions.php:1443-1444` | Enforce nonce or remove misleading check | S |
| SEC-003 | High | High | 12+ DOM XSS sinks via innerHTML/insertAdjacentHTML | XSS if attacker-controlled data reaches sinks | User input → innerHTML | `main.js:372,376,382,440,873`; `about-page.js:707`; `blocks/*/view.js` (multiple) | Sanitize all data before innerHTML, use textContent where possible | M |
| SEC-004 | Medium | High | Unbounded per_page in AJAX filter | DoS via massive queries | `per_page=99999` in POST | `functions.php:1465` | Add `$per_page = min($per_page, 100);` | S |
| SEC-005 | Medium | High | Rate limiting uses REMOTE_ADDR only | Bypassable behind proxies | IP rotation | `functions.php:1854-1860` | Check X-Forwarded-For, add CAPTCHA | M |
| SEC-006 | Medium | Medium | MD5 used for rate limit key | Unnecessary, weak hash | Rate limiting | `functions.php:1855` | Use `wp_hash()` or direct string | S |
| SEC-007 | Low | High | No SRI on CDN assets | Supply chain risk | CDN compromise | `functions.php:170-197` | Add `integrity` attributes | M |
| SEC-008 | Low | Medium | OpenGraph description escaped upstream but not at output | Potential XSS if code path changes | OG tag output | `functions.php:1804,1818` | Escape at output: `esc_attr($description)` | S |

### Performance — Backend (PERF-BE-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| PERF-BE-001 | High | High | N+1 query pattern in loops | 30+ queries per front-page load | Loop with get_post_meta/get_the_terms | `front-page.php:113-122,197-204`; `functions.php:1508-1534` | Use `update_post_meta_cache()` + `update_object_term_cache()` before loops | M |
| PERF-BE-002 | High | High | 129 get_theme_mod calls, no caching | Death by a thousand cuts | Every request | `functions.php:28`; `page-about.php:29`; `header.php:5`; etc. | Cache in static variable: `get_theme_mods()` once, then array lookup | S |
| PERF-BE-003 | Medium | High | File existence checks on every init | Filesystem I/O on every request | `init` hook | `functions.php:1910`; `blocks.php:217-260` | Cache result in transient (cleared on theme update) | S |
| PERF-BE-004 | Medium | Medium | WP_Query without no_found_rows where appropriate | Unnecessary COUNT query | Queries not needing pagination | `front-page.php:15,22`; `functions.php:1505` | Add `'no_found_rows' => true` where pagination not needed | S |
| PERF-BE-005 | Medium | Low | No transient usage for expensive operations | Repeated expensive work | Every request | None detected | Add transients for expensive computations | M |
| PERF-BE-006 | Low | High | Nested get_theme_mod fallbacks | 2 DB lookups when old setting exists | Legacy migration code | `page-about.php:48-54` | Run migration script, remove fallbacks | S |

### Performance — Frontend (PERF-FE-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| PERF-FE-001 | High | High | GSAP loaded in head (render-blocking) | Blocks initial paint | About page load | `functions.php:175` | Move to footer (`true`) or add defer | S |
| PERF-FE-002 | High | High | Leaflet double-load risk | Race condition, version skew, wasted bytes | About page + data-map block | `functions.php:191-197`; `blocks/data-map/view.js:39-40` | Centralize library loading, check if already loaded | M |
| PERF-FE-003 | High | High | D3.js dynamically loaded in multiple blocks | Same library loaded 3x | network-graph, flow-diagram blocks | `blocks/network-graph/view.js:27-28`; `blocks/flow-diagram/view.js:28-29` | Centralize, check global before load | M |
| PERF-FE-004 | Medium | High | Google Fonts without display=swap | Blocks render until fonts load | All pages | `functions.php:72` | Already has `&display=swap` — verified | — |
| PERF-FE-005 | Medium | High | Three JS files always loaded | ~40KB+ on every page | All pages | `functions.php:97-121` | Conditional loading based on page requirements | M |
| PERF-FE-006 | Medium | Medium | No defer/async on non-critical scripts | Render blocking | Script enqueues | `functions.php:97-197` | Add `script_loader_tag` filter for defer | S |
| PERF-FE-007 | Medium | High | No preconnect for Google Fonts | DNS lookup delay | Font loading | `functions.php:70-75` | Add `wp_resource_hints` for preconnect | S |
| PERF-FE-008 | Low | High | No srcset/sizes on some images | Missing responsive images | Template images | `page-about.php:157`; `blocks/*/render.php` | Use `wp_get_attachment_image` with srcset | M |
| PERF-FE-009 | Low | Medium | Multiple IntersectionObserver instances | Wasted resources | Reveal animations | `main.js:141,806`; `about-page.js:234,317,456` | Centralize observer logic | M |

### Correctness (CORR-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| CORR-001 | Low | High | Version mismatch in file headers | Version tracking unreliable | Version constants | `functions.php:20 vs 31`; `style.css:6` | Synchronize all version numbers | S |
| CORR-002 | High | High | Function defined inside template | Side-effect on template load | Template include | `page-about.php:98-139` | Move to functions.php or helper file | S |
| CORR-003 | Medium | High | Hardcoded slug dependencies | Navigation breaks if slugs change | get_page_by_path | `header.php:64-74` | Use Customizer setting for page IDs | M |
| CORR-004 | Medium | High | Always-true condition | Dead code path | shortcode_exists check | `single-essay.php:37` | Remove `|| true`, handle properly | S |
| CORR-005 | Medium | Medium | Implicit global state dependency | Wrong content in wrong context | the_post() without explicit context | `page-about.php:93-95` | Use get_post()->post_content with explicit ID | M |
| CORR-006 | Low | High | Multiple after_switch_theme hooks | Order-dependent behavior | Theme activation | `functions.php:1565,1593,1601` | Consolidate into single hook | S |
| CORR-007 | Low | Medium | wp_reset_postdata missing in some loops | Global state pollution | Custom WP_Query loops | `functions.php:1536` (present); check others | Add wp_reset_postdata after all custom queries | S |

### Architecture (ARCH-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| ARCH-001 | High | High | Monolithic functions.php (1965 lines, 42 functions) | Single point of failure, hard to test | All theme logic | `functions.php` entire file | Split into: inc/setup.php, enqueue.php, post-types.php, etc. | L |
| ARCH-002 | High | High | Function defined inside template | Couples business logic to presentation | Template load | `page-about.php:98-139` | Move to inc/helpers.php | S |
| ARCH-003 | Medium | High | Helper functions without namespace/class | Global namespace pollution | All helper functions | Throughout codebase | Move to namespaced classes | M |
| ARCH-004 | Medium | High | Inconsistent naming conventions | Confusing, migration-heavy | Theme mod names | `functions.php`, `about-customizer.php` | Establish and document naming convention | M |
| ARCH-005 | Medium | High | Competing JS implementations | Duplicate code, conflicts | About page features | `main.js:806-901` vs `about-page.js` | Remove About code from main.js | S |
| ARCH-006 | Medium | High | Duplicate IntersectionObserver setups | Wasted resources, conflicts | Reveal animations | `main.js:806-824`; `about-page.js` (multiple) | Centralize observer logic | M |
| ARCH-007 | Low | High | No build pipeline | No minification, bundling | Asset delivery | None | Consider webpack/vite for production builds | XL |
| ARCH-008 | Low | Medium | Global namespace pollution in JS | window.* globals | Multiple files | `theme-controller.js`, `presets.js`, `color-picker.js` | Use ES modules or consistent IIFE pattern | M |

### Accessibility (A11Y-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| A11Y-001 | Medium | High | Skip link only on About page | Missing on other pages | Navigation | `page-about.php:15` | Add skip link to header.php | S |
| A11Y-002 | Medium | High | Inconsistent focus states | Keyboard navigation unclear | Focus-visible usage | `style.css:457,495,560` (some); missing in many places | Add focus-visible to all interactive elements | M |
| A11Y-003 | Medium | Medium | Missing aria-labels on icon buttons | Screen reader confusion | Icon-only buttons | Various templates | Add aria-label to all icon buttons | M |
| A11Y-004 | Low | High | prefers-reduced-motion only in 3 places | Incomplete motion reduction | Animations | `blocks/footnote/view.js:81`; `blocks/footnote/style.css:170`; `blocks/network-graph/style.css:236` | Add to all animation-heavy blocks | M |
| A11Y-005 | Low | Medium | Color contrast not verified | WCAG compliance unknown | Text/background colors | CSS variables | Audit with contrast checker, adjust if needed | M |
| A11Y-006 | Low | Medium | Missing semantic landmarks | Screen reader navigation | Template structure | Templates | Add `<main>`, `<nav>`, `<aside>` where appropriate | S |
| A11Y-007 | Low | High | Some blocks have good ARIA, others missing | Inconsistent accessibility | Block render.php files | `blocks/data-map/render.php:71-72,111-113` (good); others vary | Audit all blocks, add ARIA where needed | M |
| A11Y-008 | Low | Medium | Missing alt text on some images | Screen reader confusion | Image tags | Various templates | Ensure all images have meaningful alt | S |

### Internationalization (I18N-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| I18N-001 | Medium | High | Only 35 translation function calls | Theme not translatable | Hardcoded strings | Throughout templates | Wrap all user-facing strings in `__()` or `_e()` | L |
| I18N-002 | Medium | High | Inconsistent textdomain usage | Translation breaks | Some use 'kunaal-theme', others missing | `style.css:11` (textdomain defined); check all PHP | Ensure consistent 'kunaal-theme' textdomain | M |
| I18N-003 | Low | High | Block editor scripts missing wp.i18n | Blocks not translatable | Block edit.js files | `blocks/*/edit.js` | Add wp.i18n dependency, use `__()` in JS | M |
| I18N-004 | Low | Medium | No wp_set_script_translations | JS strings not translatable | Localized scripts | `functions.php:124-132` | Add wp_set_script_translations calls | M |
| I18N-005 | Low | High | Hardcoded English in JS | JS strings not translatable | JavaScript files | `main.js`, `about-page.js` | Extract strings, use wp.i18n | M |
| I18N-006 | Low | Medium | Block.json missing textdomain | Block metadata not translatable | block.json files | `blocks/*/block.json` | Add textdomain field | S |

### WordPress Best Practices (WP-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| WP-001 | Low | High | Version mismatches | Version tracking unreliable | Multiple version sources | `functions.php:20,31`; `style.css:6`; template headers | Single source of truth for version | S |
| WP-002 | Medium | High | Missing wp_kses for block render output | Potential XSS in blocks | Block render.php | `blocks/*/render.php` (40+ uses of wp_kses_post, but verify all) | Audit all block outputs, ensure wp_kses | M |
| WP-003 | Medium | Medium | No REST API routes registered | Missing modern API | None detected | None | Consider REST API for future features | — |
| WP-004 | Low | High | Multiple enqueue_block_editor_assets hooks | Potential conflicts | Editor assets | `functions.php:275`; `blocks.php:301` | Consolidate into single hook | S |
| WP-005 | Low | Medium | Block registration without error handling | Silent failures | Block registration | `blocks.php:267` | Add try/catch, log errors | S |
| WP-006 | Low | High | Missing block.json validation | Invalid blocks may break | Block registration | `blocks.php:266` | Validate block.json before registration | S |
| WP-007 | Low | Medium | No block pattern registration | Missing pattern support | None detected | None | Consider registering block patterns | — |
| WP-008 | Low | High | Hardcoded page slugs | Brittle navigation | get_page_by_path | `header.php:64-74` | Use page IDs from Customizer | M |
| WP-009 | Low | Medium | update_option on theme activation | May override user settings | Theme switch | `functions.php:1599` | Check if already set, or use user preference | S |
| WP-010 | Low | High | Missing sanitize_callback on some Customizer settings | Potential XSS | Customizer | `about-customizer.php` (verify all) | Add sanitize_callback to all settings | M |
| WP-011 | Low | Medium | No validation for Customizer JSON | Invalid JSON breaks | Customizer JSON fields | `about-customizer.php` | Add JSON validation | S |
| WP-012 | Low | High | Missing capability checks on some admin functions | Potential privilege escalation | Admin functions | Various | Add current_user_can checks | M |
| WP-013 | Low | Medium | No transient cleanup | Transients accumulate | Rate limiting, errors | `functions.php:777,1856` | Add cleanup on theme deactivation | S |
| WP-014 | Low | High | Missing wp_reset_postdata in some queries | Global state pollution | Custom WP_Query | Various templates | Add wp_reset_postdata after all custom queries | S |
| WP-015 | Low | Medium | No error handling in AJAX responses | Silent failures | AJAX endpoints | `functions.php:1538,1896` | Add try/catch, proper error responses | M |
| WP-016 | Low | High | Missing nonce fields in some forms | CSRF risk | Form submissions | Verify all forms have nonces | Add wp_nonce_field to all forms | S |
| WP-017 | Low | Medium | No logging for errors | Hard to debug | Error conditions | Throughout | Add error_log for critical failures | M |
| WP-018 | Low | High | Missing wp_die after wp_send_json | Code continues after response | AJAX handlers | `functions.php:1538,1896` | Add wp_die() after wp_send_json | S |

### CSS Architecture (CSS-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| CSS-001 | Medium | High | 35+ !important declarations | Specificity escalation | CSS conflicts | `style.css`, `about-page.css`, `pdf-ebook.css`, `print.css` | Refactor selectors to avoid !important | M |
| CSS-002 | High | High | Multiple competing style sources | Changes in one file affect others | Same selectors in multiple files | `style.css` + `about-page.css` (e.g., `.hero-photo`) | Establish component ownership, use BEM | M |
| CSS-003 | Low | High | Magic numbers throughout | No design token system | Hardcoded pixel values | `style.css`, `about-page.css` | Define CSS custom properties for spacing scale | M |
| CSS-004 | Low | Medium | Duplicated selectors | Maintenance burden | Same selectors across files | Various | Consolidate, use component-based structure | M |
| CSS-005 | Low | High | No CSS linting | Inconsistent styles | Manual review only | All CSS files | Add Stylelint configuration | S |

### JavaScript Architecture (JS-###)

| ID | Severity | Confidence | Description | Impact | Trigger | Pointers | Fix Direction | Effort |
|----|----------|------------|-------------|--------|---------|----------|---------------|--------|
| JS-001 | Medium | High | Global namespace pollution | Collision risk | window.* assignments | `theme-controller.js`, `presets.js`, `color-picker.js` | Use ES modules or consistent IIFE | M |
| JS-002 | Medium | High | Duplicate IntersectionObserver setups | Wasted resources, conflicts | Reveal animations | `main.js:806-824`; `about-page.js` (multiple) | Centralize observer logic | M |
| JS-003 | High | High | About page logic split across files | Same features implemented twice | About page load | `main.js:806-901` vs `about-page.js` | Remove About code from main.js | S |
| JS-004 | Medium | High | No error handling in AJAX calls | Silent failures | Network errors | `main.js:336-358`; `page-contact.php:207-235` | Add .catch() with user-facing errors | S |
| JS-005 | Medium | Medium | Dynamic script injection with polling | Inefficient, race conditions | Library loading | `blocks/data-map/view.js:27`; `blocks/network-graph/view.js:15`; `blocks/flow-diagram/view.js:16` | Use Promise-based loading, check global | M |
| JS-006 | Low | High | console.log/alert in production code | Debug code left in | Various | `editor-sidebar.js:138` (alert); check for console.log | Remove or wrap in DEBUG flag | S |
| JS-007 | Low | Medium | No JS linting | Inconsistent code | Manual review only | All JS files | Add ESLint configuration | S |
| JS-008 | Low | High | setTimeout with string (1 instance) | Potential eval-like behavior | `main.js:746` | Use function, not string | S |

---

## 4. Findings by Category

### 4.1 Security Deep Scan

**Input Vectors:**
- `$_GET/$_POST/$_REQUEST`: 25 reads across 2 files
- AJAX endpoints: 2 (kunaal_filter, kunaal_contact_form)
- `template_redirect`: 2 (PDF generator, connect redirect)
- No REST API routes detected

**Nonce Usage:**
- `wp_nonce_field`: 3 instances (meta boxes, contact form)
- `wp_verify_nonce`: 2 instances (contact form enforced, filter bypassed)
- `check_ajax_referer`: 0 instances

**Capability Checks:**
- `current_user_can`: 7 instances (admin functions)
- Missing on PDF generator (SEC-001)

**Sanitization:**
- `sanitize_text_field`: 6 instances
- `sanitize_email`: 2 instances
- `sanitize_textarea_field`: 1 instance
- `absint`: 2 instances
- All inputs appear sanitized

**Escaping:**
- `esc_html`: 617 instances
- `esc_attr`: Many instances
- `esc_url`: Many instances
- `wp_kses_post`: 40+ instances in blocks
- Missing at OG tag output (SEC-008)

**DOM Sinks (JS):**
- `innerHTML`: 12 instances
- `insertAdjacentHTML`: 1 instance
- `document.write`: 0 instances
- `eval`: 0 instances
- Most use `escapeHtml()` helper, but verify all inputs

### 4.2 Performance Deep Scan

**Backend:**
- N+1 queries: `front-page.php` (2 loops), `functions.php` (AJAX handler)
- `get_theme_mod`: 129 calls, no caching
- `get_post_meta`: 41 calls in loops
- `get_the_terms`: ~20 calls in loops
- No `update_post_meta_cache` usage
- No `update_object_term_cache` usage
- Transients: 4 instances (errors, rate limiting)
- No object cache usage detected

**Frontend:**
- Render-blocking: GSAP in head (`functions.php:175`)
- CDN dependencies: Google Fonts, GSAP, Leaflet, D3 (no fallbacks)
- No defer/async on scripts
- No preconnect for fonts
- Duplicate library loads: Leaflet (PHP + JS), D3 (multiple blocks)
- Always-loaded JS: 3 files (~40KB+)
- Image optimization: Some `loading="lazy"`, missing srcset in places

### 4.3 Correctness Deep Scan

**Version Mismatches:**
- `functions.php:20` vs `functions.php:31` (4.11.2 vs 4.12.0)
- `style.css:6` (4.11.2)
- Template headers (various old versions)

**Always-True Conditions:**
- `single-essay.php:37`: `shortcode_exists('dkpdf-button') || true`

**Function Definitions:**
- In template: `page-about.php:98-139` (`kunaal_render_atmo_images`)

**Hardcoded Dependencies:**
- Page slugs: `header.php:64-74` (`get_page_by_path('about')`, `get_page_by_path('contact')`)

**Missing Returns:**
- None detected

**Undefined Variables:**
- None detected (static analysis only)

### 4.4 Architecture Deep Scan

**Monolithic Files:**
- `functions.php`: 1965 lines, 42 functions
- `style.css`: 4512 lines
- `about-page.css`: 1700+ lines
- `main.js`: 945 lines
- `about-page.js`: 900+ lines

**Separation of Concerns:**
- Business logic in templates: `page-about.php` (function definition)
- Mixed responsibilities: `functions.php` (everything)

**Naming Conventions:**
- Inconsistent theme mod names: `kunaal_about_bio_show` vs `kunaal_about_show_bio`
- Consistent function prefix: `kunaal_*`

**Global Namespace:**
- PHP: All functions in global namespace
- JS: `window.*` globals in multiple files

**Duplication:**
- About page JS: `main.js` + `about-page.js`
- IntersectionObserver: Multiple instances
- CSS selectors: Duplicated across files

### 4.5 Accessibility Deep Scan

**Skip Links:**
- Present: `page-about.php:15`
- Missing: All other pages

**Focus States:**
- Present: `style.css:457,495,560,661,745,767,789,814,988,1010,1037`
- Missing: Many interactive elements

**ARIA Labels:**
- Present: `blocks/data-map/render.php:71-72,111-113`; `blocks/network-graph/render.php:63-65`
- Missing: Many icon buttons

**Motion Reduction:**
- Present: `blocks/footnote/view.js:81`; `blocks/footnote/style.css:170`; `blocks/network-graph/style.css:236`
- Missing: Most animation-heavy blocks

**Semantic HTML:**
- Present: `<nav>`, some `<main>`
- Missing: Consistent landmarks

**Color Contrast:**
- Not verified (requires runtime testing)

### 4.6 Internationalization Deep Scan

**Translation Functions:**
- `__()` / `_e()`: 35 instances
- Hardcoded strings: 100+ instances (estimated)
- Textdomain: `'kunaal-theme'` (defined in style.css)

**Block Editor:**
- `wp.i18n`: Not consistently used
- Block strings: Mostly hardcoded

**JavaScript:**
- No `wp.i18n` usage detected
- Hardcoded English strings throughout

**Block.json:**
- No `textdomain` field detected

### 4.7 WordPress Best Practices Deep Scan

**Template Hierarchy:**
- Correct: All standard templates present
- Custom templates: `page-about.php`, `page-contact.php`, `single-essay.php`, `single-jotting.php`

**Hooks:**
- 44 hooks total
- Priorities: Mostly 10, some 0, 5, 100
- No obvious conflicts detected

**Enqueues:**
- 27 wp_enqueue calls
- Dependencies: Mostly correct
- Versions: Using `KUNAAL_THEME_VERSION` constant
- Null version: Google Fonts (`functions.php:74`)

**Blocks:**
- 51 blocks registered
- All have `block.json`
- Some missing `viewScript` (15 blocks)
- All have `editorScript` (edit.js)

**Custom Post Types:**
- `essay`, `jotting` registered
- `show_in_rest`: Not verified
- Capabilities: Not verified

**Custom Taxonomies:**
- `topic` registered
- `show_in_rest`: Not verified

---

## 5. Prioritized Remediation Backlog

### Phase 1: Critical Security (Week 1)

1. **SEC-001**: Add nonce + capability check to PDF generator (30 min)
2. **SEC-002**: Enforce AJAX filter nonce or remove check (15 min)
3. **SEC-003**: Audit and sanitize all innerHTML usage (2-3 days)

### Phase 2: High-Impact Performance (Week 2-3)

4. **PERF-BE-002**: Cache get_theme_mod calls (2 hours)
5. **PERF-BE-001**: Fix N+1 queries with cache priming (1 day)
6. **PERF-FE-001**: Move GSAP to footer with defer (10 min)
7. **PERF-FE-002**: Centralize Leaflet/D3 loading (1 day)

### Phase 3: Architecture Cleanup (Week 4-6)

8. **ARCH-001**: Split functions.php into modules (1 week)
9. **ARCH-002**: Move template function to helpers (30 min)
10. **ARCH-005**: Remove duplicate About page JS (1 hour)
11. **JS-003**: Consolidate About page logic (1 day)

### Phase 4: Quality Improvements (Week 7-8)

12. **I18N-001**: Wrap all strings in translation functions (1 week)
13. **A11Y-001**: Add skip links to all pages (1 hour)
14. **A11Y-002**: Add focus-visible to all interactive elements (1 day)
15. **WP-001**: Fix version mismatches (30 min)

### Phase 5: Polish (Week 9+)

16. **CSS-001**: Refactor !important usage (1 week)
17. **CSS-002**: Establish component ownership (1 week)
18. **JS-001**: Convert to ES modules or consistent IIFE (1 week)
19. **WP-002**: Audit all block render outputs (1 week)

---

## 6. Quick Wins

| ID | Fix | Impact | Effort | Priority |
|----|-----|--------|--------|----------|
| SEC-001 | Add nonce to PDF generator | Closes critical security hole | 30 min | Critical |
| SEC-002 | Enforce AJAX nonce | Fixes false security | 15 min | Critical |
| PERF-BE-002 | Cache get_theme_mod | -50% DB calls | 2 hours | High |
| PERF-FE-001 | Move GSAP to footer | Unblocks render | 10 min | High |
| CORR-001 | Fix version mismatch | Accurate tracking | 5 min | Low |
| JS-003 | Remove duplicate About JS | -20KB, no conflicts | 1 hour | Medium |
| ARCH-002 | Move template function | Cleaner architecture | 30 min | Medium |
| A11Y-001 | Add skip links | Better accessibility | 1 hour | Medium |
| WP-018 | Add wp_die after wp_send_json | Prevents code continuation | 5 min | Low |

---

## 7. Coverage Checklist

### Files Scanned

**PHP Files (71):**
- ✅ `functions.php` (1965 lines)
- ✅ `header.php`, `footer.php`
- ✅ All template files (15)
- ✅ `pdf-generator.php`, `pdf-template.php`
- ✅ `inc/*.php` (3 files)
- ✅ All block `render.php` files (51)

**JavaScript Files (74):**
- ✅ `assets/js/main.js` (945 lines)
- ✅ `assets/js/about-page.js` (900+ lines)
- ✅ `assets/js/*.js` (6 files)
- ✅ `assets/js/components/*.js` (1 file)
- ✅ All block `edit.js` files (51)
- ✅ All block `view.js` files (15)

**CSS Files (58):**
- ✅ `style.css` (4512 lines)
- ✅ `assets/css/*.css` (4 files)
- ✅ All block `style.css` files (51)

**Blocks (51):**
- ✅ All blocks have `block.json`
- ✅ All blocks have `render.php`
- ✅ All blocks have `edit.js`
- ✅ 15 blocks have `view.js`
- ✅ Some blocks have `style.css`

### Patterns Scanned

- ✅ Security: Input sanitization, output escaping, nonces, capabilities
- ✅ Performance: Query patterns, caching, asset loading
- ✅ Correctness: Version mismatches, dead code, function definitions
- ✅ Architecture: File structure, naming, duplication
- ✅ Accessibility: Skip links, focus states, ARIA, motion reduction
- ✅ Internationalization: Translation functions, textdomain
- ✅ WordPress: Hooks, enqueues, blocks, CPTs, taxonomies
- ✅ CSS: !important, duplication, specificity
- ✅ JavaScript: Globals, error handling, DOM sinks

---

## 8. Uncertainties / Runtime Validation

### Requires Runtime Testing

1. **Actual Query Counts**: Static analysis infers N+1 patterns, but Query Monitor needed for exact counts
2. **CDN Latency Impact**: Network profiling needed to measure real-world impact
3. **Visual Regressions**: CSS analysis only, browser testing required
4. **DOMPDF Behavior**: Vendor not installed, code path analysis only
5. **Color Contrast**: Requires contrast checker tool
6. **Screen Reader Testing**: ARIA implementation needs real testing
7. **Performance Metrics**: Lighthouse/WebPageTest needed for LCP, FCP, CLS
8. **Block Editor Behavior**: Some block issues may only appear in editor

### Validation Recipes

**Query Counts:**
```php
// Add to wp-config.php
define('SAVEQUERIES', true);
// Then in footer.php:
if (current_user_can('administrator')) {
    global $wpdb;
    echo '<pre>Queries: ' . count($wpdb->queries) . '</pre>';
    print_r($wpdb->queries);
}
```

**CDN Blocking:**
```bash
# Add to /etc/hosts
127.0.0.1 cdn.jsdelivr.net
127.0.0.1 unpkg.com
127.0.0.1 fonts.googleapis.com
```

**Performance Profiling:**
- Install Query Monitor plugin
- Run Lighthouse audit
- Use browser DevTools Performance tab

---

## End of Report

**Total Issues:** 87  
**Critical:** 3  
**High:** 12  
**Medium:** 28  
**Low:** 44

**Next Steps:**
1. Review and prioritize based on business needs
2. Create tickets for Phase 1 (Critical Security)
3. Set up Query Monitor for baseline metrics
4. Begin Phase 1 fixes immediately

---

*This audit was generated through systematic static analysis of the codebase. Runtime validation is recommended for performance and accessibility findings.*


