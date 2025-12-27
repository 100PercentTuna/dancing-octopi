# CSS Refactoring Plan

## Current State
- **File:** `kunaal-theme/style.css`
- **Size:** 4,661 lines
- **Status:** Monolithic file with all styles

## Proposed Structure

### Modular Files
1. **`assets/css/variables.css`** - CSS custom properties (root variables)
2. **`assets/css/base.css`** - Base resets, body, typography
3. **`assets/css/layout.css`** - Container, grid, layout utilities
4. **`assets/css/components.css`** - Reusable components (buttons, cards, etc.)
5. **`assets/css/header.css`** - Header, navigation, theme toggle
6. **`assets/css/footer.css`** - Footer styles
7. **`assets/css/blocks.css`** - Gutenberg block styles
8. **`assets/css/pages.css`** - Page-specific styles (home, about, contact)
9. **`assets/css/utilities.css`** - Utility classes
10. **`assets/css/dark-mode.css`** - Dark mode overrides

### Implementation Options

#### Option 1: @import (Simple, no build process)
```css
/* style.css - WordPress theme header + imports */
@import url('assets/css/variables.css');
@import url('assets/css/base.css');
@import url('assets/css/layout.css');
/* ... */
```

**Pros:**
- No build process needed
- Easy to implement
- Works immediately

**Cons:**
- Multiple HTTP requests (unless combined by server/CDN)
- Slightly slower initial load

#### Option 2: Build Process (Recommended for production)
Use a build tool (Webpack, Vite, etc.) to:
1. Combine CSS files in order
2. Minify
3. Generate source maps
4. Output single `style.css` for WordPress

**Pros:**
- Single HTTP request
- Optimized output
- Better performance

**Cons:**
- Requires build setup
- More complex deployment

### Migration Strategy

1. **Phase 1: Extract Variables** (Low risk)
   - Move `:root` variables to `variables.css`
   - Import in `style.css`
   - Test thoroughly

2. **Phase 2: Extract Base Styles** (Low risk)
   - Move body, typography, resets to `base.css`
   - Test thoroughly

3. **Phase 3: Extract Layout** (Medium risk)
   - Move container, grid, layout utilities
   - Test on all page types

4. **Phase 4: Extract Components** (Medium risk)
   - Move reusable components
   - Test all components

5. **Phase 5: Extract Page-Specific** (Low risk)
   - Move page-specific styles
   - Test each page type

6. **Phase 6: Extract Blocks** (Medium risk)
   - Move block styles
   - Test all blocks

### Dead Code Detection

#### Method 1: PurgeCSS (Recommended)
```bash
npm install -D purgecss
npx purgecss --css style.css --content "**/*.php" "**/*.js" --output ./purged/
```

#### Method 2: Manual Analysis
1. Extract all CSS selectors
2. Search codebase for each selector
3. Mark unused selectors
4. Remove after verification

#### Method 3: Browser DevTools
1. Use Coverage tab in Chrome DevTools
2. Record page loads
3. Identify unused CSS rules
4. Remove unused code

### Next Steps

1. **Immediate:** Start with Option 1 (@import) for variables and base
2. **Short-term:** Extract 2-3 more modules
3. **Long-term:** Set up build process if performance becomes critical

### Files to Create

- [ ] `assets/css/variables.css`
- [ ] `assets/css/base.css`
- [ ] `assets/css/layout.css`
- [ ] `assets/css/components.css`
- [ ] `assets/css/header.css`
- [ ] `assets/css/footer.css`
- [ ] `assets/css/blocks.css`
- [ ] `assets/css/pages.css`
- [ ] `assets/css/utilities.css`
- [ ] `assets/css/dark-mode.css`

### Testing Checklist

- [ ] All pages render correctly
- [ ] All components display properly
- [ ] Dark mode works
- [ ] Responsive breakpoints work
- [ ] No visual regressions
- [ ] Performance not degraded




