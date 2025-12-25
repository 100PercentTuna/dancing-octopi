/**
 * Aside Block - Editor Component
 * Uses InnerBlocks for flexible content
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/aside', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { outcome } = attributes;
            
            const blockProps = useBlockProps({
                className: 'aside reveal'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Aside Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Outcome/Result', 'kunaal-theme'),
                            value: outcome,
                            onChange: function(value) { setAttributes({ outcome: value }); },
                            help: __('Optional: Key outcome or result to highlight', 'kunaal-theme'),
                            placeholder: __('e.g., "Increased conversion by 25%"', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(InnerBlocks, {
                        template: [['core/paragraph', { placeholder: 'Aside content goes here...' }]],
                        templateLock: false
                    }),
                    outcome ? el('div', { className: 'outcome' }, 'Result: ', el('strong', null, outcome)) : null
                )
            );
        },
        save: function() {
            return null; // Dynamic block - rendered by PHP
        }
    });
})(window.wp);

