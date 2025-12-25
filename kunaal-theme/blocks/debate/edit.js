/**
 * Debate Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/debate', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, question } = attributes;
            
            const blockProps = useBlockProps({
                className: 'debate'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Section Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    title ? el('h3', { className: 'debate-title' }, title) : null,
                    el(RichText, {
                        tagName: 'p',
                        className: 'debate-question',
                        value: question,
                        onChange: function(value) { setAttributes({ question: value }); },
                        placeholder: __('The question being debated...', 'kunaal-theme')
                    }),
                    el(
                        'div',
                        { className: 'debate-sides' },
                        el(InnerBlocks, {
                            allowedBlocks: ['kunaal/debate-side'],
                            template: [
                                ['kunaal/debate-side', { position: 'for', label: 'For' }],
                                ['kunaal/debate-side', { position: 'against', label: 'Against' }]
                            ],
                            templateLock: 'all'
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

