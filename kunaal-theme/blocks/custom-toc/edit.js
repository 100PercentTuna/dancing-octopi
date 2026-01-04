/**
 * Custom TOC Block - Editor Component
 * Allows manual creation of TOC items linked to anchor points
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, ToggleControl, Button } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/custom-toc', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, items, sticky, highlightActive, showNumbers } = attributes;
            
            const blockProps = useBlockProps({
                className: 'customToc' + (sticky ? ' customToc--sticky' : '')
            });

            const addItem = function() {
                const newItems = [...items, { label: '', anchorId: '' }];
                setAttributes({ items: newItems });
            };

            const updateItem = function(index, field, value) {
                const newItems = [...items];
                newItems[index] = { ...newItems[index], [field]: value };
                setAttributes({ items: newItems });
            };

            const removeItem = function(index) {
                const newItems = items.filter(function(_, i) { return i !== index; });
                setAttributes({ items: newItems });
            };

            const moveItem = function(index, direction) {
                const newItems = [...items];
                const newIndex = index + direction;
                if (newIndex < 0 || newIndex >= items.length) return;
                const temp = newItems[index];
                newItems[index] = newItems[newIndex];
                newItems[newIndex] = temp;
                setAttributes({ items: newItems });
            };

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('TOC Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); },
                            help: __('Title shown above the table of contents', 'kunaal-theme')
                        }),
                        el(ToggleControl, {
                            label: __('Sticky Position', 'kunaal-theme'),
                            help: __('TOC stays visible while scrolling', 'kunaal-theme'),
                            checked: sticky,
                            onChange: function(value) { setAttributes({ sticky: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Highlight Active Section', 'kunaal-theme'),
                            checked: highlightActive,
                            onChange: function(value) { setAttributes({ highlightActive: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Show Numbers', 'kunaal-theme'),
                            checked: showNumbers,
                            onChange: function(value) { setAttributes({ showNumbers: value }); }
                        })
                    )
                ),
                el(
                    'nav',
                    blockProps,
                    title && el('h4', { className: 'customToc__title' }, title),
                    el(
                        'div',
                        { className: 'customToc__items' },
                        items.map(function(item, index) {
                            return el(
                                'div',
                                { className: 'customToc__item-editor', key: index },
                                el(
                                    'div',
                                    { className: 'customToc__item-fields' },
                                    el(TextControl, {
                                        label: __('Chapter Name', 'kunaal-theme'),
                                        value: item.label,
                                        onChange: function(value) { updateItem(index, 'label', value); },
                                        placeholder: __('e.g., Introduction', 'kunaal-theme')
                                    }),
                                    el(TextControl, {
                                        label: __('Anchor ID', 'kunaal-theme'),
                                        value: item.anchorId,
                                        onChange: function(value) { updateItem(index, 'anchorId', value); },
                                        placeholder: __('e.g., introduction', 'kunaal-theme'),
                                        help: __('Use the same ID in a block\'s Advanced > HTML Anchor field', 'kunaal-theme')
                                    })
                                ),
                                el(
                                    'div',
                                    { className: 'customToc__item-actions' },
                                    el(Button, {
                                        icon: 'arrow-up-alt2',
                                        label: __('Move up', 'kunaal-theme'),
                                        onClick: function() { moveItem(index, -1); },
                                        disabled: index === 0,
                                        isSmall: true
                                    }),
                                    el(Button, {
                                        icon: 'arrow-down-alt2',
                                        label: __('Move down', 'kunaal-theme'),
                                        onClick: function() { moveItem(index, 1); },
                                        disabled: index === items.length - 1,
                                        isSmall: true
                                    }),
                                    el(Button, {
                                        icon: 'trash',
                                        label: __('Remove', 'kunaal-theme'),
                                        onClick: function() { removeItem(index); },
                                        isDestructive: true,
                                        isSmall: true
                                    })
                                )
                            );
                        }),
                        el(Button, {
                            variant: 'secondary',
                            onClick: addItem,
                            className: 'customToc__add-btn'
                        }, __('+ Add Section', 'kunaal-theme'))
                    ),
                    items.length === 0 && el(
                        'p',
                        { className: 'customToc__empty' },
                        __('Click "Add Section" to create TOC items. Link them to anchors in your content.', 'kunaal-theme')
                    )
                )
            );
        },
        save: function() {
            return null; // Dynamic block
        }
    });
})(window.wp);

