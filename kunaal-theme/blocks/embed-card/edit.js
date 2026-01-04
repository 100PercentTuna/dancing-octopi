/**
 * Embed Card Block - Editor
 */
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl, Button, Spinner, Notice } = wp.components;
const { Fragment, useState } = wp.element;
const apiFetch = wp.apiFetch;

const LAYOUT_OPTIONS = [
    { value: 'horizontal', label: 'Horizontal (image left)' },
    { value: 'vertical', label: 'Vertical (image top)' },
    { value: 'compact', label: 'Compact (no image)' }
];

registerBlockType('kunaal/embed-card', {
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { url, title, description, imageUrl, siteName, favicon, layout, openInNewTab } = attributes;
        
        const [inputUrl, setInputUrl] = useState(url);
        const [isLoading, setIsLoading] = useState(false);
        const [error, setError] = useState('');

        const fetchOGData = async () => {
            if (!inputUrl) {
                setError('Please enter a URL');
                return;
            }
            
            setIsLoading(true);
            setError('');
            
            try {
                const response = await apiFetch({
                    path: '/kunaal/v1/og-data',
                    method: 'POST',
                    data: { url: inputUrl }
                });
                
                if (response.success) {
                    setAttributes({
                        url: inputUrl,
                        title: response.data.title || '',
                        description: response.data.description || '',
                        imageUrl: response.data.image || '',
                        siteName: response.data.siteName || '',
                        favicon: response.data.favicon || ''
                    });
                    setError('');
                } else {
                    setError(response.message || 'Failed to fetch link preview');
                }
            } catch (err) {
                setError('Failed to fetch link preview. You can enter details manually.');
                // Still set the URL so user can proceed manually
                setAttributes({ url: inputUrl });
            }
            
            setIsLoading(false);
        };

        const domain = url ? new URL(url).hostname.replace('www.', '') : '';

        return Fragment(
            {},
            InspectorControls(
                {},
                PanelBody(
                    { title: 'Link Settings', initialOpen: true },
                    wp.element.createElement(
                        'div',
                        { style: { display: 'flex', gap: '8px', marginBottom: '16px' } },
                        TextControl({
                            label: 'URL',
                            value: inputUrl,
                            onChange: setInputUrl,
                            placeholder: 'https://example.com/article',
                            style: { flex: 1 }
                        }),
                        wp.element.createElement(
                            'div',
                            { style: { paddingTop: '24px' } },
                            Button({
                                variant: 'secondary',
                                onClick: fetchOGData,
                                disabled: isLoading
                            }, isLoading ? Spinner({}) : 'Fetch')
                        )
                    ),
                    error && Notice({ status: 'warning', isDismissible: false }, error),
                    SelectControl({
                        label: 'Layout',
                        value: layout,
                        options: LAYOUT_OPTIONS,
                        onChange: (value) => setAttributes({ layout: value })
                    }),
                    ToggleControl({
                        label: 'Open in New Tab',
                        checked: openInNewTab,
                        onChange: (value) => setAttributes({ openInNewTab: value })
                    })
                ),
                PanelBody(
                    { title: 'Manual Override', initialOpen: false },
                    TextControl({
                        label: 'Title',
                        value: title,
                        onChange: (value) => setAttributes({ title: value })
                    }),
                    TextControl({
                        label: 'Description',
                        value: description,
                        onChange: (value) => setAttributes({ description: value })
                    }),
                    TextControl({
                        label: 'Image URL',
                        value: imageUrl,
                        onChange: (value) => setAttributes({ imageUrl: value })
                    }),
                    TextControl({
                        label: 'Site Name',
                        value: siteName,
                        onChange: (value) => setAttributes({ siteName: value })
                    })
                )
            ),
            wp.element.createElement(
                'div',
                { className: `wp-block-kunaal-embed-card embed-card embed-card--${layout}` },
                url ? 
                    wp.element.createElement(
                        'a',
                        { 
                            href: url, 
                            className: 'embed-card__link',
                            target: openInNewTab ? '_blank' : '_self',
                            rel: openInNewTab ? 'noopener' : undefined
                        },
                        (layout !== 'compact' && imageUrl) && wp.element.createElement(
                            'div',
                            { className: 'embed-card__image' },
                            wp.element.createElement('img', { src: imageUrl, alt: title || '' })
                        ),
                        wp.element.createElement(
                            'div',
                            { className: 'embed-card__content' },
                            wp.element.createElement(
                                'div',
                                { className: 'embed-card__meta' },
                                favicon && wp.element.createElement('img', { src: favicon, alt: '', className: 'embed-card__favicon' }),
                                wp.element.createElement('span', { className: 'embed-card__domain' }, siteName || domain)
                            ),
                            title && wp.element.createElement('h4', { className: 'embed-card__title' }, title),
                            description && wp.element.createElement('p', { className: 'embed-card__description' }, description)
                        )
                    ) :
                    wp.element.createElement(
                        'div',
                        { className: 'embed-card__placeholder' },
                        wp.element.createElement('p', {}, 'Enter a URL in the sidebar and click "Fetch" to generate a link preview.')
                    )
            )
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});

