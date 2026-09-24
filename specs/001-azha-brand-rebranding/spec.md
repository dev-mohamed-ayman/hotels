# Feature Specification: Azha Brand Rebranding

**Feature Branch**: `[###-azha-brand-rebranding]`

**Created**: 2026-09-24

**Status**: Draft

**Input**: User description: "Full rebranding of the Azha Travel Hotels system to apply the official Azha Travel brand identity (colors, typography, logos, favicon, PDF exports, and brand text cleanup) across the admin panel, login page, and all customer-facing export documents, replacing the default Vuexy template branding."

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Branded Admin Interface Shell (Priority: P1)

A system admin opens the application and immediately recognizes the Azha Travel brand in the primary navigation shell before interacting with any feature. The sidebar, top navigation bar, and browser tab all display official Azha Travel logos, colors, and brand name instead of the previous default template branding.

**Why this priority**: This is the highest-visibility surface of the product. Users form their first impression within 1-2 seconds of opening the panel, and brand recognition is the primary business goal of the entire rebrand.

**Independent Test**: Can be fully tested by opening the admin dashboard in a fresh browser profile, observing the sidebar logo, navbar logo, brand text, tab favicon, and primary button/link colors — all must match the official Azha Travel brand assets without requiring any export, login flow, or booking interaction.

**Acceptance Scenarios**:

1. **Given** an authenticated admin on the dashboard, **When** they look at the left sidebar header, **Then** the Azha Travel logo and "AZHA" brand name are displayed in place of the previous template branding.
2. **Given** an authenticated admin on the dashboard, **When** they look at the top navigation bar, **Then** the Azha Travel logo and "AZHA" brand name are displayed in place of the previous template branding.
3. **Given** any page of the admin panel is loaded, **When** the user checks the browser tab, **Then** the tab icon (favicon) is the Azha Travel mark.
4. **Given** the admin panel is loaded, **When** the user inspects any primary action button, link, or accent element, **Then** its color matches the official Azha Navy Blue primary color palette.
5. **Given** the admin panel is loaded, **When** the user inspects any brand-accent element (highlights, selected states, brand badges), **Then** its color matches the official Azha Gold accent palette.

---

### User Story 2 - Branded Login Experience (Priority: P1)

A new or returning user lands on the login page. The page introduces them to Azha Travel through brand-consistent logo, colors, greeting text, and typography — there is no trace of the default template's welcome message or identity.

**Why this priority**: The login page is the first unauthenticated touchpoint. It must convey trust and brand identity before the user even enters credentials. Combined with the admin shell (Story 1), it delivers a complete and independently-demonstrable branded MVP.

**Independent Test**: Can be fully tested by logging out (or opening an incognito window) and navigating to the login URL. Verify logo, greeting copy, colors, fonts, and submit button styling all reflect Azha brand without needing any authenticated access to bookings, reports, or exports.

**Acceptance Scenarios**:

1. **Given** an unauthenticated user on the login page, **When** they view the page header area, **Then** the Azha Travel brand identity (logo or brand name) is visible.
2. **Given** an unauthenticated user on the login page, **When** they read the primary greeting headline, **Then** the text welcomes them to "AZHA Travel" rather than any third-party template name.
3. **Given** an unauthenticated user on the login page, **When** they view the login button and form accents, **Then** colors match the Azha Navy / Gold brand palette.
4. **Given** the login page is rendered in Arabic, **When** the user views text alignment and layout direction, **Then** the page correctly follows RTL conventions with the same brand assets and colors applied.

---

### User Story 3 - Branded Typography and Font System (Priority: P2)

An admin uses the system daily and reads dense reports, tables, and forms. All text throughout the admin panel and login page uses the official Azha Travel typeface family (Montserrat for English content, with a licensed Arabic companion font where available, falling back gracefully otherwise).

**Why this priority**: Typography substantially impacts perceived quality and readability. It is second-tier brand identity (after colors + logos) and pairs naturally with the completion of Stories 1 and 2.

