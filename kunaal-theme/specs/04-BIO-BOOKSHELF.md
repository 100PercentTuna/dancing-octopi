# 04 - BIO & BOOKSHELF SECTION
## Complete Specification

---

## OVERVIEW

The bio section introduces the person with:
- Elegant drop cap opening
- Readable, well-spaced paragraphs
- Optional pull quote
- "Currently Reading" bookshelf

---

## BIO SECTION

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   01                                         ← Mono, 11px, muted2           │
│   ──                                         ← Brown underline, 24px wide   │
│   ABOUT                                      ← Mono, 11px, uppercase        │
│                                                                             │
│   T  he bio text begins here with a beautiful drop cap. The first letter   │
│      is large (4.5em) and floats left, creating an elegant magazine-style  │
│   opening. The text wraps around it naturally.                              │
│                                                                             │
│   Second paragraph continues with normal text. Line height is generous     │
│   (1.75) for comfortable reading. The max-width is 620px to maintain       │
│   optimal line length.                                                      │
│                                                                             │
│   │ "A pull quote can appear here, indented with a brown accent border    │
│   │  on the left side. It breaks up the text and highlights key thoughts." │
│   │                                                        — attribution   │
│                                                                             │
│   Third paragraph continues after the quote...                              │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### CSS Implementation

```css
.about-bio {
  max-width: var(--prose); /* 620px */
  margin: 0 auto;
  padding: var(--space-15) var(--space-4);
}

.about-bio-label {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted2);
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  margin-bottom: var(--space-8);
}

.about-bio-label::before {
  content: '01';
  display: block;
}

.about-bio-label::after {
  content: '';
  width: 24px;
  height: 2px;
  background: var(--warm);
  margin: var(--space-1) 0;
}

/* Drop Cap */
.about-bio-text p:first-of-type::first-letter {
  float: left;
  font-family: var(--serif);
  font-size: 4.5em;
  line-height: 0.8;
  padding-right: var(--space-2);
  padding-top: 0.1em;
  color: var(--ink);
}

.about-bio-text p {
  font-family: var(--serif);
  font-size: 19px;
  line-height: 1.75;
  color: var(--ink);
  margin-bottom: var(--space-4);
}

/* Pull Quote */
.about-pullquote {
  border-left: 3px solid var(--warm);
  padding-left: var(--space-4);
  margin: var(--space-6) 0;
  font-family: var(--serif);
  font-size: 20px;
  font-style: italic;
  line-height: 1.6;
  color: var(--ink);
}

.about-pullquote-attr {
  display: block;
  font-family: var(--mono);
  font-size: 11px;
  font-style: normal;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--muted2);
  margin-top: var(--space-2);
}
```

---

## BOOKSHELF SECTION

### Visual Layout

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                                                                             │
│   CURRENTLY READING                          ← Mono, 11px, uppercase        │
│                                                                             │
│   ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐                          │
│   │     │ │     │ │     │ │     │ │     │ │     │ ← Book covers             │
│   │  B  │ │  B  │ │  B  │ │  B  │ │  B  │ │  B  │   standing upright        │
│   │  O  │ │  O  │ │  O  │ │  O  │ │  O  │ │  O  │                          │
│   │  O  │ │  O  │ │  O  │ │  O  │ │  O  │ │  O  │                          │
│   │  K  │ │  K  │ │  K  │ │  K  │ │  K  │ │  K  │                          │
│   │     │ │     │ │     │ │     │ │     │ │     │                          │
│   └──┬──┘ └──┬──┘ └──┬──┘ └──┬──┘ └──┬──┘ └──┬──┘                          │
│   ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ ← Shelf surface        │
│   ═══════════════════════════════════════════════════ ← Shelf edge          │
│                                                                             │
│   On hover: Book lifts, tilts, shows tooltip with title/author             │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### CSS Implementation

