# Data Model: Azha Brand Rebranding

**Created**: 2026-09-24 | **Feature**: [spec.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/spec.md)

> This feature does **not** introduce any persisted database entities, migrations, or Eloquent models. All "entities" below are file-system-resident artifacts, layout anchor points, and client-side state. They are modeled here to define invariants, relationships, and validation rules that downstream tasks must enforce.

---

## Entity 1 — Brand Identity System

**Represents**: The complete, authoritative set of Azha Travel brand assets and rules that all visible surfaces (web UI and PDFs) must draw from.

**Key attributes / fields**:

| Field | Type / Source | Invariant / Validation |
|-------|---------------|------------------------|
| `primary_hex` | Color hex | Exactly `#12214c` (Azha Navy Blue). |
| `primary_rgb` | RGB triple | Exactly `18, 33, 76`. MUST equal the RGB representation of `primary_hex`. |
| `primary_bg_subtle_hex` | Color hex | `#e8eaf0` (navy-tinted subtle background). |
| `primary_border_subtle_hex` | Color hex | `#b0b8cc` (navy-tinted subtle border). |
| `primary_text_emphasis_hex` | Color hex | `#0a1230` (navy-tinted emphasis text). |
| `accent_hex` | Color hex | Exactly `#af934e` (Azha Gold). |
| `accent_rgb` | RGB triple | Exactly `175, 147, 78`. MUST equal the RGB representation of `accent_hex`. |
| `accent_light_hex` | Color hex | `#d4b876` (lighter Gold for softer accents). |
| `accent_subtle_bg_hex` | Color hex | `#faf6ee` (10% Gold tint, light card backgrounds). |
| `semantic_danger_hex` | Color hex | Existing project danger red (e.g., `#c00000` in PDF exports). INVARIANT: preserved unchanged — NEVER replaced with Navy/Gold. |
| `primary_latin_font` | Font family string | `"Montserrat"`; weights in use: `300,400,500,600,700` + matching italics (per Google Fonts link). |
| `primary_arabic_font` | Font family string | `"Neue Frutiger World"` when license permits deployment in `public/assets/fonts/`. Otherwise MUST be `"Cairo"` or `"Tajawal"` per the research Decision 2 fallback stack. |
| `arabic_fallback_font` | Font family string | Open-source Arabic fallback declared in CSS; default `"Cairo"`. |
| `icon_logo_svg_light` | File path | `public/assets/img/branding/azha-logo.svg` — standalone mark on light background. |
| `icon_logo_svg_dark` | File path | `public/assets/img/branding/azha-logo-dark.svg` — standalone mark on dark background or dark navbar. |
| `horizontal_logo_svg_light` | File path | `public/assets/img/branding/azha-logo-horizontal.svg` — wordmark with mark, for sidebar/navbar headers. |
| `branding_png_light` | File path | `public/assets/img/branding/brand-img-light.png` — replaces the Vuexy default PNG light logo. |
| `branding_png_dark` | File path | `public/assets/img/branding/brand-img-dark.png` — replaces Vuexy default PNG dark logo. |
| `branding_png_small` | File path | `public/assets/img/branding/brand-img-small.png` — replaces Vuexy default small PNG. |
| `branding_png_logo` | File path | `public/assets/img/branding/logo.png` — small default PNG logo spot (Azha mark). |
| `favicon_ico` | File path | `public/assets/img/favicon/favicon.ico` — multi-size (16/32/48) Azha Gold mark icon. |
| `brand_css_override_file` | File path | `public/assets/css/azha-brand.css` — single source of truth for brand CSS vars, `@font-face`, light/dark overrides. |
| `brand_font_dir_latin` | Directory path | `public/assets/fonts/montserrat/` — if self-hosting Montserrat (optional; Google Fonts is also acceptable as the first CDN layer). |
| `brand_font_dir_arabic` | Directory path | `public/assets/fonts/neue-frutiger/` — if and only if license allows; otherwise omitted. |
| `message_key_welcome_login` | Localization key | `brand.welcome_login` — MUST resolve to "Welcome to AZHA Travel! 👋" in English and its correct Arabic equivalent in `lang/ar.json`. |
| `message_key_footer_copyright` | Localization key | `brand.footer_copyright` — MUST resolve to an Azha Travel attribution/copyright line in both locales. |

