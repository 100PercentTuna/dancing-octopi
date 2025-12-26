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

$jsFiles = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.js | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName

$occ = New-Object System.Collections.Generic.List[object]

foreach ($f in $jsFiles) {
  $i = 0
  foreach ($line in Get-Content -Path $f.FullName) {
    $i++
    $matches = [regex]::Matches($line, 'window\.[A-Za-z0-9_]+')
    foreach ($m in $matches) {
      $occ.Add([pscustomobject]@{ Name=$m.Value; File=$f.FullName; Line=$i; Text=($line.Trim()) })
    }
  }
}

$groups = $occ | Group-Object Name | Sort-Object Count -Descending
"WINDOW_DOT_TOTAL=$($occ.Count)"
"WINDOW_DOT_UNIQUE=$($groups.Count)"
""

"TOP30_WINDOW_GLOBALS_WITH_DEFINE_AND_CONSUME_SITES:"
""

$top30 = $groups | Select-Object -First 30
foreach ($g in $top30) {
  $name = $g.Name
  $items = $g.Group | Sort-Object File,Line
  $defs = $items | Where-Object { $_.Text -match ([regex]::Escape($name) + '\s*=') -or $_.Text -match ([regex]::Escape($name) + '\s*=\s*new') -or $_.Text -match ([regex]::Escape($name) + '\s*=\s*\{') }
  $cons = $items | Where-Object { $_.Text -notmatch ([regex]::Escape($name) + '\s*=') }

  "## Global: $name"
  "Total occurrences: $($g.Count)"
  "Define sites:"
  if ($defs.Count -eq 0) { "(none detected by heuristic)" } else { $defs | ForEach-Object { "$($_.File):$($_.Line):$($_.Text)" } }
  ""
  "Consume sites:"
  if ($cons.Count -eq 0) { "(none)" } else { $cons | ForEach-Object { "$($_.File):$($_.Line):$($_.Text)" } }
  ""
}



