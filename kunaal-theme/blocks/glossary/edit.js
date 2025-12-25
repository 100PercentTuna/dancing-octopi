/**
 * Glossary Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/glossary', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title } = attributes;
            
            const blockProps = useBlockProps({
                className: 'glossary'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Glossary Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('h3', { className: 'glossary-title' }, title),
                    el(
                        'dl',
                        { className: 'glossary-list' },
                        el(InnerBlocks, {
                            allowedBlocks: ['kunaal/glossary-term'],
                            template: [
                                ['kunaal/glossary-term', { term: 'Term', definition: 'Definition here...' }]
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

