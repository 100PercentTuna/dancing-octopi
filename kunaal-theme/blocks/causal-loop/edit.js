/**
 * Causal Loop Diagram Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, SelectControl, Button, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/causal-loop', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { title, nodes, description } = attributes;
            
            const blockProps = useBlockProps({
                className: 'causal-loop'
            });
            
            const addNode = function() {
                setAttributes({
                    nodes: [...nodes, {
                        label: 'Variable ' + (nodes.length + 1),
                        effect: 'positive',
                        next: ''
                    }]
                });
            };
            
            const updateNode = function(index, field, value) {
                const updated = [...nodes];
                updated[index] = { ...updated[index], [field]: value };
                setAttributes({ nodes: updated });
            };
            
            const removeNode = function(index) {
                setAttributes({ nodes: nodes.filter(function(_, i) { return i !== index; }) });
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
                        }),
                        el(TextareaControl, {
                            label: __('Description', 'kunaal-theme'),
                            value: description,
                            onChange: function(value) { setAttributes({ description: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    title ? el('h3', { className: 'cl-title' }, title) : null,
                    el(
                        'div',
                        { className: 'cl-diagram' },
                        nodes.map(function(node, index) {
                            return el(
                                'div',
                                { key: index, className: 'cl-node' },
                                el(RichText, {
                                    tagName: 'span',
                                    className: 'cl-node-label',
                                    value: node.label,
                                    onChange: function(value) { updateNode(index, 'label', value); },
                                    placeholder: __('Variable...', 'kunaal-theme')
                                }),
                                index < nodes.length - 1 ? el(
                                    'div',
                                    { className: 'cl-arrow cl-' + node.effect },
                                    el(SelectControl, {
                                        value: node.effect,
                                        options: [
                                            { label: '+', value: 'positive' },
                                            { label: '−', value: 'negative' }
                                        ],
                                        onChange: function(value) { updateNode(index, 'effect', value); }
                                    })
                                ) : null,
                                el(Button, {
                                    className: 'cl-remove',
                                    variant: 'link',
                                    isDestructive: true,
                                    onClick: function() { removeNode(index); }
                                }, '×')
                            );
                        }),
                        nodes.length > 0 ? el(
                            'div',
                            { className: 'cl-loop-arrow cl-' + (nodes[nodes.length - 1]?.effect || 'positive') },
                            el('span', null, nodes.length > 0 ? (nodes[nodes.length - 1]?.effect === 'positive' ? '+' : '−') : '+'),
                            ' loops back'
                        ) : null
                    ),
                    el(Button, { variant: 'secondary', onClick: addNode }, '+ Add Variable'),
                    description ? el('p', { className: 'cl-description' }, description) : null
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

