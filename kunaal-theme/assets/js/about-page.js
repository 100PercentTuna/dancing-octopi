/**
 * About Page - The Layered Exhibition
 * Robust implementation with proper GSAP fallback
 */
(function() {
  'use strict';

  // ========================================
  // STATE & CONFIG
  // ========================================
  var mapInstance = null;
  var countriesLayer = null;
  var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var isInitialized = false;
  var hasGSAP = false;

  // ========================================
  // UTILITY: Check if GSAP is available
  // ========================================
  function checkGSAP() {
    return typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined';
  }

  // ========================================
  // UTILITY: Set element styles (GSAP or native)
  // ========================================
  function setStyles(elements, styles) {
    if (!elements) return;
    var els = elements.length !== undefined ? Array.from(elements) : [elements];
    
    els.forEach(function(el) {
      if (!el) return;
      Object.keys(styles).forEach(function(prop) {
        if (prop === 'opacity') {
          el.style.opacity = styles[prop];
        } else if (prop === 'y') {
          el.style.transform = 'translateY(' + styles[prop] + 'px)';
        } else if (prop === 'x') {
          el.style.transform = 'translateX(' + styles[prop] + 'px)';
        } else if (prop === 'scale') {
          var currentTransform = el.style.transform || '';
          if (currentTransform.includes('translate')) {
            el.style.transform = currentTransform + ' scale(' + styles[prop] + ')';
          } else {
            el.style.transform = 'scale(' + styles[prop] + ')';
          }
        } else if (prop === 'filter') {
          el.style.filter = styles[prop];
        }
      });
    });
  }

  // ========================================
  // HERO SECTION - Works with or without GSAP
  // ========================================
  function initHero() {
    var hero = document.querySelector('.about-hero');
    if (!hero) return;

    var photos = hero.querySelectorAll('.hero-photo');
    var identity = hero.querySelector('.hero-identity');
    var annotation = hero.querySelector('.hero-annotation');
    var scrollHint = hero.querySelector('.hero-scroll-hint');

    // Handle reduced motion - show everything immediately
    if (prefersReducedMotion) {
      photos.forEach(function(photo) {
        photo.style.opacity = '1';
        photo.style.transform = 'none';
        var img = photo.querySelector('img');
        if (img) img.style.filter = 'grayscale(0%) sepia(0%)';
        photo.classList.add('is-colored');
      });
      if (identity) {
        identity.style.opacity = '1';
        identity.style.transform = 'none';
      }
      if (annotation) {
        annotation.style.opacity = '1';
      }
      hero.classList.add('is-loaded');
      return;
    }

    // Check if GSAP is available
    if (!hasGSAP) {
      // Fallback: Show everything with CSS transitions
      setTimeout(function() {
        photos.forEach(function(photo, i) {
          setTimeout(function() {
            photo.style.opacity = '1';
            photo.style.transform = 'translateY(0) scale(1)';
            var img = photo.querySelector('img');
            if (img) img.style.filter = 'grayscale(0%) sepia(0%)';
            photo.classList.add('is-colored');
          }, i * 150);
        });
        if (identity) {
          setTimeout(function() {
            identity.style.opacity = '1';
            identity.style.transform = 'translateY(0)';
          }, 400);
        }
        if (annotation) {
          setTimeout(function() {
            annotation.style.opacity = '1';
          }, 800);
        }
        hero.classList.add('is-loaded');
      }, 100);
      return;
    }

    // GSAP animation
    gsap.set(photos, { opacity: 0, y: 40, scale: 0.95 });
    
    gsap.to(photos, {
      opacity: 1,
      y: 0,
      scale: 1,
      duration: 1.2,
      stagger: { amount: 0.6, from: 'start' },
      ease: 'power3.out',
      delay: 0.2,
      onComplete: function() {
        hero.classList.add('is-loaded');
      }
    });

    if (identity) {
      gsap.set(identity, { opacity: 0, y: 30 });
      gsap.to(identity, {
        opacity: 1,
        y: 0,
        duration: 1,
        ease: 'power2.out',
        delay: 0.8
      });
    }

    if (annotation) {
      gsap.set(annotation, { opacity: 0, y: 20, rotation: -6 });
      gsap.to(annotation, {
        opacity: 1,
        y: 0,
        rotation: -4,
        duration: 0.8,
        ease: 'back.out(1.2)',
        delay: 1.4
      });
    }

    // Parallax for photos
    photos.forEach(function(photo, index) {
      var speeds = [0.08, 0.12, 0.10, 0.08];
      var speed = speeds[index] || 0.08;

      gsap.to(photo, {
        y: function() { return window.innerHeight * speed; },
        ease: 'none',
        scrollTrigger: {
          trigger: hero,
          start: 'top top',
          end: 'bottom top',
          scrub: 1.5,
          invalidateOnRefresh: true
        }
      });
    });

    // Color reveal
    photos.forEach(function(photo) {
      var img = photo.querySelector('img');
      if (!img) return;

      ScrollTrigger.create({
        trigger: hero,
        start: 'top 70%',
        onEnter: function() {
          gsap.to(img, {
            filter: 'grayscale(0%) sepia(0%)',
            duration: 1.2,
            ease: 'power2.out'
          });
          photo.classList.add('is-colored');
        },
        once: true
      });
    });

    // Scroll hint fade
    if (scrollHint) {
      ScrollTrigger.create({
        trigger: hero,
        start: 'top 50%',
        onEnter: function() {
          gsap.to(scrollHint, { opacity: 0, duration: 0.5 });
        },
        once: true
      });
    }

    // Hero pin
    ScrollTrigger.create({
      trigger: hero,
      start: 'top top',
      end: '+=80vh',
      pin: true,
      pinSpacing: true,
      anticipatePin: 1
    });
  }

  // ========================================
  // ATMOSPHERIC IMAGES
  // ========================================
  function initAtmosphericImages() {
    var atmoContainers = document.querySelectorAll('.atmo-full, .about-quote-image');
    
    if (prefersReducedMotion) {
      atmoContainers.forEach(function(container) {
        var img = container.querySelector('img');
        if (img) {
          img.style.filter = 'grayscale(0%) sepia(0%)';
          img.classList.add('is-revealed');
        }
      });
      return;
    }

    if (!hasGSAP) {
      // Fallback with IntersectionObserver
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            var img = entry.target.querySelector('img');
            if (img) {
              img.style.filter = 'grayscale(0%) sepia(0%)';
              img.classList.add('is-revealed');
            }
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });

      atmoContainers.forEach(function(container) {
        observer.observe(container);
      });
      return;
    }

    // GSAP animation
    atmoContainers.forEach(function(container) {
      var img = container.querySelector('img');
      if (!img) return;

      gsap.to(img, {
        y: function() { return window.innerHeight * 0.1; },
        ease: 'none',
        scrollTrigger: {
          trigger: container,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1.5,
          invalidateOnRefresh: true
        }
      });

      ScrollTrigger.create({
        trigger: container,
        start: 'top 75%',
        onEnter: function() {
          gsap.to(img, {
            filter: 'grayscale(0%) sepia(0%)',
            duration: 1.5,
            ease: 'power2.out'
          });
          img.classList.add('is-revealed');
        },
        once: true
      });
    });
  }

  // ========================================
  // SECTION REVEALS
  // ========================================
  function initSectionReveals() {
    var sections = [
      { selector: '.about-bio', children: ['.gallery-label', '.bio-text', '.bio-pullquote'] },
      { selector: '.about-books', children: ['.about-books-label', '.bookshelf'] },
      { selector: '.about-map', children: ['.gallery-label', '.about-map-intro', '.about-map-container', '.about-map-legend'] },
      { selector: '.about-interests', children: ['.gallery-label', '.interests-cloud'] },
      { selector: '.about-inspirations', children: ['.gallery-label', '.inspirations-grid'] },
      { selector: '.about-stats', children: ['.stats-row'] },
      { selector: '.about-connect', children: ['.about-connect-title', '.connect-icons', '.connect-link'] }
    ];

    if (prefersReducedMotion) {
      sections.forEach(function(sec) {
        var section = document.querySelector(sec.selector);
        if (!section) return;
        sec.children.forEach(function(childSel) {
          var el = section.querySelector(childSel);
          if (el) {
            el.style.opacity = '1';
            el.style.transform = 'none';
          }
        });
      });
      return;
    }

    if (!hasGSAP) {
      // Fallback with IntersectionObserver
      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

      sections.forEach(function(sec) {
        var section = document.querySelector(sec.selector);
        if (!section) return;
        sec.children.forEach(function(childSel) {
          var el = section.querySelector(childSel);
          if (el) {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
            observer.observe(el);
          }
        });
      });
      return;
    }

    // GSAP animations
    sections.forEach(function(sec) {
      var section = document.querySelector(sec.selector);
      if (!section) return;

      sec.children.forEach(function(childSel, i) {
        var el = section.querySelector(childSel);
        if (!el) return;

        gsap.set(el, { opacity: 0, y: 30 });
        gsap.to(el, {
          opacity: 1,
          y: 0,
          duration: 0.8,
          delay: i * 0.1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: section,
            start: 'top 85%',
            once: true
          }
        });
      });
    });

    // Special animations for grids
    initGridAnimations();
  }

  function initGridAnimations() {
    if (!hasGSAP) return;

    // Books
    var books = document.querySelectorAll('.book-slot');
    if (books.length) {
      gsap.set(books, { opacity: 0, y: 40, scale: 0.9 });
      gsap.to(books, {
        opacity: 1,
        y: 0,
        scale: 1,
        duration: 0.8,
        stagger: { amount: 0.4, from: 'center' },
        ease: 'back.out(1.1)',
        scrollTrigger: {
          trigger: '.about-books',
          start: 'top 80%',
          once: true
        }
      });
    }

    // Interests
    var interests = document.querySelectorAll('.interest-item');
    if (interests.length) {
      gsap.set(interests, { opacity: 0, scale: 0.8 });
      gsap.to(interests, {
        opacity: 1,
        scale: 1,
        duration: 0.6,
        stagger: { amount: 0.5, from: 'random' },
        ease: 'back.out(1.1)',
        scrollTrigger: {
          trigger: '.about-interests',
          start: 'top 80%',
          once: true
        }
      });
    }

    // Inspirations
    var cards = document.querySelectorAll('.inspiration-card');
    if (cards.length) {
      gsap.set(cards, { opacity: 0, y: 40 });
      gsap.to(cards, {
        opacity: 1,
        y: 0,
        duration: 0.8,
        stagger: 0.1,
        ease: 'power2.out',
        scrollTrigger: {
          trigger: '.about-inspirations',
          start: 'top 80%',
          once: true
        }
      });
    }

    // Stats
    var stats = document.querySelectorAll('.stat-item');
    if (stats.length) {
      gsap.set(stats, { opacity: 0, y: 30 });
      gsap.to(stats, {
        opacity: 1,
        y: 0,
        duration: 0.8,
        stagger: 0.15,
        ease: 'back.out(1.2)',
        scrollTrigger: {
          trigger: '.about-stats',
          start: 'top 80%',
          once: true
        }
      });
    }
  }

  // ========================================
  // STATS COUNTERS
  // ========================================
  function initStatsCounters() {
    var stats = document.querySelectorAll('.stat-number[data-target]');
    if (!stats.length) return;

    var observer = new IntersectionObserver(function(entries) {
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

    if (prefersReducedMotion) {
      element.textContent = target;
      return;
    }

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

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
      maxZoom: 19
    }).addTo(mapInstance);

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

    var colorLived = '#7D6B5D';
    var colorVisited = '#B8A99A';
    var colorCurrent = '#C9553D';
    var colorDefault = '#E8E8E8';

    countriesLayer = L.geoJSON(geoData, {
      style: function(feature) {
        var iso = (feature.properties.ISO_A2 || feature.properties.ISO_A3 || '').toUpperCase();
        var iso3 = (feature.properties.ISO_A3 || '').toUpperCase();
        
        var isCurrent = current && (iso === current || iso3 === current);
        var isLived = lived.indexOf(iso) !== -1 || lived.indexOf(iso3) !== -1;
        var isVisited = visited.indexOf(iso) !== -1 || visited.indexOf(iso3) !== -1;

        if (isCurrent) {
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
          
          var popupContent = '<div class="map-popup"><strong>' + name + '</strong><br>';
          popupContent += '<small style="color:#7D6B5D;">' + status + '</small>';
          if (story && story.years) {
            popupContent += '<br><span style="font-family:monospace;font-size:11px;">' + story.years + '</span>';
          }
          if (story && story.text) {
            popupContent += '<br><em style="color:#555;">' + story.text + '</em>';
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

  // ========================================
  // BOOKSHELF
  // ========================================
  function initBookshelf() {
    var bookCovers = document.querySelectorAll('.book-cover-3d[role="button"]');
    
    bookCovers.forEach(function(book) {
      book.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          this.closest('.book-slot').classList.toggle('is-active');
        }
        if (e.key === 'Escape') {
          this.closest('.book-slot').classList.remove('is-active');
        }
      });

      book.addEventListener('click', function() {
        this.closest('.book-slot').classList.toggle('is-active');
      });
    });

    document.addEventListener('click', function(e) {
      if (!e.target.closest('.book-slot')) {
        document.querySelectorAll('.book-slot.is-active').forEach(function(slot) {
          slot.classList.remove('is-active');
        });
      }
    });
  }

  // ========================================
  // IMAGE ERROR HANDLING
  // ========================================
  function initImageErrorHandling() {
    document.querySelectorAll('.atmo-full img, .about-quote-image-bg img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.parentElement.classList.add('atmo--fallback');
        this.style.display = 'none';
      });
    });

    document.querySelectorAll('.hero-photo img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.closest('.hero-photo').style.display = 'none';
      });
    });

    document.querySelectorAll('.interest-image img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.style.display = 'none';
        var placeholder = document.createElement('span');
        placeholder.className = 'interest-placeholder';
        placeholder.textContent = this.alt ? this.alt.charAt(0).toUpperCase() : '?';
        this.parentElement.appendChild(placeholder);
      });
    });

    document.querySelectorAll('.inspiration-photo img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.style.display = 'none';
        var placeholder = document.createElement('span');
        placeholder.className = 'inspiration-photo-placeholder';
        placeholder.textContent = this.alt ? this.alt.charAt(0).toUpperCase() : '?';
        this.parentElement.appendChild(placeholder);
      });
    });

    document.querySelectorAll('.book-cover-3d img').forEach(function(img) {
      img.addEventListener('error', function() {
        this.style.display = 'none';
        var title = this.alt || 'Book';
        var placeholder = document.createElement('div');
        placeholder.className = 'book-cover-placeholder';
        // Escape title to prevent XSS from alt attribute
        var span = document.createElement('span');
        span.textContent = title;
        placeholder.appendChild(span);
        this.parentElement.appendChild(placeholder);
      });
    });
  }

  // ========================================
  // INITIALIZE
  // ========================================
  function init() {
    if (isInitialized) return;
    isInitialized = true;

    // Mark that JS is ready (enables CSS animations)
    document.body.classList.add('js-ready');

    // Check if GSAP is available
    hasGSAP = checkGSAP();

    if (hasGSAP) {
      try {
        gsap.registerPlugin(ScrollTrigger);
        gsap.defaults({ ease: 'power2.out', duration: 1 });
        
        var resizeTimer;
        window.addEventListener('resize', function() {
          clearTimeout(resizeTimer);
          resizeTimer = setTimeout(function() {
            ScrollTrigger.refresh();
          }, 250);
        }, { passive: true });
      } catch (e) {
        console.error('GSAP setup error:', e);
        hasGSAP = false;
      }
    }

    // Initialize all components
    initHero();
    initAtmosphericImages();
    initSectionReveals();
    initStatsCounters();
    initMap();
    initBookshelf();
    initImageErrorHandling();

    // Refresh ScrollTrigger if available
    if (hasGSAP) {
      requestAnimationFrame(function() {
        ScrollTrigger.refresh();
      });
    }
  }

  // ========================================
  // WAIT FOR DOM AND GSAP
  // ========================================
  function waitForGSAP(callback, attempts) {
    attempts = attempts || 0;
    if (checkGSAP()) {
      callback();
    } else if (attempts < 30) {
      setTimeout(function() {
        waitForGSAP(callback, attempts + 1);
      }, 100);
    } else {
      // GSAP didn't load, init without it
      callback();
    }
  }

  function startInit() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        waitForGSAP(init);
      });
    } else {
      waitForGSAP(init);
    }
  }

  // Start
  startInit();

})();
