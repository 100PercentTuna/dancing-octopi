/**
 * Timeline Item Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, RichText } = wp.blockEditor;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/timeline-item', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { date, title, description } = attributes;
            
            const blockProps = useBlockProps({
                className: 'timeline-item'
            });

            return el(
                'div',
                blockProps,
                el('div', { className: 'timeline-marker' }),
                el(
                    'div',
                    { className: 'timeline-content' },
                    el(RichText, {
                        tagName: 'span',
                        className: 'timeline-date',
                        value: date,
                        onChange: function(value) { setAttributes({ date: value }); },
                        placeholder: __('Date...', 'kunaal-theme')
                    }),
                    el(RichText, {
                        tagName: 'h4',
                        className: 'timeline-event-title',
                        value: title,
                        onChange: function(value) { setAttributes({ title: value }); },
                        placeholder: __('Event title...', 'kunaal-theme')
                    }),
                    el(RichText, {
                        tagName: 'p',
                        className: 'timeline-description',
                        value: description,
                        onChange: function(value) { setAttributes({ description: value }); },
                        placeholder: __('Description...', 'kunaal-theme')
                    })
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

