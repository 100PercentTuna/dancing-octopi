/**
 * About Page V22 Polished Design JavaScript
 * 
 * Extracted and adapted from kunaal-about-v22-polished.html
 * All animations, effects, and interactions preserved
 *
 * @package Kunaal_Theme
 * @since 4.21.0
 */

(function () {
  'use strict';

  // Debug logging helper - uses WordPress AJAX endpoint
  // Only active when debug is enabled via localized config
  function debugLog(location, message, data, hypothesisId) {
    // Gate behind debug config - no-op if debug is false
    if (!window.kunaalAbout || !window.kunaalAbout.debug) {
      return; // Debug logging disabled by default
    }
    
    // Verify required config is available
    if (!window.kunaalAbout.ajaxUrl || !window.kunaalAbout.nonce) {
      return; // Skip if config incomplete
    }
    
    const logData = {
      location: location,
      message: message,
      data: data || {},
      timestamp: Date.now(),
      sessionId: 'debug-session',
      runId: 'run1',
      hypothesisId: hypothesisId || ''
    };
    const formData = new FormData();
    formData.append('action', 'kunaal_debug_log');
    formData.append('log_data', JSON.stringify(logData));
      formData.append('nonce', window.kunaalAbout.nonce);
      fetch(window.kunaalAbout.ajaxUrl, {
      method: 'POST',
      body: formData
    }).catch(function(error) {
      // Silently fail if logging unavailable - debug logging should never break the page
      if (window.console && window.console.warn) {
        window.console.warn('[kunaal-theme] Debug log failed:', error);
      }
    });
  }

  let reduceMotion = false;
  try {
    reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  } catch (e) {
    // Fallback if matchMedia is not supported
    reduceMotion = false;
    if (window.console && window.console.warn) {
      window.console.warn('[kunaal-theme] matchMedia not supported:', e);
    }
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
    // #region agent log
    debugLog('about-page.js:33', 'init() called', {viewportWidth:window.innerWidth,viewportHeight:window.innerHeight}, 'H2.1,H3.1,H4.1');
    // #endregion
    
    const gsapOk = hasGSAP();
    // #region agent log
    debugLog('about-page.js:36', 'GSAP check', {gsapOk:gsapOk,hasGSAP:!!window.gsap,hasScrollTrigger:!!window.ScrollTrigger}, 'H2.1');
    // #endregion
    
    // Mark elements as GSAP-ready only if GSAP is available
    if (gsapOk) {
      // Add class to body so CSS knows GSAP is ready
      document.body.classList.add('gsap-ready');
      try { 
        window.gsap.registerPlugin(window.ScrollTrigger); 
      } catch (e) {
        if (window.kunaalTheme?.debug) {
          console.warn('GSAP ScrollTrigger registration failed:', e);
        }
        // #region agent log
        debugLog('about-page.js:40', 'GSAP registration error', {error:e.message}, 'H2.1');
        // #endregion
      }
    }

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
    
    // #region agent log - Check dog-ear and scroll indicator after init
    setTimeout(function() {
      const accentPhoto = document.querySelector('.hero-photo.has-accent');
      const scrollIndicator = document.getElementById('scrollIndicator');
      if (accentPhoto) {
        // Ensure overflow: visible is applied (in case something overrides it)
        const computedOverflow = window.getComputedStyle(accentPhoto).overflow;
        if (computedOverflow !== 'visible') {
          accentPhoto.style.overflow = 'visible';
        }
        const img = accentPhoto.querySelector('img');
        const before = window.getComputedStyle(accentPhoto, '::before');
        const imgStyles = window.getComputedStyle(img);
        debugLog('about-page.js:58', 'Dog-ear styles check', {hasAccentPhoto:!!accentPhoto,imgZIndex:imgStyles.zIndex,imgPosition:imgStyles.position,imgTransform:imgStyles.transform,accentIsolation:window.getComputedStyle(accentPhoto).isolation,accentOverflow:window.getComputedStyle(accentPhoto).overflow}, 'H1.1,H1.2,H1.3,H1.5');
      }
      if (scrollIndicator) {
        const siStyles = window.getComputedStyle(scrollIndicator);
        const rect = scrollIndicator.getBoundingClientRect();
        debugLog('about-page.js:65', 'Scroll indicator check', {exists:!!scrollIndicator,opacity:siStyles.opacity,display:siStyles.display,visibility:siStyles.visibility,zIndex:siStyles.zIndex,top:rect.top,left:rect.left,width:rect.width,height:rect.height,inViewport:rect.top>=0&&rect.left>=0&&rect.bottom<=window.innerHeight&&rect.right<=window.innerWidth}, 'H2.2,H2.3,H2.5');
      }
    }, 1000);
    // #endregion
  }

  // =============================================
  // PAGE LOAD - quiet editorial entrance
  // =============================================
  function initPageLoad(gsapOk) {
    // #region agent log
    debugLog('about-page.js:113', 'initPageLoad called', {gsapOk:gsapOk,reduceMotion:reduceMotion}, 'H2.1');
    // #endregion
    
    if (reduceMotion || !gsapOk) return;
    try {
      const scrollIndicator = document.getElementById('scrollIndicator');
      // #region agent log
      debugLog('about-page.js:120', 'Scroll indicator before GSAP', {exists:!!scrollIndicator,initialOpacity:scrollIndicator?window.getComputedStyle(scrollIndicator).opacity:null}, 'H2.1');
      // #endregion
      
      const tl = window.gsap.timeline({ defaults: { ease: 'power2.out' } });
      tl.from('.nav', { y: -10, opacity: 0, duration: 0.55 })
        .from('.hero-photo', { opacity: 0, duration: 0.6, stagger: 0.06 }, '<0.05')
        .from('.hero-text [data-reveal]', { y: 16, opacity: 0, duration: 0.55, stagger: 0.08 }, '<0.15')
        .from('#scrollIndicator', { 
          opacity: 0,
          y: 8,
          x: 0,
          duration: 0.35,
          immediateRender: false,
          onComplete: function() {
            if (scrollIndicator) {
              window.gsap.set(scrollIndicator, { 
                opacity: 1, 
                x: 0, 
                y: 0, 
                clearProps: 'all'
              });
              scrollIndicator.style.opacity = '1';
            }
          }
        }, '<0.25');
      
      // Scroll indicator is now in-flow (not fixed) - only adjust opacity if needed
      // No display toggles to avoid layout shifts
      if (scrollIndicator) {
        const scrollFadeHandler = function() {
          const scrollY = window.scrollY || window.pageYOffset;
          const fadeStart = 100; // Start fading after 100px scroll
          const fadeEnd = 300; // Fully faded at 300px
          let opacity = 1;
          
          if (scrollY > fadeStart) {
            opacity = Math.max(0, 1 - ((scrollY - fadeStart) / (fadeEnd - fadeStart)));
          }
          
          // Only adjust opacity, no display toggles
          scrollIndicator.style.opacity = opacity.toString();
        };
        
        window.addEventListener('scroll', scrollFadeHandler, { passive: true });
        scrollFadeHandler(); // Initial check
      }
      
      // #region agent log
      tl.eventCallback('onComplete', function() {
        if (scrollIndicator) {
          const finalStyles = window.getComputedStyle(scrollIndicator);
          debugLog('about-page.js:137', 'Scroll indicator after GSAP animation', {opacity:finalStyles.opacity,display:finalStyles.display,visibility:finalStyles.visibility}, 'H2.1');
        }
      });
      // #endregion
    } catch (e) {
      if (window.kunaalTheme?.debug) {
        console.warn('Page load animation failed:', e);
      }
      // #region agent log
      debugLog('about-page.js:144', 'Page load animation error', {error:e.message,stack:e.stack}, 'H2.1');
      // #endregion
    }
  }

  // =============================================
  // Scroll-triggered reveals (left/right/up)
  // =============================================
  function initScrollReveals(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    const els = document.querySelectorAll('[data-reveal]');
    let isMobile = window.innerWidth < 900;
    
    // Skip ScrollTrigger animations on mobile - use CSS fallback instead
    if (isMobile) {
      window.gsap.set('.hero-label, .hero-title, .hero-intro, .hero-meta', {
        opacity: 1, 
        y: 0, 
        clearProps: 'all'
      });
      return; // Don't set up ScrollTrigger on mobile
    }
    
    // Store all ScrollTrigger instances for global resize handler
    const scrollTriggers = [];
    const revealElements = [];
    
    for (var i = 0; i < els.length; i++) {
      (function (el) {
        const dir = el.getAttribute('data-reveal') || 'up';
        let x = 0, y = 14;
        if (dir === 'left') { x = -18; y = 0; }
        if (dir === 'right') { x = 18; y = 0; }
        if (dir === 'down') { x = 0; y = -14; }
        
        // Skip scroll indicator - it should not have x transform
        // Note: scroll indicator is now hero-scroll inside hero-text, not fixed
        if (el.id === 'scrollIndicator' || el.closest('#scrollIndicator') || el.classList.contains('hero-scroll')) {
          x = 0; // Force no x transform for scroll indicator
        }
        
        // Mobile-specific handling for hero-text elements
        const isHeroText = el.closest('.hero-text') !== null;
        const startPos = 'top 86%';
        const immediateRender = true;
        
        try {
          // Set initial state for animation
          window.gsap.set(el, { opacity: 0, x: x, y: y });
          
          const st = window.gsap.from(el, {
            x: x,
            y: y,
            opacity: 0,
            duration: 0.55,
            ease: 'power2.out',
            immediateRender: immediateRender,
            scrollTrigger: {
              trigger: el,
              start: startPos,
              toggleActions: 'play none none reverse',
              refreshPriority: 1,
              invalidateOnRefresh: true, // Recalculate on resize
              onEnter: function() {
                // Ensure final state is always opacity:1, y:0 - use clearProps to remove all GSAP styles
                window.gsap.set(el, { opacity: 1, x: 0, y: 0, clearProps: 'all' });
                el.style.opacity = '1';
                el.style.transform = 'none';
              },
              onEnterBack: function() {
                // Ensure final state when scrolling back
                window.gsap.set(el, { opacity: 1, x: 0, y: 0, clearProps: 'all' });
                el.style.opacity = '1';
                el.style.transform = 'none';
              },
              onLeave: function() {
                // Keep visible when scrolling past
                window.gsap.set(el, { opacity: 1, x: 0, y: 0, clearProps: 'all' });
              }
            }
          });
          
          // Store for global resize handler
          scrollTriggers.push({ st: st, el: el, isHeroText: isHeroText });
          revealElements.push(el);
        } catch (e) {
          if (window.kunaalTheme?.debug) {
            console.warn('Scroll reveal failed for element:', e);
          }
        }
      })(els[i]);
    }
    
    // Single global debounced resize handler for all elements
    function handleGlobalResize() {
      if (!window.gsap || !window.ScrollTrigger) return;
      
      const newIsMobile = window.innerWidth < 900;
      
      // Refresh all ScrollTriggers
      window.ScrollTrigger.refresh();
      
      // Check all elements after refresh
      setTimeout(function() {
        for (var j = 0; j < scrollTriggers.length; j++) {
          const item = scrollTriggers[j];
          const el = item.el;
          const st = item.st;
          const isHeroText = item.isHeroText;
          
          if (!st || !st.scrollTrigger) continue;
          
          const afterRect = el.getBoundingClientRect();
          const afterIsInViewport = afterRect.top >= 0 && afterRect.left >= 0 && afterRect.bottom <= window.innerHeight && afterRect.right <= window.innerWidth;
          const isActuallyVisible = afterRect.top < window.innerHeight && afterRect.bottom > 0 && afterRect.left < window.innerWidth && afterRect.right > 0;
          
          // For hero text elements, always check if they're in viewport and force visible if needed
          if (isHeroText && (afterIsInViewport || isActuallyVisible)) {
            const computed = window.getComputedStyle(el);
            const currentOpacity = parseFloat(computed.opacity);
            const currentY = parseFloat(computed.transform.match(/translateY\(([^)]+)\)/) ? computed.transform.match(/translateY\(([^)]+)\)/)[1] : '0') || 0;
            if (currentOpacity < 0.9 || Math.abs(currentY) > 2 || isNaN(currentOpacity)) {
              window.gsap.set(el, { opacity: 1, x: 0, y: 0, clearProps: 'all' });
              el.style.opacity = '1';
              el.style.transform = 'none';
              st.scrollTrigger.refresh();
            }
          } else if (afterIsInViewport || (isActuallyVisible && afterRect.top > -100)) {
            // For non-hero elements
            const computed = window.getComputedStyle(el);
            const currentOpacity = parseFloat(computed.opacity);
            if (currentOpacity < 0.9 || isNaN(currentOpacity)) {
              window.gsap.set(el, { opacity: 1, x: 0, y: 0, clearProps: 'all' });
              el.style.opacity = '1';
              el.style.transform = 'none';
              st.scrollTrigger.refresh();
            }
          }
        }
      }, 150);
    }
    
    // Debounce helper
    function debounce(func, wait) {
      let timeout;
      return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
          func.apply(context, args);
        }, wait);
      };
    }
    
    // Single global resize listener (replaces per-element listeners)
    const debouncedResize = debounce(handleGlobalResize, 150);
    window.addEventListener('resize', debouncedResize, { passive: true });
    
    // Handle wide viewport - clear GSAP inline styles that override CSS
    function handleWideViewport() {
      if (window.innerWidth > 1600) {
        window.gsap.set('.hero-label, .hero-title, .hero-intro, .hero-meta', {
          clearProps: 'all'  // This removes GSAP inline styles
        });
      }
    }
    
    window.addEventListener('resize', debounce(handleWideViewport, 250), { passive: true });
    handleWideViewport(); // Run on load too
  }

  // =============================================
  // Masked "portal band" parallax (fast)
  // Performance: Disabled on mobile, gated by IntersectionObserver
  // =============================================
  function initPanoramaParallax(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    
    // Disable parallax on mobile for performance
    const isMobile = window.innerWidth < 900 || window.matchMedia('(hover: none)').matches;
    if (isMobile) return;
    
    const bands = document.querySelectorAll('.panorama');
    if (!bands.length) return;
    
    // Use IntersectionObserver to only activate parallax when bands are near viewport
    const observerOptions = {
      root: null,
      rootMargin: '50%', // Start parallax when band is 50% away from viewport
      threshold: 0
    };
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (!entry.isIntersecting) return;
        
        const band = entry.target;
        const img = band.querySelector('.panorama-img');
        if (!img) return;
        
        // Only initialize if not already initialized
        if (band.dataset.parallaxInitialized === 'true') return;
        band.dataset.parallaxInitialized = 'true';
        
        const speed = parseFloat(band.getAttribute('data-speed') || '1');
        if (!isFinite(speed)) speed = 1;
        // Parallax movement: increased amplitude for more dramatic scrollytelling effect
        // Image is 220% height in CSS, so we have plenty of room for movement
        const amp = 25 * speed; // percent of the IMAGE height (increased from 10 for more movement)
        try {
          window.gsap.fromTo(img,
            { yPercent: -amp },
            {
              yPercent: amp,
              ease: 'none',
              force3D: true, // Force hardware acceleration for smooth performance
              scrollTrigger: {
                trigger: band,
                start: 'top bottom',
                end: 'bottom top',
                scrub: true // Smooth, lag-free parallax
              }
            }
          );
        } catch (e) {
          if (window.kunaalTheme?.debug) {
            console.warn('Panorama parallax failed:', e);
          }
        }
      });
    }, observerOptions);
    
    // Observe all bands
    for (var i = 0; i < bands.length; i++) {
      observer.observe(bands[i]);
    }
  }

  // =============================================
  // "Sticky scroll" / pinned scene (tasteful)
  // =============================================
  function initPinnedScenes(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    const pins = document.querySelectorAll('[data-pin="true"]');
    for (var i = 0; i < pins.length; i++) {
      try {
        window.ScrollTrigger.create({
          trigger: pins[i],
          start: 'top top',
          end: '+=140%',
          pin: true,
          pinSpacing: true
        });
      } catch (e) {
        if (window.kunaalTheme?.debug) {
          console.warn('Pinned scene failed:', e);
        }
      }
    }
  }

  // =============================================
  // Giant background words drifting horizontally
  // =============================================
  function initMarqueeWords(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    const words = document.querySelectorAll('[data-marquee]');
    for (var i = 0; i < words.length; i++) {
      (function (el) {
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
          if (window.kunaalTheme?.debug) {
            console.warn('Marquee word failed:', e);
          }
        }
      })(words[i]);
    }
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
    // #region agent log
    debugLog('about-page.js:442', 'initWorldMap called', {timestamp:Date.now()}, 'H3.1,H3.2');
    // #endregion
    
    const host = document.getElementById('world-map');
    // #region agent log
    debugLog('about-page.js:447', 'Map element check', {hostExists:!!host,hostId:host?host.id:null,hostWidth:host?host.clientWidth:null,hostHeight:host?host.clientHeight:null}, 'H3.2,H3.5');
    // #endregion
    
    if (!host) return;

    // D3 and TopoJSON should already be loaded via WordPress enqueue
    // #region agent log
    debugLog('about-page.js:454', 'D3/TopoJSON check', {hasD3:!!window.d3,hasTopojson:!!window.topojson}, 'H3.1');
    // #endregion
    
    if (!window.d3 || !window.topojson) {
      if (window.kunaalTheme?.debug) {
        console.warn('D3.js or TopoJSON not loaded');
      }
      return;
    }

    function draw() {
      // #region agent log
      debugLog('about-page.js:464', 'Map draw() called', {timestamp:Date.now()}, 'H3.3,H3.4');
      // #endregion
      
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
      
      // #region agent log
      debugLog('about-page.js:480', 'Places data check', {hasKunaalAbout:!!window.kunaalAbout,currentCount:current.length,livedCount:lived.length,visitedCount:visited.length,current:current,lived:lived,visited:visited}, 'H3.3');
      // #endregion
      
      // Debug: log if no places data (helpful for troubleshooting)
      if (current.length === 0 && lived.length === 0 && visited.length === 0) {
        if (window.kunaalTheme?.debug) {
          console.warn('About page map: No places data found. Check Customizer settings for Places section.');
        }
      }
      
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

      // #region agent log
      debugLog('about-page.js:563', 'Starting D3.json fetch', {width:width,height:height}, 'H3.4');
      // #endregion
      
      window.d3.json('https://unpkg.com/world-atlas@2.0.2/countries-110m.json').then(function (world) {
        // #region agent log
        debugLog('about-page.js:568', 'D3.json success', {hasWorld:!!world,hasObjects:!!world.objects}, 'H3.4');
        // #endregion
        
        const countries = window.topojson.feature(world, world.objects.countries);

        svg.selectAll('path')
          .data(countries.features)
          .enter()
          .append('path')
          .attr('d', path)
          .attr('class', function (d) {
            const iso = idToIso[d.id];
            if (!iso) {
              return 'country';
            }
            // Check all arrays (case-insensitive for safety)
            const isoUpper = iso.toUpperCase();
            if (current.length > 0 && current.some(function(c) { return c.toUpperCase() === isoUpper; })) {
              return 'country current';
            }
            if (lived.length > 0 && lived.some(function(l) { return l.toUpperCase() === isoUpper; })) {
              return 'country lived';
            }
            if (visited.length > 0 && visited.some(function(v) { return v.toUpperCase() === isoUpper; })) {
              return 'country visited';
            }
            return 'country';
          })
          .on('mouseenter', function (event, d) {
            if (!tooltip) return;
            const iso = idToIso[d.id];
            if (iso && countryNames[iso]) {
              tooltip.textContent = countryNames[iso];
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
          var smallCountryCoords = {
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
          for (var i = 0; i < countries.features.length; i++) {
            const iso = idToIso[countries.features[i].id];
            if (iso && iso.toUpperCase() === currentIso) {
              currentFeature = countries.features[i];
              break;
            }
          }
          
          // Calculate position - use feature centroid or fallback coordinates
          var px, py;
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
        if (window.kunaalTheme?.debug) {
          console.warn('World map data load failed:', err);
        }
        // #region agent log
        debugLog('about-page.js:699', 'D3.json error', {error:err.message,stack:err.stack}, 'H3.4');
        // #endregion
      });
      } catch (drawError) {
        // #region agent log
        debugLog('about-page.js:704', 'Map draw() error', {error:drawError.message,stack:drawError.stack}, 'H3.4');
        // #endregion
        if (window.kunaalTheme?.debug) {
          console.warn('Map draw() failed:', drawError);
        }
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
        if (window.kunaalTheme?.debug) {
          console.warn('World map: D3.js or TopoJSON not loaded after waiting.');
        }
        return;
      }
      
      // Check for places data (wp_localize_script might need a moment)
      if (!window.kunaalAbout || !window.kunaalAbout.places) {
        if (attempts < 50) { // Wait up to 5 seconds
          setTimeout(function() { tryDraw(attempts + 1); }, 100);
          return;
        }
        // After waiting, proceed with empty data
        if (window.kunaalTheme?.debug) {
          console.warn('About page map: Places data not loaded after waiting. Proceeding with empty data.');
        }
      }
      
      try {
        draw();
      } catch (e) {
        if (window.kunaalTheme?.debug) {
          console.warn('World map draw failed:', e);
        }
      }
    }
    
    // Start trying to draw
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

  /**
   * Sync media item heights - digital items match book items (the taller variant)
   * Books have 2:3 aspect ratio covers, digital has 1:1, so books are always taller.
   * This ensures rows align across the two columns.
   * 
   * FIX: Uses CSS-based calculation to avoid layout shift.
   * Measures book natural height without disturbing existing layout.
   */
  function syncMediaItemHeights(forceReset) {
    var bookItems = document.querySelectorAll('.media-item--book');
    var digitalItems = document.querySelectorAll('.media-item--digital');
    
    if (!bookItems.length || !digitalItems.length) return;
    
    var maxBookHeight = 0;
    
    if (forceReset) {
      // On resize: reset all heights and remeasure
      bookItems.forEach(function(item) { 
        item.style.removeProperty('height');
      });
      digitalItems.forEach(function(item) { 
        item.style.removeProperty('height');
      });
      
      // Force reflow after reset
      void document.body.offsetHeight;
      
      // Measure natural book heights
      bookItems.forEach(function(item) {
        var height = item.getBoundingClientRect().height;
        if (height > maxBookHeight) maxBookHeight = height;
      });
    } else {
      // Initial load: measure without disturbing layout
      // Use a hidden clone to measure natural height
      var firstBook = bookItems[0];
      if (!firstBook) return;
      
      // Check if heights are already set (from previous run or inline styles)
      var existingHeight = firstBook.style.height;
      if (existingHeight && existingHeight !== 'auto' && existingHeight !== '') {
        // Heights already set, just verify digital items have same height
        var parsedHeight = parseFloat(existingHeight);
        if (parsedHeight > 0) {
          digitalItems.forEach(function(item) {
            if (item.style.height !== existingHeight) {
              item.style.setProperty('height', existingHeight, 'important');
            }
          });
          return;
        }
      }
      
      // Measure natural book heights (no inline heights set yet)
      bookItems.forEach(function(item) {
        var height = item.getBoundingClientRect().height;
        if (height > maxBookHeight) maxBookHeight = height;
      });
    }
    
    // Apply the book height to all items immediately
    if (maxBookHeight > 0) {
      var heightPx = Math.ceil(maxBookHeight) + 'px';
      bookItems.forEach(function(item) { 
        item.style.setProperty('height', heightPx, 'important');
      });
      digitalItems.forEach(function(item) { 
        item.style.setProperty('height', heightPx, 'important');
      });
    }
  }

  // Run on load, after images, and resize
  ready(function() {
    init();
    
    // Run sync immediately (no delay) - synchronous to prevent layout shift
    syncMediaItemHeights(false);
    
    // Also run after all images in media section are loaded
    var mediaImages = document.querySelectorAll('.media-section img');
    var loadedCount = 0;
    var totalImages = mediaImages.length;
    
    if (totalImages > 0) {
      mediaImages.forEach(function(img) {
        if (img.complete) {
          loadedCount++;
          if (loadedCount === totalImages) syncMediaItemHeights(false);
        } else {
          img.addEventListener('load', function() {
            loadedCount++;
            if (loadedCount === totalImages) syncMediaItemHeights(false);
          });
        }
      });
    }
    
    // Final sync after window fully loads (fonts, images, etc.)
    window.addEventListener('load', function() {
      syncMediaItemHeights(false);
    });
    
    // Re-sync on window resize (debounced) - force reset on resize
    var resizeTimer;
    window.addEventListener('resize', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        syncMediaItemHeights(true); // Force reset on resize
      }, 150);
    });
  });
})();

