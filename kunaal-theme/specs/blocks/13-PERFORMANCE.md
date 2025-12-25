# Performance Optimization Specification

> **Scope:** All blocks and theme assets  
> **Priority:** High (Infrastructure)  
> **Goal:** Fast initial load, smooth interactions, minimal resource usage

---

## 1. Overview

This specification covers performance optimizations for the theme's advanced blocks, focusing on:
- Lazy loading of heavy components (charts, maps, D3.js)
- Code splitting to reduce initial bundle size
- Image optimization for responsive delivery
- Runtime performance for animations and interactions

### 1.1 Performance Targets

| Metric | Target | Tool |
|--------|--------|------|
| Largest Contentful Paint (LCP) | < 2.5s | Lighthouse |
| First Input Delay (FID) | < 100ms | Lighthouse |
| Cumulative Layout Shift (CLS) | < 0.1 | Lighthouse |
| Total Blocking Time (TBT) | < 300ms | Lighthouse |
| Initial JS Bundle | < 100KB gzipped | webpack-bundle-analyzer |
| Chart JS (lazy) | < 50KB gzipped | webpack-bundle-analyzer |
| Map JS (lazy) | < 60KB gzipped | webpack-bundle-analyzer |

---

## 2. Lazy Loading Strategy

### 2.1 Block-Level Lazy Loading

Heavy blocks load their JavaScript only when needed:

```javascript
// Block registration (lightweight shell)
registerBlockType('kunaal/heatmap', {
  // ... metadata
  edit: function HeatmapEditWrapper(props) {
    return (
      <LazyBlockLoader
        loader={() => import(
          /* webpackChunkName: "heatmap-edit" */
          './HeatmapEdit'
        )}
        fallback={<HeatmapPlaceholder />}
        {...props}
      />
    );
  },
  save: () => null, // Dynamic block, PHP renders
});
```

### 2.2 Lazy Loader Component

```jsx
// components/LazyBlockLoader.js
import { Suspense, lazy } from '@wordpress/element';

export function LazyBlockLoader({ loader, fallback, ...props }) {
  const Component = lazy(loader);
  
  return (
    <Suspense fallback={fallback || <BlockPlaceholder />}>
      <Component {...props} />
    </Suspense>
  );
}

function BlockPlaceholder() {
  return (
    <div className="kunaal-block-placeholder">
      <Spinner />
      <p>Loading...</p>
    </div>
  );
}
```

### 2.3 Frontend Lazy Loading

Charts and maps load when scrolled into viewport:

```javascript
// assets/js/lazy-blocks.js
document.addEventListener('DOMContentLoaded', () => {
  const lazyBlocks = document.querySelectorAll('[data-lazy-block]');
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        loadBlock(entry.target);
        observer.unobserve(entry.target);
      }
    });
  }, {
    rootMargin: '200px', // Load 200px before visible
  });
  
  lazyBlocks.forEach(block => observer.observe(block));
});

async function loadBlock(element) {
  const blockType = element.dataset.lazyBlock;
  
  // Show loading state
  element.classList.add('is-loading');
  
  try {
    // Dynamic import based on block type
    const module = await import(
      /* webpackChunkName: "[request]" */
      `./blocks/${blockType}/view.js`
    );
    
    // Initialize the block
    module.init(element);
    element.classList.remove('is-loading');
    element.classList.add('is-loaded');
  } catch (error) {
    console.error(`Failed to load ${blockType}:`, error);
    element.classList.add('is-error');
  }
}
```

### 2.4 Skeleton Placeholders

```css
.kunaal-block-placeholder {
  background: linear-gradient(
    90deg,
    var(--bg-alt) 25%,
    var(--bg) 50%,
    var(--bg-alt) 75%
  );
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s infinite;
  border-radius: 8px;
  min-height: 200px;
}

@keyframes skeleton-loading {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* Specific block skeletons */
.wp-block-kunaal-heatmap[data-lazy-block]::before {
  content: '';
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  grid-template-rows: repeat(5, 1fr);
  gap: 4px;
  /* Grid of placeholder cells */
}
```

---

## 3. Code Splitting

### 3.1 Webpack Configuration

