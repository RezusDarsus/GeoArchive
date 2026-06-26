# GEOARCHIVE: Laravel-Based Web Archive Management System

**Author:** Rezo Darsavelidze
**Institution:** Kutaisi International University (KIU)
**Course:** Web Development with PHP & Laravel
**Project type:** Final Project — CRUD-based web application
**Presentation:** 27 June 2026, 10:00, Auditorium 1_028
**Repository:** https://github.com/RezusDarsus/GeoArchive

> This report was written by analysing the actual GEOARCHIVE source code (routes, controllers, models, migrations, Blade views, requests, policies, middleware, API resources, and tests). Where a feature is **not** present in the code, the report says **TODO** instead of inventing it.

---

## Abstract

GEOARCHIVE is a web application built with the **Laravel** framework (PHP) that stores, manages, searches, and presents information about Georgian historical **artifacts** (objects, monuments, manuscripts) and historical **events**. It solves a simple but real problem: historical material is often kept in scattered, unstructured, hard-to-search places. GEOARCHIVE puts this material into one structured database with a clean web interface.

The application uses the **Model-View-Controller (MVC)** pattern, **Eloquent ORM** with database **migrations**, the **Blade** templating engine, **form validation**, **CSRF protection**, a custom **authentication** system, **middleware** for access control, **file uploads** to storage, and a small read-only **JSON API** built with Resource classes.

The main features that were implemented are: a public archive that anyone can browse (with search, category filter, tag filter, and chronological sorting); a registered-user profile area with an avatar upload; and a protected administrator area with full **Create, Read, Update, Delete (CRUD)** management of artifacts, categories, tags, and historical events. The data layer models all three required relationship types — **one-to-one**, **one-to-many**, and **two many-to-many** relationships.

The final result is a complete, runnable Laravel application with seeded demonstration data (22 artifacts, 37 events, 7 categories, 12 tags, 2 demo users, and 66 locally stored images) and an automated test suite of **33 tests (607 assertions)** that all pass.

---

## Chapter 1: Introduction

### 1.1 What GEOARCHIVE is
GEOARCHIVE is a server-rendered web application for cataloguing Georgian historical heritage. Each **artifact** record describes an object, building, or document (title, description, period, location, category, tags, owner, and image). Each **historical event** record describes a moment in Georgian history (title, description, date or period, sort year, location, and image). Artifacts and events can be linked together, so a visitor can move from an object to the events it belongs to and back.

### 1.2 Why digital archive systems are useful
Physical archives are difficult to browse, easy to damage, and almost impossible to search quickly. A digital archive keeps the information in a database, so it can be searched in milliseconds, displayed to many people at once, backed up, and updated without touching the original material.

### 1.3 Why web applications are good for archive management
A web application needs no installation on the visitor's computer — only a browser. It centralises the data on one server, supports many users at the same time, separates public viewing from protected editing, and can be reached from anywhere.

### 1.4 Motivation
The motivation for this project was to build one coherent application that demonstrates every core Laravel concept taught in the course — MVC, migrations, Eloquent relationships, CRUD, Blade, validation, authentication, middleware, file uploads, and a basic API — inside a meaningful subject (Georgian history) rather than empty placeholder data.

### 1.5 Objectives
- Build a working Laravel MVC application.
- Design a relational database using migrations.
- Implement one-to-one, one-to-many, and many-to-many Eloquent relationships.
- Provide full CRUD with forms and CSRF protection.
- Render the interface with Blade layouts, partials, and components.
- Add validation, authentication, and middleware-based access control.
- Support image uploads.
- Expose a basic JSON API.

### 1.6 Scope
The project covers: public browsing (artifacts, events, categories, tags, search, filter, sort), a user profile area, an administrator content-management area, and a read-only JSON API. It is a single-server, single-language (English UI) application.

### 1.7 Limitations
- The user interface is in **English only**. A Georgian/English bilingual interface is **TODO** (future work).
- The JSON API is **read-only** (GET). Write endpoints are **TODO**.
- There is **no map / geo-coordinate visualisation** yet (TODO).
- There is **no export to PDF/Excel** (TODO).
- Email verification scaffolding exists in the `User` model but is **disabled** (commented out).

---

## Chapter 2: Problem Statement

### 2.1 Problems of manual archive management
When historical records are kept on paper or in unsorted digital folders, they are easy to lose, hard to copy, and risky to share. Updating one fact may mean editing many copies.

