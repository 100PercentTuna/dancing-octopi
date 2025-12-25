/**
 * Chart Block - Editor (v2.0)
 * Comprehensive chart block supporting multiple chart types:
 * - Bar (vertical/horizontal, simple/stacked/clustered)
 * - Line (single/multi-series)
 * - Pie / Donut
 * - Waterfall (build-up/build-down)
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl, TextareaControl, ToggleControl, RangeControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/chart', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                chartType, orientation, barMode, title, 
                data, data2, data3, labels, seriesLabels,
                source, caption, showLegend, showValues, showGrid,
                colors, startValue, unit, unitPosition
            } = attributes;
            
            const blockProps = useBlockProps({
                className: 'chart chart-' + chartType + ' chart-' + orientation + ' chart-mode-' + barMode
            });
            
            // Parse data strings into arrays
            const parseData = function(dataStr) {
                if (!dataStr) return [];
                return dataStr.split(',').map(function(v) { return parseFloat(v.trim()) || 0; });
            };
            
            const dataArray = parseData(data);
            const dataArray2 = parseData(data2);
            const dataArray3 = parseData(data3);
            const labelsArray = labels ? labels.split(',').map(function(l) { return l.trim(); }) : [];
            const seriesLabelsArray = seriesLabels ? seriesLabels.split(',').map(function(l) { return l.trim(); }) : ['Series 1', 'Series 2', 'Series 3'];

            // Chart type descriptions
            const chartTypeHelp = {
                'bar': 'Classic bar chart. Choose vertical (upright) or horizontal (sideways) orientation.',
                'stacked-bar': 'Bars stacked on top of each other showing composition. Enter multiple data series.',
                'clustered-bar': 'Bars grouped side-by-side for comparison. Enter multiple data series.',
                'line': 'Line chart showing trends over time. Can show multiple series.',
                'pie': 'Circular chart showing proportions. Good for 3-7 categories.',
                'donut': 'Pie chart with center cutout. Modern look with space for central label.',
                'waterfall': 'Shows cumulative effect of sequential values. Great for financial analysis.'
            };

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    // Chart Type Panel
                    el(
                        PanelBody,
                        { title: __('Chart Type', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Chart Type', 'kunaal-theme'),
                            value: chartType,
                            options: [
                                { label: '📊 Bar Chart', value: 'bar' },
                                { label: '📊 Stacked Bar', value: 'stacked-bar' },
                                { label: '📊 Clustered Bar', value: 'clustered-bar' },
                                { label: '📈 Line Chart', value: 'line' },
                                { label: '🥧 Pie Chart', value: 'pie' },
                                { label: '🍩 Donut Chart', value: 'donut' },
                                { label: '📉 Waterfall', value: 'waterfall' }
                            ],
                            onChange: function(value) { setAttributes({ chartType: value }); },
                            help: chartTypeHelp[chartType] || ''
                        }),
                        (chartType === 'bar' || chartType === 'stacked-bar' || chartType === 'clustered-bar') ? 
                            el(SelectControl, {
                                label: __('Orientation', 'kunaal-theme'),
                                value: orientation,
                                options: [
                                    { label: '↕ Vertical (Upright)', value: 'vertical' },
                                    { label: '↔ Horizontal (Sideways)', value: 'horizontal' }
                                ],
                                onChange: function(value) { setAttributes({ orientation: value }); }
                            }) : null
                    ),
                    // Data Panel
                    el(
                        PanelBody,
                        { title: __('Data', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Chart Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); },
                            placeholder: 'e.g., Revenue by Quarter'
                        }),
                        el(TextareaControl, {
                            label: chartType === 'waterfall' ? __('Values (changes)', 'kunaal-theme') : __('Data Series 1', 'kunaal-theme'),
                            value: data,
                            onChange: function(value) { setAttributes({ data: value }); },
                            help: chartType === 'waterfall' 
                                ? __('Enter positive/negative values (e.g., 100, 30, -15, 20)', 'kunaal-theme')
                                : __('Enter numbers separated by commas (e.g., 10, 20, 30, 40)', 'kunaal-theme'),
                            rows: 2
                        }),
                        (chartType === 'stacked-bar' || chartType === 'clustered-bar' || chartType === 'line') ?
                            el(TextareaControl, {
                                label: __('Data Series 2 (optional)', 'kunaal-theme'),
                                value: data2,
                                onChange: function(value) { setAttributes({ data2: value }); },
                                help: __('Second data series for comparison', 'kunaal-theme'),
                                rows: 2
                            }) : null,
                        (chartType === 'stacked-bar' || chartType === 'clustered-bar' || chartType === 'line') ?
                            el(TextareaControl, {
                                label: __('Data Series 3 (optional)', 'kunaal-theme'),
                                value: data3,
                                onChange: function(value) { setAttributes({ data3: value }); },
                                help: __('Third data series (optional)', 'kunaal-theme'),
                                rows: 2
                            }) : null,
                        el(TextareaControl, {
                            label: chartType === 'waterfall' ? __('Step Labels', 'kunaal-theme') : __('Category Labels', 'kunaal-theme'),
                            value: labels,
                            onChange: function(value) { setAttributes({ labels: value }); },
                            help: chartType === 'waterfall' 
                                ? __('e.g., Start, Revenue, Costs, Gains, Total', 'kunaal-theme')
                                : __('Enter labels separated by commas (e.g., Q1, Q2, Q3, Q4)', 'kunaal-theme'),
                            rows: 2
                        }),
                        (chartType === 'stacked-bar' || chartType === 'clustered-bar' || chartType === 'line') ?
                            el(TextControl, {
                                label: __('Series Names (for legend)', 'kunaal-theme'),
                                value: seriesLabels,
                                onChange: function(value) { setAttributes({ seriesLabels: value }); },
                                help: __('e.g., 2022, 2023, 2024', 'kunaal-theme')
                            }) : null,
                        chartType === 'waterfall' ?
                            el(TextControl, {
                                label: __('Starting Value', 'kunaal-theme'),
                                type: 'number',
                                value: startValue,
                                onChange: function(value) { setAttributes({ startValue: parseFloat(value) || 0 }); },
                                help: __('Base value to start the waterfall from', 'kunaal-theme')
                            }) : null
                    ),
                    // Display Options Panel
                    el(
                        PanelBody,
                        { title: __('Display Options', 'kunaal-theme'), initialOpen: false },
                        el(TextControl, {
                            label: __('Value Unit', 'kunaal-theme'),
                            value: unit,
                            onChange: function(value) { setAttributes({ unit: value }); },
                            placeholder: 'e.g., $, %, M, K'
                        }),
                        unit ? el(SelectControl, {
                            label: __('Unit Position', 'kunaal-theme'),
                            value: unitPosition,
                            options: [
                                { label: 'After value (10K)', value: 'suffix' },
                                { label: 'Before value ($10)', value: 'prefix' }
                            ],
                            onChange: function(value) { setAttributes({ unitPosition: value }); }
                        }) : null,
                        el(ToggleControl, {
                            label: __('Show Values on Chart', 'kunaal-theme'),
                            checked: showValues,
                            onChange: function(value) { setAttributes({ showValues: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Legend', 'kunaal-theme'),
                            checked: showLegend,
                            onChange: function(value) { setAttributes({ showLegend: value }); }
                        }),
                        (chartType === 'bar' || chartType === 'line' || chartType === 'stacked-bar' || chartType === 'clustered-bar') ?
                            el(ToggleControl, {
                                label: __('Show Grid Lines', 'kunaal-theme'),
                                checked: showGrid,
                                onChange: function(value) { setAttributes({ showGrid: value }); }
                            }) : null
                    ),
                    // Style Panel
                    el(
                        PanelBody,
                        { title: __('Colors & Style', 'kunaal-theme'), initialOpen: false },
                        el(SelectControl, {
                            label: __('Color Scheme', 'kunaal-theme'),
                            value: colors,
                            options: [
                                { label: '🎨 Theme Colors (Blue/Warm)', value: 'theme' },
                                { label: '💙 Blue Gradient', value: 'blue' },
                                { label: '🔥 Warm Tones', value: 'warm' },
                                { label: '🌿 Green/Nature', value: 'green' },
                                { label: '⬛ Monochrome', value: 'mono' },
                                { label: '🌈 Rainbow', value: 'rainbow' }
                            ],
                            onChange: function(value) { setAttributes({ colors: value }); }
                        }),
                        el(TextControl, {
                            label: __('Source', 'kunaal-theme'),
                            value: source,
                            onChange: function(value) { setAttributes({ source: value }); },
                            placeholder: 'e.g., Company Financial Report 2024'
                        }),
                        el(TextControl, {
                            label: __('Caption', 'kunaal-theme'),
                            value: caption,
                            onChange: function(value) { setAttributes({ caption: value }); },
                            placeholder: 'Additional notes about the data...'
                        })
                    )
                ),
                // Editor Preview
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
                            dataArray.length > 0 ? 
                                el('div', { className: 'chart-preview-info' },
                                    el('span', { className: 'chart-type-badge' }, 
                                        chartType === 'bar' ? '📊 Bar Chart' :
                                        chartType === 'stacked-bar' ? '📊 Stacked Bar' :
                                        chartType === 'clustered-bar' ? '📊 Clustered Bar' :
                                        chartType === 'line' ? '📈 Line Chart' :
                                        chartType === 'pie' ? '🥧 Pie Chart' :
                                        chartType === 'donut' ? '🍩 Donut Chart' :
                                        chartType === 'waterfall' ? '📉 Waterfall' : 'Chart'
                                    ),
                                    el('p', { className: 'chart-data-summary' }, 
                                        dataArray.length + ' data points' + 
                                        (dataArray2.length > 0 ? ', ' + dataArray2.length + ' in series 2' : '') +
                                        (dataArray3.length > 0 ? ', ' + dataArray3.length + ' in series 3' : '')
                                    ),
                                    orientation && (chartType === 'bar' || chartType === 'stacked-bar' || chartType === 'clustered-bar') ?
                                        el('p', { className: 'chart-orientation' }, 
                                            orientation === 'horizontal' ? '↔ Horizontal' : '↕ Vertical'
                                        ) : null
                                ) :
                                el('p', { className: 'chart-placeholder' }, 
                                    '📊 Enter data values in the sidebar to generate chart'
                                )
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
