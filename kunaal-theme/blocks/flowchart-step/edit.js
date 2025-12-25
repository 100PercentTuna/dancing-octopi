/**
 * Flow Chart Step Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, SelectControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/flowchart-step', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { label, description, stepType } = attributes;
            
            const blockProps = useBlockProps({
                className: 'flowchart-step step-' + stepType
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
                        el(SelectControl, {
                            label: __('Step Type', 'kunaal-theme'),
                            value: stepType,
                            options: [
                                { label: 'Process', value: 'process' },
                                { label: 'Decision', value: 'decision' },
                                { label: 'Start/End', value: 'terminal' },
                                { label: 'Input/Output', value: 'io' }
                            ],
                            onChange: function(value) { setAttributes({ stepType: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(
                        'div',
                        { className: 'fcs-box' },
                        el(RichText, {
                            tagName: 'span',
                            className: 'fcs-label',
                            value: label,
                            onChange: function(value) { setAttributes({ label: value }); },
                            placeholder: __('Step label...', 'kunaal-theme')
                        }),
                        el(RichText, {
                            tagName: 'p',
                            className: 'fcs-description',
                            value: description,
                            onChange: function(value) { setAttributes({ description: value }); },
                            placeholder: __('Description...', 'kunaal-theme')
                        })
                    ),
                    el('div', { className: 'fcs-arrow' }, '→')
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

