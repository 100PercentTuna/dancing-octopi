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
      // 
      // NOTE: Currently, all blocks resolve to empty init functions because:
      // 1. Block-specific JavaScript is loaded via view.js files (enqueued in blocks.php)
      // 2. view.js files run automatically when enqueued and handle their own initialization
      // 3. This lazy-loading system primarily handles the IntersectionObserver logic
      //    and loading states, while actual block functionality is handled by view.js
      //
      // Future enhancement: If code-splitting is needed, actual dynamic imports
      // can be implemented here to load block-specific chunks on demand.
      const moduleMap = {
        'heatmap': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/heatmap/view.js
        'dumbbell-chart': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/dumbbell-chart/view.js
        'slopegraph': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/slopegraph/view.js
        'small-multiples': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/small-multiples/view.js
        'statistical-distribution': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/statistical-distribution/view.js
        'flow-diagram': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/flow-diagram/view.js
        'network-graph': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/network-graph/view.js
        'data-map': () => Promise.resolve({ init: () => {} }), // Initialized by blocks/data-map/view.js
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

