/**
 * Reveal Wrapper Block - Editor Component
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, RangeControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/reveal-wrapper', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { animationType, delay, duration, threshold } = attributes;
            
            const blockProps = useBlockProps({
                className: 'reveal-wrapper-editor'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Animation Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Animation Type', 'kunaal-theme'),
                            value: animationType,
                            options: [
                                { label: 'Fade Up', value: 'fade-up' },
                                { label: 'Fade Down', value: 'fade-down' },
                                { label: 'Fade In', value: 'fade-in' },
                                { label: 'Slide from Left', value: 'slide-left' },
                                { label: 'Slide from Right', value: 'slide-right' },
                                { label: 'Scale Up', value: 'scale-up' },
                                { label: 'Scale Down', value: 'scale-down' }
                            ],
                            onChange: function(value) { setAttributes({ animationType: value }); }
                        }),
                        el(RangeControl, {
                            label: __('Delay (ms)', 'kunaal-theme'),
                            value: delay,
                            onChange: function(value) { setAttributes({ delay: value }); },
                            min: 0,
                            max: 1000,
                            step: 50,
                            help: __('Delay before animation starts', 'kunaal-theme')
                        }),
                        el(RangeControl, {
                            label: __('Duration (ms)', 'kunaal-theme'),
                            value: duration,
                            onChange: function(value) { setAttributes({ duration: value }); },
                            min: 200,
                            max: 1500,
                            step: 100,
                            help: __('How long the animation takes', 'kunaal-theme')
                        }),
                        el(RangeControl, {
                            label: __('Trigger Threshold (%)', 'kunaal-theme'),
                            value: threshold,
                            onChange: function(value) { setAttributes({ threshold: value }); },
                            min: 0,
                            max: 50,
                            help: __('How far into viewport before triggering', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('div', { className: 'reveal-label' }, 
                        '✨ ' + animationType.replace('-', ' ').toUpperCase() + ' • ' + delay + 'ms delay'
                    ),
                    el(InnerBlocks, {
                        template: [['core/paragraph', { placeholder: 'Add content to animate...' }]],
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

