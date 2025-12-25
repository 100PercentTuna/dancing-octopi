/**
 * Evaluation Rubric Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/rubric', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, columns } = attributes;
            
            const blockProps = useBlockProps({
                className: 'rubric'
            });
            
            const updateColumn = function(index, value) {
                const updated = [...columns];
                updated[index] = value;
                setAttributes({ columns: updated });
            };

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Rubric Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        el('p', { className: 'components-base-control__help' }, 'Column Headers:'),
                        columns.map(function(col, index) {
                            return el(TextControl, {
                                key: index,
                                label: 'Column ' + (index + 1),
                                value: col,
                                onChange: function(value) { updateColumn(index, value); }
                            });
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('h3', { className: 'rubric-title' }, title),
                    el(
                        'table',
                        { className: 'rubric-table' },
                        el(
                            'thead',
                            null,
                            el(
                                'tr',
                                null,
                                el('th', { className: 'rubric-criteria-header' }, 'Criteria'),
                                columns.map(function(col, index) {
                                    return el('th', { key: index }, col);
                                })
                            )
                        ),
                        el(
                            'tbody',
                            null,
                            el(InnerBlocks, {
                                allowedBlocks: ['kunaal/rubric-row'],
                                template: [
                                    ['kunaal/rubric-row', { criteria: 'Criterion 1' }]
                                ],
                                templateLock: false,
                                renderAppender: InnerBlocks.ButtonBlockAppender
                            })
                        )
                    )
                )
            );
        },
        save: function() {
            return el(InnerBlocks.Content);
        }
    });
})(window.wp);

