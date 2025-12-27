# GoDaddy WordPress Directory Cleanup Instructions

## Problem
The WordPress directory on GoDaddy has nested `kunaal-theme` folders (e.g., `kunaal-theme/kunaal-theme/`) and other clutter from previous deployments.

## Solution: Clean Re-deployment

### Option 1: Complete Clean Re-deployment (Recommended)

1. **Backup First** (CRITICAL):
   - Go to GoDaddy cPanel → File Manager
   - Navigate to `wp-content/themes/`
   - Download/backup the entire `kunaal-theme` folder to your computer
   - Also backup your WordPress database (via phpMyAdmin or cPanel backup)

2. **Delete Old Theme Folder**:
   - In File Manager, navigate to `wp-content/themes/`
   - Delete the entire `kunaal-theme` folder (this removes all nested folders and clutter)

3. **Upload Fresh Theme**:
   - Download the latest version from GitHub: https://github.com/100PercentTuna/dancing-octopi
   - Extract the ZIP file
   - Upload ONLY the `kunaal-theme` folder (not the entire repo) to `wp-content/themes/`
   - Ensure the structure is: `wp-content/themes/kunaal-theme/` (not nested)

4. **Verify Structure**:
   - The correct structure should be:
     ```
     wp-content/themes/kunaal-theme/
       ├── style.css
       ├── functions.php
       ├── header.php
       ├── footer.php
       ├── assets/
       ├── inc/
       └── ... (other theme files)
     ```
   - There should NOT be: `kunaal-theme/kunaal-theme/`

5. **Activate Theme**:
   - Go to WordPress Admin → Appearance → Themes
   - The theme should appear (if not, check file permissions)
   - Activate it

### Option 2: Manual Cleanup (If you want to keep existing)

1. **Navigate to Theme Folder**:
   - GoDaddy cPanel → File Manager
   - Go to `wp-content/themes/kunaal-theme/`

2. **Check for Nested Folders**:
   - Look for `kunaal-theme/kunaal-theme/` inside
   - Look for any duplicate or unused files

3. **Remove Nested Folders**:
   - Delete any `kunaal-theme` folder inside the main `kunaal-theme` folder
   - Delete any `.git` folders (WordPress doesn't need Git)
   - Delete any `reference-files` or other non-theme folders

4. **Verify Clean Structure**:
   - Ensure only theme files remain
   - Check that `style.css` and `functions.php` are in the root of `kunaal-theme/`

## File Permissions

After upload, ensure these permissions:
- Folders: 755
- Files: 644
- `wp-config.php`: 600 (if you edit it)

## After Cleanup

1. Clear WordPress cache (if using a caching plugin)
2. Clear browser cache
3. Test the site to ensure everything works
4. Check Customizer to ensure all settings are intact (they should be, as they're stored in the database)

## Notes

- Theme settings are stored in the WordPress database, not in files, so deleting and re-uploading the theme folder won't lose your Customizer settings
- Always backup before making changes
- If something breaks, restore from backup