### 2.2 Difficulty of searching physical or unorganised materials
Finding a single artifact in a physical archive can take hours. Without a structured index, searching by name, period, or location is slow and unreliable.

### 2.3 Need for structured storage
The solution is a database where every record has clearly defined fields (title, description, period, location, category, tags, image). Structured storage makes searching, filtering, and relating records possible.

### 2.4 Need for user-friendly CRUD operations
Archive staff must be able to add, view, edit, and delete records through simple forms, without writing SQL. GEOARCHIVE provides this through Laravel resource controllers and Blade forms.

### 2.5 Need for secure access and data validation
Not everyone should be able to change the archive. The system must separate public visitors from administrators, validate all input before saving it, and protect forms against attacks. GEOARCHIVE does this with authentication, middleware, policies, Form Request validation, and CSRF tokens.

---

## Chapter 3: Technology Review

All technologies below were confirmed in the project files.

| Technology | Where it is used in GEOARCHIVE |
|---|---|
| **PHP 8.2+** | Language of the whole application (developed/tested on PHP 8.4.22). |
| **Laravel 12** | Application framework (`composer.json`, `bootstrap/app.php`). |
| **MVC architecture** | Controllers in `app/Http/Controllers`, models in `app/Models`, views in `resources/views`. |
| **Blade templating** | All views; master layout `resources/views/layouts/app.blade.php`. |
| **Routing** | `routes/web.php` (web) and `routes/api.php` (API), wired in `bootstrap/app.php`. |
| **Controllers** | Public, Admin, Auth, Profile, and API controllers. |
| **Models** | `User`, `Profile`, `Category`, `Tag`, `Artifact`, `HistoricalEvent`. |
| **Migrations** | `database/migrations` — all tables, keys, and pivot tables. |
| **Eloquent ORM** | Relationships, casts, and query scopes inside the models. |
| **Middleware** | `AdminMiddleware` (alias `admin`) protects the admin area. |
| **Authentication** | Custom session login/register/logout in `AuthController`. |
| **Validation** | Form Request classes in `app/Http/Requests`. |
| **CSRF protection** | `@csrf` token in every state-changing Blade form (Laravel web middleware). |
| **File upload** | Images saved to the `public` storage disk via `PublicImageStorage`. |
| **Database** | **MySQL / MariaDB** (via XAMPP). The application database is `geoarchive`; the automated tests use a separate MySQL database `geoarchive_test`. |
| **API Resources** | `ArtifactResource`, `HistoricalEventResource` shape JSON output. |

> **Note on the database.** The `.env` and `.env.example` set `DB_CONNECTION=mysql` (database `geoarchive`, user `root`, empty password — the XAMPP default). The PHPUnit configuration (`phpunit.xml`) points the tests at a separate MySQL database, `geoarchive_test`, so running the test suite never touches the real data.

The interface uses **plain HTML and CSS** (`public/css/app.css`). No front-end framework (React, Vue, Tailwind, Bootstrap, Livewire) is used, so no Node.js build step is required.

---

## Chapter 4: System Requirements

### 4.1 Functional requirements (confirmed in code)
- **FR-1** A visitor can register a new account. *(`AuthController@register`)*
- **FR-2** A visitor can log in and log out. *(`AuthController@login`, `@logout`)*
- **FR-3** A registered user can edit their profile (biography) and upload/remove an avatar. *(`ProfileController`)*
- **FR-4** A visitor can browse a paginated list of artifacts. *(`PublicArchive\ArtifactController@index`)*
- **FR-5** A visitor can view a single artifact with its category, owner, period, location, tags, and connected events. *(`@show`)*
- **FR-6** A visitor can **search** artifacts and **filter** them by category and by tag, and the filters survive pagination. *(`Artifact` query scopes `search`, `inCategory`, `withTag`)*
- **FR-7** A visitor can browse historical events and sort them by date ascending or descending. *(`HistoricalEvent` scope `chronological`)*
- **FR-8** A visitor can view category pages and tag pages with their related artifacts.
- **FR-9** An administrator can perform full **CRUD** on artifacts, categories, tags, and events. *(four `Route::resource` controllers)*
- **FR-10** When creating/editing an artifact, the admin can choose a category, attach many **tags**, connect many **historical events**, set the owner, and upload an **image**.
- **FR-11** An administrator sees a dashboard with record counts. *(`AdminDashboardController`)*
- **FR-12** A non-administrator who opens an admin page receives HTTP **403**. *(`AdminMiddleware`)*
- **FR-13** A client can request archive data as **JSON** through the API. *(`Api\ArtifactController`, `Api\HistoricalEventController`)*

