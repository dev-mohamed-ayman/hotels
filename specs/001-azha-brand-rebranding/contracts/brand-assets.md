# Public Contract: Azha Brand Assets and Identity Surface

**Created**: 2026-09-24 | **Feature**: [spec.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/spec.md)

> **Scope**: This feature does NOT expose network-based APIs, CLI commands, SDK interfaces, or HTTP endpoints for external systems. The only "interfaces" this feature defines are (1) the **public set of CSS variables, asset file paths, and localization keys** that downstream templates, new features, and future brand refreshes MUST depend on rather than hardcode hex values directly, and (2) the **layout anchor points** where brand assets are rendered so that future template upgrades must preserve them. Any task or PR in the implementation phase that breaks these contracts MUST be rejected in code review — they are the stability layer for the rebrand.

---

## Contract 1 — Brand CSS Variables (Admin Web UI)

**Declared in**: `public/assets/css/azha-brand.css`
**Applicable selector scope**: `:root`, `[data-bs-theme=light]`, `[data-bs-theme=dark]`
**Invariants**:
- The variables below are the ONLY brand color/typography variables that downstream Blade templates, custom admin components, or vendor overrides are allowed to reference. Hardcoding `#12214c` or `#af934e` directly in template CSS, inline style attributes, or JS files is NOT allowed — use the variables.
- `azha-brand.css` MUST be loaded AFTER `public/assets/vendor/css/core.css` and AFTER `demo.css`. The cascade order is part of the contract.

| CSS Variable | Value (hex or RGB) | Usage Rule |
|--------------|--------------------|------------|
| `--bs-primary` | `#12214c` | Azha Navy Blue — any primary button, link, selected state, focus ring, alert primary, header accent. MUST be the single source of truth for primary color in web UI. |
| `--bs-primary-rgb` | `18, 33, 76` | Used when constructing `rgba(...)` translucent Navy shades (e.g., borders, subtle backgrounds). MUST equal the RGB of `--bs-primary`. |
| `--bs-primary-bg-subtle` | `#e8eaf0` | Subtle Navy background for cards, alerts, pills, soft accents. |
| `--bs-primary-border-subtle` | `#b0b8cc` | Subtle Navy border for focus rings, outlined components. |
| `--bs-primary-text-emphasis` | `#0a1230` | Heavier Navy used for emphasis text inside subtle components. |
| `--azha-gold` | `#af934e` | Azha Gold accent — brand badges, selected brand marks, totals highlight callouts, accent borders. This is the dedicated accent variable — never repurpose `--bs-info` to carry Gold (even if a prior plan mentioned it). |
| `--azha-gold-rgb` | `175, 147, 78` | RGB representation of Gold for translucent overlays/borders. MUST equal the RGB of `--azha-gold`. |
| `--azha-gold-light` | `#d4b876` | Lighter Gold variant, used for soft backgrounds or row-highlight where full Gold is too intense. |
| `--azha-gold-subtle` | `#faf6ee` | 10% Gold tint — soft brand card background. |
| `--bs-font-sans-serif` | `"Montserrat", "Neue Frutiger World", "Cairo", "Tajawal", -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif` | Canonical font stack. Order matters per research Decision 2. Montserrat first for English; Neue Frutiger World → Cairo → Tajawal fallbacks for Arabic. |

### Dark-theme specific overrides

| CSS Variable (in `[data-bs-theme=dark]`) | Value | Usage Rule |
|-------------------------------------------|-------|------------|
| `--bs-primary` | `#af934e` | Gold becomes the "readable primary" on dark backgrounds (Navy is too dark for contrast on black). Web UI dark mode is OPTIONAL for the MVP, but if the app renders in dark mode, this rule MUST hold. |
| `--bs-primary-rgb` | `175, 147, 78` | Match the above in dark theme. |

---

## Contract 2 — Brand Asset File Paths

These are the canonical static file paths that templates, PDF generators, and favicon logic reference. Tasks that rename or move these files MUST update every file that references them AND update this contract accordingly. Missing files at these paths are contract violations.

