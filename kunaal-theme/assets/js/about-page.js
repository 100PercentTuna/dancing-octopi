/**
 * About Page JavaScript
 * 
 * Handles animations, scroll effects, and interactive features for the About page.
 * Progressive enhancement: all content is visible without JS; animations enhance UX.
 *
 * @package Kunaal_Theme
 * @since 4.21.0
 */

(function () {
  'use strict';

  // Check for reduced motion preference
  let reduceMotion = false;
  try {
    reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  } catch (e) {
    reduceMotion = false;
  }

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  function hasGSAP() {
    return !!(window.gsap && window.ScrollTrigger);
  }

  function init() {
    const gsapOk = hasGSAP();
    
    // Mark elements as GSAP-ready only if GSAP is available
    if (gsapOk) {
      document.body.classList.add('gsap-ready');
      try { 
        window.gsap.registerPlugin(window.ScrollTrigger); 
      } catch (e) {
        // Fail silently - GSAP is progressive enhancement
      }
    }

    // Initialize all modules
    initPageLoad(gsapOk);
    initScrollReveals(gsapOk);
    initPanoramaParallax(gsapOk);
    initPinnedScenes(gsapOk);
    initMarqueeWords(gsapOk);
    initNumbers(gsapOk);
    initWorldMap();
    initProgressBar();
    initHeaderNav();
    initHeroMosaicCycle();
    initCapsuleLife();
    initFooterYear();
    
    // Verify accent photo overflow after init (defensive check)
    setTimeout(function() {
      const accentPhoto = document.querySelector('.hero-photo.has-accent');
      if (accentPhoto) {
        const computedOverflow = window.getComputedStyle(accentPhoto).overflow;
        if (computedOverflow !== 'visible') {
          accentPhoto.style.overflow = 'visible';
        }
      }
    }, 100);
  }

  // =============================================
  // PAGE LOAD - quiet editorial entrance
  // =============================================
  function initPageLoad(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    
    try {
      const scrollIndicator = document.getElementById('scrollIndicator');
      const accentPhoto = document.querySelector('.hero-photo.has-accent');
      const heroTextReveals = document.querySelectorAll('.hero-text [data-reveal]');
      const nonAccentPhotos = document.querySelectorAll('.hero-photo:not(.has-accent)');
      
      // CRITICAL: Force accent photo to opacity:1 BEFORE any animation
      // The dog-ear (::before) inherits opacity from parent - must NEVER be animated
      if (accentPhoto) {
        accentPhoto.style.setProperty('opacity', '1', 'important');
      }
      
      const tl = window.gsap.timeline({ 
        defaults: { ease: 'power2.out' },
          onComplete: function() {
          // Final cleanup: ensure all animated elements are fully visible
          finalizeAnimatedElements(nonAccentPhotos, heroTextReveals, accentPhoto, scrollIndicator);
        }
      });
      
      // Nav entrance
      tl.from('.nav', { y: -10, opacity: 0, duration: 0.55 });
      
      // Hero photos - animate ONLY non-accent photos (NodeList, not CSS selector)
      if (nonAccentPhotos.length > 0) {
        tl.from(nonAccentPhotos, { 
          opacity: 0, 
          duration: 0.6, 
          stagger: 0.06
        }, '<0.05');
      }
      
      // Hero text elements
      if (heroTextReveals.length > 0) {
        tl.from(heroTextReveals, { 
          y: 16, 
          opacity: 0, 
          duration: 0.55, 
          stagger: 0.08
        }, '<0.15');
      }
      
      // Scroll indicator
      if (scrollIndicator) {
        tl.from(scrollIndicator, { 
          opacity: 0,
          y: 8,
          duration: 0.35
        }, '<0.25');
        
        // Scroll fade handler - clean passive listener
        initScrollIndicatorFade(scrollIndicator);
      }
      
    } catch (e) {
      // Fallback: ensure visibility even if animation fails
      forceAllHeroElementsVisible();
    }
  }
  
  /**
   * Finalize animated elements after timeline completes
   * Sets explicit inline styles to guarantee visibility, then clears GSAP state
   */
  function finalizeAnimatedElements(photos, textEls, accentPhoto, scrollIndicator) {
    // Non-accent photos: set visible, then clear GSAP state
    photos.forEach(function(photo) {
      photo.style.opacity = '1';
      window.gsap.set(photo, { clearProps: 'opacity' });
    });
    
    // Hero text: set visible, clear all GSAP state
    textEls.forEach(function(el) {
      el.style.opacity = '1';
      el.style.transform = 'none';
      window.gsap.set(el, { clearProps: 'all' });
    });
    
    // Accent photo: NEVER clear - keep explicit opacity:1
    if (accentPhoto) {
      accentPhoto.style.setProperty('opacity', '1', 'important');
    }
    
    // Scroll indicator
        if (scrollIndicator) {
      scrollIndicator.style.opacity = '1';
      window.gsap.set(scrollIndicator, { clearProps: 'all' });
    }
  }
  
  /**
   * Initialize scroll indicator fade behavior
   */
  function initScrollIndicatorFade(scrollIndicator) {
    let ticking = false;
    
    function updateFade() {
      ticking = false;
      const scrollY = window.scrollY || window.pageYOffset;
      const fadeStart = 100;
      const fadeEnd = 300;
      
      if (scrollY <= fadeStart) {
        scrollIndicator.style.opacity = '1';
      } else if (scrollY >= fadeEnd) {
        scrollIndicator.style.opacity = '0';
      } else {
        const progress = (scrollY - fadeStart) / (fadeEnd - fadeStart);
        scrollIndicator.style.opacity = (1 - progress).toFixed(3);
      }
    }
    
    function onScroll() {
      if (!ticking) {
        ticking = true;
        requestAnimationFrame(updateFade);
      }
    }
    
    window.addEventListener('scroll', onScroll, { passive: true });
    updateFade(); // Initial state
  }
  
  /**
   * Fallback: force all hero elements visible if animation fails
   */
  function forceAllHeroElementsVisible() {
    document.querySelectorAll('.hero-photo').forEach(function(p) {
      p.style.opacity = '1';
    });
    document.querySelectorAll('.hero-text [data-reveal]').forEach(function(el) {
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
  }

  // =============================================
  // Scroll-triggered reveals (left/right/up)
  // =============================================
  function initScrollReveals(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    
    const isMobile = window.innerWidth < 900;
    
    // On mobile, skip ScrollTrigger - CSS handles visibility
    if (isMobile) {
      ensureHeroTextVisible();
      return;
    }
    
    const els = document.querySelectorAll('[data-reveal]');
    if (!els.length) return;
    
    // Categorize elements for different animation behaviors
    const heroTextEls = [];
    const otherEls = [];
    
    els.forEach(function(el) {
      // Skip scroll indicator entirely - it's handled in initPageLoad
        if (el.id === 'scrollIndicator' || el.closest('#scrollIndicator') || el.classList.contains('hero-scroll')) {
        return;
      }
      
      if (el.closest('.hero-text')) {
        heroTextEls.push(el);
      } else {
        otherEls.push(el);
      }
    });
    
    // Hero text elements: NEVER reverse, always end at full visibility
    // This prevents "stuck at partial opacity" bug
    heroTextEls.forEach(function(el) {
      setupRevealAnimation(el, {
        toggleActions: 'play none none none', // Never reverse
        onEnter: function() { forceElementVisible(el); },
        onEnterBack: function() { forceElementVisible(el); }
      });
    });
    
    // Other elements: can reverse for scroll-based interactivity
    otherEls.forEach(function(el) {
      setupRevealAnimation(el, {
        toggleActions: 'play none none reverse'
      });
    });
    
    // Global resize handler
    initScrollRevealResizeHandler(heroTextEls);
  }
  
  /**
   * Setup a reveal animation for a single element
   */
  function setupRevealAnimation(el, options) {
    const dir = el.getAttribute('data-reveal') || 'up';
    let x = 0, y = 14;
    
    if (dir === 'left') { x = -18; y = 0; }
    else if (dir === 'right') { x = 18; y = 0; }
    else if (dir === 'down') { x = 0; y = -14; }
    
    try {
      window.gsap.from(el, {
            x: x,
            y: y,
            opacity: 0,
            duration: 0.55,
            ease: 'power2.out',
            scrollTrigger: {
              trigger: el,
          start: 'top 86%',
          toggleActions: options.toggleActions || 'play none none reverse',
          invalidateOnRefresh: true,
          onEnter: options.onEnter || null,
          onEnterBack: options.onEnterBack || null
        }
      });
    } catch (e) {
      // Fallback: make element visible
                el.style.opacity = '1';
                el.style.transform = 'none';
    }
  }
  
  /**
   * Force an element to be visible (used as callback and fallback)
   */
  function forceElementVisible(el) {
                el.style.opacity = '1';
                el.style.transform = 'none';
    if (window.gsap) {
                window.gsap.set(el, { opacity: 1, x: 0, y: 0, clearProps: 'all' });
              }
            }
  
  /**
   * Ensure hero text elements are visible (mobile fallback)
   */
  function ensureHeroTextVisible() {
    if (window.gsap) {
      window.gsap.set('.hero-label, .hero-title, .hero-intro, .hero-meta', {
        opacity: 1, 
        y: 0, 
        clearProps: 'all'
      });
    }
    // Also set inline styles as backup
    document.querySelectorAll('.hero-label, .hero-title, .hero-intro, .hero-meta').forEach(function(el) {
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
  }
  
  /**
   * Handle resize for scroll reveals - ensure visibility on viewport changes
   */
  function initScrollRevealResizeHandler(heroTextEls) {
    let resizeTimeout;
    
    function handleResize() {
      if (!window.gsap || !window.ScrollTrigger) return;
      
      const isMobile = window.innerWidth < 900;
      const isWide = window.innerWidth > 1600;
      
      // On mobile or wide viewport, ensure hero text is visible
      if (isMobile || isWide) {
        ensureHeroTextVisible();
      }
      
      // Refresh ScrollTrigger calculations
      window.ScrollTrigger.refresh();
      
      // Check hero text elements are visible
      setTimeout(function() {
        heroTextEls.forEach(function(el) {
          const rect = el.getBoundingClientRect();
          const isInView = rect.top < window.innerHeight && rect.bottom > 0;
          
          if (isInView) {
            const computed = window.getComputedStyle(el);
            const opacity = parseFloat(computed.opacity);
            
            if (isNaN(opacity) || opacity < 0.9) {
              forceElementVisible(el);
            }
          }
        });
      }, 100);
    }
    
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(handleResize, 150);
    }, { passive: true });
  }

  // =============================================
  // Panorama parallax effect
  // =============================================
  // Performance optimizations:
  // - Disabled on mobile (touch devices)
  // - Uses IntersectionObserver to only activate when near viewport
  // - Uses GSAP scrub for smooth, RAF-synced animation
  //
  // CSS coordination:
  // - Image is 280% height, positioned at top: -90%
  // - Parallax amplitude: ±30% ensures image never exposes empty edges
  // - Math: at -90% + 30% = -60%, bottom is at 280% - 60% = 220% (covers 100%)
  //         at -90% - 30% = -120%, bottom is at 280% - 120% = 160% (covers 100%)
  // =============================================
  function initPanoramaParallax(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    
    // Disable on mobile for performance
    const isMobile = window.innerWidth < 900 || window.matchMedia('(hover: none)').matches;
    if (isMobile) return;
    
    const bands = document.querySelectorAll('.panorama');
    if (!bands.length) return;
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (!entry.isIntersecting) return;
        
        const band = entry.target;
        const img = band.querySelector('.panorama-img');
        if (!img || band.dataset.parallaxInitialized === 'true') return;
        
        band.dataset.parallaxInitialized = 'true';
        
        // Speed multiplier from data attribute (default 1)
        let speed = parseFloat(band.getAttribute('data-speed') || '1');
        if (!isFinite(speed) || speed <= 0) speed = 1;
        
        // Amplitude: 30% base, scaled by speed
        // This keeps image within bounds (see CSS math above)
        const amp = 30 * Math.min(speed, 1.5); // Cap speed to prevent edge exposure
        
        try {
          window.gsap.fromTo(img,
            { yPercent: -amp },
            {
              yPercent: amp,
              ease: 'none',
              force3D: true,
              scrollTrigger: {
                trigger: band,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
              }
            }
          );
        } catch (e) {
          // Fail silently - parallax is progressive enhancement
        }
      });
    }, {
      root: null,
      rootMargin: '50%',
      threshold: 0
    });
    
    bands.forEach(function(band) {
      observer.observe(band);
    });
  }

  // =============================================
  // Pinned scenes (sticky scroll effect)
  // =============================================
  function initPinnedScenes(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    
    const pins = document.querySelectorAll('[data-pin="true"]');
    pins.forEach(function(pin) {
      try {
        window.ScrollTrigger.create({
          trigger: pin,
          start: 'top top',
          end: '+=140%',
          pin: true,
          pinSpacing: true
        });
      } catch (e) {
        // Fail silently - pinning is progressive enhancement
        }
    });
  }

  // =============================================
  // Marquee background words (horizontal drift)
  // =============================================
  function initMarqueeWords(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    
    const words = document.querySelectorAll('[data-marquee]');
    words.forEach(function(el) {
        const dir = (el.getAttribute('data-dir') || 'left').toLowerCase();
        const dist = (dir === 'right') ? 120 : -120;
        const container = el.parentElement || el;
      
        try {
          window.gsap.fromTo(el,
            { x: -dist },
            {
              x: dist,
              ease: 'none',
              scrollTrigger: {
                trigger: container,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true
              }
            }
          );
        } catch (e) {
        // Fail silently - marquee is progressive enhancement
          }
    });
  }

  // =============================================
  // Numbers: quick "slot machine" feel, then settle
  // =============================================
  function initNumbers(gsapOk) {
    const section = document.querySelector('.numbers');
    if (!section) return;

    const numberEls = section.querySelectorAll('.number-value[data-target]');
    if (!numberEls.length) return;

    const infinityEl = section.querySelector('.infinity-value');
    let fired = false;

    function run() {
      if (fired) return;
      fired = true;

      for (var i = 0; i < numberEls.length; i++) {
        (function (el, idx) {
          const target = parseInt(el.getAttribute('data-target') || '0', 10);
          const suffix = el.getAttribute('data-suffix') || '';
          let spins = 0;
          const maxSpins = 12 + Math.floor(Math.random() * 6);

          setTimeout(function () {
            const spinInterval = setInterval(function () {
              el.textContent = Math.floor(Math.random() * target * 1.3).toLocaleString() + suffix;
              spins++;
              if (spins >= maxSpins) {
                clearInterval(spinInterval);

                const start = 0;
                const duration = 500;
                const t0 = performance.now();

                function tick(now) {
                  const p = Math.min(1, (now - t0) / duration);
                  const eased = 1 - Math.pow(1 - p, 3);
                  const val = Math.floor(start + (target - start) * eased);
                  el.textContent = val.toLocaleString() + suffix;
                  if (p < 1) {
                    requestAnimationFrame(tick);
                  }
                }
                requestAnimationFrame(tick);
              }
            }, 40);
          }, idx * 180);
        })(numberEls[i], i);
      }

      if (infinityEl) {
        setTimeout(function () {
          if (hasGSAP() && !reduceMotion) {
            try {
              window.gsap.to(infinityEl, { opacity: 1, scale: 1, duration: 0.7, ease: 'back.out(2)' });
            } catch (e) {
              infinityEl.style.opacity = '1';
              infinityEl.style.transform = 'scale(1)';
            }
          } else {
            infinityEl.style.opacity = '1';
            infinityEl.style.transform = 'scale(1)';
          }
        }, numberEls.length * 180 + 800);
      }
    }

    if (hasGSAP() && !reduceMotion) {
      try {
        window.ScrollTrigger.create({
          trigger: section,
          start: 'top 80%',
          once: true,
          onEnter: run
        });
      } catch (e) {
        // Fallback to IntersectionObserver
        if ('IntersectionObserver' in window) {
          const io = new IntersectionObserver(function (entries) {
            for (var i = 0; i < entries.length; i++) {
              if (entries[i].isIntersecting) {
                run();
                io.disconnect();
                break;
              }
            }
          }, { threshold: 0.25 });
          io.observe(section);
        } else {
          run();
        }
      }
    } else if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver(function (entries) {
        for (var i = 0; i < entries.length; i++) {
          if (entries[i].isIntersecting) {
            run();
            io.disconnect();
            break;
          }
        }
      }, { threshold: 0.25 });
      io.observe(section);
    } else {
      run();
    }
  }

  // =============================================
  // World map (D3) - uses WordPress localized data
  // =============================================
  function initWorldMap() {
    const host = document.getElementById('world-map');
    if (!host) return;

    // D3 and TopoJSON should already be loaded via WordPress enqueue
    if (!window.d3 || !window.topojson) return;

    function draw() {
      // Get places data from WordPress localization
      const placesData = (window.kunaalAbout && window.kunaalAbout.places) || {
        current: [],
        lived: [],
        visited: []
      };

      // Ensure all are arrays
      const current = Array.isArray(placesData.current) ? placesData.current : (placesData.current ? [placesData.current] : []);
      const lived = Array.isArray(placesData.lived) ? placesData.lived : (placesData.lived ? [placesData.lived] : []);
      const visited = Array.isArray(placesData.visited) ? placesData.visited : (placesData.visited ? [placesData.visited] : []);
      
      try {

      // Country name mapping (expandable)
      const countryNames = {
        'USA': 'United States', 'IND': 'India', 'SGP': 'Singapore', 'PHL': 'Philippines',
        'THA': 'Thailand', 'ZAF': 'South Africa', 'GBR': 'United Kingdom', 'CHE': 'Switzerland',
        'CAN': 'Canada', 'MYS': 'Malaysia', 'MDV': 'Maldives', 'BRA': 'Brazil', 'MEX': 'Mexico',
        'CHN': 'China', 'JPN': 'Japan', 'KOR': 'South Korea', 'AUS': 'Australia', 'NZL': 'New Zealand',
        'FRA': 'France', 'DEU': 'Germany', 'ITA': 'Italy', 'ESP': 'Spain', 'NLD': 'Netherlands',
        'BEL': 'Belgium', 'AUT': 'Austria', 'SWE': 'Sweden', 'NOR': 'Norway', 'DNK': 'Denmark',
        'FIN': 'Finland', 'POL': 'Poland', 'CZE': 'Czech Republic', 'HUN': 'Hungary', 'GRC': 'Greece',
        'TUR': 'Turkey', 'RUS': 'Russia', 'ISR': 'Israel', 'ARE': 'United Arab Emirates', 'SAU': 'Saudi Arabia',
        'EGY': 'Egypt', 'KEN': 'Kenya', 'TZA': 'Tanzania', 'ZWE': 'Zimbabwe', 'BWA': 'Botswana',
        'ARG': 'Argentina', 'CHL': 'Chile', 'PER': 'Peru', 'COL': 'Colombia', 'VEN': 'Venezuela',
        'ECU': 'Ecuador', 'URY': 'Uruguay', 'PRY': 'Paraguay', 'BOL': 'Bolivia', 'CRI': 'Costa Rica',
        'PAN': 'Panama', 'GTM': 'Guatemala', 'HND': 'Honduras', 'NIC': 'Nicaragua', 'SLV': 'El Salvador'
      };

      // ISO numeric (ISO 3166-1 numeric) -> ISO3 mapping
      // Complete mapping for world-atlas TopoJSON countries-110m.json
      const idToIso = {
        // Major countries
        4: 'AFG', 8: 'ALB', 12: 'DZA', 16: 'ASM', 20: 'AND', 24: 'AGO', 28: 'ATG', 31: 'AZE', 32: 'ARG',
        36: 'AUS', 40: 'AUT', 44: 'BHS', 48: 'BHR', 50: 'BGD', 51: 'ARM', 52: 'BRB', 56: 'BEL', 60: 'BMU',
        64: 'BTN', 68: 'BOL', 70: 'BIH', 72: 'BWA', 76: 'BRA', 84: 'BLZ', 86: 'IOT', 90: 'SLB', 92: 'VGB',
        96: 'BRN', 100: 'BGR', 104: 'MMR', 108: 'BDI', 112: 'BLR', 116: 'KHM', 120: 'CMR', 124: 'CAN',
        132: 'CPV', 136: 'CYM', 140: 'CAF', 144: 'LKA', 148: 'TCD', 152: 'CHL', 156: 'CHN', 158: 'TWN',
        162: 'CXR', 166: 'CCK', 170: 'COL', 174: 'COM', 175: 'MYT', 178: 'COG', 180: 'COD', 184: 'COK',
        188: 'CRI', 191: 'HRV', 192: 'CUB', 196: 'CYP', 203: 'CZE', 204: 'BEN', 208: 'DNK', 212: 'DMA',
        214: 'DOM', 218: 'ECU', 222: 'SLV', 226: 'GNQ', 231: 'ETH', 232: 'ERI', 233: 'EST', 234: 'FRO',
        238: 'FLK', 239: 'SGS', 242: 'FJI', 246: 'FIN', 248: 'ALA', 250: 'FRA', 254: 'GUF', 258: 'PYF',
        260: 'ATF', 262: 'DJI', 266: 'GAB', 268: 'GEO', 270: 'GMB', 275: 'PSE', 276: 'DEU', 288: 'GHA',
        292: 'GIB', 296: 'KIR', 300: 'GRC', 304: 'GRL', 308: 'GRD', 312: 'GLP', 316: 'GUM', 320: 'GTM',
        324: 'GIN', 328: 'GUY', 332: 'HTI', 334: 'HMD', 336: 'VAT', 340: 'HND', 344: 'HKG', 348: 'HUN',
        352: 'ISL', 356: 'IND', 360: 'IDN', 364: 'IRN', 368: 'IRQ', 372: 'IRL', 376: 'ISR', 380: 'ITA',
        384: 'CIV', 388: 'JAM', 392: 'JPN', 398: 'KAZ', 400: 'JOR', 404: 'KEN', 408: 'PRK', 410: 'KOR',
        414: 'KWT', 417: 'KGZ', 418: 'LAO', 422: 'LBN', 426: 'LSO', 428: 'LVA', 430: 'LBR', 434: 'LBY',
        438: 'LIE', 440: 'LTU', 442: 'LUX', 446: 'MAC', 450: 'MDG', 454: 'MWI', 458: 'MYS', 462: 'MDV',
        466: 'MLI', 470: 'MLT', 474: 'MTQ', 478: 'MRT', 480: 'MUS', 484: 'MEX', 492: 'MCO', 496: 'MNG',
        498: 'MDA', 499: 'MNE', 500: 'MSR', 504: 'MAR', 508: 'MOZ', 512: 'OMN', 516: 'NAM', 520: 'NRU',
        524: 'NPL', 528: 'NLD', 531: 'CUW', 533: 'ABW', 534: 'SXM', 535: 'BES', 540: 'NCL', 548: 'VUT',
        554: 'NZL', 558: 'NIC', 562: 'NER', 566: 'NGA', 570: 'NIU', 574: 'NFK', 578: 'NOR', 580: 'MNP',
        581: 'UMI', 583: 'FSM', 584: 'MHL', 585: 'PLW', 586: 'PAK', 591: 'PAN', 598: 'PNG', 600: 'PRY',
        604: 'PER', 608: 'PHL', 612: 'PCN', 616: 'POL', 620: 'PRT', 624: 'GNB', 626: 'TLS', 630: 'PRI',
        634: 'QAT', 638: 'REU', 642: 'ROU', 643: 'RUS', 646: 'RWA', 652: 'BLM', 654: 'SHN', 659: 'KNA',
        660: 'AIA', 662: 'LCA', 663: 'MAF', 666: 'SPM', 670: 'VCT', 674: 'SMR', 678: 'STP', 682: 'SAU',
        686: 'SEN', 688: 'SRB', 690: 'SYC', 694: 'SLE', 702: 'SGP', 703: 'SVK', 704: 'VNM', 705: 'SVN',
        706: 'SOM', 710: 'ZAF', 716: 'ZWE', 724: 'ESP', 728: 'SSD', 729: 'SDN', 732: 'ESH', 740: 'SUR',
        744: 'SJM', 748: 'SWZ', 752: 'SWE', 756: 'CHE', 760: 'SYR', 762: 'TJK', 764: 'THA', 768: 'TGO',
        772: 'TKL', 776: 'TON', 780: 'TTO', 784: 'ARE', 788: 'TUN', 792: 'TUR', 795: 'TKM', 796: 'TCA',
        798: 'TUV', 800: 'UGA', 804: 'UKR', 807: 'MKD', 818: 'EGY', 826: 'GBR', 831: 'GGY', 832: 'JEY',
        833: 'IMN', 834: 'TZA', 840: 'USA', 850: 'VIR', 854: 'BFA', 858: 'URY', 860: 'UZB', 862: 'VEN',
        876: 'WLF', 882: 'WSM', 886: 'YEM', 887: 'ZMB', 894: 'ZWE'
      };

      const width = host.clientWidth || 900;
      const height = host.clientHeight || 360; // Use actual container height, not hardcoded 460

      host.innerHTML = '';
      const svg = window.d3.select(host)
        .append('svg')
        .attr('viewBox', '0 0 ' + width + ' ' + height)
        .attr('width', '100%')
        .attr('height', height);

      const projection = window.d3.geoEquirectangular()
        .scale(width / 6.5)
        .center([0, 20])
        .translate([width / 2, height / 2]);

      const path = window.d3.geoPath().projection(projection);

      const tooltip = document.getElementById('mapTooltip');

      // Helper: lookup ISO code from numeric ID (handles string/number type coercion)
      function getIsoFromId(id) {
        // TopoJSON may store IDs as strings or numbers - try both
        const numericId = parseInt(id, 10);
        return idToIso[numericId] || idToIso[id] || null;
      }
      
      // Helper: check if ISO code is in an array (case-insensitive)
      function isInArray(iso, arr) {
        if (!arr || arr.length === 0) return false;
        const isoUpper = iso.toUpperCase();
        return arr.some(function(item) {
          return item && item.toUpperCase() === isoUpper;
        });
      }
      
      window.d3.json('https://unpkg.com/world-atlas@2.0.2/countries-110m.json').then(function (world) {
        const countries = window.topojson.feature(world, world.objects.countries);

        svg.selectAll('path')
          .data(countries.features)
          .enter()
          .append('path')
          .attr('d', path)
          .attr('class', function (d) {
            const iso = getIsoFromId(d.id);
            if (!iso) {
              return 'country';
            }
            // Check arrays in priority order: current > lived > visited
            if (isInArray(iso, current)) {
              return 'country current';
            }
            if (isInArray(iso, lived)) {
              return 'country lived';
            }
            if (isInArray(iso, visited)) {
              return 'country visited';
            }
            return 'country';
          })
          .on('mouseenter', function (event, d) {
            if (!tooltip) return;
            const iso = getIsoFromId(d.id);
            // Only show tooltip for countries in current, lived, or visited arrays
            if (iso && (isInArray(iso, current) || isInArray(iso, lived) || isInArray(iso, visited))) {
              // Use country name from mapping, or fall back to ISO code
              const displayName = countryNames[iso] || iso;
              tooltip.textContent = displayName;
              tooltip.classList.add('visible');
              tooltip.setAttribute('aria-hidden', 'false');
            }
          })
          .on('mousemove', function (event) {
            if (!tooltip) return;
            tooltip.style.left = (event.clientX + 12) + 'px';
            tooltip.style.top = (event.clientY - 30) + 'px';
          })
          .on('mouseleave', function () {
            if (!tooltip) return;
            tooltip.classList.remove('visible');
            tooltip.setAttribute('aria-hidden', 'true');
          });

        // Add beacon for current location (use first current location)
        if (current.length > 0) {
          const currentIso = current[0].toUpperCase();
          
          // Fallback coordinates for small countries not in 110m TopoJSON
          // [longitude, latitude] format for D3 geo projection
          const smallCountryCoords = {
            'SGP': [103.8198, 1.3521],   // Singapore
            'MDV': [73.2207, 3.2028],    // Maldives
            'BHR': [50.5577, 26.0667],   // Bahrain
            'MLT': [14.3754, 35.9375],   // Malta
            'LUX': [6.1296, 49.8153],    // Luxembourg
            'MCO': [7.4246, 43.7384],    // Monaco
            'SMR': [12.4578, 43.9424],   // San Marino
            'LIE': [9.5554, 47.1660],    // Liechtenstein
            'AND': [1.6016, 42.5063],    // Andorra
            'VAT': [12.4534, 41.9029],   // Vatican City
            'MUS': [57.5522, -20.3484],  // Mauritius
            'SYC': [55.4540, -4.6796],   // Seychelles
            'BRB': [-59.5432, 13.1939],  // Barbados
            'ATG': [-61.7964, 17.0608],  // Antigua and Barbuda
            'GRD': [-61.6790, 12.1165],  // Grenada
            'LCA': [-60.9789, 13.9094],  // Saint Lucia
            'VCT': [-61.1971, 13.2528],  // Saint Vincent
            'KNA': [-62.7830, 17.3578],  // Saint Kitts and Nevis
            'BRN': [114.7277, 4.5353],   // Brunei
            'QAT': [51.1839, 25.3548],   // Qatar
          };
          
          // Find the feature for current ISO
          let currentFeature = null;
          for (let i = 0; i < countries.features.length; i++) {
            const iso = getIsoFromId(countries.features[i].id);
            if (iso && iso.toUpperCase() === currentIso) {
              currentFeature = countries.features[i];
              break;
            }
          }
          
          // Calculate position - use feature centroid or fallback coordinates
          let px, py;
          if (currentFeature) {
            // Use TopoJSON feature centroid
            const centroid = path.centroid(currentFeature);
            px = centroid[0];
            py = centroid[1];
          } else if (smallCountryCoords[currentIso]) {
            // Use manual coordinates for small countries via projection
            const coords = projection(smallCountryCoords[currentIso]);
            if (coords) {
              px = coords[0];
              py = coords[1];
            }
          }
          
          // Only create beacon if we have valid coordinates
          if (px && py && !isNaN(px) && !isNaN(py)) {
            const g = svg.append('g').attr('class', 'current-marker-group').attr('transform', 'translate(' + px + ',' + py + ')');

          // Get beacon color from CSS variables (respects dark mode)
          function getBeaconColor() {
            const styles = window.getComputedStyle(document.documentElement);
            // Use --blue for light mode, or read a dark mode variant if available
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            if (isDark) {
              // In dark mode, use orange (inverse of blue) - check if CSS variable exists
              const darkBeacon = styles.getPropertyValue('--blue-dark-beacon')?.trim();
              if (darkBeacon) {
                return darkBeacon;
              }
              // Fallback: use CSS variable for orange if defined, otherwise use computed --blue and invert
              const orange = styles.getPropertyValue('--orange')?.trim() || '#FF6B35';
              return orange;
            } else {
              // Light mode: use --blue CSS variable
              return styles.getPropertyValue('--blue')?.trim() || '#1E5AFF';
            }
          }
          
          const beaconColor = getBeaconColor();
          const beaconSize = 7;
          
          // Pulsing outer circle
          function pulse() {
            g.append('circle')
              .attr('class', 'current-pulse')
              .attr('r', beaconSize)
              .attr('fill', 'none')
              .attr('stroke', beaconColor)
              .attr('stroke-width', 2)
              .attr('opacity', 0.7)
              .transition()
              .duration(1200)
              .attr('r', 22)
              .attr('opacity', 0)
              .remove()
              .on('end', pulse);
          }
          pulse();

          // Main marker circle
          g.append('circle')
            .attr('class', 'current-marker')
            .attr('r', beaconSize)
            .attr('fill', beaconColor);
          
          // Inner white dot
          g.append('circle')
            .attr('r', 3)
            .attr('fill', '#fff');
          
          // Listen for theme changes and update beacon color
          const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
              if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                const newBeaconColor = getBeaconColor();
                // Update existing circles
                g.selectAll('.current-marker').attr('fill', newBeaconColor);
                g.selectAll('.current-pulse').attr('stroke', newBeaconColor);
              }
            });
          });
          observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
          }
        }
      }).catch(function (err) {
        // Fail silently - map is progressive enhancement
      });
      } catch (drawError) {
        // Fail silently - map is progressive enhancement
      }
    }

    // Wait for D3, TopoJSON, and places data to be ready
    function tryDraw(attempts) {
      attempts = attempts || 0;
      
      // Check for D3 and TopoJSON
      if (!window.d3 || !window.topojson) {
        if (attempts < 50) { // Wait up to 5 seconds
          setTimeout(function() { tryDraw(attempts + 1); }, 100);
          return;
        }
        return; // Give up silently
      }
      
      // Check for places data (wp_localize_script might need a moment)
      if (!window.kunaalAbout || !window.kunaalAbout.places) {
        if (attempts < 50) { // Wait up to 5 seconds
          setTimeout(function() { tryDraw(attempts + 1); }, 100);
          return;
        }
        // After waiting, proceed with empty data
      }
      
      try {
        draw();
      } catch (e) {
        // Fail silently - map is progressive enhancement
      }
    }
    
    tryDraw();
  }

  // =============================================
  // Progress bar (top) and scroll indicator hide
  // =============================================
  function initProgressBar(){
    const fill = document.getElementById('progressFill');
    if(!fill) return;
    let ticking = false;
    function update(){
      ticking = false;
      const doc = document.documentElement;
      const scrollTop = window.pageYOffset || doc.scrollTop || 0;
      const scrollHeight = (doc.scrollHeight || 0) - (doc.clientHeight || window.innerHeight || 1);
      const p = scrollHeight > 0 ? (scrollTop / scrollHeight) : 0;
      fill.style.width = (p * 100).toFixed(2) + '%';

      // Header compaction (drives CSS var(--p))
      const hp = Math.min(scrollTop / 120, 1);
      document.body.style.setProperty('--p', hp.toFixed(4));
    }
    function onScroll(){
      if(ticking) return;
      ticking = true;
      requestAnimationFrame(update);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    update();
  }

  // =============================================
  // Header nav (mobile toggle)
  // =============================================
  function initHeaderNav(){
    const toggle = document.querySelector('[data-ui="nav-toggle"]');
    const nav = document.querySelector('[data-ui="nav"]');
    if(!toggle || !nav) return;

    function close(){
      nav.classList.remove('open');
      toggle.setAttribute('aria-expanded','false');
    }
    function open(){
      nav.classList.add('open');
      toggle.setAttribute('aria-expanded','true');
    }

    toggle.setAttribute('aria-expanded','false');

    toggle.addEventListener('click', function(e){
      e.preventDefault();
      e.stopPropagation();
      if(nav.classList.contains('open')) close();
      else open();
    });

    // Close when clicking outside
    document.addEventListener('click', function(e){
      if(!nav.classList.contains('open')) return;
      const t = e.target;
      if(nav.contains(t) || toggle.contains(t)) return;
      close();
    });

    // Close after selecting a link (mobile)
    nav.addEventListener('click', function(e){
      const a = e.target && e.target.closest ? e.target.closest('a') : null;
      if(a) close();
    });

    // Escape closes
    document.addEventListener('keydown', function(e){
      if(e.key === 'Escape') close();
    });

    // Resizing up closes
    window.addEventListener('resize', function(){
      if(window.innerWidth > 760) close();
    }, { passive: true });
  }

  // =============================================
  // Hero mosaic: keep ONE tile in color, cycling like subtle "Christmas lights"
  // =============================================
  function initHeroMosaicCycle(){
    // Scope selectors to About page to prevent accidental cross-page matches
    const aboutPage = document.querySelector('.kunaal-about-page');
    if (!aboutPage) return;
    
    const tiles = Array.prototype.slice.call(aboutPage.querySelectorAll('.hero-photo'));
    if(!tiles.length) return;

    // Track hover so we don't constantly "steal" focus from the user's cursor.
    tiles.forEach(function(t){
      t.__isHover = false;
      t.addEventListener('mouseenter', function(){ t.__isHover = true; });
      t.addEventListener('mouseleave', function(){ t.__isHover = false; });
    });

    let idx = Math.floor(Math.random() * tiles.length);
    function setActive(i){
      tiles.forEach(function(t, j){
        if(j === i) t.classList.add('color-active');
        else t.classList.remove('color-active');
      });
      idx = i;
    }
    setActive(idx);

    setInterval(function(){
      if(document.hidden) return;
      // Choose next non-hovered tile if possible.
      const candidates = tiles.map(function(_, i){ return i; }).filter(function(i){ return !tiles[i].__isHover; });
      if(!candidates.length) return;
      const currentPos = candidates.indexOf(idx);
      const next = candidates[(currentPos + 1) % candidates.length];
      setActive(next);
    }, 2600);
  }

  // =============================================
  // Rabbit hole capsules: wrap inner content so CSS animations don't fight GSAP
  // =============================================
  function initCapsuleLife(){
    const capsules = document.querySelectorAll('.capsules-cloud .capsule');
    if(!capsules.length) return;

    capsules.forEach(function(cap){
      let inner = cap.querySelector('.capsule-inner');

      // Wrap contents so our idle animation doesn't fight GSAP transforms
      if(!inner){
        inner = document.createElement('span');
        inner.className = 'capsule-inner';
        while(cap.firstChild){
          inner.appendChild(cap.firstChild);
        }
        cap.appendChild(inner);
      }

      // Ensure the category dot moves with the capsule (no pseudo-element jitter)
      if(!inner.querySelector('.capsule-dot')){
        const dot = document.createElement('span');
        dot.className = 'capsule-dot';
        dot.setAttribute('aria-hidden', 'true');
        inner.appendChild(dot);
      }
    });
  }

  // =============================================
  // Footer year (keep it evergreen)
  // =============================================
  function initFooterYear(){
    const year = document.getElementById('footerYear');
    if(year) {
      year.textContent = new Date().getFullYear();
    }
    // Also check for #year (reference HTML uses this)
    const yearAlt = document.getElementById('year');
    if(yearAlt) {
      yearAlt.textContent = new Date().getFullYear();
    }
  }

  // =============================================
  // INITIALIZATION
  // =============================================
  ready(function() {
    init();
  });
})();