**Independent Test**: Can be fully tested by opening any admin page (dashboard, login, or any list view) and inspecting the rendered font family on headings, body text, and table cells. No PDF, booking data, or third-party integration is required to validate the font stack is active.

**Acceptance Scenarios**:

1. **Given** any admin panel page is loaded, **When** English text is rendered, **Then** the Montserrat font family is applied as the primary sans-serif typeface.
2. **Given** any admin panel page is loaded, **When** Arabic text is rendered and the licensed Arabic font is available, **Then** the official Azha Arabic companion font is applied as the primary Arabic typeface.
3. **Given** the licensed Arabic companion font is not available in a deployment environment, **When** Arabic text is rendered, **Then** the system gracefully falls back to a widely-available open-source Arabic font without layout breakage or visual regressions.
4. **Given** any admin panel page (including login) is loaded, **When** the user inspects the declared font stack, **Then** neither page references the previous default template font.

---

### User Story 4 - Branded PDF Export Documents (Priority: P3)

A hotel manager or accounts team member generates one of the system's PDF exports (bank report, client statement, detailed booking report, guest list, net-rate report, or wallet statement). The resulting PDF visually matches the Azha Travel brand — correct logo, header/footer colors, table row striping, totals accents, and fonts — and remains fully legible and printable.

**Why this priority**: PDF exports leave the system and are shared directly with clients, banks, and partners. They are high-impact brand touchpoints but are consumed less frequently than the daily admin UI, so they follow the completion of Stories 1–3.

**Independent Test**: Can be fully tested by generating each of the six PDF types using seeded or real booking data, then visually inspecting each document for the Azha Travel logo, Navy/Gold color palette, branded fonts, and correct totals/row accents. No UI theme config or browser cache dependency exists — each PDF is a self-contained document.

**Acceptance Scenarios**:

1. **Given** a user generates any PDF export, **When** the PDF opens, **Then** the document header or footer displays the official Azha Travel logo in place of any previous branding placeholder.
2. **Given** a PDF export with a colored header (bank report or wallet statement), **When** the user inspects the header background color, **Then** it matches the Azha Navy Blue palette.
3. **Given** a PDF export with alternating row shading, **When** the user inspects the table rows, **Then** the alternating shades are Azha Navy subtle variants.
4. **Given** a PDF export with accent or total/highlight rows, **When** the user inspects those rows, **Then** the accent color matches the Azha Gold palette (or a defined close shade).
5. **Given** a PDF export, **When** the user inspects the body and heading typefaces, **Then** the fonts match the Azha Travel font family (or an acceptable, readable fallback for PDF rendering).
6. **Given** the existing red danger-style color used for negative/alert values in PDFs, **When** generating a report that contains such values, **Then** the red semantic color is preserved unchanged for clarity and recognizability.

---

### User Story 5 - Brand Text Cleanup and Footer Attribution (Priority: P3)

An admin uses the system extensively. Nowhere in the admin layout, login page, sidebar, navbar, or footer does the previous template brand name ("Vuexy") appear. The footer carries an Azha Travel copyright or attribution line appropriate to the application.

**Why this priority**: These are small, scattered cosmetic cleanups. They rarely block core brand recognition once Stories 1–4 are complete, but leaving template references erodes brand credibility over time.

**Independent Test**: Can be fully tested by performing a visual scan of the sidebar brand text, navbar brand text, login greeting, and footer, plus a full-text search for the old template name across rendered layout copy — independent of any bookings, PDF, or configuration work.

**Acceptance Scenarios**:

1. **Given** the admin sidebar or navbar, **When** the brand text next to the logo is visible, **Then** it reads as "AZHA" or "AZHA Travel" and does not contain the previous template name.
2. **Given** the login page headline, **When** the user reads the greeting, **Then** it says "Welcome to AZHA Travel!" and does not reference the previous template.
3. **Given** the admin footer, **When** the user reads the copyright or attribution line, **Then** it references Azha Travel as the product owner.

---

