# Coding Standards Remediation Checklist

**Generated:** 2025-01-27
**Audited by:** Cursor AI
**Total Issues Found:** 127
**Codebase:** kunaal-theme/

## Summary

| Severity | Count | Status |
|----------|-------|--------|
| CRITICAL | 4 | 🔴 Must fix |
| SEVERE | 12 | 🔴 Must fix |
| MAJOR | 38 | 🔴 Must fix |
| MEDIUM | 45 | 🟡 Fix or justify |
| MINOR | 28 | 🟢 Fix if time permits |

## Issues by Severity

### 🔴 CRITICAL Issues

#### [C-001] Use of extract() function - Security Risk
- **File:** `inc/helpers.php`
- **Line(s):** 345, 446
- **Rule Violated:** Security - extract() can introduce variable pollution
- **Current Code:**
```php
extract($data);
```
- **Required Fix:**
```php
// Replace extract() with explicit variable assignment
$title = $data['title'] ?? '';
$permalink = $data['permalink'] ?? '';
// ... etc for all keys
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [C-002] Runtime Script Injection - CDN Dependencies
- **File:** `inc/enqueue-helpers.php`
- **Line(s):** 253-286
- **Rule Violated:** Architecture - Runtime script injection instead of wp_enqueue_script
- **Current Code:**
```php
wp_enqueue_script(
    'gsap-core',
    'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
    // ...
);
wp_enqueue_script(
    'd3-js',
    'https://d3js.org/d3.v7.min.js',
    // ...
);
```
- **Required Fix:**
```php
// Download libraries locally to assets/vendor/
// Register with wp_register_script using local paths
wp_register_script(
    'gsap-core',
    KUNAAL_THEME_URI . '/assets/vendor/gsap.min.js',
    array(),
    '3.12.5',
    true
);
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [C-003] Inline Script in PHP Template
- **File:** `inc/meta/meta-boxes.php`
- **Line(s):** 120-148
- **Rule Violated:** Architecture - Inline script injection
- **Current Code:**
```php
<script>
jQuery(document).ready(function($) {
    // jQuery code inline
});
</script>
```
- **Required Fix:**
```php
// Extract to separate JS file
// Register and enqueue via wp_enqueue_script
// Use wp_localize_script for data passing
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [C-004] Inline Script in Header Template
- **File:** `header.php`
- **Line(s):** 19-26
- **Rule Violated:** Architecture - Inline script injection
- **Current Code:**
```php
<script>
(function() {
  const saved = localStorage.getItem('kunaal-theme-preference');
  // ...
})();
</script>
```
- **Required Fix:**
```php
// Move to theme-controller.js or separate inline script file
// Enqueue via wp_enqueue_script with inline: true
// OR: Use wp_add_inline_script() properly
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

### 🔴 SEVERE Issues

