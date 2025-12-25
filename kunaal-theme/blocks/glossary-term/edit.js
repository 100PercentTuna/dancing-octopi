/**
 * Glossary Term Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText } = wp.blockEditor;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/glossary-term', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { term, definition } = attributes;
            
            const blockProps = useBlockProps({
                className: 'glossary-term'
            });

            return el(
                'div',
                blockProps,
                el(RichText, {
                    tagName: 'dt',
                    className: 'glossary-term-title',
                    value: term,
                    onChange: function(value) { setAttributes({ term: value }); },
                    placeholder: __('Term...', 'kunaal-theme')
                }),
                el(RichText, {
                    tagName: 'dd',
                    className: 'glossary-term-definition',
                    value: definition,
                    onChange: function(value) { setAttributes({ definition: value }); },
                    placeholder: __('Definition...', 'kunaal-theme')
                })
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

