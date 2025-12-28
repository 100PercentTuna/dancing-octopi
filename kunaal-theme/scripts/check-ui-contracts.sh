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

echo "[1/4] Checking link underline drift..."

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
echo "[2/4] Checking section rule drift..."

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

echo ""
echo "[3/4] Checking deprecated .uBlue usage in templates..."

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
echo "[4/4] Checking nth-child layout violations..."

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
    echo "See architecture.mdc and coding-standards.mdc for guidance."
    exit 1
fi

