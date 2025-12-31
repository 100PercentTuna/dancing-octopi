/**
 * Centralized Library Loader
 * Prevents duplicate loading of external libraries (D3, Leaflet)
 * Handles CSS loading for libraries that require it (Leaflet)
 * 
 * Uses memoized Promises - concurrent callers share the same Promise.
 * No polling loops; libraries load exactly once.
 * 
 * Configuration: Set window.kunaalLibConfig before this script loads to override URLs:
 * {
 *   d3Src: '/path/to/d3.v7.min.js',
 *   leafletJs: '/path/to/leaflet.js',
 *   leafletCss: '/path/to/leaflet.css'
 * }
 */
(function() {
  'use strict';

  // Configuration with CDN fallbacks
  const config = window.kunaalLibConfig || {};
  const D3_SRC = config.d3Src || 'https://d3js.org/d3.v7.min.js';
  const LEAFLET_JS = config.leafletJs || 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
  const LEAFLET_CSS = config.leafletCss || 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';

  // Memoized promises - concurrent callers share the same promise
  let d3Promise = null;
  let leafletPromise = null;
  let leafletCssPromise = null;

  /**
   * Load a script and return a Promise
   * @param {string} src - Script URL
   * @param {string} globalName - Expected global variable name
   * @returns {Promise} Resolves with the global object
   */
  function loadScript(src, globalName) {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = src;
      script.onload = () => {
        if (window[globalName]) {
          resolve(window[globalName]);
        } else {
          reject(new Error(globalName + ' loaded but window.' + globalName + ' is not available'));
        }
      };
      script.onerror = () => reject(new Error('Failed to load script: ' + src));
      document.head.appendChild(script);
    });
  }

  /**
   * Check if a stylesheet link already exists
   * @param {string} href - Stylesheet URL (or partial match)
   * @returns {boolean}
   */
  function stylesheetExists(href) {
    const links = document.querySelectorAll('link[rel="stylesheet"]');
    for (let i = 0; i < links.length; i++) {
      if (links[i].href === href || links[i].href.endsWith(href)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Load a CSS file and return a Promise
   * @param {string} href - Stylesheet URL
   * @returns {Promise}
   */
  function loadCSS(href) {
    if (stylesheetExists(href)) {
      return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = href;
      link.onload = resolve;
      link.onerror = () => reject(new Error('Failed to load CSS: ' + href));
      document.head.appendChild(link);
    });
  }

  window.kunaalLibLoader = {
    /**
     * Load D3.js library
     * @returns {Promise<object>} Promise that resolves with window.d3
     */
    loadD3: function() {
      // If D3 already exists globally, return resolved promise
      if (window.d3) {
        return Promise.resolve(window.d3);
      }
      
      // Return memoized promise - concurrent callers share the same one
      if (!d3Promise) {
        d3Promise = loadScript(D3_SRC, 'd3');
      }
      
      return d3Promise;
    },

    /**
     * Load Leaflet.js library and CSS
     * @returns {Promise<object>} Promise that resolves with window.L
     */
    loadLeaflet: function() {
      // If Leaflet already exists globally, ensure CSS is loaded
      if (window.L) {
        if (!leafletCssPromise) {
          leafletCssPromise = loadCSS(LEAFLET_CSS).catch(() => {
            // Resolve even if CSS fails - library is still usable
          });
        }
        return leafletCssPromise.then(() => window.L);
      }
      
      // Return memoized promise - concurrent callers share the same one
      if (!leafletPromise) {
        // Load CSS first, then JS
        leafletCssPromise = loadCSS(LEAFLET_CSS);
        leafletPromise = leafletCssPromise
          .catch(() => {
            // Continue even if CSS fails
          })
          .then(() => loadScript(LEAFLET_JS, 'L'));
      }
      
      return leafletPromise;
    }
  };
})();
