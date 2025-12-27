# PurgeCSS Usage Guide

## Installation

If Node.js/npm is not installed, you can:
1. Install Node.js from https://nodejs.org/
2. Or use PurgeCSS via Docker
3. Or use the online tool at https://purgecss.com/

## Setup

```bash
npm install
```

This will install `@fullhuman/purgecss` as a dev dependency.

## Running PurgeCSS

### Basic Usage
```bash
npm run purgecss
```

This will:
- Scan all PHP, JS, and HTML files in `kunaal-theme/`
- Analyze CSS files (style.css and all modular CSS files)
- Remove unused CSS selectors
- Output purged CSS to `kunaal-theme/assets/css/purged/`

### Watch Mode (for development)
```bash
npm run purgecss:watch
```

## Configuration

The configuration is in `purgecss.config.js`. Key settings:

- **content**: Files to scan for class usage
- **css**: CSS files to purge
- **safelist**: Classes/patterns to always keep (even if unused)
- **rejected**: Log rejected selectors to console
- **rejectedCss**: Generate file with rejected CSS for review

## Safelist Patterns

The config includes safelists for:
- Dynamic JavaScript classes (`is-*`, `has-*`, `js-*`)
- WordPress admin classes (`admin-*`, `wp-*`)
- Theme-specific dynamic classes (`revealed`, `open`, `active`)
- Animation classes (`stagger-*`, `fade-*`, `scale-*`)
- Gutenberg block classes (`wp-block-*`, `block-*`)
- CSS variables (`--*`)
- Pseudo-classes and pseudo-elements

## Reviewing Results

After running PurgeCSS:
1. Check `kunaal-theme/assets/css/purged/` for output files
2. Review `rejected.css` (if enabled) to see what was removed
3. Test the site thoroughly to ensure no styles are missing
4. If styles are missing, add them to the safelist in `purgecss.config.js`

## Integration with Build Process

For production, you can:
1. Run PurgeCSS as part of your build process
2. Replace original CSS files with purged versions
3. Or use PurgeCSS in watch mode during development

## Alternative: Browser DevTools

If you prefer not to use PurgeCSS:
1. Open Chrome DevTools
2. Go to Coverage tab
3. Record page loads
4. Identify unused CSS rules
5. Manually remove unused code

## Notes

- PurgeCSS works best with static class names
- Dynamic classes (added via JavaScript) should be in the safelist
- Test thoroughly after purging to catch any missing styles
- Keep a backup of original CSS files

