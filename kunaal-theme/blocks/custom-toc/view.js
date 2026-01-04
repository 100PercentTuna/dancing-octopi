/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
 */
(function() {
    'use strict';

    function initCustomToc() {
        // Get ALL custom TOC blocks
        const tocs = document.querySelectorAll('.customToc');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            const links = toc.querySelectorAll('.customToc__link');
            if (!links.length) return;

            const shouldHighlight = toc.classList.contains('customToc--highlight');

            // Get all anchor targets - handle various ID formats
            const anchors = [];
            links.forEach(function(link) {
                let anchorId = link.getAttribute('data-anchor');
                if (!anchorId) return;
                
                // Clean the anchor ID - remove # prefix if present, trim whitespace
                anchorId = anchorId.replace(/^#/, '').trim();
                
                // Try to find the target element
                let target = document.getElementById(anchorId);
                
                // If not found, try querySelector with the ID (handles edge cases)
                if (!target) {
                    try {
                        target = document.querySelector('#' + CSS.escape(anchorId));
                    } catch (e) {
                        // CSS.escape might not be available in all browsers
                        target = null;
                    }
                }
                
                if (target) {
                    anchors.push({ link: link, target: target, id: anchorId });
                }
            });

            // Scroll-based active highlighting
            if (shouldHighlight && anchors.length > 0) {
                let ticking = false;
                
                function updateActiveLink() {
                    ticking = false;
                    const offset = 200; // Offset from top to trigger active state
                    let activeIndex = 0;

                    // Find the current section based on scroll position
                    anchors.forEach(function(anchor, index) {
                        const rect = anchor.target.getBoundingClientRect();
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
                
                // Initial state after page settles
                setTimeout(updateActiveLink, 100);
            }

            // Smooth scroll on click
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    let anchorId = link.getAttribute('data-anchor');
                    if (!anchorId) return;
                    
                    // Clean the anchor ID
                    anchorId = anchorId.replace(/^#/, '').trim();
                    
                    // Find target
                    let target = document.getElementById(anchorId);
                    if (!target) {
                        try {
                            target = document.querySelector('#' + CSS.escape(anchorId));
                        } catch (e) {
                            target = null;
                        }
                    }
                    
                    if (!target) return;
                    
                    // Get masthead height for offset
                    const mastHeight = parseInt(
                        getComputedStyle(document.documentElement).getPropertyValue('--mastH')
                    ) || 77;
                    
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - mastHeight - 24;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    // Update URL hash
                    if (history.pushState) {
                        history.pushState(null, null, '#' + anchorId);
                    }
                    
                    // Set active state immediately on click
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
    
    // Re-init after full page load (for lazy content)
    window.addEventListener('load', function() {
        setTimeout(initCustomToc, 300);
    });
})();
