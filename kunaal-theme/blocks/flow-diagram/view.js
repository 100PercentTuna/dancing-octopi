/**
 * Flow Diagram - Frontend JavaScript
 * Loads D3.js dynamically and renders Sankey/Alluvial diagrams
 */
(function() {
  'use strict';

  /**
   * Safe JSON parse helper - prevents crashes from malformed data
   * @param {string} raw - The raw JSON string to parse
   * @param {*} fallback - Fallback value if parsing fails
   * @returns {*} Parsed value or fallback
   */
  function safeJsonParse(raw, fallback) {
    try {
      return JSON.parse(raw);
    } catch (e) {
      return fallback;
    }
  }

  // Use centralized library loader
  async function loadD3() {
    if (window.kunaalLibLoader && window.kunaalLibLoader.loadD3) {
      return window.kunaalLibLoader.loadD3();
    }
    // Fallback if loader not available
    if (window.d3) {
      return Promise.resolve(window.d3);
    }
    return Promise.reject(new Error('D3 loader not available'));
  }

  async function initFlowDiagram(block) {
    const diagramType = block.dataset.diagramType || 'sankey';
    const nodes = safeJsonParse(block.dataset.nodes || '[]', []);
    const links = safeJsonParse(block.dataset.links || '[]', []);
    
    if (nodes.length === 0 || links.length === 0) {
      const svg = block.querySelector('.flow-svg');
      svg.innerHTML = '';
      const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      text.setAttribute('x', '400');
      text.setAttribute('y', '250');
      text.setAttribute('text-anchor', 'middle');
      text.setAttribute('fill', 'var(--muted)');
      text.textContent = 'No data';
      svg.appendChild(text);
      return;
    }

    try {
      const d3 = await loadD3();
      renderFlowDiagram(block, d3, diagramType, nodes, links);
    } catch (error) {
      console.error('Failed to render flow diagram:', error);
      const svg = block.querySelector('.flow-svg');
      svg.innerHTML = '';
      const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      text.setAttribute('x', '400');
      text.setAttribute('y', '250');
      text.setAttribute('text-anchor', 'middle');
      text.setAttribute('fill', 'var(--muted)');
      text.textContent = 'Error loading diagram';
      svg.appendChild(text);
    }
  }

  function renderFlowDiagram(block, d3, type, nodes, links) {
    const svg = d3.select(block.querySelector('.flow-svg'));
    svg.selectAll('*').remove();

    const width = 800;
    const height = 500;
    const nodeWidth = parseInt(block.dataset.nodeWidth) || 20;
    const nodePadding = parseInt(block.dataset.nodePadding) || 8;
    
    // Read colors from CSS tokens with fallbacks
    const computedStyle = getComputedStyle(document.documentElement);
    const nodeStrokeColor = computedStyle.getPropertyValue('--k-color-ink').trim() || '#1A1A1A';

    // Simple layout for Sankey
    if (type === 'sankey') {
      // Group nodes by column
      const columns = {};
      nodes.forEach(node => {
        const col = node.column || 0;
        if (!columns[col]) columns[col] = [];
        columns[col].push(node);
      });

      const colCount = Object.keys(columns).length;
      const colWidth = (width - 200) / Math.max(colCount - 1, 1);

      // Position nodes
      const nodePositions = {};
      Object.keys(columns).sort((a, b) => a - b).forEach((col, colIndex) => {
        const colNodes = columns[col];
        const nodeHeight = (height - 100) / colNodes.length;
        colNodes.forEach((node, nodeIndex) => {
          nodePositions[node.id] = {
            x: 100 + colIndex * colWidth,
            y: 50 + nodeIndex * (nodeHeight + nodePadding),
            width: nodeWidth,
            height: nodeHeight
          };
        });
      });

      // Draw links
      const linkColorMode = block.dataset.linkColorMode || 'source';
      const defaultLinkColor = computedStyle.getPropertyValue('--chart-warm-light').trim() || '#B8A99A';
      const singleLinkColor = block.dataset.singleLinkColor || defaultLinkColor;
      const themeColors = [
        computedStyle.getPropertyValue('--chart-warm').trim() || '#7D6B5D',
        computedStyle.getPropertyValue('--chart-warm-light').trim() || '#B8A99A',
        computedStyle.getPropertyValue('--chart-accent').trim() || '#C9553D',
        '#8B7355', // Additional warm shade
        computedStyle.getPropertyValue('--chart-warm-muted').trim() || '#D4C4B5'
      ];

      links.forEach(link => {
        const source = nodePositions[link.source];
        const target = nodePositions[link.target];
        if (!source || !target) return;

        const sourceColor = nodes.find(n => n.id === link.source)?.color || themeColors[0];
        const targetColor = nodes.find(n => n.id === link.target)?.color || themeColors[1];
        
        let linkColor = singleLinkColor;
        if (linkColorMode === 'source') linkColor = sourceColor;
        else if (linkColorMode === 'target') linkColor = targetColor;
        else if (linkColorMode === 'gradient') {
          // Use gradient
          const gradientId = `grad-${link.source}-${link.target}`;
          const defs = svg.append('defs');
          const gradient = defs.append('linearGradient').attr('id', gradientId);
          gradient.append('stop').attr('offset', '0%').attr('stop-color', sourceColor);
          gradient.append('stop').attr('offset', '100%').attr('stop-color', targetColor);
          linkColor = `url(#${gradientId})`;
        }

        const path = d3.path();
        const midX = (source.x + source.width + target.x) / 2;
        path.moveTo(source.x + source.width, source.y + source.height / 2);
        path.bezierCurveTo(midX, source.y + source.height / 2, midX, target.y + target.height / 2, target.x, target.y + target.height / 2);
        
        svg.append('path')
          .attr('d', path.toString())
          .attr('stroke', linkColor)
          .attr('stroke-width', Math.max(2, Math.sqrt(link.value) / 10))
          .attr('fill', 'none')
          .attr('opacity', 0.4)
          .attr('class', 'flow-link')
          .attr('data-value', link.value);
      });

      // Draw nodes
      nodes.forEach(node => {
        const pos = nodePositions[node.id];
        if (!pos) return;

        const nodeColor = node.color || themeColors[nodes.indexOf(node) % themeColors.length];

        svg.append('rect')
          .attr('x', pos.x)
          .attr('y', pos.y)
          .attr('width', pos.width)
          .attr('height', pos.height)
          .attr('fill', nodeColor)
          .attr('stroke', nodeStrokeColor)
          .attr('stroke-width', 1)
          .attr('rx', 3)
          .attr('class', 'flow-node')
          .attr('data-id', node.id);

        svg.append('text')
          .attr('x', pos.x + pos.width + 5)
          .attr('y', pos.y + pos.height / 2)
          .attr('dy', '0.35em')
          .attr('class', 'flow-node-label')
          .text(node.label || node.id);

        if (block.dataset.showValues === 'true') {
          const nodeValue = links.filter(l => l.source === node.id || l.target === node.id)
            .reduce((sum, l) => sum + (l.value || 0), 0);
          svg.append('text')
            .attr('x', pos.x + pos.width + 5)
            .attr('y', pos.y + pos.height / 2 + 15)
            .attr('class', 'flow-node-value')
            .text(formatValue(nodeValue, block));
        }
      });
    }
  }

  function formatValue(value, block) {
    const format = block.dataset.valueFormat || 'number';
    const currency = block.dataset.currencySymbol || '$';
    const unit = block.dataset.valueUnit || '';

    switch (format) {
      case 'percent': return round(value, 1) + '%';
      case 'currency': return currency + number_format(value) + (unit ? ' ' + unit : '');
      case 'compact':
        if (value >= 1000000) return currency + round(value / 1000000, 1) + 'M' + (unit ? ' ' + unit : '');
        if (value >= 1000) return currency + round(value / 1000, 1) + 'K' + (unit ? ' ' + unit : '');
        return currency + round(value) + (unit ? ' ' + unit : '');
      default: return round(value) + (unit ? ' ' + unit : '');
    }
  }

  function number_format(num) {
    return num.toLocaleString();
  }

  function round(num, decimals = 0) {
    return Number(num.toFixed(decimals));
  }

  // Initialize all flow diagrams
  function initAllFlowDiagrams() {
    document.querySelectorAll('.wp-block-kunaal-flow-diagram[data-lazy-block="flow-diagram"]').forEach(block => {
      if (block.classList.contains('is-loaded')) return;
      block.classList.add('is-loading');
      initFlowDiagram(block).then(() => {
        block.classList.remove('is-loading');
        block.classList.add('is-loaded');
      });
    });
  }

  // Register loader with lazy loading system
  if (window.kunaalLazyLoad && window.kunaalLazyLoad.register) {
    window.kunaalLazyLoad.register('flow-diagram', async function(element) {
      await initFlowDiagram(element);
    });
  }

  // Also initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllFlowDiagrams);
  } else {
    initAllFlowDiagrams();
  }
})();

