/**
 * Citation Block - Editor Component
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/citation', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { quote, author } = attributes;
            
            const blockProps = useBlockProps({
                className: 'citation reveal'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Citation Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Author', 'kunaal-theme'),
                            value: author,
                            onChange: function(value) { setAttributes({ author: value }); },
                            placeholder: __('Author name', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(
                        'blockquote',
                        { className: 'wp-block-quote' },
                        el(RichText, {
                            tagName: 'p',
                            value: quote,
                            onChange: function(value) { setAttributes({ quote: value }); },
                            placeholder: __('Quote text...', 'kunaal-theme'),
                            allowedFormats: ['core/bold', 'core/italic']
                        })
                    ),
                    author ? el('div', { className: 'author' }, '— ' + author) : null
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
})(window.wp);

