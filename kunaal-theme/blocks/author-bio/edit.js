/**
 * Author Bio Block - Editor
 */
const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl, Button } = wp.components;
const { Fragment } = wp.element;

const LAYOUT_OPTIONS = [
    { value: 'horizontal', label: 'Horizontal' },
    { value: 'vertical', label: 'Vertical (Centered)' },
    { value: 'compact', label: 'Compact (Inline)' }
];

registerBlockType('kunaal/author-bio', {
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { 
            name, title, bio, avatarUrl, avatarId, 
            email, website, twitter, linkedin,
            layout, showMoreLink, moreUrl, useThemeSettings 
        } = attributes;

        const onSelectImage = (media) => {
            setAttributes({
                avatarUrl: media.url,
                avatarId: media.id
            });
        };

        const onRemoveImage = () => {
            setAttributes({
                avatarUrl: '',
                avatarId: 0
            });
        };

        return Fragment(
            {},
            InspectorControls(
                {},
                PanelBody(
                    { title: 'Author Settings', initialOpen: true },
                    ToggleControl({
                        label: 'Use Theme Settings',
                        help: 'Pull name and avatar from theme Customizer settings',
                        checked: useThemeSettings,
                        onChange: (value) => setAttributes({ useThemeSettings: value })
                    }),
                    SelectControl({
                        label: 'Layout',
                        value: layout,
                        options: LAYOUT_OPTIONS,
                        onChange: (value) => setAttributes({ layout: value })
                    })
                ),
                !useThemeSettings && PanelBody(
                    { title: 'Author Details', initialOpen: true },
                    TextControl({
                        label: 'Name',
                        value: name,
                        onChange: (value) => setAttributes({ name: value })
                    }),
                    TextControl({
                        label: 'Title/Role',
                        value: title,
                        onChange: (value) => setAttributes({ title: value }),
                        placeholder: 'e.g., Writer, Researcher'
                    }),
                    wp.element.createElement(
                        'div',
                        { className: 'components-base-control' },
                        wp.element.createElement('label', { className: 'components-base-control__label' }, 'Avatar'),
                        MediaUploadCheck({},
                            MediaUpload({
                                onSelect: onSelectImage,
                                allowedTypes: ['image'],
                                value: avatarId,
                                render: ({ open }) => wp.element.createElement(
                                    'div',
                                    {},
                                    avatarUrl ? wp.element.createElement(
                                        'div',
                                        { style: { marginBottom: '8px' } },
                                        wp.element.createElement('img', { 
                                            src: avatarUrl, 
                                            alt: 'Avatar preview',
                                            style: { width: '80px', height: '80px', borderRadius: '50%', objectFit: 'cover' }
                                        }),
                                        wp.element.createElement(Button, { 
                                            onClick: onRemoveImage, 
                                            isDestructive: true,
                                            isSmall: true,
                                            style: { marginLeft: '8px' }
                                        }, 'Remove')
                                    ) : null,
                                    wp.element.createElement(Button, { onClick: open, variant: 'secondary' }, 
                                        avatarUrl ? 'Change Avatar' : 'Select Avatar'
                                    )
                                )
                            })
                        )
                    )
                ),
                PanelBody(
                    { title: 'Social Links', initialOpen: false },
                    TextControl({
                        label: 'Email',
                        type: 'email',
                        value: email,
                        onChange: (value) => setAttributes({ email: value })
                    }),
                    TextControl({
                        label: 'Website URL',
                        type: 'url',
                        value: website,
                        onChange: (value) => setAttributes({ website: value })
                    }),
                    TextControl({
                        label: 'Twitter/X Username',
                        value: twitter,
                        onChange: (value) => setAttributes({ twitter: value }),
                        placeholder: 'username (without @)'
                    }),
                    TextControl({
                        label: 'LinkedIn URL',
                        type: 'url',
                        value: linkedin,
                        onChange: (value) => setAttributes({ linkedin: value })
                    })
                ),
                PanelBody(
                    { title: 'More Link', initialOpen: false },
                    ToggleControl({
                        label: 'Show "More by this author" link',
                        checked: showMoreLink,
                        onChange: (value) => setAttributes({ showMoreLink: value })
                    }),
                    showMoreLink && TextControl({
                        label: 'Link URL',
                        type: 'url',
                        value: moreUrl,
                        onChange: (value) => setAttributes({ moreUrl: value })
                    })
                )
            ),
            wp.element.createElement(
                'div',
                { className: `wp-block-kunaal-author-bio author-bio author-bio--${layout}` },
                wp.element.createElement(
                    'div',
                    { className: 'author-bio__avatar' },
                    avatarUrl || useThemeSettings ? 
                        wp.element.createElement('img', { 
                            src: avatarUrl || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23ccc"%3E%3Cpath d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/%3E%3C/svg%3E',
                            alt: name || 'Author'
                        }) :
                        wp.element.createElement('div', { className: 'author-bio__avatar-placeholder' }, '?')
                ),
                wp.element.createElement(
                    'div',
                    { className: 'author-bio__content' },
                    wp.element.createElement(
                        'div',
                        { className: 'author-bio__name' },
                        useThemeSettings ? '(Theme Author)' : (name || 'Author Name')
                    ),
                    title && wp.element.createElement(
                        'div',
                        { className: 'author-bio__title' },
                        title
                    ),
                    RichText({
                        tagName: 'div',
                        className: 'author-bio__bio',
                        value: bio,
                        onChange: (value) => setAttributes({ bio: value }),
                        placeholder: 'Enter author bio...'
                    })
                )
            )
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});

