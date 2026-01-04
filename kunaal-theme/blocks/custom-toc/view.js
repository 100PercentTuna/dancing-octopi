/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
 * Uses IntersectionObserver for reliable scroll detection
 */
(function() {
    'use strict';

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
            links.forEach(function(link) {
                var anchorId = link.getAttribute('data-anchor');
                if (!anchorId) return;
                
                // Clean the anchor ID - remove # prefix, trim whitespace
                anchorId = anchorId.replace(/^#/, '').trim();
                
                // Find target element
                var target = document.getElementById(anchorId);
                if (target) {
                    anchors.push({ link: link, target: target, id: anchorId });
                }
            });

            // Scroll-based active highlighting using IntersectionObserver
            if (shouldHighlight && anchors.length > 0) {
                // Track which sections are currently visible
                var visibleSections = new Set();
                
                // Get masthead height for offset
                function getMastHeight() {
                    var mastHeight = 77;
                    var mastHValue = getComputedStyle(document.documentElement).getPropertyValue('--mastH');
                    if (mastHValue) {
                        mastHeight = parseInt(mastHValue) || 77;
                    }
                    return mastHeight;
                }
                
                // Update active link based on visible sections
                function updateActiveFromVisible() {
                    if (visibleSections.size === 0) {
                        // No sections visible - find the closest one above viewport
                        var closestAbove = -1;
                        var closestDistance = Infinity;
                        
                        for (var i = 0; i < anchors.length; i++) {
                            var rect = anchors[i].target.getBoundingClientRect();
                            if (rect.bottom < 0) {
                                // Section is above viewport
                                var dist = Math.abs(rect.bottom);
                                if (dist < closestDistance) {
                                    closestDistance = dist;
                                    closestAbove = i;
                                }
                            }
                        }
                        
                        if (closestAbove >= 0) {
                            setActiveIndex(closestAbove);
                        }
                        return;
                    }
                    
                    // Find the topmost visible section
                    var topmostIndex = -1;
                    var topmostTop = Infinity;
                    
                    visibleSections.forEach(function(anchorId) {
                        for (var i = 0; i < anchors.length; i++) {
                            if (anchors[i].id === anchorId) {
                                var rect = anchors[i].target.getBoundingClientRect();
                                if (rect.top < topmostTop) {
                                    topmostTop = rect.top;
                                    topmostIndex = i;
                                }
                                break;
                            }
                        }
                    });
                    
                    if (topmostIndex >= 0) {
                        setActiveIndex(topmostIndex);
                    }
                }
                
                var currentActiveIndex = -1;
                
                function setActiveIndex(index) {
                    if (index === currentActiveIndex) return;
                    currentActiveIndex = index;
                    
                    links.forEach(function(link) {
                        link.classList.remove('is-active');
                    });
                    
                    if (anchors[index]) {
                        anchors[index].link.classList.add('is-active');
                    }
                }
                
                // Create IntersectionObserver
                var observerOptions = {
                    root: null,
                    rootMargin: '-' + getMastHeight() + 'px 0px -40% 0px',
                    threshold: [0, 0.1, 0.5, 1]
                };
                
                var observer = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        var anchorId = entry.target.id;
                        
                        if (entry.isIntersecting) {
                            visibleSections.add(anchorId);
                        } else {
                            visibleSections.delete(anchorId);
                        }
                    });
                    
                    updateActiveFromVisible();
                }, observerOptions);
                
                // Observe all anchor targets
                anchors.forEach(function(anchor) {
                    observer.observe(anchor.target);
                });
                
                // Fallback scroll-based detection for edge cases
                var scrollTimeout;
                function onScroll() {
                    if (scrollTimeout) {
                        cancelAnimationFrame(scrollTimeout);
                    }
                    scrollTimeout = requestAnimationFrame(function() {
                        // Simple scroll position based detection as fallback
                        var mastHeight = getMastHeight();
                        var offset = mastHeight + 80;
                        var activeIndex = 0;

                        for (var i = 0; i < anchors.length; i++) {
                            var rect = anchors[i].target.getBoundingClientRect();
                            if (rect.top <= offset) {
                                activeIndex = i;
                            }
                        }

                        setActiveIndex(activeIndex);
                    });
                }

                window.addEventListener('scroll', onScroll, { passive: true });
                window.addEventListener('resize', function() {
                    // Recreate observer with new rootMargin on resize
                    observer.disconnect();
                    observerOptions.rootMargin = '-' + getMastHeight() + 'px 0px -40% 0px';
                    observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            var anchorId = entry.target.id;
                            if (entry.isIntersecting) {
                                visibleSections.add(anchorId);
                            } else {
                                visibleSections.delete(anchorId);
                            }
                        });
                        updateActiveFromVisible();
                    }, observerOptions);
                    anchors.forEach(function(anchor) {
                        observer.observe(anchor.target);
                    });
                }, { passive: true });
                
                // Initial state - trigger scroll handler after delays
                setTimeout(onScroll, 100);
                setTimeout(onScroll, 500);
                setTimeout(onScroll, 1500);
            }

            // Click handler for smooth scroll
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    var anchorId = link.getAttribute('data-anchor');
                    if (!anchorId) return;
                    
                    anchorId = anchorId.replace(/^#/, '').trim();
                    var target = document.getElementById(anchorId);
                    if (!target) return;
                    
                    // Get masthead height
                    var mastHeight = 77;
                    var mastHValue = getComputedStyle(document.documentElement).getPropertyValue('--mastH');
                    if (mastHValue) {
                        mastHeight = parseInt(mastHValue) || 77;
                    }
                    
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
                    
                    // Update active state immediately
                    if (toc.classList.contains('customToc--highlight')) {
                        links.forEach(function(l) { l.classList.remove('is-active'); });
                        link.classList.add('is-active');
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
