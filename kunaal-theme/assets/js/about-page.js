/**
 * About Page - The Layered Exhibition
 * Scrollytelling, parallax, and interactive elements
 */
(function() {
  'use strict';

  // ========================================
  // STATE
  // ========================================
  var scrollY = 0;
  var ticking = false;
  var mapInstance = null;
  var countriesLayer = null;

  // ========================================
  // SCROLL TRACKING
  // ========================================
  function updateScrollY() {
    document.documentElement.style.setProperty('--scroll-y', scrollY);
  }

  function onScroll() {
    scrollY = window.scrollY || window.pageYOffset;
    if (!ticking) {
      requestAnimationFrame(function() {
        updateScrollY();
        ticking = false;
      });
      ticking = true;
    }
  }

  // ========================================
  // REVEAL ANIMATIONS
  // ========================================
  function initReveals() {
    var revealElements = document.querySelectorAll('.reveal-up, .about-image-reveal');
    if (!revealElements.length) return;

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
      revealElements.forEach(function(el) {
        el.classList.add('is-visible');
      });
      return;
    }

    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
        }
      });
    }, {
      threshold: 0.15,
      rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(function(el) {
      observer.observe(el);
    });
  }

  // ========================================
  // GRAYSCALE TO COLOR IMAGE REVEALS
  // ========================================
  function initImageReveals() {
    var images = document.querySelectorAll('.about-image');
    if (!images.length) return;

    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          setTimeout(function() {
            entry.target.classList.add('is-revealed');
          }, 100);
        }
      });
    }, {
      threshold: 0.4,
      rootMargin: '0px 0px -100px 0px'
    });

    images.forEach(function(img) {
      observer.observe(img);
    });
  }

  // ========================================
  // LEAFLET MAP
  // ========================================
  function initMap() {
    var mapContainer = document.getElementById('about-map');
    if (!mapContainer) return;

    if (typeof L === 'undefined') {
      console.warn('Leaflet not loaded');
      return;
    }

    var visitedCountries = [];
    var livedCountries = [];
    var countryNotes = {};
    var placesData = [];

    try {
      if (mapContainer.dataset.visited) {
        visitedCountries = mapContainer.dataset.visited.split(',').map(function(s) { return s.trim().toUpperCase(); });
      }
      if (mapContainer.dataset.lived) {
        livedCountries = mapContainer.dataset.lived.split(',').map(function(s) { return s.trim().toUpperCase(); });
      }
      if (mapContainer.dataset.notes) {
        countryNotes = JSON.parse(mapContainer.dataset.notes);
      }
      if (mapContainer.dataset.places) {
        placesData = JSON.parse(mapContainer.dataset.places);
      }
    } catch (e) {
      console.warn('Error parsing map data:', e);
    }

    mapInstance = L.map('about-map', {
      center: [25, 0],
      zoom: 2,
      minZoom: 1.5,
      maxZoom: 6,
      zoomControl: true,
      scrollWheelZoom: false,
      attributionControl: false
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
      maxZoom: 19
    }).addTo(mapInstance);

    // Load countries GeoJSON
    fetch('https://raw.githubusercontent.com/datasets/geo-countries/master/data/countries.geojson')
      .then(function(response) { return response.json(); })
      .then(function(data) {
        addCountryLayer(data, visitedCountries, livedCountries, countryNotes);
      })
      .catch(function(err) {
        console.warn('Could not load country boundaries:', err);
      });

    addPlaceMarkers(placesData);
  }

  function addCountryLayer(geoData, visited, lived, notes) {
    if (!mapInstance) return;

    countriesLayer = L.geoJSON(geoData, {
      style: function(feature) {
        var iso = (feature.properties.ISO_A2 || feature.properties.ISO_A3 || '').toUpperCase();
        var iso3 = (feature.properties.ISO_A3 || '').toUpperCase();
        
        var isLived = lived.indexOf(iso) !== -1 || lived.indexOf(iso3) !== -1;
        var isVisited = visited.indexOf(iso) !== -1 || visited.indexOf(iso3) !== -1;

        if (isLived) {
          return { fillColor: '#1E5AFF', fillOpacity: 0.6, color: '#1E5AFF', weight: 1 };
        } else if (isVisited) {
          return { fillColor: '#B8A99A', fillOpacity: 0.5, color: '#B8A99A', weight: 1 };
        } else {
          return { fillColor: '#E8E6E3', fillOpacity: 0.3, color: '#D4D0CC', weight: 0.5 };
        }
      },
      onEachFeature: function(feature, layer) {
        var iso = (feature.properties.ISO_A2 || feature.properties.ISO_A3 || '').toUpperCase();
        var iso3 = (feature.properties.ISO_A3 || '').toUpperCase();
        var name = feature.properties.ADMIN || feature.properties.name || iso;
        
        var isLived = lived.indexOf(iso) !== -1 || lived.indexOf(iso3) !== -1;
        var isVisited = visited.indexOf(iso) !== -1 || visited.indexOf(iso3) !== -1;

        if (isLived || isVisited) {
          var status = isLived ? 'Lived here' : 'Visited';
          var note = notes[iso] || notes[iso3] || notes[name] || '';
          
          var popupContent = '<div class="map-popup">';
          popupContent += '<strong>' + name + '</strong><br>';
          popupContent += '<small>' + status + '</small>';
          if (note) {
            popupContent += '<br><em>' + note + '</em>';
          }
          popupContent += '</div>';
          
          layer.bindPopup(popupContent);

          layer.on('mouseover', function() {
            this.setStyle({ weight: 2, fillOpacity: 0.8 });
          });

          layer.on('mouseout', function() {
            countriesLayer.resetStyle(this);
          });
        }
      }
    }).addTo(mapInstance);
  }

  function addPlaceMarkers(places) {
    if (!mapInstance || !places || !places.length) return;

    places.forEach(function(place) {
      if (!place.lat || !place.lng) return;

      var isLived = place.type === 'lived';
      var markerColor = isLived ? '#1E5AFF' : '#B8A99A';

      var marker = L.circleMarker([place.lat, place.lng], {
        radius: isLived ? 8 : 6,
        fillColor: markerColor,
        color: '#fff',
        weight: 2,
        fillOpacity: 0.9
      }).addTo(mapInstance);

      if (place.name || place.note) {
        var popupContent = '<strong>' + (place.name || '') + '</strong>';
        if (place.years) popupContent += '<br><small>' + place.years + '</small>';
        if (place.note) popupContent += '<br><em>' + place.note + '</em>';
        marker.bindPopup(popupContent);
      }
    });
  }

  // ========================================
  // BOOKSHELF
  // ========================================
  function initBookshelf() {
    var books = document.querySelectorAll('.book-slot');
    if (!books.length) return;

    books.forEach(function(book) {
      book.addEventListener('touchstart', function() {
        this.classList.add('is-touched');
      }, { passive: true });

      book.addEventListener('touchend', function() {
        var self = this;
        setTimeout(function() {
          self.classList.remove('is-touched');
        }, 300);
      }, { passive: true });
    });
  }

  // ========================================
  // INTERESTS CLOUD
  // ========================================
  function initInterestsCloud() {
    var interests = document.querySelectorAll('.interest-item');
    if (!interests.length) return;

    interests.forEach(function(item, index) {
      var delay = (Math.random() * 2).toFixed(2);
      var duration = (3 + Math.random() * 2).toFixed(2);
      item.style.animationDelay = '-' + delay + 's';
      item.style.animationDuration = duration + 's';
    });
  }

  // ========================================
  // STATS COUNTERS
  // ========================================
  function initStatsCounters() {
    var stats = document.querySelectorAll('.stat-number[data-target]');
    if (!stats.length) return;

    var animated = new Set();

    var observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting && !animated.has(entry.target)) {
          animated.add(entry.target);
          animateCounter(entry.target);
        }
      });
    }, { threshold: 0.5 });

    stats.forEach(function(stat) {
      observer.observe(stat);
    });
  }

  function animateCounter(element) {
    var target = element.dataset.target;
    var matches = target.match(/^([^0-9]*)([0-9,]+)(.*)$/);
    if (!matches) {
      element.textContent = target;
      return;
    }

    var prefix = matches[1];
    var number = parseInt(matches[2].replace(/,/g, ''), 10);
    var suffix = matches[3];
    var duration = 1500;
    var startTime = null;

    function animate(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var current = Math.floor(number * eased);
      
      element.textContent = prefix + current.toLocaleString() + suffix;

      if (progress < 1) {
        requestAnimationFrame(animate);
      } else {
        element.textContent = target;
      }
    }

    requestAnimationFrame(animate);
  }

  // ========================================
  // OPENING ANIMATION
  // ========================================
  function initOpeningAnimation() {
    var opening = document.querySelector('.about-opening');
    if (!opening) return;

    setTimeout(function() {
      opening.classList.add('is-loaded');
    }, 100);
  }

  // ========================================
  // INITIALIZE
  // ========================================
  function init() {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    initReveals();
    initImageReveals();
    initOpeningAnimation();
    initMap();
    initBookshelf();
    initInterestsCloud();
    initStatsCounters();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
