# Bundled Data Licenses

This directory contains third-party data files bundled locally for performance and privacy.

---

## World Atlas TopoJSON

- **Data:** World countries boundaries (110m resolution)
- **Version:** 2.0.2
- **License:** Public Domain (Natural Earth data)
- **Upstream URL:** https://github.com/topojson/world-atlas
- **NPM Package:** https://www.npmjs.com/package/world-atlas
- **Date Pulled:** 2024-12-31
- **Files:**
  - `countries-110m.json`

**License:**
This data is derived from Natural Earth (https://www.naturalearthdata.com/), which is in the public domain. The TopoJSON processing by Mike Bostock is also public domain.

---

## Update Procedure

To update:

```bash
curl -o countries-110m.json "https://unpkg.com/world-atlas@2.0.2/countries-110m.json"
```

After updating:
- Update the version number and date in this file
- Test the About page world map
- Commit with message: `[Data] Update world-atlas to vX.X.X`


