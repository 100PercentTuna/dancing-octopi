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

function TagIfMatch($content, $regex, $tag) {
  if ($content -match $regex) { return $tag }
  return $null
}

function SummarizeFile([string]$path, [string]$type) {
  $raw = Get-Content -Raw -Path $path -ErrorAction Stop
  $tags = @()

  if ($type -eq 'php') {
    $tags += TagIfMatch $raw 'add_action\s*\(' 'hooks:add_action'
    $tags += TagIfMatch $raw 'add_filter\s*\(' 'hooks:add_filter'
    $tags += TagIfMatch $raw 'wp_(enqueue|register)_(script|style)\s*\(' 'assets:enqueue'
    $tags += TagIfMatch $raw 'wp_add_inline_(script|style)\s*\(' 'assets:inline'
    $tags += TagIfMatch $raw 'wp_localize_script\s*\(' 'assets:localize'
    $tags += TagIfMatch $raw '\$_(GET|POST|REQUEST)\b' 'input:superglobal'
    $tags += TagIfMatch $raw 'wp_ajax_(nopriv_)?[A-Za-z0-9_]+' 'endpoint:ajax'
    $tags += TagIfMatch $raw 'register_rest_route\s*\(' 'endpoint:rest'
    $tags += TagIfMatch $raw '(wp_verify_nonce|check_ajax_referer)\s*\(' 'sec:nonce'
    $tags += TagIfMatch $raw 'current_user_can\s*\(' 'sec:caps'
    $tags += TagIfMatch $raw '\$wpdb\b' 'db:wpdb'
    $tags += TagIfMatch $raw '\b(WP_Query|get_posts|query_posts)\s*\(' 'db:query'
    $tags += TagIfMatch $raw '\b(set_transient|get_transient|wp_cache_)\b' 'perf:cache'
    $tags += TagIfMatch $raw '\b(esc_html|esc_attr|esc_url|esc_url_raw|wp_kses_post)\s*\(' 'sec:escaping'
    $tags += TagIfMatch $raw '\b(sanitize_text_field|sanitize_email|sanitize_textarea_field|absint)\s*\(' 'sec:sanitize'
    $tags += TagIfMatch $raw '<script\b' 'output:inline_script'
  }

  if ($type -eq 'js') {
    $tags += TagIfMatch $raw '\bwindow\.' 'js:window_global'
    $tags += TagIfMatch $raw '\b(innerHTML|insertAdjacentHTML|outerHTML)\b' 'js:dom_sink'
    $tags += TagIfMatch $raw 'fetch\s*\(' 'js:fetch'
    $tags += TagIfMatch $raw "createElement\\('script'\\)" 'js:dynamic_script'
    $tags += TagIfMatch $raw 'IntersectionObserver' 'js:intersection_observer'
    $tags += TagIfMatch $raw 'MutationObserver' 'js:mutation_observer'
    $tags += TagIfMatch $raw 'setInterval\s*\(' 'js:setInterval'
    # Use substring matching here to avoid regex edge-cases with parentheses in patterns.
    if ($raw.Contains("addEventListener('scroll'") -or $raw.Contains('addEventListener("scroll"')) { $tags += 'js:scroll_listener' }
    if ($raw.Contains("addEventListener('resize'") -or $raw.Contains('addEventListener("resize"')) { $tags += 'js:resize_listener' }
    $tags += TagIfMatch $raw 'localStorage\.' 'js:localStorage'
    $tags += TagIfMatch $raw '\bconsole\.log\b' 'js:console_log'
    $tags += TagIfMatch $raw '\balert\s*\(' 'js:alert'
  }

  if ($type -eq 'css') {
    $tags += TagIfMatch $raw '!important' 'css:important'
    $tags += TagIfMatch $raw '(^|\s)(html|body|\*)\b' 'css:global_selector'
    $tags += TagIfMatch $raw '@media\s+print' 'css:print'
    $tags += TagIfMatch $raw 'data-theme="dark"|prefers-color-scheme' 'css:dark_mode'
  }

  $tags = $tags | Where-Object { $_ } | Sort-Object -Unique
  [pscustomobject]@{
    Path = $path
    Tags = ($tags -join ', ')
  }
}

