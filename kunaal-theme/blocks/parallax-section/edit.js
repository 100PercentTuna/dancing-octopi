/**
 * Parallax Section Block - Editor Component
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { PanelBody, RangeControl, SelectControl, Button, ColorPicker } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/parallax-section', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { mediaId, mediaUrl, minHeight, overlayOpacity, parallaxIntensity, contentAlignment } = attributes;
            
            const blockProps = useBlockProps({
                className: 'parallax-section-editor',
                style: {
                    minHeight: minHeight,
                    backgroundImage: mediaUrl ? 'url(' + mediaUrl + ')' : 'none',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center'
                }
            });

            const overlayStyle = {
                position: 'absolute',
                inset: 0,
                background: 'rgba(11,18,32,' + (overlayOpacity / 100) + ')',
                pointerEvents: 'none'
            };

            const contentStyle = {
                position: 'relative',
                zIndex: 1,
                textAlign: contentAlignment,
                padding: 'var(--space-6, 48px)',
                color: '#fff'
            };

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Background Image', 'kunaal-theme'), initialOpen: true },
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
                                    return el(
                                        'div',
                                        null,
                                        mediaUrl 
                                            ? el('img', { src: mediaUrl, style: { maxWidth: '100%', marginBottom: '10px' } })
                                            : null,
                                        el(
                                            Button,
                                            { 
                                                onClick: obj.open, 
                                                variant: 'primary',
                                                style: { marginRight: '8px' }
                                            },
                                            mediaUrl ? __('Replace Image', 'kunaal-theme') : __('Select Image', 'kunaal-theme')
                                        ),
                                        mediaUrl ? el(
                                            Button,
                                            { 
                                                onClick: function() { setAttributes({ mediaId: 0, mediaUrl: '' }); },
                                                isDestructive: true
                                            },
                                            __('Remove', 'kunaal-theme')
                                        ) : null
                                    );
                                }
                            })
                        )
                    ),
                    el(
                        PanelBody,
                        { title: __('Section Settings', 'kunaal-theme'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Minimum Height', 'kunaal-theme'),
                            value: minHeight,
                            options: [
                                { label: '40vh (Short)', value: '40vh' },
                                { label: '60vh (Medium)', value: '60vh' },
                                { label: '80vh (Tall)', value: '80vh' },
                                { label: '100vh (Full Screen)', value: '100vh' }
                            ],
                            onChange: function(value) { setAttributes({ minHeight: value }); }
                        }),
                        el(RangeControl, {
                            label: __('Overlay Darkness', 'kunaal-theme'),
                            value: overlayOpacity,
                            onChange: function(value) { setAttributes({ overlayOpacity: value }); },
                            min: 0,
                            max: 90,
                            help: __('How dark the overlay appears (0-90%)', 'kunaal-theme')
                        }),
                        el(RangeControl, {
                            label: __('Parallax Intensity', 'kunaal-theme'),
                            value: parallaxIntensity,
                            onChange: function(value) { setAttributes({ parallaxIntensity: value }); },
                            min: 0,
                            max: 50,
                            help: __('How much the background moves on scroll (0 = disabled)', 'kunaal-theme')
                        }),
                        el(SelectControl, {
                            label: __('Content Alignment', 'kunaal-theme'),
                            value: contentAlignment,
                            options: [
                                { label: 'Left', value: 'left' },
                                { label: 'Center', value: 'center' },
                                { label: 'Right', value: 'right' }
                            ],
                            onChange: function(value) { setAttributes({ contentAlignment: value }); }
                        })
                    )
                ),
                el(
                    'section',
                    blockProps,
                    el('div', { style: overlayStyle }),
                    el(
                        'div',
                        { style: contentStyle },
                        el(InnerBlocks, {
                            template: [
                                ['core/heading', { level: 2, placeholder: 'Section Title', textColor: 'white' }],
                                ['core/paragraph', { placeholder: 'Add your content here...', textColor: 'white' }]
                            ],
                            templateLock: false
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