```css
.about-bookshelf {
  max-width: var(--wide);
  margin: var(--space-10) auto;
  padding: 0 var(--space-4);
}

.about-bookshelf-label {
  font-family: var(--mono);
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--muted2);
  margin-bottom: var(--space-4);
  text-align: center;
}

.bookshelf {
  display: flex;
  justify-content: center;
  align-items: flex-end;
  gap: var(--space-2);
  position: relative;
  padding-bottom: var(--space-4);
}

/* Shelf surface and edge */
.bookshelf::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: calc(100% + 60px);
  max-width: 700px;
  height: 16px;
  background: linear-gradient(
    to bottom,
    var(--warmDark) 0%,
    var(--warm) 30%,
    var(--warmLight) 100%
  );
  border-radius: 0 0 4px 4px;
  box-shadow: 
    0 4px 12px rgba(0,0,0,0.15),
    inset 0 2px 4px rgba(255,255,255,0.1);
}

/* Individual book */
.book {
  width: clamp(50px, 8vw, 70px);
  height: clamp(80px, 12vw, 110px);
  position: relative;
  transform-origin: bottom center;
  transition: 
    transform 300ms cubic-bezier(0.34, 1.56, 0.64, 1),
    box-shadow 300ms ease;
  cursor: pointer;
  z-index: 10;
}

.book:hover {
  transform: translateY(-15px) rotateX(-5deg);
  z-index: 20;
}

.book img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 2px 4px 4px 2px;
  box-shadow: 
    2px 0 4px rgba(0,0,0,0.2),
    4px 4px 12px rgba(0,0,0,0.15);
}

.book:hover img {
  box-shadow: 
    4px 0 8px rgba(0,0,0,0.25),
    6px 8px 20px rgba(0,0,0,0.2);
}

/* Spine effect */
.book::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(
    to right,
    rgba(0,0,0,0.2),
    transparent
  );
  border-radius: 2px 0 0 2px;
  pointer-events: none;
}

/* Book tooltip */
.book-tooltip {
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%) translateY(-8px);
  background: white;
  border: 1px solid var(--hair);
  border-radius: 6px;
  padding: var(--space-2) var(--space-3);
  box-shadow: 0 8px 24px rgba(0,0,0,0.12);
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: opacity 200ms ease, transform 200ms ease;
  z-index: 30;
}

.book:hover .book-tooltip {
  opacity: 1;
  transform: translateX(-50%) translateY(-12px);
}

.book-tooltip-title {
  font-family: var(--serif);
  font-size: 14px;
  font-weight: 500;
  color: var(--ink);
}

.book-tooltip-author {
  font-family: var(--mono);
  font-size: 11px;
  color: var(--muted2);
  margin-top: 2px;
}
```

---

## USER STORIES

### US-BIO-001: Drop Cap Display
As a visitor, I want an elegant opening paragraph.
- [ ] First letter: 4.5em, float left
- [ ] Serif font
- [ ] Proper line wrapping

### US-BIO-002: Bio Text Styling
As a visitor, I want comfortable reading.
- [ ] Serif, 19px
- [ ] Line-height: 1.75
- [ ] Max-width: 620px

### US-BIO-003: Pull Quote
As a visitor, I want highlighted quotes.
- [ ] Left border: 3px brown
- [ ] Indented (padding-left: 32px)
- [ ] Italic text
- [ ] Attribution in mono

### US-BIO-004: Section Label
As a visitor, I want clear section identification.
- [ ] Mono, 11px, uppercase
- [ ] "01 ABOUT" format
- [ ] Brown underline accent

### US-BOOK-001: Bookshelf Display
As a visitor, I want to see current reads.
- [ ] 6-8 books maximum
- [ ] Shelf surface visible
- [ ] Books standing upright

### US-BOOK-002: Book Hover Tilt
As a visitor, I want interactive books.
- [ ] Lift: translateY(-15px)
- [ ] Tilt: rotateX(-5deg)
- [ ] Shadow intensifies

### US-BOOK-003: Book Tooltip
As a visitor, I want book details on hover.
- [ ] Title and author visible
- [ ] Appears above book
- [ ] 200ms fade animation