```javascript
// webpack.config.js
module.exports = {
  entry: {
    // Main bundle (always loaded)
    'main': './assets/js/main.js',
    
    // Editor bundles
    'blocks-editor': './blocks/index.js',
  },
  
  output: {
    filename: '[name].js',
    chunkFilename: 'chunks/[name].[contenthash:8].js',
    path: path.resolve(__dirname, 'dist'),
  },
  
  optimization: {
    splitChunks: {
      chunks: 'all',
      cacheGroups: {
        // Vendor chunks
        d3: {
          test: /[\\/]node_modules[\\/]d3/,
          name: 'vendor-d3',
          priority: 30,
        },
        chartjs: {
          test: /[\\/]node_modules[\\/]chart\.js/,
          name: 'vendor-chartjs',
          priority: 30,
        },
        leaflet: {
          test: /[\\/]node_modules[\\/]leaflet/,
          name: 'vendor-leaflet',
          priority: 30,
        },
        // Shared utilities
        shared: {
          test: /[\\/]assets[\\/]js[\\/]shared/,
          name: 'shared',
          minChunks: 2,
        },
      },
    },
  },
};
```

### 3.2 Block Chunk Groupings

| Chunk Name | Blocks | Approx Size |
|------------|--------|-------------|
| `chunks/basic-charts` | Bar, Line, Pie, Donut | 45KB |
| `chunks/advanced-charts` | Heatmap, Box, Violin | 35KB |
| `chunks/flow-diagrams` | Sankey, Alluvial, Network | 40KB |
| `chunks/maps` | Choropleth, Dots, Combined | 60KB |
| `chunks/vendor-d3` | D3.js core | 35KB |
| `chunks/vendor-chartjs` | Chart.js | 42KB |
| `chunks/vendor-leaflet` | Leaflet | 45KB |

### 3.3 Conditional Asset Loading (PHP)

```php
// functions.php
function kunaal_enqueue_block_assets() {
    // Always load main bundle
    wp_enqueue_script(
        'kunaal-main',
        get_template_directory_uri() . '/dist/main.js',
        [],
        KUNAAL_THEME_VERSION,
        true
    );
    
    // Only load chart/map assets if blocks are present
    if (has_kunaal_chart_blocks()) {
        wp_enqueue_script(
            'kunaal-charts-loader',
            get_template_directory_uri() . '/dist/charts-loader.js',
            ['kunaal-main'],
            KUNAAL_THEME_VERSION,
            true
        );
    }
    
    if (has_kunaal_map_blocks()) {
        wp_enqueue_script(
            'kunaal-maps-loader',
            get_template_directory_uri() . '/dist/maps-loader.js',
            ['kunaal-main'],
            KUNAAL_THEME_VERSION,
            true
        );
        
        wp_enqueue_style(
            'leaflet',
            'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            [],
            '1.9.4'
        );
    }
}
add_action('wp_enqueue_scripts', 'kunaal_enqueue_block_assets');

function has_kunaal_chart_blocks() {
    global $post;
    if (!$post) return false;
    
    $chart_blocks = [
        'kunaal/chart',
        'kunaal/heatmap',
        'kunaal/small-multiples',
        'kunaal/slopegraph',
        'kunaal/dumbbell-chart',
        'kunaal/statistical-distribution',
        'kunaal/flow-diagram',
        'kunaal/network-graph',
    ];
    
    foreach ($chart_blocks as $block) {
        if (has_block($block, $post)) {
            return true;
        }
    }
    return false;
}
```

---

## 4. Image Optimization

### 4.1 Responsive Images

```php
// Generate multiple sizes for block images
add_filter('kunaal_block_image_sizes', function($sizes) {
    return array_merge($sizes, [
        'block-thumb' => [300, 200, true],
        'block-medium' => [600, 400, true],
        'block-large' => [1200, 800, true],
    ]);
});

// Use srcset for block images
function kunaal_get_responsive_image($attachment_id, $size = 'block-medium') {
    $img_src = wp_get_attachment_image_src($attachment_id, $size);
    $img_srcset = wp_get_attachment_image_srcset($attachment_id, $size);
    $img_sizes = '(max-width: 600px) 100vw, (max-width: 1200px) 50vw, 600px';
    
    return sprintf(
        '<img src="%s" srcset="%s" sizes="%s" alt="%s" loading="lazy" decoding="async">',
        esc_url($img_src[0]),
        esc_attr($img_srcset),
        esc_attr($img_sizes),
        esc_attr(get_post_meta($attachment_id, '_wp_attachment_image_alt', true))
    );
}
```

