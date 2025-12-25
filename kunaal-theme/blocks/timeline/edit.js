/**
 * Timeline Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/timeline', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, orientation } = attributes;
            
            const blockProps = useBlockProps({
                className: 'timeline timeline-' + orientation
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Timeline Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title (Optional)', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        el(SelectControl, {
                            label: __('Orientation', 'kunaal-theme'),
                            value: orientation,
                            options: [
                                { label: 'Vertical', value: 'vertical' },
                                { label: 'Horizontal', value: 'horizontal' }
                            ],
                            onChange: function(value) { setAttributes({ orientation: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    title ? el('h3', { className: 'timeline-title' }, title) : null,
                    el(
                        'div',
                        { className: 'timeline-events' },
                        el(InnerBlocks, {
                            allowedBlocks: ['kunaal/timeline-item'],
                            template: [
                                ['kunaal/timeline-item', { date: '2020', title: 'Event One' }],
                                ['kunaal/timeline-item', { date: '2021', title: 'Event Two' }],
                                ['kunaal/timeline-item', { date: '2022', title: 'Event Three' }]
                            ],
                            templateLock: false
                        })
                    )
                )
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });
})(window.wp);