### Edge Cases

- What happens when a returning user has previously customized their theme via the template customizer and saved preferences in browser storage? The official Azha primary color MUST still apply as the default, overriding stale saved customizations so the rebrand is visible on first load.
- How does the system handle the case where the licensed Neue Frutiger World Arabic font cannot be shipped or deployed due to licensing constraints? A documented, readable open-source fallback font MUST be used without visual corruption or broken Arabic text rendering.
- How does the system render when viewing the admin on a device that has neither Montserrat nor the Arabic fallback font installed? The browser font stack MUST degrade gracefully to system sans-serif fonts, preserving brand colors and layout so the identity is still recognizable.
- How are PDF exports validated when mPDF's CSS support differs from a desktop browser? Every PDF export type MUST be visually compared against a brand reference sheet after generation, because PDF renderers handle colors, fonts, and backgrounds differently than web browsers.
- What happens when a PDF document does not include a logo image path that is valid on the server? The PDF MUST still render with the correct Azha Navy/Gold color system, textual brand name, and layout — it MUST NOT revert to a previous template's styling or produce a blank document.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST render the Azha Travel Navy Blue color as the primary color across all admin panel surfaces (buttons, links, selected states, focus rings, form accents, alerts).
- **FR-002**: System MUST render the Azha Travel Gold color as the designated brand-accent color across all admin panel surfaces where a secondary/highlight color is called for.
- **FR-003**: System MUST display the official Azha Travel logo mark in the sidebar header of the admin panel layout.
- **FR-004**: System MUST display the official Azha Travel logo mark (or horizontal wordmark, as appropriate) in the top navigation bar of the admin panel layout.
- **FR-005**: System MUST display the official Azha Travel favicon in every browser tab for the admin application, replacing the previous default favicon.
- **FR-006**: The login page MUST display Azha Travel identity (logo or wordmark) in the page header area.
- **FR-007**: The login page headline MUST display the localized message welcoming the user to AZHA Travel, and MUST NOT reference any third-party template name.
- **FR-008**: The admin panel MUST apply the Montserrat font family as the primary typeface for all Latin-script text (headings, body, tables, buttons, forms).
- **FR-009**: Where the official Azha-licensed Arabic companion font is available, the admin panel MUST apply it as the primary typeface for all Arabic-script text.
- **FR-010**: If the licensed Arabic font is not available, the system MUST automatically fall back to a readable open-source Arabic font without layout regressions.
- **FR-011**: Every PDF export document type (bank report, client statement, detailed booking report, guest list, net-rate report, wallet statement) MUST be styled with the Azha Navy Blue / Gold color system for headers, accents, and total rows.
- **FR-012**: Every PDF export document that includes a brand mark MUST use the official Azha Travel logo in place of any prior placeholder logo.
- **FR-013**: Semantic danger/alert red styling used for negative values, errors, and warnings in PDFs MUST be preserved (not rebranded away) for user recognition and clarity.
- **FR-014**: The previous default template brand name MUST NOT appear in rendered brand-text locations of the sidebar, navbar, login headline, or footer.
- **FR-015**: The admin panel footer MUST carry an Azha Travel copyright or attribution line identifying Azha Travel as the product owner.
- **FR-016**: All user-visible strings introduced or changed for this rebrand MUST use the application's existing localization helper (no hardcoded English or Arabic strings baked into templates).
- **FR-017**: The Arabic (RTL) mode of the application MUST render the same brand colors, logos, fonts, and copy in a correctly right-to-left oriented layout — no visual regressions or broken branding in RTL.
- **FR-018**: The system MUST apply the Azha Navy Blue primary color as the default color for users with stale theme customizer settings in browser storage, so the rebrand is visible on first load.
- **FR-019**: Brand color overrides MUST be applied in a way that survives future template vendor updates — the application MUST NOT rely on directly editing the vendor-provided core CSS bundle for brand identity values.

### Key Entities *(include if feature involves data)*

