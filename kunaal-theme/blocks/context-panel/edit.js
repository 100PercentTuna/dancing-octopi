/**
 * Context Panel Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/context-panel', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { label, title } = attributes;
            
            const blockProps = useBlockProps({
                className: 'context-panel'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Panel Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Label', 'kunaal-theme'),
                            value: label,
                            options: [
                                { label: 'Context', value: 'Context' },
                                { label: 'Background', value: 'Background' },
                                { label: 'Why It Matters', value: 'Why It Matters' },
                                { label: 'Key Point', value: 'Key Point' },
                                { label: 'Note', value: 'Note' },
                                { label: 'Custom', value: 'custom' }
                            ],
                            onChange: function(value) { setAttributes({ label: value }); }
                        }),
                        label === 'custom' ? el(TextControl, {
                            label: __('Custom Label', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }) : null
                    )
                ),
                el(
                    'aside',
                    blockProps,
                    el('div', { className: 'context-label' }, label === 'custom' ? title : label),
                    el(
                        'div',
                        { className: 'context-content' },
                        el(InnerBlocks, {
                            allowedBlocks: ['core/paragraph', 'core/list', 'core/heading'],
                            template: [['core/paragraph', { placeholder: 'Add context...' }]],
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

