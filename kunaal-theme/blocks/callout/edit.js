/**
 * Callout Block - Editor
 */
const { registerBlockType } = wp.blocks;
const { RichText, InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, ToggleControl, TextControl } = wp.components;
const { Fragment } = wp.element;

const CALLOUT_TYPES = [
    { value: 'info', label: 'Info (Blue)' },
    { value: 'warning', label: 'Warning (Amber)' },
    { value: 'success', label: 'Success (Green)' },
    { value: 'danger', label: 'Danger (Red)' },
    { value: 'note', label: 'Note (Gray)' },
    { value: 'tip', label: 'Tip (Purple)' }
];

const ICONS = {
    info: '🛈',
    warning: '⚠',
    success: '✓',
    danger: '✕',
    note: '✎',
    tip: '💡'
};

registerBlockType('kunaal/callout', {
    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { type, title, content, showIcon, collapsible, defaultOpen } = attributes;

        return Fragment(
            {},
            InspectorControls(
                {},
                PanelBody(
                    { title: 'Callout Settings', initialOpen: true },
                    SelectControl({
                        label: 'Type',
                        value: type,
                        options: CALLOUT_TYPES,
                        onChange: (value) => setAttributes({ type: value })
                    }),
                    TextControl({
                        label: 'Title (optional)',
                        value: title,
                        onChange: (value) => setAttributes({ title: value }),
                        placeholder: 'e.g., Important Note'
                    }),
                    ToggleControl({
                        label: 'Show Icon',
                        checked: showIcon,
                        onChange: (value) => setAttributes({ showIcon: value })
                    }),
                    ToggleControl({
                        label: 'Collapsible',
                        checked: collapsible,
                        onChange: (value) => setAttributes({ collapsible: value })
                    }),
                    collapsible && ToggleControl({
                        label: 'Open by Default',
                        checked: defaultOpen,
                        onChange: (value) => setAttributes({ defaultOpen: value })
                    })
                )
            ),
            wp.element.createElement(
                'div',
                { className: `wp-block-kunaal-callout callout callout--${type}` },
                wp.element.createElement(
                    'div',
                    { className: 'callout__header' },
                    showIcon && wp.element.createElement(
                        'span',
                        { className: 'callout__icon', 'aria-hidden': 'true' },
                        ICONS[type]
                    ),
                    title && wp.element.createElement(
                        'span',
                        { className: 'callout__title' },
                        title
                    )
                ),
                RichText({
                    tagName: 'div',
                    className: 'callout__content',
                    value: content,
                    onChange: (value) => setAttributes({ content: value }),
                    placeholder: 'Enter callout content...'
                })
            )
        );
    },
    save: function() {
        return null; // Server-side rendered
    }
});

