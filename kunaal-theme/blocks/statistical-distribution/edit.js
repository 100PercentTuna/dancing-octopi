/**
 * Statistical Distribution Block - Editor Component
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, Button, RadioControl } = components;
  const { __ } = i18n;
  const el = element.createElement;

  registerBlockType('kunaal/statistical-distribution', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const { title, subtitle, chartType, orientation, showMean, showOutliers, showDataPoints,
        showStatistics, valueFormat, currencySymbol, sourceNote, dataGroups, dataMode } = attributes;

      const blockProps = useBlockProps({ className: 'wp-block-kunaal-statistical-distribution' });

      function updateGroup(index, field, value) {
        const newGroups = [...dataGroups];
        if (!newGroups[index]) newGroups[index] = { label: '', values: [], color: '' };
        if (field === 'values') {
          newGroups[index].values = value.split(',').map(v => parseFloat(v.trim()) || 0);
        } else {
          newGroups[index][field] = value;
        }
        setAttributes({ dataGroups: newGroups });
      }

      function addGroup() {
        setAttributes({ dataGroups: [...dataGroups, { label: '', values: [], color: '' }] });
      }

      function removeGroup(index) {
        setAttributes({ dataGroups: dataGroups.filter((_, i) => i !== index) });
      }

      return el('div', blockProps,
        el(InspectorControls, {},
          el(PanelBody, { title: __('General'), initialOpen: true },
            el(TextControl, { label: __('Title'), value: title, onChange: v => setAttributes({ title: v }) }),
            el(TextControl, { label: __('Subtitle'), value: subtitle, onChange: v => setAttributes({ subtitle: v }) }),
            el(TextControl, { label: __('Source Note'), value: sourceNote, onChange: v => setAttributes({ sourceNote: v }) })
          ),
          el(PanelBody, { title: __('Chart Type') },
            el(RadioControl, { label: __('Type'), selected: chartType, options: [
              { label: 'Box Plot', value: 'box' },
              { label: 'Violin Plot', value: 'violin' },
              { label: 'Combo', value: 'combo' }
            ], onChange: v => setAttributes({ chartType: v }) }),
            el(RadioControl, { label: __('Orientation'), selected: orientation, options: [
              { label: 'Horizontal', value: 'horizontal' },
              { label: 'Vertical', value: 'vertical' }
            ], onChange: v => setAttributes({ orientation: v }) })
          ),
          el(PanelBody, { title: __('Data Entry'), initialOpen: true },
            el(RadioControl, { label: __('Data Mode'), selected: dataMode, options: [
              { label: 'Raw Values', value: 'raw' },
              { label: 'Precomputed Stats', value: 'precomputed' }
            ], onChange: v => setAttributes({ dataMode: v }) }),
            dataMode === 'raw' && el('table', { className: 'stat-dist-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('Label')),
                  el('th', {}, __('Values (comma-separated)')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                dataGroups.map((group, i) =>
                  el('tr', { key: i },
                    el('td', {}, el(TextControl, { value: group.label || '', onChange: v => updateGroup(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el('input', { type: 'text', value: (group.values || []).join(','), onChange: e => updateGroup(i, 'values', e.target.value), placeholder: '1,2,3,4,5' })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeGroup(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 3 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addGroup }, __('+ Add Group'))
                  )
                )
              )
            )
          ),
          el(PanelBody, { title: __('Display') },
            el(ToggleControl, { label: __('Show Mean'), checked: showMean, onChange: v => setAttributes({ showMean: v }) }),
            el(ToggleControl, { label: __('Show Outliers'), checked: showOutliers, onChange: v => setAttributes({ showOutliers: v }) }),
            el(ToggleControl, { label: __('Show Data Points'), checked: showDataPoints, onChange: v => setAttributes({ showDataPoints: v }) }),
            el(ToggleControl, { label: __('Show Statistics Panel'), checked: showStatistics, onChange: v => setAttributes({ showStatistics: v }) }),
            el(SelectControl, { label: __('Value Format'), value: valueFormat, options: [
              { label: 'Number', value: 'number' },
              { label: 'Currency', value: 'currency' },
              { label: 'Percent', value: 'percent' },
              { label: '1 Decimal', value: 'decimal1' },
              { label: '2 Decimals', value: 'decimal2' }
            ], onChange: v => setAttributes({ valueFormat: v }) }),
            valueFormat === 'currency' && el(TextControl, { label: __('Currency Symbol'), value: currencySymbol, onChange: v => setAttributes({ currencySymbol: v }) })
          )
        ),
        el('div', { className: 'stat-dist-preview' },
          title && el('h3', {}, title),
          subtitle && el('p', {}, subtitle),
          el('div', { className: 'stat-dist-chart-preview' },
            dataGroups.length === 0 ? el('p', {}, __('Add data groups to see preview')) :
            el('p', {}, `${dataGroups.length} ${chartType === 'box' ? 'box plot' : chartType === 'violin' ? 'violin plot' : 'combo'} groups`)
          )
        )
      );
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

