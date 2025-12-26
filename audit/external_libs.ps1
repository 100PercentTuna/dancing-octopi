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

function CollectUrls($files) {
  $hits = @()
  foreach ($f in $files) {
    $m = Select-String -Path $f.FullName -Pattern 'https?://[^\s"''\)]+'
    if ($m) { $hits += $m }
  }
  return $hits
}

$php = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.php | Where-Object { -not (IsExcludedPath $_.FullName) }
$js  = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.js  | Where-Object { -not (IsExcludedPath $_.FullName) }

$urlHits = (CollectUrls $php) + (CollectUrls $js)

"EXTERNAL_URL_HITS_TOTAL=$($urlHits.Count)"
"TOP20_URL_HITS:"
$urlHits | Select-Object -First 20 | ForEach-Object { "$($_.Path):$($_.LineNumber):$($_.Line.Trim())" }
""

$libs = @(
  @{ Name='GoogleFonts'; Pattern='fonts\.googleapis\.com' },
  @{ Name='Leaflet'; Pattern='unpkg\.com/leaflet' },
  @{ Name='D3'; Pattern='d3js\.org/d3' },
  @{ Name='GSAP'; Pattern='cdn\.jsdelivr\.net/npm/gsap' },
  @{ Name='CartoTiles'; Pattern='basemaps\.cartocdn\.com' },
  @{ Name='GitHubRawGeoJSON'; Pattern='raw\.githubusercontent\.com/datasets/geo-countries' },
  @{ Name='GoogleChartQR'; Pattern='chart\.googleapis\.com/chart' },
  @{ Name='XTwitter'; Pattern='x\.com|twitter\.com/intent' },
  @{ Name='LinkedInShare'; Pattern='linkedin\.com/sharing' },
  @{ Name='FacebookShare'; Pattern='facebook\.com/sharer' },
  @{ Name='RedditShare'; Pattern='reddit\.com/submit' },
  @{ Name='WhatsAppShare'; Pattern='wa\.me' }
)

"LIB_LOAD_PATH_MATRIX:"
"| Library | Enqueue (PHP) sites | Dynamic injection (JS) sites | Usage sites (JS) | Conflicts |"
"|---|---|---|---|---|"

foreach ($lib in $libs) {
  $pat = $lib.Pattern
  $enqueue = Select-String -Path ($php | Select-Object -ExpandProperty FullName) -Pattern $pat -ErrorAction SilentlyContinue
  $inject = Select-String -Path ($js  | Select-Object -ExpandProperty FullName) -Pattern $pat -ErrorAction SilentlyContinue

  $enqueueSites = if ($enqueue) { ($enqueue | Select-Object -First 8 | ForEach-Object { "$($_.Path):$($_.LineNumber)" }) -join "<br>" } else { "" }
  $injectSites  = if ($inject)  { ($inject  | Select-Object -First 8 | ForEach-Object { "$($_.Path):$($_.LineNumber)" }) -join "<br>" } else { "" }

  # Usage heuristic: presence of globals like window.L, window.d3, gsap, ScrollTrigger, etc.
  $usageSites = ""
  if ($lib.Name -eq 'Leaflet') {
    $usage = Select-String -Path ($js | Select-Object -ExpandProperty FullName) -Pattern '\b(window\.L|L\.)\b' -ErrorAction SilentlyContinue
    if ($usage) { $usageSites = ($usage | Select-Object -First 8 | ForEach-Object { "$($_.Path):$($_.LineNumber)" }) -join "<br>" }
  } elseif ($lib.Name -eq 'D3') {
    $usage = Select-String -Path ($js | Select-Object -ExpandProperty FullName) -Pattern '\b(window\.d3|d3\.)\b' -ErrorAction SilentlyContinue
    if ($usage) { $usageSites = ($usage | Select-Object -First 8 | ForEach-Object { "$($_.Path):$($_.LineNumber)" }) -join "<br>" }
  } elseif ($lib.Name -eq 'GSAP') {
    $usage = Select-String -Path ($js | Select-Object -ExpandProperty FullName) -Pattern '\b(gsap|ScrollTrigger)\b' -ErrorAction SilentlyContinue
    if ($usage) { $usageSites = ($usage | Select-Object -First 8 | ForEach-Object { "$($_.Path):$($_.LineNumber)" }) -join "<br>" }
  } else {
    $usageSites = if ($injectSites) { $injectSites } else { "" }
  }

  $conflict = ""
  if ($enqueueSites -and $injectSites) { $conflict = "enqueue+inject (double load risk)" }

  "| $($lib.Name) | $enqueueSites | $injectSites | $usageSites | $conflict |"
}



