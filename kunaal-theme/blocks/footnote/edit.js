/**
 * Footnote Block - Editor Component
 * Inline footnote reference with content
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText } = wp.blockEditor;
    const { __ } = wp.i18n;
    const { useEffect } = wp.element;
    const el = wp.element.createElement;

    registerBlockType('kunaal/footnote', {
        edit: function(props) {
            const { attributes, setAttributes, clientId } = props;
            const { content, footnoteId } = attributes;
            
            // Generate unique ID if not set
            useEffect(function() {
                if (!footnoteId) {
                    setAttributes({ footnoteId: 'fn-' + clientId.substring(0, 8) });
                }
            }, []);
            
            const blockProps = useBlockProps({
                className: 'footnote-editor-wrapper'
            });

            return el(
                'span',
                blockProps,
                el('sup', { className: 'footnote-ref-preview' }, '[n]'),
                el(
                    'span',
                    { className: 'footnote-content-preview' },
                    el(RichText, {
                        tagName: 'span',
                        value: content,
                        onChange: function(value) { setAttributes({ content: value }); },
                        placeholder: __('Footnote content...', 'kunaal-theme'),
                        allowedFormats: ['core/bold', 'core/italic', 'core/link']
                    })
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
})(window.wp);

