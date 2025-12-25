/**
 * Section Header Block - Editor Component
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, RangeControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/section-header', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, number, level } = attributes;
            
            const blockProps = useBlockProps({
                className: 'sectionHead reveal'
            });

            const HeadingTag = 'h' + level;

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Section Header Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Section Number', 'kunaal-theme'),
                            value: number,
                            onChange: function(value) { setAttributes({ number: value }); },
                            help: __('Number shown on the right (e.g., "01", "02", "I", "II")', 'kunaal-theme')
                        }),
                        el(RangeControl, {
                            label: __('Heading Level', 'kunaal-theme'),
                            value: level,
                            onChange: function(value) { setAttributes({ level: value }); },
                            min: 1,
                            max: 6,
                            help: __('HTML heading level (h1-h6)', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(
                        HeadingTag,
                        { className: 'wp-block-heading' },
                        el('input', {
                            type: 'text',
                            value: title,
                            onChange: function(e) { setAttributes({ title: e.target.value }); },
                            placeholder: __('Section title...', 'kunaal-theme'),
                            style: {
                                background: 'transparent',
                                border: 'none',
                                fontSize: 'inherit',
                                fontFamily: 'inherit',
                                fontWeight: 'inherit',
                                color: 'inherit',
                                width: '100%',
                                padding: 0,
                                margin: 0
                            }
                        })
                    ),
                    el('span', { className: 'sectionNum' }, number)
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
})(window.wp);

