/**
 * Kunaal Theme - About Page
 * Scrollytelling and parallax effects
 * 
 * Dependencies: Scrollama.js
 */
(function() {
  'use strict';

  // Only run on About page
  if (!document.querySelector('.about-page-premium')) return;

  // ========================================
  // PARALLAX SYSTEM
  // ========================================
  const parallaxElements = document.querySelectorAll('[data-parallax]');
  const speeds = { slow: 0.3, medium: 0.5, fast: 0.8 };
  
  function updateParallax() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    
    const scrollY = window.scrollY;
    
    parallaxElements.forEach(function(el) {
      const speed = speeds[el.dataset.parallax] || 0.5;
      const rect = el.getBoundingClientRect();
      const centerY = rect.top + rect.height / 2;
      const viewportCenter = window.innerHeight / 2;
      const offset = (centerY - viewportCenter) * speed;
      
      el.style.transform = 'translateY(' + offset + 'px)';
    });
  }

  // Throttled scroll handler
  let ticking = false;
  function onScroll() {
    if (!ticking) {
      requestAnimationFrame(function() {
        updateParallax();
        ticking = false;
      });
      ticking = true;
    }
  }

  // ========================================
  // SCROLLAMA INITIALIZATION
  // ========================================
  let scroller = null;
  
  function initScrollama() {
    if (typeof scrollama === 'undefined') {
      console.warn('Scrollama not loaded');
      return;
    }

    scroller = scrollama();
    
    scroller
      .setup({
        step: '.about-step',
        offset: 0.5,
        progress: true
      })
      .onStepEnter(handleStepEnter)
      .onStepProgress(handleStepProgress)
      .onStepExit(handleStepExit);
  }

  function handleStepEnter(response) {
    const el = response.element;
    el.classList.add('is-active');
    
    // Trigger reveal animations
    const reveals = el.querySelectorAll('.reveal');
    reveals.forEach(function(reveal, i) {
      setTimeout(function() {
        reveal.classList.add('is-visible');
      }, i * 150);
    });
  }

  function handleStepProgress(response) {
    const el = response.element;
    const progress = response.progress;
    
    // Hero section fade out
    if (el.classList.contains('about-hero')) {
      if (progress > 0.8) {
        const fadeProgress = (progress - 0.8) / 0.2;
        el.style.opacity = 1 - (fadeProgress * 0.3);
        const scale = 1 - (fadeProgress * 0.05);
        el.querySelector('.hero-content').style.transform = 'scale(' + scale + ')';
      }
    }
  }

  function handleStepExit(response) {
    // Keep active state for visited sections
  }

  // ========================================
  // REVEAL ANIMATIONS (Intersection Observer)
  // ========================================
  function initRevealAnimations() {
    const reveals = document.querySelectorAll('.reveal');
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    reveals.forEach(function(el) {
      observer.observe(el);
    });
  }

  // ========================================
  // HERO SCROLL INDICATOR
  // ========================================
  function initScrollIndicator() {
    const indicator = document.querySelector('.scroll-indicator');
    if (!indicator) return;
    
    // Hide after 3 seconds or first scroll
    let hidden = false;
    
    function hideIndicator() {
      if (hidden) return;
      hidden = true;
      indicator.style.opacity = '0';
      setTimeout(function() {
        indicator.style.display = 'none';
      }, 500);
    }
    
    setTimeout(hideIndicator, 3000);
    
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) hideIndicator();
    }, { once: true });
  }

  // ========================================
  // STATS COUNTER ANIMATION
  // ========================================
  function initStatsCounters() {
    const stats = document.querySelectorAll('.stat-number[data-value]');
    if (!stats.length) return;
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });
    
    stats.forEach(function(stat) {
      observer.observe(stat);
    });
  }

  function animateCounter(el) {
    const value = el.dataset.value;
    const match = value.match(/^(\d+)(.*)$/);
    if (!match) {
      el.textContent = value;
      return;
    }
    
    const target = parseInt(match[1], 10);
    const suffix = match[2] || '';
    const duration = 2000;
    const start = performance.now();
    
    function update(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3); // ease-out
      const current = Math.floor(target * eased);
      
      el.textContent = current + suffix;
      
      if (progress < 1) {
        requestAnimationFrame(update);
      }
    }
    
    requestAnimationFrame(update);
  }

  // ========================================
  // LEAFLET MAP INITIALIZATION
  // ========================================
  function initMap() {
    const mapContainer = document.getElementById('places-map');
    if (!mapContainer || typeof L === 'undefined') return;
    
    const placesData = mapContainer.dataset.places;
    let places = [];
    try {
      places = JSON.parse(placesData);
    } catch (e) {
      console.warn('Invalid places data');
      return;
    }
    
    if (!places.length) return;
    
    // Initialize map
    const map = L.map('places-map', {
      zoomControl: false,
      scrollWheelZoom: false,
      dragging: !L.Browser.mobile
    });
    
    // CartoDB Positron tiles (elegant grayscale)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; OpenStreetMap, &copy; CARTO',
      maxZoom: 19
    }).addTo(map);
    
    // Custom marker styles
    const livedIcon = L.divIcon({
      className: 'map-marker lived',
      iconSize: [12, 12],
      iconAnchor: [6, 6]
    });
    
    const visitedIcon = L.divIcon({
      className: 'map-marker visited',
      iconSize: [10, 10],
      iconAnchor: [5, 5]
    });
    
    // Add markers
    const markers = [];
    const sidebar = document.getElementById('map-sidebar');
    
    places.forEach(function(place) {
      const icon = place.type === 'lived' ? livedIcon : visitedIcon;
      const marker = L.marker([place.lat, place.lng], { icon: icon }).addTo(map);
      markers.push(marker);
      
      marker.on('click', function() {
        showPlaceDetails(place, sidebar);
      });
    });
    
    // Fit bounds to show all markers
    if (markers.length) {
      const group = L.featureGroup(markers);
      map.fitBounds(group.getBounds().pad(0.2));
    }
  }

  function showPlaceDetails(place, sidebar) {
    if (!sidebar) return;
    
    sidebar.innerHTML = 
      '<button class="sidebar-close" aria-label="Close">&times;</button>' +
      '<h3>' + escapeHtml(place.name) + '</h3>' +
      (place.years ? '<p class="place-years">' + escapeHtml(place.years) + '</p>' : '') +
      '<span class="place-type">' + (place.type === 'lived' ? 'Lived here' : 'Visited') + '</span>' +
      (place.note ? '<p class="place-note">' + escapeHtml(place.note) + '</p>' : '');
    
    sidebar.classList.add('is-open');
    
    sidebar.querySelector('.sidebar-close').addEventListener('click', function() {
      sidebar.classList.remove('is-open');
    });
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ========================================
  // BOOKSHELF SCROLL ANIMATION
  // ========================================
  function initBookshelf() {
    const bookshelf = document.querySelector('.bookshelf');
    if (!bookshelf) return;
    
    const books = bookshelf.querySelectorAll('.book');
    const shelf = bookshelf.querySelector('.shelf-line');
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          // Animate books from right to left
          books.forEach(function(book, i) {
            setTimeout(function() {
              book.classList.add('is-visible');
            }, i * 150);
          });
          
          // Animate shelf line
          if (shelf) {
            setTimeout(function() {
              shelf.classList.add('is-visible');
            }, books.length * 150);
          }
          
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });
    
    observer.observe(bookshelf);
  }

  // ========================================
  // INTERESTS FLOATING ANIMATION
  // ========================================
  function initInterests() {
    const tags = document.querySelectorAll('.interest-tag');
    
    tags.forEach(function(tag) {
      // Random rotation
      const rotation = (Math.random() - 0.5) * 4; // -2 to +2 degrees
      tag.style.setProperty('--rotation', rotation + 'deg');
      
      // Random float duration
      const duration = 4 + Math.random() * 4; // 4-8s
      tag.style.setProperty('--float-duration', duration + 's');
      
      // Random delay
      const delay = Math.random() * 2;
      tag.style.setProperty('--float-delay', delay + 's');
    });
  }

  // ========================================
  // INITIALIZE
  // ========================================
  function init() {
    window.addEventListener('scroll', onScroll, { passive: true });
    
    initScrollama();
    initRevealAnimations();
    initScrollIndicator();
    initStatsCounters();
    initMap();
    initBookshelf();
    initInterests();
    
    // Initial parallax
    updateParallax();
  }

  // Run when DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();

