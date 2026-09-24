<!--
SYNC IMPACT REPORT
==================
Version change: N/A (initial formalization) → 1.0.0

Modified principles:
  - I. Laravel-First Architecture (formalized, no content change)
  - II. UI/UX Consistency (Azha Brand) (formalized, no content change)
  - III. Security & Permissions (formalized, no content change)
  - IV. Localization (i18n) (formalized, no content change)
  - V. Audit & Accountability (formalized, no content change)

Added sections:
  - Technology Stack Constraints (aligned with resolved template section naming)
  - Development Workflow & Quality Gates (aligned with resolved template section naming)
  - Governance (formalized with explicit amendment procedure)

Removed sections: None

Follow-up TODOs: None (all placeholders resolved)
-->

# Azha Travel Hotels Constitution

## Core Principles

### I. Laravel-First Architecture
- Follow standard Laravel conventions and directory structures.
- Use Eloquent ORM for database interactions.
- Utilize Blade templates for server-side rendering, integrated with the Vuexy UI theme.
- Favor Laravel's built-in features (Routing, Middleware, Form Requests, Policies) over custom implementations or third-party packages when possible.

### II. UI/UX Consistency (Azha Brand)
- The application UI must strictly adhere to the Azha Travel Brand Guidelines.
- All new views and components must use the customized Vuexy theme with Azha's color palette (Navy Blue `#12214c` and Gold `#af934e`) and fonts (Montserrat / Neue Frutiger World).
- PDF exports must maintain the defined structure and styling (mPDF).

### III. Security & Permissions
- Implement role-based access control (RBAC) using the `spatie/laravel-permission` package.
- All routes (except auth) must be protected by appropriate middleware.
- Never trust user input; always validate using Form Requests before processing.

### IV. Localization (i18n)
- The application must support multiple languages (primarily English and Arabic).
- Use `mcamara/laravel-localization` for routing and session state.
- Hardcoded strings are strictly forbidden in views and controllers; always use the `__()` helper or `@lang()` directive.
- Ensure RTL support for Arabic interfaces.

### V. Audit & Accountability
- Track all significant model changes (create, update, delete) using `spatie/laravel-activitylog`.
- Log entries must capture the authenticated user performing the action and the specific changes made.

## Technology Stack Constraints

- **PHP Version:** ^8.2
- **Framework:** Laravel ^12.0
- **Database:** MySQL / MariaDB (managed via Eloquent and Migrations)
- **Frontend Stack:** Blade, Tailwind CSS / Bootstrap (via Vuexy), JavaScript
- **Key Packages:**
  - `mpdf/mpdf`: PDF generation for exports and statements.
  - `spatie/laravel-permission`: Roles and permissions management.
  - `spatie/laravel-activitylog`: Activity tracking.
  - `livewire/livewire`: Dynamic frontend components where necessary.
  - `mcamara/laravel-localization`: Multi-language support.

## Development Workflow & Quality Gates

- **Migrations:** Database schema changes must be done via migrations. Never modify the database directly.
- **Code Style:** Follow PSR-12 coding standards for PHP.
- **Environment Variables:** Do not hardcode sensitive information or environment-specific configuration. Use `.env` and `config()` helpers.
- **Error Handling:** Avoid displaying raw exceptions to end-users in production. Implement proper error pages and logging.

## Governance

- This constitution supersedes all other informal practices.
- Amendments require a PR with justification and team approval.
- Code reviews must verify compliance with these core principles and constraints.

**Version**: 1.0.0 | **Ratified**: 2026-09-24 | **Last Amended**: 2026-09-24
