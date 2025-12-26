/**
 * Dumbbell Chart - Frontend JavaScript
 */
(function() {
  'use strict';

  function initDumbbell(block) {
    const rows = block.querySelectorAll('.dumbbell-row');
    const tooltip = createTooltip();

    rows.forEach(row => {
      row.addEventListener('mouseenter', () => showTooltip(row));
      row.addEventListener('mouseleave', hideTooltip);
      row.addEventListener('focus', () => showTooltip(row));
      row.addEventListener('blur', hideTooltip);
    });

    function showTooltip(row) {
      const category = row.dataset.category;
      const startText = row.querySelector('.dumbbell-value-start')?.textContent;
      const endText = row.querySelector('.dumbbell-value-end')?.textContent;
      const gapText = row.querySelector('.dumbbell-gap')?.textContent;
      
      tooltip.innerHTML = `
        <strong>${category}</strong><br>
        Start: ${startText}<br>
        End: ${endText}<br>
        Gap: ${gapText || 'N/A'}
      `;
      tooltip.classList.add('visible');
      positionTooltip(row, tooltip);
    }

    function hideTooltip() {
      tooltip.classList.remove('visible');
    }

    function positionTooltip(row, tooltip) {
      const rect = row.getBoundingClientRect();
      tooltip.style.top = `${rect.top + window.scrollY - tooltip.offsetHeight - 8}px`;
      tooltip.style.left = `${rect.left + window.scrollX + (rect.width / 2) - (tooltip.offsetWidth / 2)}px`;
    }

    function createTooltip() {
      const tooltip = document.createElement('div');
      tooltip.className = 'dumbbell-tooltip';
      document.body.appendChild(tooltip);
      return tooltip;
    }
  }

  document.querySelectorAll('.wp-block-kunaal-dumbbell-chart').forEach(initDumbbell);
})();

