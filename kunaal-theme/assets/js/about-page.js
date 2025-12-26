/**
 * About Page - The Layered Exhibition
 * Professional scrollytelling with GSAP ScrollTrigger
 * Inspired by gentlerain.ai, dhnn.com, and other premium sites
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

  // ========================================
  // HERO SECTION - Professional Collage
  // ========================================
  function initHero() {
    var hero = document.querySelector('.about-hero');
    if (!hero) return;

    var photos = hero.querySelectorAll('.hero-photo');
    var identity = hero.querySelector('.hero-identity');
    var annotation = hero.querySelector('.hero-annotation');

    if (prefersReducedMotion) {
      // Skip animations, show everything immediately
      photos.forEach(function(photo) {
        gsap.set(photo, { opacity: 1, y: 0 });
        photo.querySelector('img').style.filter = 'grayscale(0%) sepia(0%)';
      });
      if (identity) gsap.set(identity, { opacity: 1, y: 0 });
      if (annotation) gsap.set(annotation, { opacity: 1 });
      return;
    }

    // Initial state - photos hidden and slightly offset
    gsap.set(photos, {
      opacity: 0,
      y: 40,
      scale: 0.95
    });

    // Staggered photo entrance (inspired by premium sites)
    gsap.to(photos, {
      opacity: 1,
      y: 0,
      scale: 1,
      duration: 1.2,
      stagger: {
        amount: 0.6,
        from: 'start'
      },
      ease: 'power3.out',
      delay: 0.2
    });

    // Identity entrance - use timeline for better control
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

    // Annotation entrance
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

    // Scroll hint fade out
    var scrollHint = hero.querySelector('.hero-scroll-hint');
    if (scrollHint) {
      ScrollTrigger.create({
        trigger: hero,
        start: 'top 50%',
        onEnter: function() {
          gsap.to(scrollHint, {
            opacity: 0,
            duration: 0.5,
            ease: 'power2.out'
          });
        },
        once: true
      });
    }

    // ========================================
    // HERO PARALLAX - Subtle, smooth movement
    // ========================================
    photos.forEach(function(photo, index) {
      // Much more subtle speeds - prevent extreme offsets
      var speeds = [0.08, 0.15, 0.12, 0.10]; // Reduced from 0.2-0.6
      var speed = speeds[index] || 0.1;

      gsap.to(photo, {
        y: function() {
          return window.innerHeight * speed;
        },
        ease: 'none',
        scrollTrigger: {
          trigger: hero,
          start: 'top top',
          end: 'bottom top',
          scrub: 1.5, // Smooth scrubbing
          invalidateOnRefresh: true
        }
      });
    });

    // ========================================
    // HERO COLOR REVEAL - Scroll-triggered
    // ========================================
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

    // ========================================
    // HERO PIN - Pin during initial scroll (premium scrollytelling)
    // ========================================
    ScrollTrigger.create({
      trigger: hero,
      start: 'top top',
      end: '+=100vh',
      pin: true,
      pinSpacing: true,
      anticipatePin: 1,
      onEnter: function() {
        // Ensure photos are colored by the time pinning starts
        photos.forEach(function(photo) {
          var img = photo.querySelector('img');
          if (img && !photo.classList.contains('is-colored')) {
            gsap.to(img, {
              filter: 'grayscale(0%) sepia(0%)',
              duration: 1.2,
              ease: 'power2.out'
            });
            photo.classList.add('is-colored');
          }
        });
      }
    });
  }

  // ========================================
  // ATMOSPHERIC IMAGES - Scrollytelling Integration
  // ========================================
  function initAtmosphericImages() {
    if (prefersReducedMotion) {
      document.querySelectorAll('.atmo-full img, .about-quote-image-bg img').forEach(function(img) {
        img.style.filter = 'grayscale(0%) sepia(0%)';
        img.classList.add('is-revealed');
      });
      return;
    }

    var atmoContainers = document.querySelectorAll('.atmo-full, .about-quote-image');
    
    atmoContainers.forEach(function(container) {
      var img = container.querySelector('img');
      if (!img) return;

      // Pin atmospheric images during scroll transitions (premium scrollytelling)
      var pinTrigger = ScrollTrigger.create({
        trigger: container,
        start: 'top 80%',
        end: 'bottom 20%',
        pin: false, // Don't pin, but use for timing
        pinSpacing: false
      });

      // Subtle parallax - creates depth illusion
      gsap.to(img, {
        y: function() {
          return window.innerHeight * 0.12; // Very subtle parallax
        },
        ease: 'none',
        scrollTrigger: {
          trigger: container,
          start: 'top bottom',
          end: 'bottom top',
          scrub: 1.5,
          invalidateOnRefresh: true
        }
      });

      // Color reveal on scroll - progressive reveal
      ScrollTrigger.create({
        trigger: container,
        start: 'top 75%',
        onEnter: function() {
          gsap.to(img, {
            filter: 'grayscale(0%) sepia(0%)',
            duration: 1.8,
            ease: 'power2.out'
          });
          img.classList.add('is-revealed');
        },
        once: true
      });

      // Fade in/out at edges for smooth transitions
      var fadeTrigger = ScrollTrigger.create({
        trigger: container,
        start: 'top bottom',
        end: 'bottom top',
        onUpdate: function(self) {
          var progress = self.progress;
          // Fade in as it enters, fade out as it exits
          var opacity = progress < 0.1 ? progress * 10 : (progress > 0.9 ? (1 - progress) * 10 : 1);
          gsap.set(container, { opacity: Math.max(0.3, opacity) });
        }
      });
    });
  }

  // ========================================
  // SECTION REVEALS - Professional Stagger
  // ========================================
  function initSectionReveals() {
    if (prefersReducedMotion) {
      document.querySelectorAll('.reveal-up').forEach(function(el) {
        gsap.set(el, { opacity: 1, y: 0, x: 0, scale: 1 });
        el.classList.add('is-visible');
      });
      return;
    }

    // Bio section
    var bioSection = document.querySelector('.about-bio');
    if (bioSection) {
      var bioLabel = bioSection.querySelector('.gallery-label');
      var bioText = bioSection.querySelector('.bio-text');
      var pullquote = bioSection.querySelector('.bio-pullquote');

      if (bioLabel) {
        gsap.set(bioLabel, { opacity: 0, y: 40 });
        gsap.to(bioLabel, {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: bioSection,
            start: 'top 85%',
            once: true
          }
        });
      }

      if (bioText) {
        gsap.set(bioText, { opacity: 0, y: 30 });
        gsap.to(bioText, {
          opacity: 1,
          y: 0,
          duration: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: bioText,
            start: 'top 85%',
            once: true
          }
        });
      }

      if (pullquote) {
        gsap.set(pullquote, { opacity: 0, x: -30 });
        gsap.to(pullquote, {
          opacity: 1,
          x: 0,
          duration: 1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: pullquote,
            start: 'top 85%',
            once: true
          }
        });
      }
    }

    // Bookshelf
    var booksSection = document.querySelector('.about-books');
    if (booksSection) {
      var books = booksSection.querySelectorAll('.book-slot');
      
      if (books.length) {
        gsap.set(books, { opacity: 0, y: 50, scale: 0.9 });
        gsap.to(books, {
          opacity: 1,
          y: 0,
          scale: 1,
          duration: 0.9,
          stagger: {
            amount: 0.4,
            from: 'center'
          },
          ease: 'back.out(1.2)',
          scrollTrigger: {
            trigger: booksSection,
            start: 'top 80%',
            once: true
          }
        });
      }
    }

    // Map section
    var mapSection = document.querySelector('.about-map');
    if (mapSection) {
      var mapContainer = mapSection.querySelector('.about-map-container');
      var mapLegend = mapSection.querySelector('.about-map-legend');

      if (mapContainer) {
        gsap.set(mapContainer, { opacity: 0, scale: 0.95 });
        gsap.to(mapContainer, {
          opacity: 1,
          scale: 1,
          duration: 1.2,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: mapSection,
            start: 'top 75%',
            once: true
          }
        });
      }

      if (mapLegend) {
        gsap.set(mapLegend, { opacity: 0, y: 20 });
        gsap.to(mapLegend, {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: mapLegend,
            start: 'top 90%',
            once: true
          }
        });
      }
    }

    // Interests cloud
    var interestsSection = document.querySelector('.about-interests');
    if (interestsSection) {
      var interests = interestsSection.querySelectorAll('.interest-item');
      
      if (interests.length) {
        gsap.set(interests, { opacity: 0, scale: 0.8, y: 30 });
        gsap.to(interests, {
          opacity: 1,
          scale: 1,
          y: 0,
          duration: 0.7,
          stagger: {
            amount: 0.6,
            grid: 'auto',
            from: 'random'
          },
          ease: 'back.out(1.1)',
          scrollTrigger: {
            trigger: interestsSection,
            start: 'top 80%',
            once: true
          }
        });
      }
    }

    // Inspirations grid
    var inspirationsSection = document.querySelector('.about-inspirations');
    if (inspirationsSection) {
      var cards = inspirationsSection.querySelectorAll('.inspiration-card');
      
      if (cards.length) {
        gsap.set(cards, { opacity: 0, y: 40 });
        gsap.to(cards, {
          opacity: 1,
          y: 0,
          duration: 0.9,
          stagger: {
            amount: 0.5,
            grid: 'auto',
            from: 'start'
          },
          ease: 'power2.out',
          scrollTrigger: {
            trigger: inspirationsSection,
            start: 'top 80%',
            once: true
          }
        });
      }
    }

    // Stats section
    var statsSection = document.querySelector('.about-stats');
    if (statsSection) {
      var stats = statsSection.querySelectorAll('.stat-item');
      
      if (stats.length) {
        gsap.set(stats, { opacity: 0, scale: 0.9, y: 30 });
        gsap.to(stats, {
          opacity: 1,
          scale: 1,
          y: 0,
          duration: 1,
          stagger: 0.15,
          ease: 'back.out(1.2)',
          scrollTrigger: {
            trigger: statsSection,
            start: 'top 80%',
            once: true
          }
        });
      }
    }

    // Connect section
    var connectSection = document.querySelector('.about-connect');
    if (connectSection) {
      var connectTitle = connectSection.querySelector('.about-connect-title');
      var connectIcons = connectSection.querySelectorAll('.connect-icon');
      var connectLink = connectSection.querySelector('.connect-link');

      if (connectTitle) {
        gsap.set(connectTitle, { opacity: 0, y: 30 });
        gsap.to(connectTitle, {
          opacity: 1,
          y: 0,
          duration: 0.9,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: connectSection,
            start: 'top 85%',
            once: true
          }
        });
      }

      if (connectIcons.length) {
        gsap.set(connectIcons, { opacity: 0, scale: 0.8 });
        gsap.to(connectIcons, {
          opacity: 1,
          scale: 1,
          duration: 0.7,
          stagger: 0.1,
          ease: 'back.out(1.2)',
          scrollTrigger: {
            trigger: connectSection,
            start: 'top 85%',
            once: true
          }
        });
      }

      if (connectLink) {
        gsap.set(connectLink, { opacity: 0, x: -20 });
        gsap.to(connectLink, {
          opacity: 1,
          x: 0,
          duration: 0.8,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: connectLink,
            start: 'top 90%',
            once: true
          }
        });
      }
    }
  }

  // ========================================
  // STATS COUNTERS - GSAP Animation
  // ========================================
  function initStatsCounters() {
    var stats = document.querySelectorAll('.stat-number[data-target]');
    if (!stats.length) return;

    stats.forEach(function(stat) {
      var target = stat.dataset.target;
      
      // Handle non-numeric values
      var matches = target.match(/^([^0-9]*)([0-9,]+)(.*)$/);
      if (!matches) {
        stat.textContent = target;
        return;
      }

      var prefix = matches[1];
      var number = parseInt(matches[2].replace(/,/g, ''), 10);
      var suffix = matches[3];

      if (prefersReducedMotion) {
        stat.textContent = target;
        return;
      }

      ScrollTrigger.create({
        trigger: stat,
        start: 'top 85%',
        onEnter: function() {
          gsap.fromTo(stat, 
            { textContent: prefix + '0' + suffix },
            {
              textContent: prefix + number.toLocaleString() + suffix,
              duration: 2,
              ease: 'power2.out',
              snap: { textContent: 1 },
              onUpdate: function() {
                var current = parseInt(this.targets()[0].textContent.replace(/[^0-9]/g, ''));
                if (!isNaN(current)) {
                  this.targets()[0].textContent = prefix + current.toLocaleString() + suffix;
                }
              }
            }
          );
        },
        once: true
      });
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
    
    // Keyboard navigation
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

    // Grayscale tile layer
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
      maxZoom: 19
    }).addTo(mapInstance);

    // Load countries GeoJSON
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
    
    var currentCountryBounds = null;

    countriesLayer = L.geoJSON(geoData, {
      style: function(feature) {
        var iso = (feature.properties.ISO_A2 || feature.properties.ISO_A3 || '').toUpperCase();
        var iso3 = (feature.properties.ISO_A3 || '').toUpperCase();
        
        var isCurrent = current && (iso === current || iso3 === current);
        var isLived = lived.indexOf(iso) !== -1 || lived.indexOf(iso3) !== -1;
        var isVisited = visited.indexOf(iso) !== -1 || visited.indexOf(iso3) !== -1;

        if (isCurrent) {
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
    
    if (current && currentCountryBounds) {
      positionCurrentMarker(currentCountryBounds);
    }
  }
  
  function positionCurrentMarker(countryFeature) {
    var marker = document.querySelector('.map-current-marker');
    var mapContainer = document.querySelector('.about-map-container');
    if (!marker || !mapContainer || !mapInstance) return;
    
    try {
      var bounds = L.geoJSON(countryFeature).getBounds();
      var center = bounds.getCenter();
      var point = mapInstance.latLngToContainerPoint(center);
      
      marker.style.left = point.x + 'px';
      marker.style.top = point.y + 'px';
      marker.classList.add('is-positioned');
      
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
  // BOOKSHELF - Enhanced 3D Effects
  // ========================================
  function initBookshelf() {
    var bookCovers = document.querySelectorAll('.book-cover-3d[role="button"]');
    if (!bookCovers.length) return;

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

    document.addEventListener('click', function(e) {
      if (!e.target.closest('.book-slot')) {
        document.querySelectorAll('.book-slot.is-active').forEach(function(slot) {
          slot.classList.remove('is-active');
        });
      }
    });
  }

  // ========================================
  // KEYBOARD ACCESSIBILITY
  // ========================================
  function initKeyboardNav() {
    var cards = document.querySelectorAll('.inspiration-card, .book-cover-3d');
    
    cards.forEach(function(card) {
      if (card.tagName !== 'A' && card.tagName !== 'BUTTON') {
        card.setAttribute('tabindex', '-1');
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
        placeholder.innerHTML = '<span>' + title + '</span>';
        this.parentElement.appendChild(placeholder);
      });
    });
  }

  // ========================================
  // FALLBACK FOR NO GSAP
  // ========================================
  function initFallback() {
    // Basic reveal animations if GSAP not available
    var revealElements = document.querySelectorAll('.reveal-up');
    if (revealElements.length) {
      // Respect reduced motion
      if (prefersReducedMotion) {
        revealElements.forEach(function(el) {
          el.style.opacity = '1';
          el.style.transform = 'none';
          el.classList.add('is-visible');
        });
        return;
      }

      var observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'none';
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
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

    // Initialize other features (non-GSAP dependent)
    initMap();
    initBookshelf();
    initKeyboardNav();
    initImageErrorHandling();
  }

  // ========================================
  // WAIT FOR GSAP TO LOAD
  // ========================================
  function waitForGSAP(callback, maxAttempts) {
    maxAttempts = maxAttempts || 20; // Try for up to 2 seconds
    
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
      // GSAP is ready, proceed
      callback();
    } else if (maxAttempts > 0) {
      // Wait 100ms and try again
      setTimeout(function() {
        waitForGSAP(callback, maxAttempts - 1);
      }, 100);
    } else {
      // GSAP didn't load, use fallback
      console.warn('GSAP or ScrollTrigger not available after waiting. Using fallback animations.');
      initFallback();
    }
  }

  // ========================================
  // INITIALIZE WITH GSAP
  // ========================================
  function initWithGSAP() {
    if (isInitialized) return;
    isInitialized = true;

    // Double-check GSAP is available
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
      console.warn('GSAP check failed during init. Using fallback.');
      initFallback();
      return;
    }

    // ========================================
    // GSAP SETUP
    // ========================================
    try {
      gsap.registerPlugin(ScrollTrigger);
    } catch (e) {
      console.error('Error registering ScrollTrigger:', e);
      initFallback();
      return;
    }

    // Set default easing for smoother animations
    gsap.defaults({
      ease: 'power2.out',
      duration: 1
    });

    // Refresh ScrollTrigger on resize
    var resizeTimer;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        ScrollTrigger.refresh();
      }, 250);
    }, { passive: true });

    // Initialize all features
    try {
      initHero();
      initAtmosphericImages();
      initSectionReveals();
      initStatsCounters();
      initMap();
      initBookshelf();
      initKeyboardNav();
      initImageErrorHandling();

      // Refresh ScrollTrigger after everything is set up
      // Use requestAnimationFrame to ensure DOM is ready
      requestAnimationFrame(function() {
        ScrollTrigger.refresh();
      });
    } catch (e) {
      console.error('Error during initialization:', e);
      // Still try to show content even if animations fail
      document.querySelectorAll('.reveal-up').forEach(function(el) {
        el.style.opacity = '1';
        el.style.transform = 'none';
      });
    }
  }

  // ========================================
  // START INITIALIZATION
  // ========================================
  function startInit() {
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function() {
        waitForGSAP(initWithGSAP);
      });
    } else {
      // DOM is ready, wait for GSAP
      waitForGSAP(initWithGSAP);
    }
  }

  // Start the initialization process
  startInit();

})();
