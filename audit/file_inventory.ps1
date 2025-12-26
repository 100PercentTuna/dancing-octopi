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

$php = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.php | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName
$js  = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.js  | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName
$css = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.css | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName

$blocks = Get-ChildItem -Directory -Path 'kunaal-theme/blocks' | Sort-Object Name
$blocksWithJson = $blocks | Where-Object { Test-Path (Join-Path $_.FullName 'block.json') }
$blocksWithoutJson = $blocks | Where-Object { -not (Test-Path (Join-Path $_.FullName 'block.json')) }

"FILES_PHP_EXCL_SPECS=$($php.Count)"
"FILES_JS_EXCL_SPECS=$($js.Count)"
"FILES_CSS_EXCL_SPECS=$($css.Count)"
"BLOCK_DIRS=$($blocks.Count)"
"BLOCK_DIRS_WITH_BLOCKJSON=$($blocksWithJson.Count)"
"BLOCK_DIRS_WITHOUT_BLOCKJSON=$($blocksWithoutJson.Count)"

"--- PHP_FILES ---"
$php | ForEach-Object { $_.FullName }
"--- JS_FILES ---"
$js | ForEach-Object { $_.FullName }
"--- CSS_FILES ---"
$css | ForEach-Object { $_.FullName }
"--- BLOCK_DIRS ---"
$blocks | ForEach-Object { $_.FullName }
"--- BLOCK_DIRS_WITHOUT_BLOCKJSON ---"
$blocksWithoutJson | ForEach-Object { $_.FullName }



