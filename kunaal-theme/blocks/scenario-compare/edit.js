/**
 * Scenario Comparison Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl, Button, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/scenario-compare', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, scenarios } = attributes;
            
            const blockProps = useBlockProps({
                className: 'scenario-compare'
            });
            
            const addScenario = function() {
                setAttributes({
                    scenarios: [...scenarios, {
                        name: 'Scenario ' + (scenarios.length + 1),
                        probability: 'medium',
                        description: '',
                        outcome: ''
                    }]
                });
            };
            
            const updateScenario = function(index, field, value) {
                const updated = [...scenarios];
                updated[index] = { ...updated[index], [field]: value };
                setAttributes({ scenarios: updated });
            };
            
            const removeScenario = function(index) {
                setAttributes({ scenarios: scenarios.filter(function(_, i) { return i !== index; }) });
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
                            label: __('Section Title', 'kunaal-theme'),
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    title ? el('h3', { className: 'sc-title' }, title) : null,
                    el(
                        'div',
                        { className: 'sc-grid', style: { gridTemplateColumns: 'repeat(' + Math.max(scenarios.length, 1) + ', 1fr)' } },
                        scenarios.map(function(scenario, index) {
                            return el(
                                'div',
                                { key: index, className: 'sc-card sc-' + scenario.probability },
                                el(
                                    'div',
                                    { className: 'sc-card-header' },
                                    el(RichText, {
                                        tagName: 'span',
                                        className: 'sc-name',
                                        value: scenario.name,
                                        onChange: function(value) { updateScenario(index, 'name', value); },
                                        placeholder: __('Scenario name...', 'kunaal-theme')
                                    }),
                                    el(SelectControl, {
                                        className: 'sc-probability',
                                        value: scenario.probability,
                                        options: [
                                            { label: 'High Likelihood', value: 'high' },
                                            { label: 'Medium', value: 'medium' },
                                            { label: 'Low Likelihood', value: 'low' }
                                        ],
                                        onChange: function(value) { updateScenario(index, 'probability', value); }
                                    }),
                                    el(Button, {
                                        variant: 'link',
                                        isDestructive: true,
                                        onClick: function() { removeScenario(index); }
                                    }, '×')
                                ),
                                el(RichText, {
                                    tagName: 'p',
                                    className: 'sc-description',
                                    value: scenario.description,
                                    onChange: function(value) { updateScenario(index, 'description', value); },
                                    placeholder: __('Scenario description...', 'kunaal-theme')
                                }),
                                el(RichText, {
                                    tagName: 'p',
                                    className: 'sc-outcome',
                                    value: scenario.outcome,
                                    onChange: function(value) { updateScenario(index, 'outcome', value); },
                                    placeholder: __('Expected outcome...', 'kunaal-theme')
                                })
                            );
                        })
                    ),
                    el(Button, { variant: 'secondary', onClick: addScenario }, '+ Add Scenario')
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

