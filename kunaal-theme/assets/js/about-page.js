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
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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
        updateHeroPhotos();
        updateParallax();
        ticking = false;
      });
      ticking = true;
    }
  }

  // ========================================
  // HERO PHOTO COLLAGE
  // ========================================
  function initHeroCollage() {
    var photos = document.querySelectorAll('.hero-photo');
    if (!photos.length) return;

    // Skip animations if reduced motion
    if (prefersReducedMotion) {
      photos.forEach(function(photo) {
        photo.classList.add('is-colored');
      });
      return;
    }

    // Initial state
    updateHeroPhotos();
  }

  function updateHeroPhotos() {
    var photos = document.querySelectorAll('.hero-photo');
    if (!photos.length || prefersReducedMotion) return;

    var colorThreshold = window.innerHeight * 0.3; // 30vh

    photos.forEach(function(photo, index) {
      // Grayscale to color transition
      if (scrollY > colorThreshold) {
        photo.classList.add('is-colored');
      } else {
        photo.classList.remove('is-colored');
      }

      // Parallax effect per photo
      var speed = parseFloat(photo.dataset.parallaxSpeed) || 0.3;
      var offset = scrollY * speed;
      photo.style.transform = 'translateY(' + offset + 'px)';
    });
  }

  // ========================================
  // PARALLAX FOR OTHER ELEMENTS
  // ========================================
  function updateParallax() {
    if (prefersReducedMotion) return;

    var slowElements = document.querySelectorAll('.parallax-slow');
    var medElements = document.querySelectorAll('.parallax-medium');

    slowElements.forEach(function(el) {
      var rect = el.getBoundingClientRect();
      var centerY = rect.top + rect.height / 2;
      var viewportCenterY = window.innerHeight / 2;
      var offset = (centerY - viewportCenterY) * 0.2;
      el.style.transform = 'translateY(' + offset + 'px)';
    });

    medElements.forEach(function(el) {
      var rect = el.getBoundingClientRect();
      var centerY = rect.top + rect.height / 2;
      var viewportCenterY = window.innerHeight / 2;
      var offset = (centerY - viewportCenterY) * 0.4;
      el.style.transform = 'translateY(' + offset + 'px)';
    });
  }

  // ========================================
  // REVEAL ANIMATIONS
  // ========================================
  function initReveals() {
    var revealElements = document.querySelectorAll('.reveal-up');
    if (!revealElements.length) return;

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

    if (prefersReducedMotion) {
      images.forEach(function(img) {
        img.classList.add('is-revealed');
      });
      return;
    }

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
  // LEAFLET MAP WITH COUNTRY SHADING
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
    var currentCountry = '';
    var countryStories = {};

    try {
      if (mapContainer.dataset.visited) {
        visitedCountries = mapContainer.dataset.visited.split(',').map(function(s) { 
          return s.trim().toUpperCase(); 
        });
      }
      if (mapContainer.dataset.lived) {
        livedCountries = mapContainer.dataset.lived.split(',').map(function(s) { 
          return s.trim().toUpperCase(); 
        });
      }
      if (mapContainer.dataset.current) {
        currentCountry = mapContainer.dataset.current.trim().toUpperCase();
      }
      if (mapContainer.dataset.stories) {
        countryStories = JSON.parse(mapContainer.dataset.stories) || {};
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
      attributionControl: false,
      keyboard: true,
      keyboardPanDelta: 80
    });
    
    // Keyboard navigation for map
    var mapEl = document.getElementById('about-map');
    if (mapEl) {
      mapEl.addEventListener('keydown', function(e) {
        if (e.key === '+' || e.key === '=') {
          mapInstance.zoomIn();
        } else if (e.key === '-') {
          mapInstance.zoomOut();
        }
      });
    }

    // Use a grayscale tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
      maxZoom: 19
    }).addTo(mapInstance);

    // Load countries GeoJSON for shading
    fetch('https://raw.githubusercontent.com/datasets/geo-countries/master/data/countries.geojson')
      .then(function(response) { return response.json(); })
      .then(function(data) {
        addCountryLayer(data, visitedCountries, livedCountries, currentCountry, countryStories);
      })
      .catch(function(err) {
        console.warn('Could not load country boundaries:', err);
      });
  }

  function addCountryLayer(geoData, visited, lived, current, stories) {
    if (!mapInstance) return;

    // Color palette per spec - browns for lived/visited, terracotta for current
    var colorLived = '#7D6B5D';     // --warm
    var colorVisited = '#B8A99A';   // --warmLight
    var colorCurrent = '#C9553D';   // --map-current (terracotta)
    var colorDefault = '#E8E8E8';   // --map-default
    
    var currentCountryBounds = null;

    countriesLayer = L.geoJSON(geoData, {
      style: function(feature) {
        var iso = (feature.properties.ISO_A2 || feature.properties.ISO_A3 || '').toUpperCase();
        var iso3 = (feature.properties.ISO_A3 || '').toUpperCase();
        
        var isCurrent = current && (iso === current || iso3 === current);
        var isLived = lived.indexOf(iso) !== -1 || lived.indexOf(iso3) !== -1;
        var isVisited = visited.indexOf(iso) !== -1 || visited.indexOf(iso3) !== -1;

        if (isCurrent) {
          // Store bounds for marker positioning
          currentCountryBounds = feature;
          return { fillColor: colorCurrent, fillOpacity: 0.7, color: colorCurrent, weight: 1.5 };
        } else if (isLived) {
          return { fillColor: colorLived, fillOpacity: 0.6, color: colorLived, weight: 1 };
        } else if (isVisited) {
          return { fillColor: colorVisited, fillOpacity: 0.5, color: colorVisited, weight: 1 };
        } else {
          return { fillColor: colorDefault, fillOpacity: 0.3, color: '#D4D0CC', weight: 0.5 };
        }
      },
      onEachFeature: function(feature, layer) {
        var iso = (feature.properties.ISO_A2 || feature.properties.ISO_A3 || '').toUpperCase();
        var iso3 = (feature.properties.ISO_A3 || '').toUpperCase();
        var name = feature.properties.ADMIN || feature.properties.name || iso;
        
        var isCurrent = current && (iso === current || iso3 === current);
        var isLived = lived.indexOf(iso) !== -1 || lived.indexOf(iso3) !== -1;
        var isVisited = visited.indexOf(iso) !== -1 || visited.indexOf(iso3) !== -1;

        if (isCurrent || isLived || isVisited) {
          var status = isCurrent ? 'Currently here' : (isLived ? 'Lived here' : 'Visited');
          var story = stories[iso] || stories[iso3] || stories[name] || null;
          
          var popupContent = '<div class="map-popup">';
          popupContent += '<strong>' + name + '</strong><br>';
          popupContent += '<small style="color:#7D6B5D;">' + status + '</small>';
          if (story) {
            if (story.years) {
              popupContent += '<br><span style="font-family:monospace;font-size:11px;color:#666;">' + story.years + '</span>';
            }
            if (story.text) {
              popupContent += '<br><em style="font-style:italic;color:#555;">' + story.text + '</em>';
            }
          }
          popupContent += '</div>';
          
          layer.bindPopup(popupContent, {
            className: 'about-map-popup'
          });

          layer.on('mouseover', function() {
            this.setStyle({ weight: 2, fillOpacity: 0.8 });
          });

          layer.on('mouseout', function() {
            countriesLayer.resetStyle(this);
          });
        }
      }
    }).addTo(mapInstance);
    
    // Position the current location marker if we found the country
    if (current && currentCountryBounds) {
      positionCurrentMarker(currentCountryBounds);
    }
  }
  
  // Position the pulsing marker on the current country
  function positionCurrentMarker(countryFeature) {
    var marker = document.querySelector('.map-current-marker');
    var mapContainer = document.querySelector('.about-map-container');
    if (!marker || !mapContainer || !mapInstance) return;
    
    try {
      // Get the center of the country
      var bounds = L.geoJSON(countryFeature).getBounds();
      var center = bounds.getCenter();
      
      // Convert to pixel position
      var point = mapInstance.latLngToContainerPoint(center);
      
      // Position the marker
      marker.style.left = point.x + 'px';
      marker.style.top = point.y + 'px';
      marker.classList.add('is-positioned');
      
      // Update marker position on map move/zoom
      mapInstance.on('move zoom', function() {
        var newPoint = mapInstance.latLngToContainerPoint(center);
        marker.style.left = newPoint.x + 'px';
        marker.style.top = newPoint.y + 'px';
      });
    } catch (e) {
      console.warn('Could not position current marker:', e);
    }
  }

  // ========================================
  // BOOKSHELF
  // ========================================
  function initBookshelf() {
    var bookCovers = document.querySelectorAll('.book-cover-3d[role="button"]');
    if (!bookCovers.length) return;

    // Keyboard accessibility for non-link books
    bookCovers.forEach(function(book) {
      book.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          var slot = this.closest('.book-slot');
          slot.classList.toggle('is-active');
        }
        if (e.key === 'Escape') {
          var slot = this.closest('.book-slot');
          slot.classList.remove('is-active');
        }
      });

      book.addEventListener('click', function() {
        var slot = this.closest('.book-slot');
        slot.classList.toggle('is-active');
      });
    });

    // Touch support for mobile (all books)
    var allBooks = document.querySelectorAll('.book-slot');
    allBooks.forEach(function(book) {
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

    // Close book tooltips when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.book-slot')) {
        document.querySelectorAll('.book-slot.is-active').forEach(function(slot) {
          slot.classList.remove('is-active');
        });
      }
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
    
    // Handle non-numeric values (like "∞" or "500+")
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

    if (prefersReducedMotion) {
      element.textContent = target;
      return;
    }

    function animate(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3); // Cubic ease-out
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
  // HERO LOADED STATE
  // ========================================
  function initHeroLoadState() {
    var hero = document.querySelector('.about-hero');
    if (!hero) return;

    // Mark as loaded after brief delay for smooth entrance
    setTimeout(function() {
      hero.classList.add('is-loaded');
    }, 100);
  }

  // ========================================
  // KEYBOARD ACCESSIBILITY
  // ========================================
  function initKeyboardNav() {
    // Ensure all interactive elements are reachable
    var cards = document.querySelectorAll('.inspiration-card, .book-cover-3d');
    
    cards.forEach(function(card) {
      if (card.tagName !== 'A' && card.tagName !== 'BUTTON') {
        // Non-link cards should not be focusable
        card.setAttribute('tabindex', '-1');
      }
    });
  }

  // ========================================
  // IMAGE ERROR HANDLING
  // ========================================
  function initImageErrorHandling() {
    // Atmospheric images
    document.querySelectorAll('.atmo-full img, .about-quote-image-bg img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.parentElement.classList.add('atmo--fallback');
        this.style.display = 'none';
      });
    });

    // Interest images
    document.querySelectorAll('.interest-image img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.style.display = 'none';
        var placeholder = document.createElement('span');
        placeholder.className = 'interest-placeholder';
        placeholder.textContent = this.alt ? this.alt.charAt(0).toUpperCase() : '?';
        this.parentElement.appendChild(placeholder);
      });
    });

    // Inspiration photos
    document.querySelectorAll('.inspiration-photo img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.style.display = 'none';
        var placeholder = document.createElement('span');
        placeholder.className = 'inspiration-photo-placeholder';
        placeholder.textContent = this.alt ? this.alt.charAt(0).toUpperCase() : '?';
        this.parentElement.appendChild(placeholder);
      });
    });

    // Book covers
    document.querySelectorAll('.book-cover-3d img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.style.display = 'none';
        var title = this.alt || 'Book';
        var placeholder = document.createElement('div');
        placeholder.className = 'book-cover-placeholder';
        placeholder.innerHTML = '<span>' + title + '</span>';
        this.parentElement.appendChild(placeholder);
      });
    });
  }

  // ========================================
  // INITIALIZE
  // ========================================
  function init() {
    // Scroll tracking
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Initialize components
    initHeroCollage();
    initHeroLoadState();
    initReveals();
    initImageReveals();
    initMap();
    initBookshelf();
    initStatsCounters();
    initKeyboardNav();
    initImageErrorHandling();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
