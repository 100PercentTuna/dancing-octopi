/**
 * Sidenote Block - Frontend Script
 * Handles mobile toggle behavior
 */
(function() {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Add keyboard support for sidenote toggles
        const sidenoteNumbers = document.querySelectorAll('.sidenote-number');
        
        sidenoteNumbers.forEach(function(label) {
            // Make label focusable
            label.setAttribute('tabindex', '0');
            label.setAttribute('role', 'button');
            
            // Handle Enter/Space key
            label.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const checkbox = label.nextElementSibling;
                    if (checkbox && checkbox.classList.contains('sidenote-toggle')) {
                        checkbox.checked = !checkbox.checked;
                    }
                }
            });
        });
        
        // Close sidenotes when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth >= 1000) return; // Only on mobile/tablet
            
            if (!e.target.closest('.sidenote-wrapper')) {
                const openToggles = document.querySelectorAll('.sidenote-toggle:checked');
                openToggles.forEach(function(toggle) {
                    toggle.checked = false;
                });
            }
        });
    });
})();
