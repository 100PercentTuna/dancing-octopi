/**
 * Takeaway Item Block - Editor Component
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/takeaway-item', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { heading, description } = attributes;
            
            const blockProps = useBlockProps({
                className: 'reveal'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Takeaway Item Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Heading', 'kunaal-theme'),
                            value: heading,
                            onChange: function(value) { setAttributes({ heading: value }); },
                            help: __('Short heading for this takeaway', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'li',
                    blockProps,
                    el(
                        'div',
                        null,
                        heading ? el('h4', null, heading) : null,
                        el(RichText, {
                            tagName: 'p',
                            value: description,
                            onChange: function(value) { setAttributes({ description: value }); },
                            placeholder: __('Description of this takeaway...', 'kunaal-theme'),
                            allowedFormats: ['core/bold', 'core/italic', 'core/link']
                        })
                    )
                )
            );
        },
        save: function() {
            return null; // Dynamic block - rendered by PHP
        }
    });
})(window.wp);
