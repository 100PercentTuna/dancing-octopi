/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
 * 
 * Uses IntersectionObserver as the SINGLE source of truth for scroll detection.
 * No scroll event fallback - avoids flickering from competing systems.
 */
(function() {
    'use strict';

    // Get masthead height from CSS variable
    function getMastHeight() {
        var mastHeight = 77;
        var mastHValue = getComputedStyle(document.documentElement).getPropertyValue('--mastH');
        if (mastHValue) {
            mastHeight = parseInt(mastHValue) || 77;
        }
        return mastHeight;
    }

    function initCustomToc() {
        var tocs = document.querySelectorAll('.customToc');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            // Prevent duplicate initialization
            if (toc.hasAttribute('data-toc-init')) return;
            toc.setAttribute('data-toc-init', 'true');

            var links = toc.querySelectorAll('.customToc__link');
            if (!links.length) return;

            var shouldHighlight = toc.classList.contains('customToc--highlight');

            // Build anchors array - find target elements by ID
            var anchors = [];
            links.forEach(function(link, index) {
                var anchorId = link.getAttribute('data-anchor');
                if (!anchorId) return;
                
                // Clean the anchor ID - remove # prefix, trim whitespace
                anchorId = anchorId.replace(/^#/, '').trim();
                
                // Find target element
                var target = document.getElementById(anchorId);
                if (target) {
                    // Store index on link for quick lookup
                    link.setAttribute('data-toc-index', index);
                    anchors.push({ link: link, target: target, id: anchorId, index: index });
                }
            });

            // =========================================
            // SCROLL-BASED ACTIVE HIGHLIGHTING
            // Single IntersectionObserver - no fallback
            // =========================================
            if (shouldHighlight && anchors.length > 0) {
                var currentActiveIndex = -1;
                
                function setActiveIndex(index) {
                    if (index === currentActiveIndex) return;
                    currentActiveIndex = index;
                    
                    // Remove active class from all links
                    links.forEach(function(link) {
                        link.classList.remove('is-active');
                    });
                    
                    // Add active class to current link
                    if (anchors[index]) {
                        anchors[index].link.classList.add('is-active');
                    }
                }
                
                // Track which sections are currently in the "active zone"
                var sectionsInView = {};
                
                function updateActiveSection() {
                    // Find the section with the smallest positive offset from top of active zone
                    // This means the section that is closest to (but past) the trigger line
                    var mastHeight = getMastHeight();
                    var triggerLine = mastHeight + 50; // 50px below header
                    
                    var bestIndex = 0;
                    var bestDistance = Infinity;
                    
                    for (var i = 0; i < anchors.length; i++) {
                        var rect = anchors[i].target.getBoundingClientRect();
                        // Distance from trigger line to section top
                        // Positive means section is below trigger, negative means above
                        var distance = rect.top - triggerLine;
                        
                        // If section top is above trigger line (we've scrolled past it)
                        if (distance <= 0) {
                            // This section is a candidate - it's the most recent one we passed
                            bestIndex = i;
                        }
                    }
                    
                    setActiveIndex(bestIndex);
                }
                
                // IntersectionObserver for detecting when sections enter/exit viewport
                var mastHeight = getMastHeight();
                var observerOptions = {
                    root: null,
                    // Active zone: from masthead to 40% down the viewport
                    rootMargin: '-' + mastHeight + 'px 0px -60% 0px',
                    threshold: [0, 0.25, 0.5, 0.75, 1]
                };
                
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        var anchorId = entry.target.id;
                        
                        if (entry.isIntersecting) {
                            sectionsInView[anchorId] = true;
                        } else {
                            delete sectionsInView[anchorId];
                        }
                    });
                    
                    // After processing all entries, update the active section
                    updateActiveSection();
                }, observerOptions);
                
                // Observe all anchor targets
                anchors.forEach(function(anchor) {
                    observer.observe(anchor.target);
                });
                
                // Initial state - set first as active, then update based on scroll position
                setActiveIndex(0);
                
                // Delayed initial check for content that loads late
                setTimeout(updateActiveSection, 300);
                setTimeout(updateActiveSection, 1000);
            }

            // =========================================
            // MOBILE COLLAPSE/EXPAND
            // =========================================
            function setupMobileToggle() {
                var title = toc.querySelector('.customToc__title');
                if (!title) return;
                
                // Only setup on mobile/tablet
                var isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    // Add collapsed class by default on mobile
                    toc.classList.add('is-collapsed');
                    toc.classList.remove('is-expanded');
                    
                    // Make title clickable
                    title.setAttribute('role', 'button');
                    title.setAttribute('aria-expanded', 'false');
                    title.setAttribute('tabindex', '0');
                    
                    function toggleExpanded(e) {
                        e.preventDefault();
                        var isExpanded = toc.classList.contains('is-expanded');
                        
                        if (isExpanded) {
                            toc.classList.remove('is-expanded');
                            toc.classList.add('is-collapsed');
                            title.setAttribute('aria-expanded', 'false');
                        } else {
                            toc.classList.add('is-expanded');
                            toc.classList.remove('is-collapsed');
                            title.setAttribute('aria-expanded', 'true');
                        }
                    }
                    
                    // Handle click and keyboard
                    title.addEventListener('click', toggleExpanded);
                    title.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            toggleExpanded(e);
                        }
                    });
                } else {
                    // Desktop - ensure expanded
                    toc.classList.remove('is-collapsed');
                    toc.classList.remove('is-expanded');
                    title.removeAttribute('role');
                    title.removeAttribute('aria-expanded');
                    title.removeAttribute('tabindex');
                }
            }
            
            setupMobileToggle();
            
            // Re-check on resize (debounced)
            var resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    // Remove old state classes
                    toc.classList.remove('is-collapsed', 'is-expanded');
                    setupMobileToggle();
                }, 150);
            });

            // =========================================
            // CLICK HANDLER FOR SMOOTH SCROLL
            // =========================================
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    var anchorId = link.getAttribute('data-anchor');
                    if (!anchorId) return;
                    
                    anchorId = anchorId.replace(/^#/, '').trim();
                    var target = document.getElementById(anchorId);
                    if (!target) return;
                    
                    // Get masthead height
                    var mastHeight = getMastHeight();
                    
                    // Calculate exact scroll position
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    var targetRect = target.getBoundingClientRect();
                    var targetPosition = scrollTop + targetRect.top - mastHeight - 24;
                    
                    // Smooth scroll to target
                    window.scrollTo({
                        top: Math.max(0, targetPosition),
                        behavior: 'smooth'
                    });

                    // Update URL hash without jumping
                    if (history.pushState) {
                        history.pushState(null, null, '#' + anchorId);
                    }
                    
                    // Update active state immediately on click
                    if (toc.classList.contains('customToc--highlight')) {
                        links.forEach(function(l) { l.classList.remove('is-active'); });
                        link.classList.add('is-active');
                    }
                    
                    // On mobile, collapse after selection
                    if (window.innerWidth <= 768) {
                        toc.classList.remove('is-expanded');
                        toc.classList.add('is-collapsed');
                        var title = toc.querySelector('.customToc__title');
                        if (title) title.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomToc);
    } else {
        initCustomToc();
    }
    
    // Re-initialize after full page load (handles lazy-loaded content)
    window.addEventListener('load', function() {
        setTimeout(initCustomToc, 100);
    });
})();
