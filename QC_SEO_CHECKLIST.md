# Theme-owned SEO QC Checklist

This checklist validates **no visible UI changes** while ensuring high-quality SEO tags and discoverability.

## 0) Transition safety (Yoast on)
- With **Yoast enabled**, view-source on a singular post should NOT contain duplicate:
  - `<meta name="description" ...>`
  - `<link rel="canonical" ...>`
  - `<meta property="og:*" ...>` / `<meta name="twitter:*" ...>`
  - `<script type="application/ld+json">...`
- Expected: theme SEO modules **early-return** when Yoast is active.

## 1) Theme SEO active (Yoast off)
Disable Yoast and re-check:

### 1.1 Meta description
- Singular essay/jotting/page/post:
  - expect exactly one `<meta name="description" ...>` in `<head>`
  - precedence: SEO meta box → subtitle → excerpt → trimmed content → default setting
- Archives:
  - Essays/Jottings/Topics archives use Settings → SEO archive descriptions (fallback to default description)

### 1.2 Canonical URL
- Singular: canonical equals permalink.
- Archives/taxonomies:
  - Page 1 canonical equals base archive URL.
  - Page 2+ canonical equals the page URL (pagination-aware).

### 1.3 Robots directives
- Search results: noindex (if enabled in Settings → SEO).
- 404: always noindex.
- Per-post “Noindex this page/post” meta box: respected.

### 1.4 Open Graph / Twitter cards
- Singular:
  - `og:type=article`
  - `og:title`, `og:description`, `og:url`, `og:image` (if available)
  - `twitter:card=summary_large_image`
- Archives/home:
  - `og:type=website`

### 1.5 Schema.org JSON-LD
- With Yoast OFF:
  - One `<script type="application/ld+json">` exists
  - Includes WebSite + Person graph entries
  - Singular includes BlogPosting/WebPage entry with URL, headline, dates, author
- With noindex views: no JSON-LD output (by theme policy).

### 1.6 Feeds
- Ensure `<link rel="alternate" ... application/rss+xml ...>` exists in `<head>` (automatic-feed-links).

## 2) Admin UX (no Customizer bloat)
- Settings → SEO page:
  - Default description saved and reflected on archives
  - Default share image selectable via media picker
  - Noindex toggles work
- Post editor meta box:
  - Save SEO title/description/noindex/share image
  - Verify no warnings/notices in admin

# Theme-owned SEO QC Checklist

This checklist validates **no visible UI changes** while ensuring high-quality SEO tags and discoverability.

## 0) Transition safety (Yoast on)
- With **Yoast enabled**, view-source on a singular post should NOT contain duplicate:
  - `<meta name="description" ...>`
  - `<link rel="canonical" ...>`
  - `<meta property="og:*" ...>` / `<meta name="twitter:*" ...>`
  - `<script type="application/ld+json">...`
- Expected: theme SEO modules **early-return** when Yoast is active.

## 1) Theme SEO active (Yoast off)
Disable Yoast and re-check:

### 1.1 Meta description
- Singular essay/jotting/page/post:
  - expect exactly one `<meta name="description" ...>` in `<head>`
  - precedence: SEO meta box → subtitle → excerpt → trimmed content → default setting
- Archives:
  - Essays/Jottings/Topics archives use Settings → SEO archive descriptions (fallback to default description)

### 1.2 Canonical URL
- Singular: canonical equals permalink.
- Archives/taxonomies:
  - Page 1 canonical equals base archive URL.
  - Page 2+ canonical equals the page URL (pagination-aware).

### 1.3 Robots directives
- Search results: noindex (if enabled in Settings → SEO).
- 404: always noindex.
- Per-post “Noindex this page/post” meta box: respected.

### 1.4 Open Graph / Twitter cards
- Singular:
  - `og:type=article`
  - `og:title`, `og:description`, `og:url`, `og:image` (if available)
  - `twitter:card=summary_large_image`
- Archives/home:
  - `og:type=website`

### 1.5 Schema.org JSON-LD
- With Yoast OFF:
  - One `<script type="application/ld+json">` exists
  - Includes WebSite + Person graph entries
  - Singular includes BlogPosting/WebPage entry with URL, headline, dates, author
- With noindex views: no JSON-LD output (by theme policy).

### 1.6 Feeds
- Ensure `<link rel="alternate" ... application/rss+xml ...>` exists in `<head>` (automatic-feed-links).

## 2) Admin UX (no Customizer bloat)
- Settings → SEO page:
  - Default description saved and reflected on archives
  - Default share image selectable via media picker
  - Noindex toggles work
- Post editor meta box:
  - Save SEO title/description/noindex/share image
  - Verify no warnings/notices in admin


