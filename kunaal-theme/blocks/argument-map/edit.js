/**
 * Argument Map Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, Button, TextareaControl } = wp.components;
    const { __ } = wp.i18n;
    const { useState } = wp.element;
    const el = wp.element.createElement;

    registerBlockType('kunaal/argument-map', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { claim, supporting, opposing } = attributes;
            
            const blockProps = useBlockProps({
                className: 'argument-map'
            });
            
            const addSupporting = function() {
                setAttributes({ supporting: [...supporting, ''] });
            };
            
            const addOpposing = function() {
                setAttributes({ opposing: [...opposing, ''] });
            };
            
            const updateSupporting = function(index, value) {
                const updated = [...supporting];
                updated[index] = value;
                setAttributes({ supporting: updated });
            };
            
            const updateOpposing = function(index, value) {
                const updated = [...opposing];
                updated[index] = value;
                setAttributes({ opposing: updated });
            };
            
            const removeSupporting = function(index) {
                const updated = supporting.filter(function(_, i) { return i !== index; });
                setAttributes({ supporting: updated });
            };
            
            const removeOpposing = function(index) {
                const updated = opposing.filter(function(_, i) { return i !== index; });
                setAttributes({ opposing: updated });
            };

            return el(
                'div',
                blockProps,
                el(
                    'div',
                    { className: 'argument-claim' },
                    el('span', { className: 'argument-label' }, 'Main Claim'),
                    el(RichText, {
                        tagName: 'p',
                        className: 'claim-text',
                        value: claim,
                        onChange: function(value) { setAttributes({ claim: value }); },
                        placeholder: __('State the main claim or thesis...', 'kunaal-theme')
                    })
                ),
                el(
                    'div',
                    { className: 'argument-columns' },
                    el(
                        'div',
                        { className: 'argument-column supporting' },
                        el('h4', { className: 'column-title' }, 'Supporting Evidence'),
                        supporting.map(function(item, index) {
                            return el(
                                'div',
                                { key: index, className: 'argument-item' },
                                el(RichText, {
                                    tagName: 'p',
                                    value: item,
                                    onChange: function(value) { updateSupporting(index, value); },
                                    placeholder: __('Evidence...', 'kunaal-theme')
                                }),
                                el(Button, {
                                    variant: 'link',
                                    isDestructive: true,
                                    onClick: function() { removeSupporting(index); }
                                }, '×')
                            );
                        }),
                        el(Button, { 
                            variant: 'secondary', 
                            onClick: addSupporting,
                            className: 'add-item-btn'
                        }, '+ Add Supporting Point')
                    ),
                    el(
                        'div',
                        { className: 'argument-column opposing' },
                        el('h4', { className: 'column-title' }, 'Counter-Arguments'),
                        opposing.map(function(item, index) {
                            return el(
                                'div',
                                { key: index, className: 'argument-item' },
                                el(RichText, {
                                    tagName: 'p',
                                    value: item,
                                    onChange: function(value) { updateOpposing(index, value); },
                                    placeholder: __('Counter-argument...', 'kunaal-theme')
                                }),
                                el(Button, {
                                    variant: 'link',
                                    isDestructive: true,
                                    onClick: function() { removeOpposing(index); }
                                }, '×')
                            );
                        }),
                        el(Button, { 
                            variant: 'secondary', 
                            onClick: addOpposing,
                            className: 'add-item-btn'
                        }, '+ Add Counter-Argument')
                    )
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

