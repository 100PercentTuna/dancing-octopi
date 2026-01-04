/**
 * Section Divider Block - Editor Component
 * Stylized asterisk ornaments for section breaks
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    const VARIANT_OPTIONS = [
        { value: 'single', label: 'Single Asterisk (section break)' },
        { value: 'triple', label: 'Triple Asterisks (ending)' },
        { value: 'fleuron', label: 'Fleuron (decorative)' }
    ];

    const SPACING_OPTIONS = [
        { value: 'small', label: 'Small' },
        { value: 'medium', label: 'Medium' },
        { value: 'large', label: 'Large' }
    ];

    // Render the ornament based on variant
    function renderOrnament(variant) {
        if (variant === 'single') {
            return el('span', { className: 'sectionDivider__ornament' }, '✦');
        } else if (variant === 'triple') {
            return el(
                'span',
                { className: 'sectionDivider__ornament' },
                el('span', null, '✦'),
                el('span', null, '✦'),
                el('span', null, '✦')
            );
        } else if (variant === 'fleuron') {
            return el('span', { className: 'sectionDivider__ornament' }, '❧');
        }
        return null;
    }

    registerBlockType('kunaal/section-divider', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { variant, spacing } = attributes;
            
            const blockProps = useBlockProps({
                className: 'sectionDivider sectionDivider--' + variant + ' sectionDivider--' + spacing
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Divider Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Style', 'kunaal-theme'),
                            value: variant,
                            options: VARIANT_OPTIONS,
                            onChange: function(value) { setAttributes({ variant: value }); },
                            help: __('Single for section breaks, triple for endings', 'kunaal-theme')
                        }),
                        el(SelectControl, {
                            label: __('Spacing', 'kunaal-theme'),
                            value: spacing,
                            options: SPACING_OPTIONS,
                            onChange: function(value) { setAttributes({ spacing: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    renderOrnament(variant)
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
})(window.wp);

