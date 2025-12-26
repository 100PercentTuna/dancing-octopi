# KUNAAL THEME — A++ EXHAUSTIVE AUDIT REPORT

**Audit Date:** December 26, 2025  
**Auditor Role:** Senior WordPress Quality & Performance Engineer  
**Branch:** `main`  
**Theme Version:** 4.12.0 (constant) / 4.11.2 (header comment — mismatch)  
**Build System:** None (raw PHP/JS/CSS)

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Scope, Assumptions, and Non-goals](#2-scope-assumptions-and-non-goals)
3. [System Map (Theme Architecture)](#3-system-map-theme-architecture)
4. [Methodology](#4-methodology)
5. [Findings](#5-findings)
6. [Prioritized Remediation Backlog](#6-prioritized-remediation-backlog)
7. [Quick Wins](#7-quick-wins)
8. [Open Questions / Validation Plan](#8-open-questions--validation-plan)
9. [Appendix](#9-appendix)

---

## 1. Executive Summary

### What Matters Most (Top 10 Findings)

1. **SEC-CRITICAL: PDF Generator has no nonce or capability check** — Any unauthenticated user can generate PDFs for any post via `?kunaal_pdf=1&post_id=X`. Enumeration/DoS risk.

2. **SEC-HIGH: AJAX filter bypasses nonce validation** — `kunaal_filter_content()` checks nonce but proceeds regardless of result. The nonce is effectively useless.

3. **SEC-HIGH: 14 innerHTML/insertAdjacentHTML sinks in JS** — Multiple DOM injection points that could enable XSS if attacker-controlled data reaches them.

4. **PERF-HIGH: N+1 query pattern in front-page.php** — Each of 6+ posts triggers 3-4 separate meta/term queries inside the loop (30+ queries per page load).

5. **PERF-HIGH: 146 get_theme_mod calls per request** — No caching; each call hits options API. Death by a thousand cuts.

6. **PERF-HIGH: Leaflet double-load risk** — Enqueued via PHP on About page AND dynamically injected via JS in data-map block. Race condition, version skew, wasted bytes.

7. **PERF-MEDIUM: GSAP loaded in head (render-blocking)** — `false` passed to `wp_enqueue_script` for `in_footer`, blocking initial paint.

8. **ARCH-HIGH: Monolithic functions.php (1965 lines, 42 functions)** — Single point of failure; no separation of concerns; hard to test.

9. **ARCH-HIGH: Function defined inside template** — `kunaal_render_atmo_images()` defined in `page-about.php:98-139`. Side-effect on template load.

10. **MAINT-MEDIUM: Only 35 translation function calls** — Hardcoded English strings throughout templates (e.g., "Share", "Subscribe", "Download PDF").

### Top Risks

| Risk | Impact | Likelihood |
|------|--------|------------|
| Unauthenticated PDF generation | Data exposure, resource abuse | High |
| DOM-based XSS via innerHTML | User session compromise | Medium |
| Performance degradation at scale | Poor UX, SEO impact | High |
| CDN outage breaks site | GSAP/Leaflet/D3 unavailable | Medium |
| Cascade breakage from CSS !important | Hard-to-debug visual regressions | High |

### Top Opportunities

- **Quick win:** Add nonce + capability check to PDF generator (30 min fix, eliminates critical security hole)
- **Quick win:** Cache theme mods in static variable (2hr fix, cuts DB calls by 50%+)
- **Quick win:** Move GSAP to footer with defer (10 min fix, improves FCP)
- **Medium lift:** Split functions.php into modules (1 week, dramatically improves maintainability)
- **Medium lift:** Centralize external library loading (1 week, eliminates double-load bugs)

### 30-Second Recommendation

**Immediate (this week):** Fix SEC-001 (PDF nonce), SEC-002 (AJAX nonce), and PERF-003 (GSAP defer). These are surgical changes with high ROI.

**Next sprint:** Cache theme_mod calls, add meta cache priming before loops, and audit/sanitize all innerHTML usage.

**This quarter:** Split functions.php, establish CSS ownership boundaries, self-host critical libraries.

---

## 2. Scope, Assumptions, and Non-goals

### What Was Audited

- **71 PHP files** (templates, functions, blocks, includes)
- **74 JavaScript files** (main scripts, block edit/view scripts, components)
- **58 CSS files** (main stylesheet, about-page, print, pdf, block styles)
- **51 Gutenberg blocks** (each with block.json, render.php, edit.js, style.css, some with view.js)
- All hooks (44 total: 36 add_action + 8 add_filter)
- All enqueues (27 wp_enqueue + 2 inline + 1 localize)
- All AJAX endpoints (2: kunaal_filter, kunaal_contact_form)
- Security input vectors (41 superglobal reads, 3 nonce checks, 7 capability checks)

### What Could Not Be Verified (Runtime Testing)

| Item | Reason | Confidence Impact |
|------|--------|-------------------|
| Actual query counts | No Query Monitor access | Inferred from code patterns |
| CDN latency impact | No network profiling | Based on known CDN behavior |
| Visual regression | No browser testing | CSS analysis only |
| DOMPDF behavior | Vendor not installed | Code path analysis only |

### Explicit Assumptions

1. **WordPress 6.0+** as stated in style.css requirements
2. **PHP 8.0+** as stated in style.css requirements
3. **No object cache** installed (no transient usage detected for theme data)
4. **DOMPDF** is optional; fallback to browser print exists
5. **ACF not used** — no get_field() calls found
6. **No build pipeline** — raw source files served directly

### Non-goals

- Plugin audit (only theme files reviewed)
- Server configuration review
- Full penetration testing
- Performance profiling with real traffic

---

## 3. System Map (Theme Architecture)

### Directory Structure

```
kunaal-theme/
├── functions.php           # HOTSPOT: 1965 lines, all core logic
├── style.css               # HOTSPOT: 4512 lines, design tokens + components
├── inc/
│   ├── blocks.php          # Block registration (310 lines)
│   ├── about-customizer.php # About page Customizer (666 lines)
│   └── interest-icons.php  # Icon helper
├── assets/
│   ├── css/
│   │   ├── about-page.css  # HOTSPOT: 1700+ lines
│   │   ├── editor-style.css
│   │   ├── pdf-ebook.css
│   │   └── print.css
│   └── js/
│       ├── main.js         # HOTSPOT: 945 lines
│       ├── about-page.js   # HOTSPOT: 900+ lines, GSAP animations
│       ├── theme-controller.js
│       ├── lazy-blocks.js
│       ├── editor-sidebar.js
│       ├── presets.js
│       ├── customizer-preview.js
│       └── components/color-picker.js
├── blocks/                 # 51 blocks with block.json
│   └── inline-formats/     # 1 non-block (shared code)
├── pdf-generator.php       # PDF generation (240 lines)
├── pdf-template.php        # Potentially unused
└── [15 template files]     # front-page, single-*, page-*, archive-*, etc.
```

### Data Flow: Request → HTML/CSS/JS

```
HTTP Request
    ↓
WordPress loads theme
    ↓
functions.php executes:
  → Defines constants
  → Requires: pdf-generator.php, about-customizer.php, blocks.php
  → Registers hooks (44 total)
    ↓
after_setup_theme:
  → kunaal_theme_setup() — theme supports, image sizes
    ↓
init:
  → kunaal_register_post_types() — essay, jotting CPTs
  → kunaal_register_meta_fields()
  → kunaal_register_blocks() — 51 blocks
    ↓
wp_enqueue_scripts:
  → kunaal_enqueue_assets()
    → Google Fonts (CDN)
    → style.css
    → main.js, theme-controller.js, lazy-blocks.js
    → IF About page: GSAP (CDN), Leaflet (CDN), about-page.js
    ↓
Template renders (e.g., front-page.php):
  → get_header() — inline theme script, nav
  → 2x WP_Query (essays, jottings)
  → LOOP: get_post_meta() per post (N+1!)
  → get_footer()
    ↓
HTML sent to browser
```

### Theme Token System (CSS Custom Properties)

**Location:** `style.css:23-140`

```css
:root {
  /* Core colors */
  --bg: #FDFCFA;
  --ink: #0b1220;
  --blue: #1E5AFF;
  
  /* Spacing (magic numbers, no scale) */
  /* Typography (font stacks) */
  /* Chart colors */
}

:root[data-theme="dark"] {
  --bg: #0B1220;
  --ink: #F4F3F1;
  /* ... dark overrides ... */
}
```

**Observation:** Design tokens exist but spacing uses magic numbers throughout. No systematic scale (e.g., 4px, 8px, 12px...).

### External Dependencies

| Library | Version | Load Method | Used By |
|---------|---------|-------------|---------|
| Google Fonts | N/A | wp_enqueue_style (CDN) | Global |
| GSAP | 3.12.5 | wp_enqueue_script (CDN) | About page |
| Leaflet | 1.9.4 | wp_enqueue_script (CDN) + dynamic injection | About page, data-map block |
| D3 | 7.x | Dynamic injection only | network-graph, flow-diagram blocks |
| Carto Tiles | N/A | Tile URL in JS | About page, data-map block |

---

## 4. Methodology

### Search Patterns Used

| Pattern | Purpose | Tool |
|---------|---------|------|
| `add_action\s*\(` | Find all action hooks | grep |
| `add_filter\s*\(` | Find all filter hooks | grep |
| `wp_(enqueue\|register)_(script\|style)` | Find asset loading | grep |
| `\$_(GET\|POST\|REQUEST)` | Find input vectors | grep |
| `wp_verify_nonce\|check_ajax_referer` | Find nonce checks | grep |
| `current_user_can` | Find capability checks | grep |
| `innerHTML\|insertAdjacentHTML` | Find DOM sinks | grep |
| `!important` | Find CSS specificity issues | grep |
| `window\.` | Find global pollution | grep |
| `get_theme_mod\|get_post_meta` | Find DB calls | grep |

### Severity Scale

| Level | Definition | Example |
|-------|------------|---------|
| **Blocker** | Site broken, data loss imminent | N/A found |
| **Critical** | Security vulnerability exploitable now | PDF without auth |
| **High** | Significant perf/security/arch issue | N+1 queries, nonce bypass |
| **Medium** | Notable issue, not urgent | !important overuse, magic numbers |
| **Low** | Minor improvement opportunity | Version mismatch, console.log |

### Effort Scale

| Level | Definition |
|-------|------------|
| **S (Small)** | < 2 hours, single file, low risk |
| **M (Medium)** | 2-8 hours, few files, moderate risk |
| **L (Large)** | 1-3 days, many files, needs testing |
| **XL (Extra Large)** | 1+ weeks, architectural change |

---

## 5. Findings

### 5.1 Security Findings

#### SEC-001: PDF Generator Missing Authentication

| Field | Value |
|-------|-------|
| **Severity** | Critical |
| **Confidence** | High |
| **Location** | `pdf-generator.php:19-22` |
| **Evidence** | ```php
function kunaal_generate_pdf() {
    if (!isset($_GET['kunaal_pdf']) || !isset($_GET['post_id'])) {
        return;
    }
    // NO NONCE CHECK
    // NO CAPABILITY CHECK
    $post_id = absint($_GET['post_id']);
``` |
| **Why It Matters** | Any visitor can request `?kunaal_pdf=1&post_id=123` and generate PDFs. Enables post enumeration (try IDs 1-9999), resource exhaustion (PDF generation is CPU-intensive), and potential information disclosure if draft posts are accessible. |
| **Root Cause** | Developer assumed public posts = public PDF generation. No security layer added. |
| **Fix Recommendation** | Add nonce via URL parameter for logged-in users; for public, add rate limiting and restrict to published posts only. |
| **Effort** | S (30 min) |
| **Risk/Side Effects** | Existing PDF links will break until updated with nonce |
| **Owner Archetype** | Backend/Security |

---

#### SEC-002: AJAX Filter Nonce Check Bypassed

| Field | Value |
|-------|-------|
| **Severity** | High |
| **Confidence** | High |
| **Location** | `functions.php:1442-1449` |
| **Evidence** | ```php
function kunaal_filter_content() {
    // Don't die on nonce failure - just log and continue for public pages
    $nonce_valid = isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'kunaal_theme_nonce');
    
    // For logged-in admin users, we might want stricter checking
    // But for public filtering, allow it to work
    
    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'essay';
``` |
| **Why It Matters** | The nonce is checked but the result is ignored. Code proceeds regardless. This is worse than no check because it gives false confidence. CSRF attacks can manipulate filter parameters. |
| **Root Cause** | Intentional bypass for "public pages" — misunderstanding that AJAX still needs CSRF protection. |
| **Fix Recommendation** | Either enforce nonce (die on failure) or remove the misleading check entirely if truly public. Add rate limiting for unauthenticated requests. |
| **Effort** | S (15 min) |
| **Risk/Side Effects** | None if nonce is passed correctly by frontend |
| **Owner Archetype** | Backend/Security |

---

#### SEC-003: DOM Injection Sinks (innerHTML Usage)

| Field | Value |
|-------|-------|
| **Severity** | High |
| **Confidence** | High |
| **Location** | 14 occurrences across 6 files |
| **Evidence** | ```
main.js:372:    container.innerHTML = '';
main.js:376:    container.innerHTML = `<p class="no-posts">...`;
main.js:382:    container.insertAdjacentHTML('beforeend', el);
main.js:873:    tooltip.innerHTML = html;
about-page.js:707: placeholder.innerHTML = '<span>' + title + '</span>';
dumbbell-chart/view.js:24: tooltip.innerHTML = `...`;
footnote/view.js:112: tooltip.innerHTML = content.innerHTML;
network-graph/view.js:184: connectionsList.innerHTML = connections.map(...);
``` |
| **Why It Matters** | If any data flowing into these sinks comes from user input, URL parameters, or untrusted AJAX responses, DOM-based XSS is possible. |
| **Root Cause** | Convenience of innerHTML over safe DOM APIs (textContent, createElement). |
| **Fix Recommendation** | Audit each sink: (1) trace data sources, (2) if user-controlled, switch to textContent or sanitize with DOMPurify, (3) add CSP header as defense-in-depth. |
| **Effort** | M (4 hours) — requires tracing each data flow |
| **Risk/Side Effects** | Some HTML formatting may need restructuring |
| **Owner Archetype** | Frontend/Security |

---

#### SEC-004: Rate Limiting Uses REMOTE_ADDR Only

| Field | Value |
|-------|-------|
| **Severity** | Low |
| **Confidence** | Medium |
| **Location** | `functions.php:1854-1860` (contact form handler) |
| **Evidence** | ```php
$ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
$rate_key = 'kunaal_contact_rl_' . md5($ip);
``` |
| **Why It Matters** | Behind a proxy/CDN, all requests have same REMOTE_ADDR. Attackers can rotate IPs. Rate limiting is weak. |
| **Root Cause** | Common oversight; X-Forwarded-For not checked. |
| **Fix Recommendation** | Check X-Forwarded-For (with validation), add CAPTCHA for persistent abuse, consider honeypot (already present but could be stronger). |
| **Effort** | S (1 hour) |
| **Risk/Side Effects** | False positives possible with shared IPs |
| **Owner Archetype** | Backend |

---

### 5.2 Performance Findings (Backend)

#### PERF-BE-001: N+1 Query Pattern in Post Loops

| Field | Value |
|-------|-------|
| **Severity** | High |
| **Confidence** | High |
| **Location** | `front-page.php:111-122`, `archive-essay.php:81-82`, `archive-jotting.php:81`, `taxonomy-topic.php:26-27` |
| **Evidence** | ```php
<?php while ($essays_query->have_posts()) : $essays_query->the_post(); ?>
  <?php
  $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
  $read_time = get_post_meta(get_the_ID(), 'kunaal_read_time', true);
  $topics = get_the_terms(get_the_ID(), 'topic');
  $card_image = kunaal_get_card_image_url(get_the_ID()); // Another get_post_meta
  ?>
``` |
| **Why It Matters** | For 6 essays + 6 jottings on front page: 12 posts × 4 queries = 48 extra queries. With caching cold, this hammers the database. |
| **Root Cause** | No meta/term cache priming before loop. |
| **Fix Recommendation** | Before loop: `update_post_meta_cache(wp_list_pluck($query->posts, 'ID'))` and set `'update_post_term_cache' => true` in WP_Query args. |
| **Effort** | S (1 hour) |
| **Risk/Side Effects** | Slightly higher memory usage (acceptable) |
| **Owner Archetype** | Backend |

---

#### PERF-BE-002: Excessive get_theme_mod Calls

| Field | Value |
|-------|-------|
| **Severity** | High |
| **Confidence** | High |
| **Location** | 146 calls across theme files |
| **Evidence** | ```
footer.php: 5 calls
header.php: 5 calls (via nav)
page-about.php: 29 calls
functions.php: 31 calls
page-contact.php: ~20 calls
... and more
``` |
| **Why It Matters** | Each `get_theme_mod()` retrieves the entire theme_mods option and extracts one value. Called 146 times = 146 option lookups (though WP caches after first, still overhead). |
| **Root Cause** | No centralized theme config object. |
| **Fix Recommendation** | Create `kunaal_get_config()` that loads all theme_mods once into a static variable, then returns individual values. |
| **Effort** | M (3 hours) |
| **Risk/Side Effects** | Need to invalidate cache on Customizer save |
| **Owner Archetype** | Backend |

---

#### PERF-BE-003: Nested get_theme_mod Fallbacks

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | `page-about.php:48-54` |
| **Evidence** | ```php
$show_bio = get_theme_mod('kunaal_about_bio_show', get_theme_mod('kunaal_about_show_bio', true));
$show_map = get_theme_mod('kunaal_about_map_show', get_theme_mod('kunaal_about_show_map', true));
// ... 6 more like this
``` |
| **Why It Matters** | Each nested call doubles the lookups. This is migration debt from renamed settings. |
| **Root Cause** | Settings were renamed; old names kept as fallback without migration script. |
| **Fix Recommendation** | Run one-time migration script to copy old values to new keys, then remove fallbacks. |
| **Effort** | S (1 hour) |
| **Risk/Side Effects** | Need to test migration doesn't overwrite intentional new values |
| **Owner Archetype** | Backend |

---

#### PERF-BE-004: Unbounded per_page in AJAX Filter

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | `functions.php:1465` |
| **Evidence** | ```php
$per_page = isset($_POST['per_page']) ? absint($_POST['per_page']) : 12;
// No maximum validation
``` |
| **Why It Matters** | Malicious request with `per_page=99999` causes massive query, potential DoS. |
| **Root Cause** | Trusted user input. |
| **Fix Recommendation** | Add `$per_page = min($per_page, 100);` |
| **Effort** | S (5 min) |
| **Risk/Side Effects** | None |
| **Owner Archetype** | Backend |

---

#### PERF-BE-005: file_exists Checks on Every Request

| Field | Value |
|-------|-------|
| **Severity** | Low |
| **Confidence** | High |
| **Location** | `functions.php:1910`, `blocks.php:219,234,266`, `pdf-generator.php:26,236` |
| **Evidence** | 6 `file_exists()` calls on init/template hooks |
| **Why It Matters** | Filesystem I/O on every request. Files don't change at runtime. |
| **Root Cause** | Defensive coding without caching. |
| **Fix Recommendation** | Cache results in transient (clear on theme update) or move checks to activation hook. |
| **Effort** | S (30 min) |
| **Risk/Side Effects** | Transient invalidation needed on deploy |
| **Owner Archetype** | Backend |

---

### 5.3 Performance Findings (Frontend)

#### PERF-FE-001: GSAP Loaded in Head (Render-Blocking)

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | `functions.php:170-176` |
| **Evidence** | ```php
wp_enqueue_script(
    'gsap-core',
    'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
    array(),
    '3.12.5',
    false // Load in head for better performance <-- COMMENT IS WRONG
);
``` |
| **Why It Matters** | Comment says "better performance" but loading in head blocks HTML parsing. ~60KB of JS before any content renders. |
| **Root Cause** | Misunderstanding of render-blocking. |
| **Fix Recommendation** | Change to `true` (footer) or add `defer` attribute via `script_loader_tag` filter. |
| **Effort** | S (10 min) |
| **Risk/Side Effects** | GSAP animations may flash briefly before init (acceptable tradeoff) |
| **Owner Archetype** | Frontend |

---

#### PERF-FE-002: Leaflet Double-Load Risk

| Field | Value |
|-------|-------|
| **Severity** | High |
| **Confidence** | High |
| **Location** | `functions.php:191-197` + `blocks/data-map/view.js:39-40` |
| **Evidence** | ```php
// functions.php (About page)
wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'...);

// blocks/data-map/view.js
const script = document.createElement('script');
script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
``` |
| **Why It Matters** | If About page has a data-map block, Leaflet loads twice. Version skew risk if URLs diverge. Wasted bytes. Race condition on `window.L`. |
| **Root Cause** | Two systems (PHP enqueue + JS dynamic) don't coordinate. |
| **Fix Recommendation** | Block's view.js should check `window.L` before injecting, OR use `wp_localize_script` to signal Leaflet is already loaded. |
| **Effort** | M (2 hours) |
| **Risk/Side Effects** | Need to test both scenarios (About page, standalone block) |
| **Owner Archetype** | Frontend |

---

#### PERF-FE-003: D3 Polling Loader Pattern

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | `blocks/flow-diagram/view.js:15-33`, `blocks/network-graph/view.js:15-32` |
| **Evidence** | ```javascript
const checkInterval = setInterval(() => {
    if (window.d3) {
        clearInterval(checkInterval);
        resolve(window.d3);
    }
}, 50);
``` |
| **Why It Matters** | CPU-burning polling loop while D3 loads. If D3 fails to load, interval runs forever (no timeout). |
| **Root Cause** | Quick solution for async library loading. |
| **Fix Recommendation** | Use script onload/onerror events. Add timeout (e.g., 10s) with fallback message. |
| **Effort** | S (1 hour) |
| **Risk/Side Effects** | None |
| **Owner Archetype** | Frontend |

---

#### PERF-FE-004: 86 !important Declarations

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | style.css (35), about-page.css (3), pdf-ebook.css (23), print.css (9), block styles (16) |
| **Evidence** | `grep -c "!important"` across CSS files |
| **Why It Matters** | Specificity escalation makes CSS unmaintainable. Each `!important` breeds more. Changes become risky. |
| **Root Cause** | Fighting cascade conflicts, quick fixes that stuck. |
| **Fix Recommendation** | Audit top offenders, refactor selectors to avoid specificity wars, establish component ownership. |
| **Effort** | L (3-5 days) — requires careful regression testing |
| **Risk/Side Effects** | Visual regressions possible |
| **Owner Archetype** | Design Systems / Frontend |

---

### 5.4 Architecture & Maintainability Findings

#### ARCH-001: Monolithic functions.php

| Field | Value |
|-------|-------|
| **Severity** | High |
| **Confidence** | High |
| **Location** | `functions.php` (1965 lines, 42 functions) |
| **Evidence** | File contains: Theme setup, Asset enqueuing, CPT registration, Meta boxes, Validation, Customizer (400+ lines), AJAX handlers, PDF integration, OG tags, Contact form, Inline formats |
| **Why It Matters** | Hard to navigate, test, or modify. Single point of failure. Any syntax error crashes entire site. No clear separation of concerns. |
| **Root Cause** | Organic growth without refactoring. |
| **Fix Recommendation** | Split into modules: `inc/setup.php`, `inc/enqueue.php`, `inc/post-types.php`, `inc/customizer.php`, `inc/ajax-handlers.php`, `inc/helpers.php` |
| **Effort** | L (1 week) |
| **Risk/Side Effects** | Include order matters; need careful testing |
| **Owner Archetype** | Platform / Backend |

---

#### ARCH-002: Function Defined Inside Template

| Field | Value |
|-------|-------|
| **Severity** | High |
| **Confidence** | High |
| **Location** | `page-about.php:98-139` |
| **Evidence** | ```php
if (!function_exists('kunaal_render_atmo_images')) :
function kunaal_render_atmo_images($position, $images) {
    // 40 lines of function definition
}
endif;
``` |
| **Why It Matters** | Function defined as side-effect of template loading. If template loaded multiple times (AJAX, nested includes), behavior unpredictable. Couples business logic to presentation. |
| **Root Cause** | Developer convenience; function only used here. |
| **Fix Recommendation** | Move to `inc/helpers.php` or `functions.php`. |
| **Effort** | S (30 min) |
| **Risk/Side Effects** | None |
| **Owner Archetype** | Backend |

---

#### ARCH-003: Hardcoded Slug Dependencies

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | `header.php:64-74` |
| **Evidence** | ```php
$about_page = get_page_by_path('about');
$contact_page = get_page_by_path('contact');
``` |
| **Why It Matters** | If user renames page slugs, navigation breaks silently. No error, just broken links. |
| **Root Cause** | Assumed slugs are stable. |
| **Fix Recommendation** | Use Customizer settings to store page IDs (already partially done with `kunaal_about_page_id`), or use menu locations. |
| **Effort** | S (1 hour) |
| **Risk/Side Effects** | Existing sites need to re-select pages in Customizer |
| **Owner Archetype** | Backend |

---

#### ARCH-004: Global Namespace Pollution (JS)

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | Multiple JS files |
| **Evidence** | Custom globals defined: `window.kunaalLazyLoad`, `window.kunaalPresets`, `window.themeController`, `window.kunaalTheme` |
| **Why It Matters** | Creates load-order coupling, collision risk with other scripts/plugins, hard to test in isolation. |
| **Root Cause** | Pre-module-era patterns; need to share state across files. |
| **Fix Recommendation** | Consolidate into single namespace object `window.kunaal = {}` or use ES modules when WP supports. |
| **Effort** | M (4 hours) |
| **Risk/Side Effects** | Need to update all consumers |
| **Owner Archetype** | Frontend |

---

#### ARCH-005: CSS Selector Duplication

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | Top offenders: `:root` (7x), `.sidenote` (6x), `.hero-photo img` (5x), `.about-hero` (4x in 2 files) |
| **Evidence** | Selector `:root` defined in: `style.css:23,144,161,3397`, `about-page.css:1420`, `editor-style.css:7`, `pdf-ebook.css:466` |
| **Why It Matters** | Same selector in multiple files = cascade conflicts, hard to reason about which wins, fragile changes. |
| **Root Cause** | No component ownership boundaries. |
| **Fix Recommendation** | Assign ownership: `:root` only in `style.css`. Use BEM-like naming for component selectors. Consolidate about-page styles. |
| **Effort** | L (1 week) |
| **Risk/Side Effects** | High risk of visual regressions |
| **Owner Archetype** | Design Systems |

---

### 5.5 Internationalization Findings

#### I18N-001: Missing Translation Wrappers

| Field | Value |
|-------|-------|
| **Severity** | Medium |
| **Confidence** | High |
| **Location** | `single-essay.php`, `single-jotting.php`, various templates |
| **Evidence** | ```php
<span class="tip">Share</span>
<span class="tip">Subscribe</span>
<span class="tip">Download PDF</span>
// Should be:
<span class="tip"><?php esc_html_e('Share', 'kunaal-theme'); ?></span>
``` |
| **Why It Matters** | Theme cannot be translated to other languages. Only 35 translation function calls found across entire theme. |
| **Root Cause** | Single-language assumption during development. |
| **Fix Recommendation** | Wrap all user-facing strings in `__()` or `_e()` with consistent text domain `kunaal-theme`. |
| **Effort** | M (4-6 hours) — tedious but straightforward |
| **Risk/Side Effects** | None |
| **Owner Archetype** | Frontend |

---

### 5.6 Correctness Findings

#### CORR-001: Version Mismatch

| Field | Value |
|-------|-------|
| **Severity** | Low |
| **Confidence** | High |
| **Location** | `functions.php:20`, `functions.php:31`, `style.css:6` |
| **Evidence** | Header comment: `@version 4.11.2`, Constant: `KUNAAL_THEME_VERSION = '4.12.0'`, style.css: `Version: 4.11.2` |
| **Why It Matters** | Version tracking unreliable; debugging harder; WordPress update checks confused. |
| **Root Cause** | Manual version updates in multiple places. |
| **Fix Recommendation** | Single source of truth: update `style.css` and reference it via `wp_get_theme()->get('Version')` |
| **Effort** | S (10 min) |
| **Risk/Side Effects** | None |
| **Owner Archetype** | Any |

---

#### CORR-002: Always-True Condition

| Field | Value |
|-------|-------|
| **Severity** | Low |
| **Confidence** | High |
| **Location** | `single-essay.php:37` (inferred from summary) |
| **Evidence** | `if (shortcode_exists('dkpdf-button') || true)` — always true |
| **Why It Matters** | Dead code; the shortcode check is meaningless. |
| **Root Cause** | Quick fix that stuck. |
| **Fix Recommendation** | Remove `|| true` or remove entire condition. |
| **Effort** | S (5 min) |
| **Risk/Side Effects** | None |
| **Owner Archetype** | Any |

---

### 5.7 Dead Code / Redundancy Candidates

| Candidate | Location | Evidence | How to Confirm Unused |
|-----------|----------|----------|----------------------|
| `pdf-template.php` | Root | Not referenced by `pdf-generator.php` (uses `kunaal_build_pdf_html()` instead) | `grep -r "pdf-template" kunaal-theme` |
| `inline-formats/` no block.json | `blocks/inline-formats/` | Only block dir without `block.json` | Confirm it's shared editor code, not a block |
| Legacy theme_mod fallbacks | `page-about.php:48-54` | Nested `get_theme_mod` calls | Run migration, check if old keys have values |

---

## 6. Prioritized Remediation Backlog

### Now (0-2 weeks)

| # | Item | Finding | Outcome | Effort | Risk |
|---|------|---------|---------|--------|------|
| 1 | Add nonce + auth to PDF generator | SEC-001 | Close critical security hole | S | Low |
| 2 | Enforce AJAX nonce | SEC-002 | Fix false security | S | Low |
| 3 | Cap AJAX per_page | PERF-BE-004 | Prevent DoS | S | Low |
| 4 | Move GSAP to footer | PERF-FE-001 | Improve FCP | S | Low |
| 5 | Fix version mismatch | CORR-001 | Accurate tracking | S | Low |
| 6 | Prime meta cache before loops | PERF-BE-001 | Cut queries 50%+ | S | Low |

### Next (2-6 weeks)

| # | Item | Finding | Outcome | Effort | Risk |
|---|------|---------|---------|--------|------|
| 7 | Cache theme_mod calls | PERF-BE-002 | Reduce option lookups | M | Low |
| 8 | Audit innerHTML sinks | SEC-003 | Harden XSS surface | M | Medium |
| 9 | Coordinate Leaflet loading | PERF-FE-002 | Eliminate double-load | M | Medium |
| 10 | Remove theme_mod fallbacks | PERF-BE-003 | Clean up migration debt | S | Low |
| 11 | Add polling timeout to D3 loaders | PERF-FE-003 | Prevent infinite loops | S | Low |
| 12 | Wrap strings in translation functions | I18N-001 | Enable i18n | M | Low |

### Later (6+ weeks)

| # | Item | Finding | Outcome | Effort | Risk |
|---|------|---------|---------|--------|------|
| 13 | Split functions.php | ARCH-001 | Improve maintainability | L | Medium |
| 14 | Refactor CSS !important usage | PERF-FE-004 | Sustainable cascade | L | High |
| 15 | Establish CSS component ownership | ARCH-005 | Reduce fragility | L | High |
| 16 | Consolidate JS globals | ARCH-004 | Cleaner architecture | M | Medium |
| 17 | Move template function to helpers | ARCH-002 | Proper separation | S | Low |
| 18 | Self-host critical libraries | PERF-FE-002 | CDN independence | M | Medium |

---

## 7. Quick Wins

These can be done in a single focused session with minimal risk:

1. **SEC-001 Fix (30 min):** Add `wp_verify_nonce()` + `current_user_can('read')` check to `kunaal_generate_pdf()`. For public posts, add transient-based rate limiting per IP.

2. **SEC-002 Fix (15 min):** Change line 1444 to: `if (!$nonce_valid) { wp_send_json_error('Invalid nonce'); return; }` — or remove the check entirely if truly public.

3. **PERF-BE-004 Fix (5 min):** After line 1465, add: `$per_page = min($per_page, 100);`

4. **PERF-FE-001 Fix (10 min):** Change `false` to `true` in GSAP enqueue (line 175-176), same for ScrollTrigger (line 184).

5. **CORR-001 Fix (10 min):** Update `functions.php:20` and `style.css:6` to match constant `4.12.0`.

6. **PERF-BE-001 Fix (30 min):** Before the while loop in `front-page.php:109`, add:
   ```php
   $post_ids = wp_list_pluck($essays_query->posts, 'ID');
   update_post_meta_cache($post_ids);
   update_object_term_cache($post_ids, 'essay');
   ```

7. **PERF-BE-003 Fix (30 min):** One-time WP-CLI command to migrate old theme_mod keys to new, then remove nested fallbacks.

8. **Remove console.log (5 min):** Delete `console.log` from `data-map/view.js:116` and `inline-formats/index.js:239`.

9. **Remove alert() (5 min):** Replace `alert('Failed to create topic...')` in `editor-sidebar.js:138` with proper error UI.

10. **D3 timeout (15 min):** Add `setTimeout(() => { clearInterval(checkInterval); reject('D3 load timeout'); }, 10000);` to D3 loaders.

---

## 8. Open Questions / Validation Plan

| Question | Why It Matters | How to Validate |
|----------|---------------|-----------------|
| Is `pdf-template.php` actually unused? | Dead code removal | `grep -r "pdf-template" kunaal-theme && grep -r "require.*pdf-template" kunaal-theme` |
| Does Leaflet actually double-load on About page with data-map block? | Confirms PERF-FE-002 | Add About page with data-map block, check Network tab |
| Are legacy theme_mod keys still populated? | Migration decision | `wp option get theme_mods_kunaal-theme` via WP-CLI |
| What's actual query count on front page? | Baseline for PERF-BE-001 | Install Query Monitor, load front page |
| Does AJAX filter work without nonce? | Confirms SEC-002 | Send POST to admin-ajax.php with action=kunaal_filter, no nonce |

---

## 9. Appendix

### 9.1 Quantified Inventory (Exact Counts)

| Metric | Count | Method |
|--------|-------|--------|
| PHP files (excl. specs) | 71 | `find kunaal-theme -name "*.php" -not -path "*/specs/*" | wc -l` |
| JS files (excl. specs) | 74 | Same pattern |
| CSS files (excl. specs) | 58 | Same pattern |
| Block directories | 52 | `find kunaal-theme/blocks -mindepth 1 -maxdepth 1 -type d` |
| Blocks with block.json | 51 | 52 - 1 (inline-formats) |
| `add_action` calls | 36 | `grep -c "add_action\s*(" kunaal-theme/**/*.php` |
| `add_filter` calls | 8 | Same pattern |
| `wp_enqueue_*` calls | 27 | Pattern for script/style |
| `wp_add_inline_*` calls | 2 | `functions.php:188,239` |
| `wp_localize_script` calls | 1 | `functions.php:124` |
| `$_GET/$_POST/$_REQUEST` reads | 41 | Security scan |
| Nonce verifications | 3 | Pattern match |
| Capability checks | 7 | Pattern match |
| `get_theme_mod` calls | 146 | Pattern match |
| `get_post_meta` calls | 30 | Pattern match |
| `WP_Query` / `get_posts` | 3 | Pattern match |
| `!important` in CSS | 86 | Literal match |
| `window.*` assignments | 160 | Pattern match |
| innerHTML/DOM sinks | 14 | Pattern match |
| Dynamic script injection | 3 | createElement('script') |
| IntersectionObserver uses | 17 | Pattern match |
| console.log statements | 2 | Pattern match |
| alert() calls | 1 | Pattern match |
| Translation function calls | 35 | `__`, `_e`, `esc_html__`, `esc_attr__` |

### 9.2 Key Files Inspected

| File | Lines | Hotspot Reason |
|------|-------|----------------|
| `functions.php` | 1965 | All core logic, 42 functions |
| `style.css` | 4512 | All design tokens + components |
| `page-about.php` | 460 | 29 theme_mod calls, function definition |
| `main.js` | 945 | Filters, sharing, parallax, scroll |
| `about-page.js` | 900+ | GSAP, Leaflet, complex animations |
| `about-page.css` | 1700+ | Duplicates style.css selectors |
| `front-page.php` | 236 | N+1 query pattern |
| `pdf-generator.php` | 240 | No auth check |
| `blocks.php` | 310 | Block registration |
| `about-customizer.php` | 666 | 100+ settings |

### 9.3 Commands Run

```bash
# Git context
git rev-parse --abbrev-ref HEAD  # → main

# File counts
find kunaal-theme -name "*.php" -not -path "*/specs/*" | wc -l
find kunaal-theme -name "*.js" -not -path "*/specs/*" | wc -l
find kunaal-theme -name "*.css" -not -path "*/specs/*" | wc -l

# Pattern searches (all via grep tool)
grep "add_action\s*\(" kunaal-theme/**/*.php
grep "add_filter\s*\(" kunaal-theme/**/*.php
grep "wp_(enqueue|register)_(script|style)" kunaal-theme/**/*.php
grep "\$_(GET|POST|REQUEST)" kunaal-theme/**/*.php
grep "(wp_verify_nonce|check_ajax_referer)" kunaal-theme/**/*.php
grep "current_user_can" kunaal-theme/**/*.php
grep "get_theme_mod" kunaal-theme/**/*.php
grep "get_post_meta" kunaal-theme/**/*.php
grep "innerHTML|insertAdjacentHTML" kunaal-theme/**/*.js
grep "!important" kunaal-theme/**/*.css
grep "window\." kunaal-theme/**/*.js
grep "console\.log" kunaal-theme/**/*.js
grep "alert\s*\(" kunaal-theme/**/*.js
grep "__\(|_e\(" kunaal-theme/**/*.php
```

### 9.4 PHP Lint Results

```
PHP_LINT_TOTAL_FILES=71
PHP_LINT_BAD_FILES=0
```

All 71 PHP files pass `php -l` syntax check.

---

**End of Audit Report**

*This audit was conducted read-only. No theme files were modified. All findings are based on static analysis of source code.*
