/**
 * Insight Block - Editor Component
 * Uses WordPress global dependencies (no build required)
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/insight', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { label, content } = attributes;
            
            const blockProps = useBlockProps({
                className: 'insightBox'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Insight Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Label', 'kunaal-theme'),
                            value: label,
                            onChange: function(value) { setAttributes({ label: value }); },
                            help: __('Text shown above the insight (e.g., "Key insight", "Important", "Note")', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('div', { className: 'label' }, label),
                    el(RichText, {
                        tagName: 'div',
                        className: 'insightContent',
                        value: content,
                        onChange: function(value) { setAttributes({ content: value }); },
                        placeholder: __('Write your insight here...', 'kunaal-theme'),
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
