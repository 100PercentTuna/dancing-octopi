/**
 * Pullquote Block - Editor Component
 * Uses WordPress global dependencies (no build required)
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText, InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/pullquote', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { quote, citation, size } = attributes;
            
            const blockProps = useBlockProps({
                className: 'pullquote' + (size === 'large' ? ' pullquote--large' : '')
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Quote Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Size', 'kunaal-theme'),
                            value: size,
                            options: [
                                { label: __('Normal', 'kunaal-theme'), value: 'normal' },
                                { label: __('Large', 'kunaal-theme'), value: 'large' }
                            ],
                            onChange: function(value) { setAttributes({ size: value }); }
                        }),
                        el(TextControl, {
                            label: __('Citation', 'kunaal-theme'),
                            value: citation,
                            onChange: function(value) { setAttributes({ citation: value }); },
                            placeholder: __('Author or source name', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'blockquote',
                    blockProps,
                    el(RichText, {
                        tagName: 'p',
                        value: quote,
                        onChange: function(value) { setAttributes({ quote: value }); },
                        placeholder: __('Write the quote...', 'kunaal-theme'),
                        allowedFormats: ['core/bold', 'core/italic']
                    }),
                    citation ? el('cite', null, '— ' + citation) : null
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);
