/**
 * Data Map - Frontend JavaScript
 * Loads Leaflet.js dynamically and renders maps
 */
(function() {
  'use strict';

  // Use centralized library loader
  async function loadLeaflet() {
    if (window.kunaalLibLoader && window.kunaalLibLoader.loadLeaflet) {
      return window.kunaalLibLoader.loadLeaflet();
    }
    // Fallback if loader not available
    if (window.L) {
      return Promise.resolve(window.L);
    }
    return Promise.reject(new Error('Leaflet loader not available'));
  }

  async function initDataMap(block) {
    const mapType = block.dataset.mapType || 'choropleth';
    const baseMap = block.dataset.baseMap || 'world';
    const centerLat = parseFloat(block.dataset.centerLat) || 25;
    const centerLng = parseFloat(block.dataset.centerLng) || 0;
    const initialZoom = parseInt(block.dataset.initialZoom) || 2;
    const enableZoom = block.dataset.enableZoom === 'true';
    const enablePan = block.dataset.enablePan === 'true';
    const regionData = JSON.parse(block.dataset.regionData || '[]');
    const pointData = JSON.parse(block.dataset.pointData || '[]');
    const colorScale = block.dataset.colorScale || 'sequential';
    const colorLow = block.dataset.colorLow || '#F5F0EB';
    const colorHigh = block.dataset.colorHigh || '#7D6B5D';
    const colorMid = block.dataset.colorMid || '#F5F0EB';
    const colorNegative = block.dataset.colorNegative || '#C9553D';
    const dotSizeMin = parseInt(block.dataset.dotSizeMin) || 4;
    const dotSizeMax = parseInt(block.dataset.dotSizeMax) || 40;
    const dotOpacity = parseFloat(block.dataset.dotOpacity) || 0.7;
    const dotBorderColor = block.dataset.dotBorderColor || '#FFFFFF';
    const valueFormat = block.dataset.valueFormat || 'number';
    const currencySymbol = block.dataset.currencySymbol || '$';
    const valueSuffix = block.dataset.valueSuffix || '';

    const mapContainer = block.querySelector('.data-map-visual');
    if (!mapContainer) return;

    try {
      const L = await loadLeaflet();
      
      // Determine tile layer based on theme
      const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
      const tileUrl = isDark
        ? 'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png'
        : 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png';

      const map = L.map(mapContainer, {
        center: [centerLat, centerLng],
        zoom: initialZoom,
        zoomControl: enableZoom,
        scrollWheelZoom: enableZoom,
        dragging: enablePan,
        touchZoom: enableZoom,
        doubleClickZoom: enableZoom,
        boxZoom: enableZoom,
        keyboard: true
      });

      L.tileLayer(tileUrl, {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
      }).addTo(map);

      // Remove loading message
      const loading = mapContainer.querySelector('.map-loading');
      if (loading) loading.remove();

      // Add region data (choropleth)
      if ((mapType === 'choropleth' || mapType === 'combined') && regionData.length > 0) {
        // Note: Full choropleth requires GeoJSON data
        // This is a simplified version - full implementation would load country GeoJSON
        regionData.forEach(region => {
          // Placeholder: In full implementation, would load GeoJSON and style by value
          // Removed console.log for production
        });
      }

      // Add point data (dots)
      if ((mapType === 'dots' || mapType === 'gradient-dots' || mapType === 'combined') && pointData.length > 0) {
        const allValues = pointData.map(p => parseFloat(p.value) || 0);
        const minVal = Math.min(...allValues);
        const maxVal = Math.max(...allValues);
        const range = maxVal - minVal || 1;

        pointData.forEach(point => {
          const value = parseFloat(point.value) || 0;
          const normalized = (value - minVal) / range;
          
          let radius, color;
          if (mapType === 'gradient-dots') {
            radius = dotSizeMin + (dotSizeMax - dotSizeMin) * 0.5; // Fixed size for gradient
            // Interpolate color
            color = interpolateColor(colorLow, colorHigh, normalized);
          } else {
            radius = dotSizeMin + (dotSizeMax - dotSizeMin) * Math.sqrt(normalized);
            color = colorHigh;
          }

          const circle = L.circleMarker([point.lat, point.lng], {
            radius: radius,
            fillColor: color,
            color: dotBorderColor,
            weight: 2,
            opacity: 1,
            fillOpacity: dotOpacity
          }).addTo(map);

          const label = point.label || `${point.lat}, ${point.lng}`;
          const formattedValue = formatValue(value, valueFormat, currencySymbol, valueSuffix);
          
          circle.bindPopup(`<strong>${label}</strong><br>${formattedValue}`);
        });
      }

      // Store map instance for theme changes
      mapContainer._mapInstance = map;

      // Listen for theme changes
      window.addEventListener('themechange', (e) => {
        const newTileUrl = e.detail.theme === 'dark'
          ? 'https://{s}.basemaps.cartocdn.com/dark_nolabels/{z}/{x}/{y}{r}.png'
          : 'https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png';
        
        map.eachLayer(layer => {
          if (layer instanceof L.TileLayer) {
            layer.setUrl(newTileUrl);
          }
        });
      });

    } catch (error) {
      console.error('Failed to render map:', error);
      const loading = mapContainer.querySelector('.map-loading');
      if (loading) {
        loading.textContent = 'Error loading map';
      }
    }
  }

  function interpolateColor(color1, color2, t) {
    const c1 = hexToRgb(color1);
    const c2 = hexToRgb(color2);
    const r = Math.round(c1.r + (c2.r - c1.r) * t);
    const g = Math.round(c1.g + (c2.g - c1.g) * t);
    const b = Math.round(c1.b + (c2.b - c1.b) * t);
    return `rgb(${r}, ${g}, ${b})`;
  }

  function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
    } : { r: 0, g: 0, b: 0 };
  }

  function formatValue(value, format, currency, suffix) {
    switch (format) {
      case 'percent': return round(value, 1) + '%';
      case 'currency': return currency + number_format(value) + (suffix ? ' ' + suffix : '');
      case 'compact':
        if (value >= 1000000) return currency + round(value / 1000000, 1) + 'M' + (suffix ? ' ' + suffix : '');
        if (value >= 1000) return currency + round(value / 1000, 1) + 'K' + (suffix ? ' ' + suffix : '');
        return currency + round(value) + (suffix ? ' ' + suffix : '');
      case 'decimal1': return number_format(value, 1) + (suffix ? ' ' + suffix : '');
      default: return round(value) + (suffix ? ' ' + suffix : '');
    }
  }

  function number_format(num, decimals = 0) {
    return num.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
  }

  function round(num, decimals = 0) {
    return Number(num.toFixed(decimals));
  }

  // Initialize all maps
  function initAllDataMaps() {
    document.querySelectorAll('.wp-block-kunaal-data-map[data-lazy-block="data-map"]').forEach(block => {
      if (block.classList.contains('is-loaded')) return;
      block.classList.add('is-loading');
      initDataMap(block).then(() => {
        block.classList.remove('is-loading');
        block.classList.add('is-loaded');
      });
    });
  }

  // Register loader with lazy loading system
  if (window.kunaalLazyLoad && window.kunaalLazyLoad.register) {
    window.kunaalLazyLoad.register('data-map', async function(element) {
      await initDataMap(element);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllDataMaps);
  } else {
    initAllDataMaps();
  }
})();

