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

        // Tooltip preview on hover/focus (enhanced per spec)
        const tooltip = createTooltip();
        let tooltipTimeout;

        footnoteRefs.forEach(function(ref) {
            ref.addEventListener('mouseenter', function() {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    return; // Skip tooltips for reduced motion
                }
                tooltipTimeout = setTimeout(() => {
                    showTooltip(this);
                }, 300);
            });

            ref.addEventListener('mouseleave', function() {
                clearTimeout(tooltipTimeout);
                hideTooltip();
            });

            ref.addEventListener('focus', function() {
                showTooltip(this);
            });

            ref.addEventListener('blur', function() {
                hideTooltip();
            });
        });

        function showTooltip(ref) {
            const targetId = ref.getAttribute('href').substring(1);
            const footnoteItem = document.getElementById(targetId);
            
            if (!footnoteItem) return;

            const content = footnoteItem.querySelector('.footnote-content');
            if (!content) return;

            // Safe: content.innerHTML is from server-rendered HTML sanitized with wp_kses_post()
            tooltip.innerHTML = content.innerHTML;
            tooltip.classList.add('visible');
            positionTooltip(tooltip, ref);
        }

        function hideTooltip() {
            tooltip.classList.remove('visible');
        }

        function positionTooltip(tooltip, ref) {
            const rect = ref.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            const scrollY = window.scrollY;
            const scrollX = window.scrollX;

            let top = rect.top + scrollY - tooltipRect.height - 8;
            let left = rect.left + scrollX + (rect.width / 2) - (tooltipRect.width / 2);

            if (left < 8) {
                left = rect.left + scrollX + 8;
            }
            if (left + tooltipRect.width > window.innerWidth - 8) {
                left = rect.left + scrollX + rect.width - tooltipRect.width - 8;
            }
            if (top < scrollY + 8) {
                top = rect.bottom + scrollY + 8;
            }

            tooltip.style.top = top + 'px';
            tooltip.style.left = left + 'px';
        }

        function createTooltip() {
            const tooltip = document.createElement('div');
            tooltip.className = 'footnote-tooltip';
            tooltip.setAttribute('role', 'tooltip');
            tooltip.setAttribute('aria-hidden', 'true');
            document.body.appendChild(tooltip);
            return tooltip;
        }

        // Mobile: tap to show tooltip
        if ('ontouchstart' in window) {
            footnoteRefs.forEach(function(ref) {
                ref.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (tooltip.classList.contains('visible')) {
                        hideTooltip();
                    } else {
                        showTooltip(this);
                    }
                });
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.footnote-ref, .footnote-tooltip')) {
                    hideTooltip();
                }
            });
        }
    });
})();

