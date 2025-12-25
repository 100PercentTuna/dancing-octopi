/**
 * Footnote Block - Frontend Script
 * Handles smooth scrolling and bidirectional navigation
 */
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for footnote links
        const footnoteRefs = document.querySelectorAll('.footnote-ref a');
        const footnoteBacklinks = document.querySelectorAll('.footnote-number a');
        
        function smoothScrollTo(element, offset) {
            if (!element) return;
            
            const headerHeight = document.querySelector('.mast')?.offsetHeight || 80;
            const targetPosition = element.getBoundingClientRect().top + window.scrollY - headerHeight - (offset || 20);
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
        
        // Click on footnote reference -> scroll to footnote
        footnoteRefs.forEach(function(ref) {
            ref.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                
                if (target) {
                    smoothScrollTo(target);
                    // Update URL hash without jumping
                    history.pushState(null, null, '#' + targetId);
                    target.focus({ preventScroll: true });
                }
            });
        });
        
        // Click on footnote number -> scroll back to reference
        footnoteBacklinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const target = document.getElementById(targetId);
                
                if (target) {
                    smoothScrollTo(target);
                    // Update URL hash without jumping
                    history.pushState(null, null, '#' + targetId);
                    target.focus({ preventScroll: true });
                }
            });
        });
        
        // Handle direct navigation to footnote (via URL hash)
        function handleHashNavigation() {
            const hash = window.location.hash;
            if (hash) {
                const target = document.querySelector(hash);
                if (target && (target.closest('.footnote-item') || target.closest('.footnote-ref'))) {
                    setTimeout(function() {
                        smoothScrollTo(target);
                    }, 100);
                }
            }
        }
        
        window.addEventListener('hashchange', handleHashNavigation);
        if (window.location.hash) {
            handleHashNavigation();
        }
    });
})();