- **Brand Identity System**: The collective set of Azha Travel brand assets and rules — primary color (Navy Blue #12214c), accent color (Gold #af934e and lighter/darker variants), logo variants (light, dark, horizontal, icon-only), primary Latin font (Montserrat), primary Arabic font (Neue Frutiger World), and fallback rules. This entity has no persisted database state; it is represented entirely as static assets and configuration.
- **Admin Layout Shell**: The composed sidebar, top navbar, footer, and login page that frame all authenticated and unauthenticated system pages. This entity defines where brand assets and brand text are anchored so they appear consistently across every feature.
- **PDF Export Document**: Any of the six printable statements/reports produced by the system (bank, client, detailed, guest, net-rate, wallet). Each PDF Export Document is a self-contained deliverable that inherits Brand Identity System values (colors, fonts, logo) and carries them to external recipients.
- **Theme Preference**: A per-user stored preference (e.g., in browser local storage) that previously allowed users to customize the template color. This entity interacts with the rebrand because the new Azha default MUST take precedence over any old template-issue preference on first load after deployment.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A first-time visitor opening the admin panel correctly identifies the product as "Azha Travel" within 3 seconds of page load, based on visible logo, brand text, and colors alone.
- **SC-002**: 100% of sampled admin UI surfaces (sidebar, navbar, login, dashboard, forms, buttons, table accents, alerts, footer) use Azha Navy Blue as the primary color and Azha Gold as the accent color, with zero surfaces still displaying the previous template purple.
- **SC-003**: A full-text visual scan plus a rendered-template scan of the sidebar, navbar, login page, and footer finds zero occurrences of the previous default template brand name in user-visible copy or as brand label text next to logos.
- **SC-004**: A user opening each of the 6 PDF export types visually confirms each PDF uses the Azha Navy header / Gold accent palette and displays the Azha Travel logo; all 6 pass a single-iteration brand review pass with no further changes.
- **SC-005**: When an existing user who last used the system before the rebrand returns and loads the dashboard without clearing their browser storage, the Azha Navy Blue primary color is already active on first render — the user does not need to open a customizer panel to see the rebrand.
- **SC-006**: In Arabic RTL mode, every brand element (logo position, colors, fonts, welcome text, footer) renders correctly mirrored per RTL conventions, with zero brand-related visual regressions compared to LTR mode.
- **SC-007**: All text throughout the admin panel and login page renders using the Montserrat font family for English, with either the official Azha Arabic companion font or a single documented readable fallback active for Arabic.

## Assumptions

- The official Azha Travel brand assets (SVG/PNG logo variants in light and dark versions, horizontal and icon-only lockups, brand colors, favicon source, Montserrat font files, Arabic companion font files, and brand guidebook) are present under the project's brand-kit directory and may be used as the source of truth.
- The existing admin panel is built on the Vuexy template for Laravel; the rebrand will work within the template's extension points and will not replace the template wholesale.
- Brand identity overrides must survive a future vendor update to the Vuexy template, so direct edits to the vendor-maintained CSS bundle are avoided in favor of overlay-style overrides.
- The existing role-based access control, routing, booking workflows, wallet logic, and all business features are out of scope for this rebrand and remain functionally unchanged.
- For PDF documents that rely on `mPDF` for rendering, the selected fonts and colors MUST be tested against `mPDF`'s actual CSS support; the team assumes browser-identical rendering is not guaranteed and that small palette/fallback adjustments may be required specifically for PDFs.
- The licensed Arabic companion font (Neue Frutiger World) is assumed to be available for deployment on production servers. If licensing prevents shipping it, the team will use a predefined open-source Arabic fallback (e.g., Cairo or Tajawal) as the default drop-in replacement, documented in the implementation notes.
- Users with stale browser local-storage theme customizer settings from the pre-rebrand template are expected to receive the new Azha default on first load; any customizer-provided UX to further tweak colors is secondary to enforcing the Azha default.
- All user-facing copy introduced or modified by the rebrand will be managed through the existing English and Arabic localization files already used by the application.