### 4.2 Native Lazy Loading

```php
// Add loading="lazy" to all block images
add_filter('wp_get_attachment_image_attributes', function($attr, $attachment) {
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    if (!isset($attr['decoding'])) {
        $attr['decoding'] = 'async';
    }
    return $attr;
}, 10, 2);
```

### 4.3 Blur-Up Placeholders

```javascript
// Low-quality image placeholder (LQIP) approach
document.querySelectorAll('.kunaal-image[data-src]').forEach(img => {
  const placeholder = img.querySelector('.kunaal-image-placeholder');
  const actualImg = new Image();
  
  actualImg.onload = () => {
    img.appendChild(actualImg);
    actualImg.classList.add('kunaal-image-loaded');
    
    // Fade out placeholder
    setTimeout(() => {
      placeholder.classList.add('kunaal-image-placeholder--hidden');
    }, 300);
  };
  
  actualImg.src = img.dataset.src;
  actualImg.srcset = img.dataset.srcset;
  actualImg.sizes = img.dataset.sizes;
  actualImg.alt = img.dataset.alt;
  actualImg.className = 'kunaal-image-full';
});
```

```css
.kunaal-image {
  position: relative;
  overflow: hidden;
}

.kunaal-image-placeholder {
  position: absolute;
  inset: 0;
  background-size: cover;
  filter: blur(20px);
  transform: scale(1.1);
  transition: opacity 300ms ease;
}

.kunaal-image-placeholder--hidden {
  opacity: 0;
}

.kunaal-image-full {
  position: relative;
  z-index: 1;
  opacity: 0;
  transition: opacity 300ms ease;
}

.kunaal-image-full.kunaal-image-loaded {
  opacity: 1;
}
```

---

## 5. Runtime Performance

### 5.1 Animation Performance

```javascript
// Use requestAnimationFrame for smooth animations
function animateValue(element, start, end, duration) {
  let startTime = null;
  
  function step(timestamp) {
    if (!startTime) startTime = timestamp;
    const progress = Math.min((timestamp - startTime) / duration, 1);
    const value = start + (end - start) * easeOutCubic(progress);
    
    element.textContent = formatNumber(value);
    
    if (progress < 1) {
      requestAnimationFrame(step);
    }
  }
  
  requestAnimationFrame(step);
}

function easeOutCubic(t) {
  return 1 - Math.pow(1 - t, 3);
}
```

### 5.2 Scroll Performance

```javascript
// Throttled scroll handler
let ticking = false;

window.addEventListener('scroll', () => {
  if (!ticking) {
    requestAnimationFrame(() => {
      handleScroll();
      ticking = false;
    });
    ticking = true;
  }
}, { passive: true });

function handleScroll() {
  const scrollY = window.scrollY;
  
  // Update parallax elements
  document.querySelectorAll('[data-parallax-speed]').forEach(el => {
    const speed = parseFloat(el.dataset.parallaxSpeed);
    el.style.transform = `translateY(${scrollY * speed}px)`;
  });
}
```

### 5.3 Will-Change Optimization

```css
/* Apply will-change only during animation */
.chart-animating {
  will-change: transform, opacity;
}

/* Remove after animation completes */
.chart-animated {
  will-change: auto;
}
```

```javascript
// Add/remove will-change dynamically
function animateChart(chart) {
  chart.classList.add('chart-animating');
  
  // Start animation...
  
  // When complete:
  chart.addEventListener('animationend', () => {
    chart.classList.remove('chart-animating');
    chart.classList.add('chart-animated');
  }, { once: true });
}
```

---

## 6. Caching Strategy

### 6.1 Browser Caching Headers

```php
// Add cache headers for static assets
function kunaal_cache_headers() {
    if (is_singular()) {
        header('Cache-Control: public, max-age=3600'); // 1 hour for pages
    }
}
add_action('send_headers', 'kunaal_cache_headers');
```

