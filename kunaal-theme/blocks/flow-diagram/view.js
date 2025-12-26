/**
 * Flow Diagram - Frontend JavaScript
 * Loads D3.js dynamically and renders Sankey/Alluvial diagrams
 */
(function() {
  'use strict';

  let d3Loaded = false;
  let d3Loading = false;

  async function loadD3() {
    if (d3Loaded) return window.d3;
    if (d3Loading) {
      // Wait for existing load
      return new Promise(resolve => {
        const checkInterval = setInterval(() => {
          if (d3Loaded) {
            clearInterval(checkInterval);
            resolve(window.d3);
          }
        }, 100);
      });
    }

    d3Loading = true;
    
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = 'https://d3js.org/d3.v7.min.js';
      script.onload = () => {
        d3Loaded = true;
        d3Loading = false;
        resolve(window.d3);
      };
      script.onerror = () => {
        d3Loading = false;
        reject(new Error('Failed to load D3.js'));
      };
      document.head.appendChild(script);
    });
  }

  async function initFlowDiagram(block) {
    const diagramType = block.dataset.diagramType || 'sankey';
    const nodes = JSON.parse(block.dataset.nodes || '[]');
    const links = JSON.parse(block.dataset.links || '[]');
    
    if (nodes.length === 0 || links.length === 0) {
      block.querySelector('.flow-svg').innerHTML = '<text x="400" y="250" text-anchor="middle" fill="var(--muted)">No data</text>';
      return;
    }

    try {
      const d3 = await loadD3();
      renderFlowDiagram(block, d3, diagramType, nodes, links);
    } catch (error) {
      console.error('Failed to render flow diagram:', error);
      block.querySelector('.flow-svg').innerHTML = '<text x="400" y="250" text-anchor="middle" fill="var(--muted)">Error loading diagram</text>';
    }
  }

  function renderFlowDiagram(block, d3, type, nodes, links) {
    const svg = d3.select(block.querySelector('.flow-svg'));
    svg.selectAll('*').remove();

    const width = 800;
    const height = 500;
    const nodeWidth = parseInt(block.dataset.nodeWidth) || 20;
    const nodePadding = parseInt(block.dataset.nodePadding) || 8;

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
      const singleLinkColor = block.dataset.singleLinkColor || '#B8A99A';
      const themeColors = ['#7D6B5D', '#B8A99A', '#C9553D', '#8B7355', '#D4C4B5'];

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
          .attr('stroke', '#1A1A1A')
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

  // Use lazy loading system if available
  if (window.kunaalLazyLoad) {
    window.kunaalLazyLoad.loadBlock = async function(element) {
      if (element.dataset.lazyBlock === 'flow-diagram') {
        await initFlowDiagram(element);
        element.classList.remove('is-loading');
        element.classList.add('is-loaded');
      }
    };
  }

  // Also initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllFlowDiagrams);
  } else {
    initAllFlowDiagrams();
  }
})();

