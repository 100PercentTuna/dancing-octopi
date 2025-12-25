/**
 * Related Link Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText } = wp.blockEditor;
    const { TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/related-link', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { url, title, source, description } = attributes;
            
            const blockProps = useBlockProps({
                className: 'related-link'
            });

            return el(
                'div',
                blockProps,
                el(
                    'div',
                    { className: 'related-link-content' },
                    el(RichText, {
                        tagName: 'span',
                        className: 'related-link-title',
                        value: title,
                        onChange: function(value) { setAttributes({ title: value }); },
                        placeholder: __('Article title...', 'kunaal-theme')
                    }),
                    el(RichText, {
                        tagName: 'span',
                        className: 'related-link-source',
                        value: source,
                        onChange: function(value) { setAttributes({ source: value }); },
                        placeholder: __('Source / Publication', 'kunaal-theme')
                    }),
                    el(RichText, {
                        tagName: 'p',
                        className: 'related-link-desc',
                        value: description,
                        onChange: function(value) { setAttributes({ description: value }); },
                        placeholder: __('Brief description (optional)...', 'kunaal-theme')
                    })
                ),
                el(
                    'div',
                    { className: 'related-link-url' },
                    el(TextControl, {
                        value: url,
                        onChange: function(value) { setAttributes({ url: value }); },
                        placeholder: __('URL', 'kunaal-theme'),
                        type: 'url'
                    })
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

