/**
 * Centralized Library Loader
 * Prevents duplicate loading of external libraries (D3, Leaflet)
 * Handles CSS loading for libraries that require it (Leaflet)
 */
(function() {
  'use strict';

  let leafletLoaded = false;
  let leafletLoading = false;
  let leafletCssLoaded = false;
  let d3Loaded = false;
  let d3Loading = false;

  /**
   * Check if a stylesheet link already exists
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
   * Load a CSS file
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
     * @returns {Promise} Promise that resolves when D3 is loaded
     */
    loadD3: function() {
      // If D3 already exists globally, resolve immediately and mark as loaded
      if (window.d3) {
        d3Loaded = true;
        return Promise.resolve(window.d3);
      }
      
      // If already marked as loaded, resolve with existing instance
      if (d3Loaded) {
        return Promise.resolve(window.d3);
      }
      
      // Wait if currently loading (shared Promise per library)
      if (d3Loading) {
        return new Promise(resolve => {
          const checkInterval = setInterval(() => {
            if (d3Loaded && window.d3) {
              clearInterval(checkInterval);
              resolve(window.d3);
            }
          }, 100);
        });
      }

      d3Loading = true;
      return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://d3js.org/d3.v7.min.js';
        script.onload = () => {
          d3Loaded = true;
          d3Loading = false;
          if (window.d3) {
            resolve(window.d3);
          } else {
            reject(new Error('D3.js loaded but window.d3 is not available'));
          }
        };
        script.onerror = () => {
          d3Loading = false;
          reject(new Error('Failed to load D3.js'));
        };
        document.head.appendChild(script);
      });
    },

    /**
     * Load Leaflet.js library and CSS
     * @returns {Promise} Promise that resolves when Leaflet is loaded
     */
    loadLeaflet: function() {
      // If Leaflet already exists globally, resolve immediately and mark as loaded
      if (window.L) {
        leafletLoaded = true;
        // Ensure CSS is loaded if library exists but CSS wasn't tracked
        if (!leafletCssLoaded) {
          return loadCSS('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css')
            .then(() => {
              leafletCssLoaded = true;
              return Promise.resolve(window.L);
            })
            .catch(() => Promise.resolve(window.L)); // Resolve even if CSS fails
        }
        return Promise.resolve(window.L);
      }
      
      // If already marked as loaded, resolve with existing instance
      if (leafletLoaded) {
        return Promise.resolve(window.L);
      }
      
      // Wait if currently loading (shared Promise per library)
      if (leafletLoading) {
        return new Promise(resolve => {
          const checkInterval = setInterval(() => {
            if (leafletLoaded && window.L) {
              clearInterval(checkInterval);
              resolve(window.L);
            }
          }, 100);
        });
      }

      leafletLoading = true;
      
      // Load CSS first, then JS (ensure CSS loads exactly once)
      return loadCSS('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css')
        .then(() => {
          leafletCssLoaded = true;
          return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => {
              leafletLoaded = true;
              leafletLoading = false;
              if (window.L) {
                resolve(window.L);
              } else {
                reject(new Error('Leaflet.js loaded but window.L is not available'));
              }
            };
            script.onerror = () => {
              leafletLoading = false;
              reject(new Error('Failed to load Leaflet.js'));
            };
            document.head.appendChild(script);
          });
        })
        .catch((error) => {
          leafletLoading = false;
          throw error;
        });
    }
  };
})();

