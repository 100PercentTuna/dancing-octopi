/**
 * What We Know / Don't Know Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText } = wp.blockEditor;
    const { Button } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/know-dont-know', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { knowItems, dontKnowItems } = attributes;
            
            const blockProps = useBlockProps({
                className: 'know-dont-know'
            });
            
            const addKnow = function() {
                setAttributes({ knowItems: [...knowItems, ''] });
            };
            
            const addDontKnow = function() {
                setAttributes({ dontKnowItems: [...dontKnowItems, ''] });
            };
            
            const updateKnow = function(index, value) {
                const updated = [...knowItems];
                updated[index] = value;
                setAttributes({ knowItems: updated });
            };
            
            const updateDontKnow = function(index, value) {
                const updated = [...dontKnowItems];
                updated[index] = value;
                setAttributes({ dontKnowItems: updated });
            };
            
            const removeKnow = function(index) {
                setAttributes({ knowItems: knowItems.filter(function(_, i) { return i !== index; }) });
            };
            
            const removeDontKnow = function(index) {
                setAttributes({ dontKnowItems: dontKnowItems.filter(function(_, i) { return i !== index; }) });
            };

            return el(
                'div',
                blockProps,
                el(
                    'div',
                    { className: 'kdk-columns' },
                    el(
                        'div',
                        { className: 'kdk-column know' },
                        el('h4', { className: 'kdk-title' }, '✓ What We Know'),
                        knowItems.map(function(item, index) {
                            return el(
                                'div',
                                { key: index, className: 'kdk-item' },
                                el(RichText, {
                                    tagName: 'p',
                                    value: item,
                                    onChange: function(value) { updateKnow(index, value); },
                                    placeholder: __('Known fact...', 'kunaal-theme')
                                }),
                                el(Button, {
                                    variant: 'link',
                                    isDestructive: true,
                                    onClick: function() { removeKnow(index); }
                                }, '×')
                            );
                        }),
                        el(Button, { variant: 'secondary', onClick: addKnow }, '+ Add')
                    ),
                    el(
                        'div',
                        { className: 'kdk-column dont-know' },
                        el('h4', { className: 'kdk-title' }, '? What We Don\'t Know'),
                        dontKnowItems.map(function(item, index) {
                            return el(
                                'div',
                                { key: index, className: 'kdk-item' },
                                el(RichText, {
                                    tagName: 'p',
                                    value: item,
                                    onChange: function(value) { updateDontKnow(index, value); },
                                    placeholder: __('Unknown or uncertain...', 'kunaal-theme')
                                }),
                                el(Button, {
                                    variant: 'link',
                                    isDestructive: true,
                                    onClick: function() { removeDontKnow(index); }
                                }, '×')
                            );
                        }),
                        el(Button, { variant: 'secondary', onClick: addDontKnow }, '+ Add')
                    )
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