### 4.2 Non-functional requirements
- **Usability** — simple, consistent pages built from one Blade layout; clear navigation; flash messages after actions.
- **Security** — authentication, middleware, per-model policies, validation, CSRF tokens, and hashed passwords.
- **Maintainability** — clean MVC plus a service layer; small controllers; validation isolated in Form Requests.
- **Reliability** — write operations run inside database transactions; uploaded files are removed automatically if the related database write fails.
- **Database consistency** — foreign keys, unique constraints, and a restricted delete on categories that still contain artifacts.
- **Responsive interface** — plain CSS in `public/css/app.css`. *(Degree of responsiveness across devices: TODO — verify on mobile during the demo.)*

---

## Chapter 5: System Design

### 5.1 Overall architecture
GEOARCHIVE follows MVC and adds a thin **service layer** so controllers stay small and all writes happen in one place.

```
            ┌─────────────────────────────────────────────┐
            │                   Browser                    │
            └───────────────┬──────────────────────────────┘
                            │ HTTP request
                            v
                    routes/web.php  /  routes/api.php
                            │
                            v
                 Middleware (auth, admin, guest)
                            │
                            v
                       Controller
                            │  (delegates writes)
                            v
              Form Request  ──►  Service / Manager
              (validate +        (DB transaction +
               authorize)         file storage)
                            │
                            v
                     Eloquent Model
                            │
                            v
                        Database
                            │
                            v
                   Blade View  /  API Resource (JSON)
                            │
                            v
                        Response
```

### 5.2 MVC structure
- **Model** (`app/Models`) — holds relationships, casts, and reusable query scopes.
- **View** (`resources/views`) — Blade templates: one master layout, partials, and a component.
- **Controller** (`app/Http/Controllers`) — receives the request and returns a view or redirect; contains no SQL.

### 5.3 Request-response flow (simple form)
```
User submits form
      │
      v
Route  ──►  Controller  ──►  Form Request (validation)
      │                           │ fails → back with errors (old input kept)
      │                           │ passes
      v                           v
   Service/Manager  ──►  Eloquent  ──►  Database
      │
      v
Redirect with success message  ──►  Blade View
```

### 5.4 Main entities / models
`User`, `Profile`, `Category`, `Tag`, `Artifact`, `HistoricalEvent`.

### 5.5 Relationships between models (confirmed in code)
```
users ───1:1─── profiles
  │
  │ 1:N (owner)
  v
artifacts ───N:1─── categories
  │   │
  │   └────N:M──── tags                 (pivot: artifact_tag)
  │
  └────────N:M──── historical_events    (pivot: artifact_historical_event)
```

### 5.6 Routes, controllers, and views
Public routes map to `PublicArchive\*` controllers and the `home`, `history-paths`, and `history-graph` pages. Admin routes are grouped under `/admin` with `auth` + `admin` middleware and use resource controllers. Views are organised into folders that match the controllers (`artifacts/`, `events/`, `categories/`, `admin/…`, `auth/`, `profile/`).

---

## Chapter 6: Database Design

The schema is created entirely by migrations in `database/migrations`. Every table uses an auto-increment **primary key** (`id`) and, unless noted, Laravel **timestamps** (`created_at`, `updated_at`).

### 6.1 `users`
- **Purpose:** application accounts.
- **Important columns:** `name`, `email` (unique), `password` (stored **hashed**), `role` (added by a later migration, default `user`).
- **Relationships:** one-to-one with `profiles`; one-to-many with `artifacts`.

### 6.2 `profiles`
- **Purpose:** extra information for one user.
- **Columns:** `user_id` (**foreign key**, **unique**, cascade on delete), `bio` (nullable text), `avatar` (nullable string path).
- **Relationship:** one-to-one — `profiles.user_id` is unique, so each user has at most one profile.

### 6.3 `categories`
- **Purpose:** classify artifacts (e.g. Coin, Manuscript, Church).
- **Columns:** `name` (unique), `description` (nullable), `image` (nullable).
- **Relationship:** one-to-many with `artifacts`.

### 6.4 `tags`
- **Purpose:** flexible labels for artifacts (e.g. Medieval, Religion).
- **Columns:** `name` (unique).
- **Relationship:** many-to-many with `artifacts`.

