# Implementation Plan: Azha Brand Rebranding

**Branch**: `001-azha-brand-rebranding` | **Date**: 2026-09-24 | **Spec**: [spec.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/spec.md)

**Input**: Feature specification from [spec.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/spec.md), supplemented by the source rebranding plan at [azha-rebranding-plan.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/azha-rebranding-plan.md).

## Summary

Rebrand the Azha Travel Hotels admin application (Laravel 12 + Vuexy template) so that every visible surface presents the official Azha Travel brand identity instead of the default Vuexy template look-and-feel. Deliverables: (1) a new CSS brand-override file, with brand colors (Navy Blue `#12214c` as primary, Gold `#af934e` as accent) and official fonts (Montserrat + Arabic companion), loaded from the shared admin + login Blade layouts; (2) Azha logo variants + favicon deployed under `public/assets/` and wired into sidebar, navbar, login page, and browser-tab icons; (3) all 6 PDF export templates re-styled inline to the Navy/Gold palette and official Azha logo + fonts; (4) theme config default color pinned to Navy so returning users with stale `localStorage` customizer state see the rebrand on first load; (5) all visible previous-template brand text replaced with AZHA / AZHA Travel via the existing `__()` localization helpers; and (6) Arabic RTL layouts verified to render the same brand assets with zero brand regressions. No database migrations or business-logic changes are in scope; all existing models, RBAC, activity logging, and booking workflows are preserved exactly.

## Technical Context

**Language/Version**: PHP ^8.2 (Laravel ^12.0 application with Blade server-side rendering; frontend assets delivered as static CSS/JS/fonts/images plus localized Blade templates).

**Primary Dependencies**: `laravel/framework` (^12.0), `livewire/livewire` (^3.7, used sparingly for dynamic UI; out of scope for brand identity), `mcamara/laravel-localization` (^2.3, routing + session locale state; heavily used for ar/en + RTL), `mpdf/mpdf` (^8.2, PDF rendering for the 6 export documents), `spatie/laravel-permission` (^6.23, RBAC; no changes required), `spatie/laravel-activitylog` (^4.10, audit; no changes required). Frontend template delivery: Vuexy vendor assets under `public/assets/vendor/` plus application static files under `public/assets/`.

**Storage**: File-based static assets only. No new database tables, migrations, Eloquent models, or cached application state required for this rebrand. Existing filesystems (`public` disk + Blade view paths) are the only storage surfaces touched.

**Testing**: `pestphp/pest` (^4.1) is the canonical PHP test runner for this project (`composer test`). Brand identity is primarily visual; automated tests are limited to render-based assertions (color CSS variable presence, localized string assertions for new AZHA messages, PDF generation returns 200 with logo asset embedded). Manual visual validation is the primary QA surface and is documented in [quickstart.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md).

**Target Platform**: Linux/macOS production + local development web servers running PHP 8.2+, served behind any standard reverse proxy (nginx/Apache). Client target: modern desktop web browsers (Chrome/Edge/Firefox/Safari, latest 2 stable releases) for admin usage; PDF rendering server-side via `mPDF` on PHP 8.2+.

**Project Type**: Monolithic web application (Laravel backend + Blade server-rendered UI), admin-facing panel for hotels/operations users, with a self-contained set of printable PDF documents shared with banks, clients, and partners.

**Performance Goals**: No measurable performance regression on first-load Time-to-Interactive (TTI) for the admin dashboard or login page. The new CSS override file must be small (< ~40KB unminified) relative to the existing vendor `core.css`, and font preloading must use `font-display: swap` to avoid layout blocking. PDF generation time per document must not increase by more than 15% compared to pre-rebrand baselines (brand-only styling + `@font-face` rules are cheap in `mPDF`, but the 6 PDFs are benchmarked anyway).

**Constraints**:
- Brand overrides MUST survive the next Vuexy template vendor update: `public/assets/vendor/css/core.css` is NEVER edited directly (per `FR-019`).
- Licensed Arabic companion font (Neue Frutiger World) is NOT shipped to public environments without explicit license confirmation on the target deployment; a pre-selected open-source fallback (Cairo or Tajawal) is declared and tested in both CSS and PDF pipelines.
- Every user-visible string added or modified by the rebrand flows through `__()` / `@lang()` and `lang/en.json` + `lang/ar.json` (per `FR-016`).
- Existing red danger/alert semantic colors in PDFs and admin UI are preserved unchanged (per `FR-013`).
- Stale client-side `TemplateCustomizer` state (localStorage) MUST NOT hide the rebrand on first load (per `FR-018`).

