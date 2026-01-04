/**
 * Custom TOC Block - Frontend Script
 * Handles active section highlighting based on scroll position
 */
(function() {
    'use strict';

    function initCustomToc() {
        const tocs = document.querySelectorAll('.customToc--highlight');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            const links = toc.querySelectorAll('.customToc__link');
            if (!links.length) return;

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

            if (!anchors.length) return;

            // Scroll handler
            let ticking = false;
            
            function updateActiveLink() {
                ticking = false;
                const scrollY = window.scrollY || window.pageYOffset;
                const viewportHeight = window.innerHeight;
                const offset = 150; // Offset from top to trigger active state

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

            // Smooth scroll on click
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const anchorId = link.getAttribute('data-anchor');
                    const target = document.getElementById(anchorId);
                    if (target) {
                        const mastHeight = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--mastH')) || 77;
                        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - mastHeight - 20;
                        
                        window.scrollTo({
                            top: targetPosition,
                            behavior: 'smooth'
                        });

                        // Update URL hash without jumping
                        history.pushState(null, null, '#' + anchorId);
                    }
                });
            });

            window.addEventListener('scroll', onScroll, { passive: true });
            updateActiveLink(); // Initial state
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCustomToc);
    } else {
        initCustomToc();
    }
})();

