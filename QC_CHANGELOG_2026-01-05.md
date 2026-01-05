# QC Changelog — 2026-01-05

This changelog is intended to make post-change QC deterministic across viewports (375/768/1024/1440) and light/dark.

## Theme token + dark mode contract changes

### `kunaal-theme/assets/css/tokens.css`
- **Dark mode accent no longer repurposes “blue” into orange**:
  - `--k-color-accent` is now **blue** in dark mode again.
  - `--k-underline-blue-color` follows `--k-color-accent` (blue) in dark mode.
- **WP preset color variables now follow tokens in dark mode**:
  - In `:root[data-theme="dark"]`, `--wp--preset--color--ink/background/blue/...` are overridden to token-derived values.
  - **QC target**: headings/text styled by `theme.json` (WP global styles) must render light in dark mode (no “black headings on dark bg”).
- **Chart palette canonicalized**:
  - Added `--k-chart-*` palette for light and dark mode.
- **Added**: `--k-color-accent-solid-hover` (light + dark values) for solid hover backgrounds.

### `kunaal-theme/assets/css/dark-mode.css`
- Removed hardcoded blue shadow/muted values; now maps to token vars.
- Removed hardcoded chart palette; now maps `--chart-*` → `--k-chart-*`.

### `kunaal-theme/assets/css/variables.css`
- Converted to **mapping-only** for chart vars:
  - `--chart-*` now maps to `--k-chart-*` (no chart hex literals here).

## Legibility fixes (dark mode + contracts)

### `kunaal-theme/assets/css/utilities.css`
- Removed dark-mode special-casing that forced section underline to orange and heading text to hardcoded `#f0f0f0 !important`.
- **QC target**: section underline remains accent-colored in dark mode and headings remain readable without duct-tape overrides.

### `kunaal-theme/assets/css/about-page.css`
- Removed “orange accent” overrides and hardcoded fallbacks.
- Replaced multiple `#fff` and orange RGBA highlights with token-driven values:
  - Focus/hover backgrounds now use `color-mix(... var(--k-color-accent) ...)`.
- **QC target**: About page accents match theme accent in dark mode; all headings readable; focus styles visible.

### `kunaal-theme/assets/css/blocks.css`
- TOC link hover/active colors now use `var(--k-color-accent)` in all modes.
- Removed dark-mode-only orange override.

### `kunaal-theme/assets/css/components.css`
- Subscribe form button hover now uses `--k-color-accent-solid-hover` and token-based shadow.
- Removed dark-mode warm/orange override for subscribe button.
- **QC target**: subscribe CTA remains on-brand + readable in dark mode; hover is visible.

## Block CSS hardcode reduction (mechanical tokenization)

### `kunaal-theme/blocks/**/style.css` (many files)
- Replaced common theme-palette literals with tokens (mechanical replacements), e.g.:
  - `#1E5AFF` → `var(--k-color-accent)`
  - `#0b1220` → `var(--k-color-ink)`
  - `#7D6B5D` → `var(--k-color-warm)`
  - `#F9F7F4` / `#F5F3EF` / `#F8F6F3` / `#FDFCFA` → corresponding `--k-color-*` tokens
- **Excluded**: `kunaal-theme/blocks/custom-toc/style.css` (claimed by another agent at the time).
- **QC target**: blocks that previously used those literal palette values should look identical in light mode, and have improved dark-mode consistency.

## Block JS hardcode reduction + safety

### `kunaal-theme/blocks/network-graph/view.js`
- Uses `--k-chart-*` palette (with fallback to `--chart-*` for compatibility).
- Debug-gated `console.error`.

### `kunaal-theme/blocks/flow-diagram/view.js`
- Uses `--k-chart-*` palette (with fallback to `--chart-*`).
- Debug-gated `console.error`.

### `kunaal-theme/blocks/data-map/view.js`
- Default colors now come from CSS tokens (computed), not hardcoded hex.
- Tile URL selection prefers `window.kunaalTheme.mapTiles.{light,dark}` (fallback to existing Carto URLs).
- Debug-gated `console.error`.
- **QC target**: map dots/gradients remain readable in dark mode; theme toggle updates tiles; no console noise in prod.

## Contract documentation + enforcement

### `kunaal-theme/UI_CONTRACTS.md`
- Updated dark-mode contract to reflect: **accent stays blue**; added note about WP preset alignment for legibility.

### `kunaal-theme/scripts/check-ui-contracts.sh`
- Added drift checks for the RCA root cause:
  - Fail if `#E8A87C` appears outside `tokens.css` in theme CSS.
  - Fail if `tokens.css` hardcodes orange for `--k-color-accent` or `--k-underline-blue-color`.

## Version bump

### `kunaal-theme/style.css`
- Version bumped: `4.99.18` → `4.99.19`

## QC checklist (quick)

- **Theme toggle**: switch light↔dark; confirm no “black headings on dark bg” anywhere (esp. subscribe panel `h4`).
- **Links**: hover/focus-visible underline remains accent blue in both modes.
- **Subscribe panel**:
  - heading and body text readable in dark
  - button hover visible in both modes
- **About page**: accents, titles, and focus rings readable; no orange-only overrides.
- **Blocks**: spot-check a few tokenized blocks (callout, footnote, author-bio, toc, pullquote) in both modes.


