/**
 * Debate Side Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, Button, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/debate-side', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { position, label, argument, points } = attributes;
            
            const blockProps = useBlockProps({
                className: 'debate-side side-' + position
            });
            
            const addPoint = function() {
                setAttributes({ points: [...points, ''] });
            };
            
            const updatePoint = function(index, value) {
                const updated = [...points];
                updated[index] = value;
                setAttributes({ points: updated });
            };
            
            const removePoint = function(index) {
                setAttributes({ points: points.filter(function(_, i) { return i !== index; }) });
            };

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Side Settings', 'kunaal-theme'), initialOpen: true },
                        el(TextControl, {
                            label: __('Label', 'kunaal-theme'),
                            value: label,
                            onChange: function(value) { setAttributes({ label: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el('span', { className: 'ds-label' }, label),
                    el(RichText, {
                        tagName: 'p',
                        className: 'ds-argument',
                        value: argument,
                        onChange: function(value) { setAttributes({ argument: value }); },
                        placeholder: __('Main argument...', 'kunaal-theme')
                    }),
                    el(
                        'ul',
                        { className: 'ds-points' },
                        points.map(function(point, index) {
                            return el(
                                'li',
                                { key: index },
                                el(RichText, {
                                    tagName: 'span',
                                    value: point,
                                    onChange: function(value) { updatePoint(index, value); },
                                    placeholder: __('Supporting point...', 'kunaal-theme')
                                }),
                                el(Button, {
                                    variant: 'link',
                                    isDestructive: true,
                                    onClick: function() { removePoint(index); }
                                }, '×')
                            );
                        })
                    ),
                    el(Button, { variant: 'link', onClick: addPoint }, '+ Add Point')
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

