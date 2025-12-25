/**
 * Citation Block - Editor Component
 * Centered elegant block quote
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
            const { quote, author, sourceText, sourceUrl } = attributes;
            
            const blockProps = useBlockProps({
                className: 'citation'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Citation Details', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Author', 'kunaal-theme'),
                            value: author,
                            onChange: function(value) { setAttributes({ author: value }); },
                            placeholder: __('Author name', 'kunaal-theme')
                        }),
                        el(TextControl, {
                            label: __('Source Text', 'kunaal-theme'),
                            value: sourceText,
                            onChange: function(value) { setAttributes({ sourceText: value }); },
                            placeholder: __('e.g., "Book Title" or "Article Name"', 'kunaal-theme')
                        }),
                        el(TextControl, {
                            label: __('Source URL', 'kunaal-theme'),
                            value: sourceUrl,
                            onChange: function(value) { setAttributes({ sourceUrl: value }); },
                            placeholder: __('https://...', 'kunaal-theme'),
                            type: 'url'
                        })
                    )
                ),
                el(
                    'blockquote',
                    blockProps,
                    el(RichText, {
                        tagName: 'p',
                        className: 'citation-quote',
                        value: quote,
                        onChange: function(value) { setAttributes({ quote: value }); },
                        placeholder: __('Enter quote...', 'kunaal-theme'),
                        allowedFormats: ['core/bold', 'core/italic']
                    }),
                    (author || sourceText) ? el(
                        'footer',
                        { className: 'citation-footer' },
                        author ? el('cite', { className: 'citation-author' }, author) : null,
                        sourceText ? el(
                            'span',
                            { className: 'citation-source' },
                            sourceUrl 
                                ? el('a', { href: sourceUrl, target: '_blank', rel: 'noopener' }, sourceText)
                                : sourceText
                        ) : null
                    ) : null
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
})(window.wp);