**Lifecycle**: Static. Once deployed, changes only happen as a future rebrand.

**Relationships**:
- Is referenced by **Admin Layout Shell** (for CSS var loading, logo asset paths, and localized message rendering).
- Is referenced by **PDF Export Document** (for inline palette, registered fonts, logo asset path).
- Interacts with **Theme Preference** (so Theme Preference defaults to `primary_hex`, not stale template purple).

---

## Entity 2 — Admin Layout Shell

**Represents**: The composed set of Blade layout files that frame every authenticated and unauthenticated page of the admin application. This entity anchors brand assets and brand copy so they appear consistently across every feature page.

**Key attributes / fields**:

| Field | Type / Source | Invariant / Validation |
|-------|---------------|------------------------|
| `app_layout_path` | Blade path | `resources/views/admin/layouts/app.blade.php` — authenticated layout shell. |
| `login_layout_path` | Blade path | `resources/views/admin/pages/login.blade.php` — unauthenticated login shell. |
| `vertical_sidebar_path` | Blade path | `resources/views/admin/layouts/vertical/sidebar.blade.php` — vertical layout brand anchor. |
| `horizontal_navbar_path` | Blade path | `resources/views/admin/layouts/horizontal/navbar.blade.php` — horizontal layout brand anchor. |
| `footer_path` | Blade path | `resources/views/admin/layouts/footer.blade.php` — copyright / attribution brand anchor. |
| `google_fonts_link` | `<link>` element | Appears in `<head>` of `app.blade.php` + `login.blade.php`. Value: Montserrat weights (300–700, regular + italic) via `fonts.googleapis.com/css2?family=Montserrat:...&display=swap`. Arabic companion (Cairo or Tajawal per Brand Identity System) included as additional family in the same `<link>` or an adjacent `<link>` with matching `display=swap`. |
| `brand_css_link` | `<link>` element | Appears in `<head>` of `app.blade.php` + `login.blade.php`, **after** any `<link>` that loads the vendor `core.css` and after `demo.css`, and points to `Brand Identity System.brand_css_override_file` via `asset()`. |
| `sidebar_brand_logo_asset` | `<img src>` in vertical sidebar | MUST equal `Brand Identity System.horizontal_logo_svg_light` (or dark variant in dark navbars), with `alt="Azha Travel"` and an explicit width/height matching the current sidebar logo box. |
| `sidebar_brand_text` | Plain text next to sidebar logo | MUST equal `"AZHA"` (or the localized wordmark approved by brand; MUST NOT contain the old template name "Vuexy"). |
| `navbar_brand_logo_asset` | `<img src>` in horizontal navbar | Same constraints as sidebar logo. |
| `navbar_brand_text` | Plain text next to navbar logo | Same constraints as sidebar text. |
| `login_header_identity` | Brand area on login page | MUST display at least ONE of: approved Azha logo, approved Azha wordmark, or `__('brand.welcome_login')` headline. MUST NOT display the old template name in brand position. |
| `login_welcome_headline` | H1/H4 on login page | MUST equal `{{ __('brand.welcome_login') }}` exactly. MUST NOT be a hardcoded string. |
| `footer_attribution_line` | Footer copy | MUST equal `{{ __('brand.footer_copyright') }}` or a clearly-Azha copyright line rendered via localization helpers. MUST reference "Azha Travel". |
| `theme_config_file` | JS asset path | `public/assets/js/config.js`. |
| `theme_default_primary` | JS option in `theme_config_file` | MUST be exactly `defaultPrimaryColor: '#12214c'`, matching `Brand Identity System.primary_hex`. |
| `theme_stale_storage_migration` | Inline JS in config.js or layout | MUST implement Decision 4 (Phase 0) so returning users with stored old-purple or non-approved colors receive Navy `#12214c` on first load. |
| `app_css_font_sans_variable` | CSS variable in `resources/css/app.css` | `--font-sans` (or existing equivalent) MUST include `"Montserrat"` followed by the Arabic font stack documented in Brand Identity System. |

**Lifecycle**: Static after rebrand deployment.

