/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
 * Safari iOS compatible
 */
(function() {
    'use strict';

    function getMastHeight() {
        var mastHeight = 77;
        var mastHValue = getComputedStyle(document.documentElement).getPropertyValue('--mastH');
        if (mastHValue) {
            mastHeight = parseInt(mastHValue) || 77;
        }
        return mastHeight;
    }

    // Resolve the actual scroll container (some browsers scroll BODY, not window/documentElement)
    function getScroller() {
        var doc = document.documentElement;
        var body = document.body;
        if (body && (body.scrollHeight - body.clientHeight) > 20) {
            return body;
        }
        if (doc && (doc.scrollHeight - doc.clientHeight) > 20) {
            return doc;
        }
        return document.scrollingElement || window;
    }

    function initCustomToc() {
        var tocs = document.querySelectorAll('.customToc');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            if (toc.hasAttribute('data-toc-init')) return;
            toc.setAttribute('data-toc-init', 'true');

            var links = toc.querySelectorAll('.customToc__link');
            if (!links.length) return;

            var shouldHighlight = toc.classList.contains('customToc--highlight');

            // Build anchors array
            var anchors = [];
            links.forEach(function(link, index) {
                var anchorId = link.getAttribute('data-anchor');
                if (!anchorId) return;
                
                anchorId = anchorId.replace(/^#/, '').trim();
                var target = document.getElementById(anchorId);
                if (target) {
                    link.setAttribute('data-toc-index', index);
                    anchors.push({ link: link, target: target, id: anchorId, index: index });
                }
            });

            // Active state management - at TOC scope
            var currentActiveIndex = -1;
            var isScrollingToTarget = false;
            
            function setActiveIndex(index) {
                if (index === currentActiveIndex) return;
                currentActiveIndex = index;
                
                links.forEach(function(link) {
                    link.classList.remove('is-active');
                    var parentItem = link.closest('.customToc__item');
                    if (parentItem) {
                        parentItem.classList.remove('has-active');
                    }
                });
                
                if (anchors[index]) {
                    anchors[index].link.classList.add('is-active');
                    var parentItem = anchors[index].link.closest('.customToc__item');
                    if (parentItem) {
                        parentItem.classList.add('has-active');
                    }
                }
            }

            // Scroll-based highlighting
            if (shouldHighlight && anchors.length > 0) {
                function updateActiveSection() {
                    if (isScrollingToTarget) return;
                    
                    var mastHeight = getMastHeight();
                    var triggerLine = mastHeight + 50;
                    var bestIndex = 0;
                    
                    for (var i = 0; i < anchors.length; i++) {
                        var rect = anchors[i].target.getBoundingClientRect();
                        if (rect.top - triggerLine <= 0) {
                            bestIndex = i;
                        }
                    }
                    
                    setActiveIndex(bestIndex);
                }
                
                var mastHeight = getMastHeight();
                var observer = new IntersectionObserver(function(entries) {
                    if (isScrollingToTarget) return;
                    updateActiveSection();
                }, {
                    root: null,
                    rootMargin: '-' + mastHeight + 'px 0px -60% 0px',
                    threshold: [0, 0.25, 0.5, 0.75, 1]
                });
                
                anchors.forEach(function(anchor) {
                    observer.observe(anchor.target);
                });
                
                var scrollTimeout;
                window.addEventListener('scroll', function() {
                    if (isScrollingToTarget) return;
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(updateActiveSection, 50);
                }, { passive: true });
                
                setActiveIndex(0);
                setTimeout(updateActiveSection, 300);
            }

            // Mobile collapse/expand
            function setupMobileToggle() {
                var title = toc.querySelector('.customToc__title');
                if (!title) return;
                
                var isMobile = window.innerWidth <= 768;
                
                if (isMobile) {
                    toc.classList.remove('is-expanded');
                    title.setAttribute('role', 'button');
                    title.setAttribute('aria-expanded', 'false');
                    title.setAttribute('tabindex', '0');
                    
                    // Track touch time for ghost click prevention (following nav toggle pattern)
                    if (!title._lastTouchTime) {
                        title._lastTouchTime = 0;
                    }
                    
                    // Toggle handler function
                    var toggleHandler = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var isExpanded = toc.classList.contains('is-expanded');
                        if (isExpanded) {
                            toc.classList.remove('is-expanded');
                            title.setAttribute('aria-expanded', 'false');
                        } else {
                            toc.classList.add('is-expanded');
                            title.setAttribute('aria-expanded', 'true');
                        }
                    };
                    
                    // Remove existing handlers if they exist (idempotent)
                    if (title._toggleClickHandler) {
                        title.removeEventListener('click', title._toggleClickHandler);
                    }
                    if (title._toggleTouchStartHandler) {
                        title.removeEventListener('touchstart', title._toggleTouchStartHandler);
                    }
                    if (title._toggleTouchEndHandler) {
                        title.removeEventListener('touchend', title._toggleTouchEndHandler);
                    }
                    if (title._toggleKeydownHandler) {
                        title.removeEventListener('keydown', title._toggleKeydownHandler);
                    }
                    
                    // Touch start: record touch time
                    title._toggleTouchStartHandler = function(e) {
                        title._lastTouchTime = Date.now();
                    };
                    
                    // Touch end: handle toggle
                    title._toggleTouchEndHandler = function(e) {
                        e.preventDefault();
                        toggleHandler(e);
                    };
                    
                    // Click: handle toggle with ghost click prevention
                    title._toggleClickHandler = function(e) {
                        // Ghost click prevention: skip if this click came right after a touch
                        // iOS fires touchend, then click ~300ms later
                        if (Date.now() - title._lastTouchTime < 500) {
                            e.preventDefault();
                            e.stopPropagation();
                            return; // Skip - already handled by touchend
                        }
                        toggleHandler(e);
                    };
                    
                    // Keyboard: handle Enter and Space
                    title._toggleKeydownHandler = function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            toggleHandler(e);
                        }
                    };
                    
                    // Attach all handlers
                    title.addEventListener('touchstart', title._toggleTouchStartHandler, { passive: true });
                    title.addEventListener('touchend', title._toggleTouchEndHandler);
                    title.addEventListener('click', title._toggleClickHandler);
                    title.addEventListener('keydown', title._toggleKeydownHandler);
                } else {
                    toc.classList.remove('is-expanded');
                    title.removeAttribute('role');
                    title.removeAttribute('aria-expanded');
                    title.removeAttribute('tabindex');
                    
                    // Remove handlers when not mobile
                    if (title._toggleClickHandler) {
                        title.removeEventListener('click', title._toggleClickHandler);
                        title._toggleClickHandler = null;
                    }
                    if (title._toggleTouchStartHandler) {
                        title.removeEventListener('touchstart', title._toggleTouchStartHandler);
                        title._toggleTouchStartHandler = null;
                    }
                    if (title._toggleTouchEndHandler) {
                        title.removeEventListener('touchend', title._toggleTouchEndHandler);
                        title._toggleTouchEndHandler = null;
                    }
                    if (title._toggleKeydownHandler) {
                        title.removeEventListener('keydown', title._toggleKeydownHandler);
                        title._toggleKeydownHandler = null;
                    }
                }
            }
            
            setupMobileToggle();
            
            var resizeTimeout;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    toc.classList.remove('is-expanded');
                    setupMobileToggle();
                }, 150);
            });

            // CLICK HANDLER - iOS Safari compatible smooth scroll
            // Uses multiple approaches for maximum compatibility
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var anchorId = link.getAttribute('data-anchor');
                    if (!anchorId) return;
                    
                    anchorId = anchorId.replace(/^#/, '').trim();
                    var target = document.getElementById(anchorId);
                    if (!target) return;
                    
                    // Set scroll lock
                    isScrollingToTarget = true;
                    
                    // Update active state immediately
                    for (var i = 0; i < anchors.length; i++) {
                        if (anchors[i].id === anchorId) {
                            setActiveIndex(i);
                            break;
                        }
                    }
                    
                    // Calculate offset for masthead
                    var mastHeight = getMastHeight();
                    // Provide additional breathing room so target text isn't tucked under the mast.
                    // (Users reported first line still clipping under mast on some viewports.)
                    var offset = mastHeight + 96;
                    
                    // Reliable scroll method: Use scrollIntoView first, then adjust for masthead offset
                    // This works consistently across all browsers and scroll containers
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // After scrollIntoView, adjust for masthead offset
                    // Use requestAnimationFrame to ensure scrollIntoView has started
                    requestAnimationFrame(function() {
                        requestAnimationFrame(function() {
                            var currentScroll = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
                            var targetRect = target.getBoundingClientRect();
                            var targetPosition = Math.max(0, currentScroll + targetRect.top - offset);
                            
                            // Apply the offset adjustment
                            window.scrollTo({ top: targetPosition, behavior: 'smooth' });
                        });
                    });

                    // Update URL hash
                    if (history.pushState) {
                        history.pushState(null, null, '#' + anchorId);
                    }
                    
                    // Release scroll lock after animation completes.
                    // Scale lock duration lightly with distance to avoid observer fighting the click-scroll.
                    var distance = Math.abs(targetPosition - scrollTop);
                    var lockMs = Math.min(1600, Math.max(900, Math.round(distance * 0.6)));
                    setTimeout(function() { isScrollingToTarget = false; }, lockMs);
                    
                    // Collapse on mobile
                    if (window.innerWidth <= 768 || window.innerHeight <= 500) {
                        toc.classList.remove('is-expanded');
                        var titleEl = toc.querySelector('.customToc__title');
                        if (titleEl) titleEl.setAttribute('aria-expanded', 'false');
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
    
    // Also init after full load (for dynamically added content)
    window.addEventListener('load', function() {
        setTimeout(initCustomToc, 100);
    });
})();