| Asset Role | Canonical Path | Acceptable Formats / Notes |
|------------|----------------|-----------------------------|
| Brand CSS override | `public/assets/css/azha-brand.css` | UTF-8 text/css. MUST exist. |
| Standalone Azha icon mark (light backgrounds) | `public/assets/img/branding/azha-logo.svg` | SVG. Vector. Transparent background. Used for sidebar/navbar logos when paired with brand text. |
| Horizontal Azha logo (mark + wordmark, light backgrounds) | `public/assets/img/branding/azha-logo-horizontal.svg` | SVG. Vector. Used for sidebar header and navbar header where wordmark is displayed. |
| Standalone Azha icon mark (dark backgrounds / dark navbars) | `public/assets/img/branding/azha-logo-dark.svg` | SVG. Vector. Must be a version that contrasts well against dark backgrounds. |
| Existing Vuexy replacement — light brand PNG | `public/assets/img/branding/brand-img-light.png` | PNG. Exact file path preserved; content replaced with Azha light brand asset. This path is referenced by existing template code that we do not want to rewrite. |
| Existing Vuexy replacement — dark brand PNG | `public/assets/img/branding/brand-img-dark.png` | PNG. Same rationale as above. |
| Existing Vuexy replacement — small brand PNG | `public/assets/img/branding/brand-img-small.png` | PNG. Small square icon mark. |
| Existing Vuexy replacement — default small logo PNG | `public/assets/img/branding/logo.png` | PNG. Preserves the default `logo.png` path used elsewhere. |
| Browser tab favicon | `public/assets/img/favicon/favicon.ico` | Multi-size ICO containing 16x16, 32x32, and 48x48 Azha Gold mark. Preserves the existing file path. |
| Self-hosted Montserrat font files (OPTIONAL) | `public/assets/fonts/montserrat/*.woff2` | WOFF2 format with `@font-face` declarations in `azha-brand.css`. Optional: Google Fonts CDN link satisfies the font contract even if self-hosted files are not shipped. |
| Self-hosted Neue Frutiger World font files (CONDITIONAL) | `public/assets/fonts/neue-frutiger/*` | Only present if deployment explicitly has the license to ship. Contract allows absence; CSS stack MUST fall back to Cairo/Tajawal via Google Fonts if absent. |

---

## Contract 3 — Localized Brand Message Keys

All new user-visible strings added by the rebrand live here. Do not hardcode them in Blade templates.

| Localization Key | English value (`lang/en.json`) | Arabic value (`lang/ar.json`) | Usage Site |
|------------------|---------------------------------|--------------------------------|------------|
| `brand.welcome_login` | `"Welcome to AZHA Travel! 👋"` | `"أهلاً بك في AZHA Travel! 👋"` | `login.blade.php` headline. MUST be rendered with `{{ __('brand.welcome_login') }}` and MUST NOT reference the former template name. |
| `brand.footer_copyright` | `"© [YEAR] AZHA Travel. All rights reserved."` | `"© [YEAR] جميع الحقوق محفوظة - AZHA Travel"` | `footer.blade.php` attribution line. Replace `[YEAR]` with the current calendar year at render time (or leave as a placeholder if the template chooses to embed year dynamically). |
| `brand.name_short` | `"AZHA"` | `"أظهى"` | Sidebar + navbar brand text next to logos. If brand team prefers the wordmark in both languages, align with the approved local spelling. |
| `brand.name_full` | `"AZHA Travel"` | `"أظهى للسفر"` | Alt text for logos (`alt="AZHA Travel"`), login brand area. |

---

## Contract 4 — Layout Anchor Points (Admin and Login)

These DOM positions in the Blade layouts are the stable places where brand assets render. Future Vuexy template upgrades that move or rename these sections must preserve the brand insertion points:

### Anchor 1 — Admin shared layout head (`app.blade.php`)
- Position: inside `<head>`, after the existing `<link>` for `vendor/css/core.css` and after `demo.css`.
- Required insertion: `<link rel="stylesheet" href="{{ asset('assets/css/azha-brand.css') }}" />`
- Also in `<head>`: the Google Fonts `<link>` for Montserrat + Cairo/Tajawal MUST appear.

### Anchor 2 — Login page head (`login.blade.php`)
- Same requirements as Anchor 1.

### Anchor 3 — Sidebar brand block (`vertical/sidebar.blade.php`)
- Position: the `.app-brand` / `.app-brand-logo` + `.app-brand-text` region at the top of the sidebar.
- Required: logo `<img>` pointed at one of the canonical SVG asset paths (from Contract 2), `alt` uses `brand.name_full`, and adjacent brand text uses `brand.name_short`.
- Forbidden: any occurrence of the former template name as brand text in this block.

