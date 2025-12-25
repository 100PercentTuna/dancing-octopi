/**
 * Inline Annotation Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, SelectControl, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/annotation', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { text, note, highlightColor } = attributes;
            
            const blockProps = useBlockProps({
                className: 'annotation annotation-' + highlightColor
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Annotation Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Highlight Color', 'kunaal-theme'),
                            value: highlightColor,
                            options: [
                                { label: 'Yellow', value: 'yellow' },
                                { label: 'Blue', value: 'blue' },
                                { label: 'Green', value: 'green' },
                                { label: 'Pink', value: 'pink' }
                            ],
                            onChange: function(value) { setAttributes({ highlightColor: value }); }
                        }),
                        el(TextareaControl, {
                            label: __('Annotation Note', 'kunaal-theme'),
                            value: note,
                            onChange: function(value) { setAttributes({ note: value }); },
                            help: __('This note appears when hovering/clicking the highlighted text.', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'span',
                    blockProps,
                    el(RichText, {
                        tagName: 'mark',
                        className: 'annotation-text',
                        value: text,
                        onChange: function(value) { setAttributes({ text: value }); },
                        placeholder: __('Highlighted text...', 'kunaal-theme')
                    }),
                    note ? el('span', { className: 'annotation-note-preview' }, note) : null
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

