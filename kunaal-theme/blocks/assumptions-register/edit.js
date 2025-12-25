/**
 * Assumptions Register Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl, Button, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/assumptions-register', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, assumptions } = attributes;
            
            const blockProps = useBlockProps({
                className: 'assumptions-register'
            });
            
            const addAssumption = function() {
                setAttributes({
                    assumptions: [...assumptions, {
                        text: '',
                        confidence: 'medium',
                        status: 'untested',
                        notes: ''
                    }]
                });
            };
            
            const updateAssumption = function(index, field, value) {
                const updated = [...assumptions];
                updated[index] = { ...updated[index], [field]: value };
                setAttributes({ assumptions: updated });
            };
            
            const removeAssumption = function(index) {
                setAttributes({ assumptions: assumptions.filter(function(_, i) { return i !== index; }) });
            };

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('h3', { className: 'ar-title' }, title),
                    el(
                        'div',
                        { className: 'ar-header' },
                        el('span', { className: 'ar-col-assumption' }, 'Assumption'),
                        el('span', { className: 'ar-col-confidence' }, 'Confidence'),
                        el('span', { className: 'ar-col-status' }, 'Status'),
                        el('span', { className: 'ar-col-actions' }, '')
                    ),
                    assumptions.map(function(item, index) {
                        return el(
                            'div',
                            { key: index, className: 'ar-row' },
                            el(TextareaControl, {
                                className: 'ar-col-assumption',
                                value: item.text,
                                onChange: function(value) { updateAssumption(index, 'text', value); },
                                placeholder: __('Assumption...', 'kunaal-theme'),
                                rows: 2
                            }),
                            el(SelectControl, {
                                className: 'ar-col-confidence',
                                value: item.confidence,
                                options: [
                                    { label: 'High', value: 'high' },
                                    { label: 'Medium', value: 'medium' },
                                    { label: 'Low', value: 'low' }
                                ],
                                onChange: function(value) { updateAssumption(index, 'confidence', value); }
                            }),
                            el(SelectControl, {
                                className: 'ar-col-status',
                                value: item.status,
                                options: [
                                    { label: 'Untested', value: 'untested' },
                                    { label: 'Validated', value: 'validated' },
                                    { label: 'Invalidated', value: 'invalidated' },
                                    { label: 'Partial', value: 'partial' }
                                ],
                                onChange: function(value) { updateAssumption(index, 'status', value); }
                            }),
                            el(Button, {
                                className: 'ar-col-actions',
                                variant: 'link',
                                isDestructive: true,
                                onClick: function() { removeAssumption(index); }
                            }, '×')
                        );
                    }),
                    el(Button, { variant: 'secondary', onClick: addAssumption }, '+ Add Assumption')
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

