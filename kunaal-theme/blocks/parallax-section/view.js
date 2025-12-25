/**
 * Parallax Section Block - Frontend Script
 * Handles parallax scroll effect
 */
(function() {
    'use strict';

    // Respect reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // Skip on touch devices for performance
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    if (isTouchDevice) return;

    let ticking = false;
    const parallaxSections = [];

    function initParallaxSections() {
        const sections = document.querySelectorAll('.wp-block-kunaal-parallax-section, .parallax-section');
        
        sections.forEach(function(section) {
            const bg = section.querySelector('.parallax-bg');
            const intensity = parseFloat(section.dataset.parallaxIntensity) || 30;
            
            if (bg && intensity > 0) {
                parallaxSections.push({
                    section: section,
                    bg: bg,
                    intensity: intensity / 100
                });
            }
        });
    }

    function updateParallax() {
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;

        parallaxSections.forEach(function(item) {
            const rect = item.section.getBoundingClientRect();
            const sectionTop = rect.top + scrollY;
            const sectionHeight = rect.height;
            
            // Check if section is in viewport
            if (scrollY + windowHeight > sectionTop && scrollY < sectionTop + sectionHeight) {
                // Calculate parallax offset
                const progress = (scrollY + windowHeight - sectionTop) / (windowHeight + sectionHeight);
                const offset = (progress - 0.5) * sectionHeight * item.intensity;
                
                item.bg.style.transform = 'translate3d(0, ' + offset + 'px, 0)';
            }
        });

        ticking = false;
    }

    function onScroll() {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initParallaxSections();
        
        if (parallaxSections.length > 0) {
            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', updateParallax, { passive: true });
            updateParallax();
        }
    });
})();

