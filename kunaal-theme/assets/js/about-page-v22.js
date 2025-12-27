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

  var reduceMotion = false;
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
    var gsapOk = hasGSAP();
    if (gsapOk) {
      try { 
        window.gsap.registerPlugin(window.ScrollTrigger); 
      } catch (e) {
        console.warn('GSAP ScrollTrigger registration failed:', e);
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
  }

  // =============================================
  // PAGE LOAD - quiet editorial entrance
  // =============================================
  function initPageLoad(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    try {
      var tl = window.gsap.timeline({ defaults: { ease: 'power2.out' } });
      tl.from('.nav', { y: -10, opacity: 0, duration: 0.55 })
        .from('.hero-photo', { opacity: 0, duration: 0.6, stagger: 0.06 }, '<0.05')
        .from('.hero-text [data-reveal]', { y: 16, opacity: 0, duration: 0.55, stagger: 0.08 }, '<0.15')
        .from('#scrollIndicator', { y: 8, opacity: 0, duration: 0.35 }, '<0.25');
    } catch (e) {
      console.warn('Page load animation failed:', e);
    }
  }

  // =============================================
  // Scroll-triggered reveals (left/right/up)
  // =============================================
  function initScrollReveals(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    var els = document.querySelectorAll('[data-reveal]');
    for (var i = 0; i < els.length; i++) {
      (function (el) {
        var dir = el.getAttribute('data-reveal') || 'up';
        var x = 0, y = 14;
        if (dir === 'left') { x = -18; y = 0; }
        if (dir === 'right') { x = 18; y = 0; }
        if (dir === 'down') { x = 0; y = -14; }
        try {
          var st = window.gsap.from(el, {
            x: x,
            y: y,
            opacity: 0,
            duration: 0.55,
            ease: 'power2.out',
            scrollTrigger: {
              trigger: el,
              start: 'top 86%',
              toggleActions: 'play none none reverse',
              // Refresh on resize to prevent disappearing text
              refreshPriority: 1
            }
          });
          // Recalculate on window resize to fix disappearing text
          window.addEventListener('resize', function() {
            if (st && st.scrollTrigger) {
              st.scrollTrigger.refresh();
            }
          }, { passive: true });
        } catch (e) {
          console.warn('Scroll reveal failed for element:', e);
        }
      })(els[i]);
    }
  }

  // =============================================
  // Masked "portal band" parallax (fast)
  // =============================================
  function initPanoramaParallax(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    var bands = document.querySelectorAll('.panorama');
    for (var i = 0; i < bands.length; i++) {
      (function (band) {
        var img = band.querySelector('.panorama-img');
        if (!img) return;
        var speed = parseFloat(band.getAttribute('data-speed') || '1');
        if (!isFinite(speed)) speed = 1;
        // Parallax movement (safe): small yPercent range + oversized image in CSS
        // This prevents "running out of picture" while still feeling alive.
        var amp = 10 * speed; // percent of the IMAGE height
        try {
          window.gsap.fromTo(img,
            { yPercent: -amp },
            {
              yPercent: amp,
              ease: 'none',
              scrollTrigger: {
                trigger: band,
                start: 'top bottom',
                end: 'bottom top',
                scrub: 1
              }
            }
          );
        } catch (e) {
          console.warn('Panorama parallax failed:', e);
        }
      })(bands[i]);
    }
  }

  // =============================================
  // "Sticky scroll" / pinned scene (tasteful)
  // =============================================
  function initPinnedScenes(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    var pins = document.querySelectorAll('[data-pin="true"]');
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
        console.warn('Pinned scene failed:', e);
      }
    }
  }

  // =============================================
  // Giant background words drifting horizontally
  // =============================================
  function initMarqueeWords(gsapOk) {
    if (reduceMotion || !gsapOk) return;
    var words = document.querySelectorAll('[data-marquee]');
    for (var i = 0; i < words.length; i++) {
      (function (el) {
        var dir = (el.getAttribute('data-dir') || 'left').toLowerCase();
        var dist = (dir === 'right') ? 120 : -120;
        var container = el.parentElement || el;
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
          console.warn('Marquee word failed:', e);
        }
      })(words[i]);
    }
  }

  // =============================================
  // Numbers: quick "slot machine" feel, then settle
  // =============================================
  function initNumbers(gsapOk) {
    var section = document.querySelector('.numbers');
    if (!section) return;

    var numberEls = section.querySelectorAll('.number-value[data-target]');
    if (!numberEls.length) return;

    var infinityEl = section.querySelector('.infinity-value');
    var fired = false;

    function run() {
      if (fired) return;
      fired = true;

      for (var i = 0; i < numberEls.length; i++) {
        (function (el, idx) {
          var target = parseInt(el.getAttribute('data-target') || '0', 10);
          var suffix = el.getAttribute('data-suffix') || '';
          var spins = 0;
          var maxSpins = 12 + Math.floor(Math.random() * 6);

          setTimeout(function () {
            var spinInterval = setInterval(function () {
              el.textContent = Math.floor(Math.random() * target * 1.3).toLocaleString() + suffix;
              spins++;
              if (spins >= maxSpins) {
                clearInterval(spinInterval);

                var start = 0;
                var duration = 500;
                var t0 = performance.now();

                function tick(now) {
                  var p = Math.min(1, (now - t0) / duration);
                  var eased = 1 - Math.pow(1 - p, 3);
                  var val = Math.floor(start + (target - start) * eased);
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
          var io = new IntersectionObserver(function (entries) {
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
      var io = new IntersectionObserver(function (entries) {
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
    var host = document.getElementById('world-map');
    if (!host) return;

    // D3 and TopoJSON should already be loaded via WordPress enqueue
    if (!window.d3 || !window.topojson) {
      console.warn('D3.js or TopoJSON not loaded');
      return;
    }

    function draw() {
      // Get places data from WordPress localization
      var placesData = (window.kunaalAboutV22 && window.kunaalAboutV22.places) || {
        current: [],
        lived: [],
        visited: []
      };

      // Ensure all are arrays
      var current = Array.isArray(placesData.current) ? placesData.current : (placesData.current ? [placesData.current] : []);
      var lived = Array.isArray(placesData.lived) ? placesData.lived : (placesData.lived ? [placesData.lived] : []);
      var visited = Array.isArray(placesData.visited) ? placesData.visited : (placesData.visited ? [placesData.visited] : []);
      
      // Debug: log if no places data (helpful for troubleshooting)
      if (current.length === 0 && lived.length === 0 && visited.length === 0) {
        console.warn('About page map: No places data found. Check Customizer settings for Places section.');
      }

      // Country name mapping (expandable)
      var countryNames = {
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
      var idToIso = {
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

      var width = host.clientWidth || 900;
      var height = 460;

      host.innerHTML = '';
      var svg = window.d3.select(host)
        .append('svg')
        .attr('viewBox', '0 0 ' + width + ' ' + height)
        .attr('width', '100%')
        .attr('height', height);

      var projection = window.d3.geoEquirectangular()
        .scale(width / 6.5)
        .center([0, 20])
        .translate([width / 2, height / 2]);

      var path = window.d3.geoPath().projection(projection);

      var tooltip = document.getElementById('mapTooltip');

      window.d3.json('https://unpkg.com/world-atlas@2.0.2/countries-110m.json').then(function (world) {
        var countries = window.topojson.feature(world, world.objects.countries);

        svg.selectAll('path')
          .data(countries.features)
          .enter()
          .append('path')
          .attr('d', path)
          .attr('class', function (d) {
            var iso = idToIso[d.id];
            if (!iso) {
              return 'country';
            }
            // Check all arrays (case-insensitive for safety)
            var isoUpper = iso.toUpperCase();
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
            var iso = idToIso[d.id];
            if (iso && countryNames[iso]) {
              tooltip.textContent = countryNames[iso];
              tooltip.classList.add('visible');
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
          });

        // Add pin for current location (use first current location)
        if (current.length > 0) {
          // Coordinates for common locations (expandable)
          var coordsMap = {
            'SGP': [103.8198, 1.3521],
            'USA': [-95.7129, 37.0902],
            'IND': [78.9629, 20.5937],
            'GBR': [-3.4360, 55.3781],
            'THA': [100.9925, 15.8700],
            'PHL': [121.7740, 12.8797],
            'CHE': [8.2275, 46.8182],
            'CAN': [-106.3468, 56.1304],
            'MYS': [101.9758, 4.2105],
            'MDV': [73.2207, 3.2028],
            'BRA': [-51.9253, -14.2350],
            'MEX': [-102.5528, 23.6345],
            'ZAF': [22.9375, -30.5595]
          };

          var currentIso = current[0];
          var coords = coordsMap[currentIso] || coordsMap['SGP']; // Default to Singapore
          var pt = projection(coords);
          var px = pt[0], py = pt[1];
          var g = svg.append('g').attr('transform', 'translate(' + px + ',' + py + ')');

          // Get beacon color - orange (inverse of blue) in dark mode, blue in light mode
          function getBeaconColor() {
            var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            // Orange color (inverse/complement of blue) for dark mode
            if (isDark) {
              return '#FF6B35'; // Orange - inverse of blue
            } else {
              return '#1E5AFF'; // Blue for light mode
            }
          }
          
          var beaconColor = getBeaconColor();
          var beaconSize = 7; // Bigger beacon (was 5)
          
          function pulse() {
            g.append('circle')
              .attr('r', beaconSize)
              .attr('fill', 'none')
              .attr('stroke', beaconColor)
              .attr('stroke-width', 2)
              .attr('opacity', 0.7)
              .transition()
              .duration(1200)
              .attr('r', 22) // Bigger pulse (was 18)
              .attr('opacity', 0)
              .remove()
              .on('end', pulse);
          }
          pulse();

          g.append('circle').attr('r', beaconSize).attr('fill', beaconColor);
          g.append('circle').attr('r', 3).attr('fill', '#fff'); // Bigger inner dot
          
          // Listen for theme changes and update beacon color
          var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
              if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
                beaconColor = getBeaconColor();
                // Update existing circles
                g.selectAll('circle').attr('fill', function() {
                  var r = d3.select(this).attr('r');
                  if (parseFloat(r) === beaconSize) {
                    return beaconColor;
                  }
                  return '#fff';
                }).attr('stroke', function() {
                  var r = d3.select(this).attr('r');
                  if (parseFloat(r) > beaconSize) {
                    return beaconColor;
                  }
                  return null;
                });
              }
            });
          });
          observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
        }
      }).catch(function (err) {
        console.warn('World map data load failed:', err);
      });
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
        console.warn('World map: D3.js or TopoJSON not loaded after waiting.');
        return;
      }
      
      // Check for places data (wp_localize_script might need a moment)
      if (!window.kunaalAboutV22 || !window.kunaalAboutV22.places) {
        if (attempts < 50) { // Wait up to 5 seconds
          setTimeout(function() { tryDraw(attempts + 1); }, 100);
          return;
        }
        // After waiting, proceed with empty data
        console.warn('About page map: Places data not loaded after waiting. Proceeding with empty data.');
      }
      
      try {
        draw();
      } catch (e) {
        console.warn('World map draw failed:', e);
      }
    }
    
    // Start trying to draw
    tryDraw();
  }

  // =============================================
  // Progress bar (top) and scroll indicator hide
  // =============================================
  function initProgressBar(){
    var fill = document.getElementById('progressFill');
    if(!fill) return;
    var ticking = false;
    function update(){
      ticking = false;
      var doc = document.documentElement;
      var scrollTop = window.pageYOffset || doc.scrollTop || 0;
      var scrollHeight = (doc.scrollHeight || 0) - (doc.clientHeight || window.innerHeight || 1);
      var p = scrollHeight > 0 ? (scrollTop / scrollHeight) : 0;
      fill.style.width = (p * 100).toFixed(2) + '%';

      // Header compaction (drives CSS var(--p))
      var hp = Math.min(scrollTop / 120, 1);
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
    var toggle = document.getElementById('navToggle');
    var nav = document.getElementById('mainNav');
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
      var t = e.target;
      if(nav.contains(t) || toggle.contains(t)) return;
      close();
    });

    // Close after selecting a link (mobile)
    nav.addEventListener('click', function(e){
      var a = e.target && e.target.closest ? e.target.closest('a') : null;
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
    var tiles = Array.prototype.slice.call(document.querySelectorAll('.hero-photo'));
    if(!tiles.length) return;

    // Track hover so we don't constantly "steal" focus from the user's cursor.
    tiles.forEach(function(t){
      t.__isHover = false;
      t.addEventListener('mouseenter', function(){ t.__isHover = true; });
      t.addEventListener('mouseleave', function(){ t.__isHover = false; });
    });

    var idx = Math.floor(Math.random() * tiles.length);
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
      var candidates = tiles.map(function(_, i){ return i; }).filter(function(i){ return !tiles[i].__isHover; });
      if(!candidates.length) return;
      var currentPos = candidates.indexOf(idx);
      var next = candidates[(currentPos + 1) % candidates.length];
      setActive(next);
    }, 2600);
  }

  // =============================================
  // Rabbit hole capsules: wrap inner content so CSS animations don't fight GSAP
  // =============================================
  function initCapsuleLife(){
    var capsules = document.querySelectorAll('.capsules-cloud .capsule');
    if(!capsules.length) return;

    capsules.forEach(function(cap){
      var inner = cap.querySelector('.capsule-inner');

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
        var dot = document.createElement('span');
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
    var year = document.getElementById('footerYear');
    if(year) {
      year.textContent = new Date().getFullYear();
    }
    // Also check for #year (reference HTML uses this)
    var yearAlt = document.getElementById('year');
    if(yearAlt) {
      yearAlt.textContent = new Date().getFullYear();
    }
  }

  ready(init);
})();

