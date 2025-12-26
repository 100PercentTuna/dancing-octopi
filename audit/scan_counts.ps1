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

function Scan([string]$label, [string]$pattern, [string[]]$extensions) {
  $files = @()
  foreach ($ext in $extensions) {
    $files += Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter "*.$ext" | Where-Object { -not (IsExcludedPath $_.FullName) }
  }
  $files = $files | Sort-Object FullName -Unique

  $hits = @()
  foreach ($f in $files) {
    $m = Select-String -Path $f.FullName -Pattern $pattern -AllMatches
    if ($m) { $hits += $m }
  }

  $total = 0
  foreach ($h in $hits) { $total += $h.Matches.Count }

  "=== $label ==="
  "PATTERN=$pattern"
  "TOTAL_MATCHES=$total"
  "TOP10_HITS:"
  $top = $hits | Select-Object -First 10
  foreach ($t in $top) {
    $lineText = ($t.Line -replace '\s+$','')
    "$($t.Path):$($t.LineNumber):$lineText"
  }
  ""
}

function ScanSimple([string]$label, [string]$literal, [string[]]$extensions) {
  $files = @()
  foreach ($ext in $extensions) {
    $files += Get-ChildItem -Recurse -File -Path 'kunaal-theme' -Filter "*.$ext" | Where-Object { -not (IsExcludedPath $_.FullName) }
  }
  $files = $files | Sort-Object FullName -Unique

  $hits = @()
  foreach ($f in $files) {
    $m = Select-String -Path $f.FullName -SimpleMatch -Pattern $literal -AllMatches
    if ($m) { $hits += $m }
  }

  # With -SimpleMatch, MatchInfo.Matches isn't reliable for occurrence counts.
  # Count occurrences per matched line using escaped regex of the literal needle.
  $total = 0
  $needle = [regex]::Escape($literal)
  foreach ($h in $hits) {
    $total += [regex]::Matches($h.Line, $needle).Count
  }

  "=== $label ==="
  "PATTERN(simple)=$literal"
  "TOTAL_MATCHES=$total"
  "TOP10_HITS:"
  $top = $hits | Select-Object -First 10
  foreach ($t in $top) {
    $lineText = ($t.Line -replace '\s+$','')
    "$($t.Path):$($t.LineNumber):$lineText"
  }
  ""
}

# Hooks / priorities
Scan "HOOKS_add_action" 'add_action\s*\(' @('php')
Scan "HOOKS_add_filter" 'add_filter\s*\(' @('php')
Scan "HOOKS_priorities_add_action" 'add_action\s*\([^;]*,\s*[0-9]+\s*\)' @('php')
Scan "HOOKS_priorities_add_filter" 'add_filter\s*\([^;]*,\s*[0-9]+\s*(,\s*[0-9]+\s*)?\)' @('php')

# Enqueue / load paths
Scan "ENQUEUE_wp_enqueue_or_register" 'wp_(enqueue|register)_(script|style)\s*\(' @('php')
Scan "ENQUEUE_wp_add_inline" 'wp_add_inline_(script|style)\s*\(' @('php')
Scan "ENQUEUE_wp_localize_script" 'wp_localize_script\s*\(' @('php')
Scan "ENQUEUE_loader_tag_filters" '(script_loader_tag|style_loader_tag)\s*\(' @('php')

# Dynamic injection (JS)
Scan "JS_dynamic_script_createElement" 'createElement\([''"]script[''"]\)' @('js')
Scan "JS_dynamic_document_write" 'document\.write' @('js')
Scan "JS_dynamic_import" '\bimport\s*\(' @('js')

# Inputs/security
Scan "SEC_superglobals" '\$_(GET|POST|REQUEST)\b' @('php')
Scan "SEC_ajax_actions" 'wp_ajax_(nopriv_)?[A-Za-z0-9_]+' @('php')
Scan "SEC_rest_register" 'register_rest_route\s*\(' @('php')
Scan "SEC_nonce_checks" '(wp_verify_nonce|check_ajax_referer)\s*\(' @('php')
Scan "SEC_caps" 'current_user_can\s*\(' @('php')
Scan "SEC_escaping" '\b(esc_html|esc_attr|esc_url|esc_url_raw|wp_kses_post)\s*\(' @('php')
Scan "SEC_sanitization" '\b(sanitize_text_field|sanitize_email|sanitize_textarea_field|absint)\s*\(' @('php')
Scan "SEC_wpdb" '\$wpdb\b' @('php')

# JS DOM sinks
Scan "JS_dom_sinks" '\b(innerHTML|insertAdjacentHTML|outerHTML)\b' @('js')

# Queries & perf
Scan "PERF_queries" '\b(WP_Query|get_posts|query_posts)\s*\(' @('php')
Scan "PERF_meta_tax_query" '\b(meta_query|tax_query)\b' @('php')
Scan "PERF_get_theme_mod" '\bget_theme_mod\s*\(' @('php')
Scan "PERF_get_post_meta" '\bget_post_meta\s*\(' @('php')
Scan "PERF_get_option" '\bget_option\s*\(' @('php')
Scan "PERF_get_the_terms" '\bget_the_terms\s*\(' @('php')
Scan "PERF_get_terms" '\bget_terms\s*\(' @('php')
Scan "PERF_theme_mod_option_meta" '\b(get_theme_mod|get_option|get_post_meta|get_the_terms|get_terms)\s*\(' @('php')
Scan "PERF_caching" '\b(set_transient|get_transient|wp_cache_)\b' @('php')
Scan "PERF_file_exists" '\bfile_exists\s*\(' @('php')
Scan "PERF_remote_http" '\bwp_remote_(get|post)\s*\(' @('php')

# CSS/JS quality
Scan "CSS_important" '!important' @('css')
Scan "JS_window_dot" '\bwindow\.' @('js')
Scan "JS_intersection_observer" "IntersectionObserver" @('js')
Scan "JS_mutation_observer" "MutationObserver" @('js')
ScanSimple "JS_setInterval" "setInterval(" @('js')
ScanSimple "JS_scroll_listener" "addEventListener('scroll'" @('js')
ScanSimple "JS_resize_listener" "addEventListener('resize'" @('js')