### 6.5 `artifacts`
- **Purpose:** the core archive record.
- **Columns:** `title`, `description` (text), `period` (nullable), `location` (nullable), `image` (nullable), `user_id` (**FK**, cascade on delete), `category_id` (**FK**, **restrict on delete**).
- **Constraints/indexes:** `title` is **unique**; composite index on `(category_id, created_at)` and an index on `location` (added by `strengthen_archive_indexes`).
- **Relationships:** belongs to `User` and `Category`; many-to-many with `tags` and with `historical_events`.

### 6.6 `historical_events`
- **Purpose:** records of historical events.
- **Columns:** `title` (unique), `description` (text), `date_or_period` (nullable), `sort_year` (nullable integer, indexed — used to order the timeline), `location` (nullable), `image` (nullable).
- **Relationship:** many-to-many with `artifacts`.

### 6.7 `artifact_tag` (pivot)
- **Purpose:** join table for the artifact ↔ tag many-to-many.
- **Columns:** `artifact_id` (FK, cascade), `tag_id` (FK, cascade), timestamps, and a **unique** pair `(artifact_id, tag_id)`.

### 6.8 `artifact_historical_event` (pivot)
- **Purpose:** join table for the artifact ↔ historical event many-to-many.
- **Columns:** `artifact_id` (FK, cascade), `historical_event_id` (FK, cascade), with a **composite primary key** on both columns.

### 6.9 Relationship summary
- **One-to-one:** `User` ↔ `Profile`.
- **One-to-many:** `User` → `Artifact` (owner); `Category` → `Artifact`.
- **Many-to-many:** `Artifact` ↔ `Tag`; `Artifact` ↔ `HistoricalEvent`.

> The default Laravel `cache` and `jobs` tables also exist (framework tables) but are not part of the archive domain.

---

## Chapter 7: Implementation

### 7.1 Route organisation
Routes live in `routes/web.php` (interface) and `routes/api.php` (JSON). The web routes are grouped:
- **Public** routes: home, artifacts, events, categories, tags, history-paths, history-graph.
- **Guest** group (`guest` middleware): login and register pages.
- **Auth** group (`auth` middleware): logout and profile editing.
- **Admin** group (`auth` + `admin` middleware, prefix `/admin`): dashboard and four resource controllers.

### 7.2 Controllers
- **Public** (`PublicArchive\*`): `ArtifactController` and `HistoricalEventController` provide `index` (list, search, filter, sort, paginate) and `show` (detail). `CategoryController` and `TagController` show category/tag pages. `HistoryPathController` and `HistoryGraphController` are single-action (`__invoke`) controllers for the connected-history pages.
- **Admin** (`Admin\*`): four **resource controllers** (artifacts, categories, tags, events). Categories and tags use `except('show')`.
- **Auth/Profile:** `AuthController` and `ProfileController`.
- **API** (`Api\*`): `ArtifactController` and `HistoricalEventController` return JSON.

A controller action is intentionally short. Example (artifact creation):
```php
public function store(ArtifactRequest $request): RedirectResponse
{
    $this->authorize('create', Artifact::class);
    $this->artifacts->create($request->user(), $request->validated(), $request->file('image'));
    return redirect()->route('admin.artifacts.index')->with('success', 'Artifact created successfully.');
}
```

### 7.3 Model logic
Models declare relationships and **query scopes**. For example, `Artifact` has `scopeSearch`, `scopeInCategory`, and `scopeWithTag`, which keep search/filter logic out of the controllers:
```php
public function scopeSearch(Builder $query, ?string $term): Builder { /* title/description/location LIKE */ }
```
`HistoricalEvent` has a `chronological()` scope that orders by `sort_year`.

### 7.4 Service layer
Write operations are handled by manager services (`ArtifactManager`, `CategoryManager`, `HistoricalEventManager`). They wrap the database writes in a transaction and synchronise the many-to-many relationships. For artifacts, both tags **and** historical events are synced:
```php
$artifact = $owner->artifacts()->create($data);
$artifact->tags()->sync($tags);
$artifact->historicalEvents()->sync($events);
```

### 7.5 Blade templates
- `layouts/app.blade.php` is the master layout (`@yield('content')`, navigation, flash messages, validation-error block).
- Pages use `@extends('layouts.app')` and `@section('content')`.
- Reusable partials (`@include`) include `artifacts/_card.blade.php`, `events/_card.blade.php`, and the shared admin `_form.blade.php` files.
- A Blade **component** `<x-long-form>` renders the long historical text.
- Control structures used: `@foreach`, `@if`, `@isset`, `@checked`, `@selected`.

