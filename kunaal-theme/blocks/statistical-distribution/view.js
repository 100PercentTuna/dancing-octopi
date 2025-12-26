/**
 * Statistical Distribution - Frontend JavaScript
 */
(function() {
  'use strict';
  // Basic interactivity - hover effects handled by CSS
  document.querySelectorAll('.wp-block-kunaal-statistical-distribution .stat-group').forEach(group => {
    group.addEventListener('focus', function() {
      this.setAttribute('tabindex', '0');
    });
  });
})();

