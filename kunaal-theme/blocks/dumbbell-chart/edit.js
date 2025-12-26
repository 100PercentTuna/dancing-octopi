/**
 * Dumbbell Chart Block - Editor Component
 * Table-based data entry (NO JSON)
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, Button } = components;
  const { __ } = i18n;
  const el = element.createElement;
  const { useState } = element;

  registerBlockType('kunaal/dumbbell-chart', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const {
        title, subtitle, startLabel, endLabel, showGapAnnotation,
        gapPrefix, gapSuffix, showAxis, valueFormat, currencySymbol,
        sortBy, sortOrder, rowHeight, colorMode, startColor, endColor,
        showLegend, sourceNote, dataRows
      } = attributes;

      const blockProps = useBlockProps({ className: 'wp-block-kunaal-dumbbell-chart' });

      function updateRow(index, field, value) {
        const newRows = [...dataRows];
        if (!newRows[index]) {
          newRows[index] = { category: '', startValue: 0, endValue: 0 };
        }
        newRows[index][field] = field.includes('Value') ? parseFloat(value) || 0 : value;
        setAttributes({ dataRows: newRows });
      }

      function addRow() {
        setAttributes({ dataRows: [...dataRows, { category: '', startValue: 0, endValue: 0 }] });
      }

      function removeRow(index) {
        setAttributes({ dataRows: dataRows.filter((_, i) => i !== index) });
      }

      // Calculate min/max for axis
      const allValues = dataRows.flatMap(r => [r.startValue || 0, r.endValue || 0]);
      const minVal = allValues.length > 0 ? Math.min(...allValues) : 0;
      const maxVal = allValues.length > 0 ? Math.max(...allValues) : 100;

      return el('div', blockProps,
        el(InspectorControls, {},
          el(PanelBody, { title: __('General'), initialOpen: true },
            el(TextControl, { label: __('Title'), value: title, onChange: v => setAttributes({ title: v }) }),
            el(TextControl, { label: __('Subtitle'), value: subtitle, onChange: v => setAttributes({ subtitle: v }) }),
            el(TextControl, { label: __('Source Note'), value: sourceNote, onChange: v => setAttributes({ sourceNote: v }) })
          ),
          el(PanelBody, { title: __('Labels'), initialOpen: true },
            el(TextControl, { label: __('Start Label'), value: startLabel, onChange: v => setAttributes({ startLabel: v }) }),
            el(TextControl, { label: __('End Label'), value: endLabel, onChange: v => setAttributes({ endLabel: v }) })
          ),
          el(PanelBody, { title: __('Data Entry'), initialOpen: true },
            el('table', { className: 'dumbbell-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('Category')),
                  el('th', {}, startLabel),
                  el('th', {}, endLabel),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                dataRows.map((row, i) =>
                  el('tr', { key: i },
                    el('td', {},
                      el(TextControl, {
                        value: row.category || '',
                        onChange: v => updateRow(i, 'category', v),
                        placeholder: __('Category')
                      })
                    ),
                    el('td', {},
                      el('input', {
                        type: 'number',
                        value: row.startValue || 0,
                        onChange: e => updateRow(i, 'startValue', e.target.value),
                        step: 'any'
                      })
                    ),
                    el('td', {},
                      el('input', {
                        type: 'number',
                        value: row.endValue || 0,
                        onChange: e => updateRow(i, 'endValue', e.target.value),
                        step: 'any'
                      })
                    ),
                    el('td', {},
                      el(Button, { isDestructive: true, isSmall: true, onClick: () => removeRow(i) }, '×')
                    )
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
            el(ToggleControl, { label: __('Show Gap Value'), checked: showGapAnnotation, onChange: v => setAttributes({ showGapAnnotation: v }) }),
            el(TextControl, { label: __('Gap Prefix'), value: gapPrefix, onChange: v => setAttributes({ gapPrefix: v }) }),
            el(TextControl, { label: __('Gap Suffix'), value: gapSuffix, onChange: v => setAttributes({ gapSuffix: v }) }),
            el(ToggleControl, { label: __('Show Axis'), checked: showAxis, onChange: v => setAttributes({ showAxis: v }) }),
            el(SelectControl, { label: __('Value Format'), value: valueFormat, options: [
              { label: 'Number', value: 'number' },
              { label: 'Percent', value: 'percent' },
              { label: 'Currency', value: 'currency' },
              { label: 'Compact', value: 'compact' }
            ], onChange: v => setAttributes({ valueFormat: v }) }),
            valueFormat === 'currency' && el(TextControl, { label: __('Currency Symbol'), value: currencySymbol, onChange: v => setAttributes({ currencySymbol: v }) }),
            el(SelectControl, { label: __('Row Height'), value: rowHeight, options: [
              { label: 'Compact', value: 'compact' },
              { label: 'Normal', value: 'normal' },
              { label: 'Spacious', value: 'spacious' }
            ], onChange: v => setAttributes({ rowHeight: v }) })
          ),
          el(PanelBody, { title: __('Colors') },
            el(SelectControl, { label: __('Color Mode'), value: colorMode, options: [
              { label: 'Theme', value: 'theme' },
              { label: 'Direction', value: 'direction' },
              { label: 'Custom', value: 'custom' }
            ], onChange: v => setAttributes({ colorMode: v }) }),
            colorMode === 'custom' && el('div', {},
              el('p', {}, __('Start Color')),
              el('input', { type: 'color', value: startColor, onChange: e => setAttributes({ startColor: e.target.value }) }),
              el('p', {}, __('End Color')),
              el('input', { type: 'color', value: endColor, onChange: e => setAttributes({ endColor: e.target.value }) })
            )
          )
        ),
        el('div', { className: 'dumbbell-preview' },
          title && el('h3', {}, title),
          subtitle && el('p', {}, subtitle),
          el('div', { className: 'dumbbell-chart-preview' },
            dataRows.length === 0 ? el('p', {}, __('Add data rows to see preview')) :
            dataRows.map((row, i) => {
              const gap = (row.endValue || 0) - (row.startValue || 0);
              return el('div', { key: i, className: 'dumbbell-row-preview' },
                el('span', {}, row.category || __('Category')),
                el('div', { className: 'dumbbell-connector-preview' },
                  el('span', {}, row.startValue || 0),
                  el('span', {}, '→'),
                  el('span', {}, row.endValue || 0)
                ),
                showGapAnnotation && el('span', {}, `${gapPrefix}${gap}${gapSuffix}`)
              );
            })
          )
        )
      );
    }
  });
})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.i18n
);

