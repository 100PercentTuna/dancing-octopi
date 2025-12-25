/**
 * Scrolly Step Block - Editor Component
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/scrolly-step', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { stepNumber, stickyTitle, stickyDescription } = attributes;
            
            const blockProps = useBlockProps({
                className: 'scrolly-step'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Step Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Update Sticky Title (Optional)', 'kunaal-theme'),
                            value: stickyTitle,
                            onChange: function(value) { setAttributes({ stickyTitle: value }); },
                            help: __('When this step becomes active, update the sticky panel title', 'kunaal-theme')
                        }),
                        el(TextControl, {
                            label: __('Update Sticky Description (Optional)', 'kunaal-theme'),
                            value: stickyDescription,
                            onChange: function(value) { setAttributes({ stickyDescription: value }); },
                            help: __('When this step becomes active, update the sticky panel description', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('div', { className: 'step-indicator' }, 'Step ' + stepNumber),
                    el(InnerBlocks, {
                        template: [
                            ['core/heading', { level: 4, placeholder: 'Step heading...' }],
                            ['core/paragraph', { placeholder: 'Step content...' }]
                        ],
                        templateLock: false
                    })
                )
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });
})(window.wp);

