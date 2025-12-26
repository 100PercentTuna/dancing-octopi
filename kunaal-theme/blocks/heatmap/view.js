/**
 * Heatmap Block - Frontend JavaScript
 * Handles tooltips and interactions
 */
(function() {
  'use strict';

  function initHeatmap(block) {
    const cells = block.querySelectorAll('.heatmap-cell');
    const tooltip = createTooltip();
    let currentCell = null;

    cells.forEach(cell => {
      cell.addEventListener('mouseenter', handleMouseEnter);
      cell.addEventListener('mouseleave', handleMouseLeave);
      cell.addEventListener('focus', handleFocus);
      cell.addEventListener('blur', handleBlur);
    });

    function handleMouseEnter(e) {
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return; // Skip tooltips for reduced motion
      }
      showTooltip(e.target);
    }

    function handleMouseLeave() {
      hideTooltip();
    }

    function handleFocus(e) {
      showTooltip(e.target);
    }

    function handleBlur() {
      hideTooltip();
    }

    function showTooltip(cell) {
      currentCell = cell;
      const label = cell.getAttribute('aria-label');
      if (!label) return;

      tooltip.textContent = label;
      tooltip.classList.add('visible');
      
      positionTooltip(cell, tooltip);
    }

    function hideTooltip() {
      tooltip.classList.remove('visible');
      currentCell = null;
    }

    function positionTooltip(cell, tooltip) {
      const rect = cell.getBoundingClientRect();
      const tooltipRect = tooltip.getBoundingClientRect();
      const scrollY = window.scrollY;
      const scrollX = window.scrollX;

      // Position above by default
      let top = rect.top + scrollY - tooltipRect.height - 8;
      let left = rect.left + scrollX + (rect.width / 2) - (tooltipRect.width / 2);

      // Adjust if would go off screen
      if (left < 8) {
        left = rect.left + scrollX + 8;
      }
      if (left + tooltipRect.width > window.innerWidth - 8) {
        left = rect.left + scrollX + rect.width - tooltipRect.width - 8;
      }
      if (top < scrollY + 8) {
        // Position below instead
        top = rect.bottom + scrollY + 8;
      }

      tooltip.style.top = `${top}px`;
      tooltip.style.left = `${left}px`;
    }

    function createTooltip() {
      const tooltip = document.createElement('div');
      tooltip.className = 'heatmap-tooltip';
      tooltip.setAttribute('role', 'tooltip');
      tooltip.setAttribute('aria-hidden', 'true');
      document.body.appendChild(tooltip);
      return tooltip;
    }
  }

  // Initialize all heatmaps on page load
  function initAllHeatmaps() {
    document.querySelectorAll('.wp-block-kunaal-heatmap').forEach(block => {
      initHeatmap(block);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllHeatmaps);
  } else {
    initAllHeatmaps();
  }

  // Re-initialize for dynamically loaded content
  if (window.MutationObserver) {
    const observer = new MutationObserver(mutations => {
      mutations.forEach(mutation => {
        mutation.addedNodes.forEach(node => {
          if (node.nodeType === 1 && node.classList && node.classList.contains('wp-block-kunaal-heatmap')) {
            initHeatmap(node);
          } else if (node.nodeType === 1 && node.querySelectorAll) {
            node.querySelectorAll('.wp-block-kunaal-heatmap').forEach(block => {
              initHeatmap(block);
            });
          }
        });
      });
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true
    });
  }
})();

