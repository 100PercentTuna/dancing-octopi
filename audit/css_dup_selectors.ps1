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

$cssFiles = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.css | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName

$rows = New-Object System.Collections.Generic.List[object]

foreach ($f in $cssFiles) {
  $i = 0
  foreach ($line in Get-Content -Path $f.FullName) {
    $i++
    $l = $line.Trim()
    if ($l -match '^[^@].*\{' -and $l -notmatch '^\s*/\*' -and $l -notmatch '^\s*\*' -and $l -notmatch '^\s*//' -and $l -notmatch '^\s*\{') {
      $sel = ($l -replace '\s*\{.*$','').Trim()
      if ($sel.Length -gt 0 -and $sel.Length -lt 200 -and $sel -notmatch '^(to|from)$' -and $sel -notmatch '^[0-9]+%$') {
        $rows.Add([pscustomobject]@{ Selector=$sel; File=$f.FullName; Line=$i })
      }
    }
  }
}

$groups = $rows | Group-Object Selector | Where-Object { $_.Count -gt 1 } | Sort-Object Count -Descending

"DUP_SELECTOR_GROUPS=$($groups.Count)"
"TOP30_SELECTORS_WITH_FULL_OCCURRENCES:"
""

$top30 = $groups | Select-Object -First 30
foreach ($g in $top30) {
  "## Selector: $($g.Name)"
  "Occurrences: $($g.Count)"
  ($g.Group | Sort-Object File,Line | ForEach-Object { "$($_.File):$($_.Line)" })
  ""
}



