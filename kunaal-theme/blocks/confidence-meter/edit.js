/**
 * Confidence Meter Block - Editor
 */
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { useBlockProps, InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, TextControl, RangeControl, ToggleControl } = wp.components;
    const { __ } = wp.i18n;
    const el = wp.element.createElement;

    registerBlockType('kunaal/confidence-meter', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { label, level, description, showPercentage } = attributes;
            
            const getColor = function(val) {
                if (val >= 70) return '#16a34a';
                if (val >= 40) return '#eab308';
                return '#dc2626';
            };
            
            const blockProps = useBlockProps({
                className: 'confidence-meter'
            });

            return el(
                wp.element.Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Confidence Settings', 'kunaal-theme'), initialOpen: true },
                        el(RangeControl, {
                            label: __('Confidence Level', 'kunaal-theme'),
                            value: level,
                            onChange: function(value) { setAttributes({ level: value }); },
                            min: 0,
                            max: 100,
                            step: 5
                        }),
                        el(ToggleControl, {
                            label: __('Show Percentage', 'kunaal-theme'),
                            checked: showPercentage,
                            onChange: function(value) { setAttributes({ showPercentage: value }); }
                        })
                    )
                ),
                el(
                    'div',
                    blockProps,
                    el(
                        'div',
                        { className: 'cm-header' },
                        el(RichText, {
                            tagName: 'span',
                            className: 'cm-label',
                            value: label,
                            onChange: function(value) { setAttributes({ label: value }); },
                            placeholder: __('Claim or statement...', 'kunaal-theme')
                        }),
                        showPercentage ? el('span', { className: 'cm-percentage', style: { color: getColor(level) } }, level + '%') : null
                    ),
                    el(
                        'div',
                        { className: 'cm-bar-container' },
                        el('div', { 
                            className: 'cm-bar', 
                            style: { width: level + '%', background: getColor(level) }
                        })
                    ),
                    el(RichText, {
                        tagName: 'p',
                        className: 'cm-description',
                        value: description,
                        onChange: function(value) { setAttributes({ description: value }); },
                        placeholder: __('Reasoning or context...', 'kunaal-theme')
                    })
                )
            );
        },
        save: function() {
            return null;
        }
    });
})(window.wp);

