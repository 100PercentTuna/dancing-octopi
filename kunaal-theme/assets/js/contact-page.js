/**
 * Contact Page JavaScript
 * 
 * Handles scroll reveal animations and contact form submission
 * Extracted from inline script in page-contact.php
 *
 * @package Kunaal_Theme
 * @since 4.20.8
 */
(function() {
  'use strict';

  // Scroll reveal animations
  function initScrollReveals() {
    const elements = document.querySelectorAll('[data-reveal]');
    if (!elements.length) return;
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });
    
    elements.forEach(function(el) {
      observer.observe(el);
    });
  }
  
  function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) {
      setTimeout(initContactForm, 100);
      return;
    }
    
    const checkbox = document.getElementById('contact-include-info');
    const optionalFields = document.getElementById('contact-optional-fields');
    const nameField = document.getElementById('contact-name');
    const emailField = document.getElementById('contact-email');
    const submitBtn = document.getElementById('contact-submit');
    const status = document.getElementById('contact-status');
    
    // Toggle optional fields with animation
    if (checkbox && optionalFields) {
      checkbox.addEventListener('change', function() {
        if (this.checked) {
          optionalFields.style.display = 'block';
          // Trigger reflow for animation
          optionalFields.offsetHeight;
          // Name and email are OPTIONAL - do not set required
          
          // Trigger scroll reveals for new fields
          setTimeout(function() {
            const revealEls = optionalFields.querySelectorAll('[data-reveal]');
            revealEls.forEach(function(el) {
              el.classList.add('is-visible');
            });
          }, 50);
        } else {
          optionalFields.style.display = 'none';
          // Name and email are optional - no required attribute
          if (nameField) nameField.value = '';
          if (emailField) emailField.value = '';
        }
      });
    }
    
    // Form submission
    if (!submitBtn) return;
    
    const ajaxUrl = (typeof kunaalTheme !== 'undefined' && kunaalTheme.ajaxUrl) 
      ? kunaalTheme.ajaxUrl 
      : '';
    
    if (!ajaxUrl) {
      if (status) {
        status.className = 'contact-status is-error';
        status.textContent = 'Form configuration error. Please refresh the page.';
      }
      return;
    }
    
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      submitBtn.classList.add('is-loading');
      submitBtn.disabled = true;
      if (status) {
        status.className = 'contact-status';
        status.textContent = '';
      }
      
      const formData = new FormData(form);
      formData.append('action', 'kunaal_contact_form');
      
      fetch(ajaxUrl, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      })
      .then(function(response) {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(function(data) {
        submitBtn.classList.remove('is-loading');
        
        if (data.success) {
          submitBtn.classList.add('is-success');
          form.reset();
          if (optionalFields) {
            optionalFields.style.display = 'none';
            if (checkbox) checkbox.checked = false;
          }
          
          setTimeout(function() {
            submitBtn.classList.remove('is-success');
            submitBtn.disabled = false;
          }, 3000);
        } else {
          if (status) {
            status.className = 'contact-status is-error';
            status.textContent = data.data && data.data.message ? data.data.message : 'Something went wrong. Please try again.';
          }
          submitBtn.disabled = false;
        }
      })
      .catch(function(error) {
        if (window.kunaalTheme?.debug) {
          console.error('Contact form error:', error);
        }
        submitBtn.classList.remove('is-loading');
        if (status) {
          status.className = 'contact-status is-error';
          status.textContent = 'Network error. Please try again.';
        }
        submitBtn.disabled = false;
      });
    });
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      initScrollReveals();
      initContactForm();
    });
  } else {
    initScrollReveals();
    initContactForm();
  }

  // Debug logging (only if WP_DEBUG is enabled)
  if (typeof window.kunaalAboutV22 !== 'undefined' && window.kunaalAboutV22.debug) {
    setTimeout(function() {
      const contactPage = document.querySelector('.contact-page');
      if (contactPage) {
        const pageStyles = window.getComputedStyle(contactPage);
        const pageRect = contactPage.getBoundingClientRect();
        const bodyRect = document.body.getBoundingClientRect();
        // Debug logging helper
        function debugLog(location, message, data, hypothesisId) {
          if (typeof window.kunaalTheme === 'undefined' || !window.kunaalTheme.ajaxUrl) {
            return;
          }
          const logData = {
            location: location,
            message: message,
            data: data || {},
            timestamp: Date.now(),
            sessionId: 'debug-session',
            runId: 'run1',
            hypothesisId: hypothesisId || ''
          };
          const formData = new FormData();
          formData.append('action', 'kunaal_debug_log');
          formData.append('log_data', JSON.stringify(logData));
          fetch(window.kunaalTheme.ajaxUrl, {
            method: 'POST',
            body: formData
          }).catch(function(error) {
            // Silently fail - form submission error handling is done in main handler
            if (window.console && window.console.warn) {
              window.console.warn('[kunaal-theme] Contact form fetch failed:', error);
            }
          });
        }
        
        debugLog('contact-page.js', 'Contact page background CSS computed styles', {
          minHeight: pageStyles.minHeight,
          height: pageRect.height,
          viewportHeight: window.innerHeight,
          paddingTop: pageStyles.paddingTop,
          marginTop: pageStyles.marginTop,
          background: pageStyles.background,
          backgroundAttachment: pageStyles.backgroundAttachment,
          backgroundSize: pageStyles.backgroundSize,
          pageTop: pageRect.top,
          bodyTop: bodyRect.top,
          gapAbove: pageRect.top - bodyRect.top
        }, 'H6.1,H6.2,H6.3');
      }
      
      // Check X/Twitter text wrapping - use more specific selector
      let twitterLink = document.querySelector('.contact-social-link[aria-label*="Twitter"] span, .contact-social-link[aria-label*="X"] span');
      if (!twitterLink) {
        // Fallback: find link containing "Twitter" or "X" text
        const allLinks = document.querySelectorAll('.contact-social-link span');
        for (let i = 0; i < allLinks.length; i++) {
          if (allLinks[i].textContent.includes('Twitter') || allLinks[i].textContent.includes('X')) {
            twitterLink = allLinks[i];
            break;
          }
        }
      }
      if (twitterLink) {
        const linkStyles = window.getComputedStyle(twitterLink);
        const linkRect = twitterLink.getBoundingClientRect();
        debugLog('contact-page.js', 'X/Twitter text wrapping check', {
          whiteSpace: linkStyles.whiteSpace,
          width: linkRect.width,
          height: linkRect.height,
          textContent: twitterLink.textContent.trim(),
          lineCount: Math.ceil(linkRect.height / (parseFloat(linkStyles.lineHeight) || 16))
        }, 'H7.1,H7.2');
      }
    }, 1000);
  }
})();




