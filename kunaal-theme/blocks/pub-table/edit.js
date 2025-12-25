/**
 * Publication Table Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, ToggleControl, Button } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/pub-table', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, source, caption, headers, rows, highlightFirst } = attributes;
            
            const blockProps = useBlockProps({
                className: 'pub-table' + (highlightFirst ? ' highlight-first' : '')
            });
            
            const addRow = function() {
                const newRow = headers.map(function() { return ''; });
                setAttributes({ rows: [...rows, newRow] });
            };
            
            const updateCell = function(rowIndex, colIndex, value) {
                const updated = [...rows];
                updated[rowIndex] = [...updated[rowIndex]];
                updated[rowIndex][colIndex] = value;
                setAttributes({ rows: updated });
            };
            
            const removeRow = function(index) {
                setAttributes({ rows: rows.filter(function(_, i) { return i !== index; }) });
            };
            
            const updateHeader = function(index, value) {
                const updated = [...headers];
                updated[index] = value;
                setAttributes({ headers: updated });
            };
            
            const addColumn = function() {
                setAttributes({
                    headers: [...headers, 'Column ' + (headers.length + 1)],
                    rows: rows.map(function(row) { return [...row, '']; })
                });
            };

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Table Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        el(TextControl, {
                            label: __('Source', 'kunaal-theme'),
                            value: source,
                            onChange: function(value) { setAttributes({ source: value }); }
                        }),
                        el(TextControl, {
                            label: __('Caption', 'kunaal-theme'),
                            value: caption,
                            onChange: function(value) { setAttributes({ caption: value }); }
                        }),
                        el(ToggleControl, {
                            label: __('Highlight First Column', 'kunaal-theme'),
                            checked: highlightFirst,
                            onChange: function(value) { setAttributes({ highlightFirst: value }); }
                        }),
                        el(Button, { variant: 'secondary', onClick: addColumn }, '+ Add Column')
                    )
                ),
                el(
                    'figure',
                    blockProps,
                    title ? el('h3', { className: 'pt-title' }, title) : null,
                    el(
                        'div',
                        { className: 'pt-wrapper' },
                        el(
                            'table',
                            { className: 'pt-table' },
                            el(
                                'thead',
                                null,
                                el(
                                    'tr',
                                    null,
                                    headers.map(function(header, index) {
                                        return el(
                                            'th',
                                            { key: index },
                                            el(RichText, {
                                                tagName: 'span',
                                                value: header,
                                                onChange: function(value) { updateHeader(index, value); },
                                                placeholder: __('Header...', 'kunaal-theme')
                                            })
                                        );
                                    })
                                )
                            ),
                            el(
                                'tbody',
                                null,
                                rows.map(function(row, rowIndex) {
                                    return el(
                                        'tr',
                                        { key: rowIndex },
                                        row.map(function(cell, colIndex) {
                                            return el(
                                                'td',
                                                { key: colIndex },
                                                el(RichText, {
                                                    tagName: 'span',
                                                    value: cell,
                                                    onChange: function(value) { updateCell(rowIndex, colIndex, value); },
                                                    placeholder: __('...', 'kunaal-theme')
                                                })
                                            );
                                        }),
                                        el(
                                            'td',
                                            { className: 'pt-actions' },
                                            el(Button, {
                                                variant: 'link',
                                                isDestructive: true,
                                                onClick: function() { removeRow(rowIndex); }
                                            }, '×')
                                        )
                                    );
                                })
                            )
                        )
                    ),
                    el(Button, { variant: 'secondary', onClick: addRow, style: { marginTop: '16px' } }, '+ Add Row'),
                    (caption || source) ? el(
                        'figcaption',
                        { className: 'pt-caption' },
                        caption ? el('span', { className: 'pt-caption-text' }, caption) : null,
                        source ? el('span', { className: 'pt-source' }, 'Source: ' + source) : null
                    ) : null
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

