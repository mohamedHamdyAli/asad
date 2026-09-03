# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

ASAD — a construction/real-estate project management platform. A "unit" is a construction project; it carries phases, timelines, reports, drawings, docs, galleries, live cameras, contractors, consultants, payments/installments/invoices, and issues. Owners (`user` role) track their unit from a mobile app; staff manage everything from an Inertia dashboard.

Laravel 11 + Inertia 2 + Vue 3 + Tailwind, Passport for the mobile API, `spatie/laravel-permission` for RBAC.

Longer-form docs live in [docs/](docs/) as PDFs (not readable by tools — for the human team): `DEVELOPER_GUIDE.pdf` (domain model, table reference, per-module deep dives, API reference, known bugs and tech debt) and `DEPLOYMENT.pdf` (server requirements, install steps, env vars, troubleshooting).

## Commands

```bash
composer install && npm install
php artisan migrate --seed          # seeders create languages, roles, permissions, settings, banners, intro
php artisan storage:link            # required: uploads live on the `public` disk
php artisan passport:install        # Passport::ignoreRoutes() is set — no oauth routes, but a personal access client is needed for createToken()
npm run dev                         # Vite dev server (page components are per-page code-split, see resources/views/app.blade.php)
php artisan serve

php artisan test                                    # all
php artisan test --filter=ProfileTest               # single test class
php artisan test tests/Feature/Auth/AuthenticationTest.php
./vendor/bin/pint --dirty                           # format changed files

php artisan installments:mark-overdue   # scheduled daily 00:05 (routes/console.php)
php artisan detect:lazy-loading         # scans models for N+1 risks
```

`.env` is not committed and there is no `.env.example`. Beyond the Laravel defaults the app needs: `FILESYSTEM_DISK=public` (see the FileService gotcha below), `PROJECT_ID` (Firebase project for FCM), `storage/firebase-asad.json` (FCM service account, gitignored), and mail credentials (OTP emails).

## Three surfaces, one codebase

1. **Inertia dashboard** — `routes/web.php` renders Vue pages from `resources/js/Pages/`. Almost every route is a closure returning `inertia('PageName')`; the page then fetches its own data over `/api/...` with axios. Route params are cast and passed as props (`['unitId' => (int) $unitId]`).
2. **Mobile/public REST API** — `routes/api.php` under `prefix('user')`, guarded by `auth('api')` (Passport). Controllers in `app/Http/Controllers/Api/`.
3. **Marketing site** — plain Blade views (`resources/views/about`, `portfolio`, `services`, `privacy`, …) served from `routes/web.php` closures.

`routes/api.php` also holds all the **dashboard CRUD endpoints** (`/api/units`, `/api/contractors`, `/api/unit-payments`, …). These are consumed by the Vue pages via same-origin axios + session cookie. Note: those admin groups carry **no auth or permission middleware at the route level** — some add `'middleware' => 'web'` for session access, most add nothing, and `FormRequest::authorize()` returns `true` everywhere. Access control is enforced on the *page* routes in `web.php` (`role:admin`, `role_or_permission:admin|project manager`, `banners.view`, …) and by `v-if="can(...)"` in the Vue components. When touching these endpoints, don't assume the request is authorized — and don't remove the page-level middleware thinking the API layer covers it.

## Request flow (dashboard CRUD)

Vue page → `resources/js/Services/<entity>.js` → `/api/<entity>/{create,update/{id},delete/{id},show/{id}}` → `App\Http\Controllers\Admin\*Controller` → `App\Http\Requests\Admin\*Request` (validation) → `App\services\<Domain>\*Service` (all business logic, wrapped in `DB::transaction`) → Eloquent.

Controllers are thin: constructor-inject the service, call it, return `response()->json(['status' => 'success', 'message' => ..., 'data' => ...])`. Services return `bool`/model and the controller maps `false` to a 404. Put logic in the service, not the controller.

The mobile API uses a different response shape — the global helpers in `app/Helpers/ApiResponse.php` (`successReturnData`, `failReturnMsg`, `returnSuccessMsg`, …) which emit `{key, data, msg, code}` with a **200 HTTP status carrying a `code` field**. `resources/js/app.js` installs an axios interceptor that rejects any response whose `data.code >= 400`, so both shapes surface as errors on the client. Both helper files are autoloaded via composer `files`, alongside `app/Helpers/Helpers.php`.

`app/Http/Requests/Api/BaseApiRequest` and most Admin requests override `failedValidation()` to call the global `failedValidation()` helper, which throws a 422 in the `{key, msg, code}` shape. Several requests branch their rules on the route name (`Route::is('units.store')`) so create is required-heavy and update is all-nullable.

## Frontend conventions

- `resources/js/Services/*.js` is the API client layer. Each module exports a `<Entity>Api` object plus `normalize()` (unwraps the several possible envelope shapes via `unwrapList`, JSON-parses translatable columns into `{en, ar}`, and prefixes image paths with `/storage/`) and `build<Entity>CreateFD` / `UpdateFD` FormData builders. Create endpoints often accept a **batch** (`data[0][title][en]`), update a single object (`data[title][en]`) — check the matching request class before changing shapes.
- All writes go over `POST` with `multipart/form-data`, including updates (`/update/{id}`), never `PUT`/`PATCH`.
- Permission gating in Vue is a locally redeclared helper in each page/component:

  ```js
  function can(p) { return role.value === 'admin' || userPermissions.value.includes(p) }
  ```

  fed by `page.props.auth.{user,role,permissions}` shared from `HandleInertiaRequests::share()`. There is no shared composable — follow the local pattern.
