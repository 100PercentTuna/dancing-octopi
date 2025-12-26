/**
 * Kunaal Theme - Lazy Loading System for Heavy Blocks
 * Loads chart/map blocks only when scrolled into viewport
 */
(function() {
  'use strict';

  // IntersectionObserver for lazy loading
  let observer = null;

  function initLazyLoading() {
    // Check if IntersectionObserver is supported
    if (!('IntersectionObserver' in window)) {
      // Fallback: load all blocks immediately
      document.querySelectorAll('[data-lazy-block]').forEach(block => {
        loadBlock(block);
      });
      return;
    }

    // Create observer with 200px root margin (load before visible)
    observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          loadBlock(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, {
      rootMargin: '200px',
      threshold: 0.01
    });

    // Observe all lazy blocks
    document.querySelectorAll('[data-lazy-block]').forEach(block => {
      observer.observe(block);
    });
  }

  async function loadBlock(element) {
    const blockType = element.dataset.lazyBlock;
    
    if (!blockType) return;

    // Show loading state
    element.classList.add('is-loading');

    try {
      // Dynamic import based on block type
      // Note: In production, these will be code-split chunks
      const moduleMap = {
        'heatmap': () => Promise.resolve({ init: () => {} }), // Already initialized by view.js
        'dumbbell-chart': () => Promise.resolve({ init: () => {} }),
        'slopegraph': () => Promise.resolve({ init: () => {} }),
        'small-multiples': () => Promise.resolve({ init: () => {} }),
        'statistical-distribution': () => Promise.resolve({ init: () => {} }),
        'flow-diagram': () => Promise.resolve({ init: () => {} }),
        'network-graph': () => Promise.resolve({ init: () => {} }),
        'data-map': () => Promise.resolve({ init: () => {} }),
      };

      const loader = moduleMap[blockType];
      
      if (loader) {
        const module = await loader();
        
        // Initialize the block
        if (module && typeof module.init === 'function') {
          module.init(element);
        }
        
        element.classList.remove('is-loading');
        element.classList.add('is-loaded');
      } else {
        console.warn(`No loader found for block type: ${blockType}`);
        element.classList.add('is-error');
        element.classList.remove('is-loading');
      }
    } catch (error) {
      console.error(`Failed to load ${blockType}:`, error);
      element.classList.add('is-error');
      element.classList.remove('is-loading');
      
      // Show error message
      const errorMsg = document.createElement('div');
      errorMsg.className = 'lazy-block-error';
      errorMsg.textContent = 'Failed to load chart. Please refresh the page.';
      element.appendChild(errorMsg);
    }
  }

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLazyLoading);
  } else {
    initLazyLoading();
  }

  // Expose for manual loading if needed
  window.kunaalLazyLoad = {
    loadBlock: loadBlock,
    init: initLazyLoading
  };

})();

