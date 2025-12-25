/**
 * Takeaways Block - Editor Component
 * Uses InnerBlocks for flexible takeaway items
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/takeaways', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title } = attributes;
            
            const blockProps = useBlockProps({
                className: 'takeaways reveal'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Takeaways Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Section Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); },
                            help: __('Title for the takeaways section', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('h2', null, title),
                    el(
                        'ol',
                        { className: 'takeList' },
                        el(InnerBlocks, {
                            allowedBlocks: ['kunaal/takeaway-item'],
                            template: [
                                ['kunaal/takeaway-item', { heading: 'First Takeaway', description: 'Description of the first takeaway.' }],
                                ['kunaal/takeaway-item', { heading: 'Second Takeaway', description: 'Description of the second takeaway.' }],
                                ['kunaal/takeaway-item', { heading: 'Third Takeaway', description: 'Description of the third takeaway.' }]
                            ],
                            templateLock: false
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

