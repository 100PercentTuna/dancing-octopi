# Error History Log

This document captures issues encountered during theme development and their resolutions for future reference.

---

## Issue #1: ZIP File Structure Wrong (ROOT CAUSE)

**Date:** 2025-12-25  
**Versions affected:** v4.5.0 - v4.6.0  
**Symptom:** WordPress showed "There has been a critical error on this website" with 500 status. No `debug.log` was created despite `WP_DEBUG` being enabled.

### Root Cause:
PowerShell's `Compress-Archive` creates zips with incompatible structure/format that WordPress cannot properly extract.

### Resolution:
User manually creates zip via Windows Explorer (right-click → "Compress to ZIP file"), which creates a proper zip that WordPress accepts.

### Lessons Learned:
1. Always have user manually zip the theme folder
2. Never trust programmatic zip creation for WordPress themes
3. Test with GitHub-downloaded zip if issues arise

---

## Issue #2: get_current_screen() Fatal Error

**Date:** 2025-12-25  
**Versions affected:** v4.3.0+  
**File:** `functions.php` line 207

### Root Cause:
```php
$post_type = get_current_screen()->post_type ?? '';
```
This causes a fatal error when `get_current_screen()` returns `null` (which happens in many contexts like AJAX requests, REST API, cron, etc.). The null coalescing operator `??` only handles null values from the property, not null from the function call itself.

### Resolution (v4.7.0):
```php
$screen = get_current_screen();
$post_type = ($screen && isset($screen->post_type)) ? $screen->post_type : '';
```

---

## Issue #3: Dompdf use Statement Fatal Error

**Date:** 2025-12-25  
**Versions affected:** v4.3.0+  
**File:** `pdf-generator.php` lines 19-20

### Root Cause:
```php
use Dompdf\Dompdf;
use Dompdf\Options;
```
These `use` statements at file level cause fatal errors if Composer's autoloader isn't loaded (i.e., if `/vendor/autoload.php` doesn't exist).

### Resolution (v4.7.0):
- Removed top-level `use` statements
- Moved autoloader loading inside the function
- Use fully qualified class names: `\Dompdf\Dompdf` and `\Dompdf\Options`

---

## Issue #4: unregister_block_type Fatal Error

**Date:** 2025-12-25  
**Versions affected:** v4.3.0+  
**File:** `inc/blocks.php` line 54

### Root Cause:
```php
unregister_block_type('core/pullquote');
```
Calling this on a block that isn't registered causes a fatal error.

### Resolution (v4.7.0):
```php
if (class_exists('WP_Block_Type_Registry')) {
    $registry = WP_Block_Type_Registry::get_instance();
    if ($registry->is_registered('core/pullquote')) {
        unregister_block_type('core/pullquote');
    }
}
```

---

## Best Practices Established:

1. **Manual ZIP creation only** - Never use PowerShell/automated zip
2. **Incremental feature addition** - Add one feature, test, then add next
3. **Always have a known-working baseline** - v4.3.0 is confirmed stable
4. **Document everything** - Keep this log updated

---

## Version History:

| Version | Status | Notes |
|---------|--------|-------|
| v4.3.0 | ⚠️ Had bugs | Hidden fatal error bugs in functions.php, pdf-generator.php, blocks.php |
| v4.5.0 - v4.6.0 | ❌ Broken | Too many new features + zip issues |
| v4.6.1 | ❌ Broken | Had same bugs as v4.3.0 (just version number change) |
| v4.7.0 | ✅ Testing | Fixed 3 fatal error bugs found via code review |
