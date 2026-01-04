/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
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

            // Scroll-based active highlighting
            if (shouldHighlight && anchors.length > 0) {
                var ticking = false;
                
                function updateActiveLink() {
                    ticking = false;
                    var offset = 150;
                    var activeIndex = 0;

                    // Find current section based on scroll position
                    anchors.forEach(function(anchor, index) {
                        var rect = anchor.target.getBoundingClientRect();
                        if (rect.top <= offset) {
                            activeIndex = index;
                        }
                    });

                    // Update active states
                    links.forEach(function(link) {
                        link.classList.remove('is-active');
                    });
                    
                    if (anchors[activeIndex]) {
                        anchors[activeIndex].link.classList.add('is-active');
                    }
                }

                function onScroll() {
                    if (!ticking) {
                        ticking = true;
                        requestAnimationFrame(updateActiveLink);
                    }
                }

                window.addEventListener('scroll', onScroll, { passive: true });
                
                // Initial state
                setTimeout(updateActiveLink, 100);
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
                    
                    // Use scrollIntoView then adjust for header
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    
                    // Offset adjustment after scroll starts
                    setTimeout(function() {
                        window.scrollBy({ 
                            top: -(mastHeight + 32), 
                            behavior: 'instant' 
                        });
                    }, 100);

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

    // Initialize once when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomToc);
    } else {
        initCustomToc();
    }
})();
