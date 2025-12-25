/**
 * Footnotes Section Block - Editor Component
 * Placeholder that shows where footnotes will appear
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/footnotes-section', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title } = attributes;
            
            const blockProps = useBlockProps({
                className: 'footnotes-section-editor'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Footnotes Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Section Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); },
                            help: __('Title shown above footnotes list', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('h4', null, title),
                    el('p', { className: 'footnotes-placeholder' }, 
                        __('Footnotes will appear here. Add footnotes using the Footnote block in your content.', 'kunaal-theme')
                    )
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
})(window.wp);

