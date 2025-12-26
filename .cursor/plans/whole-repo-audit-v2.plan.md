# Whole-Repo Audit v2 Plan (Exhaustive, Read-Only)

## Goal

Rewrite `AUDIT-KUNAAL-THEME.md` so it fully satisfies all non-negotiable requirements:

- Full **Command Log** with exact commands, exact counts (excluding `kunaal-theme/specs/**`), and top 10 hits.
- Complete **Master Findings Index** ingesting **all prior findings mentioned in chat** (including narrative claims), deduped to canonical IDs with Confirmed/Unconfirmed/Refuted + evidence.
- **Coverage Table** with a row for **every** PHP file (~71), JS file (~74), CSS file (~58), and block (~51).
- **Updated Quantified Inventory** with exact counts (no estimates) excluding specs.
- **Canonical Findings Register** containing the full set of findings (target 80–150), each with required fields.
- Full **Security Deep Scan**, **Performance Deep Scan**, **Asset Load Matrix**, **Duplication deliverables**, **Dead code**, **Static checks**, **Refactor plan**, and only then **Saturation Statement**.

## Constraints (hard)

- Do not modify `kunaal-theme/**` code (PHP/JS/CSS/templates/blocks). Read-only analysis only.
- Allowed: create audit helper scripts under `audit/` (read-only analysis scripts), and modify `AUDIT-KUNAAL-THEME.md` (report artifact).
- Exclude fast-scan blockers: `**/node_modules/**`, `**/vendor/**`, `**/.git/**`, `**/build/**`, `**/dist/**`, and **always exclude** `kunaal-theme/specs/**` from counts.

## Execution checklist

### A) Tooling and workspace

- Verify command availability (`rg` optional). Prefer PowerShell (`Select-String`) for exact counts if `rg` is absent.
- Create `audit/` helper scripts:
- `audit/file_inventory.ps1` (exact file lists for PHP/JS/CSS/blocks excluding specs)
- `audit/scan_counts.ps1` (exact counts + top hits for mandatory scans)
- `audit/coverage_table.ps1` (per-file heuristic summary: inputs/sinks/hooks/enqueues)
- `audit/css_dup_selectors.ps1` (top 30 duplicated selectors with full file:line occurrences)
- `audit/window_globals.ps1` (top 30 window.* globals with define vs consume sites)
- `audit/external_libs.ps1` (load-path matrix: enqueue vs injection vs usage)
- `audit/php_lint.ps1` (php -l sweep with totals and failures)

### B) Mandatory scans (for Command Log)

Run scans excluding `kunaal-theme/specs/**` and excluded dirs:

- Hooks/priorities: `add_action`, `add_filter`, explicit priority usage
- Enqueues: `wp_enqueue_*`, `wp_register_*`, `wp_add_inline_*`, `wp_localize_script`, loader tag filters
- Inputs/security: `$_GET/$_POST/$_REQUEST`, `wp_ajax_*`, REST patterns, nonce/caps, escaping, `$wpdb`, JS sinks
- Queries/perf: `WP_Query`, `get_posts`, `query_posts`, `meta_query/tax_query`, `get_option/theme_mod/meta in loops`, transients/cache
- CSS/JS quality: `!important`, broad selectors, duplicated selectors; `window.*`, observers/listeners, intervals

### C) Master Findings Index (complete)

- Parse prior chat findings into an “old findings list” (IDs and narrative items).
- For each old item:
- confirm/refute via workspace evidence (file+line + snippet)
- map `old → canonical`
- mark status and cross-links

### D) Coverage Table (file-by-file)

- For each PHP file: responsibilities (heuristic tags), inputs, outputs/sinks, hooks/enqueues, linked canonical IDs.
- For each JS file: responsibilities, sinks, globals, listeners, remote deps.
- For each CSS file: responsibilities, globals, `!important`, duplicated selectors participation.
- For each of 51 blocks: file presence (`block.json/edit.js/render.php/style.css/view.js`), notable sinks, notable external deps.

### E) Canonical Findings Register (full)

- Generate 80–150 canonical findings from:
- confirmed prior findings
- scan-derived issues (grouped by pattern with occurrence counts + full lists where required)
- Ensure every finding includes required fields (ID/category/severity/confidence/location/snippet/impact/how to confirm/remediation).

### F) Deep scans + matrices + plan

- Security deep scan: enumerate every input vector and JS sink; evaluate nonce/caps/sanitization/escaping and abuse potential.
- Performance deep scan: backend + frontend, including hotspots and repeated patterns.
- Asset load matrix by page type (templates) and by block view scripts.
- Duplication & competing behavior: include full deliverables + adjacency graph with failure modes.
- Dead code candidates: evidence and how-to-prove-unused steps.
- Static checks: php -l (include command + summary); tooling absent scans if no configs.
- Refactor Plan v2: staged, tasks, risk, verification, rollback.

## Exact command list (to be executed)

> These will be executed from repo root: `C:\Users\Wadhwa Kunaal\OneDrive - The Boston Consulting Group, Inc\Desktop\Personal\cursor-github repos\dancing-octopi`

### 1) Tool checks

- `powershell -NoProfile -Command "Set-Location '<repo>'; Get-Command rg -ErrorAction SilentlyContinue; php -v"`

### 2) File inventory (exact lists)

- `powershell -NoProfile -ExecutionPolicy Bypass -File audit\\file_inventory.ps1`

### 3) Mandatory scan counts + top hits (exact, excluding specs)

- `powershell -NoProfile -ExecutionPolicy Bypass -File audit\\scan_counts.ps1`

### 4) php -l sweep

- `powershell -NoProfile -ExecutionPolicy Bypass -File audit\\php_lint.ps1`

### 5) Coverage tables

- `powershell -NoProfile -ExecutionPolicy Bypass -File audit\\coverage_table.ps1`

### 6) Duplication artifacts

- `powershell -NoProfile -ExecutionPolicy Bypass -File audit\\css_dup_selectors.ps1`
- `powershell -NoProfile -ExecutionPolicy Bypass -File audit\\window_globals.ps1`
- `powershell -NoProfile -ExecutionPolicy Bypass -File audit\\external_libs.ps1`

### 7) Assemble report

- Overwrite `AUDIT-KUNAAL-THEME.md` with required sections in exact order, embedding:
- command log output
- master findings index mapping table
- full coverage tables (PHP/JS/CSS/blocks)
- exact quantified inventory
- full canonical findings register (80–150)
- deep scans and matrices

## Exit criteria (do not claim saturation until all true)

- Command Log has all required scans with **exact** counts excluding specs and top 10 hits.
- Master Findings Index includes **all prior findings mentioned in this chat** with mapping and confirm/refute evidence.