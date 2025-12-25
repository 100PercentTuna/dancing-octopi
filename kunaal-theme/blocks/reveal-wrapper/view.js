/**
 * Reveal Wrapper Block - Frontend Script
 * Handles scroll-triggered reveal animations
 */
(function() {
    'use strict';

    // Respect reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    document.addEventListener('DOMContentLoaded', function() {
        const revealElements = document.querySelectorAll('.reveal-wrapper');
        
        if (revealElements.length === 0) return;
        
        // If reduced motion, just show everything
        if (prefersReducedMotion) {
            revealElements.forEach(function(el) {
                el.classList.add('revealed');
            });
            return;
        }

        // Create intersection observer
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            root: null,
            rootMargin: '0px',
            threshold: 0.2
        });

        // Observe each element with custom threshold
        revealElements.forEach(function(el) {
            const threshold = parseFloat(el.dataset.revealThreshold) || 20;
            
            // Create individual observer with custom threshold
            const customObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        customObserver.unobserve(entry.target);
                    }
                });
            }, {
                root: null,
                rootMargin: '0px',
                threshold: threshold / 100
            });
            
            customObserver.observe(el);
        });
    });
})();

