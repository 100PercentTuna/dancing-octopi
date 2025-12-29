# Technical Debt Register

This document tracks known technical debt in the Kunaal Theme.

---

## Active Items

### [Visual Regression] Automated Visual Testing Not Implemented
- **Added**: 2024-12-29
- **Severity**: Low
- **Impact**: Manual visual testing required for each change
- **Suggested fix**: Implement Percy, Chromatic, or BackstopJS for automated visual diff testing in CI
- **Workaround**: Use `tests/visual-checklist.md` for manual regression testing

### [Architecture] Scattered Dark Mode Rules
- **Added**: 2024-12-29
- **Severity**: Low
- **Impact**: Dark mode rules exist in multiple files (header.css, about-page.css, contact-page.css)
- **Suggested fix**: Consolidate all dark mode rules into `dark-mode.css`
- **Note**: Some component-specific rules may need to stay with their components

### [Map] Places Data Dependency
- **Added**: 2024-12-29
- **Severity**: Info
- **Impact**: Map shows no colored countries when Places data is not configured in Customizer
- **Note**: This is expected behavior - not a bug, just user configuration dependent

---

## Resolved Items

### [CSS] nth-child Layout Positioning
- **Resolved**: 2024-12-29
- **Resolution**: Replaced with explicit grid-column positioning using :nth-of-type selectors
  and display:contents strategy for media grid alignment

### [CSS] Hardcoded Colors in Components
- **Resolved**: 2024-12-29
- **Resolution**: Audited and replaced hardcoded colors with CSS custom property tokens

### [CSS] Card Overlay Text Not Always White
- **Resolved**: 2024-12-29
- **Resolution**: Added explicit white color rules for `.overlay`, `.tTitle`, and children

---

## How to Add Tech Debt

When discovering technical debt:

1. Add an entry under "Active Items" with:
   - Title in `### [Category] Description` format
   - **Added**: Date
   - **Severity**: Critical / Major / Medium / Low / Info
   - **Impact**: What problems does this cause?
   - **Suggested fix**: How should it be resolved?
   - **Workaround**: Any temporary solution (optional)

2. When resolving tech debt:
   - Move the item to "Resolved Items"
   - Add **Resolved** date and **Resolution** description
   - Delete unnecessary fields

