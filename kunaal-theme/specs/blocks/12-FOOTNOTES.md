# Footnotes & Endnotes System Specification

> **Blocks:** `kunaal/footnote` (inline), `kunaal/footnotes-section` (endnotes)  
> **Category:** `kunaal-editorial`  
> **Status:** Enhancement of existing blocks

---

## 1. Overview

A comprehensive footnote/endnote system that supports academic citations, editorial notes, tangential thoughts, and source references with elegant typography and smooth interactions.

### 1.1 Terminology
- **Footnote:** Brief note referenced inline, displayed at bottom of page/section
- **Endnote:** Note collected at end of article
- **Sidenote:** Note displayed in margin (existing block, enhanced)
- **Citation:** Formal reference to source material

### 1.2 Requirements
- Inline footnote markers (superscript numbers)
- Automatic numbering across article
- Jump-to and back-to navigation
- Mobile-friendly tooltip preview
- Endnotes section block
- Multiple footnote styles (numbered, symbols, author notes)
- Print-friendly output
- Accessibility (screen reader support)

---

## 2. Visual Design

### 2.1 Inline Footnote Marker

```
This is regular text with a footnote¹ that continues.
                                  ↑
                          Superscript number
                          Clickable/tappable
                          Brown color (#7D6B5D)
```

### 2.2 Hover/Focus State

```
This is regular text with a footnote¹ that continues.
                                    ╔═══════════════════════════════╗
                                    ║ ¹ According to Smith (2020),  ║
                                    ║ this phenomenon was first     ║
                                    ║ documented in 1985.           ║
                                    ╚═══════════════════════════════╝
                                    ↑ Tooltip appears above/below marker
```

### 2.3 Endnotes Section

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  ────────────────────────────────                               │
│                                                                 │
│  Notes                                                          │
│                                                                 │
│  1. According to Smith (2020), this phenomenon was first        │
│     documented in 1985. See also Johnson (2018) for a          │
│     contrasting view.  [↩]                                      │
│                                                                 │
│  2. The methodology here follows the standard approach          │
│     outlined in Chapter 3, with modifications for               │
│     our specific context.  [↩]                                  │
│                                                                 │
│  3. Author's note: This section was revised after              │
│     receiving feedback from peer reviewers.  [↩]                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 2.4 Typography

| Element | Font | Size | Weight | Color |
|---------|------|------|--------|-------|
| Inline marker | ui-monospace | 12px | 500 | `--warm` |
| Tooltip text | Inter | 13px | 400 | `--ink` |
| Endnote number | ui-monospace | 13px | 500 | `--warm` |
| Endnote text | Newsreader | 15px | 400 | `--ink` |
| Back link | — | 12px | — | `--blue` |
| Section heading | Inter | 14px | 600 | `--muted` |

### 2.5 Styling

#### Inline Marker
```css
.footnote-ref {
  font-family: var(--mono);
  font-size: 0.75em;
  font-weight: 500;
  color: var(--warm);
  text-decoration: none;
  cursor: pointer;
  position: relative;
  top: -0.4em;
  padding: 0 2px;
  transition: color 200ms ease;
}

.footnote-ref:hover,
.footnote-ref:focus {
  color: var(--ink);
}

.footnote-ref:focus-visible {
  outline: 2px solid var(--blue);
  outline-offset: 2px;
  border-radius: 2px;
}
```

#### Tooltip
```css
.footnote-tooltip {
  position: absolute;
  max-width: 320px;
  padding: 12px 16px;
  background: var(--bg);
  border: 1px solid var(--warmLight);
  border-radius: 6px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.12);
  font-size: 13px;
  line-height: 1.6;
  z-index: 1000;
  opacity: 0;
  transform: translateY(4px);
  transition: opacity 200ms ease, transform 200ms ease;
  pointer-events: none;
}

.footnote-tooltip.visible {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}

.footnote-tooltip-arrow {
  /* CSS triangle pointing to marker */
}
```

