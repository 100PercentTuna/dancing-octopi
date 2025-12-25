/**
 * Aside Block - Editor Component
 * Callout box with label selector and outcome
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    const labelTypes = [
        { label: 'No Label', value: 'none' },
        { label: 'Case Study', value: 'case-study' },
        { label: 'Example', value: 'example' },
        { label: 'Note', value: 'note' },
        { label: 'Sidebar', value: 'sidebar' },
        { label: 'Definition', value: 'definition' },
        { label: 'Warning', value: 'warning' },
        { label: 'Custom', value: 'custom' }
    ];

    const labelTexts = {
        'case-study': 'Case Study',
        'example': 'Example',
        'note': 'Note',
        'sidebar': 'Sidebar',
        'definition': 'Definition',
        'warning': 'Warning'
    };

    registerBlockType('kunaal/aside', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { label, labelType, outcome } = attributes;
            
            const blockProps = useBlockProps({
                className: 'aside'
            });

            const displayLabel = labelType === 'custom' ? label : (labelTexts[labelType] || '');

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Aside Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Label Type', 'kunaal-theme'),
                            value: labelType,
                            options: labelTypes,
                            onChange: function(value) { setAttributes({ labelType: value }); }
                        }),
                        labelType === 'custom' ? el(TextControl, {
                            label: __('Custom Label', 'kunaal-theme'),
                            value: label,
                            onChange: function(value) { setAttributes({ label: value }); },
                            placeholder: __('Enter custom label...', 'kunaal-theme')
                        }) : null,
                        el(TextControl, {
                            label: __('Outcome/Result (Optional)', 'kunaal-theme'),
                            value: outcome,
                            onChange: function(value) { setAttributes({ outcome: value }); },
                            help: __('Key outcome or result to highlight at the bottom', 'kunaal-theme'),
                            placeholder: __('e.g., "Increased conversion by 25%"', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'aside',
                    blockProps,
                    displayLabel ? el('div', { className: 'aside-label' }, displayLabel) : null,
                    el(
                        'div',
                        { className: 'aside-content' },
                        el(InnerBlocks, {
                            template: [['core/paragraph', { placeholder: 'Aside content...' }]],
                            templateLock: false
                        })
                    ),
                    outcome ? el('div', { className: 'aside-outcome' }, 'Result: ', el('strong', null, outcome)) : null
                )
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });
})(window.wp);