### 7.6 Form handling and validation
Each form posts to a resource route. Validation is done by **Form Request** classes that combine `authorize()` and `rules()`. Example from `ArtifactRequest`:
```php
'title'       => ['required','string','max:255', Rule::unique('artifacts')->ignore($artifact)],
'description' => ['required','string','min:10'],
'category_id' => ['required','exists:categories,id'],
'image'       => ['nullable','image','mimes:jpg,jpeg,png,webp,gif,bmp','max:2048','dimensions:max_width=6000,max_height=6000'],
'tags.*'      => ['integer','distinct','exists:tags,id'],
'events.*'    => ['integer','distinct','exists:historical_events,id'],
```
When validation fails, Laravel sends the user back with error messages, and forms reuse `old()` input so nothing is retyped.

### 7.7 CRUD operations (data flow)
**Create artifact (example):**
1. Admin opens the create form (`admin/artifacts/create`).
2. The form (with `@csrf`) posts to `store`.
3. `ArtifactRequest` validates and authorises (admin only).
4. `ArtifactManager` stores the image, opens a transaction, creates the row, and syncs tags + events.
5. The user is redirected to the list with a success message; the new artifact appears.

Read, Update, and Delete follow the same pattern through the resource controller's `index`/`show`, `edit`/`update`, and `destroy` methods.

### 7.8 Authentication and middleware
`AuthController` implements registration, login, and logout without an external package:
- **Register:** validates input, creates the user with `Hash::make(...)` and role `user`, creates an empty profile, logs in, and regenerates the session.
- **Login:** uses `Auth::attempt(...)`, regenerates the session, and redirects admins to the dashboard and others to the home page.
- **Logout:** logs out, invalidates the session, and regenerates the CSRF token.

`AdminMiddleware` (alias `admin`) redirects guests to login and aborts with **403** for authenticated non-admins. In addition, every managed model has a **Policy** (`ArtifactPolicy`, `CategoryPolicy`, `TagPolicy`, `HistoricalEventPolicy`) checked inside the controllers.

### 7.9 File upload logic
Images are uploaded through forms and saved to the `public` disk by `PublicImageStorage`. Its `storeSafely()` method is transaction-aware: if the database write after an upload throws, the new file is deleted, so no orphan files remain. When an image is replaced or its record deleted, the old file is removed; admins can also clear an image without uploading a new one. The public symlink is created with `php artisan storage:link`.

### 7.10 Database seeding
`DatabaseSeeder` creates demonstration data using `updateOrCreate`/`firstOrCreate`, so it can be run repeatedly without creating duplicates. It seeds 7 categories, 12 tags, 22 artifacts, 37 historical events, 2 demo users, and links artifacts to tags and events. Each record points to a locally stored image (66 image files total).

### 7.11 JSON API
`routes/api.php` defines four read-only endpoints. Controllers return **API Resources** that expose the relationships as readable arrays:
```php
// ArtifactResource
'category' => $this->whenLoaded('category', fn () => $this->category?->name),
'tags' => $this->whenLoaded('tags', fn () => $this->tags->pluck('name')),
'connected_events' => $this->whenLoaded('historicalEvents', fn () => $this->historicalEvents->pluck('title')),
```
The list endpoint supports `?q=` search and `?category=` filtering and returns paginated JSON with `data`, `links`, and `meta`.

### 7.12 Error handling
- Validation errors are returned to the form and shown by the layout.
- Missing records return **404** (route-model binding), including in the API.
- Non-admins on admin routes get **403**.
- Failed uploads are rolled back (orphan files removed).

---

## Chapter 8: Security

- **CSRF protection** — every state-changing Blade form includes `@csrf`; Laravel's web middleware rejects requests with a missing/invalid token.
- **Request validation** — all input passes through Form Request `rules()` before reaching the database (required fields, unique titles, min lengths, image type/size/dimensions, existence checks for related ids).
- **Authentication** — custom session-based login/register/logout; sessions are regenerated on login and invalidated on logout.
- **Middleware** — `AdminMiddleware` restricts the entire `/admin` area; the `guest` and `auth` groups protect the matching routes.
- **Authorization** — per-model policies and `authorize()` checks in admin controllers and Form Requests (admin-only writes).
- **Password hashing** — passwords are created with `Hash::make(...)` and the `User` model casts `password` to `hashed`, so they are never stored in plain text.
- **Protection against invalid input** — type, size, and existence rules reject malformed data; unique constraints exist both in validation and in the database.
- **File upload validation** — uploads are limited to listed image MIME types, a maximum of 2 MB, and maximum dimensions of 6000×6000 px.

