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
                className: 'accordion'
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
                    'div',
                    blockProps,
                    el(
                        'div',
                        { className: 'accordion-header', style: { 
                            display: 'flex', 
                            justifyContent: 'space-between', 
                            alignItems: 'center',
                            padding: '12px 16px',
                            background: '#f8f8f8',
                            borderBottom: '1px solid #e0e0e0',
                            cursor: 'pointer'
                        }},
                        el('span', { style: { fontWeight: 600 } }, summary || 'Click to expand'),
                        el('span', { style: { color: '#999' } }, isOpen ? '−' : '+')
                    ),
                    el(
                        'div',
                        { className: 'accordion-content', style: { padding: '16px' } },
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
