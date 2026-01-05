#!/bin/bash
#
# UI Contracts Drift Check Script
# 
# Ensures no competing implementations of motifs exist outside canonical files.
# Run this before every commit and in CI to prevent drift.
#
# Exit codes:
#   0 = All checks passed
#   1 = Drift detected
#

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(dirname "$SCRIPT_DIR")"
FAIL=0

echo "==================================="
echo "UI Contracts Drift Check"
echo "==================================="
echo ""

# -----------------------------------------------------------------------------
# CHECK 0: theme.json must not force link underline
# 
# theme.json must not set elements.link.typography.textDecoration to "underline"
# Default state must be "none" (no underline at rest)
# -----------------------------------------------------------------------------

echo "[0/5] Checking theme.json for link underline..."

if [ -f "$THEME_DIR/theme.json" ]; then
    # Check if theme.json forces link underline
    THEME_UNDERLINE=$(grep -A5 '"link"' "$THEME_DIR/theme.json" | grep -i "textDecoration.*underline" || true)
    
    if [ -n "$THEME_UNDERLINE" ]; then
        echo "  ✗ FAIL: theme.json forces link underline (textDecoration: underline)"
        echo "    Remove elements.link.typography.textDecoration: underline from theme.json"
        echo "    Default state must be none (no underline at rest)"
        FAIL=1
    else
        echo "  ✓ theme.json does not force link underline"
    fi
else
    echo "  ⚠ WARNING: theme.json not found"
fi

# -----------------------------------------------------------------------------
# CHECK 1: Link underlines must only exist in canonical files
# 
# CANONICAL FILES (allowed):
#   - assets/css/utilities.css (main link underline implementation)
#   - assets/css/compatibility.css (print styles exception)
#
# FORBIDDEN patterns outside canonical files:
#   - text-decoration: underline (except in print media query)
#   - text-decoration-thickness
#   - underline-offset
# -----------------------------------------------------------------------------

echo ""
echo "[1/5] Checking link underline drift..."

# Find text-decoration: underline outside canonical files
# Exclude utilities.css, compatibility.css, and print.css
UNDERLINE_DRIFT=$(grep -rn "text-decoration:\s*underline" "$THEME_DIR/assets/css" \
    --include="*.css" \
    | grep -v "utilities.css" \
    | grep -v "compatibility.css" \
    | grep -v "print.css" \
    | grep -v "EXCEPTION" \
    || true)

if [ -n "$UNDERLINE_DRIFT" ]; then
    echo "  ✗ FAIL: text-decoration: underline found outside canonical files:"
    echo "$UNDERLINE_DRIFT" | while read -r line; do
        echo "    $line"
    done
    FAIL=1
else
    echo "  ✓ No text-decoration: underline drift detected"
fi

# -----------------------------------------------------------------------------
# CHECK 2: Section rules must only exist in canonical files
#
# CANONICAL FILES (allowed):
#   - assets/css/sections.css (.sectionHead border-bottom)
#   - assets/css/utilities.css (.u-section-underline::after)
#
# FORBIDDEN patterns outside canonical files:
#   - .sectionHead.*border-bottom (drawing gray line)
#   - ::after with blue background on section headings
# -----------------------------------------------------------------------------

echo ""
echo "[2/5] Checking section rule drift..."

# Check for border-bottom on .sectionHead outside sections.css
SECTION_DRIFT=$(grep -rn "\.sectionHead.*border-bottom\|sectionHead.*border-bottom" "$THEME_DIR/assets/css" \
    --include="*.css" \
    | grep -v "sections.css" \
    || true)

if [ -n "$SECTION_DRIFT" ]; then
    echo "  ✗ FAIL: .sectionHead border-bottom found outside sections.css:"
    echo "$SECTION_DRIFT" | while read -r line; do
        echo "    $line"
    done
    FAIL=1
else
    echo "  ✓ No section rule drift detected"
fi

# -----------------------------------------------------------------------------
# CHECK 3: Deprecated .uBlue class should not be used in templates
#
# .uBlue is deprecated - use .u-underline-double or canonical :where() selectors
# -----------------------------------------------------------------------------

# -----------------------------------------------------------------------------
# CHECK 3: Filter JS must not bind to IDs for primary contracts
#
# Filter module JS must use data-* hooks, not getElementById/querySelector('#id')
# IDs are acceptable only for:
#   - anchor targets
#   - ARIA relationships
#   - unique form inputs
# -----------------------------------------------------------------------------

echo ""
echo "[3/5] Checking filter JS for ID-based contracts..."

if [ -f "$THEME_DIR/assets/js/main.js" ]; then
    # Check for filter-related ID usage in main.js
    # Look for getElementById or querySelector with # for filter elements
    FILTER_ID_USAGE=$(grep -n "getElementById\|querySelector.*'#" "$THEME_DIR/assets/js/main.js" \
        | grep -E "(filter|topic|sort|search|reset)" \
        | grep -v "EXCEPTION" \
        || true)
    
    if [ -n "$FILTER_ID_USAGE" ]; then
        echo "  ✗ FAIL: Filter JS binds to IDs instead of data-* hooks:"
        echo "$FILTER_ID_USAGE" | while read -r line; do
            echo "    $line"
        done
        echo "    Refactor to use data-ui='filter' and data-action/data-role hooks"
        FAIL=1
    else
        echo "  ✓ Filter JS does not use IDs for primary contracts"
    fi
else
    echo "  ⚠ WARNING: main.js not found"
fi

echo ""
echo "[4/5] Checking deprecated .uBlue usage in templates..."

