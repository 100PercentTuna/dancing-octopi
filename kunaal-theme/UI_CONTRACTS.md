# UI Contracts

This file is the **single reference** for “what owns what” in the UI system.

If you change a motif (links, rules, cards, filters), you must:
1) update this file, and
2) update drift checks so regressions fail CI.

---

## Canonical ownership map

| Motif | Canonical markup (PHP) | Canonical CSS owner | Canonical JS owner | Notes |
|---|---|---|---|---|
| Link interaction (animated underline) | Apply `.u-link` to “text links”; editor links via `.prose a` | `assets/css/components.css` (Links section) or `assets/css/utilities.css` (if treated as a utility) | none | **Default state: no underline.** Hover/focus-visible animates underline L→R. |
| Section header rule (gray base + blue segment) | `template-parts/components/section-head.php` renders `.sectionRule` | `assets/css/sections.css` | none | Only one rule per section head. No border-bottom hacks elsewhere. |
| Card/tile | `template-parts/components/card.php` | `assets/css/components.css` | `assets/js/main.js` (only if cards have behavior) | Title readable at rest. Cards must not inherit link underline utilities. |
| Filter bar | `template-parts/components/filter-bar.php` | `assets/css/components.css` | `assets/js/main.js` (filter module) | Uses **data-* contracts**; optional controls allowed. |
| Rabbit hole capsule | `template-parts/components/capsule.php` (or About template renders same contract) | `assets/css/pages/about.css` (scoped) | `assets/js/about-page-v22.js` (enhancement only) | Pills render server-side. JS may animate layout. |
| About hero mosaic | About template (slot classes) | `assets/css/pages/about.css` (scoped) | `assets/js/about-page-v22.js` (optional) | No nth-child layout; explicit slot classes; dog-ear on slot 3. |
| Header controls | `header.php` | `assets/css/header.css` | `assets/js/main.js` | Use a shared `.iconBtn` sizing for toggle + menu. |

> If any motif has more than one canonical owner, you are doing it wrong. Pick one and delete the rest.

---

## Contract: Link interaction (animated underline)

### Goal
- Links look like normal text at rest (no underline).
- On hover/focus-visible: a blue underline animates left→right.

### Canonical API
- CSS utility class: `.u-link`
- Editor links: `.prose a` may share the same rule set.

### Forbidden
- `theme.json` forcing link underline (`textDecoration: underline`)
- broad selector lists that set `text-decoration: underline`
- separate underline implementations in multiple CSS files

### Acceptance checks
- No underlines at rest in nav, prose, footer.
- Hover/focus-visible animates underline.
- Cards and icon buttons do not get underlines.

---

## Contract: Section header rule

### Goal
- One single rule line per section head:
  - thin gray full width
  - thick blue segment overlay on the left (shorter than full width)

### Markup contract
`section-head.php` must output:

- container (e.g., `.sectionHead`)
- title element
- a dedicated `.sectionRule` element

No other element draws the rule.

### Forbidden
- `border-bottom` on `.sectionHead` used as the rule
- heading-level pseudo-elements drawing their own rules (`.u-section-underline` etc.)

### Acceptance checks
- Home + archives show one rule only (no double lines).

---

## Contract: Filter bar (data-* hooks)

### Why data-* (not IDs)
IDs are brittle when components are reused; data hooks keep contracts stable.

### Markup contract (required)
Root:
- `data-ui="filter"`

Actions (buttons):
- `data-action="toggle|apply|reset"`

Roles (optional elements; JS must tolerate absence):
- `data-role="search"` (input)
- `data-role="sort"` (select)
- `data-role="topic-menu"` (container)
- `data-role="topic-item"` (button)
- `data-role="results"` (results container)
- `data-role="count"` (count label)

### Optional controls + placeholders
- Default: omit optional controls if not configured for that page.
- If placeholders are enabled (flag), render a subtle placeholder element (aria-hidden) instead of breaking layout.

### JS behavior contract
- JS binds via event delegation on `[data-ui="filter"]`.
- No `getElementById('filterBtn')`-style contracts for filter UI.

### Acceptance checks
- Works on home + essays + jottings.
- No JS errors if search or sort is omitted.

---

## Contract: Card/tile

### Markup contract
Cards must use the component partial, and include:
- `.card`
- `.card__media` (image)
- `.card__scrim`
- `.card__title`
- `.card__meta` (optional)

Optional elements omitted by default unless placeholders enabled.

### Forbidden
- copy-paste card markup in templates
- card title links inheriting `.u-link` underline behavior

### Acceptance checks
- Title readable at rest across pages
- consistent hover outline (single ring)
- no underline on card titles

---

## Deprecations (delete over time)

| Deprecated | Replace with | Removal |
|---|---|---|
| `.uBlue` | `.u-link` | remove after templates migrated |
| `.u-section-underline` | `section-head.php` + `.sectionRule` | remove after migration |
| Filter JS bound to IDs (`#filterBtn`, `#topicMenu`) | data-* hooks under `[data-ui="filter"]` | remove after migration |

---

## Required tooling hooks

These rules must be enforced by `scripts/check-ui-contracts.sh`:
- theme.json must not force link underline
- only one underline implementation exists
- section rule exists only in `assets/css/sections.css`
- filter JS does not bind to IDs for primary contracts
- deprecated classes do not persist past removal window
