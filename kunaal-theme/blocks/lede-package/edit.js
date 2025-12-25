/**
 * Lede Package Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck, RichText } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl, Button } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/lede-package', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { mediaId, mediaUrl, headline, dek, credit, layout } = attributes;
            
            const blockProps = useBlockProps({
                className: 'lede-package lede-' + layout
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Layout', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Layout Style', 'kunaal-theme'),
                            value: layout,
                            options: [
                                { label: 'Text Overlay', value: 'overlay' },
                                { label: 'Text Below', value: 'below' },
                                { label: 'Split (Text Left)', value: 'split' }
                            ],
                            onChange: function(value) { setAttributes({ layout: value }); }
                        }),
                        el(TextControl, {
                            label: __('Photo Credit', 'kunaal-theme'),
                            value: credit,
                            onChange: function(value) { setAttributes({ credit: value }); }
                        })
                    )
                ),
                el(
                    'header',
                    blockProps,
                    el(
                        'div',
                        { className: 'lede-media' },
                        el(
                            MediaUploadCheck,
                            null,
                            el(MediaUpload, {
                                onSelect: function(media) {
                                    setAttributes({ mediaId: media.id, mediaUrl: media.url });
                                },
                                allowedTypes: ['image'],
                                value: mediaId,
                                render: function(obj) {
                                    return mediaUrl
                                        ? el(
                                            'div',
                                            { className: 'lede-image-wrapper' },
                                            el('img', { src: mediaUrl, alt: '' }),
                                            el(Button, { onClick: obj.open, variant: 'secondary' }, 'Change')
                                        )
                                        : el(
                                            'div',
                                            { className: 'lede-placeholder' },
                                            el(Button, { onClick: obj.open, variant: 'primary' }, 'Select Image')
                                        );
                                }
                            })
                        )
                    ),
                    el(
                        'div',
                        { className: 'lede-text' },
                        el(RichText, {
                            tagName: 'h1',
                            className: 'lede-headline',
                            value: headline,
                            onChange: function(value) { setAttributes({ headline: value }); },
                            placeholder: __('Headline...', 'kunaal-theme')
                        }),
                        el(RichText, {
                            tagName: 'p',
                            className: 'lede-dek',
                            value: dek,
                            onChange: function(value) { setAttributes({ dek: value }); },
                            placeholder: __('Dek / subheadline...', 'kunaal-theme')
                        })
                    ),
                    credit ? el('span', { className: 'lede-credit' }, credit) : null
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

