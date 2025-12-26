/**
 * Network Graph - Frontend JavaScript
 * Uses D3.js force simulation
 */
(function() {
  'use strict';

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

  async function initNetworkGraph(block) {
    const nodes = JSON.parse(block.dataset.nodes || '[]');
    const edges = JSON.parse(block.dataset.edges || '[]');
    const layout = block.dataset.layout || 'force';
    const showLabels = block.dataset.showLabels === 'true';
    const enableDrag = block.dataset.enableDrag === 'true';
    const enablePhysics = block.dataset.enablePhysics === 'true';
    const chargeStrength = parseInt(block.dataset.chargeStrength) || -300;
    const linkDistance = parseInt(block.dataset.linkDistance) || 100;
    const colorByGroup = block.dataset.colorByGroup === 'true';
    const groupColors = JSON.parse(block.dataset.groupColors || '{}');

    if (nodes.length === 0) {
      const svg = block.querySelector('.network-svg');
      svg.innerHTML = '';
      const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      text.setAttribute('x', '400');
      text.setAttribute('y', '250');
      text.setAttribute('text-anchor', 'middle');
      text.setAttribute('fill', 'var(--muted)');
      text.textContent = 'No nodes';
      svg.appendChild(text);
      return;
    }

    try {
      const d3 = await loadD3();
      renderNetworkGraph(block, d3, nodes, edges, layout, showLabels, enableDrag, enablePhysics, chargeStrength, linkDistance, colorByGroup, groupColors);
    } catch (error) {
      console.error('Failed to render network graph:', error);
      const svg = block.querySelector('.network-svg');
      svg.innerHTML = '';
      const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      text.setAttribute('x', '400');
      text.setAttribute('y', '250');
      text.setAttribute('text-anchor', 'middle');
      text.setAttribute('fill', 'var(--muted)');
      text.textContent = 'Error loading graph';
      svg.appendChild(text);
    }
  }

  function renderNetworkGraph(block, d3, nodes, edges, layout, showLabels, enableDrag, enablePhysics, chargeStrength, linkDistance, colorByGroup, groupColors) {
    const svg = d3.select(block.querySelector('.network-svg'));
    const container = block.querySelector('.network-container');
    const width = container.offsetWidth;
    const height = container.offsetHeight;

    svg.attr('viewBox', `0 0 ${width} ${height}`).selectAll('*').remove();

    const themeColors = ['#7D6B5D', '#B8A99A', '#C9553D', '#8B7355', '#D4C4B5', '#6B5B4F', '#A08B7A'];
    const sizeMap = { small: 8, medium: 12, large: 16 };

    // Color nodes
    const groups = [...new Set(nodes.map(n => n.group).filter(Boolean))];
    nodes.forEach(node => {
      if (!node.color) {
        if (colorByGroup && node.group) {
          node.color = groupColors[node.group] || themeColors[groups.indexOf(node.group) % themeColors.length];
        } else {
          node.color = themeColors[nodes.indexOf(node) % themeColors.length];
        }
      }
    });

    // Create force simulation
    const simulation = d3.forceSimulation(nodes)
      .force('link', d3.forceLink(edges).id(d => d.id).distance(linkDistance))
      .force('charge', d3.forceManyBody().strength(chargeStrength))
      .force('center', d3.forceCenter(width / 2, height / 2));

    if (!enablePhysics) {
      simulation.stop();
    }

    // Draw links
    const link = svg.append('g')
      .selectAll('line')
      .data(edges)
      .enter()
      .append('line')
      .attr('class', 'network-edge')
      .attr('stroke', '#999')
      .attr('stroke-width', d => Math.sqrt(d.weight || 1) * 2)
      .attr('stroke-opacity', 0.6);

    // Draw nodes
    const node = svg.append('g')
      .selectAll('circle')
      .data(nodes)
      .enter()
      .append('circle')
      .attr('class', 'network-node')
      .attr('r', d => sizeMap[d.size] || 12)
      .attr('fill', d => d.color)
      .attr('stroke', '#1A1A1A')
      .attr('stroke-width', 2)
      .attr('data-id', d => d.id)
      .call(enableDrag ? d3.drag()
        .on('start', dragstarted)
        .on('drag', dragged)
        .on('end', dragended) : null);

    // Draw labels
    if (showLabels) {
      const label = svg.append('g')
        .selectAll('text')
        .data(nodes)
        .enter()
        .append('text')
        .attr('class', 'network-label')
        .text(d => d.label || d.id)
        .attr('dx', d => (sizeMap[d.size] || 12) + 5)
        .attr('dy', 4);
    }

    // Update positions on simulation tick
    simulation.on('tick', () => {
      link
        .attr('x1', d => d.source.x)
        .attr('y1', d => d.source.y)
        .attr('x2', d => d.target.x)
        .attr('y2', d => d.target.y);

      node.attr('cx', d => d.x).attr('cy', d => d.y);
      
      if (showLabels) {
        svg.selectAll('.network-label')
          .attr('x', d => d.x)
          .attr('y', d => d.y);
      }
    });

    function dragstarted(event) {
      if (!event.active) simulation.alphaTarget(0.3).restart();
      event.subject.fx = event.subject.x;
      event.subject.fy = event.subject.y;
    }

    function dragged(event) {
      event.subject.fx = event.x;
      event.subject.fy = event.y;
    }

    function dragended(event) {
      if (!event.active) simulation.alphaTarget(0);
      event.subject.fx = null;
      event.subject.fy = null;
    }

    // Tooltip
    const tooltip = block.querySelector('.network-tooltip');
    node.on('mouseenter', function(event, d) {
      const connections = edges.filter(e => e.source.id === d.id || e.target.id === d.id);
      tooltip.querySelector('.tooltip-title').textContent = d.label || d.id;
      tooltip.querySelector('.tooltip-group').textContent = d.group || '';
      tooltip.querySelector('.tooltip-description').textContent = d.description || '';
      const connectionsList = tooltip.querySelector('.tooltip-connections');
      // Clear existing content
      connectionsList.innerHTML = '';
      // Build list using DOM methods to prevent XSS
      connections.forEach(e => {
        const other = e.source.id === d.id ? e.target : e.source;
        const li = document.createElement('li');
        const otherLabel = document.createTextNode(other.label || other.id);
        li.appendChild(otherLabel);
        if (e.label) {
          const edgeLabel = document.createTextNode(' (' + e.label + ')');
          li.appendChild(edgeLabel);
        }
        connectionsList.appendChild(li);
      });
      tooltip.hidden = false;
      positionTooltip(tooltip, event);
    });

    node.on('mouseleave', () => {
      tooltip.hidden = true;
    });

    function positionTooltip(tooltip, event) {
      const rect = block.getBoundingClientRect();
      tooltip.style.left = (event.pageX - rect.left + 10) + 'px';
      tooltip.style.top = (event.pageY - rect.top + 10) + 'px';
    }

    // Zoom controls
    const zoomIn = block.querySelector('.network-zoom-in');
    const zoomOut = block.querySelector('.network-zoom-out');
    const reset = block.querySelector('.network-reset');
    let currentZoom = 1;

    if (zoomIn) {
      zoomIn.addEventListener('click', () => {
        currentZoom = Math.min(currentZoom * 1.2, 3);
        svg.style.transform = `scale(${currentZoom})`;
      });
    }

    if (zoomOut) {
      zoomOut.addEventListener('click', () => {
        currentZoom = Math.max(currentZoom / 1.2, 0.5);
        svg.style.transform = `scale(${currentZoom})`;
      });
    }

    if (reset) {
      reset.addEventListener('click', () => {
        currentZoom = 1;
        svg.style.transform = 'scale(1)';
        simulation.alpha(1).restart();
      });
    }
  }

  // Initialize all network graphs
  function initAllNetworkGraphs() {
    document.querySelectorAll('.wp-block-kunaal-network-graph[data-lazy-block="network-graph"]').forEach(block => {
      if (block.classList.contains('is-loaded')) return;
      block.classList.add('is-loading');
      initNetworkGraph(block).then(() => {
        block.classList.remove('is-loading');
        block.classList.add('is-loaded');
      });
    });
  }

  if (window.kunaalLazyLoad) {
    window.kunaalLazyLoad.loadBlock = async function(element) {
      if (element.dataset.lazyBlock === 'network-graph') {
        await initNetworkGraph(element);
        element.classList.remove('is-loading');
        element.classList.add('is-loaded');
      }
    };
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllNetworkGraphs);
  } else {
    initAllNetworkGraphs();
  }
})();

