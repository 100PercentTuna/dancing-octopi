/**
 * Heatmap Block - Editor Component
 * Provides table-based data entry (NO JSON)
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, RangeControl, Button, ButtonGroup } = components;
  const { __ } = i18n;
  const el = element.createElement;
  const { useState, useEffect } = element;

  registerBlockType('kunaal/heatmap', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const {
        title,
        subtitle,
        rowLabels,
        columnLabels,
        data,
        colorScale,
        customColorLow,
        customColorHigh,
        customColorMid,
        showValues,
        valueFormat,
        showLegend,
        legendPosition,
        cellSize,
        rotateColumnLabels,
        sourceNote
      } = attributes;

      const blockProps = useBlockProps({
        className: 'wp-block-kunaal-heatmap'
      });

      // Initialize data structure if empty
      useEffect(() => {
        if (data.length === 0 && rowLabels.length > 0 && columnLabels.length > 0) {
          const initialData = rowLabels.map(() => 
            columnLabels.map(() => 0)
          );
          setAttributes({ data: initialData });
        }
      }, [rowLabels.length, columnLabels.length]);

      // Update data when dimensions change
      useEffect(() => {
        if (rowLabels.length !== data.length || columnLabels.length !== (data[0]?.length || 0)) {
          const newData = rowLabels.map((row, i) => 
            columnLabels.map((col, j) => data[i]?.[j] ?? 0)
          );
          setAttributes({ data: newData });
        }
      }, [rowLabels.length, columnLabels.length]);

      function updateRowLabel(index, value) {
        const newLabels = [...rowLabels];
        newLabels[index] = value;
        setAttributes({ rowLabels: newLabels });
      }

      function updateColumnLabel(index, value) {
        const newLabels = [...columnLabels];
        newLabels[index] = value;
        setAttributes({ columnLabels: newLabels });
      }

      function updateCellValue(rowIndex, colIndex, value) {
        const newData = data.map((row, i) => 
          row.map((cell, j) => 
            i === rowIndex && j === colIndex ? parseFloat(value) || 0 : cell
          )
        );
        setAttributes({ data: newData });
      }

      function addRow() {
        setAttributes({
          rowLabels: [...rowLabels, ''],
          data: [...data, columnLabels.map(() => 0)]
        });
      }

      function addColumn() {
        setAttributes({
          columnLabels: [...columnLabels, ''],
          data: data.map(row => [...row, 0])
        });
      }

      function removeRow(index) {
        const newLabels = rowLabels.filter((_, i) => i !== index);
        const newData = data.filter((_, i) => i !== index);
        setAttributes({
          rowLabels: newLabels,
          data: newData
        });
      }

      function removeColumn(index) {
        const newLabels = columnLabels.filter((_, i) => i !== index);
        const newData = data.map(row => row.filter((_, j) => j !== index));
        setAttributes({
          columnLabels: newLabels,
          data: newData
        });
      }

      function handlePaste(e) {
        e.preventDefault();
        const text = e.clipboardData.getData('text');
        const lines = text.split('\n').filter(line => line.trim());
        const rows = lines.map(line => line.split('\t').map(cell => parseFloat(cell.trim()) || 0));
        
        if (rows.length > 0 && rows[0].length > 0) {
          setAttributes({
            data: rows,
            rowLabels: rows.map((_, i) => rowLabels[i] || `Row ${i + 1}`),
            columnLabels: rows[0].map((_, j) => columnLabels[j] || `Col ${j + 1}`)
          });
        }
      }

      // Calculate min/max for legend
      const allValues = data.flat().filter(v => !isNaN(v));
      const minValue = allValues.length > 0 ? Math.min(...allValues) : 0;
      const maxValue = allValues.length > 0 ? Math.max(...allValues) : 100;

      return el('div', blockProps,
        // Inspector Controls
        el(InspectorControls, {},
          // General Settings
          el(PanelBody, { title: __('General Settings'), initialOpen: true },
            el(TextControl, {
              label: __('Title'),
              value: title,
              onChange: (value) => setAttributes({ title: value })
            }),
            el(TextControl, {
              label: __('Subtitle'),
              value: subtitle,
              onChange: (value) => setAttributes({ subtitle: value })
            }),
            el(TextControl, {
              label: __('Source Note'),
              value: sourceNote,
              onChange: (value) => setAttributes({ sourceNote: value })
            })
          ),

          // Data Entry
          el(PanelBody, { title: __('Data Entry'), initialOpen: true },
            el('div', { className: 'heatmap-editor-table-wrapper' },
              el('table', { className: 'heatmap-editor-table' },
                // Header row
                el('thead', {},
                  el('tr', {},
                    el('th', {}, ''),
                    columnLabels.map((label, j) =>
                      el('th', { key: j },
                        el(TextControl, {
                          value: label,
                          onChange: (value) => updateColumnLabel(j, value),
                          placeholder: `Col ${j + 1}`
                        }),
                        columnLabels.length > 1 && el(Button, {
                          isDestructive: true,
                          isSmall: true,
                          onClick: () => removeColumn(j)
                        }, '×')
                      )
                    ),
                    el('th', {},
                      el(Button, {
                        isPrimary: true,
                        isSmall: true,
                        onClick: addColumn
                      }, '+ Column')
                    )
                  )
                ),
                // Data rows
                el('tbody', {},
                  rowLabels.map((rowLabel, i) =>
                    el('tr', { key: i },
                      el('td', {},
                        el(TextControl, {
                          value: rowLabel,
                          onChange: (value) => updateRowLabel(i, value),
                          placeholder: `Row ${i + 1}`
                        }),
                        rowLabels.length > 1 && el(Button, {
                          isDestructive: true,
                          isSmall: true,
                          onClick: () => removeRow(i)
                        }, '×')
                      ),
                      columnLabels.map((colLabel, j) =>
                        el('td', { key: j },
                          el('input', {
                            type: 'number',
                            value: data[i]?.[j] ?? 0,
                            onChange: (e) => updateCellValue(i, j, e.target.value),
                            onPaste: handlePaste,
                            step: 'any'
                          })
                        )
                      )
                    )
                  ),
                  el('tr', {},
                    el('td', {},
                      el(Button, {
                        isPrimary: true,
                        isSmall: true,
                        onClick: addRow
                      }, '+ Row')
                    )
                  )
                )
              )
            ),
            el('p', { style: { fontSize: '12px', color: '#666', marginTop: '8px' } },
              __('Tip: Paste tab-delimited data from Excel/Google Sheets')
            )
          ),

          // Display Options
          el(PanelBody, { title: __('Display Options') },
            el(ToggleControl, {
              label: __('Show Values in Cells'),
              checked: showValues,
              onChange: (value) => setAttributes({ showValues: value })
            }),
            showValues && el(SelectControl, {
              label: __('Value Format'),
              value: valueFormat,
              options: [
                { label: 'Number', value: 'number' },
                { label: 'Percent', value: 'percent' },
                { label: '1 Decimal', value: 'decimal1' },
                { label: '2 Decimals', value: 'decimal2' }
              ],
              onChange: (value) => setAttributes({ valueFormat: value })
            }),
            el(SelectControl, {
              label: __('Cell Size'),
              value: cellSize,
              options: [
                { label: 'Auto', value: 'auto' },
                { label: 'Small', value: 'small' },
                { label: 'Medium', value: 'medium' },
                { label: 'Large', value: 'large' }
              ],
              onChange: (value) => setAttributes({ cellSize: value })
            }),
            el(ToggleControl, {
              label: __('Rotate Column Labels'),
              checked: rotateColumnLabels,
              onChange: (value) => setAttributes({ rotateColumnLabels: value })
            })
          ),

          // Color Settings
          el(PanelBody, { title: __('Colors') },
            el(SelectControl, {
              label: __('Color Scale'),
              value: colorScale,
              options: [
                { label: 'Theme (Brown)', value: 'theme' },
                { label: 'Diverging', value: 'diverging' },
                { label: 'Custom', value: 'custom' }
              ],
              onChange: (value) => setAttributes({ colorScale: value })
            }),
            colorScale === 'custom' ? el('div', {},
              el('p', { style: { marginBottom: '8px' } }, __('Low Value Color')),
              el('input', {
                type: 'color',
                value: customColorLow,
                onChange: (e) => setAttributes({ customColorLow: e.target.value })
              }),
              el('p', { style: { marginBottom: '8px', marginTop: '16px' } }, __('High Value Color')),
              el('input', {
                type: 'color',
                value: customColorHigh,
                onChange: (e) => setAttributes({ customColorHigh: e.target.value })
              })
            ) : null,
            colorScale === 'diverging' ? el('div', {},
              el('p', { style: { marginBottom: '8px' } }, __('Low Value Color')),
              el('input', {
                type: 'color',
                value: customColorLow,
                onChange: (e) => setAttributes({ customColorLow: e.target.value })
              }),
              el('p', { style: { marginBottom: '8px', marginTop: '16px' } }, __('Mid Value Color')),
              el('input', {
                type: 'color',
                value: customColorMid || '#F5F0EB',
                onChange: (e) => setAttributes({ customColorMid: e.target.value })
              }),
              el('p', { style: { marginBottom: '8px', marginTop: '16px' } }, __('High Value Color')),
              el('input', {
                type: 'color',
                value: customColorHigh,
                onChange: (e) => setAttributes({ customColorHigh: e.target.value })
              })
            ) : null
          ),

          // Legend
          el(PanelBody, { title: __('Legend') },
            el(ToggleControl, {
              label: __('Show Legend'),
              checked: showLegend,
              onChange: (value) => setAttributes({ showLegend: value })
            }),
            showLegend && el(SelectControl, {
              label: __('Legend Position'),
              value: legendPosition,
              options: [
                { label: 'Bottom', value: 'bottom' },
                { label: 'Right', value: 'right' }
              ],
              onChange: (value) => setAttributes({ legendPosition: value })
            })
          )
        ),

        // Preview
        el('div', { className: 'heatmap-preview' },
          title && el('h3', { className: 'heatmap-title' }, title),
          subtitle && el('p', { className: 'heatmap-subtitle' }, subtitle),
          el('div', { className: 'heatmap-grid-preview' },
            el('table', { className: 'heatmap-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}),
                  columnLabels.map((label, j) =>
                    el('th', { key: j, className: rotateColumnLabels ? 'rotated' : '' }, label)
                  )
                )
              ),
              el('tbody', {},
                rowLabels.map((rowLabel, i) =>
                  el('tr', { key: i },
                    el('th', {}, rowLabel),
                    columnLabels.map((colLabel, j) => {
                      const value = data[i]?.[j] ?? 0;
                      const normalized = maxValue > minValue 
                        ? (value - minValue) / (maxValue - minValue)
                        : 0;
                      const bgColor = colorScale === 'custom'
                        ? interpolateColor(customColorLow, customColorHigh, normalized)
                        : getThemeColor(normalized);
                      
                      return el('td', {
                        key: j,
                        className: `heatmap-cell cell-${cellSize}`,
                        style: { backgroundColor: bgColor }
                      }, showValues && value);
                    })
                  )
                )
              )
            )
          ),
          showLegend && el('div', { className: `heatmap-legend legend-${legendPosition}` },
            el('span', {}, minValue.toFixed(1)),
            el('div', { className: 'legend-gradient' }),
            el('span', {}, maxValue.toFixed(1))
          ),
          sourceNote && el('p', { className: 'heatmap-source' }, sourceNote)
        )
      );
    }
  });

  // Helper functions
  function interpolateColor(color1, color2, t) {
    const c1 = hexToRgb(color1);
    const c2 = hexToRgb(color2);
    const r = Math.round(c1.r + (c2.r - c1.r) * t);
    const g = Math.round(c1.g + (c2.g - c1.g) * t);
    const b = Math.round(c1.b + (c2.b - c1.b) * t);
    return `rgb(${r}, ${g}, ${b})`;
  }

  function hexToRgb(hex) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
      r: parseInt(result[1], 16),
      g: parseInt(result[2], 16),
      b: parseInt(result[3], 16)
    } : { r: 0, g: 0, b: 0 };
  }

  function getThemeColor(normalized) {
    // Theme brown scale
    const colors = [
      '#F5F0EB', '#E8DFD5', '#D4C4B5', '#B8A99A',
      '#8B7355', '#7D6B5D', '#5C4A3D'
    ];
    const index = Math.floor(normalized * (colors.length - 1));
    return colors[index] || colors[0];
  }

})(
  window.wp.blocks,
  window.wp.element,
  window.wp.blockEditor,
  window.wp.components,
  window.wp.i18n
);

