# CSS Architecture

This directory contains the modular CSS system for Kunaal Theme, organized by layer and responsibility.

## Design Tokens (Single Source of Truth)

**File:** `tokens.css`

Contains all design tokens as CSS custom properties:
- Color tokens (`--k-color-*`)
- Spacing tokens (`--k-space-*`)
- Typography tokens (`--k-font-*`)
- Layout tokens (`--k-layout-*`)
- Underline motif tokens (`--k-underline-*`)

Legacy variable mappings are provided for backward compatibility during migration.

## CSS Cascade Layers

The theme uses native CSS cascade layers for proper specificity management:

```css
@layer tokens, base, utilities, components, blocks;
```

### Layer Order

1. **tokens** - Design tokens only (no styling rules)
2. **base** - Resets, typography defaults
3. **utilities** - Utility classes (progress bar, lazy loading, double underline)
4. **components** - Reusable components (cards, buttons, panels, footer)
5. **blocks** - Block-specific styles

## File Organization

### Core Files (Load First)
- `tokens.css` - Design tokens
- `variables.css` - Legacy mappings and chart colors
- `base.css` - Resets and typography
- `dark-mode.css` - Dark mode variables

### Layout & Structure
- `layout.css` - Layout containers and grid
- `header.css` - Header/masthead styles
- `sections.css` - Sections and grid layouts

### Components & Utilities
- `components.css` - Cards, buttons, panels, footer
- `utilities.css` - Progress bar, lazy loading, **double underline motif**
- `filters.css` - Toolbar/filter styles

### Content & Pages
- `pages.css` - Archive, article, prose content, page utilities
- `blocks.css` - Custom block styles
- `wordpress-blocks.css` - WordPress core block overrides

### Specialized
- `motion.css` - Motion primitives and animations
- `compatibility.css` - Print, reduced motion, cross-browser fixes
- `about-page.css` - About page styles
- `contact-page.css` - Contact page styles

## Double Underline Motif

The canonical double underline implementation is in `utilities.css`:

- **Utility class:** `.u-underline-double`
- **Block style class:** `.is-style-double-underline`
- **Token-driven:** Uses `--k-underline-*` tokens

### Usage

```html
<!-- Utility class -->
<a href="#" class="u-underline-double">Link text</a>

<!-- Block style (Gutenberg) -->
<p class="is-style-double-underline">Paragraph text</p>
```

### Implementation Details

- Gray line: `text-decoration` (reliable across line breaks)
- Blue line: `background-image` gradient positioned above gray
- Supports multi-line wrapping with `box-decoration-break: clone`
- Maintains accessible focus states

## Adding New Styles

### Adding a New Motif

1. **Define tokens** in `tokens.css`:
   ```css
   @layer tokens {
     :root {
       --k-new-motif-color: #1E5AFF;
       --k-new-motif-size: 2px;
     }
   }
   ```

2. **Implement in appropriate layer**:
   - Utilities → `utilities.css`
   - Components → `components.css`
   - Blocks → `blocks.css`

3. **Use tokens** (never hardcode values):
   ```css
   @layer utilities {
     .u-new-motif {
       color: var(--k-new-motif-color);
       border-width: var(--k-new-motif-size);
     }
   }
   ```

### Adding a New Component

1. Add to `components.css` within `@layer components`
2. Use design tokens from `tokens.css`
3. Follow BEM-like naming: `.kunaal-component__element--modifier`

### Adding Block Styles

1. Add to `blocks.css` within `@layer blocks`
2. Register block style in `inc/blocks.php` if needed
3. Use tokens for consistency

## Enqueue Order

CSS files are enqueued in this order (see `inc/enqueue-helpers.php`):

1. `tokens.css` (must load first)
2. `variables.css`
3. `base.css`
4. `dark-mode.css`
5. `layout.css`
6. `header.css`
7. `components.css`
8. `utilities.css`
9. `filters.css`
10. `sections.css`
11. `pages.css`
12. `blocks.css`
13. `wordpress-blocks.css`
14. `motion.css`
15. `compatibility.css`
16. `about-page.css` (conditional)
17. `contact-page.css` (conditional)

## Migration Notes

- Legacy variables (e.g., `--blue`, `--ink`) are mapped from tokens for backward compatibility
- All new code should use token names (`--k-*`)
- `style.css` contains only the theme header - no styling rules

