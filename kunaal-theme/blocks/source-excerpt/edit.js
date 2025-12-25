/**
 * Primary Source Excerpt Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/source-excerpt', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { content, source, sourceType, date, sourceUrl } = attributes;
            
            const blockProps = useBlockProps({
                className: 'source-excerpt source-' + sourceType
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Source Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Source Type', 'kunaal-theme'),
                            value: sourceType,
                            options: [
                                { label: 'Document', value: 'document' },
                                { label: 'Letter', value: 'letter' },
                                { label: 'Transcript', value: 'transcript' },
                                { label: 'Report', value: 'report' },
                                { label: 'Legal', value: 'legal' }
                            ],
                            onChange: function(value) { setAttributes({ sourceType: value }); }
                        }),
                        el(TextControl, {
                            label: __('Source Name', 'kunaal-theme'),
                            value: source,
                            onChange: function(value) { setAttributes({ source: value }); },
                            placeholder: __('e.g., "Federalist Papers, No. 10"', 'kunaal-theme')
                        }),
                        el(TextControl, {
                            label: __('Date', 'kunaal-theme'),
                            value: date,
                            onChange: function(value) { setAttributes({ date: value }); },
                            placeholder: __('e.g., "November 22, 1787"', 'kunaal-theme')
                        }),
                        el(TextControl, {
                            label: __('Source URL (Optional)', 'kunaal-theme'),
                            value: sourceUrl,
                            onChange: function(value) { setAttributes({ sourceUrl: value }); },
                            type: 'url'
                        })
                    )
                ),
                el(
                    'blockquote',
                    blockProps,
                    el('div', { className: 'source-type-label' }, sourceType.toUpperCase()),
                    el(RichText, {
                        tagName: 'div',
                        className: 'source-content',
                        value: content,
                        onChange: function(value) { setAttributes({ content: value }); },
                        placeholder: __('Paste the primary source excerpt here...', 'kunaal-theme')
                    }),
                    el(
                        'footer',
                        { className: 'source-attribution' },
                        source ? el('cite', null, source) : null,
                        date ? el('span', { className: 'source-date' }, date) : null
                    )
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

