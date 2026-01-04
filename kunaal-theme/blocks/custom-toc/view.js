/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
 * 
 * Uses IntersectionObserver as the SINGLE source of truth for scroll detection.
 * Scroll lock prevents flickering during smooth scroll navigation.
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
            // Single IntersectionObserver with scroll lock
            // =========================================
            if (shouldHighlight && anchors.length > 0) {
                var currentActiveIndex = -1;
                
                // SCROLL LOCK: Prevents observer updates during smooth scroll
                // This fixes the "jumps back up by one" issue
                var isScrollingToTarget = false;
                
                function setActiveIndex(index) {
                    if (index === currentActiveIndex) return;
                    currentActiveIndex = index;
                    
                    // Remove active class from all links and items
                    links.forEach(function(link) {
                        link.classList.remove('is-active');
                        // Also update parent item for indicator positioning
                        var parentItem = link.closest('.customToc__item');
                        if (parentItem) {
                            parentItem.classList.remove('has-active');
                        }
                    });
                    
                    // Add active class to current link and its parent item
                    if (anchors[index]) {
                        anchors[index].link.classList.add('is-active');
                        var parentItem = anchors[index].link.closest('.customToc__item');
                        if (parentItem) {
                            parentItem.classList.add('has-active');
                        }
                    }
                }
                
                function updateActiveSection() {
                    // Skip updates during programmatic scroll
                    if (isScrollingToTarget) return;
                    
                    // Find the section whose top has most recently passed the trigger line
                    var mastHeight = getMastHeight();
                    var triggerLine = mastHeight + 50; // 50px below header
                    
                    var bestIndex = 0;
                    
                    for (var i = 0; i < anchors.length; i++) {
                        var rect = anchors[i].target.getBoundingClientRect();
                        var distance = rect.top - triggerLine;
                        
                        // If section top is above trigger line (we've scrolled past it)
                        if (distance <= 0) {
                            bestIndex = i;
                        }
                    }
                    
                    setActiveIndex(bestIndex);
                }
                
                // IntersectionObserver for detecting when sections enter/exit viewport
                var mastHeight = getMastHeight();
                var observerOptions = {
                    root: null,
                    rootMargin: '-' + mastHeight + 'px 0px -60% 0px',
                    threshold: [0, 0.25, 0.5, 0.75, 1]
                };
                
                var observer = new IntersectionObserver(function(entries) {
                    // Skip updates during programmatic scroll
                    if (isScrollingToTarget) return;
                    
                    // After processing entries, update the active section
                    updateActiveSection();
                }, observerOptions);
                
                // Observe all anchor targets
                anchors.forEach(function(anchor) {
                    observer.observe(anchor.target);
                });
                
                // Initial state
                setActiveIndex(0);
                setTimeout(updateActiveSection, 300);
                setTimeout(updateActiveSection, 1000);
            }

            // =========================================
            // MOBILE COLLAPSE/EXPAND
            // =========================================
            function setupMobileToggle() {
                var title = toc.querySelector('.customToc__title');
                if (!title) return;
                
                var isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    // On mobile, start collapsed (list hidden by default via CSS)
                    toc.classList.remove('is-expanded');
                    
                    title.setAttribute('role', 'button');
                    title.setAttribute('aria-expanded', 'false');
                    title.setAttribute('tabindex', '0');
                    
                    function toggleExpanded(e) {
                        e.preventDefault();
                        var isExpanded = toc.classList.contains('is-expanded');
                        
                        if (isExpanded) {
                            toc.classList.remove('is-expanded');
                            title.setAttribute('aria-expanded', 'false');
                        } else {
                            toc.classList.add('is-expanded');
                            title.setAttribute('aria-expanded', 'true');
                        }
                    }
                    
                    // Prevent duplicate listeners
                    title.removeEventListener('click', title._toggleHandler);
                    title._toggleHandler = toggleExpanded;
                    title.addEventListener('click', toggleExpanded);
                    
                    title.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            toggleExpanded(e);
                        }
                    });
                } else {
                    // Desktop - remove mobile attributes
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
                    toc.classList.remove('is-expanded');
                    setupMobileToggle();
                }, 150);
            });

            // =========================================
            // CLICK HANDLER FOR SMOOTH SCROLL
            // =========================================
            links.forEach(function(link, linkIndex) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    var anchorId = link.getAttribute('data-anchor');
                    if (!anchorId) return;
                    
                    anchorId = anchorId.replace(/^#/, '').trim();
                    var target = document.getElementById(anchorId);
                    if (!target) return;
                    
                    // SCROLL LOCK: Prevent observer from overriding during smooth scroll
                    isScrollingToTarget = true;
                    
                    // Get masthead height
                    var mastHeight = getMastHeight();
                    
                    // Calculate exact scroll position
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    var targetRect = target.getBoundingClientRect();
                    var targetPosition = scrollTop + targetRect.top - mastHeight - 24;
                    
                    // Update active state IMMEDIATELY (before scroll starts)
                    if (toc.classList.contains('customToc--highlight')) {
                        // Find the anchor index for this link
                        for (var i = 0; i < anchors.length; i++) {
                            if (anchors[i].link === link) {
                                setActiveIndex(i);
                                break;
                            }
                        }
                    }
                    
                    // Smooth scroll to target
                    window.scrollTo({
                        top: Math.max(0, targetPosition),
                        behavior: 'smooth'
                    });

                    // Update URL hash without jumping
                    if (history.pushState) {
                        history.pushState(null, null, '#' + anchorId);
                    }
                    
                    // Release scroll lock after animation completes (800ms is enough for most scrolls)
                    setTimeout(function() {
                        isScrollingToTarget = false;
                    }, 800);
                    
                    // On mobile, collapse after selection
                    if (window.innerWidth <= 768) {
                        toc.classList.remove('is-expanded');
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
