$ErrorActionPreference = 'Stop'

$repo = Split-Path -Parent $PSScriptRoot
Set-Location $repo

New-Item -ItemType Directory -Force -Path 'audit\out' | Out-Null

$scripts = @(
  'file_inventory',
  'scan_counts',
  'php_lint',
  'coverage_table',
  'css_dup_selectors',
  'window_globals',
  'external_libs'
)

foreach ($s in $scripts) {
  $outPath = Join-Path 'audit\out' ($s + '.txt')
  Write-Host ('RUNNING ' + $s)
  & powershell -NoProfile -ExecutionPolicy Bypass -File (Join-Path 'audit' ($s + '.ps1')) | Out-File -Encoding utf8 $outPath
}


