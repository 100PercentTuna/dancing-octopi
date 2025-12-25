# Kunaal Theme - Installation Guide

## Quick Start

1. Upload the `kunaal-theme` folder to `/wp-content/themes/`
2. Activate the theme in WordPress Admin → Appearance → Themes
3. Configure settings in Appearance → Customize

The theme works immediately with all features except optimized PDF generation.

---

## Optional: Enhanced PDF Generation with DOMPDF

The theme includes a fallback browser-print PDF option, but for the best experience (proper filenames, headers, footers, page numbers), install DOMPDF:

### Option A: Using Composer (Recommended)

1. Install Composer from https://getcomposer.org
2. Open a terminal in the theme directory:
   ```bash
   cd /path/to/wp-content/themes/kunaal-theme
   composer install --no-dev
   ```

### Option B: Manual Installation

1. Download DOMPDF from https://github.com/dompdf/dompdf/releases
2. Extract the contents
3. Create folder structure:
   ```
   kunaal-theme/
   └── vendor/
       └── autoload.php (create this file)
       └── dompdf/
           └── dompdf/
               └── (extracted dompdf files)
   ```

4. Create `vendor/autoload.php` with:
   ```php
   <?php
   require_once __DIR__ . '/dompdf/dompdf/autoload.inc.php';
   ```

---

## Theme Features

### Custom Gutenberg Blocks
- **Key Insight** - Warm callout box with customizable label
- **Pull Quote** - Blue-accented quote with citation
- **Accordion** - Expandable/collapsible content
- **Sidenote** - Marginal notes with handwritten font

### Block Categories
- Kunaal — Editorial
- Kunaal — Analysis
- Kunaal — Data
- Kunaal — Interactive
- Kunaal — Jottings

### PDF Export
Access via: `yoursite.com/?kunaal_pdf=1&post_id=123`

With DOMPDF:
- Filename: `Kunaal Wadhwa - Essay - Title.pdf`
- Header: Date | Title | Author
- Footer: URL | Page X of Y

Without DOMPDF:
- Uses browser print dialog
- Manual "Save as PDF" selection required

---

## Requirements

- WordPress 6.0+
- PHP 8.0+
- Modern browser (Chrome, Firefox, Safari, Edge)

