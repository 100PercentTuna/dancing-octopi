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

            // Scroll-based active highlighting - must update live as user scrolls
            if (shouldHighlight && anchors.length > 0) {
                var lastActiveIndex = -1;
                
                function updateActiveLink() {
                    // Get masthead height for offset
                    var mastHeight = 77;
                    var mastHValue = getComputedStyle(document.documentElement).getPropertyValue('--mastH');
                    if (mastHValue) {
                        mastHeight = parseInt(mastHValue) || 77;
                    }
                    
                    var offset = mastHeight + 100; // Trigger point below header
                    var activeIndex = 0;

                    // Find current section based on scroll position
                    for (var i = 0; i < anchors.length; i++) {
                        var rect = anchors[i].target.getBoundingClientRect();
                        if (rect.top <= offset) {
                            activeIndex = i;
                        }
                    }

                    // Only update DOM if active index changed
                    if (activeIndex !== lastActiveIndex) {
                        lastActiveIndex = activeIndex;
                        
                        // Update active states
                        links.forEach(function(link) {
                            link.classList.remove('is-active');
                        });
                        
                        if (anchors[activeIndex]) {
                            anchors[activeIndex].link.classList.add('is-active');
                        }
                    }
                }

                // Use scroll event directly (no throttling for smooth updates)
                var scrollTimeout;
                function onScroll() {
                    // Clear any pending timeout
                    if (scrollTimeout) {
                        cancelAnimationFrame(scrollTimeout);
                    }
                    // Schedule update
                    scrollTimeout = requestAnimationFrame(updateActiveLink);
                }

                window.addEventListener('scroll', onScroll, { passive: true });
                
                // Also listen to resize (content might shift)
                window.addEventListener('resize', updateActiveLink, { passive: true });
                
                // Initial state - run immediately and after a short delay (for lazy content)
                updateActiveLink();
                setTimeout(updateActiveLink, 500);
                setTimeout(updateActiveLink, 1500);
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
                    var targetPosition = scrollTop + targetRect.top - mastHeight - 32;
                    
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
