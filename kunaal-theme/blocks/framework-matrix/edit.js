/**
 * Framework Matrix Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const { useEffect } = wp.element;
    const el = wp.element.createElement;

    registerBlockType('kunaal/framework-matrix', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, size, xAxisLabel, yAxisLabel, cells } = attributes;
            
            const gridSize = size === '3x3' ? 3 : 2;
            const totalCells = gridSize * gridSize;
            
            // Initialize cells if needed
            useEffect(function() {
                if (cells.length !== totalCells) {
                    const newCells = [];
                    for (var i = 0; i < totalCells; i++) {
                        newCells.push({ label: '', content: '' });
                    }
                    setAttributes({ cells: newCells });
                }
            }, [size]);
            
            const updateCell = function(index, field, value) {
                const updated = [...cells];
                if (updated[index]) {
                    updated[index] = { ...updated[index], [field]: value };
                    setAttributes({ cells: updated });
                }
            };
            
            const blockProps = useBlockProps({
                className: 'framework-matrix matrix-' + size
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Matrix Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        el(SelectControl, {
                            label: __('Matrix Size', 'kunaal-theme'),
                            value: size,
                            options: [
                                { label: '2×2', value: '2x2' },
                                { label: '3×3', value: '3x3' }
                            ],
                            onChange: function(value) { setAttributes({ size: value }); }
                        }),
                        el(TextControl, {
                            label: __('X Axis Label', 'kunaal-theme'),
                            value: xAxisLabel,
                            onChange: function(value) { setAttributes({ xAxisLabel: value }); }
                        }),
                        el(TextControl, {
                            label: __('Y Axis Label', 'kunaal-theme'),
                            value: yAxisLabel,
                            onChange: function(value) { setAttributes({ yAxisLabel: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    title ? el('h3', { className: 'fm-title' }, title) : null,
                    el('div', { className: 'fm-y-label' }, yAxisLabel),
                    el(
                        'div',
                        { className: 'fm-grid', style: { gridTemplateColumns: 'repeat(' + gridSize + ', 1fr)' } },
                        cells.slice(0, totalCells).map(function(cell, index) {
                            return el(
                                'div',
                                { key: index, className: 'fm-cell' },
                                el(RichText, {
                                    tagName: 'span',
                                    className: 'fm-cell-label',
                                    value: cell.label,
                                    onChange: function(value) { updateCell(index, 'label', value); },
                                    placeholder: __('Label...', 'kunaal-theme')
                                }),
                                el(RichText, {
                                    tagName: 'p',
                                    className: 'fm-cell-content',
                                    value: cell.content,
                                    onChange: function(value) { updateCell(index, 'content', value); },
                                    placeholder: __('Content...', 'kunaal-theme')
                                })
                            );
                        })
                    ),
                    el('div', { className: 'fm-x-label' }, xAxisLabel)
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

