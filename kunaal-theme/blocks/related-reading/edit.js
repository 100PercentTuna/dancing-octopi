/**
 * Related Reading Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/related-reading', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title } = attributes;
            
            const blockProps = useBlockProps({
                className: 'related-reading'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Section Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        })
                    )
                ),
                el(
                    'section',
                    blockProps,
                    el('h3', { className: 'related-title' }, title),
                    el(
                        'div',
                        { className: 'related-list' },
                        el(InnerBlocks, {
                            allowedBlocks: ['kunaal/related-link'],
                            template: [
                                ['kunaal/related-link', { title: 'Article Title', source: 'Publication' }]
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