**Scale/Scope**: Single monolith codebase, 2 admin layout variants (vertical sidebar layout + horizontal navbar layout) plus 1 login layout, 6 PDF export templates, and ~15 files touched total (5 new assets/override files + ~10 modified layout / PDF / config files). No inter-service dependencies.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle / Constraint | Status | Notes |
|------------------------|--------|-------|
| **I. Laravel-First Architecture** | ✅ PASS | No new packages added beyond the approved stack. Brand overrides are applied via standard Blade layouts (`app.blade.php`, `login.blade.php`), standard `public/` asset paths, standard Blade templates + `__()` helpers. No custom routing/middleware/Form-Request work required for the rebrand. |
| **II. UI/UX Consistency (Azha Brand)** | ✅ PASS | Palette strictly aligned: Navy Blue `#12214c` primary, Gold `#af934e` accent with documented subtle/light variants; Montserrat for English + Neue Frutiger World (or documented fallback) for Arabic; Vuexy theme is retained as the delivery canvas. PDF exports re-styled per `FR-011` / `FR-012` via `mPDF`. |
| **III. Security & Permissions** | ✅ PASS | `spatie/laravel-permission` unchanged; no new routes or auth changes (only asset paths touched). No new user-input processing; rebrand assets are static files. Existing Form Requests / policies untouched. |
| **IV. Localization (i18n)** | ✅ PASS | `mcamara/laravel-localization` unchanged; all new AZHA strings routed through `__()`; RTL layout verified for sidebar/navbar/footer/login/PDF mirroring and brand asset positions. |
| **V. Audit & Accountability** | ✅ PASS | No model create/update/delete behavior changes; `spatie/laravel-activitylog` continues to track exactly what it tracks today (bookings/customers/users/roles/etc.). `LogsActivity` trait and listeners untouched. |
| **Technology Stack Constraints (PHP ^8.2 / Laravel 12 / MySQL / Blade+Vuexy / approved packages)** | ✅ PASS | No new PHP/JS packages introduced; same approved stack used; mPDF for PDFs, spatie/* for permissions/audit, mcamara/* for localization — exactly as ratified. |
| **Migrations via migrations only** | ✅ PASS | Zero migrations (pure asset + template changes). |
| **PSR-12 PHP / no hardcoded secrets / proper error handling** | ✅ PASS | Any Blade edits follow PSR-12 for inline PHP; `config()` / `.env` usage unchanged; error pages/logging untouched. |

No constitution violations detected. Proceeding to Phase 0 research and Phase 1 design. Post-Phase 1 re-evaluation is embedded in the **Constitution Re-check** section at the bottom of this file.

## Project Structure

### Documentation (this feature)

```text
specs/001-azha-brand-rebranding/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
│   └── brand-assets.md  # Brand asset + CSS variable contract (no APIs/endpoints exposed)
├── checklists/
│   └── requirements.md  # Specification quality checklist
└── tasks.md             # Phase 2 output (/speckit-tasks command - NOT created by /speckit-plan)
```

### Source Code (repository root)

```text
azha-rebranding-plan.md  # Source rebranding plan (input, read-only)
Azha Travel Brand kit/   # Source-of-truth brand assets (input, read-only)

public/
├── assets/
│   ├── css/
│   │   ├── demo.css                 # Existing (unchanged)
│   │   └── azha-brand.css           # NEW - brand color + font overrides
│   ├── fonts/
│   │   ├── montserrat/              # NEW - Montserrat woff2 files
│   │   └── neue-frutiger/           # NEW (licensed; optional on deploy)
│   ├── img/
│   │   ├── branding/
│   │   │   ├── logo.png             # Existing; REPLACED with Azha mark
│   │   │   ├── brand-img-light.png  # Existing; REPLACED with Azha light lockup
│   │   │   ├── brand-img-dark.png   # Existing; REPLACED with Azha dark lockup
│   │   │   ├── brand-img-small.png  # Existing; REPLACED with Azha small mark
│   │   │   ├── azha-logo.svg                 # NEW icon-only (light BG)
│   │   │   ├── azha-logo-horizontal.svg      # NEW horizontal (sidebar/navbar)
│   │   │   └── azha-logo-dark.svg            # NEW icon-only (dark BG / dark navbar)
│   │   └── favicon/
│   │       └── favicon.ico          # Existing; REPLACED with Azha favicon
│   └── js/
│       └── config.js                # Modified - defaultPrimaryColor pinned + optional customizer toggle
resources/
├── css/
│   └── app.css                      # Modified - --font-sans updated (FR-008/FR-009 fallback)
├── lang/
│   ├── en.json                      # Modified - "Welcome to AZHA Travel!" + footer strings
│   └── ar.json                      # Modified - matching Arabic localized strings
└── views/
    └── admin/
        ├── layouts/
        │   ├── app.blade.php              # Modified - Google Fonts link + azha-brand.css <link>
        │   ├── footer.blade.php           # Modified - Azha Travel copyright attribution
        │   ├── vertical/
        │   │   └── sidebar.blade.php      # Modified - logo <img> + AZHA brand text
        │   └── horizontal/
        │       └── navbar.blade.php       # Modified - logo <img> + AZHA brand text
        ├── pages/
        │   ├── login.blade.php            # Modified - logo + localized welcome + fonts + CSS
        │   └── bookings/
        │       └── pdf/
        │           ├── export-bank.blade.php       # Modified - Navy header + Gold accent + Azha logo + fonts
        │           ├── export-client.blade.php     # Modified - Azha palette (no old green/blush)
        │           ├── export-detailed.blade.php   # Modified - same palette as export-client
        │           ├── export-guest.blade.php      # Modified - same palette as export-client
        │           └── export-netrate.blade.php    # Modified - same palette as export-client
        └── pdf/
            └── wallet_statement.blade.php          # Modified - Navy header + Gold totals + Azha logo + fonts
```

**Structure Decision**: Selected the existing single-project Laravel + public/assets layout that the repository already uses. Brand identity changes are exclusively layered on top of the current Blade/public layout rather than introducing new modules/packages — the smallest footprint that satisfies `FR-019` (no vendor `core.css` edits) and survives Vuexy updates.

## Complexity Tracking

> Constitution Check passed with zero violations. No complexity justifications required at this stage.

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| — none — | — | — |

---

## Phase 0 → Phase 1 Artifact Index

- **Phase 0 Research**: [research.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/research.md) — resolves 6 previously implicit decisions (override strategy, Arabic fallback font, mPDF font strategy, localStorage theme override, favicon pipeline, PDF color-safe palette).
- **Phase 1 Data Model**: [data-model.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/data-model.md) — file-system entities (Brand Identity System, Admin Layout Shell, PDF Export Document, Theme Preference) with fields, relationships, and invariants (no DB migrations).
- **Phase 1 Contracts**: [contracts/brand-assets.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md) — public contract for the 13 brand CSS variables, 7 asset file paths, 2 localized message keys, and 2 layout anchor points that downstream code/tasks must not break.
- **Phase 1 Quickstart / Validation Guide**: [quickstart.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md) — 10 end-to-end validation scenarios (LTR admin, RTL admin, login, 6 PDFs, cached-theme first-load) that prove the rebrand meets SC-001…SC-007 without implementation details.

---

## Constitution Re-check (Post Phase 1 Design)

*GATE: Re-evaluated after finalizing data model + contracts.*

| Principle / Constraint | Status | Notes |
|------------------------|--------|-------|
| I. Laravel-First | ✅ PASS | Standard `public/assets` paths, standard Blade layouts, standard `asset()` + `__()` helpers; no novel patterns. |
| II. UI/UX Consistency | ✅ PASS | 13 canonical CSS variables + 7 asset paths locked in contract file; Navy/Gold never vary; PDFs re-styled through mPDF inline overrides aligned to the contract palette. |
| III. Security & Permissions | ✅ PASS | No routes/policies/RBAC changes. All new files are read-only static assets (CSS/fonts/images/ico). |
| IV. Localization | ✅ PASS | 2 message keys explicitly defined (`brand.welcome_login`, `brand.footer_copyright`) routed through `__()`; RTL brand-asset mirroring specified in quickstart scenario 2. |
| V. Audit & Accountability | ✅ PASS | Zero Eloquent/model lifecycle changes. |
| Stack Constraints / Quality Gates | ✅ PASS | No migrations, no new PHP packages, no hardcoded secrets, no raw-exception exposures, PSR-12 Blade edits. |

Gate passed. Plan artifacts are aligned with the ratified constitution.