#### Endnotes Section
```css
.footnotes-section {
  margin-top: var(--space-12);
  padding-top: var(--space-8);
  border-top: 1px solid var(--warmLight);
}

.footnotes-heading {
  font-family: var(--sans);
  font-size: 14px;
  font-weight: 600;
  color: var(--muted);
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: var(--space-4);
}

.footnote-item {
  display: flex;
  gap: 12px;
  margin-bottom: var(--space-3);
  font-family: var(--serif);
  font-size: 15px;
  line-height: 1.7;
}

.footnote-number {
  font-family: var(--mono);
  font-size: 13px;
  color: var(--warm);
  flex-shrink: 0;
  width: 24px;
}

.footnote-backref {
  color: var(--blue);
  text-decoration: none;
  font-size: 12px;
  margin-left: 4px;
}

.footnote-backref:hover {
  text-decoration: underline;
}
```

---

## 3. Block Attributes

### 3.1 Footnote Inline Format

```javascript
// Registered as a RichText format, not a block
registerFormatType('kunaal/footnote', {
  title: 'Footnote',
  tagName: 'span',
  className: 'footnote-ref',
  attributes: {
    noteId: 'data-note-id',
  },
  edit: FootnoteFormatEdit,
});
```

### 3.2 Footnotes Section Block

```json
{
  "name": "kunaal/footnotes-section",
  "title": "Footnotes",
  "category": "kunaal-editorial",
  "attributes": {
    "heading": {
      "type": "string",
      "default": "Notes"
    },
    "headingLevel": {
      "type": "string",
      "enum": ["h2", "h3", "h4", "none"],
      "default": "h3"
    },
    "style": {
      "type": "string",
      "enum": ["numbered", "symbols", "author"],
      "default": "numbered"
    },
    "showBacklinks": {
      "type": "boolean",
      "default": true
    },
    "collectFromPage": {
      "type": "boolean",
      "default": true
    },
    "notes": {
      "type": "array",
      "default": [],
      "items": {
        "type": "object",
        "properties": {
          "id": { "type": "string" },
          "content": { "type": "string" },
          "type": { "type": "string" }
        }
      }
    }
  }
}
```

---

## 4. Footnote Creation Workflow

### 4.1 Inline Creation

```
User Flow:
1. User types text in paragraph
2. User selects word/phrase or places cursor
3. User clicks "Footnote" button in format toolbar
4. Popup appears for note content
5. User types note and clicks Add
6. Superscript number appears inline
7. Note auto-added to Footnotes Section (or created if none)
```

### 4.2 Toolbar Button

```jsx
// In format toolbar
<ToolbarButton
  icon={footnoteIcon}
  title="Add Footnote (Ctrl+Shift+F)"
  onClick={openFootnotePopover}
  isActive={isActive}
/>

// Popover
<Popover>
  <TextareaControl
    label="Footnote Content"
    value={noteContent}
    onChange={setNoteContent}
    placeholder="Enter your footnote..."
  />
  <ButtonGroup>
    <Button variant="secondary" onClick={close}>Cancel</Button>
    <Button variant="primary" onClick={insertFootnote}>Add Footnote</Button>
  </ButtonGroup>
</Popover>
```

### 4.3 Keyboard Shortcut

- **Ctrl+Shift+F** — Insert footnote at cursor

---

## 5. Automatic Numbering

### 5.1 Number Management

```javascript
// Footnote registry (in-memory store during editing)
const footnoteStore = {
  notes: new Map(), // id -> { content, number, refElement }
  counter: 0,
  
  add(id, content) {
    this.counter++;
    this.notes.set(id, { 
      content, 
      number: this.counter,
      id 
    });
    return this.counter;
  },
  
  remove(id) {
    this.notes.delete(id);
    this.renumber();
  },
  
  renumber() {
    let num = 1;
    this.notes.forEach((note, id) => {
      note.number = num++;
    });
  },
  
  getAll() {
    return Array.from(this.notes.values()).sort((a, b) => a.number - b.number);
  }
};
```

### 5.2 Frontend Renumbering

```javascript
// On page load, renumber footnotes based on DOM order
document.addEventListener('DOMContentLoaded', () => {
  const refs = document.querySelectorAll('.footnote-ref');
  const section = document.querySelector('.footnotes-section');
  
  if (!section) return;
  
  const notes = section.querySelectorAll('.footnote-item');
  const noteMap = new Map();
  
  // Map note IDs to content
  notes.forEach(note => {
    noteMap.set(note.dataset.noteId, note);
  });
  
  // Renumber based on appearance order
  refs.forEach((ref, index) => {
    const num = index + 1;
    ref.textContent = num;
    ref.setAttribute('aria-label', `Footnote ${num}`);
    
    const noteId = ref.dataset.noteId;
    const noteEl = noteMap.get(noteId);
    if (noteEl) {
      noteEl.querySelector('.footnote-number').textContent = `${num}.`;
      noteEl.dataset.order = num;
    }
  });
  
  // Reorder notes in section
  const orderedNotes = Array.from(notes).sort(
    (a, b) => parseInt(a.dataset.order) - parseInt(b.dataset.order)
  );
  orderedNotes.forEach(note => section.appendChild(note));
});
```