- Forms use `vee-validate` + `yup`; icons come from `@iconify/vue`; charts from `chart.js`/`vue-chartjs`. `@/` maps to `resources/js/` (see `jsconfig.json`). Ziggy's `route()` is globally available.
- `resources/js/composables/useServerError.js` is a module-level singleton (shared error state) rendered by `ServerErrorBanner.vue`.

## Translatable content

Two separate systems — don't confuse them.

**Per-row translations**: translatable columns (`name`, `description`, `title`, `company_address`, …) are stored as **JSON strings in a single column**. Services `json_encode(..., JSON_UNESCAPED_UNICODE)` on write; models expose `getXAttribute()` returning `json_decode($value)` (an object, not array). Because of that accessor, always use `$model->getRawOriginal('title')` when you need the stored value (all the update services do this). Read helpers: `getLocalizedValue()` (API — uses the `lang` request header) and `getLocalizedValueDashboard()` (panel — uses the `language` session key).

**UI labels**: JSON files in `resources/lang/` named `<code>[_panel|_app|_vendor|_web].json`, plus a merged `<code>.json`. Sources of truth for new keys are the arrays in `storage/app/{adminDashboardFile,userAppFile,vendorDashboardFile,webFile}.php`; `FileService::generateJsonLanguageFile($code)` regenerates the per-surface JSONs and the merged file for a new language. The `LanguageEditor` Vue page edits these files at runtime through `LanguageHelperFunctionService::updatelanguage()` — it only overwrites keys that already exist in the target file, then rewrites the merged `<code>.json`. Vite is configured to ignore `resources/lang/**` so those writes don't trigger reloads.

`SetLocale` (api) reads the `lang` header; `SetLocalePanel` (web) reads the `locale` session key and hand-loads the JSON into the translator via `app('translator')->setLoaded()`. Available languages come from the `languages` DB table (`Language::getLanguageCode()`), not config.

## Roles and permissions

Roles: `admin`, `user`, `vendor`, `guest`, `consultant` (`RoleSeeder`). Permissions are generated in `RolesAndPermissionsSeeder` as `<module>.{view,create,update,delete}` over a fixed module list, plus extras like `roles.sync_permissions`, `unit_payment_installments.update_status`, `unit_payment_invoices.confirm`. Adding a new managed module means adding it to that seeder's `$crudModules`, re-running the seeder, and gating the `web.php` page route plus the Vue `can()` calls.

`admin` bypasses permission checks in the frontend `can()` helper. The role-scoped auth helpers in `app/Helpers/Helpers.php` (`userAuth()`, `vendorAuth()`, `guestAuth()`, `consultantAuth()`, `userOrGuestAuth()`) return the Passport user only if they hold the matching role — API services use these to scope queries.

Mobile auth flow: register → OTP emailed (`OtpMail`, 5-minute expiry, `generateOtp()`) → `verify-otp` issues `'Bearer ' . $user->createToken("{role}:{id}")->accessToken`. `CheckUserAuthentication` also rejects users with `is_enabled = 0`.

## Files and uploads

`App\services\FileService` handles all uploads: `upload()` stores on the **`public` disk** and stamps a watermark (`public/staticImage/watermark/logo.png`) onto JPEG/PNG via GD, `replace()` = delete + upload, `delete()` is a no-op when the file is missing. Stored values are relative paths (`units/abc123.jpg`); the frontend prefixes `/storage/`.

**Gotcha**: `upload()` writes to the `public` disk explicitly, but `delete()` uses `Storage::disk(config('filesystems.default'))`. Unless `FILESYSTEM_DISK=public`, deletes and replacements silently leave orphaned files. `upload()` also returns `null` when the GD extension is missing.

## Payments

`UnitPayment` → `UnitPaymentInstallment` → `UnitPaymentInstallmentInvoice`, with an audit trail in `UnitPaymentLog` (`app/services/PaymentLogs/UnitPaymentLogService.php`). Status transitions are event-driven — `InvoiceUploaded`, `InvoiceStatusChanged`, `PaymentStatusChanged` wired to listeners in `app/Providers/EventServiceProvider.php` (auto-confirm on upload, update the installment, write logs). Prefer firing these events over mutating installment status directly.

## Notifications

`app/Trait/notifications/`: `FCMTrait` (raw cURL to FCM HTTP v1, service account from `storage/firebase-asad.json`), `NotificationTrait` (persists a `Notification` row and fans out to the user's `Device` rows), and `NotifiesUnitOwnerTrait::notifyUnitOwner($unit, $title, $bodyTemplate, $actor)` — use `{unit}` as the placeholder for the unit name; it skips the notification when the actor is the owner. Failures are logged, never thrown. Services (e.g. `UnitCrudService`) `use` these traits directly.

## Other notes

- `Model::preventLazyLoading()` is enabled outside production — a missing eager load throws rather than degrading silently.
- `Schema::defaultStringLength(191)` is set; new string columns inherit that.
- `.htaccess` at the repo root rewrites into `public/` (shared-hosting deploy), so the repo root is the document root in production.
- Tests are the untouched Breeze scaffolding (`tests/Feature/Auth`, `ProfileTest`, two `ExampleTest`s); `phpunit.xml` has the sqlite in-memory env vars commented out, so tests run against the configured database.
- `vite.config.js.timestamp-*.mjs` at the repo root is a stray Vite artifact, not source.
