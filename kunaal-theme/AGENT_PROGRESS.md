# Agent Progress Tracker - Critical Continuation Mission

## P0 — TASK 1: FIX ABOUT HERO MOSAIC + DOG-EAR + RESPONSIVENESS

### 1.1 Fix About-page scoping correctly
- [ ] Add body class filter for `kunaal-about-v22`
- [ ] Update about-page-v22.css to use `.kunaal-about-v22` scoping
- [ ] Remove `page-template-page-about` and `page-id-about` selectors
- [ ] Verify: grep returns no `page-template-page-about` in CSS

### 1.2 Stop using :nth-child() for hero photo placement
- [ ] Add stable classes `hero-photo--1` through `hero-photo--10` in page-about.php
- [ ] Update CSS to use stable classes instead of nth-child
- [ ] Verify: grep returns no `hero-photo:nth-child` in CSS

### 1.3 Fix the hero grid so 10 photos + hero text have intentional "slots"
- [ ] Implement explicit CSS grid with defined columns
- [ ] Assign each `.hero-photo--N` to explicit grid cell
- [ ] Fix height rules (remove contradictory min-height/max-height)
- [ ] Ensure no horizontal scroll at all breakpoints
- [ ] Verify: No implicit rows, all photos in 2-row grid

### 1.4 Fix dog-ear accent so it is robust and always visible
- [ ] Ensure `.hero-photo.has-accent` is positioned relative
- [ ] Implement dog-ear using border-triangle or clip-path
- [ ] Tokenize size with `--k-dogear`
- [ ] Verify: Exactly one photo has accent, visible on desktop/mobile

### 1.5 Add responsive breakpoints to stop "thin/wide" breakage
- [ ] Add medium breakpoint (max-width: 1200px)
- [ ] Add ultra-wide handling (min-width: 1800px)
- [ ] Use clamp() for gutters/padding
- [ ] Verify: Hero layout coherent at 1000px–1300px, no overlap/overflow

## P0 — TASK 2: UNDERLINES = SINGLE SOURCE OF TRUTH

- [ ] Fix utilities.css layer structure (single @layer utilities wrapper)
- [ ] Verify canonical double underline defined ONCE
- [ ] Make nav/footer/prose links use same mechanism
- [ ] Remove competing underline implementations
- [ ] Verify: grep shows only canonical definition + tokens

## P0 — TASK 3: EDITOR STYLING (WYSIWYG) AFTER PATH B

- [ ] Ensure editor enqueues modular CSS stack
- [ ] Stop enqueueing only style.css in editor
- [ ] Verify: Editor gets tokens/base/wordpress-blocks/utilities/blocks

## P0 — TASK 4: ABOUT CATEGORY INLINE VARS ACTUALLY OUTPUT

- [ ] Fix wp_add_inline_style timing
- [ ] Attach to registered handle (kunaal-theme-tokens or kunaal-about-page-v22)
- [ ] Verify: Category CSS vars actually output

## P0 — TASK 5: CI QUALITY GATES + NO REGRESSIONS

- [ ] Ensure workflow is at repo root `.github/workflows/...`
- [ ] Add checks for BOM, php -l, node --check, render.php functions, console.log
- [ ] Verify: Workflow runs on push/PR

## FINAL VERIFICATION

- [ ] PHP lint passes
- [ ] JS syntax passes
- [ ] No BOM found
- [ ] No render.php functions
- [ ] No console.log
- [ ] Hero sanity checks pass
