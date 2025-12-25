/**
 * Kunaal Theme - Custom Inline Formats
 * Rich text formats for sophisticated essay writing
 * 
 * Formats:
 * - Sidenote: Blue bullet, shows margin note on hover
 * - Highlight: Warm highlight with optional annotation
 * - Definition: Dotted underline, shows definition on hover
 * - Key Term: Subtle emphasis for important concepts
 */

const { registerFormatType, toggleFormat, applyFormat, removeFormat } = wp.richText;
const { RichTextToolbarButton } = wp.blockEditor;
const { Popover, TextControl, Button, Modal } = wp.components;
const { useState, useCallback } = wp.element;
const { __ } = wp.i18n;

// ============================================
// SIDENOTE FORMAT
// ============================================
const SidenoteButton = ({ isActive, value, onChange }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [noteText, setNoteText] = useState('');

    const onApply = useCallback(() => {
        if (!noteText.trim()) return;
        
        onChange(applyFormat(value, {
            type: 'kunaal/sidenote',
            attributes: {
                note: noteText,
            },
        }));
        setNoteText('');
        setIsOpen(false);
    }, [noteText, value, onChange]);

    const onRemove = useCallback(() => {
        onChange(removeFormat(value, 'kunaal/sidenote'));
    }, [value, onChange]);

    return (
        <>
            <RichTextToolbarButton
                icon={
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <circle cx="12" cy="12" r="4" fill="currentColor"/>
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V5h14v14z" fill="currentColor" opacity="0.5"/>
                    </svg>
                }
                title={__('Sidenote', 'kunaal-theme')}
                onClick={() => isActive ? onRemove() : setIsOpen(true)}
                isActive={isActive}
            />
            {isOpen && (
                <Modal
                    title={__('Add Sidenote', 'kunaal-theme')}
                    onRequestClose={() => setIsOpen(false)}
                    className="kunaal-inline-modal"
                >
                    <TextControl
                        label={__('Note text', 'kunaal-theme')}
                        value={noteText}
                        onChange={setNoteText}
                        placeholder={__('Your marginal note...', 'kunaal-theme')}
                    />
                    <div style={{ display: 'flex', gap: '8px', marginTop: '16px' }}>
                        <Button variant="primary" onClick={onApply} disabled={!noteText.trim()}>
                            {__('Add Sidenote', 'kunaal-theme')}
                        </Button>
                        <Button variant="secondary" onClick={() => setIsOpen(false)}>
                            {__('Cancel', 'kunaal-theme')}
                        </Button>
                    </div>
                </Modal>
            )}
        </>
    );
};

registerFormatType('kunaal/sidenote', {
    title: __('Sidenote', 'kunaal-theme'),
    tagName: 'span',
    className: 'kunaal-sidenote',
    attributes: {
        note: 'data-note',
    },
    edit: SidenoteButton,
});

// ============================================
// HIGHLIGHT FORMAT (with optional annotation)
// ============================================
const HighlightButton = ({ isActive, value, onChange }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [annotation, setAnnotation] = useState('');

    const onApply = useCallback(() => {
        onChange(applyFormat(value, {
            type: 'kunaal/highlight',
            attributes: {
                annotation: annotation || '',
            },
        }));
        setAnnotation('');
        setIsOpen(false);
    }, [annotation, value, onChange]);

    const onRemove = useCallback(() => {
        onChange(removeFormat(value, 'kunaal/highlight'));
    }, [value, onChange]);

    const onQuickApply = useCallback(() => {
        // Quick apply without annotation
        onChange(applyFormat(value, {
            type: 'kunaal/highlight',
            attributes: {
                annotation: '',
            },
        }));
    }, [value, onChange]);

    return (
        <>
            <RichTextToolbarButton
                icon={
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor" opacity="0.5"/>
                        <rect x="6" y="8" width="7" height="3" fill="#FCD34D" rx="1"/>
                    </svg>
                }
                title={__('Highlight', 'kunaal-theme')}
                onClick={() => isActive ? onRemove() : setIsOpen(true)}
                isActive={isActive}
            />
            {isOpen && (
                <Modal
                    title={__('Highlight Text', 'kunaal-theme')}
                    onRequestClose={() => setIsOpen(false)}
                    className="kunaal-inline-modal"
                >
                    <TextControl
                        label={__('Annotation (optional)', 'kunaal-theme')}
                        value={annotation}
                        onChange={setAnnotation}
                        placeholder={__('Add a note about this highlight...', 'kunaal-theme')}
                    />
                    <p style={{ fontSize: '12px', color: '#666', marginTop: '8px' }}>
                        {__('Leave empty for a simple highlight, or add a note that appears on hover.', 'kunaal-theme')}
                    </p>
                    <div style={{ display: 'flex', gap: '8px', marginTop: '16px' }}>
                        <Button variant="primary" onClick={onApply}>
                            {annotation ? __('Add with Note', 'kunaal-theme') : __('Highlight', 'kunaal-theme')}
                        </Button>
                        <Button variant="secondary" onClick={() => setIsOpen(false)}>
                            {__('Cancel', 'kunaal-theme')}
                        </Button>
                    </div>
                </Modal>
            )}
        </>
    );
};

registerFormatType('kunaal/highlight', {
    title: __('Highlight', 'kunaal-theme'),
    tagName: 'mark',
    className: 'kunaal-highlight',
    attributes: {
        annotation: 'data-annotation',
    },
    edit: HighlightButton,
});

