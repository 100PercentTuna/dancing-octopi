/**
 * Rubric Row Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText } = wp.blockEditor;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/rubric-row', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { criteria, levels } = attributes;
            
            const blockProps = useBlockProps({
                className: 'rubric-row'
            });
            
            const updateLevel = function(index, value) {
                const updated = [...levels];
                updated[index] = value;
                setAttributes({ levels: updated });
            };

            return el(
                'tr',
                blockProps,
                el(
                    'td',
                    { className: 'rubric-criteria' },
                    el(RichText, {
                        tagName: 'span',
                        value: criteria,
                        onChange: function(value) { setAttributes({ criteria: value }); },
                        placeholder: __('Criterion...', 'kunaal-theme')
                    })
                ),
                levels.map(function(level, index) {
                    return el(
                        'td',
                        { key: index, className: 'rubric-level-cell' },
                        el(RichText, {
                            tagName: 'span',
                            value: level,
                            onChange: function(value) { updateLevel(index, value); },
                            placeholder: __('Description...', 'kunaal-theme')
                        })
                    );
                })
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

