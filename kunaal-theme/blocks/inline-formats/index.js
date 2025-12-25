/**
 * Kunaal Theme - Custom Inline Formats for Gutenberg
 * 
 * Adds rich text formats: Sidenote, Highlight, Definition, Key Term, Data Reference
 */

(function() {
    'use strict';
    
    // Safety check - wait for WordPress to be ready
    if (typeof wp === 'undefined' || !wp.richText || !wp.blockEditor || !wp.element) {
        console.warn('Kunaal Inline Formats: WordPress dependencies not available');
        return;
    }
    
    const { registerFormatType, toggleFormat, applyFormat } = wp.richText;
    const { RichTextToolbarButton } = wp.blockEditor;
    const { createElement: el, useState, Fragment } = wp.element;
    const { Modal, TextControl, Button } = wp.components;
    
    // =========================================
    // 1. SIDENOTE FORMAT
    // =========================================
    registerFormatType('kunaal/sidenote', {
        title: 'Sidenote',
        tagName: 'span',
        className: 'kunaal-sidenote',
        attributes: {
            'data-note': 'data-note'
        },
        edit: function(props) {
            const { isActive, value, onChange } = props;
            const [isModalOpen, setModalOpen] = useState(false);
            const [noteText, setNoteText] = useState('');
            
            return el(Fragment, null,
                el(RichTextToolbarButton, {
                    icon: 'admin-comments',
                    title: 'Sidenote',
                    onClick: function() {
                        if (isActive) {
                            onChange(toggleFormat(value, { type: 'kunaal/sidenote' }));
                        } else {
                            setModalOpen(true);
                        }
                    },
                    isActive: isActive
                }),
                isModalOpen && el(Modal, {
                    title: 'Add Sidenote',
                    onRequestClose: function() { setModalOpen(false); }
                },
                    el('div', { style: { minWidth: '300px' } },
                        el(TextControl, {
                            label: 'Sidenote text',
                            value: noteText,
                            onChange: setNoteText,
                            placeholder: 'Enter your sidenote...'
                        }),
                        el(Button, {
                            variant: 'primary',
                            onClick: function() {
                                onChange(applyFormat(value, {
                                    type: 'kunaal/sidenote',
                                    attributes: { 'data-note': noteText }
                                }));
                                setModalOpen(false);
                                setNoteText('');
                            }
                        }, 'Add Sidenote')
                    )
                )
            );
        }
    });
    
    // =========================================
    // 2. HIGHLIGHT FORMAT
    // =========================================
    registerFormatType('kunaal/highlight', {
        title: 'Highlight',
        tagName: 'mark',
        className: 'kunaal-highlight',
        edit: function(props) {
            return el(RichTextToolbarButton, {
                icon: 'admin-customizer',
                title: 'Highlight',
                onClick: function() {
                    props.onChange(toggleFormat(props.value, { type: 'kunaal/highlight' }));
                },
                isActive: props.isActive
            });
        }
    });
    
    // =========================================
    // 3. DEFINITION FORMAT
    // =========================================
    registerFormatType('kunaal/definition', {
        title: 'Definition',
        tagName: 'dfn',
        className: 'kunaal-definition',
        attributes: {
            'data-definition': 'data-definition'
        },
        edit: function(props) {
            const { isActive, value, onChange } = props;
            const [isModalOpen, setModalOpen] = useState(false);
            const [defText, setDefText] = useState('');
            
            return el(Fragment, null,
                el(RichTextToolbarButton, {
                    icon: 'book',
                    title: 'Definition',
                    onClick: function() {
                        if (isActive) {
                            onChange(toggleFormat(value, { type: 'kunaal/definition' }));
                        } else {
                            setModalOpen(true);
                        }
                    },
                    isActive: isActive
                }),
                isModalOpen && el(Modal, {
                    title: 'Add Definition',
                    onRequestClose: function() { setModalOpen(false); }
                },
                    el('div', { style: { minWidth: '300px' } },
                        el(TextControl, {
                            label: 'Definition',
                            value: defText,
                            onChange: setDefText,
                            placeholder: 'Enter the definition...'
                        }),
                        el(Button, {
                            variant: 'primary',
                            onClick: function() {
                                onChange(applyFormat(value, {
                                    type: 'kunaal/definition',
                                    attributes: { 'data-definition': defText }
                                }));
                                setModalOpen(false);
                                setDefText('');
                            }
                        }, 'Add Definition')
                    )
                )
            );
        }
    });
    
    // =========================================
    // 4. KEY TERM FORMAT
    // =========================================
    registerFormatType('kunaal/key-term', {
        title: 'Key Term',
        tagName: 'strong',
        className: 'kunaal-key-term',
        edit: function(props) {
            return el(RichTextToolbarButton, {
                icon: 'tag',
                title: 'Key Term',
                onClick: function() {
                    props.onChange(toggleFormat(props.value, { type: 'kunaal/key-term' }));
                },
                isActive: props.isActive
            });
        }
    });
    
    // =========================================
    // 5. DATA REFERENCE FORMAT
    // =========================================
    registerFormatType('kunaal/data-ref', {
        title: 'Data Reference',
        tagName: 'span',
        className: 'kunaal-data-ref',
        attributes: {
            'data-source': 'data-source',
            'data-value': 'data-value'
        },
        edit: function(props) {
            const { isActive, value, onChange } = props;
            const [isModalOpen, setModalOpen] = useState(false);
            const [dataValue, setDataValue] = useState('');
            const [dataSource, setDataSource] = useState('');
            
            return el(Fragment, null,
                el(RichTextToolbarButton, {
                    icon: 'chart-bar',
                    title: 'Data Reference',
                    onClick: function() {
                        if (isActive) {
                            onChange(toggleFormat(value, { type: 'kunaal/data-ref' }));
                        } else {
                            setModalOpen(true);
                        }
                    },
                    isActive: isActive
                }),
                isModalOpen && el(Modal, {
                    title: 'Add Data Reference',
                    onRequestClose: function() { setModalOpen(false); }
                },
                    el('div', { style: { minWidth: '300px' } },
                        el(TextControl, {
                            label: 'Data Value (e.g., 42%, $1.2M)',
                            value: dataValue,
                            onChange: setDataValue,
                            placeholder: 'Enter the data value...'
                        }),
                        el(TextControl, {
                            label: 'Source (optional)',
                            value: dataSource,
                            onChange: setDataSource,
                            placeholder: 'e.g., World Bank 2024'
                        }),
                        el(Button, {
                            variant: 'primary',
                            onClick: function() {
                                onChange(applyFormat(value, {
                                    type: 'kunaal/data-ref',
                                    attributes: {
                                        'data-value': dataValue,
                                        'data-source': dataSource
                                    }
                                }));
                                setModalOpen(false);
                                setDataValue('');
                                setDataSource('');
                            }
                        }, 'Add Data Reference')
                    )
                )
            );
        }
    });
    
    console.log('Kunaal Inline Formats: Registered 5 formats');
})();

