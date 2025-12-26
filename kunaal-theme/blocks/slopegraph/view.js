/**
 * Slopegraph - Frontend JavaScript
 */
(function() {
  'use strict';
  document.querySelectorAll('.wp-block-kunaal-slopegraph').forEach(block => {
    const rows = block.querySelectorAll('.slopegraph-row');
    rows.forEach(row => {
      row.addEventListener('mouseenter', () => {
        rows.forEach(r => {
          if (r !== row) r.style.opacity = '0.3';
        });
      });
      row.addEventListener('mouseleave', () => {
        rows.forEach(r => r.style.opacity = '1');
      });
    });
  });
})();

