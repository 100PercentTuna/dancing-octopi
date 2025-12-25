/**
 * Flow Chart Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/flowchart', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, orientation } = attributes;
            
            const blockProps = useBlockProps({
                className: 'flowchart flowchart-' + orientation
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Flow Chart Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        el(SelectControl, {
                            label: __('Orientation', 'kunaal-theme'),
                            value: orientation,
                            options: [
                                { label: 'Horizontal', value: 'horizontal' },
                                { label: 'Vertical', value: 'vertical' }
                            ],
                            onChange: function(value) { setAttributes({ orientation: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    title ? el('h3', { className: 'fc-title' }, title) : null,
                    el(
                        'div',
                        { className: 'fc-steps' },
                        el(InnerBlocks, {
                            allowedBlocks: ['kunaal/flowchart-step'],
                            template: [
                                ['kunaal/flowchart-step', { label: 'Step 1' }],
                                ['kunaal/flowchart-step', { label: 'Step 2' }],
                                ['kunaal/flowchart-step', { label: 'Step 3' }]
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