// ============================================
// DEFINITION FORMAT
// ============================================
const DefinitionButton = ({ isActive, value, onChange }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [definition, setDefinition] = useState('');

    const onApply = useCallback(() => {
        if (!definition.trim()) return;
        
        onChange(applyFormat(value, {
            type: 'kunaal/definition',
            attributes: {
                definition: definition,
            },
        }));
        setDefinition('');
        setIsOpen(false);
    }, [definition, value, onChange]);

    const onRemove = useCallback(() => {
        onChange(removeFormat(value, 'kunaal/definition'));
    }, [value, onChange]);

    return (
        <>
            <RichTextToolbarButton
                icon={
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <path d="M12.87 15.07l-2.54-2.51.03-.03A17.52 17.52 0 0014.07 6H17V4h-7V2H8v2H1v2h11.17C11.5 7.92 10.44 9.75 9 11.35 8.07 10.32 7.3 9.19 6.69 8h-2c.73 1.63 1.73 3.17 2.98 4.56l-5.09 5.02L4 19l5-5 3.11 3.11.76-2.04zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z" fill="currentColor"/>
                    </svg>
                }
                title={__('Definition', 'kunaal-theme')}
                onClick={() => isActive ? onRemove() : setIsOpen(true)}
                isActive={isActive}
            />
            {isOpen && (
                <Modal
                    title={__('Add Definition', 'kunaal-theme')}
                    onRequestClose={() => setIsOpen(false)}
                    className="kunaal-inline-modal"
                >
                    <TextControl
                        label={__('Definition', 'kunaal-theme')}
                        value={definition}
                        onChange={setDefinition}
                        placeholder={__('Define this term...', 'kunaal-theme')}
                    />
                    <div style={{ display: 'flex', gap: '8px', marginTop: '16px' }}>
                        <Button variant="primary" onClick={onApply} disabled={!definition.trim()}>
                            {__('Add Definition', 'kunaal-theme')}
                        </Button>
                        <Button variant="secondary" onClick={() => setIsOpen(false)}>
                            {__('Cancel', 'kunaal-theme')}
                        </Button>
                    </div>
                </Modal>
            )}
        </>
    );
};

registerFormatType('kunaal/definition', {
    title: __('Definition', 'kunaal-theme'),
    tagName: 'span',
    className: 'kunaal-definition',
    attributes: {
        definition: 'data-definition',
    },
    edit: DefinitionButton,
});

// ============================================
// KEY TERM FORMAT (simple emphasis)
// ============================================
const KeyTermButton = ({ isActive, value, onChange }) => {
    const onToggle = useCallback(() => {
        onChange(toggleFormat(value, { type: 'kunaal/key-term' }));
    }, [value, onChange]);

    return (
        <RichTextToolbarButton
            icon={
                <svg viewBox="0 0 24 24" width="20" height="20">
                    <path d="M12.65 10C11.83 7.67 9.61 6 7 6c-3.31 0-6 2.69-6 6s2.69 6 6 6c2.61 0 4.83-1.67 5.65-4H17v4h4v-4h2v-4H12.65zM7 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z" fill="currentColor"/>
                </svg>
            }
            title={__('Key Term', 'kunaal-theme')}
            onClick={onToggle}
            isActive={isActive}
        />
    );
};

registerFormatType('kunaal/key-term', {
    title: __('Key Term', 'kunaal-theme'),
    tagName: 'span',
    className: 'kunaal-key-term',
    edit: KeyTermButton,
});

// ============================================
// DATA REFERENCE FORMAT (for statistics)
// ============================================
const DataRefButton = ({ isActive, value, onChange }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [source, setSource] = useState('');
    const [year, setYear] = useState('');

    const onApply = useCallback(() => {
        onChange(applyFormat(value, {
            type: 'kunaal/data-ref',
            attributes: {
                source: source || '',
                year: year || '',
            },
        }));
        setSource('');
        setYear('');
        setIsOpen(false);
    }, [source, year, value, onChange]);

    const onRemove = useCallback(() => {
        onChange(removeFormat(value, 'kunaal/data-ref'));
    }, [value, onChange]);

    return (
        <>
            <RichTextToolbarButton
                icon={
                    <svg viewBox="0 0 24 24" width="20" height="20">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" fill="currentColor"/>
                    </svg>
                }
                title={__('Data Reference', 'kunaal-theme')}
                onClick={() => isActive ? onRemove() : setIsOpen(true)}
                isActive={isActive}
            />
            {isOpen && (
                <Modal
                    title={__('Add Data Reference', 'kunaal-theme')}
                    onRequestClose={() => setIsOpen(false)}
                    className="kunaal-inline-modal"
                >
                    <TextControl
                        label={__('Source', 'kunaal-theme')}
                        value={source}
                        onChange={setSource}
                        placeholder={__('e.g., World Bank, McKinsey', 'kunaal-theme')}
                    />
                    <TextControl
                        label={__('Year (optional)', 'kunaal-theme')}
                        value={year}
                        onChange={setYear}
                        placeholder={__('e.g., 2024', 'kunaal-theme')}
                    />
                    <div style={{ display: 'flex', gap: '8px', marginTop: '16px' }}>
                        <Button variant="primary" onClick={onApply}>
                            {__('Add Reference', 'kunaal-theme')}
                        </Button>
                        <Button variant="secondary" onClick={() => setIsOpen(false)}>
                            {__('Cancel', 'kunaal-theme')}
                        </Button>
                    </div>
                </Modal>
            )}
        </>
    );
};

registerFormatType('kunaal/data-ref', {
    title: __('Data Reference', 'kunaal-theme'),
    tagName: 'span',
    className: 'kunaal-data-ref',
    attributes: {
        source: 'data-source',
        year: 'data-year',
    },
    edit: DataRefButton,
});

