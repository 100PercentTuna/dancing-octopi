/**
 * Custom TOC Block - Frontend Script
 * Handles smooth scrolling and active section highlighting
 */
(function() {
    'use strict';

    function initCustomToc() {
        const tocs = document.querySelectorAll('.customToc');
        if (!tocs.length) return;

        tocs.forEach(function(toc) {
            // Prevent duplicate initialization
            if (toc.hasAttribute('data-toc-init')) return;
            toc.setAttribute('data-toc-init', 'true');

            const links = toc.querySelectorAll('.customToc__link');
            if (!links.length) return;

            const shouldHighlight = toc.classList.contains('customToc--highlight');

            // Build anchors array - find target elements by ID
            const anchors = [];
            links.forEach(function(link) {
                let anchorId = link.getAttribute('data-anchor');
                if (!anchorId) return;
                
                // Clean the anchor ID - remove # prefix, trim whitespace
                anchorId = anchorId.replace(/^#/, '').trim();
                
                // Find target element
                const target = document.getElementById(anchorId);
                if (target) {
                    anchors.push({ link: link, target: target, id: anchorId });
                }
            });

            // Scroll-based active highlighting
            if (shouldHighlight && anchors.length > 0) {
                let ticking = false;
                
                function updateActiveLink() {
                    ticking = false;
                    const offset = 200;
                    let activeIndex = 0;

                    // Find current section based on scroll position
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
                
                // Initial state
                setTimeout(updateActiveLink, 100);
            }

            // Click handler for smooth scroll
            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    let anchorId = link.getAttribute('data-anchor');
                    if (!anchorId) return;
                    
                    anchorId = anchorId.replace(/^#/, '').trim();
                    const target = document.getElementById(anchorId);
                    if (!target) return;
                    
                    // Calculate scroll position
                    const mastHeight = parseInt(
                        getComputedStyle(document.documentElement).getPropertyValue('--mastH')
                    ) || 77;
                    
                    // Use scrollY instead of pageYOffset (modern)
                    const currentScroll = window.scrollY || window.pageYOffset;
                    const targetRect = target.getBoundingClientRect();
                    const targetPosition = currentScroll + targetRect.top - mastHeight - 24;
                    
                    // Smooth scroll to target
                    window.scrollTo({
                        top: Math.max(0, targetPosition),
                        behavior: 'smooth'
                    });

                    // Update URL hash
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