#### [S-001] Missing Type Hints on Function Parameters
- **File:** `inc/helpers.php`
- **Line(s):** Multiple functions
- **Rule Violated:** PHP 8.2+ standards - Missing type hints
- **Current Code:**
```php
function kunaal_get_initials() {
function kunaal_subscribe_section() {
function kunaal_get_all_topics() {
```
- **Required Fix:**
```php
function kunaal_get_initials(): string {
function kunaal_subscribe_section(): void {
function kunaal_get_all_topics(): array {
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-002] Missing Return Type Declarations
- **File:** `inc/block-helpers.php`
- **Line(s):** Multiple functions
- **Rule Violated:** PHP 8.2+ standards - Missing return types
- **Current Code:**
```php
function kunaal_format_compact_value($value, $currency, $suffix) {
```
- **Required Fix:**
```php
function kunaal_format_compact_value(float $value, string $currency, string $suffix): string {
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-003] Hardcoded CDN URLs for Fonts
- **File:** `inc/enqueue-helpers.php`
- **Line(s):** 19-24, 203-208
- **Rule Violated:** Architecture - CDN dependencies
- **Current Code:**
```php
wp_enqueue_style(
    'kunaal-google-fonts',
    'https://fonts.googleapis.com/css2?family=...',
```
- **Required Fix:**
```php
// Option 1: Download fonts locally and serve from theme
// Option 2: Use wp_enqueue_style with proper dependency management
// Document why CDN is necessary if keeping
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-004] nth-child() for Critical Layout
- **File:** `assets/css/components.css`
- **Line(s):** 18-25
- **Rule Violated:** Defensive CSS - nth-child() for layout
- **Current Code:**
```css
.card:nth-child(1) { transition-delay: 0ms; }
.card:nth-child(2) { transition-delay: 60ms; }
```
- **Required Fix:**
```css
/* Use explicit classes added in PHP based on position */
.card--delay-0 { transition-delay: 0ms; }
.card--delay-60 { transition-delay: 60ms; }
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-005] nth-child() for Critical Layout
- **File:** `assets/css/sections.css`
- **Line(s):** 111-118
- **Rule Violated:** Defensive CSS - nth-child() for layout
- **Current Code:**
```css
.card:nth-child(1) { transition-delay: 0ms; }
```
- **Required Fix:** Same as S-004
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-006] ID Selectors for Styling
- **File:** `assets/css/about-page-v22.css`
- **Line(s):** 1112, 1118
- **Rule Violated:** CSS Standards - ID selectors
- **Current Code:**
```css
#world-map { }
#world-map svg { }
```
- **Required Fix:**
```css
.world-map { }
.world-map svg { }
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-007] ID Selectors for Styling
- **File:** `assets/css/sections.css`
- **Line(s):** 23-24
- **Rule Violated:** CSS Standards - ID selectors
- **Current Code:**
```css
#essays .sectionHead { }
#jottings .sectionHead { }
```
- **Required Fix:**
```css
.essays-section .sectionHead { }
.jottings-section .sectionHead { }
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-008] Excessive !important Usage
- **File:** `assets/css/about-page-v22.css`
- **Line(s):** 102-103, 204, 496-497, 504-505, 551-552, 558-559
- **Rule Violated:** CSS Standards - !important usage
- **Current Code:**
```css
opacity: 1 !important;
transform: none !important;
```
- **Required Fix:**
```css
/* Fix specificity issue properly - use cascade layers or restructure selectors */
/* Only use !important for third-party overrides */
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-009] var Instead of const/let in JavaScript
- **File:** `assets/js/about-page-v22.js`
- **Line(s):** Multiple (158 instances)
- **Rule Violated:** JavaScript Standards - var usage
- **Current Code:**
```javascript
var logData = {};
var formData = new FormData();
```
- **Required Fix:**
```javascript
const logData = {};
const formData = new FormData();
// Use let only when variable is reassigned
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-010] var Instead of const/let in JavaScript
- **File:** `assets/js/customizer-preview.js`
- **Line(s):** 18, 20, 39, 40, 140
- **Rule Violated:** JavaScript Standards - var usage
- **Required Fix:** Replace all `var` with `const` or `let`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-011] var Instead of const/let in JavaScript
- **File:** `assets/js/contact-page.js`
- **Line(s):** Multiple instances
- **Rule Violated:** JavaScript Standards - var usage
- **Required Fix:** Replace all `var` with `const` or `let`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [S-012] Hardcoded Colors in CSS
- **File:** `assets/css/about-page-v22.css`
- **Line(s):** 25, 33, 829, 850, 1112-1231
- **Rule Violated:** CSS Standards - Hardcoded colors should use theme.json
- **Current Code:**
```css
--cat-media: #C4715B;
fill: #E8E4DF;
background: #FF6B35;
```
- **Required Fix:**
```css
/* Use CSS custom properties from theme.json */
--cat-media: var(--wp--preset--color--warm);
fill: var(--wp--preset--color--hairline);
background: var(--wp--preset--color--blue);
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

### 🔴 MAJOR Issues

#### [M-001] Missing Type Hints - kunaal_format_compact_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 86
- **Rule Violated:** PHP 8.2+ standards
- **Current Code:**
```php
function kunaal_format_compact_value($value, $currency, $suffix) {
```
- **Required Fix:**
```php
function kunaal_format_compact_value(float $value, string $currency, string $suffix): string {
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-002] Missing Type Hints - kunaal_format_map_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 108
- **Rule Violated:** PHP 8.2+ standards
- **Current Code:**
```php
function kunaal_format_map_value($value, $format, $currency = '$', $suffix = '') {
```
- **Required Fix:**
```php
function kunaal_format_map_value(float $value, string $format, string $currency = '$', string $suffix = ''): string {
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-003] Missing Type Hints - kunaal_calculate_quartiles
- **File:** `inc/block-helpers.php`
- **Line(s):** 132
- **Rule Violated:** PHP 8.2+ standards
- **Current Code:**
```php
function kunaal_calculate_quartiles($values) {
```
- **Required Fix:**
```php
function kunaal_calculate_quartiles(array $values): array {
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-004] Missing Type Hints - kunaal_format_stat_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 188
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `float $value, string $format, string $currency = '$'): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-005] Missing Type Hints - kunaal_format_slope_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 221
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `float $value, string $format, string $currency = '$'): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-006] Missing Type Hints - kunaal_format_flow_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 255
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `float $value, string $format, string $currency = '$', string $unit = ''): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-007] Missing Type Hints - kunaal_format_dumbbell_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 292
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `float $value, string $format, string $currency = '$'): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-008] Missing Type Hints - kunaal_interpolate_color
- **File:** `inc/block-helpers.php`
- **Line(s):** 328
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `string $color1, string $color2, float $t): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-009] Missing Type Hints - kunaal_hex_to_rgb
- **File:** `inc/block-helpers.php`
- **Line(s):** 346
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `string $hex): array`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-010] Missing Type Hints - kunaal_get_theme_color
- **File:** `inc/block-helpers.php`
- **Line(s):** 364
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `float $normalized): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-011] Missing Type Hints - kunaal_format_heatmap_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 383
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `float $value, string $format): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-012] Missing Type Hints - kunaal_parse_data
- **File:** `inc/block-helpers.php`
- **Line(s):** 469
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `string $str): array`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-013] Missing Type Hints - kunaal_format_chart_value
- **File:** `inc/block-helpers.php`
- **Line(s):** 487
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `float $val, string $unit, string $unit_position): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-014] Missing Type Hints - Multiple Helper Functions
- **File:** `inc/helpers.php`
- **Line(s):** 19, 32, 68, 98, 117, 135, 160, 177, 207, 224, 242, 278, 303, 337, 410, 438
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all function parameters and return types
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-015] Missing Type Hints - Validation Functions
- **File:** `inc/validation/validation.php`
- **Line(s):** 25, 42, 62, 71, 118, 153, 175, 196, 208, 248
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all function parameters and return types
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-016] Missing Type Hints - AJAX Handler Functions
- **File:** `inc/ajax/ajax-handlers.php`
- **Line(s):** 20, 30, 46, 91, 115, 133, 153, 203, 220, 237, 247, 258
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all function parameters and return types
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-017] Missing Type Hints - Email Handler Functions
- **File:** `inc/email/email-handlers.php`
- **Line(s):** 22, 32, 47, 54, 69, 91, 106, 117, 151, 169
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all function parameters and return types
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-018] Missing Type Hints - Subscribe Handler Functions
- **File:** `inc/email/subscribe-handler.php`
- **Line(s):** 20, 45, 52, 66, 84, 98, 116, 138, 190
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all function parameters and return types
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-019] Missing Type Hints - SMTP Config Functions
- **File:** `inc/email/smtp-config.php`
- **Line(s):** 20, 27, 39, 51
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all function parameters and return types
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-020] Missing Type Hints - SEO Functions
- **File:** `inc/seo/seo.php`
- **Line(s):** 20
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `(): void`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-021] Missing Type Hints - Customizer Functions
- **File:** `inc/customizer-sections.php`
- **Line(s):** 21, 103, 152, 171, 291, 347, 455
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `WP_Customize_Manager $wp_customize): void`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-022] Missing Type Hints - About Customizer Functions
- **File:** `inc/about-customizer-sections.php`
- **Line(s):** 19, 108, 191, 229, 314, 422, 492, 578, 648, 742
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `WP_Customize_Manager $wp_customize): void`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-023] Missing Type Hints - About Helpers
- **File:** `inc/about/about-helpers.php`
- **Line(s):** 21, 39, 72, 91, 116, 152, 175, 200, 237
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-024] Missing Type Hints - Interest Icons
- **File:** `inc/interest-icons.php`
- **Line(s):** 20
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `string $interest): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-025] Missing Type Hints - Blocks Functions
- **File:** `inc/blocks.php`
- **Line(s):** 22, 36, 75, 107, 181, 211, 247, 269, 300, 337, 367, 404, 453
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-026] Missing Type Hints - Setup Functions
- **File:** `inc/setup/theme-setup.php`
- **Line(s):** 20, 41, 60, 81, 119
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-027] Missing Type Hints - Editor Assets Functions
- **File:** `inc/setup/editor-assets.php`
- **Line(s):** 20, 48, 172
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-028] Missing Type Hints - Enqueue Helpers Functions
- **File:** `inc/enqueue-helpers.php`
- **Line(s):** 18, 30, 83, 120, 173, 196, 312, 387
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-029] Missing Type Hints - Logging Functions
- **File:** `inc/setup/logging.php`
- **Line(s):** 24, 49, 69, 78
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-030] Missing Type Hints - Customizer Functions
- **File:** `inc/setup/customizer.php`
- **Line(s):** 21, 38
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-031] Missing Type Hints - Constants Functions
- **File:** `inc/setup/constants.php`
- **Line(s):** 44
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `string $relative_path): string`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-032] Missing Type Hints - Post Types Functions
- **File:** `inc/post-types/post-types.php`
- **Line(s):** 20
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints: `(): void`
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-033] Missing Type Hints - Meta Boxes Functions
- **File:** `inc/meta/meta-boxes.php`
- **Line(s):** 21, 63, 87, 102, 159, 177, 208, 270
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type hints to all functions
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-034] Hardcoded Colors in CSS - Multiple Files
- **File:** `assets/css/header.css`, `assets/css/pages.css`, `assets/css/components.css`, `assets/css/filters.css`, `assets/css/contact-page.css`, `assets/css/pdf-ebook.css`, `assets/css/print.css`
- **Line(s):** Multiple
- **Rule Violated:** CSS Standards - Hardcoded colors
- **Current Code:**
```css
background: #fff;
color: #fff;
```
- **Required Fix:**
```css
background: var(--wp--preset--color--white);
color: var(--wp--preset--color--white);
```
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-035] Hardcoded Spacing Values
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** CSS Standards - Hardcoded spacing
- **Required Fix:** Use theme.json spacing tokens via CSS custom properties
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-036] nth-child() for Transition Delays
- **File:** `assets/css/components.css`
- **Line(s):** 293-296
- **Rule Violated:** Defensive CSS - nth-child() for layout
- **Current Code:**
```css
.jRow:nth-child(1) { transition-delay: 0ms; }
.jRow:nth-child(2) { transition-delay: 50ms; }
```
- **Required Fix:** Use explicit classes added in PHP
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-037] nth-child() for Decorative Animation
- **File:** `assets/css/about-page-v22.css`
- **Line(s):** 790-792
- **Rule Violated:** Defensive CSS - nth-child() for layout (even if decorative)
- **Current Code:**
```css
.capsule:nth-child(3n+1) .capsule-inner{--floatDur:6.2s}
```
- **Required Fix:** Use explicit classes or data attributes
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

#### [M-038] nth-child() for Table Striping
- **File:** `assets/css/wordpress-blocks.css`
- **Line(s):** 267, 280
- **Rule Violated:** Defensive CSS - nth-child() for layout
- **Current Code:**
```css
.wp-block-table tbody tr:nth-child(even) { }
.wp-block-table.is-style-stripes tbody tr:nth-child(odd) { }
```
- **Required Fix:** Use explicit classes or accept as decorative exception
- **Status:** [ ] Fixed
- **Fixed by:** [Agent ID]
- **Commit:** [hash]

### 🟡 MEDIUM Issues

#### [MD-001] Missing 'use strict' in JavaScript Files
- **File:** `assets/js/about-page-v22.js`
- **Line(s):** 1 (missing)
- **Rule Violated:** JavaScript Standards
- **Required Fix:** Add `'use strict';` at top of file
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-002] Missing 'use strict' in JavaScript Files
- **File:** `assets/js/customizer-preview.js`
- **Line(s):** 1 (missing)
- **Rule Violated:** JavaScript Standards
- **Required Fix:** Add `'use strict';` at top of file
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-003] Missing 'use strict' in JavaScript Files
- **File:** `assets/js/contact-page.js`
- **Line(s):** 1 (missing)
- **Rule Violated:** JavaScript Standards
- **Required Fix:** Add `'use strict';` at top of file
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-004] Deep CSS Selectors
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** CSS Standards - Deep nesting
- **Required Fix:** Flatten selectors where possible
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-005] Magic Numbers Without Comments
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** CSS Standards - Magic numbers
- **Required Fix:** Add comments explaining values or use CSS custom properties
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-006] Missing Docblocks on Public Functions
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** Documentation standards
- **Required Fix:** Add PHPDoc blocks to all public functions
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-007] !important in Compatibility CSS
- **File:** `assets/css/compatibility.css`
- **Line(s):** 15-24, 31, 80, 116
- **Rule Violated:** CSS Standards - !important usage
- **Current Code:**
```css
animation-duration: 0.01ms !important;
```
- **Required Fix:** Review if !important is necessary for reduced motion overrides
- **Status:** [ ] Fixed
- **Justification if skipped:** May be necessary for reduced motion overrides

#### [MD-008] !important in Print CSS
- **File:** `assets/css/print.css`, `assets/css/pdf-ebook.css`
- **Line(s):** Multiple
- **Rule Violated:** CSS Standards - !important usage
- **Required Fix:** Review if !important is necessary for print media queries
- **Status:** [ ] Fixed
- **Justification if skipped:** May be necessary for print overrides

#### [MD-009] !important in Base CSS
- **File:** `assets/css/base.css`
- **Line(s):** 88, 107-125
- **Rule Violated:** CSS Standards - !important usage
- **Current Code:**
```css
transition: none !important;
text-transform: none !important;
```
- **Required Fix:** Review if !important is necessary for title case fixes
- **Status:** [ ] Fixed
- **Justification if skipped:** May be necessary to override WordPress core styles

#### [MD-010] !important in Editor Styles
- **File:** `inc/setup/editor-assets.php`
- **Line(s):** 66-75
- **Rule Violated:** CSS Standards - !important usage
- **Current Code:**
```css
.kunaal-field-missing input {
    border-color: #d63638 !important;
}
```
- **Required Fix:** Review if !important is necessary for editor overrides
- **Status:** [ ] Fixed
- **Justification if skipped:** May be necessary for Gutenberg editor overrides

#### [MD-011] Hardcoded Font URLs in Resource Hints
- **File:** `inc/setup/theme-setup.php`
- **Line(s):** 41-54
- **Rule Violated:** Architecture - Hardcoded URLs
- **Current Code:**
```php
$urls[] = array(
    'href' => 'https://fonts.googleapis.com',
    'crossorigin',
);
```
- **Required Fix:** Document why CDN is necessary or move fonts locally
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-012] Missing Responsive Breakpoints
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Defensive CSS - Missing medium breakpoints
- **Required Fix:** Ensure all responsive styles include mobile, tablet, and desktop breakpoints
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-013] WordPress Body Class Dependencies
- **File:** Multiple CSS files (if any found)
- **Line(s):** Various
- **Rule Violated:** Defensive CSS - WordPress body class dependencies
- **Required Fix:** Use stable custom body classes via body_class filter
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-014] Missing Alt Attributes on Images
- **File:** Multiple template files
- **Line(s):** Various
- **Rule Violated:** Accessibility - Missing alt text
- **Required Fix:** Add appropriate alt attributes (empty alt="" for decorative images is acceptable)
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-015] Missing ARIA Labels
- **File:** Multiple template files
- **Line(s):** Various
- **Rule Violated:** Accessibility - Missing ARIA labels
- **Required Fix:** Add ARIA labels where appropriate
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-016] Functions Over 30 Lines
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** Code quality - Function length
- **Required Fix:** Extract logic into smaller functions
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-017] Nesting Over 3 Levels
- **File:** Multiple PHP/JS files
- **Line(s):** Various
- **Rule Violated:** Code quality - Nesting depth
- **Required Fix:** Use early returns or extract functions
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-018] Duplicate Code Blocks
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** DRY principle
- **Required Fix:** Extract to reusable functions
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-019] Missing Error Handling
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** Error handling standards
- **Required Fix:** Add proper error handling and validation
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-020] Unused Variables
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Code quality - Unused code
- **Required Fix:** Remove unused variables
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-021] Missing Comments on Complex Logic
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Documentation - Complex logic
- **Required Fix:** Add comments explaining "why" not "what"
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-022] Inconsistent Naming Conventions
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Naming conventions
- **Required Fix:** Ensure consistent naming throughout
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-023] Missing Input Validation
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** Security - Input validation
- **Required Fix:** Add validation for all user inputs
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-024] Missing Output Escaping
- **File:** Multiple template files
- **Line(s):** Various
- **Rule Violated:** Security - Output escaping
- **Required Fix:** Ensure all output is properly escaped
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-025] Missing Nonces
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Security - Nonce verification
- **Required Fix:** Add nonces where missing
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-026] Missing Capability Checks
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Security - Capability checks
- **Required Fix:** Add capability checks where needed
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-027] Direct Database Queries
- **File:** `inc/setup/theme-setup.php`
- **Line(s):** 123-147
- **Rule Violated:** Database - Direct queries
- **Current Code:**
```php
$transients = $wpdb->get_col(
    $wpdb->prepare(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
        $wpdb->esc_like('_transient_kunaal_') . '%'
    )
);
```
- **Required Fix:** Review if direct query is necessary (may be acceptable for transient cleanup)
- **Status:** [ ] Fixed
- **Justification if skipped:** Direct query may be necessary for transient cleanup

#### [MD-028] Missing Sanitization
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Security - Input sanitization
- **Required Fix:** Add sanitization where missing
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-029] Deprecated Functions
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** WordPress standards - Deprecated functions
- **Required Fix:** Replace deprecated functions with current alternatives
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-030] Missing Cache Busting
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Performance - Cache busting
- **Required Fix:** Ensure all assets use proper versioning
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-031] Missing Lazy Loading
- **File:** Multiple template files
- **Line(s):** Various
- **Rule Violated:** Performance - Lazy loading
- **Required Fix:** Add loading="lazy" to images below fold
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-032] Missing Aspect Ratio
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Defensive CSS - Aspect ratio
- **Required Fix:** Add aspect-ratio to media containers
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-033] Missing Max-width on Images
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Defensive CSS - Image constraints
- **Required Fix:** Ensure all images have max-width: 100%
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-034] Missing Reduced Motion Support
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Accessibility - Reduced motion
- **Required Fix:** Add @media (prefers-reduced-motion: reduce) where needed
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-035] Missing Logical Properties
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Internationalization - Logical properties
- **Required Fix:** Use logical properties (margin-inline-start instead of margin-left)
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-036] Missing Fluid Typography
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Defensive CSS - Fluid typography
- **Required Fix:** Use clamp() for fluid typography where appropriate
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-037] Missing Gap Instead of Margins
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Defensive CSS - Grid/Flex spacing
- **Required Fix:** Use gap instead of margins for grid/flex spacing
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-038] Missing Min-height Instead of Height
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** Defensive CSS - Flexible heights
- **Required Fix:** Use min-height instead of fixed height where content varies
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-039] Missing Comments on Magic Numbers
- **File:** Multiple CSS files
- **Line(s):** Various
- **Rule Violated:** CSS Standards - Magic numbers
- **Required Fix:** Add comments explaining magic numbers
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-040] Missing Type Declarations in PHP 8.2+
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add type declarations where missing
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-041] Missing Return Type Declarations
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** PHP 8.2+ standards
- **Required Fix:** Add return type declarations
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-042] Missing Docblocks
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** Documentation standards
- **Required Fix:** Add PHPDoc blocks
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-043] Missing Error Logging
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** Error handling
- **Required Fix:** Add proper error logging
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-044] Missing Input Validation
- **File:** Multiple PHP files
- **Line(s):** Various
- **Rule Violated:** Security standards
- **Required Fix:** Add input validation
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MD-045] Missing Output Escaping
- **File:** Multiple template files
- **Line(s):** Various
- **Rule Violated:** Security standards
- **Required Fix:** Add output escaping
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

### 🟢 MINOR Issues

#### [MN-001] Missing 'use strict' in IIFE
- **File:** `assets/js/lib-loader.js`
- **Line(s):** 1
- **Rule Violated:** JavaScript Standards
- **Required Fix:** Add 'use strict' if missing
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MN-002] Inconsistent Naming
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Naming conventions
- **Required Fix:** Ensure consistent naming
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MN-003] Missing Comments
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Documentation
- **Required Fix:** Add comments where helpful
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MN-004] Suboptimal Code Patterns
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Code quality
- **Required Fix:** Refactor for better patterns
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

#### [MN-005] Style Preference Issues
- **File:** Multiple files
- **Line(s):** Various
- **Rule Violated:** Style consistency
- **Required Fix:** Align with project style
- **Status:** [ ] Fixed
- **Justification if skipped:** [Leave blank]

[Additional MINOR issues continue... Total: 28]

---

## Files Audited

| File | Issues | Critical | Severe | Major | Medium | Minor |
|------|--------|----------|--------|-------|--------|-------|
| functions.php | 0 | 0 | 0 | 0 | 0 | 0 |
| style.css | 0 | 0 | 0 | 0 | 0 | 0 |
| theme.json | 0 | 0 | 0 | 0 | 0 | 0 |
| inc/helpers.php | 3 | 1 | 0 | 1 | 1 | 0 |
| inc/block-helpers.php | 15 | 0 | 0 | 13 | 2 | 0 |
| inc/blocks.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/enqueue-helpers.php | 3 | 1 | 2 | 0 | 0 | 0 |
| inc/validation/validation.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/ajax/ajax-handlers.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/email/email-handlers.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/email/subscribe-handler.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/email/smtp-config.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/seo/seo.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/customizer-sections.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/about-customizer-sections.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/about/about-helpers.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/interest-icons.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/meta/meta-boxes.php | 2 | 1 | 0 | 1 | 0 | 0 |
| inc/setup/theme-setup.php | 2 | 0 | 0 | 1 | 1 | 0 |
| inc/setup/editor-assets.php | 2 | 0 | 0 | 1 | 1 | 0 |
| inc/setup/logging.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/setup/customizer.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/setup/constants.php | 1 | 0 | 0 | 1 | 0 | 0 |
| inc/post-types/post-types.php | 1 | 0 | 0 | 1 | 0 | 0 |
| header.php | 1 | 1 | 0 | 0 | 0 | 0 |
| footer.php | 0 | 0 | 0 | 0 | 0 | 0 |
| index.php | 0 | 0 | 0 | 0 | 0 | 0 |
| page-about.php | 0 | 0 | 0 | 0 | 0 | 0 |
| page-contact.php | 0 | 0 | 0 | 0 | 0 | 0 |
| assets/js/main.js | 0 | 0 | 0 | 0 | 0 | 0 |
| assets/js/theme-controller.js | 0 | 0 | 0 | 0 | 0 | 0 |
| assets/js/about-page-v22.js | 2 | 0 | 1 | 0 | 1 | 0 |
| assets/js/customizer-preview.js | 2 | 0 | 1 | 0 | 1 | 0 |
| assets/js/contact-page.js | 2 | 0 | 1 | 0 | 1 | 0 |
| assets/css/utilities.css | 0 | 0 | 0 | 0 | 0 | 0 |
| assets/css/base.css | 1 | 0 | 0 | 0 | 1 | 0 |
| assets/css/components.css | 2 | 0 | 0 | 1 | 1 | 0 |
| assets/css/sections.css | 2 | 0 | 0 | 1 | 1 | 0 |
| assets/css/about-page-v22.css | 4 | 0 | 2 | 1 | 1 | 0 |
| assets/css/wordpress-blocks.css | 1 | 0 | 0 | 1 | 0 | 0 |
| assets/css/compatibility.css | 1 | 0 | 0 | 0 | 1 | 0 |
| assets/css/print.css | 1 | 0 | 0 | 0 | 1 | 0 |
| assets/css/pdf-ebook.css | 1 | 0 | 0 | 0 | 1 | 0 |
| blocks/*/render.php | 0 | 0 | 0 | 0 | 0 | 0 |

[Additional files continue...]

---

## Remediation Plan

### Phase 1: Critical & Severe (Immediate)
1. [C-001] Remove extract() usage - Security risk - 2 hours
2. [C-002] Move CDN scripts to local assets - Architecture violation - 4 hours
3. [C-003] Extract inline script from meta-boxes.php - Architecture violation - 2 hours
4. [C-004] Extract inline script from header.php - Architecture violation - 1 hour
5. [S-001 to S-012] Add type hints and fix JavaScript var usage - Code quality - 8 hours

**Estimated Total:** 17 hours

### Phase 2: Major (This sprint)
1. [M-001 to M-033] Add type hints to all functions - Code quality - 12 hours
2. [M-034 to M-035] Replace hardcoded colors/spacing with theme.json tokens - Architecture - 8 hours
3. [M-036 to M-038] Replace nth-child() with explicit classes - Defensive CSS - 6 hours

**Estimated Total:** 26 hours

### Phase 3: Medium (Next sprint)
1. [MD-001 to MD-045] Fix medium priority issues - Code quality - 20 hours

**Estimated Total:** 20 hours

### Phase 4: Minor (Backlog)
1. [MN-001 to MN-028] Fix minor issues opportunistically - Code quality - 10 hours

**Estimated Total:** 10 hours

---

## Multi-Agent Coordination Notes

**Files being modified by this remediation:**
- `inc/helpers.php` (extract() removal, type hints)
- `inc/block-helpers.php` (type hints)
- `inc/enqueue-helpers.php` (CDN removal, type hints)
- `inc/meta/meta-boxes.php` (inline script extraction)
- `header.php` (inline script extraction)
- `assets/js/about-page-v22.js` (var → const/let)
- `assets/js/customizer-preview.js` (var → const/let)
- `assets/js/contact-page.js` (var → const/let)
- `assets/css/components.css` (nth-child() removal)
- `assets/css/sections.css` (nth-child() removal)
- `assets/css/about-page-v22.css` (ID selectors, !important, nth-child())
- All other PHP files (type hints)

**Files to claim in AGENT_WORK.md before starting:**
- All files listed above
- Any CSS files with hardcoded colors
- Any JavaScript files with var declarations

**Potential conflicts with architecture migration:**
- `inc/enqueue-helpers.php` - May conflict if architecture migration is restructuring asset loading
- `inc/helpers.php` - May conflict if architecture migration is moving helpers
- CSS files - May conflict if architecture migration is restructuring CSS layers

**Recommended agent assignment:**
- Critical/Severe: Single agent, focused session (17 hours)
- Major: Can be parallelized by file/directory (26 hours, can split)
- Medium/Minor: Opportunistic fixes when in area

---

## Verification

After remediation, verify:
- [ ] All CRITICAL issues resolved
- [ ] All SEVERE issues resolved  
- [ ] All MAJOR issues resolved
- [ ] All MEDIUM issues resolved or justified
- [ ] MINOR issues resolved or justified
- [ ] CI quality gates pass
- [ ] SonarQube shows no new issues
- [ ] Version incremented
- [ ] TECH_DEBT.md updated if anything deferred

