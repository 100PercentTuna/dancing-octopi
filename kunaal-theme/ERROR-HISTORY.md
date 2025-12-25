# Error History Log

This document captures issues encountered during theme development and their resolutions for future reference.

---

## Issue #1: Critical Error - 500 Internal Server Error (No Debug Log)

**Date:** 2025-12-25  
**Versions affected:** v4.5.0 - v4.5.3  
**Symptom:** WordPress showed "There has been a critical error on this website" with 500 status. No `debug.log` was created despite `WP_DEBUG` being enabled.

### Root Causes Found:

#### 1a. ZIP File Structure Wrong
- **Problem:** PowerShell's `Compress-Archive -Path "kunaal-theme\*"` creates a zip with files at the ROOT level, not inside a folder
- **WordPress expects:** `theme-name/style.css` (files inside a named folder)
- **We created:** `style.css` (files at root level)
- **Resolution:** Use `-Path "kunaal-theme"` (folder, not contents) or use .NET's `ZipFile.CreateFromDirectory()` with `includeBaseDirectory: true`

#### 1b. Untracked Files Not Removed by Git Checkout
- **Problem:** `git checkout <commit> -- kunaal-theme` restores tracked file contents but does NOT remove untracked files
- **Result:** New files like `blocks/inline-formats/`, `page-about.php`, `page-contact.php` remained in the directory
- **Resolution:** Manually delete untracked files or use `git clean -fd kunaal-theme`

#### 1c. PowerShell Compress-Archive Compatibility
- **Problem:** PowerShell's `Compress-Archive` creates zips that WordPress cannot properly read
- **Resolution:** User manually zips, or use .NET method, or use external zip tool

### Lessons Learned:
- Always verify zip structure by extracting and checking before upload
- When reverting to a previous version, clean untracked files
- Test with manually-created zip first when debugging

---

## Issue #2: PDF Generator - Top-Level `use` Statements

**Date:** 2025-12-25  
**File:** `pdf-generator.php`

### Problem:
```php
// Load Composer autoloader if available
if (file_exists($autoloader)) {
    require_once $autoloader;
}

use Dompdf\Dompdf;  // ← This runs even if autoloader doesn't exist!
use Dompdf\Options;
```

The `use` statements are parsed at compile time, not runtime. While they don't cause immediate errors, they can cause issues in strict environments.

### Resolution:
Move autoloader loading inside the function and use fully qualified class names:
```php
function kunaal_generate_pdf() {
    // Load autoloader INSIDE the function
    if (file_exists($autoloader)) {
        require_once $autoloader;
    }
    
    // Use fully qualified names
    $dompdf = new \Dompdf\Dompdf($options);
}
```

---

## Issue #3: Unsafe Method Chaining with Null Coalescing

**Date:** 2025-12-25  
**File:** `functions.php`

### Problem:
```php
$post_type = get_current_screen()->post_type ?? '';
```

If `get_current_screen()` returns `null`, accessing `->post_type` throws a fatal error BEFORE the `??` operator can handle it.

### Resolution:
```php
$screen = get_current_screen();
$post_type = $screen ? $screen->post_type : '';
```

---

## Issue #4: Function Redeclaration in Block Render Templates

**Date:** 2025-12-25  
**File:** `blocks/chart/render.php`

### Problem:
```php
function kunaal_parse_data($str) { ... }
function kunaal_format_value($val, $unit, $unit_position) { ... }
```

If the chart block is used multiple times on a page, PHP throws "Cannot redeclare function" error.

### Resolution:
```php
if (!function_exists('kunaal_parse_chart_data')) {
    function kunaal_parse_chart_data($str) { ... }
}
```

---

## Issue #5: Block Unregister Without Check

**Date:** 2025-12-25  
**File:** `inc/blocks.php`

### Problem:
```php
unregister_block_type('core/pullquote');
```

This fails if the block isn't registered yet or doesn't exist.

### Resolution:
```php
if (class_exists('WP_Block_Type_Registry')) {
    $registry = WP_Block_Type_Registry::get_instance();
    if ($registry->is_registered('core/pullquote')) {
        unregister_block_type('core/pullquote');
    }
}
```

---

## Issue #6: Global $post Access Without Null Check

**Date:** 2025-12-25  
**File:** `functions.php` - `kunaal_add_open_graph_tags()`

### Problem:
```php
global $post;
$subtitle = get_post_meta($post->ID, ...);  // $post could be null
```

### Resolution:
```php
global $post;
if (!$post) {
    return;
}
```

---

## Issue #7: Theme Screenshot Appearance

**Date:** 2025-12-25  
**Symptom:** Theme preview looked "ugly" compared to others in WordPress admin

### Cause:
The `screenshot.png` was a simple placeholder (17KB, basic text-only design)

### Resolution:
Create a proper 1200x900px screenshot showing actual theme design

---

## Best Practices Established:

1. **Always verify zip structure** before telling user to upload
2. **Use `git clean -fd` or manual deletion** when reverting versions
3. **Wrap function declarations** in `function_exists()` checks in render templates
4. **Always null-check** before accessing object properties
5. **Move conditional includes** inside functions, not at file top-level
6. **Check block registration** before unregistering
7. **Test manually-zipped version first** when debugging upload issues

---

## Version History Quick Reference:

| Version | Status | Notes |
|---------|--------|-------|
| v4.3.0 | ✅ Working | Last stable before new features |
| v4.5.0 | ❌ Broken | Added inline formats, About/Contact pages |
| v4.5.1 | ❌ Broken | Attempted JS fix |
| v4.5.2 | ❌ Broken | Attempted more JS fixes |
| v4.5.3 | ❌ Broken | PHP fixes but wrong zip structure |
| v4.5.4 | ❌ Broken | Diagnostic version, still wrong zip |
| v4.6.0 | 🔄 Testing | All features restored with all fixes |


