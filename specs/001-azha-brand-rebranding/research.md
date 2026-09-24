# Research: Azha Brand Rebranding

**Created**: 2026-09-24 | **Feature**: [spec.md](file:///Users/mohamedayman/Herd/Azha%20Travel/hotels/specs/001-azha-brand-rebranding/spec.md)

This document records design decisions made during planning. Each decision was previously implicit in the rebranding source plan and user stories; they are codified here so that `/speckit-tasks` and the subsequent implementation phase never need to re-debate them.

---

## Decision 1 — Brand override strategy (FR-019)

**Decision**: Add a new override stylesheet at `public/assets/css/azha-brand.css` that is loaded **after** `public/assets/vendor/css/core.css` and after `demo.css` from the shared Blade layouts (`app.blade.php`, `login.blade.php`). Never edit `public/assets/vendor/css/core.css` directly. Branded color/typography values for both `:root` / `[data-bs-theme=light]` and `[data-bs-theme=dark]` live exclusively in the override file.

**Rationale**:
- Satisfies `FR-019` directly (survives Vuexy vendor updates).
- Reduces diff footprint: one new file plus two one-line `<link>` insertions, vs. ~30 scattered edits inside a 28k-line vendor CSS.
- Audit-friendly: `azha-brand.css` contains every single brand deviation in one place.

**Alternatives considered**:
- A: Edit `core.css` directly (rejected — violates FR-019, vendor updates overwrite the file, diff is unreadable).
- B: Publish SCSS variables and recompile a theme build pipeline (rejected — the project does not currently run a Sass pipeline for the Vuexy vendor bundle, and adding one is out of scope; it would also introduce a new build step not needed elsewhere).
- C: Inline `<style>` tags in every Blade layout (rejected — duplication across layouts, no way to share CSS variables for dark/light, harder to cache).

---

## Decision 2 — Arabic font fallback when Neue Frutiger World is unavailable (FR-009 / FR-010)

**Decision**: Canonical declared Arabic font stack in `--bs-font-sans-serif`:
1. `"Neue Frutiger World"` (primary, if license allows shipping the `@font-face` files).
2. `"Cairo"` (first open-source fallback, served via Google Fonts as Arabic companion to the Montserrat Latin link and available on-device in many environments).
3. `"Tajawal"` (second open-source fallback, also Google Fonts Arabic).
4. Standard system sans-serif fallbacks (`-apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif`).
In production environments where the licensed font file cannot be deployed, omit the `@font-face` block for Neue Frutiger World from `azha-brand.css` (commented or conditional) but leave the stack so Cairo is selected automatically.

**Rationale**:
- `FR-010` explicitly requires a documented, readable open-source fallback.
- Cairo and Tajawal are the two most widely-deployed open-source Naskh-style Arabic fonts on Google Fonts. Pairing them matches the Montserrat modern-sans style better than older options (e.g., Amiri is Naskh but too literary/serif for an admin panel).
- Stack order mirrors FR-009 ("use licensed font when available").

**Alternatives considered**:
- A: Only include Neue Frutiger World and fall back to system fonts (rejected — violates `FR-010`, produces broken or inconsistent Arabic on most clients).
- B: Default to Tajawal only (rejected — Cairo is more widely installed on mobile OS browsers; keeping both gives higher first-hit match rate).
- C: Use `Noto Sans Arabic` (rejected — perfectly valid, but Cairo is lighter weight and matches the visual weight of Montserrat better in admin tables; we document the fallback order and Cairo is the first open-source choice in the plan).

---

## Decision 3 — mPDF font strategy for PDF exports (SC-004 / FR-011 / FR-012)

**Decision**: In every PDF Blade template, replace the current `DejaVu Sans` / `Aptos` font families with a two-part PDF font stack that renders reliably inside `mpdf/mpdf` 8.2:
1. English (Latin-script) body/headings: `Montserrat` registered with mPDF via the standard `@font-face` + font directory configuration (or the documented `default_font` / `fontdata` mechanism), with fallback to mPDF's built-in `DejaVu Sans Condensed` if the custom font directory is unavailable.
2. Arabic text: Register the same open-source fallback as Decision 2 (either `Cairo` or `Tajawal`, whichever TTF/OFL file we ship with the PDFs' resources) with mPDF so RTL ligatures render correctly.
3. If neither custom font is registered, mPDF falls back to `DejaVu Sans` which has partial Arabic glyphs — unacceptable for production, so the implementation checklist explicitly registers at least one Arabic-capable font.
4. Colors for PDF headers, alternating rows, and accent rows MUST use the exact palette documented in the contract (Decision 6 below) and the same palette is codified inline in each PDF template. No PDF pulls colors from the web `azha-brand.css` (mPDF does not load external CSS).

**Rationale**:
- `mPDF` does not render the same CSS as a browser (per Edge Case #4 in the spec); inline `style` attributes + inline `<style>` blocks in each PDF blade are the only reliable path.
- Font registration in mPDF is explicit; relying on default PDF fonts produces square boxes for Arabic unless the font is registered.
- SC-004 requires all 6 PDF exports to pass a single brand-review iteration, so font + color rules must be consistent across all six files.

**Alternatives considered**:
- A: Share a single `pdf-brand.css` file and `@import` it across all six PDF templates (rejected — mPDF stylesheet resolution with Laravel Blade `public_path()` assets is brittle; inlining the palette into each of the 6 templates is more reliable than one shared file that silently fails to load in some mPDF configurations. We DO share a documented palette contract, which is the real reuse unit).
- B: Keep DejaVu Sans default for Latin text, only change colors (rejected — `SC-007` requires brand font family alignment across surfaces; PDFs leaving the system for clients/banks look visually mismatched with admin UI otherwise).

---

## Decision 4 — Enforcing Azha default for returning users with stale TemplateCustomizer localStorage (FR-018 / SC-005)

**Decision**: In `public/assets/js/config.js`, explicitly set `defaultPrimaryColor: '#12214c'` on the `TemplateCustomizer` constructor options. Additionally, on first invocation of `TemplateCustomizer` (immediately after instantiation), compare the stored color key (`templateCustomizer-{templateName}--PrimaryColor`) in localStorage. If the stored value equals the old Vuexy default purple (`#7367f0`) or any color outside the explicit Azha-approved set `{ '#12214c', '#af934e' }`, overwrite the persisted value with `#12214c` once, so the customizer itself reports the new Navy default after migration, not the stale purple. `displayCustomizer` remains `true` by default; teams that wish to hide it can toggle it in a single-line change, which is tracked in the implementation checklist as a P3 optional follow-up (never blocking FR-018).

**Rationale**:
- `FR-018` and `SC-005` require the rebrand to be visible on FIRST load for returning users, without requiring them to clear cache or open the customizer panel.
- Pinned `defaultPrimaryColor` alone only affects users who have NO saved value — returning users with old purple would still see purple on first render (the customizer reads localStorage and applies it). An explicit one-shot migration handles that case.
- Keeping `displayCustomizer: true` leaves product admins free to explore other template tweaks (layout density) without changing brand colors; the approved-color guard above ensures saved customizations that would break brand colors are reset.

**Alternatives considered**:
- A: Only set `defaultPrimaryColor` (no migration) and document that users must clear storage (rejected — fails `SC-005` on the returning-user acceptance scenario).
- B: Hide `displayCustomizer` entirely (rejected — removes legitimate admin layout flexibility; hiding is allowed as optional but enforcing the Azha default programmatically is a better MVP solution because it works for BOTH cases).
- C: Server-side inject a color reset on every response (rejected — requires session/cookie coupling, unnecessary for what is purely client-local storage behavior).

---

## Decision 5 — Favicon production path (FR-005)

**Decision**: Replace `public/assets/img/favicon/favicon.ico` with a new multi-size `.ico` containing 16x16, 32x32, and 48x48 variants of the Azha Gold mark icon. The single `.ico` replacement satisfies browsers, bookmarks, and pinned-tabs. Optionally (P3 nice-to-have, not blocking FR-005), also drop modern `apple-touch-icon.png` / `site.webmanifest` PNG variants into the same favicon directory and reference them from Blade layout `<head>`; these are tracked as optional tasks because FR-005 only requires the tab icon to display the Azha mark. The favicon source image is the standalone Azha icon mark from `Azha Travel Brand kit/1-Logo/SVG/` (the same mark used for `azha-logo.svg`), rendered against a transparent background.

**Rationale**:
- A single multi-size `.ico` at the existing path is the lowest-touch fix that immediately satisfies FR-005 across all browser engines.
- No `<link>` tag changes are required for the ICO path (existing layouts either rely on browser auto-discovery or already reference the favicon directory in their `<head>`).
- Keeps the change footprint minimal — exactly the same pattern used for replacing branding PNGs.

**Alternatives considered**:
- A: Replace ICO + add `<link rel="icon" type="image/svg+xml" href="...svg">` (rejected — SVG favicon support is uneven in older desktop browsers still in the admin target matrix; ICO guarantees consistent behavior).
- B: Move favicon assets to the project root `/favicon.ico` instead of `/assets/img/favicon/favicon.ico` (rejected — requires additional `<link>` edits across layouts for no user benefit; the existing Vuexy structure already points at the `assets` directory).

---

## Decision 6 — Color-safe PDF palette shared contract (FR-011 + FR-013)

**Decision**: Canonical Azha palette used in every PDF template — same hex values are used across all six templates and documented in the contract file. Colors outside this list MUST NOT appear as header/accent/totals colors in any exported PDF. Web UI accent/alert semantic colors (success green, warning amber, danger red) are preserved unchanged for non-brand alerts/indicators; red danger rows in PDFs are preserved per FR-013.

Palette (PDF + web UI brand uses the SAME hex values; only row opacity varies):

| Role (in PDFs / UI overrides) | Hex | Notes |
|-------------------------------|-----|-------|
| Primary / header background   | `#12214c` | Azha Navy Blue — unchanged |
| Primary (RGB for alpha)       | `18, 33, 76` | Used in `rgba()` for subtle variants |
| Primary subtle (row band 1)   | `#f0f1f5` | Navy subtle, high contrast for readability |
| Primary lighter (row band 2)  | `#e0e3eb` | Alternating row, Navy lighter |
| Gold accent / totals / badges | `#af934e` | Azha Gold — unchanged |
| Gold light / header sub-row   | `#d4b876` | Lighter Gold, where full Gold is too heavy |
| Gold subtle BG / soft accent  | `#faf6ee` | Gold 10% used for light card backgrounds / emphasis rows |
| Danger / alert red (SEMANTIC, PRESERVED) | `#c00000` / current danger hex used in existing PDFs | FR-013 — NEVER rebrand to Navy/Gold |

**Rationale**:
- The source rebranding plan's PDF mapping maps several old colors (blush, old green headers, green totals, light blue) to Azha equivalents. Without a single canonical list, each PDF engineer picks slightly different shades.
- FR-011 explicitly requires all 6 PDF export types to share one Navy/Gold system.
- FR-013 explicitly carves out red danger colors as non-brand.

**Alternatives considered**:
- A: Pick row shades individually per PDF (rejected — causes visible inconsistency when the same user opens multiple PDFs back-to-back for a single booking).
- B: Use Gold for headers and Navy for accents (swap) (rejected — Gold is the weaker contrast for large header backgrounds; Navy is the stronger reading background for text headers; Gold is strictly for high-signal small elements like totals/badges).
