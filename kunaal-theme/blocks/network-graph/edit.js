/**
 * Network Graph Block - Editor Component
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, RangeControl, Button } = components;
  const { __ } = i18n;
  const el = element.createElement;

  registerBlockType('kunaal/network-graph', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const { title, subtitle, layout, showLabels, enableZoom, enableDrag, enablePhysics,
        chargeStrength, linkDistance, colorByGroup, showLegend, height, sourceNote, nodes, edges } = attributes;

      const blockProps = useBlockProps({ className: 'wp-block-kunaal-network-graph' });

      function updateNode(index, field, value) {
        const newNodes = [...nodes];
        if (!newNodes[index]) newNodes[index] = { id: '', label: '', group: '', size: 'medium', color: '', description: '', url: '' };
        newNodes[index][field] = value;
        setAttributes({ nodes: newNodes });
      }

      function addNode() {
        const newId = 'node-' + (nodes.length + 1);
        setAttributes({ nodes: [...nodes, { id: newId, label: '', group: '', size: 'medium', color: '', description: '', url: '' }] });
      }

      function removeNode(index) {
        const nodeId = nodes[index]?.id;
        setAttributes({
          nodes: nodes.filter((_, i) => i !== index),
          edges: edges.filter(e => e.source !== nodeId && e.target !== nodeId)
        });
      }

      function updateEdge(index, field, value) {
        const newEdges = [...edges];
        if (!newEdges[index]) newEdges[index] = { source: '', target: '', weight: 1, label: '' };
        newEdges[index][field] = field === 'weight' ? parseFloat(value) || 1 : value;
        setAttributes({ edges: newEdges });
      }

      function addEdge() {
        setAttributes({ edges: [...edges, { source: '', target: '', weight: 1, label: '' }] });
      }

      function removeEdge(index) {
        setAttributes({ edges: edges.filter((_, i) => i !== index) });
      }

      return el('div', blockProps,
        el(InspectorControls, {},
          el(PanelBody, { title: __('General'), initialOpen: true },
            el(TextControl, { label: __('Title'), value: title, onChange: v => setAttributes({ title: v }) }),
            el(TextControl, { label: __('Subtitle'), value: subtitle, onChange: v => setAttributes({ subtitle: v }) }),
            el(TextControl, { label: __('Source Note'), value: sourceNote, onChange: v => setAttributes({ sourceNote: v }) }),
            el(RangeControl, { label: __('Height'), value: height, min: 300, max: 800, onChange: v => setAttributes({ height: v }) })
          ),
          el(PanelBody, { title: __('Layout') },
            el(SelectControl, { label: __('Layout Algorithm'), value: layout, options: [
              { label: 'Force-Directed', value: 'force' },
              { label: 'Radial', value: 'radial' },
              { label: 'Hierarchical', value: 'hierarchical' }
            ], onChange: v => setAttributes({ layout: v }) }),
            el(ToggleControl, { label: __('Show Labels'), checked: showLabels, onChange: v => setAttributes({ showLabels: v }) }),
            el(ToggleControl, { label: __('Enable Zoom'), checked: enableZoom, onChange: v => setAttributes({ enableZoom: v }) }),
            el(ToggleControl, { label: __('Enable Drag'), checked: enableDrag, onChange: v => setAttributes({ enableDrag: v }) }),
            el(ToggleControl, { label: __('Enable Physics'), checked: enablePhysics, onChange: v => setAttributes({ enablePhysics: v }) }),
            enablePhysics && el(RangeControl, { label: __('Charge Strength'), value: chargeStrength, min: -500, max: -100, onChange: v => setAttributes({ chargeStrength: v }) }),
            enablePhysics && el(RangeControl, { label: __('Link Distance'), value: linkDistance, min: 50, max: 200, onChange: v => setAttributes({ linkDistance: v }) })
          ),
          el(PanelBody, { title: __('Nodes'), initialOpen: true },
            el('table', { className: 'network-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('ID')),
                  el('th', {}, __('Label')),
                  el('th', {}, __('Group')),
                  el('th', {}, __('Size')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                nodes.map((node, i) =>
                  el('tr', { key: i },
                    el('td', {}, el(TextControl, { value: node.id || '', onChange: v => updateNode(i, 'id', v), placeholder: 'node-1' })),
                    el('td', {}, el(TextControl, { value: node.label || '', onChange: v => updateNode(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el(TextControl, { value: node.group || '', onChange: v => updateNode(i, 'group', v), placeholder: __('Group') })),
                    el('td', {},
                      el('select', {
                        value: node.size || 'medium',
                        onChange: e => updateNode(i, 'size', e.target.value)
                      },
                        el('option', { value: 'small' }, __('Small')),
                        el('option', { value: 'medium' }, __('Medium')),
                        el('option', { value: 'large' }, __('Large'))
                      )
                    ),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeNode(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 5 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addNode }, __('+ Add Node'))
                  )
                )
              )
            )
          ),
          el(PanelBody, { title: __('Edges') },
            el('table', { className: 'network-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('From')),
                  el('th', {}, __('To')),
                  el('th', {}, __('Weight')),
                  el('th', {}, __('Label')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                edges.map((edge, i) =>
                  el('tr', { key: i },
                    el('td', {},
                      el('select', {
                        value: edge.source || '',
                        onChange: e => updateEdge(i, 'source', e.target.value)
                      },
                        el('option', { value: '' }, '—'),
                        nodes.map(node => el('option', { key: node.id, value: node.id }, node.label || node.id))
                      )
                    ),
                    el('td', {},
                      el('select', {
                        value: edge.target || '',
                        onChange: e => updateEdge(i, 'target', e.target.value)
                      },
                        el('option', { value: '' }, '—'),
                        nodes.map(node => el('option', { key: node.id, value: node.id }, node.label || node.id))
                      )
                    ),
                    el('td', {}, el('input', { type: 'number', value: edge.weight || 1, onChange: e => updateEdge(i, 'weight', e.target.value), min: 0.1, step: 0.1 })),
                    el('td', {}, el(TextControl, { value: edge.label || '', onChange: v => updateEdge(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeEdge(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 5 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addEdge }, __('+ Add Edge'))
                  )
                )
              )
            )
          ),
          el(PanelBody, { title: __('Display') },
            el(ToggleControl, { label: __('Color by Group'), checked: colorByGroup, onChange: v => setAttributes({ colorByGroup: v }) }),
            el(ToggleControl, { label: __('Show Legend'), checked: showLegend, onChange: v => setAttributes({ showLegend: v }) })
          )
        ),
        el('div', { className: 'network-preview' },
          title && el('h3', {}, title),
          subtitle && el('p', {}, subtitle),
          el('div', { className: 'network-chart-preview' },
            nodes.length === 0 ? el('p', {}, __('Add nodes and edges to see preview')) :
            el('p', {}, `${nodes.length} nodes, ${edges.length} edges`)
          )
        )
      );
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

