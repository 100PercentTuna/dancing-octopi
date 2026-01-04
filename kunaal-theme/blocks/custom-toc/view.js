/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
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
                    
                    if (!title._toggleHandler) {
                        title._toggleHandler = function(e) {
                            e.preventDefault();
                            var isExpanded = toc.classList.contains('is-expanded');
                            if (isExpanded) {
                                toc.classList.remove('is-expanded');
                                title.setAttribute('aria-expanded', 'false');
                            } else {
                                toc.classList.add('is-expanded');
                                title.setAttribute('aria-expanded', 'true');
                            }
                        };
                        title.addEventListener('click', title._toggleHandler);
                        title.addEventListener('keydown', function(e) {
                            if (e.key === 'Enter' || e.key === ' ') {
                                title._toggleHandler(e);
                            }
                        });
                    }
                } else {
                    toc.classList.remove('is-expanded');
                    title.removeAttribute('role');
                    title.removeAttribute('aria-expanded');
                    title.removeAttribute('tabindex');
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

            // CLICK HANDLER - Smooth scroll to target
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
                    
                    // Calculate scroll position
                    var mastHeight = getMastHeight();
                    var targetRect = target.getBoundingClientRect();
                    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    var targetPosition = scrollTop + targetRect.top - mastHeight - 24;
                    
                    // Update active state immediately
                    for (var i = 0; i < anchors.length; i++) {
                        if (anchors[i].id === anchorId) {
                            setActiveIndex(i);
                            break;
                        }
                    }
                    
                    // Scroll to target - use scrollTo for reliability
                    try {
                        window.scrollTo({
                            top: Math.max(0, targetPosition),
                            behavior: 'smooth'
                        });
                    } catch (err) {
                        // Fallback for older browsers
                        window.scrollTo(0, Math.max(0, targetPosition));
                    }

                    // Update URL hash
                    if (history.pushState) {
                        history.pushState(null, null, '#' + anchorId);
                    }
                    
                    // Release scroll lock
                    setTimeout(function() {
                        isScrollingToTarget = false;
                    }, 1000);
                    
                    // Collapse on mobile
                    if (window.innerWidth <= 768) {
                        toc.classList.remove('is-expanded');
                        var titleEl = toc.querySelector('.customToc__title');
                        if (titleEl) titleEl.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        });
    }

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomToc);
    } else {
        initCustomToc();
    }
    
    window.addEventListener('load', function() {
        setTimeout(initCustomToc, 100);
    });
})();
