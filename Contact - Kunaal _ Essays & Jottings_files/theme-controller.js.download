/**
 * Kunaal Theme - Dark Mode Controller
 * Handles theme switching, system preference detection, and persistence
 */
(function() {
  'use strict';

  class ThemeController {
    constructor() {
      this.storageKey = 'kunaal-theme-preference';
      this.init();
    }
    
    init() {
      // Set theme immediately to prevent flash (inline script in head)
      this.setInitialTheme();
      
      // Listen for system preference changes
      if (window.matchMedia) {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', (e) => {
          // Only auto-switch if user hasn't manually set a preference
          if (!localStorage.getItem(this.storageKey)) {
            this.setTheme(e.matches ? 'dark' : 'light', false);
          }
        });
      }
      
      // Initialize toggle button if present
      this.initToggle();
    }
    
    setInitialTheme() {
      // Check for saved preference
      const saved = localStorage.getItem(this.storageKey);
      
      if (saved) {
        this.setTheme(saved, false);
      } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        this.setTheme('dark', false);
      } else {
        this.setTheme('light', false);
      }
    }
    
    setTheme(theme, save = true) {
      if (theme !== 'light' && theme !== 'dark') {
        return;
      }
      
      document.documentElement.setAttribute('data-theme', theme);
      this.updateToggle(theme);
      
      // Dispatch event for components that need to react
      window.dispatchEvent(new CustomEvent('themechange', { 
        detail: { theme } 
      }));
      
      if (save) {
        localStorage.setItem(this.storageKey, theme);
      }
    }
    
    toggle() {
      const current = document.documentElement.getAttribute('data-theme') || 'light';
      const next = current === 'dark' ? 'light' : 'dark';
      this.setTheme(next, true);
    }
    
    updateToggle(theme) {
      const toggle = document.querySelector('.theme-toggle');
      if (toggle) {
        toggle.setAttribute('aria-pressed', theme === 'dark');
        const icon = toggle.querySelector('.theme-toggle-icon');
        if (icon) {
          icon.textContent = theme === 'dark' ? '☀️' : '🌙';
          icon.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        }
      }
    }
    
    initToggle() {
      const toggle = document.querySelector('.theme-toggle');
      if (toggle) {
        toggle.addEventListener('click', () => {
          this.toggle();
        });
        
        // Update on page load
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        this.updateToggle(currentTheme);
      }
    }
  }
  
  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      window.themeController = new ThemeController();
    });
  } else {
    window.themeController = new ThemeController();
  }
  
})();

