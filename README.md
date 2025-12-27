## dancing-octopi

This repository contains the custom WordPress theme **`kunaal-theme`** (and supporting tooling for local development and audits).

### What gets deployed

GitHub Actions deploys **only** the `kunaal-theme/` directory to your WordPress host:

- Local path: `./kunaal-theme/`
- Remote path: `wp-content/themes/`

Everything outside `kunaal-theme/` is for local workflows, auditing, and repo hygiene only.

### Theme quick start (WordPress)

- **Install**: Upload the `kunaal-theme/` folder to `wp-content/themes/` and activate it in WordPress Admin → Appearance → Themes.
- **Create required pages**:
  - Create a page named **About** and assign template **About Page**
  - Create a page named **Contact** and assign template **Contact Page**
- **Customizer**: Appearance → Customize
  - Most site-wide settings are under the theme’s sections
  - About page content is under the **About Page** panel

### About page editing (Customizer)

The About page is driven entirely by WordPress Customizer settings (no JSON editing).

Typical edits include:
- Hero collage images
- Hero intro text (location / listening / reading)
- By-the-numbers items
- Rabbit holes (image, text, category, optional URL)
- Divider / panorama images
- Books + “On repeat” media
- Places map ISO codes
- Inspirations

### Contact form (reliable email delivery)

Shared hosting often fails to deliver mail reliably via default `wp_mail()`.

This theme supports SMTP configuration via:

- Appearance → Customize → **Email Delivery (SMTP)**

Recommended free SMTP provider:
- **Brevo** (free tier typically covers personal-site volumes)

Configuration steps:
- Enable SMTP
- Set Host / Port / Encryption / Username / Password (provider credentials)
- Set From Email / From Name

Once SMTP is configured, the Contact page form will deliver reliably without additional plugins.

### Subscribe button / subscription flow

The theme supports two subscription modes:

- **Built-in** (recommended): stores subscribers privately in WordPress and sends a confirmation email (double opt-in)
- **External provider**: posts to your provider’s form action URL

Configure via:
- Appearance → Customize → **Subscribe Section**

### Repo housekeeping

- The deployment workflow only ships `kunaal-theme/`. Keep documentation and planning notes outside the theme directory.
- Local-only notes should go under `local-notes/` (gitignored).

### Scripts and audits (local)

The repo includes local scripts for audits/analysis. These are intentionally ignored from deployment and do not affect the live theme.


