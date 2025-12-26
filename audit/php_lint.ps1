$ErrorActionPreference = 'Stop'

$repo = Split-Path -Parent $PSScriptRoot
Set-Location $repo

function IsExcludedPath([string]$full) {
  return ($full -match '\\\\kunaal-theme\\\\specs\\\\') -or
         ($full -match '\\\\node_modules\\\\') -or
         ($full -match '\\\\vendor\\\\') -or
         ($full -match '\\\\build\\\\') -or
         ($full -match '\\\\dist\\\\') -or
         ($full -match '\\\\\.git\\\\')
}

$files = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.php | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName
$bad = @()

foreach ($f in $files) {
  $out = & php -l $f.FullName 2>&1
  if ($out -notmatch 'No syntax errors detected') {
    $bad += [pscustomobject]@{ File = $f.FullName; Output = ($out | Out-String) }
  }
}

"PHP_LINT_TOTAL_FILES=$($files.Count)"
"PHP_LINT_BAD_FILES=$($bad.Count)"
if ($bad.Count -gt 0) {
  "BAD_FILES_TOP10:"
  $bad | Select-Object -First 10 | ForEach-Object {
    "FILE=$($_.File)"
    "OUTPUT=$($_.Output.Trim())"
    ""
  }
}



