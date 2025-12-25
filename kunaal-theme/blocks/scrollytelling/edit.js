/**
 * Scrollytelling Block - Editor Component
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/scrollytelling', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { stickyLabel, stickyTitle, stickyDescription, stickyPosition } = attributes;
            
            const blockProps = useBlockProps({
                className: 'scrollytelling-editor'
            });

            const isRightSticky = stickyPosition === 'right';

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Scrollytelling Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Sticky Label', 'kunaal-theme'),
                            value: stickyLabel,
                            onChange: function(value) { setAttributes({ stickyLabel: value }); },
                            help: __('Small label above the sticky title', 'kunaal-theme')
                        }),
                        el(SelectControl, {
                            label: __('Sticky Panel Position', 'kunaal-theme'),
                            value: stickyPosition,
                            options: [
                                { label: 'Left', value: 'left' },
                                { label: 'Right', value: 'right' }
                            ],
                            onChange: function(value) { setAttributes({ stickyPosition: value }); }
                        })
                    )
                ),
                el(
                    'section',
                    blockProps,
                    el(
                        'div',
                        { className: 'scrolly-inner', style: { flexDirection: isRightSticky ? 'row-reverse' : 'row' } },
                        // Sticky panel
                        el(
                            'div',
                            { className: 'scrolly-sticky' },
                            el('div', { className: 'scrolly-label' }, stickyLabel),
                            el(RichText, {
                                tagName: 'h3',
                                className: 'scrolly-title',
                                value: stickyTitle,
                                onChange: function(value) { setAttributes({ stickyTitle: value }); },
                                placeholder: __('Sticky Title', 'kunaal-theme')
                            }),
                            el(RichText, {
                                tagName: 'p',
                                className: 'scrolly-description',
                                value: stickyDescription,
                                onChange: function(value) { setAttributes({ stickyDescription: value }); },
                                placeholder: __('Description that stays visible...', 'kunaal-theme')
                            })
                        ),
                        // Steps container
                        el(
                            'div',
                            { className: 'scrolly-steps' },
                            el(InnerBlocks, {
                                allowedBlocks: ['kunaal/scrolly-step'],
                                template: [
                                    ['kunaal/scrolly-step', { stepNumber: 1 }],
                                    ['kunaal/scrolly-step', { stepNumber: 2 }],
                                    ['kunaal/scrolly-step', { stepNumber: 3 }]
                                ],
                                templateLock: false
                            })
                        )
                    )
                )
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });
})(window.wp);