UBLUE_TEMPLATES=$(grep -rn "class=.*uBlue\|class=\"uBlue\|class='uBlue" "$THEME_DIR" \
    --include="*.php" \
    || true)

if [ -n "$UBLUE_TEMPLATES" ]; then
    echo "  ✗ FAIL: Deprecated .uBlue class found in templates:"
    echo "$UBLUE_TEMPLATES" | while read -r line; do
        echo "    $line"
    done
    FAIL=1
else
    echo "  ✓ No deprecated .uBlue usage in templates"
fi

# -----------------------------------------------------------------------------
# CHECK 4: nth-child() should not be used for layout positioning
#
# Acceptable uses:
#   - Table zebra striping (tr:nth-child)
#   - Background color alternation (.about-section:nth-child)
#
# Forbidden uses:
#   - Grid positioning
#   - Hero photo positioning
#   - Card positioning
# -----------------------------------------------------------------------------

echo ""
echo "[5/5] Checking nth-child layout violations..."

# Look for nth-child with layout properties (grid-column, grid-row, position, transform)
NTH_LAYOUT=$(grep -rn "nth-child" "$THEME_DIR/assets/css" \
    --include="*.css" \
    -A2 \
    | grep -E "grid-column|grid-row|transform:|position:" \
    | grep -v "/* EXCEPTION" \
    || true)

if [ -n "$NTH_LAYOUT" ]; then
    echo "  ⚠ WARNING: nth-child may be used for layout positioning:"
    echo "$NTH_LAYOUT" | while read -r line; do
        echo "    $line"
    done
    echo "  (Review manually - may be acceptable for minor styling)"
else
    echo "  ✓ No nth-child layout violations detected"
fi

# -----------------------------------------------------------------------------
# CHECK 6: Hardcoded colors (should use tokens)
#
# ALLOWED FILES:
#   - tokens.css, variables.css (token definitions)
#   - dark-mode.css (dark mode token overrides)
#   - print.css, pdf-ebook.css (print-specific colors)
#
# FORBIDDEN:
#   - #000, #111, #1E5AFF, color: black in regular CSS
# -----------------------------------------------------------------------------

echo ""
echo "[6/6] Checking for hardcoded colors..."

HARDCODED_COLORS=$(grep -rn "#000[^0]\|#111\|#1E5AFF\|color:\s*black" "$THEME_DIR/assets/css" \
    --include="*.css" \
    | grep -v "tokens.css" \
    | grep -v "variables.css" \
    | grep -v "dark-mode.css" \
    | grep -v "print.css" \
    | grep -v "pdf-ebook.css" \
    | grep -v "/* EXCEPTION" \
    || true)

if [ -n "$HARDCODED_COLORS" ]; then
    echo "  ⚠ WARNING: Hardcoded colors found (should use tokens):"
    echo "$HARDCODED_COLORS" | while read -r line; do
        echo "    $line"
    done
    echo "  (Review manually - replace with var(--ink), var(--bg), var(--blue), etc.)"
else
    echo "  ✓ No hardcoded colors outside token files"
fi

# -----------------------------------------------------------------------------
# CHECK 7: Dark-mode accent drift (no hardcoded orange outside tokens)
#
# The RCA root cause: hardcoded orange used as dark-mode accent.
# Warm may be orange in tokens, but accent/underline must not be hardcoded orange
# outside the canonical token owner.
# -----------------------------------------------------------------------------

echo ""
echo "[7/7] Checking for dark-mode accent drift (#E8A87C) outside tokens.css..."

ORANGE_DRIFT=$(grep -rn "#E8A87C" "$THEME_DIR/assets/css" \
    --include="*.css" \
    | grep -v "tokens.css" \
    | grep -v "print.css" \
    | grep -v "pdf-ebook.css" \
    | grep -v "compatibility.css" \
    | grep -v "/* EXCEPTION" \
    || true)

if [ -n "$ORANGE_DRIFT" ]; then
    echo "  ✗ FAIL: Hardcoded dark-mode orange (#E8A87C) found outside tokens.css:"
    echo "$ORANGE_DRIFT" | while read -r line; do
        echo "    $line"
    done
    echo "    Replace with semantic tokens (e.g., var(--k-color-accent) or var(--k-color-warm))"
    FAIL=1
else
    echo "  ✓ No #E8A87C drift outside tokens.css"
fi

echo ""
echo "[7b/7] Checking tokens.css keeps dark-mode accent/underline within tokens..."

ACCENT_ORANGE=$(grep -n "^[[:space:]]*--k-color-accent:[[:space:]]*#E8A87C" "$THEME_DIR/assets/css/tokens.css" || true)
UNDERLINE_ORANGE=$(grep -n "^[[:space:]]*--k-underline-blue-color:[[:space:]]*#E8A87C" "$THEME_DIR/assets/css/tokens.css" || true)

if [ -n "$ACCENT_ORANGE" ] || [ -n "$UNDERLINE_ORANGE" ]; then
    echo "  ✓ tokens.css defines orange for accent/underline (allowed; canonical owner)"
else
    echo "  ✓ tokens.css does not hardcode orange for accent/underline (also acceptable)"
fi

# -----------------------------------------------------------------------------
# SUMMARY
# -----------------------------------------------------------------------------

echo ""
echo "==================================="
if [ $FAIL -eq 0 ]; then
    echo "✓ All UI contract checks PASSED"
    echo "==================================="
    exit 0
else
    echo "✗ UI contract checks FAILED"
    echo "==================================="
    echo ""
    echo "Fix the issues above before committing."
    echo "See kunaal-theme/UI_CONTRACTS.md for guidance."
    exit 1
fi

