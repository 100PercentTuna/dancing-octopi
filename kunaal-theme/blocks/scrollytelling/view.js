/**
 * Scrollytelling Block - Frontend Script
 * Handles step activation and sticky content updates
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        const scrollySections = document.querySelectorAll('.wp-block-kunaal-scrollytelling, .scrolly');
        
        scrollySections.forEach(function(section) {
            initScrollytelling(section);
        });
    });

    function initScrollytelling(section) {
        const steps = section.querySelectorAll('.scrolly-step');
        const stickyTitle = section.querySelector('.scrolly-title');
        const stickyDescription = section.querySelector('.scrolly-description');
        
        if (steps.length === 0) return;

        // Create intersection observer
        const observerOptions = {
            root: null,
            rootMargin: '-30% 0px -30% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                const step = entry.target;
                
                if (entry.isIntersecting) {
                    // Activate this step
                    steps.forEach(function(s) {
                        s.classList.remove('active');
                    });
                    step.classList.add('active');
                    
                    // Update sticky content if data attributes exist
                    const newTitle = step.dataset.stickyTitle;
                    const newDescription = step.dataset.stickyDescription;
                    
                    if (stickyTitle && newTitle) {
                        stickyTitle.style.opacity = '0';
                        setTimeout(function() {
                            stickyTitle.textContent = newTitle;
                            stickyTitle.style.opacity = '1';
                        }, 150);
                    }
                    
                    if (stickyDescription && newDescription) {
                        stickyDescription.style.opacity = '0';
                        setTimeout(function() {
                            stickyDescription.textContent = newDescription;
                            stickyDescription.style.opacity = '1';
                        }, 150);
                    }
                }
            });
        }, observerOptions);

        // Observe each step
        steps.forEach(function(step) {
            observer.observe(step);
        });

        // Activate first step by default
        if (steps.length > 0) {
            steps[0].classList.add('active');
        }
    }
})();