### Anchor 4 — Navbar brand block (`horizontal/navbar.blade.php`)
- Same requirements as Anchor 3.

### Anchor 5 — Footer attribution (`layouts/footer.blade.php`)
- Position: any copyright or product-owner attribution line.
- Required: uses `brand.footer_copyright` key via `__()`.

### Anchor 6 — Login headline area (`login.blade.php`)
- Position: the greeting headline H4/H1 inside the login card.
- Required: uses `brand.welcome_login` key via `__()`.
- Forbidden: any reference to the former template name in this headline.

### Anchor 7 — Theme default color in `public/assets/js/config.js`
- Position: `window.templateCustomizer = new TemplateCustomizer({ ... })` constructor options.
- Required: `defaultPrimaryColor: '#12214c'` set explicitly.
- Optional: `displayCustomizer` boolean.

---

## Contract 5 — PDF Export Palette and Asset Contract

Applies to ALL 6 PDF Blade files. Shared contract across every concrete PDF template. Each PDF is responsible for embedding these rules inline via inline `<style>` blocks + inline `style=` attributes (per mPDF rendering constraints — they do not load the web `azha-brand.css`).

| Role in PDF | Exact Hex (from shared palette) | Notes |
|-------------|---------------------------------|-------|
| Colored header background | `#12214c` | Navy Blue, used in bank report and wallet statement headers. |
| Alternating row band 1 (light) | `#f0f1f5` | Navy subtle, row band A. |
| Alternating row band 2 (lighter) | `#e0e3eb` | Navy lighter, row band B. |
| Totals / highlight / badge accent | `#af934e` | Gold. Used for totals rows, strong accents, or gold header sub-rows. |
| Soft accent background (cards / emphasis rows) | `#faf6ee` | Gold 10% tint. |
| Softer Gold row background | `#d4b876` | Where full Gold background is too strong. |
| Brand logo in header/footer (when present) | Any of the Contract 2 canonical Azha logo asset paths (PNG/SVG as supported by mPDF) | MUST be Azha. MUST NOT be the prior template placeholder JPEG filename. If the logo asset is unavailable at runtime, the PDF falls back to textual `AZHA Travel` brand name — never falls back to prior template logo. |
| Body/heading font order (English then Arabic) | Montserrat → (Neue Frutiger World → Cairo → Tajawal) → fallback to mPDF defaults | Font registration in mPDF config is part of implementation; the cascade order is contractual. |
| Danger/alert negative values red | Existing `#c00000` (or project's current semantic danger hex) | PRESERVED (per FR-013). NEVER replaced with Navy or Gold. |
| Arabic/RTL direction mode | `direction: rtl` + appropriate text alignment when locale is Arabic | Preserves brand palette and logo placement even in RTL. |

---

## Contract 6 — Theme Preference Migration (Client-Side)

| Key / Behavior | Contract |
|----------------|----------|
| Stored color in `localStorage` | If the stored `templateCustomizer-*--PrimaryColor` value equals the former default purple `#7367f0` OR any value outside the set `{ '#12214c', '#af934e' }`, it MUST be overwritten ONCE with `#12214c` on first page load after deployment. If it is already in the approved set, it is preserved untouched. |
| Server-declared default | `defaultPrimaryColor` in the `TemplateCustomizer` options MUST be exactly `#12214c`. |

---

## Stability Guarantees

- The CSS variables (Contract 1), asset paths (Contract 2), and locale keys (Contract 3) are the stable public surface. Future feature work that needs a Navy accent or Gold accent MUST use the variables, not re-declare hex values.
- The Vuexy vendor `core.css` file is explicitly OUTSIDE this contract. Tasks MUST NOT modify it, and reviewers MUST reject any diff that writes into `vendor/css/core.css`. This is the most important contract-level constraint for maintainability (directly implements `FR-019`).
- If a future rebrand changes Navy Blue or Gold hex, the task list for that rebrand SHALL only need to update the six Decision-6 palette variables in research, this contract, `azha-brand.css`, and the six PDF blade files — no other template files need touching. That guarantee is the business value of this contract file.