function EmitTable([string]$title, $rows) {
  "### $title"
  "| File | Tags (heuristic: responsibilities/inputs/sinks/hooks/enqueues) | Linked canonical finding IDs (heuristic mapping) |"
  "|---|---|---|"
  foreach ($r in $rows) {
    $f = $r.Path.Replace($repo + '\', '')
    $t = if ($r.Tags -and $r.Tags.Trim().Length -gt 0) { $r.Tags } else { "No issues found (by heuristic scan)" }
    $ids = @()
    foreach ($tag in ($r.Tags -split ',\s*')) {
      if ($tagToFindingIds.ContainsKey($tag)) { $ids += $tagToFindingIds[$tag] }
    }
    $ids = $ids | Where-Object { $_ } | Sort-Object -Unique
    $idText = if ($ids.Count -gt 0) { ($ids -join ', ') } else { "No issues found (by heuristic mapping)" }
    # PowerShell uses backtick as an escape char; build markdown backticks via string concatenation.
    '| `' + $f + '` | ' + $t + ' | ' + $idText + ' |'
  }
  ""
}

$tagToFindingIds = @{
  'hooks:add_action'        = @('CAN-WP-001')
  'hooks:add_filter'        = @('CAN-WP-001')
  'assets:enqueue'          = @('CAN-ASSET-001','CAN-PERF-FE-001')
  'assets:inline'           = @('CAN-ASSET-002','CAN-PERF-FE-002')
  'assets:localize'         = @('CAN-ASSET-003')
  'input:superglobal'       = @('CAN-SEC-INPUT-001')
  'endpoint:ajax'           = @('CAN-SEC-AJAX-001')
  'endpoint:rest'           = @('CAN-SEC-REST-001')
  'sec:nonce'               = @('CAN-SEC-NONCE-001')
  'sec:caps'                = @('CAN-SEC-CAPS-001')
  'sec:escaping'            = @('CAN-SEC-ESC-001')
  'sec:sanitize'            = @('CAN-SEC-SAN-001')
  'db:query'                = @('CAN-PERF-BE-QUERY-001')
  'db:wpdb'                 = @('CAN-SEC-SQL-001')
  'perf:cache'              = @('CAN-PERF-BE-CACHE-001')
  'output:inline_script'    = @('CAN-SEC-TPL-001')
  'js:window_global'        = @('CAN-JS-ARCH-001')
  'js:dom_sink'             = @('CAN-SEC-JS-DOM-001')
  'js:dynamic_script'       = @('CAN-PERF-FE-THIRDPARTY-001','CAN-SEC-JS-DOM-002')
  'js:fetch'                = @('CAN-PERF-FE-NET-001','CAN-SEC-JS-NET-001')
  'js:intersection_observer' = @('CAN-PERF-FE-OBS-001')
  'js:mutation_observer'    = @('CAN-PERF-FE-OBS-001')
  'js:setInterval'          = @('CAN-PERF-FE-TIMER-001')
  'js:scroll_listener'      = @('CAN-PERF-FE-LISTENER-001')
  'js:resize_listener'      = @('CAN-PERF-FE-LISTENER-001')
  'js:localStorage'         = @('CAN-JS-ARCH-002')
  'js:console_log'          = @('CAN-JS-QUAL-001')
  'js:alert'                = @('CAN-JS-QUAL-002')
  'css:important'           = @('CAN-CSS-ARCH-001')
  'css:global_selector'     = @('CAN-CSS-ARCH-002')
  'css:dark_mode'           = @('CAN-CSS-DARK-001')
  'css:print'               = @('CAN-CSS-PRINT-001')
}

$phpFiles = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.php | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName
$jsFiles  = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.js  | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName
$cssFiles = Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter *.css | Where-Object { -not (IsExcludedPath $_.FullName) } | Sort-Object FullName

$phpRows = $phpFiles | ForEach-Object { SummarizeFile $_.FullName 'php' }
$jsRows  = $jsFiles  | ForEach-Object { SummarizeFile $_.FullName 'js' }
$cssRows = $cssFiles | ForEach-Object { SummarizeFile $_.FullName 'css' }

EmitTable "Coverage: PHP files (all, excluding specs)" $phpRows
EmitTable "Coverage: JS files (all, excluding specs)" $jsRows
EmitTable "Coverage: CSS files (all, excluding specs)" $cssRows

# Blocks coverage (all 51 directories)
"### Coverage: Blocks (all block directories)"
"| Block | Files present | Notable tags | Linked canonical finding IDs (heuristic mapping) |"
"|---|---|---|---|"
$blocks = Get-ChildItem -Directory -Path 'kunaal-theme/blocks' | Sort-Object Name
foreach ($b in $blocks) {
  $dir = $b.FullName
  $name = $b.Name
  $present = @()
  foreach ($p in @('block.json','edit.js','render.php','style.css','view.js','index.js')) {
    if (Test-Path (Join-Path $dir $p)) { $present += $p }
  }
  $notable = @()
  $viewPath = Join-Path $dir 'view.js'
  if (Test-Path $viewPath) {
    $raw = Get-Content -Raw -Path $viewPath
    if ($raw -match "createElement\('script'\)") { $notable += "dynamic_script" }
    if ($raw -match "\binnerHTML\b|\binsertAdjacentHTML\b") { $notable += "dom_sink" }
    if ($raw -match "\bwindow\.") { $notable += "window_global" }
    if ($raw -match "IntersectionObserver") { $notable += "intersection_observer" }
    if ($raw -match "setInterval\s*\(") { $notable += "setInterval" }
    if ($raw -match "https?://") { $notable += "remote_dependency" }
  }
  $presentText = if ($present.Count -gt 0) { ($present -join ', ') } else { "(empty)" }
  $notableText = if ($notable.Count -gt 0) { ($notable | Sort-Object -Unique) -join ', ' } else { "No issues found (by heuristic scan)" }
  $ids = @()
  foreach ($tag in ($notableText -split ',\s*')) {
    if ($tagToFindingIds.ContainsKey('js:' + $tag)) { $ids += $tagToFindingIds['js:' + $tag] }
    if ($tagToFindingIds.ContainsKey($tag)) { $ids += $tagToFindingIds[$tag] }
  }
  $ids = $ids | Where-Object { $_ } | Sort-Object -Unique
  $idText = if ($ids.Count -gt 0) { ($ids -join ', ') } else { "No issues found (by heuristic mapping)" }
  '| `' + $name + '` | ' + $presentText + ' | ' + $notableText + ' | ' + $idText + ' |'
}