**TODO:** rate limiting on the login form (brute-force protection) and email verification (scaffolding exists but is disabled) are not implemented.

---

## Chapter 9: Testing and Evaluation

### 9.1 Automated tests (present in the project)
The project includes an automated test suite run with `php artisan test`. **Result: 33 tests pass (607 assertions).** Tests run on a dedicated MySQL database, `geoarchive_test` (`phpunit.xml`), which is rebuilt for each run, so they never affect the real `geoarchive` data.

Test files and what they cover:
- `Feature/AuthenticationTest` — register, login, role-based redirect, logout.
- `Feature/AuthorizationTest` — guests redirected; regular users get 403; admins reach the dashboard.
- `Feature/AdminCrudTest` — CRUD for artifacts, categories, tags, events; image upload; safe category deletion.
- `Feature/PublicPagesTest` — public pages render; repeatable seeding; bidirectional connected history; every seeded image exists and is unique.
- `Feature/TechnicalQualityTest` — duplicate-title and unsafe-upload rejection; search/filter/sort; image replacement/removal; transaction-safe storage; DB constraints and image dimensions.
- `Feature/ApiTest` — JSON API list, single item, search, and 404 responses.
- `Unit/ModelRelationshipTest` — all relationship types are declared.
- `Unit/PolicyTest` — policies separate admins from regular users.
- `Unit/PublicImageStorageTest` — a failed operation removes the newly stored file.
- `Unit/LongFormDescriptionTest` — structured long-form text builder.

### 9.2 Manual testing plan
- **CRUD:** create, view, edit, and delete each entity as admin; confirm flash messages and list updates.
- **Validation:** submit empty/invalid forms; confirm error messages and that `old()` input is kept.
- **Authentication:** register, log in as user and as admin, log out.
- **Authorization:** open `/admin` as a guest (redirect to login) and as a regular user (403).
- **Search/filter/sort:** test artifact search, category/tag filters, and event chronological sorting.
- **File upload:** upload an image, replace it, remove it; confirm old files are deleted.
- **API:** request `/api/artifacts`, `/api/artifacts/{id}`, `/api/events`, `/api/events/{id}`; request a non-existent id for 404.

### 9.3 Evaluation
The automated suite plus the manual plan cover every functional requirement in Chapter 4. **TODO:** add browser-based UI tests (e.g. Laravel Dusk) for end-to-end coverage.

---

## Chapter 10: Results

### 10.1 What was successfully implemented
- Full MVC structure with a service layer.
- Migrations with foreign keys and two pivot tables.
- One-to-one, one-to-many, and two many-to-many relationships.
- Full admin CRUD with CSRF and validation for four entities.
- Public archive with search, category/tag filters, and chronological sorting.
- User profile area with avatar upload.
- Custom authentication, admin middleware, and per-model policies.
- Transaction-safe image uploads.
- Read-only JSON API with API Resources.
- Seeded demonstration data and a passing 33-test suite.

### 10.2 Main user flows
1. **Visitor:** open home → browse artifacts → search/filter → open an artifact → jump to a connected event.
2. **User:** register → edit profile → upload avatar.
3. **Admin:** log in → dashboard → create an artifact (category + tags + events + image) → edit → delete.
4. **API client:** `GET /api/artifacts` → `GET /api/artifacts/{id}`.

### 10.3 How the system satisfies requirements
Each functional requirement in Chapter 4 maps to a controller/route that was confirmed in code, and each is exercised by the test suite or the manual plan.

### 10.4 Screenshots
**TODO:** add screenshots of the home page, artifact list with filters, artifact detail, admin dashboard, artifact create/edit form, a validation error, and the 403 page.

### 10.5 Current limitations
English-only UI; read-only API; no map view; no PDF/Excel export (all listed as future work).

---

## Chapter 11: Conclusion and Future Work

### 11.1 Summary
GEOARCHIVE is a complete Laravel web application that stores and presents Georgian historical artifacts and events. It demonstrates MVC, migrations, Eloquent relationships (including two many-to-many relationships), full CRUD with CSRF and validation, Blade templating, authentication, middleware, file uploads, and a basic JSON API.

