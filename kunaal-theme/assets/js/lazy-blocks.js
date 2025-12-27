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

  // Registry pattern: blocks register their loaders
  const blockLoaders = {};

  function registerBlockLoader(blockName, loaderFn) {
    blockLoaders[blockName] = loaderFn;
  }

  async function loadBlock(element) {
    const blockType = element.dataset.lazyBlock;
    if (!blockType) return;

    if (element.classList.contains('is-loaded')) return;
    element.classList.add('is-loading');

    try {
      const loader = blockLoaders[blockType];
      if (loader && typeof loader === 'function') {
        await loader(element);
        element.classList.remove('is-loading');
        element.classList.add('is-loaded');
      } else {
        console.warn(`No loader registered for block type: ${blockType}`);
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

  // Expose for manual loading and registration
  window.kunaalLazyLoad = {
    register: registerBlockLoader,
    load: loadBlock,
    init: initLazyLoading
  };

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLazyLoading);
  } else {
    initLazyLoading();
  }

})();