### US-BOOK-004: Shelf Texture
As a visitor, I want realistic shelf.
- [ ] Brown gradient
- [ ] Subtle shadow
- [ ] Wood-like appearance

### US-BOOK-005: Book Cover Display
As a visitor, I want book covers visible.
- [ ] Image from admin
- [ ] object-fit: cover
- [ ] Spine edge effect (left shadow)

---

## CUSTOMIZER FIELDS

```php
// Bio Section
$wp_customize->add_section('kunaal_about_bio', array(
    'title' => 'About Page: Bio',
    'priority' => 20,
));

// Toggle
$wp_customize->add_setting('kunaal_about_bio_show', array(
    'default' => true,
    'sanitize_callback' => 'wp_validate_boolean',
));

// Bio text comes from the page content (editor)

// Pull Quote toggle
$wp_customize->add_setting('kunaal_about_pullquote_show', array(
    'default' => false,
));

// Pull Quote text
$wp_customize->add_setting('kunaal_about_pullquote_text', array(
    'sanitize_callback' => 'sanitize_textarea_field',
));

// Pull Quote attribution
$wp_customize->add_setting('kunaal_about_pullquote_attr', array(
    'sanitize_callback' => 'sanitize_text_field',
));

// Bookshelf Section
$wp_customize->add_section('kunaal_about_books', array(
    'title' => 'About Page: Bookshelf',
    'priority' => 30,
));

// Toggle
$wp_customize->add_setting('kunaal_about_books_show', array(
    'default' => true,
));

// Books (up to 8)
for ($i = 1; $i <= 8; $i++) {
    // Book cover image
    $wp_customize->add_setting("kunaal_book_{$i}_cover", array(
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize,
        "kunaal_book_{$i}_cover", array(
            'label' => "Book {$i}: Cover Image",
            'section' => 'kunaal_about_books',
            'mime_type' => 'image',
        )
    ));
    
    // Book title
    $wp_customize->add_setting("kunaal_book_{$i}_title", array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control("kunaal_book_{$i}_title", array(
        'label' => "Book {$i}: Title",
        'section' => 'kunaal_about_books',
        'type' => 'text',
    ));
    
    // Book author
    $wp_customize->add_setting("kunaal_book_{$i}_author", array(
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control("kunaal_book_{$i}_author", array(
        'label' => "Book {$i}: Author",
        'section' => 'kunaal_about_books',
        'type' => 'text',
    ));
    
    // Book link (optional)
    $wp_customize->add_setting("kunaal_book_{$i}_url", array(
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control("kunaal_book_{$i}_url", array(
        'label' => "Book {$i}: Link (optional)",
        'section' => 'kunaal_about_books',
        'type' => 'url',
    ));
}
```

---

## EDGE CASES

### E-BIO-001: Empty Bio
- Hide section or show placeholder
- Admin notification in Customizer

### E-BIO-002: Very Short Bio
- Still display with drop cap
- No minimum length required

### E-BIO-003: Very Long Bio
- Multiple paragraphs supported
- No maximum length
- Natural flow

### E-BOOK-001: No Books
- Hide bookshelf section entirely
- Smooth flow to next section

### E-BOOK-002: Single Book
- Center single book
- Shelf still visible
- Reduce shelf width

### E-BOOK-003: Book Image Missing
- Show placeholder gradient
- Title still appears in tooltip

---

## FINAL CHECKLIST

### Bio Section
- [ ] Section label: "01" + line + "ABOUT"
- [ ] Drop cap on first paragraph
- [ ] Serif font, 19px, line-height 1.75
- [ ] Max-width 620px
- [ ] Optional pull quote with brown border
- [ ] Pull quote attribution in mono

### Bookshelf
- [ ] Centered shelf with books
- [ ] Shelf surface with gradient
- [ ] Books have spine shadow
- [ ] Hover: lift + tilt
- [ ] Tooltip on hover
- [ ] Responsive (fewer books on mobile)
- [ ] Admin: 8 book slots with cover/title/author

