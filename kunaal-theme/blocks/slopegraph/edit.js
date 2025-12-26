/**
 * Slopegraph Block - Editor Component
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, Button } = components;
  const { __ } = i18n;
  const el = element.createElement;

  registerBlockType('kunaal/slopegraph', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const { title, subtitle, leftColumnLabel, rightColumnLabel, rowHeight, showPercentChange,
        showDirectionArrows, valueFormat, currencySymbol, sortBy, sortOrder, sourceNote, dataRows } = attributes;

      const blockProps = useBlockProps({ className: 'wp-block-kunaal-slopegraph' });

      function updateRow(index, field, value) {
        const newRows = [...dataRows];
        if (!newRows[index]) newRows[index] = { label: '', leftValue: 0, rightValue: 0 };
        newRows[index][field] = field.includes('Value') ? parseFloat(value) || 0 : value;
        setAttributes({ dataRows: newRows });
      }

      function addRow() {
        setAttributes({ dataRows: [...dataRows, { label: '', leftValue: 0, rightValue: 0 }] });
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
          el(PanelBody, { title: __('Columns') },
            el(TextControl, { label: __('Left Column'), value: leftColumnLabel, onChange: v => setAttributes({ leftColumnLabel: v }) }),
            el(TextControl, { label: __('Right Column'), value: rightColumnLabel, onChange: v => setAttributes({ rightColumnLabel: v }) })
          ),
          el(PanelBody, { title: __('Data Entry'), initialOpen: true },
            el('table', { className: 'slopegraph-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('Label')),
                  el('th', {}, leftColumnLabel),
                  el('th', {}, rightColumnLabel),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                dataRows.map((row, i) =>
                  el('tr', { key: i },
                    el('td', {}, el(TextControl, { value: row.label || '', onChange: v => updateRow(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el('input', { type: 'number', value: row.leftValue || 0, onChange: e => updateRow(i, 'leftValue', e.target.value), step: 'any' })),
                    el('td', {}, el('input', { type: 'number', value: row.rightValue || 0, onChange: e => updateRow(i, 'rightValue', e.target.value), step: 'any' })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeRow(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 4 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addRow }, __('+ Add Row'))
                  )
                )
              )
            )
          ),
          el(PanelBody, { title: __('Display') },
            el(SelectControl, { label: __('Value Format'), value: valueFormat, options: [
              { label: 'Number', value: 'number' },
              { label: 'Percent', value: 'percent' },
              { label: 'Currency', value: 'currency' },
              { label: '1 Decimal', value: 'decimal1' },
              { label: '2 Decimals', value: 'decimal2' }
            ], onChange: v => setAttributes({ valueFormat: v }) }),
            valueFormat === 'currency' && el(TextControl, { label: __('Currency Symbol'), value: currencySymbol, onChange: v => setAttributes({ currencySymbol: v }) }),
            el(SelectControl, { label: __('Row Height'), value: rowHeight, options: [
              { label: 'Compact', value: 'compact' },
              { label: 'Normal', value: 'normal' },
              { label: 'Spacious', value: 'spacious' }
            ], onChange: v => setAttributes({ rowHeight: v }) }),
            el(ToggleControl, { label: __('Show % Change'), checked: showPercentChange, onChange: v => setAttributes({ showPercentChange: v }) }),
            el(ToggleControl, { label: __('Show Direction Arrows'), checked: showDirectionArrows, onChange: v => setAttributes({ showDirectionArrows: v }) })
          )
        ),
        el('div', { className: 'slopegraph-preview' },
          title && el('h3', {}, title),
          subtitle && el('p', {}, subtitle),
          el('div', { className: 'slopegraph-chart-preview' },
            dataRows.length === 0 ? el('p', {}, __('Add data rows to see preview')) :
            dataRows.map((row, i) => {
              const change = row.rightValue - row.leftValue;
              const pct = row.leftValue ? ((change / row.leftValue) * 100).toFixed(1) : '0';
              return el('div', { key: i, className: 'slopegraph-row-preview' },
                el('span', {}, row.label || __('Label')),
                el('span', {}, `${row.leftValue || 0} → ${row.rightValue || 0}`),
                showPercentChange && el('span', {}, `${change >= 0 ? '+' : ''}${pct}%`)
              );
            })
          )
        )
      );
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

