/**
 * Sidenote Block - Frontend Interactivity
 * Handles tap-to-expand on mobile/tablet
 */
(function() {
    'use strict';
    
    function initSidenotes() {
        const refs = document.querySelectorAll('.sidenote-ref');
        
        refs.forEach(function(ref) {
            ref.addEventListener('click', function(e) {
                e.preventDefault();
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                // Close all other sidenotes
                refs.forEach(function(r) {
                    r.setAttribute('aria-expanded', 'false');
                });
                
                // Toggle current
                this.setAttribute('aria-expanded', String(!isExpanded));
            });
        });
        
        // Close sidenotes when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.sidenote-wrapper')) {
                refs.forEach(function(r) {
                    r.setAttribute('aria-expanded', 'false');
                });
            }
        });
    }
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidenotes);
    } else {
        initSidenotes();
    }
})();

