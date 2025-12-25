/**
 * Decision Entry Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/decision-entry', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { decision, date, rationale, status, outcome } = attributes;
            
            const blockProps = useBlockProps({
                className: 'decision-entry de-' + status
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Decision Details', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Date', 'kunaal-theme'),
                            value: date,
                            onChange: function(value) { setAttributes({ date: value }); },
                            placeholder: __('e.g., Jan 2024', 'kunaal-theme')
                        }),
                        el(SelectControl, {
                            label: __('Status', 'kunaal-theme'),
                            value: status,
                            options: [
                                { label: 'Active', value: 'active' },
                                { label: 'Superseded', value: 'superseded' },
                                { label: 'Reversed', value: 'reversed' }
                            ],
                            onChange: function(value) { setAttributes({ status: value }); }
                        }),
                        el(TextareaControl, {
                            label: __('Outcome (if known)', 'kunaal-theme'),
                            value: outcome,
                            onChange: function(value) { setAttributes({ outcome: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(
                        'div',
                        { className: 'de-header' },
                        el(RichText, {
                            tagName: 'h4',
                            className: 'de-decision',
                            value: decision,
                            onChange: function(value) { setAttributes({ decision: value }); },
                            placeholder: __('Decision made...', 'kunaal-theme')
                        }),
                        date ? el('span', { className: 'de-date' }, date) : null
                    ),
                    el(RichText, {
                        tagName: 'p',
                        className: 'de-rationale',
                        value: rationale,
                        onChange: function(value) { setAttributes({ rationale: value }); },
                        placeholder: __('Rationale: Why was this decision made?', 'kunaal-theme')
                    })
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

