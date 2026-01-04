/**
 * Image Comparison Block - Editor
 */
const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl, RangeControl, Button } = wp.components;
const { Fragment } = wp.element;

const ORIENTATION_OPTIONS = [
    { value: 'horizontal', label: 'Horizontal (left/right)' },
    { value: 'vertical', label: 'Vertical (top/bottom)' }
];

registerBlockType('kunaal/image-comparison', {
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { 
            beforeUrl, beforeId, beforeAlt, beforeLabel,
            afterUrl, afterId, afterAlt, afterLabel,
            caption, initialPosition, orientation, showLabels
        } = attributes;

        const onSelectBefore = (media) => {
            setAttributes({
                beforeUrl: media.url,
                beforeId: media.id,
                beforeAlt: media.alt || ''
            });
        };

        const onSelectAfter = (media) => {
            setAttributes({
                afterUrl: media.url,
                afterId: media.id,
                afterAlt: media.alt || ''
            });
        };

        return Fragment(
            {},
            InspectorControls(
                {},
                PanelBody(
                    { title: 'Before Image', initialOpen: true },
                    wp.element.createElement(
                        'div',
                        { className: 'components-base-control' },
                        MediaUploadCheck({},
                            MediaUpload({
                                onSelect: onSelectBefore,
                                allowedTypes: ['image'],
                                value: beforeId,
                                render: ({ open }) => wp.element.createElement(
                                    'div',
                                    {},
                                    beforeUrl ? wp.element.createElement(
                                        'div',
                                        { style: { marginBottom: '8px' } },
                                        wp.element.createElement('img', { 
                                            src: beforeUrl, 
                                            alt: 'Before preview',
                                            style: { width: '100%', height: 'auto', borderRadius: '4px' }
                                        })
                                    ) : null,
                                    wp.element.createElement(Button, { onClick: open, variant: 'secondary', isFullWidth: true }, 
                                        beforeUrl ? 'Change "Before" Image' : 'Select "Before" Image'
                                    )
                                )
                            })
                        )
                    ),
                    TextControl({
                        label: 'Alt Text',
                        value: beforeAlt,
                        onChange: (value) => setAttributes({ beforeAlt: value })
                    }),
                    TextControl({
                        label: 'Label',
                        value: beforeLabel,
                        onChange: (value) => setAttributes({ beforeLabel: value })
                    })
                ),
                PanelBody(
                    { title: 'After Image', initialOpen: true },
                    wp.element.createElement(
                        'div',
                        { className: 'components-base-control' },
                        MediaUploadCheck({},
                            MediaUpload({
                                onSelect: onSelectAfter,
                                allowedTypes: ['image'],
                                value: afterId,
                                render: ({ open }) => wp.element.createElement(
                                    'div',
                                    {},
                                    afterUrl ? wp.element.createElement(
                                        'div',
                                        { style: { marginBottom: '8px' } },
                                        wp.element.createElement('img', { 
                                            src: afterUrl, 
                                            alt: 'After preview',
                                            style: { width: '100%', height: 'auto', borderRadius: '4px' }
                                        })
                                    ) : null,
                                    wp.element.createElement(Button, { onClick: open, variant: 'secondary', isFullWidth: true }, 
                                        afterUrl ? 'Change "After" Image' : 'Select "After" Image'
                                    )
                                )
                            })
                        )
                    ),
                    TextControl({
                        label: 'Alt Text',
                        value: afterAlt,
                        onChange: (value) => setAttributes({ afterAlt: value })
                    }),
                    TextControl({
                        label: 'Label',
                        value: afterLabel,
                        onChange: (value) => setAttributes({ afterLabel: value })
                    })
                ),
                PanelBody(
                    { title: 'Settings', initialOpen: false },
                    SelectControl({
                        label: 'Orientation',
                        value: orientation,
                        options: ORIENTATION_OPTIONS,
                        onChange: (value) => setAttributes({ orientation: value })
                    }),
                    RangeControl({
                        label: 'Initial Slider Position (%)',
                        value: initialPosition,
                        onChange: (value) => setAttributes({ initialPosition: value }),
                        min: 10,
                        max: 90,
                        step: 5
                    }),
                    ToggleControl({
                        label: 'Show Labels',
                        checked: showLabels,
                        onChange: (value) => setAttributes({ showLabels: value })
                    }),
                    TextControl({
                        label: 'Caption',
                        value: caption,
                        onChange: (value) => setAttributes({ caption: value })
                    })
                )
            ),
            wp.element.createElement(
                'figure',
                { className: `wp-block-kunaal-image-comparison imgcmp imgcmp--${orientation}` },
                beforeUrl && afterUrl ? 
                    wp.element.createElement(
                        'div',
                        { className: 'imgcmp__container', style: { position: 'relative' } },
                        wp.element.createElement(
                            'div',
                            { className: 'imgcmp__after' },
                            wp.element.createElement('img', { src: afterUrl, alt: afterAlt || 'After' })
                        ),
                        wp.element.createElement(
                            'div',
                            { 
                                className: 'imgcmp__before', 
                                style: { 
                                    width: orientation === 'horizontal' ? `${initialPosition}%` : '100%',
                                    height: orientation === 'vertical' ? `${initialPosition}%` : '100%'
                                }
                            },
                            wp.element.createElement('img', { src: beforeUrl, alt: beforeAlt || 'Before' })
                        ),
                        wp.element.createElement(
                            'div',
                            { 
                                className: 'imgcmp__handle',
                                style: orientation === 'horizontal' 
                                    ? { left: `${initialPosition}%` }
                                    : { top: `${initialPosition}%` }
                            },
                            wp.element.createElement('div', { className: 'imgcmp__handle-line' }),
                            wp.element.createElement(
                                'div',
                                { className: 'imgcmp__handle-button' },
                                wp.element.createElement('span', { className: 'imgcmp__arrows' }, '◄►')
                            )
                        ),
                        showLabels && wp.element.createElement(
                            Fragment,
                            {},
                            wp.element.createElement('span', { className: 'imgcmp__label imgcmp__label--before' }, beforeLabel),
                            wp.element.createElement('span', { className: 'imgcmp__label imgcmp__label--after' }, afterLabel)
                        )
                    ) :
                    wp.element.createElement(
                        'div',
                        { className: 'imgcmp__placeholder' },
                        wp.element.createElement('p', {}, 'Select "Before" and "After" images in the sidebar.')
                    ),
                caption && wp.element.createElement('figcaption', { className: 'imgcmp__caption' }, caption)
            )
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});

