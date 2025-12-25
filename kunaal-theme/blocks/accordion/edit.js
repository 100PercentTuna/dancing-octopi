/**
 * Accordion Block - Editor Component
 * Uses InnerBlocks for flexible content editing
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, ToggleControl } = wp.components;
    const { __ } = wp.i18n;
    const { useState } = wp.element;
    const el = wp.element.createElement;

    registerBlockType('kunaal/accordion', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { summary, startOpen } = attributes;
            const [isOpen, setIsOpen] = useState(true); // Always open in editor for editing
            
            const blockProps = useBlockProps({
                className: 'accordion wp-block-kunaal-accordion'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Accordion Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Header Text', 'kunaal-theme'),
                            value: summary,
                            onChange: function(value) { setAttributes({ summary: value }); },
                            help: __('The clickable header that expands/collapses the content', 'kunaal-theme')
                        }),
                        el(ToggleControl, {
                            label: __('Start Expanded', 'kunaal-theme'),
                            checked: startOpen,
                            onChange: function(value) { setAttributes({ startOpen: value }); },
                            help: __('Whether the accordion is open by default on page load', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'details',
                    Object.assign({}, blockProps, { open: isOpen }),
                    el(
                        'summary',
                        { 
                            onClick: function(e) { 
                                e.preventDefault(); 
                                setIsOpen(!isOpen); 
                            },
                            style: { cursor: 'pointer' }
                        },
                        el('span', null, summary || 'Click to expand'),
                        el('span', { className: 'marker' }, '+')
                    ),
                    el(
                        'div',
                        { className: 'accBody' },
                        el(InnerBlocks, {
                            template: [['core/paragraph', { placeholder: 'Add content here...' }]],
                            templateLock: false
                        })
                    )
                )
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });
})(window.wp);
