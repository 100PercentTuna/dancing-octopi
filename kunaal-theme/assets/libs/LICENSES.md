# Bundled Library Licenses

This directory contains third-party libraries bundled locally for performance, reliability, and privacy.

---

## GSAP (GreenSock Animation Platform)

- **Library:** GSAP Core + ScrollTrigger
- **Version:** 3.12.5
- **License:** GSAP Standard License (free for most uses)
- **Upstream URL:** https://greensock.com/gsap/
- **NPM Package:** https://www.npmjs.com/package/gsap
- **Date Pulled:** 2024-12-31
- **Files:**
  - `gsap.min.js`
  - `ScrollTrigger.min.js`

**License Summary:**
GSAP is free to use for personal projects, most commercial projects, and can be used on unlimited sites without a paid license. The "No Charge" license applies to most use cases. See https://greensock.com/licensing/ for full details.

---

## D3.js

- **Library:** D3 (Data-Driven Documents)
- **Version:** 7.x (v7 minified bundle)
- **License:** ISC License
- **Upstream URL:** https://d3js.org/
- **GitHub:** https://github.com/d3/d3
- **Date Pulled:** 2024-12-31
- **Files:**
  - `d3.v7.min.js`

**License Text (ISC):**
```
Copyright 2010-2023 Mike Bostock

Permission to use, copy, modify, and/or distribute this software for any purpose
with or without fee is hereby granted, provided that the above copyright notice
and this permission notice appear in all copies.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS
OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER
TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF
THIS SOFTWARE.
```

---

## TopoJSON Client

- **Library:** TopoJSON Client
- **Version:** 3.x
- **License:** ISC License
- **Upstream URL:** https://github.com/topojson/topojson-client
- **NPM Package:** https://www.npmjs.com/package/topojson-client
- **Date Pulled:** 2024-12-31
- **Files:**
  - `topojson-client.min.js`

**License:** Same ISC license as D3.js (same author/ecosystem).

---

## Update Procedure

To update these libraries:

1. **GSAP:**
   ```bash
   curl -o gsap.min.js "https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"
   curl -o ScrollTrigger.min.js "https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"
   ```

2. **D3.js:**
   ```bash
   curl -o d3.v7.min.js "https://d3js.org/d3.v7.min.js"
   ```

3. **TopoJSON Client:**
   ```bash
   curl -o topojson-client.min.js "https://unpkg.com/topojson-client@3/dist/topojson-client.min.js"
   ```

4. **After updating:**
   - Update the version numbers in this file
   - Update the `Date Pulled` field
   - Test all blocks that depend on these libraries
   - Commit with message: `[Deps] Update bundled libraries - [library names]`

---

## Why Bundle Locally?

1. **Reliability:** No CDN failures or outages
2. **Performance:** Full cache control, no third-party DNS lookups
3. **Privacy/GDPR:** No requests to external servers
4. **Determinism:** Pinned versions, no surprise updates


