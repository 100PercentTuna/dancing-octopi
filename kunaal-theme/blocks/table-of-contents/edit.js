/**
 * Table of Contents Block - Editor
 */
const { registerBlockType } = wp.blocks;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl, CheckboxControl } = wp.components;
const { Fragment, useState, useEffect } = wp.element;
const { useSelect } = wp.data;

const STYLE_OPTIONS = [
    { value: 'numbered', label: 'Numbered (1, 2, 3...)' },
    { value: 'bulleted', label: 'Bulleted' },
    { value: 'plain', label: 'Plain (no markers)' }
];

registerBlockType('kunaal/table-of-contents', {
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { title, headingLevels, style, collapsible, defaultOpen, highlightActive, smoothScroll, sticky } = attributes;
        
        // Get headings from current post content
        const blocks = useSelect((select) => {
            return select('core/block-editor').getBlocks();
        }, []);
        
        const [headings, setHeadings] = useState([]);
        
        useEffect(() => {
            const extractHeadings = (blocks, result = []) => {
                blocks.forEach(block => {
                    if (block.name === 'core/heading') {
                        const level = 'h' + (block.attributes.level || 2);
                        if (headingLevels.includes(level)) {
                            result.push({
                                level: block.attributes.level || 2,
                                text: block.attributes.content?.replace(/<[^>]+>/g, '') || '',
                                anchor: block.attributes.anchor || ''
                            });
                        }
                    }
                    if (block.name === 'kunaal/section-header') {
                        if (headingLevels.includes('h2')) {
                            result.push({
                                level: 2,
                                text: block.attributes.title || '',
                                anchor: block.attributes.anchor || ''
                            });
                        }
                    }
                    if (block.innerBlocks && block.innerBlocks.length) {
                        extractHeadings(block.innerBlocks, result);
                    }
                });
                return result;
            };
            
            setHeadings(extractHeadings(blocks));
        }, [blocks, headingLevels]);
        
        const toggleHeadingLevel = (level) => {
            if (headingLevels.includes(level)) {
                setAttributes({ headingLevels: headingLevels.filter(l => l !== level) });
            } else {
                setAttributes({ headingLevels: [...headingLevels, level].sort() });
            }
        };

        return Fragment(
            {},
            InspectorControls(
                {},
                PanelBody(
                    { title: 'Table of Contents Settings', initialOpen: true },
                    TextControl({
                        label: 'Title',
                        value: title,
                        onChange: (value) => setAttributes({ title: value })
                    }),
                    SelectControl({
                        label: 'List Style',
                        value: style,
                        options: STYLE_OPTIONS,
                        onChange: (value) => setAttributes({ style: value })
                    }),
                    wp.element.createElement(
                        'div',
                        { className: 'components-base-control' },
                        wp.element.createElement('label', { className: 'components-base-control__label' }, 'Include Headings'),
                        wp.element.createElement(
                            'div',
                            { style: { display: 'flex', gap: '16px', marginTop: '8px' } },
                            CheckboxControl({
                                label: 'H2',
                                checked: headingLevels.includes('h2'),
                                onChange: () => toggleHeadingLevel('h2')
                            }),
                            CheckboxControl({
                                label: 'H3',
                                checked: headingLevels.includes('h3'),
                                onChange: () => toggleHeadingLevel('h3')
                            }),
                            CheckboxControl({
                                label: 'H4',
                                checked: headingLevels.includes('h4'),
                                onChange: () => toggleHeadingLevel('h4')
                            })
                        )
                    )
                ),
                PanelBody(
                    { title: 'Behavior', initialOpen: false },
                    ToggleControl({
                        label: 'Collapsible',
                        checked: collapsible,
                        onChange: (value) => setAttributes({ collapsible: value })
                    }),
                    collapsible && ToggleControl({
                        label: 'Open by Default',
                        checked: defaultOpen,
                        onChange: (value) => setAttributes({ defaultOpen: value })
                    }),
                    ToggleControl({
                        label: 'Highlight Active Section',
                        checked: highlightActive,
                        onChange: (value) => setAttributes({ highlightActive: value })
                    }),
                    ToggleControl({
                        label: 'Smooth Scroll',
                        checked: smoothScroll,
                        onChange: (value) => setAttributes({ smoothScroll: value })
                    }),
                    ToggleControl({
                        label: 'Sticky Position',
                        help: 'Make TOC stick to top when scrolling',
                        checked: sticky,
                        onChange: (value) => setAttributes({ sticky: value })
                    })
                )
            ),
            wp.element.createElement(
                'nav',
                { className: `wp-block-kunaal-table-of-contents toc toc--${style}` },
                wp.element.createElement(
                    'div',
                    { className: 'toc__header' },
                    title && wp.element.createElement('h4', { className: 'toc__title' }, title)
                ),
                headings.length > 0 ? 
                    wp.element.createElement(
                        style === 'numbered' ? 'ol' : 'ul',
                        { className: 'toc__list' },
                        headings.map((heading, index) => 
                            wp.element.createElement(
                                'li',
                                { 
                                    key: index, 
                                    className: `toc__item toc__item--level-${heading.level}`,
                                    style: { paddingLeft: `${(heading.level - 2) * 16}px` }
                                },
                                wp.element.createElement('a', { href: '#' }, heading.text || '(Untitled)')
                            )
                        )
                    ) :
                    wp.element.createElement(
                        'p',
                        { className: 'toc__empty' },
                        'Add headings to your content to generate the table of contents.'
                    )
            )
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});

