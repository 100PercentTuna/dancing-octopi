/**
 * Flow Diagram Block - Editor Component
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, RangeControl, Button } = components;
  const { __ } = i18n;
  const el = element.createElement;

  registerBlockType('kunaal/flow-diagram', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const { title, subtitle, diagramType, nodeWidth, nodePadding, linkColorMode, singleLinkColor,
        showValues, valueFormat, currencySymbol, valueUnit, columnLabels, sourceNote, nodes, links } = attributes;

      const blockProps = useBlockProps({ className: 'wp-block-kunaal-flow-diagram' });

      function updateNode(index, field, value) {
        const newNodes = [...nodes];
        if (!newNodes[index]) newNodes[index] = { id: '', label: '', column: 0, color: '' };
        newNodes[index][field] = field === 'column' ? parseInt(value) || 0 : value;
        setAttributes({ nodes: newNodes });
      }

      function addNode() {
        const newId = 'node-' + (nodes.length + 1);
        setAttributes({ nodes: [...nodes, { id: newId, label: '', column: 0, color: '' }] });
      }

      function removeNode(index) {
        const nodeId = nodes[index]?.id;
        setAttributes({
          nodes: nodes.filter((_, i) => i !== index),
          links: links.filter(l => l.source !== nodeId && l.target !== nodeId)
        });
      }

      function updateLink(index, field, value) {
        const newLinks = [...links];
        if (!newLinks[index]) newLinks[index] = { source: '', target: '', value: 0, label: '' };
        newLinks[index][field] = field === 'value' ? parseFloat(value) || 0 : value;
        setAttributes({ links: newLinks });
      }

      function addLink() {
        setAttributes({ links: [...links, { source: '', target: '', value: 0, label: '' }] });
      }

      function removeLink(index) {
        setAttributes({ links: links.filter((_, i) => i !== index) });
      }

      return el('div', blockProps,
        el(InspectorControls, {},
          el(PanelBody, { title: __('General'), initialOpen: true },
            el(TextControl, { label: __('Title'), value: title, onChange: v => setAttributes({ title: v }) }),
            el(TextControl, { label: __('Subtitle'), value: subtitle, onChange: v => setAttributes({ subtitle: v }) }),
            el(TextControl, { label: __('Source Note'), value: sourceNote, onChange: v => setAttributes({ sourceNote: v }) })
          ),
          el(PanelBody, { title: __('Diagram Type') },
            el(SelectControl, { label: __('Type'), value: diagramType, options: [
              { label: 'Sankey', value: 'sankey' },
              { label: 'Alluvial', value: 'alluvial' }
            ], onChange: v => setAttributes({ diagramType: v }) }),
            diagramType === 'alluvial' && el(TextControl, {
              label: __('Column Labels (comma-separated)'),
              value: columnLabels.join(','),
              onChange: v => setAttributes({ columnLabels: v.split(',').map(s => s.trim()) })
            })
          ),
          el(PanelBody, { title: __('Nodes'), initialOpen: true },
            el('table', { className: 'flow-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('ID')),
                  el('th', {}, __('Label')),
                  el('th', {}, __('Column')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                nodes.map((node, i) =>
                  el('tr', { key: i },
                    el('td', {}, el(TextControl, { value: node.id || '', onChange: v => updateNode(i, 'id', v), placeholder: 'node-1' })),
                    el('td', {}, el(TextControl, { value: node.label || '', onChange: v => updateNode(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el('input', { type: 'number', value: node.column || 0, onChange: e => updateNode(i, 'column', e.target.value), min: 0 })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeNode(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 4 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addNode }, __('+ Add Node'))
                  )
                )
              )
            )
          ),
          el(PanelBody, { title: __('Links') },
            el('table', { className: 'flow-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('From')),
                  el('th', {}, __('To')),
                  el('th', {}, __('Value')),
                  el('th', {}, __('Label')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                links.map((link, i) =>
                  el('tr', { key: i },
                    el('td', {},
                      el('select', {
                        value: link.source || '',
                        onChange: e => updateLink(i, 'source', e.target.value)
                      },
                        el('option', { value: '' }, '—'),
                        nodes.map(node => el('option', { key: node.id, value: node.id }, node.label || node.id))
                      )
                    ),
                    el('td', {},
                      el('select', {
                        value: link.target || '',
                        onChange: e => updateLink(i, 'target', e.target.value)
                      },
                        el('option', { value: '' }, '—'),
                        nodes.map(node => el('option', { key: node.id, value: node.id }, node.label || node.id))
                      )
                    ),
                    el('td', {}, el('input', { type: 'number', value: link.value || 0, onChange: e => updateLink(i, 'value', e.target.value), step: 'any' })),
                    el('td', {}, el(TextControl, { value: link.label || '', onChange: v => updateLink(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeLink(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 5 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addLink }, __('+ Add Link'))
                  )
                )
              )
            )
          ),
          el(PanelBody, { title: __('Display') },
            el(ToggleControl, { label: __('Show Values'), checked: showValues, onChange: v => setAttributes({ showValues: v }) }),
            el(SelectControl, { label: __('Value Format'), value: valueFormat, options: [
              { label: 'Number', value: 'number' },
              { label: 'Percent', value: 'percent' },
              { label: 'Currency', value: 'currency' },
              { label: 'Compact', value: 'compact' }
            ], onChange: v => setAttributes({ valueFormat: v }) }),
            valueFormat === 'currency' && el(TextControl, { label: __('Currency Symbol'), value: currencySymbol, onChange: v => setAttributes({ currencySymbol: v }) }),
            el(TextControl, { label: __('Unit Suffix'), value: valueUnit, onChange: v => setAttributes({ valueUnit: v }), placeholder: 'TWh, M, etc.' })
          ),
          el(PanelBody, { title: __('Colors') },
            el(SelectControl, { label: __('Link Color Mode'), value: linkColorMode, options: [
              { label: 'By Source', value: 'source' },
              { label: 'By Target', value: 'target' },
              { label: 'Gradient', value: 'gradient' },
              { label: 'Single Color', value: 'single' }
            ], onChange: v => setAttributes({ linkColorMode: v }) }),
            linkColorMode === 'single' && el('div', {},
              el('p', {}, __('Single Color')),
              el('input', { type: 'color', value: singleLinkColor, onChange: e => setAttributes({ singleLinkColor: e.target.value }) })
            )
          )
        ),
        el('div', { className: 'flow-preview' },
          title && el('h3', {}, title),
          subtitle && el('p', {}, subtitle),
          el('div', { className: 'flow-chart-preview' },
            nodes.length === 0 ? el('p', {}, __('Add nodes and links to see preview')) :
            el('p', {}, `${nodes.length} nodes, ${links.length} links`)
          )
        )
      );
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