**Relationships**:
- Composes Brand Identity System assets and copy into each page `<head>`, sidebar, navbar, login page, and footer.
- Consumes Theme Preference client state and enforces approved default on stale values.
- Every PDF Export Document (Entity 3) is rendered from a controller action invoked within pages framed by this layout shell, though PDF templates themselves are standalone Blade files.

**Invariants across layout variants**:
- Brand Identity System `primary_hex` / `accent_hex` values MUST render identically in vertical and horizontal layouts.
- Arabic RTL mode MUST NOT swap logo asset validity; logos must remain readable, and the brand text MUST remain AZHA / AZHA Travel (not mirrored or translated).
- The old template name MUST NOT appear in any of the five layout files in a user-visible position (brand text, headlines, footer).

---

## Entity 3 — PDF Export Document (concrete subtypes: Bank / Client / Detailed / Guest / NetRate / WalletStatement)

**Represents**: Any one of the six printable statements/reports generated by the application for clients, banks, or internal finance teams. Each concrete PDF subtype is a standalone Blade file that embeds its own styling inline (consistent with mPDF rendering constraints per research Decision 3).

**Common fields shared by all 6 subtypes**:

| Field | Type / Source | Invariant / Validation |
|-------|---------------|------------------------|
| `blade_path` | Concrete Blade path | One of: <br/>`resources/views/admin/pages/bookings/pdf/export-bank.blade.php`<br/>`resources/views/admin/pages/bookings/pdf/export-client.blade.php`<br/>`resources/views/admin/pages/bookings/pdf/export-detailed.blade.php`<br/>`resources/views/admin/pages/bookings/pdf/export-guest.blade.php`<br/>`resources/views/admin/pages/bookings/pdf/export-netrate.blade.php`<br/>`resources/views/admin/pdf/wallet_statement.blade.php` |
| `header_color_hex` | Inline CSS on header row(s) / header cell | MUST equal `Brand Identity System.primary_hex` (`#12214c`), unless the subtype explicitly has no colored header. |
| `row_band_light_hex` | Inline CSS on alternating row band 1 | MUST equal `#f0f1f5` (per research Decision 6 shared palette). |
| `row_band_lighter_hex` | Inline CSS on alternating row band 2 | MUST equal `#e0e3eb` (per research Decision 6 shared palette). |
| `accent_totals_hex` | Inline CSS on totals / highlight rows / callouts | MUST equal `Brand Identity System.accent_hex` (`#af934e`) or Gold light `#d4b876` for softer row backgrounds. |
| `accent_subtle_bg_hex` | Inline CSS on light Gold-tinted card backgrounds | MUST equal `Brand Identity System.accent_subtle_bg_hex` (`#faf6ee`). |
| `danger_semantic_hex` | Inline CSS on negative/alert values | MUST equal the existing semantic red used prior to rebrand (e.g., `#c00000`). NEVER overwritten by Navy or Gold (FR-013). |
| `brand_logo_asset_absolute` | Absolute path to Azha logo in PDF header/footer | If the subtype shows a brand mark, MUST resolve under the hood to a valid Azha image (one of the PNG/SVG assets from Brand Identity System), NOT the old template placeholder JPEG. When the asset is missing, the PDF MUST still render the correct palette and AZHA textual brand name. |
| `body_font_stack` | Inline `<style>` in the PDF template | English headings/body MUST declare Montserrat as first choice. Arabic MUST declare the same Arabic stack as Brand Identity System (Neue Frutiger World → Cairo → Tajawal → default). Fallback to built-in mPDF fonts only if the custom font directory is unavailable in the deployment environment. |
| `rtl_mode_support` | PDF directive or inline `direction: rtl` | When locale is Arabic, the PDF MUST render with `direction: rtl`, correct text alignment, and still use the same Navy/Gold palette and Azha logo assets. |

**Subtype-specific extra fields**:

| Subtype | Unique constraint |
|---------|-------------------|
| Bank Report (`export-bank`) | Dark header row (Navy) + alternating Navy band rows + Gold totals accent. Header cell carries Azha logo. |
| Wallet Statement (`wallet_statement`) | Same dark header (Navy) + Gold totals pattern as Bank Report. Header cell carries Azha logo. |
| Client / Detailed / Guest / NetRate | Previously contained multiple non-brand colors (blush, green, light blue, green total rows, green header). After rebrand: all these are mapped to the shared palette from research Decision 6. No row headers or totals should be "green" or "blush" — they are now Gold or Navy variants as mapped. |

