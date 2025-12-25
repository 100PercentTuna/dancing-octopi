/**
 * Chart Block - Editor
 * Unified chart block supporting bar, line, and pie charts
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl, TextareaControl, ToggleControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/chart', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { chartType, orientation, title, data, labels, source, caption, showLegend, colors } = attributes;
            
            const blockProps = useBlockProps({
                className: 'chart chart-' + chartType + ' chart-' + orientation
            });
            
            // Parse data string into array for preview
            const parseData = function(dataStr) {
                if (!dataStr) return [];
                return dataStr.split(',').map(function(v) { return parseFloat(v.trim()) || 0; });
            };
            
            const dataArray = parseData(data);
            const labelsArray = labels ? labels.split(',').map(function(l) { return l.trim(); }) : [];

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Chart Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Chart Type', 'kunaal-theme'),
                            value: chartType,
                            options: [
                                { label: 'Bar Chart', value: 'bar' },
                                { label: 'Line Chart', value: 'line' },
                                { label: 'Pie Chart', value: 'pie' }
                            ],
                            onChange: function(value) { setAttributes({ chartType: value }); }
                        }),
                        chartType === 'bar' ? el(SelectControl, {
                            label: __('Orientation', 'kunaal-theme'),
                            value: orientation,
                            options: [
                                { label: 'Vertical (Upright)', value: 'vertical' },
                                { label: 'Horizontal (Sideways)', value: 'horizontal' }
                            ],
                            onChange: function(value) { setAttributes({ orientation: value }); }
                        }) : null,
                        el(TextControl, {
                            label: __('Chart Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        el(TextareaControl, {
                            label: __('Data Values', 'kunaal-theme'),
                            value: data,
                            onChange: function(value) { setAttributes({ data: value }); },
                            help: __('Enter numbers separated by commas (e.g., 10, 20, 30, 40)', 'kunaal-theme'),
                            rows: 3
                        }),
                        el(TextareaControl, {
                            label: __('Labels', 'kunaal-theme'),
                            value: labels,
                            onChange: function(value) { setAttributes({ labels: value }); },
                            help: __('Enter labels separated by commas (e.g., Q1, Q2, Q3, Q4)', 'kunaal-theme'),
                            rows: 3
                        }),
                        el(TextControl, {
                            label: __('Source', 'kunaal-theme'),
                            value: source,
                            onChange: function(value) { setAttributes({ source: value }); },
                            placeholder: __('Data source...', 'kunaal-theme')
                        }),
                        el(TextControl, {
                            label: __('Caption', 'kunaal-theme'),
                            value: caption,
                            onChange: function(value) { setAttributes({ caption: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Legend', 'kunaal-theme'),
                            checked: showLegend,
                            onChange: function(value) { setAttributes({ showLegend: value }); }
                        }),
                        el(SelectControl, {
                            label: __('Color Scheme', 'kunaal-theme'),
                            value: colors,
                            options: [
                                { label: 'Theme Colors', value: 'theme' },
                                { label: 'Blue Gradient', value: 'blue' },
                                { label: 'Warm Gradient', value: 'warm' },
                                { label: 'Green Gradient', value: 'green' }
                            ],
                            onChange: function(value) { setAttributes({ colors: value }); }
                        })
                    )
                ),
                el(
                    'figure',
                    blockProps,
                    title ? el('h3', { className: 'chart-title' }, title) : null,
                    el(
                        'div',
                        { className: 'chart-container' },
                        el(
                            'div',
                            { className: 'chart-preview' },
                            dataArray.length > 0 ? el('p', { className: 'chart-preview-text' }, 
                                chartType === 'bar' ? 'Bar Chart Preview' : 
                                chartType === 'line' ? 'Line Chart Preview' : 
                                'Pie Chart Preview'
                            ) : el('p', { className: 'chart-placeholder' }, 'Enter data values to see chart preview')
                        )
                    ),
                    (caption || source) ? el(
                        'figcaption',
                        { className: 'chart-caption' },
                        caption ? el('span', { className: 'chart-caption-text' }, caption) : null,
                        source ? el('span', { className: 'chart-source' }, 'Source: ' + source) : null
                    ) : null
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

