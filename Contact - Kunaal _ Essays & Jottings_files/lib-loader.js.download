/**
 * Centralized Library Loader
 * Prevents duplicate loading of external libraries (D3, Leaflet)
 */
(function() {
  'use strict';

  let leafletLoaded = false;
  let leafletLoading = false;
  let d3Loaded = false;
  let d3Loading = false;

  window.kunaalLibLoader = {
    /**
     * Load D3.js library
     * @returns {Promise} Promise that resolves when D3 is loaded
     */
    loadD3: function() {
      if (d3Loaded) return Promise.resolve(window.d3);
      if (d3Loading) {
        return new Promise(resolve => {
          const checkInterval = setInterval(() => {
            if (d3Loaded) {
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
          resolve(window.d3);
        };
        script.onerror = () => {
          d3Loading = false;
          reject(new Error('Failed to load D3.js'));
        };
        document.head.appendChild(script);
      });
    },

    /**
     * Load Leaflet.js library
     * @returns {Promise} Promise that resolves when Leaflet is loaded
     */
    loadLeaflet: function() {
      if (leafletLoaded) return Promise.resolve(window.L);
      if (leafletLoading) {
        return new Promise(resolve => {
          const checkInterval = setInterval(() => {
            if (leafletLoaded) {
              clearInterval(checkInterval);
              resolve(window.L);
            }
          }, 100);
        });
      }

      leafletLoading = true;
      return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.onload = () => {
          leafletLoaded = true;
          leafletLoading = false;
          resolve(window.L);
        };
        script.onerror = () => {
          leafletLoading = false;
          reject(new Error('Failed to load Leaflet.js'));
        };
        document.head.appendChild(script);
      });
    }
  };
})();