**Lifecycle**: Static per rebrand. When new PDF subtypes are added later, they MUST conform to the same common fields and shared palette.

**Relationships**:
- References Brand Identity System (colors, fonts, logo).
- The controller action that renders each PDF blade is unchanged in business logic; only the Blade file itself is re-skinned. Activity logging and RBAC for "can export PDF" are untouched, aligning with Constitution V and III.

---

## Entity 4 — Theme Preference (client-side)

**Represents**: The per-user client-side stored color/theme state previously written by the Vuexy `TemplateCustomizer` widget into browser `localStorage`. It is NOT a server-side persisted entity; it is per-browser and per-device.

**Key attributes / fields**:

| Field | Type / Source | Invariant / Validation |
|-------|---------------|------------------------|
| `storage_key_color` | `localStorage` key name | `templateCustomizer-{templateName}--PrimaryColor` — exact key used by TemplateCustomizer implementation (`config.js` references this exact pattern). |
| `storage_key_lang` | `localStorage` key name | `templateCustomizer-{templateName}--Lang` — language preference preserved untouched (not part of rebrand scope beyond ensuring brand colors render correctly in both locales). |
| `approved_primary_colors` | Allowed set | `{ '#12214c', '#af934e' }` — the only two brand-approved primaries allowed. Any stored value outside this set (including the former default purple `#7367f0`) MUST be migrated to `#12214c` on first load after deployment (research Decision 4). |
| `default_primary` | Server-declared default | MUST equal `Brand Identity System.primary_hex` (`#12214c`), surfaced via `Admin Layout Shell.theme_default_primary`. |
| `display_customizer_toggle` | UI visibility flag in `config.js` | Boolean. True by default (preserves user layout UX). Setting to false is a P3 optional task. |

**State transitions**:
- **Pre-rebrand → first load post-rebrand**:
  - `storage_key_color` == old purple `#7367f0` or any value NOT in `approved_primary_colors` → transition to `#12214c` (write to storage) and then render page.
- **Pre-rebrand → first load post-rebrand, value already `#12214c` or `#af934e`**: no-op transition; keep existing.
- **User later tweaks layout density/rtl/lang via customizer but keeps color unchanged**: state transitions are allowed as before; no migration re-runs.

**Relationships**:
- Is read by Admin Layout Shell on every page load.
- Is constrained by Brand Identity System's color palette (approved set + default).
- Does not exist for unauthenticated visitors of login page; login page always renders Azha palette directly from CSS overrides, regardless of stored customizer state (login page `<head>` loads `azha-brand.css` before any customizer code runs).

---

## Cross-Entity Invariants (global rules)

1. **Brand palette consistency**: `Brand Identity System.primary_hex` / `accent_hex` MUST be the EXACT same hex values used in: `azha-brand.css` CSS vars, inline header/row colors inside each PDF Export Document, `config.js` default primary, and any inline brand accents in layouts. There is no "web palette" vs. "PDF palette" — they are the same.
2. **No vendor CSS edits**: No task, no blade, no script is allowed to write changes into `public/assets/vendor/css/core.css`. All brand CSS lives in `Brand Identity System.brand_css_override_file` or in per-PDF inline styles.
3. **Localization for strings**: Every user-visible string introduced for the brand (welcome message, footer attribution, alt text for logos) MUST pass through `__()` helpers and be present in BOTH `lang/en.json` and `lang/ar.json`. Hardcoded English/Arabic in Blade templates is disallowed for these strings.
4. **RTL parity**: Any change made to the LTR Admin Layout Shell MUST be manually verified against the RTL Arabic mode to ensure brand assets and palette still render without regressions.
5. **Red semantic colors preserved**: `Brand Identity System.semantic_danger_hex` and any existing admin UI danger classes are never rebranded to Navy or Gold. They remain red, consistent with existing user training and industry convention.
6. **No business-logic changes**: The Data Model deliberately contains zero persisted model changes — any implementation work that adds, renames, or modifies Eloquent model fields, observers, policies, permissions, routes, or middleware is automatically out of scope and must be reviewed as a separate feature.
