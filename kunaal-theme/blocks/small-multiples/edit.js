/**
 * Small Multiples Block - Editor Component
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, RangeControl, Button } = components;
  const { __ } = i18n;
  const el = element.createElement;

  registerBlockType('kunaal/small-multiples', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const { title, subtitle, chartType, columns, cellAspectRatio, showAxes, sharedYScale, 
        showLegend, highlightMax, highlightMin, sourceNote, dataRows, xLabels, colorPalette } = attributes;

      const blockProps = useBlockProps({ className: 'wp-block-kunaal-small-multiples' });

      function updateRow(index, field, value) {
        const newRows = [...dataRows];
        if (!newRows[index]) newRows[index] = { label: '', values: [], color: '' };
        if (field === 'values') {
          newRows[index].values = value.split(',').map(v => parseFloat(v.trim()) || 0);
        } else {
          newRows[index][field] = value;
        }
        setAttributes({ dataRows: newRows });
      }

      function addRow() {
        setAttributes({ dataRows: [...dataRows, { label: '', values: [], color: '' }] });
      }

      function removeRow(index) {
        setAttributes({ dataRows: dataRows.filter((_, i) => i !== index) });
      }

      return el('div', blockProps,
        el(InspectorControls, {},
          el(PanelBody, { title: __('General'), initialOpen: true },
            el(TextControl, { label: __('Title'), value: title, onChange: v => setAttributes({ title: v }) }),
            el(TextControl, { label: __('Subtitle'), value: subtitle, onChange: v => setAttributes({ subtitle: v }) }),
            el(TextControl, { label: __('Source Note'), value: sourceNote, onChange: v => setAttributes({ sourceNote: v }) })
          ),
          el(PanelBody, { title: __('Chart Settings') },
            el(SelectControl, { label: __('Chart Type'), value: chartType, options: [
              { label: 'Line', value: 'line' },
              { label: 'Bar', value: 'bar' },
              { label: 'Area', value: 'area' },
              { label: 'Sparkline', value: 'sparkline' }
            ], onChange: v => setAttributes({ chartType: v }) }),
            el(RangeControl, { label: __('Columns'), value: columns, min: 2, max: 8, onChange: v => setAttributes({ columns: v }) }),
            el(SelectControl, { label: __('Aspect Ratio'), value: cellAspectRatio, options: [
              { label: '4:3', value: '4:3' },
              { label: '1:1', value: '1:1' },
              { label: '16:9', value: '16:9' },
              { label: '3:2', value: '3:2' }
            ], onChange: v => setAttributes({ cellAspectRatio: v }) }),
            el(ToggleControl, { label: __('Show Axes'), checked: showAxes, onChange: v => setAttributes({ showAxes: v }) }),
            el(ToggleControl, { label: __('Shared Y Scale'), checked: sharedYScale, onChange: v => setAttributes({ sharedYScale: v }) })
          ),
          el(PanelBody, { title: __('Data Entry'), initialOpen: true },
            el('table', { className: 'small-multiples-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('Label')),
                  el('th', {}, __('Values (comma-separated)')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                dataRows.map((row, i) =>
                  el('tr', { key: i },
                    el('td', {}, el(TextControl, { value: row.label || '', onChange: v => updateRow(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el('input', { type: 'text', value: (row.values || []).join(','), onChange: e => updateRow(i, 'values', e.target.value), placeholder: '1,2,3,4' })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeRow(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 3 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addRow }, __('+ Add Row'))
                  )
                )
              )
            )
          )
        ),
        el('div', { className: 'small-multiples-preview' },
          title && el('h3', {}, title),
          subtitle && el('p', {}, subtitle),
          el('div', { className: 'small-multiples-grid-preview', style: { '--columns': columns } },
            dataRows.length === 0 ? el('p', {}, __('Add data rows to see preview')) :
            dataRows.map((row, i) =>
              el('div', { key: i, className: 'small-multiples-cell-preview' },
                el('p', {}, row.label || __('Category')),
                el('div', { className: 'chart-preview-mini' }, chartType)
              )
            )
          )
        )
      );
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

