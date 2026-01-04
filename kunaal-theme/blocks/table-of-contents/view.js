/**
 * Table of Contents Block - Frontend JavaScript
 * Handles smooth scrolling and active section highlighting
 */
(function() {
    'use strict';

    function initTOC(tocElement) {
        const shouldHighlight = tocElement.dataset.tocHighlight === 'true';
        const shouldSmoothScroll = tocElement.dataset.tocSmooth === 'true';
        const links = tocElement.querySelectorAll('.toc__link');
        
        if (links.length === 0) return;

        // Collect heading elements that correspond to TOC links
        const headingMap = new Map();
        links.forEach(link => {
            const href = link.getAttribute('href');
            if (href && href.startsWith('#')) {
                const id = href.substring(1);
                const heading = document.getElementById(id);
                if (heading) {
                    headingMap.set(link, heading);
                }
            }
        });

        // Smooth scroll handler
        if (shouldSmoothScroll) {
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const heading = headingMap.get(this);
                    if (!heading) return;
                    
                    e.preventDefault();
                    
                    const headerHeight = document.querySelector('.mast')?.offsetHeight || 80;
                    const targetPosition = heading.getBoundingClientRect().top + window.scrollY - headerHeight - 20;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                    
                    // Update URL hash without jumping
                    const id = this.getAttribute('href').substring(1);
                    history.pushState(null, null, '#' + id);
                    
                    // Focus heading for accessibility
                    heading.setAttribute('tabindex', '-1');
                    heading.focus({ preventScroll: true });
                });
            });
        }

        // Active section highlighting
        if (shouldHighlight && headingMap.size > 0) {
            const headings = Array.from(headingMap.values());
            
            // Use IntersectionObserver for efficient scroll tracking
            const observerOptions = {
                rootMargin: '-10% 0px -80% 0px',
                threshold: 0
            };
            
            let activeLink = null;
            
            const observer = new IntersectionObserver((entries) => {
                // Find the topmost visible heading
                let topEntry = null;
                
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (!topEntry || entry.boundingClientRect.top < topEntry.boundingClientRect.top) {
                            topEntry = entry;
                        }
                    }
                });
                
                if (topEntry) {
                    // Find the link for this heading
                    headingMap.forEach((heading, link) => {
                        if (heading === topEntry.target) {
                            if (activeLink !== link) {
                                // Remove previous active state
                                if (activeLink) {
                                    activeLink.classList.remove('is-active');
                                }
                                // Add new active state
                                link.classList.add('is-active');
                                activeLink = link;
                            }
                        }
                    });
                }
            }, observerOptions);
            
            headings.forEach(heading => {
                observer.observe(heading);
            });
            
            // Fallback: Update on scroll for better accuracy when scrolling up
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    const scrollPos = window.scrollY;
                    const headerHeight = document.querySelector('.mast')?.offsetHeight || 80;
                    
                    let currentHeading = null;
                    
                    headings.forEach(heading => {
                        const headingTop = heading.getBoundingClientRect().top + scrollPos - headerHeight - 100;
                        if (scrollPos >= headingTop) {
                            currentHeading = heading;
                        }
                    });
                    
                    if (currentHeading) {
                        headingMap.forEach((heading, link) => {
                            if (heading === currentHeading) {
                                if (activeLink !== link) {
                                    if (activeLink) {
                                        activeLink.classList.remove('is-active');
                                    }
                                    link.classList.add('is-active');
                                    activeLink = link;
                                }
                            }
                        });
                    }
                }, 50);
            }, { passive: true });
            
            // Set initial active state
            if (window.location.hash) {
                const initialLink = tocElement.querySelector(`a[href="${window.location.hash}"]`);
                if (initialLink) {
                    initialLink.classList.add('is-active');
                    activeLink = initialLink;
                }
            } else if (links.length > 0) {
                // Default to first item
                links[0].classList.add('is-active');
                activeLink = links[0];
            }
        }
    }

    // Initialize all TOC blocks
    function initAllTOC() {
        document.querySelectorAll('.wp-block-kunaal-table-of-contents').forEach(toc => {
            if (toc.classList.contains('is-initialized')) return;
            toc.classList.add('is-initialized');
            initTOC(toc);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllTOC);
    } else {
        initAllTOC();
    }
})();

