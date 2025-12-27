/**
 * PurgeCSS Configuration
 * Removes unused CSS from the theme
 * 
 * Usage:
 *   npx purgecss --config ./purgecss.config.js
 * 
 * Or add to package.json:
 *   "scripts": {
 *     "purgecss": "purgecss --config ./purgecss.config.js"
 *   }
 */

module.exports = {
  content: [
    './kunaal-theme/**/*.php',
    './kunaal-theme/**/*.js',
    './kunaal-theme/**/*.html',
  ],
  css: [
    './kunaal-theme/style.css',
    './kunaal-theme/assets/css/*.css',
  ],
  output: './kunaal-theme/assets/css/purged/',
  defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || [],
  safelist: [
    // Dynamic classes that might be added via JavaScript
    /^is-/,
    /^has-/,
    /^js-/,
    /^revealed$/,
    /^open$/,
    /^active$/,
    /^loading$/,
    /^error$/,
    /^success$/,
    /^stagger-\d+$/,
    /^fade-/,
    /^scale-/,
    /^slide-/,
    // WordPress admin classes
    /^admin-/,
    /^wp-/,
    // Theme-specific dynamic classes
    /^data-theme/,
    /^page-template-/,
    /^single-/,
    /^archive-/,
    /^category-/,
    /^tag-/,
    // Gutenberg block classes
    /^wp-block-/,
    /^block-/,
    // Animation classes
    /^animate-/,
    /^@keyframes/,
    // CSS variables
    /^--/,
    // Pseudo-classes and pseudo-elements
    /::before/,
    /::after/,
    /:hover/,
    /:focus/,
    /:active/,
    /:visited/,
    /:first-child/,
    /:last-child/,
    /:nth-child/,
  ],
  fontFace: true,
  keyframes: true,
  variables: true,
  rejected: true, // Log rejected selectors
  rejectedCss: true, // Generate file with rejected CSS
}

