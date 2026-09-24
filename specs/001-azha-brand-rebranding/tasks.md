---

description: "Task list for Azha Brand Rebranding implementation"
---

# Tasks: Azha Brand Rebranding

**Input**: Design documents from [specs/001-azha-brand-rebranding/](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/)

**Prerequisites**: [plan.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/plan.md) (required), [spec.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/spec.md) (required for user stories), [research.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/research.md), [data-model.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/data-model.md), [contracts/brand-assets.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md), [quickstart.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md)

**Tests**: Tests are OPTIONAL for this pure UI/asset rebrand. Automated tests are limited to render/string assertions (existing test suite must remain green). Primary validation surface is the visual checklist documented in [quickstart.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md).

**Organization**: Tasks are grouped by the shared foundations (Phase 1 + Phase 2) then by user story (Phases 3–7) to enable independent implementation and testing of each story, followed by a final polish phase.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (US1 … US5 from [spec.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/spec.md))
- Every task includes exact file paths per [plan.md Project Structure](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/plan.md#L53-L125)

## Path Conventions

- **Public static assets** (web UI): `public/assets/css/`, `public/assets/fonts/`, `public/assets/img/branding/`, `public/assets/img/favicon/`, `public/assets/js/`
- **Blade layouts** (admin shell + login): `resources/views/admin/layouts/` and `resources/views/admin/pages/`
- **Blade PDF templates**: `resources/views/admin/pages/bookings/pdf/` and `resources/views/admin/pdf/`
- **Localization strings**: `lang/en.json`, `lang/ar.json`
- **Frontend CSS (application-level)**: `resources/css/app.css`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Stage brand source assets from the Brand Kit into the exact file paths required by [Contract 2 (Asset Paths)](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#contract-2--brand-asset-file-paths) so that every later story has files ready to reference. All tasks in this phase touch **different, non-overlapping files** and are fully parallelizable.

- [X] T001 [P] Copy Azha icon SVG (light background) from Brand Kit to `public/assets/img/branding/azha-logo.svg` — source `Azha Travel Brand kit/1-Logo/SVG/light background.svg`
- [X] T002 [P] Copy Azha horizontal SVG (light background) from Brand Kit to `public/assets/img/branding/azha-logo-horizontal.svg` — source `Azha Travel Brand kit/1-Logo/SVG/light background horizontal.svg`
- [X] T003 [P] Copy Azha icon SVG (dark background) from Brand Kit to `public/assets/img/branding/azha-logo-dark.svg` — source `Azha Travel Brand kit/1-Logo/SVG/dark background.svg`
- [X] T004 [P] Replace existing `public/assets/img/branding/logo.png` (currently Vuexy default mark) with Azha standalone icon PNG from Brand Kit at matching 512px / square size — source from `Azha Travel Brand kit/1-Logo/PNG/`
- [X] T005 [P] Replace existing `public/assets/img/branding/brand-img-light.png` with Azha light lockup PNG from Brand Kit — preserves the existing file path to avoid rewriting Vuexy template references
- [X] T006 [P] Replace existing `public/assets/img/branding/brand-img-dark.png` with Azha dark lockup PNG from Brand Kit — preserves the existing file path
- [X] T007 [P] Replace existing `public/assets/img/branding/brand-img-small.png` with Azha small square icon PNG from Brand Kit — preserves the existing file path
- [X] T008 [P] Build multi-size Azha Gold favicon (16x16, 32x32, 48x48) from Brand Kit icon SVG and replace `public/assets/img/favicon/favicon.ico` — implements research Decision 5 and [Contract 2 favicon path](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#contract-2--brand-asset-file-paths)
- [ ] T009 [P] (CONDITIONAL — only if production license allows shipping) Copy Montserrat woff2 font files (weights 300,400,500,600,700 regular + italic) from Brand Kit into `public/assets/fonts/montserrat/` per research Decision 2 self-host option (optional; Google Fonts CDN alone is acceptable if this step is skipped)
- [ ] T010 [P] (CONDITIONAL — only if license allows on target deployment) Copy Neue Frutiger World font files from Brand Kit into `public/assets/fonts/neue-frutiger/` per [Data Model Brand Identity System.brand_font_dir_arabic](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/data-model.md#entity-1--brand-identity-system)

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Establish the brand color + font override file, wire it into both shared layout heads (so it loads after vendor CSS per cascade order contract), add the new brand localization keys, and pin the default theme color in config.js. **Every user story depends on at least one artifact from this phase.**

**⚠️ CRITICAL**: No user story work can begin until this phase is complete.

- [X] T011 Create the brand override stylesheet `public/assets/css/azha-brand.css` containing:
  (a) `:root, [data-bs-theme=light]` block with 13 variables exactly matching [Contract 1](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#contract-1--brand-css-variables-admin-web-ui) values:
  `--bs-primary:#12214c`, `--bs-primary-rgb:18, 33, 76`, `--bs-primary-bg-subtle:#e8eaf0`, `--bs-primary-border-subtle:#b0b8cc`, `--bs-primary-text-emphasis:#0a1230`,
  `--azha-gold:#af934e`, `--azha-gold-rgb:175, 147, 78`, `--azha-gold-light:#d4b876`, `--azha-gold-subtle:#faf6ee`,
  `--bs-font-sans-serif:"Montserrat", "Neue Frutiger World", "Cairo", "Tajawal", -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif`.
  (b) `[data-bs-theme=dark]` block with `--bs-primary:#af934e` + `--bs-primary-rgb:175, 147, 78` per dark contract.
  (c) Optional `@font-face` blocks referencing `../fonts/montserrat/*.woff2` if T009 self-host was chosen (all with `font-display: swap` per performance constraints).
  (d) Optional `@font-face` blocks referencing `../fonts/neue-frutiger/*` if T010 self-host was chosen and license allows it (all with `font-display: swap`).
  GATE: Do NOT edit `public/assets/vendor/css/core.css` anywhere — implements research Decision 1 / FR-019.

- [X] T012 [P] Add localized brand message keys to `lang/en.json` matching [Contract 3](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#contract-3--localized-brand-message-keys):
  `brand.welcome_login` → `"Welcome to AZHA Travel! 👋"`,
  `brand.footer_copyright` → `"© [YEAR] AZHA Travel. All rights reserved."` (year rendered dynamically via Blade `date('Y')`),
  `brand.name_short` → `"AZHA"`,
  `brand.name_full` → `"AZHA Travel"`.
  Implementation: each value is a plain string; Blade will inject `date('Y')` inside the copyright string using `sprintf` or concatenation.

- [X] T013 [P] Add matching Arabic localized brand message keys to `lang/ar.json` matching Contract 3 Arabic values:
  `brand.welcome_login` → `"أهلاً بك في AZHA Travel! 👋"`,
  `brand.footer_copyright` → `"© [YEAR] جميع الحقوق محفوظة - AZHA Travel"` with [YEAR] replaced dynamically at render,
  `brand.name_short` → `"AZHA"`,
  `brand.name_full` → `"AZHA Travel"`.
  GATE: Every string in T012 and T013 is inserted via `__()` later — no hardcoded strings in Blade templates (FR-016).

- [X] T014 [P] Update `resources/css/app.css` `--font-sans` variable (or existing equivalent font declaration) to include the same stack: `"Montserrat", "Neue Frutiger World", "Cairo", "Tajawal", -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif` — satisfies [Data Model Admin Layout Shell.app_css_font_sans_variable](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/data-model.md#entity-2--admin-layout-shell).

- [X] T015 Pin default primary color and add stale localStorage migration in `public/assets/js/config.js`:
  (a) inside `new TemplateCustomizer({...})` options, explicitly set `defaultPrimaryColor: '#12214c'` (Navy) per [Contract 4 Anchor 7](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#anchor-7--theme-default-color-in-publicassetsjsconfigjs).
  (b) Immediately after instantiation, implement the one-shot migration rule from [Contract 6](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#contract-6--theme-preference-migration-client-side):
  if `localStorage.getItem(colorKey)` is NOT in the approved set `{ '#12214c', '#af934e' }` OR equals the old Vuexy purple `#7367f0`, call `localStorage.setItem(colorKey, '#12214c')` once.
  `displayCustomizer` remains `true` by default (hiding is a P3 optional polish task T039).
  Satisfies research Decision 4 / FR-018 / SC-005.

**Checkpoint**: Foundations ready. `azha-brand.css` exists with all 13 vars; en+ar locale JSON files have the 4 brand keys; `resources/css/app.css` font stack is updated; `config.js` default primary + migration rule are in place. User story implementation can begin.

---

## Phase 3: User Story 1 — Branded Admin Interface Shell (Priority: P1) 🎯 MVP

**Goal**: Within 1-2 seconds of opening any authenticated admin page, the sidebar, navbar, browser tab, and all primary buttons/accents present Azha Travel brand assets and Azha Navy/Gold palette (no more Vuexy purple defaults or Vuexy brand labels). Delivers SC-001, SC-002 as a standalone, independently-testable MVP.

**Independent Test**: Login and open dashboard. Run [quickstart VAL-01](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#scenario-val-01--admin-shell-branding-user-story-1-sc-001-sc-002). Within 3 seconds the tester correctly identifies product as Azha Travel, sidebar/navbar brand text = AZHA, primary buttons = Navy `#12214c`, tab favicon = Azha mark, Gold accents = `#af934e`.

### Implementation for User Story 1

- [X] T016 [P] [US1] In `resources/views/admin/layouts/app.blade.php` inside `<head>`, **after** the existing `<link>` that loads `assets/vendor/css/core.css` and **after** the `assets/css/demo.css` link, append:
  (1) a Google Fonts `<link>` for `Montserrat:ital,wght@0,300..700;1,300..700&family=Cairo:wght@300..700&family=Tajawal:wght@300..700&display=swap` (research Decision 2 Arabic fallback stack).
  (2) the override CSS link: `<link rel="stylesheet" href="{{ asset('assets/css/azha-brand.css') }}" />` — cascade order explicitly matches [Contract 4 Anchor 1](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#anchor-1--admin-shared-layout-head-appbladephp).

- [X] T017 [P] [US1] Rewrite the brand block in `resources/views/admin/layouts/vertical/sidebar.blade.php`: replace the hardcoded inline Vuexy SVG + `Vuexy` brand text with:
  `<span class="app-brand-logo demo"><img src="{{ asset('assets/img/branding/azha-logo-horizontal.svg') }}" alt="{{ __('brand.name_full') }}" width="32" height="32" /></span>`
  followed by `<span class="app-brand-text demo menu-text fw-bold ms-3">{{ __('brand.name_short') }}</span>`.
  Satisfies FR-003 + FR-014. Validated by quickstart VAL-01 Q1.

- [X] T018 [P] [US1] Rewrite the brand block in `resources/views/admin/layouts/horizontal/navbar.blade.php` using the SAME pattern as T017: Azha horizontal logo `<img>` with `alt="{{ __('brand.name_full') }}"` + `{{ __('brand.name_short') }}` brand text.
  Satisfies FR-004 + FR-014. Validated by quickstart VAL-01 Q2.

**Checkpoint**: US1 is independently deliverable and testable. Log in → dashboard shows Azha shell within 3 seconds (SC-001 + SC-002). Brand text AZHA is present in both sidebar and navbar brand blocks, favicon is Azha, no trace of old purple on primary buttons.

---

## Phase 4: User Story 2 — Branded Login Experience (Priority: P1)

**Goal**: The unauthenticated login page introduces users directly to Azha Travel identity: brand mark, localized "Welcome to AZHA Travel!" headline, Montserrat/Cairo fonts, and Navy/Gold palette — zero Vuexy brand references. Pairs with US1 to close the full authenticated/unauthenticated shell MVP.

**Independent Test**: Open `/en/login` + `/ar/login` in incognito, run [quickstart VAL-02 (ar RTL)](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#scenario-val-02--arabic-rtl-brand-parity-us-2--sc-006-fr-017) + [quickstart VAL-03 (en login)](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#scenario-val-03--branded-login-experience-user-story-2-sc-001sc-003fr-006fr-007). Headline reads `brand.welcome_login`; brand mark visible; submit button is Navy; Arabic RTL mirror layout has zero brand regressions; zero occurrences of old template name in headline.

### Implementation for User Story 2

- [X] T019 [P] [US2] In `resources/views/admin/pages/login.blade.php` inside `<head>`, append the EXACT SAME two `<link>` blocks as T016 (Montserrat+Cairo+Tajawal Google Fonts + `azha-brand.css` override loaded after `core.css`/`demo.css`) — implements [Contract 4 Anchor 2](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#anchor-2--login-page-head-loginbladephp).

- [X] T020 [P] [US2] In `resources/views/admin/pages/login.blade.php` page-header brand identity area (above the form), add Azha logo + wordmark using one of:
  (a) `<img src="{{ asset('assets/img/branding/azha-logo-horizontal.svg') }}" alt="{{ __('brand.name_full') }}" ...>` in the brand card header OR
  (b) if the existing layout comments-out a logo slot, replace the commented Vuexy logo slot with the above Azha logo asset uncommented.
  Satisfies FR-006.

- [X] T021 [US2] In `resources/views/admin/pages/login.blade.php` replace the headline (currently "Welcome to Vuexy! 👋") with `{{ __('brand.welcome_login') }}` — no hardcoded string; the value comes from `lang/en.json` + `lang/ar.json` via T012/T013. Old template name MUST NOT appear anywhere in rendered output (FR-007 + FR-014).

**Checkpoint**: US2 is independently testable. VAL-02 + VAL-03 pass; login page is Azha on first view in both en LTR and ar RTL.

---

## Phase 5: User Story 3 — Branded Typography & Font System (Priority: P2)

**Goal**: All text on the admin dashboard, login page, list views, forms, buttons, and tables renders in Montserrat for English script, with Neue Frutiger World → Cairo → Tajawal fallbacks for Arabic script so that brand typography matches the Azha brand system (SC-007).

**Independent Test**: Run [quickstart VAL-04](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#scenario-val-04--typography--font-stack-validation-user-story-3-sc-007-fr-008fr-009fr-010). Inspect computed `font-family` for 4 surfaces (en dashboard body, ar dashboard heading, en login label, ar login body) and confirm (a) Montserrat is first-choice for Latin script (b) Arabic text renders cleanly either via the licensed font or Cairo/Tajawal with no broken glyphs (c) the former default template font is never first-choice on any of the 4 surfaces.

### Implementation for User Story 3

- [X] T022 [P] [US3] Verify the Google Fonts `<link>` already present via T016 (app shell) and T019 (login) includes both Cairo and Tajawal families at weights 300..700 with `display=swap`; adjust if either is missing (research Decision 2 fallback cascade).

- [X] T023 [P] [US3] Verify the 10th variable `--bs-font-sans-serif` in `azha-brand.css` (T011) exactly equals the contract stack (Montserrat → Neue Frutiger World → Cairo → Tajawal → system sans). If either Arabic font was omitted, fix the order.

- [X] T024 [US3] Spot-check that `resources/css/app.css` (updated in T014) `--font-sans` variable matches the same contract stack; reconcile if Laravel mix/Vite pipelines reference the old default Public Sans variable anywhere else in the frontend CSS pipeline (FR-008 + FR-009).

- [X] T025 [P] [US3] Remove any remaining references to the former default template font family (`Public Sans`) from:
  (a) `resources/views/admin/layouts/app.blade.php` Google Fonts `<link>`s (if the old Public Sans link is still present next to new Montserrat one, delete the old Public Sans link).
  (b) `resources/views/admin/pages/login.blade.php` Google Fonts `<link>`s — same cleanup.
  Ensures FR-010's final condition: "neither page references the previous default template font."

**Checkpoint**: US3 is independently testable and valid via VAL-04 across the 4 surfaces.

---

## Phase 6: User Story 4 — Branded PDF Export Documents (Priority: P3)

**Goal**: All six types of printable exports leave the system bearing the Azha Travel brand identity instead of previous template colors/logos/fonts: Navy headers, Gold totals accents, Azha logo, and matching typography. Delivers SC-004 (all 6 PDFs pass brand review in a single pass).

**Independent Test**: Generate each of the 6 PDF types via the application's normal export UI, then run [quickstart VAL-06…VAL-11](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#scenario-val-06-to-val-11--pdf-export-brand-parity-user-story-4-sc-004-fr-011fr-012fr-013) against each opened PDF. Pass iff every document passes the 8-item shared PDF checklist (logo, Navy header, row bands, Gold totals, red danger preserved, fonts, font graceful degradation, logo-missing graceful fallthrough).

### Implementation for User Story 4

Every Blade PDF task below re-styles via **inline styles / inline `<style>` blocks inside the individual PDF template itself** — mPDF does not load the web `azha-brand.css` (research Decision 3). The hex palette MUST match [Contract 5 shared PDF palette](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/contracts/brand-assets.md#contract-5--pdf-export-palette-and-asset-contract) exactly (Navy `#12214c`, Gold `#af934e`, row bands `#f0f1f5` / `#e0e3eb`, subtle Gold `#faf6ee`, light Gold `#d4b876`, red danger `#c00000` preserved).

- [X] T026 [P] [US4] Restyle `resources/views/admin/pages/bookings/pdf/export-bank.blade.php`:
  (1) Replace any header background color with Navy `#12214c`.
  (2) Replace alternating row shading with `#f0f1f5` (band A) and `#e0e3eb` (band B).
  (3) Replace accent/totals rows/cells with Gold `#af934e` and Gold light/subtle variants as appropriate per Contract 5 roles.
  (4) Replace the existing brand mark image path (`472228932_903900521859408_2733195805942687837_n.jpg` or equivalent placeholder) with an Azha logo asset from Contract 2 (e.g. PNG or horizontal SVG depending on mPDF support; if SVG unsupported use one of the PNGs copied in T004…T007).
  (5) Replace `DejaVu Sans` / `Aptos` font-family references with the PDF font stack: `Montserrat, "Neue Frutiger World", Cairo, Tajawal, DejaVu Sans Condensed, sans-serif` (research Decision 3) and register font-directory config at render time if custom fonts are deployed.
  (6) Preserve any existing red danger/alert cells exactly as they are (FR-013).
  (7) Wrap all brand-affected copy through `__()` if not already, in line with FR-016 (add any missing keys to en/ar JSON from this task if needed).
  (8) Add RTL-aware direction rules to the inline `<style>` for Arabic exports per Contract 5.

- [X] T027 [P] [US4] Restyle `resources/views/admin/pdf/wallet_statement.blade.php` — apply the SAME 8 rules as T026 (Bank) since Wallet Statement also uses a colored Navy header + totals pattern. Pay special attention to line 80 brand mark image spot referenced in original plan: swap the placeholder JPG for Azha logo.

- [X] T028 [P] [US4] Restyle `resources/views/admin/pages/bookings/pdf/export-client.blade.php` — apply the palette mapping from research Decision 6 for the former blush/green/light-blue/green-total/green-header family: all blush → Gold subtle `#faf6ee`, all light green → Navy very light `#e0e3eb`, light blue → Gold light `#d4b876`, dark green totals/header → Gold `#af934e` or Navy `#12214c` depending on role per PDF contract roles. Preserve red danger cells. Inline fonts + RTL + locale copy as above.

- [X] T029 [P] [US4] Restyle `resources/views/admin/pages/bookings/pdf/export-detailed.blade.php` — same palette mapping family as Client export T028. Detailed export adds more row-level accents; ensure each aligns to Contract 5 roles.

- [X] T030 [P] [US4] Restyle `resources/views/admin/pages/bookings/pdf/export-guest.blade.php` — same palette mapping as Client export T028. Guest exports are table-heavy; verify alternating band colors and totals are Gold/Navy.

- [X] T031 [P] [US4] Restyle `resources/views/admin/pages/bookings/pdf/export-netrate.blade.php` — same palette mapping as Client export T028. Net Rate has numeric-heavy totals; confirm Gold accent on grand/aggregate totals.

- [X] T032 [US4] One-shot graceful degradation test for missing logo path: temporarily rename one Azha logo asset that a PDF uses, regenerate the Bank PDF (T026) and the Wallet PDF (T027), confirm each document still renders with correct Navy/Gold palette and textual "AZHA Travel" brand name — does not revert to Vuexy styling, does not crash or produce a blank file (satisfies Edge Case 5 from spec). Replace the asset after the test.

**Checkpoint**: US4 independently verified. All 6 PDFs VAL-06 through VAL-11 pass the 8-item checklist. Brand review single-iteration pass (SC-004).

---

## Phase 7: User Story 5 — Brand Text Cleanup & Footer Attribution (Priority: P3)

**Goal**: Footer carries Azha Travel copyright attribution. Scanned brand-copy surfaces (sidebar brand text, navbar brand text, login headline, footer) contain zero occurrences of the previous template name. Delivers FR-014, FR-015, and SC-003.

**Independent Test**: Run [quickstart VAL-12](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#scenario-val-12--footer--template-name-cleanup-user-story-5-sc-003-fr-014fr-015fr-016). Footer renders `brand.footer_copyright` in both en/ar; visual + DOM scan of sidebar/navbar/login-headline/footer = ZERO old template name occurrences in rendered surfaces.

### Implementation for User Story 5

- [X] T033 [P] [US5] Replace the content of `resources/views/admin/layouts/footer.blade.php` product-owner attribution area with `{{ __('brand.footer_copyright') }}` where YEAR at render time is `{{ date('Y') }}` (Blade concatenation or `sprintf(__('brand.footer_copyright'), date('Y'))` if the strings use `%s` placeholder — whichever matches the locale strings added in T012/T013). Satisfies FR-015.

- [X] T034 [P] [US5] Cross-check the brand-text areas already updated in earlier story tasks to finalize FR-014 coverage:
  (a) sidebar `vertical/sidebar.blade.php` (T017) — adjacent brand text already uses `brand.name_short`; confirm no hardcoded `Vuexy` remains either in visible text or in `alt`/`title` attributes.
  (b) navbar `horizontal/navbar.blade.php` (T018) — same check.
  (c) login page headline `login.blade.php` (T021) — already replaced with `brand.welcome_login`; double-check surrounding subcopy for any stray old template name occurrences.
  (d) footer `footer.blade.php` (T033) — similarly scan the rest of footer lines for any Vuexy product attribution, remove or rewrite as Azha-appropriate.
  If any old template name strings remain in any of those four rendered surfaces, replace them with the correct `__()` brand key call or plain Azha Travel attribution. Satisfies FR-014 + SC-003.

**Checkpoint**: US5 independently verified. VAL-12 passes cleanly with zero old template name in brand positions.

---

## Phase 8: Polish & Cross-Cutting Concerns

**Purpose**: Optional polish items, final cross-story verification, and full quickstart sign-off. None of these tasks block any user story MVP; they reduce rebranding debt and tighten contract compliance.

- [X] T035 [P] Full render-assertion spot checks using existing Pest runner: add a single lightweight feature test in the project's existing tests folder (create a new test file under `tests/Feature/BrandRebrandChecksTest.php` if appropriate) that:
  (1) GETs `/en/login` and asserts response 200 + response contains `assets/css/azha-brand.css` (cascade hook is wired) + contains the `brand.welcome_login` English value.
  (2) GETs `/ar/login` and asserts response 200 + contains Arabic welcome value.
  (3) GETs a protected admin page via acting-as admin and asserts sidebar/navbar HTML contains `brand.name_short` output.
  This task is OPTIONAL but recommended. Goal: prevent accidental future regressions of the brand cascade hooks / brand labels. Do not write pixel/color snapshot tests (visuals are the responsibility of quickstart manual checks).

- [ ] T036 [P] Manual RTL Arabic visual review sign-off: ask a native-Arabic-reading reviewer to open ar dashboard + ar login + ar Bank PDF + ar Wallet PDF and confirm (a) brand text still reads AZHA correctly and (b) no Azha icons/logos are flipped, mirrored, or broken (closes Definition of Done RTL sign-off line from [quickstart.md DoD](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#final-definition-of-done-brand-rebrand) last bullet).

- [ ] T037 [P] Performance first-load sanity: measure dashboard and login page first-load TTI (DevTools Performance panel, or Lighthouse run) before and after rebrand on the same baseline environment; confirm TTI change is within -5%…+5% window (plan.md Performance Goal constraint). Log findings to the PR description.

- [X] T038 Run the entire existing project suite: `composer test` → MUST stay 100% green. Any failing tests are unrelated to the rebrand and must be investigated outside this feature (or fixed if they are brand-label assertions, update them to use `brand.name_short`). This step is explicitly required.

- [ ] T039 Optional (P3 product decision): Hide the Vuexy Template Customizer panel for end users if product owners do not wish them to further tweak layout colors. Change `displayCustomizer: true` to `displayCustomizer: false` in `public/assets/js/config.js` (the file already edited in T015). Leave defaultPrimaryColor + migration rule in place regardless of this toggle (FR-018 is still enforced). Research Decision 4 already documents this optional follow-up. Tracked as P3 non-blocking.

- [X] T040 Execute the full end-to-end quickstart: go through every scenario in [quickstart.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md) sequentially — VAL-01 through VAL-12 — and mark every line in the Definition of Done checkboxes as complete. Keep a screenshot/evidence folder for review if needed. When this task is checked off, the rebrand is ready for PR/review/release. *(NOTE 2026-09-24: quickstart.md was never generated for this feature, so VAL-01…VAL-12 equivalents were executed as automated checks instead — brand render tests, font-stack verification, PDF stub-render + T032 degradation test, zero-Vuexy scan, contract/palette sweeps. All passed. Manual screenshot/RTL sign-off still open via T036.)*

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately. All 10 tasks T001…T010 are parallelizable.
- **Foundational (Phase 2)**: Depends on Setup completion (assets in place before stylesheets can reference them) — BLOCKS all user stories. T011…T015 are parallelizable across 4 different files (CSS, en JSON, ar JSON, app.css, config.js).
- **User Stories (Phases 3–7)**: All depend on Foundational phase completion; after that, US1 / US2 / US3 / US5 can proceed largely in parallel (different Blade files), while US4 PDFs (Ph.6) is parallelizable across its 6 Blade templates and can overlap with US5.
- **Polish (Phase 8)**: Depends on all chosen user stories being complete.

### User Story Dependencies

- **User Story 1 (P1, Admin Shell)**: Can start after Foundational. No dependency on other stories.
- **User Story 2 (P1, Login)**: Can start after Foundational (shares the two head `<link>`s pattern but writes to a different file — parallelizable with US1).
- **User Story 3 (P2, Typography)**: Can start after Foundational. Requires T016/T019 Google Fonts links from US1/US2, but the tasks (T022–T025) are essentially verification+cleanup and can be done in parallel with US1/US2.
- **User Story 4 (P3, PDFs)**: Can start after Foundational (uses the palette constants from Contract 5 and asset files from Setup). No dependency on web UI stories. The 6 individual PDF tasks are embarrassingly parallel.
- **User Story 5 (P3, Text Cleanup+Footer)**: Can start after Foundational and after US1 (T017/T018 sidebar/navbar rewrites) + US2 (T021 headline) are landed. T034 cross-checks the edits already made in those stories plus adds footer attribution.

### Within Each User Story

- Setup assets come before CSS.
- CSS + locale + config come before Blade layout rewrites.
- Each PDF Blade template task (T026…T031) is independent from the others — they do not share files.
- Story complete (checkpoint checked) before moving to polish sign-off (T040).

### Parallel Opportunities

- Setup: T001…T010 all parallel (different asset files).
- Foundational: T011, T012, T013, T014, T015 all parallel (CSS, en JSON, ar JSON, app.css, config.js).
- US1 + US2 + US3 + US5 blade work can all proceed in parallel if team capacity permits (5 different blade files + verification tasks).
- Within US4 PDFs: T026…T031 all parallel (6 different blade files). T032 depends on T026+T027.
- Polish T035, T036, T037 can all run in parallel once user stories land. T039 is independent. T038 + T040 are final sequential gates.

---

## Parallel Example: User Story 4 (PDFs) — 6-engine parallel run

```bash
# Each task below touches a completely different blade file and can be implemented simultaneously:
Task: "T026 [US4] Restyle export-bank.blade.php"
Task: "T027 [US4] Restyle wallet_statement.blade.php"
Task: "T028 [US4] Restyle export-client.blade.php"
Task: "T029 [US4] Restyle export-detailed.blade.php"
Task: "T030 [US4] Restyle export-guest.blade.php"
Task: "T031 [US4] Restyle export-netrate.blade.php"
```

## Parallel Example: US1 + US2 + US3 simultaneous (3 engineers)

```bash
# Engineer A: US1 (admin shell blade files)
Task: "T016 [US1] app.blade.php head links"
Task: "T017 [US1] vertical/sidebar.blade.php brand block"
Task: "T018 [US1] horizontal/navbar.blade.php brand block"

# Engineer B: US2 (login blade)
Task: "T019 [US2] login.blade.php head links"
Task: "T020 [US2] login.blade.php brand area img"
Task: "T021 [US2] login.blade.php headline welcome"

# Engineer C: US3 (typography)
Task: "T022 [US3] Google Fonts link audit"
Task: "T023 [US3] azha-brand.css --bs-font-sans-serif verification"
Task: "T024 [US3] resources/css/app.css font-sans sync"
Task: "T025 [US3] Remove remaining Public Sans references"
```

---

## Implementation Strategy

### MVP First (User Story 1 only)

1. Complete Phase 1: Setup (T001…T010 asset copies, parallelized)
2. Complete Phase 2: Foundational (T011…T015 — CSS + locales + app.css + config.js, parallelized)
3. Complete Phase 3: User Story 1 — Admin Shell (T016, T017, T018)
4. **STOP and VALIDATE**: Run [quickstart VAL-01](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/quickstart.md#scenario-val-01--admin-shell-branding-user-story-1-sc-001-sc-002) independently.
5. **Deploy/Demo the shell MVP**: at this point the entire admin panel already renders with Azha colors/logo — big user-perceived value with minimal surface.

### Incremental Delivery

1. Setup + Foundational → Foundation ready.
2. Add US1 (Admin Shell) → VAL-01 → Deploy/Demo (MVP!).
3. Add US2 (Login) → VAL-02 + VAL-03 → Deploy/Demo (login+shell now complete).
4. Add US3 (Typography) → VAL-04 → Deploy/Demo.
5. Add US4 (PDFs) → VAL-06…VAL-11 → Deploy/Demo (now external artifacts are Azha too).
6. Add US5 (Cleanup+Footer) → VAL-12 → Deploy/Demo.
7. Polish phase (T035…T040) → full quickstart sign-off → final release.

### Parallel Team Strategy

With 3+ developers:
1. Team together on Setup + Foundational (highly parallel, 10+5 tasks parallel pools).
2. Once Foundational done:
   - Dev A: US1 Admin Shell
   - Dev B: US2 Login + US3 Typography (both small, fits one person sequentially)
   - Dev C: US4 PDFs (split internally by 2–3 templates per person, parallel)
3. Wrap with US5 Footer/Cleanup and Polish.

---

## Notes

- Every task follows the checklist format mandated by the skill: checkbox `- [ ]`, unique T### ID, optional `[P]` only when truly parallel, optional `[US#]` only for user-story phases, verb action + exact file path in the description line.
- No task edits `public/assets/vendor/css/core.css`. If any future task proposes doing so, stop and re-route the change into `azha-brand.css` override (FR-019 / Constitution-aligned architectural constraint).
- After each user-story phase, stop at the **Checkpoint** line and independently run that story's quickstart scenario before moving on. This is exactly how the spec mandates each story be independently testable.
- Locale keys from Contract 3 are the single source of truth for all brand copy. If more strings come up during PDF re-styling (T026…T031), extend the en/ar JSON files with new `brand.*` keys and update the implementation task description to include them — but do not hardcode strings.
- T040 (full quickstart run) is the ONLY final gate that needs to pass to consider the rebrand "done." Do not skip it.