### 11.2 What was learned
How to structure a Laravel project cleanly (MVC + services), design a relational schema with migrations and pivot tables, validate and authorise input safely, manage file uploads reliably, and expose data both as HTML and as JSON.

### 11.3 Main achievements
A working, seeded, and tested application that covers every required course concept in one coherent project.

### 11.4 Future work
- Better search and filtering (e.g. full-text or combined filters).
- A richer admin dashboard with charts.
- Finer role-based permissions (beyond admin/user).
- Improved UI/UX and verified mobile responsiveness.
- Multilingual interface (Georgian/English).
- Map integration for locations.
- More advanced archive categories and tagging.
- Export to PDF/Excel.
- Browser-based automated UI tests.
- A write-enabled (POST/PUT/DELETE) API version.

---

## References

*Placeholders only — replace with proper citations before final submission.*
- [Citation needed] Laravel official documentation.
- [Citation needed] PHP documentation.
- [Citation needed] MVC architecture source.
- [Citation needed] Database design / relational modelling source.

---

## Appendix A: Installation Guide

### Requirements
- XAMPP for Windows (provides Apache, **MySQL/MariaDB**, PHP, and phpMyAdmin) — or PHP 8.2+ with the `pdo_mysql` extension and a MySQL server.
- Composer.
- Node.js / npm is **not required** (the stylesheet is plain CSS, no build step).

### Steps (MySQL via XAMPP)
```bash
# 1. In the XAMPP Control Panel, start MySQL (and Apache if you serve through it).
# 2. Create the databases (e.g. in phpMyAdmin or the MySQL client):
#      CREATE DATABASE geoarchive;
#      CREATE DATABASE geoarchive_test;   -- only needed to run the tests
composer install
cp .env.example .env          # Windows PowerShell: Copy-Item .env.example .env
php artisan key:generate      # .env already targets MySQL: db geoarchive, user root, empty password
php artisan migrate --seed
php artisan storage:link
php artisan serve             # open http://127.0.0.1:8000
```

### Running the tests
The tests use the separate `geoarchive_test` MySQL database (configured in `phpunit.xml`):
```bash
php artisan test
```

### Default demo accounts (created by the seeder)
| Role | Email | Password |
|---|---|---|
| Administrator | `admin@geoarchive.test` | `password` |
| Regular user | `user@geoarchive.test` | `password` |

---

## Appendix B: Main Routes

### Web routes (`routes/web.php`)
| Method | URI | Name | Purpose |
|---|---|---|---|
| GET | `/` | `home` | Home page |
| GET | `/artifacts` | `artifacts.index` | Artifact list (search/filter/sort) |
| GET | `/artifacts/{artifact}` | `artifacts.show` | Artifact detail |
| GET | `/events` | `events.index` | Event list |
| GET | `/events/{event}` | `events.show` | Event detail |
| GET | `/history-paths` | `history-paths.index` | Connected-history journeys |
| GET | `/history-graph` | `history-graph.index` | Interactive connected-history graph |
| GET | `/categories` | `categories.index` | Category list |
| GET | `/categories/{category}` | `categories.show` | Category detail |
| GET | `/tags/{tag}` | `tags.show` | Tag detail |
| GET/POST | `/login` | `login` | Login form / submit |
| GET/POST | `/register` | `register` | Register form / submit |
| POST | `/logout` | `logout` | Logout |
| GET | `/profile/edit` | `profile.edit` | Edit profile |
| PUT | `/profile` | `profile.update` | Save profile |
| — | `/admin/dashboard` | `admin.dashboard` | Admin dashboard (`auth`+`admin`) |
| — | `/admin/artifacts` | `admin.artifacts.*` | Artifact CRUD (resource) |
| — | `/admin/categories` | `admin.categories.*` | Category CRUD (resource, no show) |
| — | `/admin/events` | `admin.events.*` | Event CRUD (resource) |
| — | `/admin/tags` | `admin.tags.*` | Tag CRUD (resource, no show) |

### API routes (`routes/api.php`, prefix `/api`)
| Method | URI | Purpose |
|---|---|---|
| GET | `/api/artifacts` | Paginated artifact JSON (supports `?q=`, `?category=`) |
| GET | `/api/artifacts/{artifact}` | Single artifact JSON |
| GET | `/api/events` | Event JSON collection |
| GET | `/api/events/{event}` | Single event JSON |

