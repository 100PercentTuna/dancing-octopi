/**
 * Small Multiples - Frontend JavaScript
 * Renders mini charts in each cell
 */
(function() {
  'use strict';

  function renderChart(canvas, values, type) {
    const ctx = canvas.getContext('2d');
    const width = canvas.width = canvas.offsetWidth;
    const height = canvas.height = canvas.offsetHeight;
    ctx.clearRect(0, 0, width, height);

    const data = values.split(',').map(v => parseFloat(v) || 0);
    if (data.length === 0) return;

    const min = Math.min(...data);
    const max = Math.max(...data);
    const range = max - min || 1;
    const padding = 10;
    const chartWidth = width - (padding * 2);
    const chartHeight = height - (padding * 2);

    // Read chart color from CSS token with fallback
    const computedStyle = getComputedStyle(document.documentElement);
    const chartColor = computedStyle.getPropertyValue('--chart-warm').trim() || '#7D6B5D';
    ctx.strokeStyle = chartColor;
    ctx.fillStyle = chartColor;
    ctx.lineWidth = 2;

    if (type === 'line' || type === 'sparkline') {
      ctx.beginPath();
      data.forEach((val, i) => {
        const x = padding + (i / (data.length - 1 || 1)) * chartWidth;
        const y = padding + chartHeight - ((val - min) / range) * chartHeight;
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
      });
      ctx.stroke();
    } else if (type === 'bar') {
      const barWidth = chartWidth / data.length;
      data.forEach((val, i) => {
        const barHeight = ((val - min) / range) * chartHeight;
        ctx.fillRect(padding + i * barWidth, padding + chartHeight - barHeight, barWidth * 0.8, barHeight);
      });
    } else if (type === 'area') {
      ctx.beginPath();
      ctx.moveTo(padding, padding + chartHeight);
      data.forEach((val, i) => {
        const x = padding + (i / (data.length - 1 || 1)) * chartWidth;
        const y = padding + chartHeight - ((val - min) / range) * chartHeight;
        ctx.lineTo(x, y);
      });
      ctx.lineTo(padding + chartWidth, padding + chartHeight);
      ctx.closePath();
      ctx.fillStyle = 'rgba(125, 107, 93, 0.3)';
      ctx.fill();
      ctx.stroke();
    }
  }

  function initSmallMultiples(block) {
    const cells = block.querySelectorAll('.cell-chart');
    cells.forEach(cell => {
      const canvas = cell.querySelector('canvas');
      const values = cell.dataset.values;
      const type = cell.dataset.chartType || 'line';
      if (canvas && values) {
        renderChart(canvas, values, type);
      }
    });
  }

  document.querySelectorAll('.wp-block-kunaal-small-multiples').forEach(initSmallMultiples);

  // Re-render on resize
  let resizeTimer;
  window.addEventListener('resize', () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      document.querySelectorAll('.wp-block-kunaal-small-multiples').forEach(initSmallMultiples);
    }, 250);
  });
})();

