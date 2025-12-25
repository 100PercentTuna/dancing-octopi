/**
 * Sidenote Block - Editor Component
 * Uses WordPress global dependencies (no build required)
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/sidenote', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { marker, content } = attributes;
            
            const blockProps = useBlockProps({
                className: 'sidenote-wrapper sidenote-editor'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Sidenote Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Marker Symbol', 'kunaal-theme'),
                            value: marker,
                            onChange: function(value) { setAttributes({ marker: value }); },
                            help: __('Symbol shown in text (e.g., *, †, 1)', 'kunaal-theme'),
                            maxLength: 3
                        })
                    )
                ),
                el(
                    'span',
                    blockProps,
                    el('span', { className: 'sidenote-ref' }, marker),
                    el(
                        'span',
                        { className: 'sidenote' },
                        el(RichText, {
                            tagName: 'span',
                            value: content,
                            onChange: function(value) { setAttributes({ content: value }); },
                            placeholder: __('Add sidenote content...', 'kunaal-theme'),
                            allowedFormats: ['core/bold', 'core/italic', 'core/link']
                        })
                    )
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);
