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

## Issue #2: New Features Causing Conflicts

**Date:** 2025-12-25  
**Features attempted:**
- Inline formats (blocks/inline-formats/)
- About page template (page-about.php)
- Contact page template (page-contact.php)
- PDF generator improvements

### Status:
These features have been rolled back until proper debugging can be done with PHP error logs. The base v4.3.0 theme is confirmed working.

### Next Steps:
1. Get proper PHP error logging working on the server
2. Add features one at a time and test
3. Ensure each feature works before adding the next

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
| v4.3.0 | ✅ Working | Baseline stable version |
| v4.5.0 - v4.6.0 | ❌ Broken | Too many new features + zip issues |
| v4.6.1 | ✅ Testing | Clean slate (v4.3.0 code, new version number) |
