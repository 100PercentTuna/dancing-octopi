/**
 * Magazine Figure Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck, RichText } = wp.blockEditor;
    const { PanelBody, SelectControl, TextControl, Button } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/magazine-figure', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { mediaId, mediaUrl, alt, caption, credit, size } = attributes;
            
            const blockProps = useBlockProps({
                className: 'magazine-figure size-' + size
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Figure Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Size', 'kunaal-theme'),
                            value: size,
                            options: [
                                { label: 'Default (prose width)', value: 'default' },
                                { label: 'Wide', value: 'wide' },
                                { label: 'Full Width', value: 'full' }
                            ],
                            onChange: function(value) { setAttributes({ size: value }); }
                        }),
                        el(TextControl, {
                            label: __('Alt Text', 'kunaal-theme'),
                            value: alt,
                            onChange: function(value) { setAttributes({ alt: value }); }
                        }),
                        el(TextControl, {
                            label: __('Photo Credit', 'kunaal-theme'),
                            value: credit,
                            onChange: function(value) { setAttributes({ credit: value }); },
                            placeholder: __('Photo: Name / Source', 'kunaal-theme')
                        })
                    )
                ),
                el(
                    'figure',
                    blockProps,
                    el(
                        MediaUploadCheck,
                        null,
                        el(MediaUpload, {
                            onSelect: function(media) {
                                setAttributes({ 
                                    mediaId: media.id, 
                                    mediaUrl: media.url,
                                    alt: media.alt || alt
                                });
                            },
                            allowedTypes: ['image'],
                            value: mediaId,
                            render: function(obj) {
                                return mediaUrl
                                    ? el(
                                        'div',
                                        { className: 'figure-image-wrapper' },
                                        el('img', { src: mediaUrl, alt: alt }),
                                        el(Button, { 
                                            onClick: obj.open, 
                                            variant: 'secondary',
                                            className: 'change-image-btn'
                                        }, __('Change Image', 'kunaal-theme'))
                                    )
                                    : el(
                                        'div',
                                        { className: 'figure-placeholder' },
                                        el(Button, { onClick: obj.open, variant: 'primary' }, 
                                            __('Select Image', 'kunaal-theme')
                                        )
                                    );
                            }
                        })
                    ),
                    el(
                        'figcaption',
                        { className: 'figure-caption' },
                        el(RichText, {
                            tagName: 'span',
                            className: 'caption-text',
                            value: caption,
                            onChange: function(value) { setAttributes({ caption: value }); },
                            placeholder: __('Caption...', 'kunaal-theme')
                        }),
                        credit ? el('span', { className: 'figure-credit' }, credit) : null
                    )
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

