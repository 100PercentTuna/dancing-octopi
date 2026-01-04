/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
 */
(function() {
    'use strict';

    function initCustomToc() {
        // Get ALL custom TOC blocks (not just highlight ones)
        const tocs = document.querySelectorAll('.customToc');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            const links = toc.querySelectorAll('.customToc__link');
            if (!links.length) return;

            const shouldHighlight = toc.classList.contains('customToc--highlight');

            // Get all anchor targets
            const anchors = [];
            links.forEach(function(link) {
                const anchorId = link.getAttribute('data-anchor');
                if (anchorId) {
                    const target = document.getElementById(anchorId);
                    if (target) {
                        anchors.push({ link: link, target: target });
                    }
                }
            });

            // Scroll-based active highlighting
            if (shouldHighlight && anchors.length > 0) {
                let ticking = false;
                
                function updateActiveLink() {
                    ticking = false;
                    const offset = 200; // Offset from top to trigger active state
                    let activeIndex = 0;

                    // Find the current section
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
                
                // Initial state after a small delay (let page settle)
                setTimeout(updateActiveLink, 100);
            }

            // Smooth scroll on click - works for ALL links
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const anchorId = link.getAttribute('data-anchor');
                    if (!anchorId) return;
                    
                    const target = document.getElementById(anchorId);
                    if (!target) {
                        // Try with hash prefix in case user added it
                        const altTarget = document.getElementById(anchorId.replace(/^#/, ''));
                        if (!altTarget) return;
                    }
                    
                    const actualTarget = target || document.getElementById(anchorId.replace(/^#/, ''));
                    if (!actualTarget) return;
                    
                    // Get masthead height for offset
                    const mastHeight = parseInt(
                        getComputedStyle(document.documentElement).getPropertyValue('--mastH')
                    ) || 77;
                    
                    const targetPosition = actualTarget.getBoundingClientRect().top + window.pageYOffset - mastHeight - 24;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    // Update URL hash without jumping
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
    
    // Also re-init after a delay in case of lazy loading
    window.addEventListener('load', function() {
        setTimeout(initCustomToc, 500);
    });
})();