### 6.2 Asset Versioning

```php
// Add content hash to asset URLs for cache busting
function kunaal_asset_version($url, $handle) {
    $file_path = str_replace(
        get_template_directory_uri(),
        get_template_directory(),
        $url
    );
    
    if (file_exists($file_path)) {
        return add_query_arg('v', md5_file($file_path), $url);
    }
    
    return $url;
}
add_filter('script_loader_src', 'kunaal_asset_version', 10, 2);
add_filter('style_loader_src', 'kunaal_asset_version', 10, 2);
```

### 6.3 Service Worker (Optional)

```javascript
// sw.js - Cache critical assets
const CACHE_NAME = 'kunaal-v1';
const PRECACHE_ASSETS = [
  '/wp-content/themes/kunaal-theme/dist/main.js',
  '/wp-content/themes/kunaal-theme/dist/main.css',
  '/wp-content/themes/kunaal-theme/assets/fonts/Newsreader.woff2',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(PRECACHE_ASSETS);
    })
  );
});

self.addEventListener('fetch', (event) => {
  // Cache-first for static assets
  if (event.request.url.includes('/dist/') || 
      event.request.url.includes('/assets/')) {
    event.respondWith(
      caches.match(event.request).then(response => {
        return response || fetch(event.request);
      })
    );
  }
});
```

---

## 7. Monitoring & Measurement

### 7.1 Performance Marks

```javascript
// Add performance marks for key events
performance.mark('chart-render-start');

// ... render chart ...

performance.mark('chart-render-end');
performance.measure('chart-render', 'chart-render-start', 'chart-render-end');

// Log measurements
const measures = performance.getEntriesByType('measure');
console.table(measures.map(m => ({
  name: m.name,
  duration: `${m.duration.toFixed(2)}ms`
})));
```

### 7.2 Core Web Vitals Tracking

```javascript
// Track CLS, LCP, FID
import { getCLS, getLCP, getFID } from 'web-vitals';

function sendToAnalytics({ name, value, id }) {
  // Send to your analytics service
  if (window.ga) {
    ga('send', 'event', {
      eventCategory: 'Web Vitals',
      eventAction: name,
      eventValue: Math.round(name === 'CLS' ? value * 1000 : value),
      eventLabel: id,
      nonInteraction: true,
    });
  }
}

getCLS(sendToAnalytics);
getLCP(sendToAnalytics);
getFID(sendToAnalytics);
```

---

## 8. Reduced Motion Support

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

```javascript
// Check for reduced motion preference
const prefersReducedMotion = window.matchMedia(
  '(prefers-reduced-motion: reduce)'
).matches;

function animate(element, options) {
  if (prefersReducedMotion) {
    // Skip animation, apply final state
    applyFinalState(element, options);
    return;
  }
  
  // Run full animation
  runAnimation(element, options);
}
```

---

## 9. User Stories

### US-PF-01: Fast Initial Load
**As a** reader  
**I want** pages to load quickly  
**So that** I can start reading without waiting  

**Acceptance Criteria:**
- [ ] LCP < 2.5s on 4G
- [ ] Initial JS bundle < 100KB
- [ ] No layout shift from lazy content

### US-PF-02: Lazy Load Charts
**As a** reader scrolling an article  
**I want** charts to load as I reach them  
**So that** the initial page load isn't delayed  

**Acceptance Criteria:**
- [ ] Charts show placeholder until visible
- [ ] Chart loads 200px before scrolled into view
- [ ] Loading state is visually clear
- [ ] Error state handles failures

### US-PF-03: Optimized Images
**As a** reader on mobile  
**I want** images sized appropriately for my screen  
**So that** I don't waste data or wait for oversized images  

**Acceptance Criteria:**
- [ ] Images use srcset for responsive delivery
- [ ] Native lazy loading applied
- [ ] Blur-up placeholder while loading
- [ ] No layout shift on load

### US-PF-04: Smooth Animations
**As a** reader  
**I want** animations to be smooth  
**So that** the experience feels polished  

**Acceptance Criteria:**
- [ ] Animations run at 60fps
- [ ] No jank during scroll
- [ ] Reduced motion preference respected
- [ ] Animations don't block interaction