---

## 6. Navigation Behavior

### 6.1 Click on Inline Marker

```javascript
document.querySelectorAll('.footnote-ref').forEach(ref => {
  ref.addEventListener('click', (e) => {
    e.preventDefault();
    const noteId = ref.dataset.noteId;
    const noteEl = document.querySelector(`.footnote-item[data-note-id="${noteId}"]`);
    
    if (noteEl) {
      // Smooth scroll to note
      noteEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      
      // Highlight briefly
      noteEl.classList.add('footnote-item--highlighted');
      setTimeout(() => {
        noteEl.classList.remove('footnote-item--highlighted');
      }, 2000);
      
      // Focus for accessibility
      noteEl.setAttribute('tabindex', '-1');
      noteEl.focus();
    }
  });
});
```

### 6.2 Back Link in Endnotes

```javascript
document.querySelectorAll('.footnote-backref').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    const noteId = link.closest('.footnote-item').dataset.noteId;
    const ref = document.querySelector(`.footnote-ref[data-note-id="${noteId}"]`);
    
    if (ref) {
      ref.scrollIntoView({ behavior: 'smooth', block: 'center' });
      ref.focus();
    }
  });
});
```

---

## 7. Tooltip Preview

### 7.1 Desktop Behavior

```javascript
let tooltipTimeout;
let currentTooltip = null;

document.querySelectorAll('.footnote-ref').forEach(ref => {
  ref.addEventListener('mouseenter', () => {
    tooltipTimeout = setTimeout(() => {
      showTooltip(ref);
    }, 300); // Delay to prevent accidental triggers
  });
  
  ref.addEventListener('mouseleave', () => {
    clearTimeout(tooltipTimeout);
    hideTooltip();
  });
  
  ref.addEventListener('focus', () => {
    showTooltip(ref);
  });
  
  ref.addEventListener('blur', () => {
    hideTooltip();
  });
});

function showTooltip(ref) {
  const noteId = ref.dataset.noteId;
  const noteEl = document.querySelector(`.footnote-item[data-note-id="${noteId}"]`);
  
  if (!noteEl) return;
  
  const content = noteEl.querySelector('.footnote-content').innerHTML;
  
  // Create tooltip
  currentTooltip = document.createElement('div');
  currentTooltip.className = 'footnote-tooltip';
  currentTooltip.innerHTML = content;
  
  // Position relative to ref
  document.body.appendChild(currentTooltip);
  positionTooltip(currentTooltip, ref);
  
  // Animate in
  requestAnimationFrame(() => {
    currentTooltip.classList.add('visible');
  });
}
```

### 7.2 Mobile Behavior

On touch devices, tap shows tooltip:

```javascript
if ('ontouchstart' in window) {
  refs.forEach(ref => {
    ref.addEventListener('click', (e) => {
      e.preventDefault();
      if (currentTooltip) {
        hideTooltip();
      } else {
        showTooltip(ref);
      }
    });
  });
  
  // Tap elsewhere to close
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.footnote-ref, .footnote-tooltip')) {
      hideTooltip();
    }
  });
}
```

---

## 8. Editor Interface

### 8.1 Footnotes Section Block Settings

