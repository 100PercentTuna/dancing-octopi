Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Get-ThemeVersion {
    param(
        [Parameter(Mandatory = $true)]
        [string]$StyleCssPath
    )
    if (!(Test-Path -LiteralPath $StyleCssPath)) {
        return 'unknown'
    }
    $content = Get-Content -LiteralPath $StyleCssPath -Raw
    $m = [regex]::Match($content, '(?m)^\s*Version:\s*(?<v>[0-9]+(\.[0-9]+){1,3})\s*$')
    if ($m.Success) { return $m.Groups['v'].Value }
    return 'unknown'
}

$repoRoot = Resolve-Path (Join-Path $PSScriptRoot '..') | Select-Object -ExpandProperty Path
$themeDir = Join-Path $repoRoot 'kunaal-theme'
$styleCss = Join-Path $themeDir 'style.css'

if (!(Test-Path -LiteralPath $themeDir)) {
    throw "Theme folder not found at: $themeDir"
}

$version = Get-ThemeVersion -StyleCssPath $styleCss
$outDir = Join-Path $repoRoot 'dist'
New-Item -ItemType Directory -Force -Path $outDir | Out-Null

$zipName = if ($version -ne 'unknown') { "kunaal-theme-$version.zip" } else { "kunaal-theme.zip" }
$zipPath = Join-Path $outDir $zipName

# Prefer Windows tar.exe (bsdtar) because it consistently produces a WP-friendly ZIP layout.
$tarPath = $null
$tarCmd = Get-Command tar -ErrorAction SilentlyContinue
if ($tarCmd) {
    $tarPath = $tarCmd.Source
} else {
    $fallbackTar = Join-Path $env:SystemRoot 'System32\tar.exe'
    if (Test-Path -LiteralPath $fallbackTar) {
        $tarPath = $fallbackTar
    }
}

if ($tarPath) {
    if (Test-Path -LiteralPath $zipPath) { Remove-Item -LiteralPath $zipPath -Force }

    # Creates a ZIP with `kunaal-theme/` as the top-level folder.
    & $tarPath -a -cf $zipPath -C $repoRoot 'kunaal-theme'
    Write-Host "Created: $zipPath"
    exit 0
}

Write-Warning "Could not find tar.exe. Please create a ZIP manually:"
Write-Host "1) In File Explorer, right-click the folder: kunaal-theme"
Write-Host "2) Click: Compress to ZIP file"
Write-Host "3) Upload that ZIP in WordPress: Appearance > Themes > Add New > Upload Theme"
exit 1