---

## Appendix C: Main Files

- **Routes:** `routes/web.php`, `routes/api.php`, `bootstrap/app.php` (routing + middleware alias).
- **Controllers:** `app/Http/Controllers/PublicArchive/*`, `app/Http/Controllers/Admin/*`, `AuthController`, `ProfileController`, `HomeController`, `AdminDashboardController`, `app/Http/Controllers/Api/*`.
- **Models:** `app/Models/{User,Profile,Category,Tag,Artifact,HistoricalEvent}.php`.
- **Migrations:** `database/migrations/*` (profiles, categories, tags, artifacts, artifact_tag, historical_events, artifact_historical_event, add-role, strengthen-indexes).
- **Requests (validation):** `app/Http/Requests/*`.
- **Policies:** `app/Policies/*`.
- **Middleware:** `app/Http/Middleware/AdminMiddleware.php`.
- **Services:** `app/Services/{ArtifactManager,CategoryManager,HistoricalEventManager,PublicImageStorage}.php`.
- **API Resources:** `app/Http/Resources/{ArtifactResource,HistoricalEventResource}.php`.
- **Views:** `resources/views/layouts/app.blade.php`, `resources/views/artifacts/*`, `events/*`, `categories/*`, `admin/*`, `auth/*`, `profile/*`, `components/long-form.blade.php`.
- **Seeder:** `database/seeders/DatabaseSeeder.php`.
- **Tests:** `tests/Feature/*`, `tests/Unit/*`.
- **Styles:** `public/css/app.css`.

---

## Appendix D: Instructor Requirements & Rubric Mapping

### D.1 Submission and presentation (from the instructor)
- **Presentation:** 27 June 2026, 10:00, Auditorium 1_028. The live presentation is **mandatory** — absence means the project is **not graded (0 points)**.
- **LMS upload:** upload the project source code and working files, ideally as a ZIP. (Per the latest official announcement, **no written thesis/report is required**; this document is provided as optional supporting material.)
- **Deadline:** present and upload by **27 June 2026** (the LMS "1 July" date is only a technical buffer).
- **Final examination period:** 22.06.2026 – 11.07.2026.

### D.2 Required technologies (from the instructor)
PHP + Laravel only, demonstrating: MVC; Eloquent ORM, databases, migrations, and relationships (One-to-Many, Many-to-Many); CRUD with forms and CSRF; Blade (layouts, `@extends`, `@section`, `@include`, `@if`, `@foreach`); validation, authentication, and middleware; file uploads and API basics / Resource Controllers / JSON responses. Projects using unrequested technologies, or extra work beyond the requirements, are **not graded**.

### D.3 Rubric mapping (Total 40 points)
| Rubric block (10 pts each) | Where GEOARCHIVE demonstrates it |
|---|---|
| **MVC & Database Management** | MVC + service layer; migrations with FKs and two pivot tables; one-to-one, one-to-many, and two many-to-many Eloquent relationships. |
| **CRUD Operations** | Four resource controllers (artifacts, categories, tags, events); `@csrf` on every form; `old()` repopulation; transactional managers. |
| **Blade Templating & UI** | Master layout with `@extends`/`@section`; `@include` partials; `<x-long-form>` component; `@foreach`/`@if`; validation-error and flash-message display. |
| **Security, Validation & Advanced Features** | Form Request validation + authorization; custom authentication; `AdminMiddleware` (403); per-model policies; transaction-safe file uploads; and a read-only JSON API built with Resource controllers and API Resources. |

---

## Summary: Files Created and Remaining TODOs

### Files created by this report
- `docs/thesis.md` — this full report.
- `docs/thesis_outline.md` — the short outline / table of contents.

### TODO items (genuinely not in the project yet)
- **Screenshots** for Chapter 10 (Results).
- **Verify mobile responsiveness** of the plain-CSS interface (Chapter 4).
- **Multilingual (Georgian/English) interface** — future work.
- **Map / geo-coordinate integration** — future work.
- **PDF / Excel export** — future work.
- **Write (POST/PUT/DELETE) API endpoints** — only read endpoints exist.
- **Login rate limiting** and **email verification** — not implemented (scaffolding disabled).
- **Browser-based UI tests** (e.g. Dusk) — current tests are feature/unit level.
- **Real references** — replace the placeholder citations in the References section.