```jsx
<InspectorControls>
  <PanelBody title="Footnotes Settings" initialOpen>
    <TextControl
      label="Section Heading"
      value={heading}
      onChange={(v) => setAttributes({ heading: v })}
      placeholder="Notes"
    />
    
    <SelectControl
      label="Heading Level"
      value={headingLevel}
      options={[
        { label: 'H2', value: 'h2' },
        { label: 'H3', value: 'h3' },
        { label: 'H4', value: 'h4' },
        { label: 'No heading', value: 'none' },
      ]}
    />
    
    <SelectControl
      label="Numbering Style"
      value={style}
      options={[
        { label: '1, 2, 3...', value: 'numbered' },
        { label: '*, †, ‡...', value: 'symbols' },
        { label: '[Author note]', value: 'author' },
      ]}
    />
    
    <ToggleControl
      label="Show back links"
      checked={showBacklinks}
      help="Show ↩ link to jump back to reference"
    />
    
    <ToggleControl
      label="Collect footnotes automatically"
      checked={collectFromPage}
      help="Automatically gather all footnotes from the page"
    />
  </PanelBody>
  
  {!collectFromPage && (
    <PanelBody title="Manual Notes">
      {notes.map((note, index) => (
        <div key={note.id} className="manual-note-item">
          <TextareaControl
            label={`Note ${index + 1}`}
            value={note.content}
            onChange={(v) => updateNote(note.id, v)}
          />
          <Button 
            isDestructive 
            onClick={() => removeNote(note.id)}
          >
            Remove
          </Button>
        </div>
      ))}
      <Button onClick={addManualNote}>Add Note</Button>
    </PanelBody>
  )}
</InspectorControls>
```

---

## 9. Accessibility

### 9.1 ARIA Structure

```html
<!-- Inline reference -->
<a href="#fn-1" 
   id="fnref-1" 
   class="footnote-ref" 
   role="doc-noteref"
   aria-describedby="fn-1-preview">
  1
</a>
<span id="fn-1-preview" class="sr-only">
  Footnote: According to Smith (2020)...
</span>

<!-- Endnotes section -->
<section class="footnotes-section" role="doc-endnotes" aria-labelledby="fn-heading">
  <h3 id="fn-heading">Notes</h3>
  
  <div class="footnote-item" 
       id="fn-1" 
       role="doc-endnote"
       data-note-id="abc123">
    <span class="footnote-number">1.</span>
    <p class="footnote-content">
      According to Smith (2020), this phenomenon was first documented in 1985.
      <a href="#fnref-1" 
         class="footnote-backref" 
         role="doc-backlink"
         aria-label="Back to reference 1">
        ↩
      </a>
    </p>
  </div>
</section>
```

### 9.2 Keyboard Navigation

- **Tab** moves between footnote markers
- **Enter/Space** jumps to corresponding endnote
- **In endnotes:** Tab to back link, Enter to return

---

## 10. Print Styles

```css
@media print {
  .footnote-ref {
    /* Disable link styling */
    color: inherit;
    text-decoration: none;
  }
  
  .footnote-tooltip {
    display: none !important;
  }
  
  .footnotes-section {
    page-break-inside: avoid;
    border-top: 1px solid #000;
    margin-top: 2em;
  }
  
  .footnote-backref {
    display: none;
  }
}
```

---

## 11. Edge Cases

| Scenario | Behavior |
|----------|----------|
| No footnotes section | Auto-create at end of post when first footnote added |
| Deleted footnote | Remove from section, renumber remaining |
| Duplicated paragraph | Each footnote gets unique ID |
| Copy/paste footnote | Creates new footnote with same content |
| Multiple articles on page | Scope numbering to each article |
| Very long footnote | Truncate in tooltip, full in section |

---

## 12. User Stories

### US-FN-01: Add Footnote While Writing
**As a** content author  
**I want to** add a footnote without leaving my writing flow  
**So that** I can capture sources and asides efficiently  

**Acceptance Criteria:**
- [ ] Keyboard shortcut (Ctrl+Shift+F)
- [ ] Popup for content entry
- [ ] Number auto-inserted
- [ ] Focus returns to text

### US-FN-02: Preview Footnote on Hover
**As a** reader  
**I want to** see footnote content without scrolling away  
**So that** I can read the note in context  

**Acceptance Criteria:**
- [ ] Tooltip appears on hover
- [ ] Shows full note content
- [ ] Positioned correctly
- [ ] Accessible via keyboard

### US-FN-03: Navigate to/from Footnotes
**As a** reader  
**I want to** click a footnote to jump to it  
**So that** I can read the full note easily  

**Acceptance Criteria:**
- [ ] Clicking marker scrolls to note
- [ ] Note is highlighted briefly
- [ ] Back link returns to marker
- [ ] Smooth scrolling animation

### US-FN-04: Automatic Renumbering
**As a** content author  
**I want** footnotes to renumber when I reorder content  
**So that** the numbering is always correct  

**Acceptance Criteria:**
- [ ] Numbers update when paragraphs reordered
- [ ] Endnotes section reorders to match
- [ ] No manual intervention needed
- [ ] Works during editing and on frontend


