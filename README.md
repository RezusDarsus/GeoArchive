# GeoArchive

> Georgian Historical Artifact and Event Management System built as a complete Laravel CRUD final project.

GeoArchive is a traditional server-rendered Laravel application for preserving and exploring Georgian historical artifacts, monuments, manuscripts, and events. Visitors can browse the archive. Registered users can maintain their profile. Administrators receive a protected dashboard and complete content-management tools.

The code deliberately favors clear Laravel conventions over clever abstractions, making it suitable for a student presentation or exam defense.

## Project Status

- Laravel 12 application
- Complete and runnable
- 43 application routes
- 22 seeded artifacts, including expanded archaeological, architectural, and church collections
- 37 seeded historical events presented chronologically, from ancient Colchis through modern Georgia
- 7 categories and 12 tags
- 2 demonstration users
- 66 locally stored, individually sourced images covering every seeded category, artifact, and event
- Five clickable History Paths that connect artifacts, monuments, and events into guided stories
- A minimum of 700 words of structured historical interpretation for every artifact and event
- Automated coverage for authentication, policies, CRUD, uploads, Form Requests, relationships, search/filtering, public pages, and repeatable seeding
- No external API or internet connection required at runtime

## Course Concepts Demonstrated

| Requirement | Implementation |
| --- | --- |
| MVC | Public/admin controllers are separated; models own queries, manager services own writes, and Blade owns rendering |
| Migrations | Every project table and foreign key is defined in `database/migrations` |
| Eloquent ORM | Controllers and views use models and relationships instead of raw SQL |
| One-to-one | `User` has one `Profile` |
| One-to-many | `User` and `Category` each have many `Artifact` records |
| Many-to-many | Artifacts connect to tags and historical events through two database pivot tables |
| CRUD | Admin resource workflows cover artifacts, categories, tags, and events |
| Blade | Shared layout, directives, partials, forms, lists, tables, and pagination |
| Authentication | Custom session-based register, login, and logout flow |
| Authorization | `auth`/`admin` middleware and dedicated policies for every managed model protect operations |
| Validation | Dedicated Form Request classes authorize and validate archive writes |
| CSRF | Every state-changing Blade form contains `@csrf` |
| Uploads | Artifact, event, and avatar images use Laravel's public storage disk |

## Features

### Public archive

- Home page with the latest three artifacts and events
- Paginated artifact collection
- Artifact search plus category and tag filtering
- Artifact sorting with query-string-preserving pagination
- Artifact details with category, owner, period, location, and tags
- Searchable historical event collection with ascending or descending chronology
- Event details with date/period and location
- Connected-history links between event and artifact detail pages
- Five visual History Paths from Bronze Age Georgia through modern independence
- Public category directory with artifact counts
- Dedicated category and tag detail pages with paginated related artifacts
- Responsive interface written in plain HTML and CSS

### User account

- Registration with validation and forced regular-user role
- Secure login with session regeneration
- Logout with session invalidation and CSRF-token regeneration
- Profile biography update
- Avatar upload, replacement, and removal
- Role-aware navigation

### Administrator

- Dashboard counters for artifacts, categories, tags, events, and users
- Full artifact CRUD with category, multiple tags, owner, and image upload
- Category CRUD with deletion protection when artifacts exist
- Tag CRUD with automatic pivot cleanup
- Historical event CRUD with image upload
- Image cleanup when a file is replaced or its record is deleted
- Protected routes that return HTTP 403 for regular users
- Policy-level authorization for every managed archive model
- Separate `ArtifactPolicy`, `CategoryPolicy`, `TagPolicy`, and `HistoricalEventPolicy` classes
- Unique title constraints in both validation and the database
- Transaction-safe uploads and explicit image-removal controls

## Technology

- PHP 8.2+
- Laravel 12
- Eloquent ORM
- Blade templates
- MySQL/MariaDB through XAMPP by default
- Plain HTML and CSS
- PHPUnit through Laravel's test runner

The interface does not use React, Vue, Livewire, Inertia, Bootstrap, Tailwind, or an API-only architecture. Node.js and npm are not required because the final stylesheet is served directly from `public/css/app.css`.

## Quick Start

### Requirements

Install XAMPP for Windows, Composer, and these common PHP extensions. XAMPP includes Apache, MariaDB/MySQL, PHP, and phpMyAdmin in one local package.

- Ctype
- DOM and XML
- Fileinfo
- Mbstring
- OpenSSL
- PDO MySQL
- Tokenizer

### XAMPP / MySQL installation

1. Start **MySQL** in the XAMPP Control Panel.
2. Create a database named `geoarchive` in phpMyAdmin, or run:

```sql
CREATE DATABASE IF NOT EXISTS geoarchive
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

3. Confirm `.env` uses the default XAMPP database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=geoarchive
DB_USERNAME=root
DB_PASSWORD=
```

4. Install dependencies, migrate, seed, link storage, and run:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

On Windows PowerShell with XAMPP PHP, the same setup can be run as:

```powershell
Copy-Item .env.example .env
C:\xampp\php\php.exe artisan key:generate
C:\xampp\php\php.exe artisan migrate:fresh --seed
C:\xampp\php\php.exe artisan storage:link
C:\xampp\php\php.exe artisan serve
```

### Composer helper commands

The project also includes simplified Composer scripts:

```bash
composer run setup
php artisan storage:link
composer run dev
```

`composer run setup` installs PHP dependencies, prepares `.env`, generates the key, and runs migrations with seed data. `composer run dev` starts Laravel's local server. No frontend build command is needed.

## Demo Login Accounts

| Role | Email | Password | Login destination |
| --- | --- | --- | --- |
| Admin | `admin@geoarchive.test` | `password` | `/admin/dashboard` |
| User | `user@geoarchive.test` | `password` | `/` |

These credentials are intended only for local demonstration. Change them before any public deployment.

## Main Routes

### Public and account routes

| Method | URL | Purpose |
| --- | --- | --- |
| GET | `/` | Home page |
| GET | `/artifacts` | Artifact list and category filter |
| GET | `/artifacts/{artifact}` | Artifact details |
| GET | `/events` | Historical event list |
| GET | `/events/{event}` | Event details |
| GET | `/history-paths` | Clickable connected-history journeys |
| GET | `/categories` | Public categories |
| GET | `/categories/{category}` | Category details and related artifacts |
| GET | `/tags/{tag}` | Tag details and related artifacts |
| GET/POST | `/login` | Login form and authentication |
| GET/POST | `/register` | Registration form and account creation |
| POST | `/logout` | Logout |
| GET | `/profile/edit` | Edit the authenticated user's profile |
| PUT | `/profile` | Save the authenticated user's profile |

### Protected administrator routes

All URLs below require both `auth` and `admin` middleware.

| URL | Purpose |
| --- | --- |
| `/admin/dashboard` | Archive statistics and shortcuts |
| `/admin/artifacts` | Full artifact resource CRUD |
| `/admin/categories` | Category resource CRUD |
| `/admin/tags` | Tag resource CRUD |
| `/admin/events` | Full event resource CRUD |

Run `php artisan route:list --except-vendor` to inspect all 43 application routes and their controller actions.

## Database Design

```text
users 1 ----- 1 profiles
  |
  +-----< artifacts >----- 1 categories
               |
               +-----< artifact_tag >----- tags
               |
               +-----< artifact_historical_event >----- historical_events
```

### Eloquent relationships

```php
$user->profile;          // User hasOne Profile
$user->artifacts;        // User hasMany Artifacts
$category->artifacts;    // Category hasMany Artifacts
$artifact->user;         // Artifact belongsTo User
$artifact->category;     // Artifact belongsTo Category
$artifact->tags;         // Artifact belongsToMany Tags
$artifact->historicalEvents; // Artifact belongsToMany HistoricalEvents
$tag->artifacts;         // Tag belongsToMany Artifacts
$event->artifacts;       // HistoricalEvent belongsToMany Artifacts
```

Foreign keys provide referential integrity. User deletion cascades to profiles and artifacts. Artifact/tag pivot rows cascade when either side is deleted. Category deletion is restricted by the database and prevented with a friendly controller message when artifacts still exist.

## Seeded Georgian History Content

The repeatable database seeder provides a useful demo archive instead of placeholder lorem ipsum:

- Dmanisi hominin finds
- Bolnisi Sioni inscription
- Vani/Colchis coinage
- Trialeti gold work
- Jvari and Svan architecture
- Gelati manuscript culture
- Khakhuli Triptych
- *The Knight in the Panther's Skin* manuscript tradition
- Battle of Didgori and Georgia's Golden Age
- Colchis and the rise of the Kingdom of Iberia
- Christianization of the Kingdom of Iberia
- Vakhtang Gorgasali, the Lazic War, and the Principality of Iberia
- Arab rule, the Emirate of Tbilisi, and the Kingdom of Abkhazia
- Tao-Klarjeti and the restoration of Bagrationi kingship
- Unification under Bagrat III
- The Great Turkish Invasion
- David the Builder's accession, reforms, Ertsukhi, Didgori, and the liberation of Tbilisi
- Treaty of Georgievsk and Russian annexation
- First Democratic Republic
- 1991 independence referendum and restoration

Historical seed text was checked against reference summaries including [Colchis](https://en.wikipedia.org/wiki/Colchis), the [Kingdom of Iberia](https://en.wikipedia.org/wiki/Kingdom_of_Iberia_(antiquity)), [Christianization of Iberia](https://en.wikipedia.org/wiki/Christianization_of_Iberia), the [Lazic War](https://en.wikipedia.org/wiki/Lazic_War), [Arab rule in Georgia](https://en.wikipedia.org/wiki/Arab_rule_in_Georgia), the [Emirate of Tbilisi](https://en.wikipedia.org/wiki/Emirate_of_Tbilisi), the [Kingdom of Abkhazia](https://en.wikipedia.org/wiki/Kingdom_of_Abkhazia), [Bagrat III](https://en.wikipedia.org/wiki/Bagrat_III_of_Georgia), [David IV](https://en.wikipedia.org/wiki/David_IV_of_Georgia), [Battle of Didgori](https://en.wikipedia.org/wiki/Battle_of_Didgori), [Dmanisi hominins](https://en.wikipedia.org/wiki/Dmanisi_hominins), [Bolnisi inscriptions](https://en.wikipedia.org/wiki/Bolnisi_inscriptions), and the [1991 independence referendum](https://en.wikipedia.org/wiki/1991_Georgian_independence_referendum). These links are documentation references only; GeoArchive does not call external APIs.

The seeder uses `updateOrCreate` and `firstOrCreate`, so this command can safely be executed more than once without duplicating demonstration records:

```bash
php artisan db:seed
```

## Validation Rules

### Artifact

- Title: required, unique, maximum 255 characters
- Description: required, minimum 10 characters
- Period and location: optional, maximum 255 characters
- Category: required and must exist
- Tags: optional array; every selected tag must exist
- Image: JPEG, PNG, WebP, GIF, or BMP; maximum 2 MB and 6,000 × 6,000 pixels

### Category and tag

- Name: required, unique, maximum 255 characters
- Category description: optional
- Category image: approved image format, maximum 2 MB and 6,000 × 6,000 pixels
- Update rules ignore the current record when checking uniqueness

### Historical event

- Title: required and unique; description: required with at least 10 characters
- Date/period and location: optional, maximum 255 characters
- Image: approved image format, maximum 2 MB and 6,000 × 6,000 pixels

### Profile

- Biography: optional, maximum 1,000 characters
- Avatar: approved image format, maximum 2 MB and 4,000 × 4,000 pixels

Validation and request authorization live in `app/Http/Requests`. Errors are rendered by the shared Blade layout, and forms use `old()` so input is retained after failure.

Login and registration also use dedicated `LoginRequest` and `RegisterRequest` classes, keeping authentication validation outside the controller.

## File Uploads

Uploads use Laravel's `public` disk:

| Upload | Storage location |
| --- | --- |
| Artifact image | `storage/app/public/artifacts` |
| Category image | `storage/app/public/categories` |
| Event image | `storage/app/public/events` |
| Profile avatar | `storage/app/public/avatars` |

Run this once after installation:

```bash
php artisan storage:link
```

Blade renders stored images with `asset('storage/' . $path)`. Replaced files and files belonging to deleted artifacts, categories, or events are removed from disk. Users can explicitly remove an existing avatar.

`PublicImageStorage` centralizes storage and deletion. Its transaction-safe callback removes a newly uploaded file automatically if the associated database operation throws an exception. Admin edit forms can remove an existing image without requiring a replacement.

## Testing and Verification

Rebuild the complete database and run the test suite:

```bash
php artisan migrate:fresh --seed
php artisan test
```

The feature tests verify:

- Registration, login, role-specific redirect, and logout
- Regular-user denial on admin pages
- Administrator dashboard access
- Artifact CRUD, image storage, owner/category/tag relationships
- Category and tag CRUD
- Safe category deletion
- Historical event CRUD
- Profile updates
- Public home, collection, category, and detail pages
- Repeatable seeding without duplicate data
- Policy decisions for administrators and regular users
- Duplicate-title and unsafe-upload rejection
- Artifact/event search, filters, and chronological sorting
- Uniqueness of all 66 seeded image files
- Image replacement, explicit removal, and failed-transaction cleanup
- Database-level uniqueness and maximum image dimensions
- Preservation of search parameters across pagination

Useful additional checks:

```bash
php artisan view:cache
php artisan route:list --except-vendor
composer validate --no-check-publish
vendor/bin/pint --test
```

## Project Structure

```text
app/
├── Http/Controllers/Admin/ Protected administrator resource controllers
├── Http/Controllers/PublicArchive/ Public browsing controllers
├── Http/Requests/          Authorization-aware validation
├── Models/                 Relationships, casts, and reusable query scopes
├── Policies/               Dedicated model authorization
├── Services/               Transactional CRUD and image lifecycle managers
├── Http/Middleware/        Administrator role gate
└── Models/                 Eloquent models and relationships
database/
├── migrations/             Schema, foreign keys, and pivot table
└── seeders/                Accounts and Georgian demo content
public/css/app.css          Framework-free responsive styling
resources/views/            Shared layout, public pages, auth, profile, admin CRUD
routes/web.php              Public, authenticated, and protected route groups
tests/Feature/              Acceptance-focused application tests
```

## Exam Defense Walkthrough

1. Open `routes/web.php` and explain the public, `auth`, and `admin` groups.
2. Show `User`, `Profile`, `Artifact`, `Category`, and `Tag` relationships.
3. Open the migrations and identify the one-to-one, one-to-many, and pivot foreign keys.
4. Demonstrate public artifact browsing and category filtering.
5. Login as the regular user and show profile editing.
6. Attempt `/admin/dashboard` as the regular user to demonstrate the middleware's 403 result.
7. Login as admin and explain dashboard counts.
8. Create an artifact with a category, multiple tags, and an image.
9. Edit it and show validation, flash messages, and old-image cleanup.
10. Run `php artisan test` and explain what the feature tests protect.

More report-ready explanations are available in `PROJECT_REPORT_NOTES.md`.
Image provenance and reuse links are documented in `IMAGE_CREDITS.md`.

## Screenshot Checklist

Add these images to the final written report:

1. Home page
2. Artifact grid with category filter
3. Artifact detail page
4. Historical events and event detail
5. Login and registration pages
6. Regular-user profile form
7. Admin dashboard counters
8. Artifact create/edit form
9. Admin artifact, category, tag, and event tables
10. Validation errors and success messages
11. HTTP 403 page for a regular user
12. Passing test output

## Troubleshooting

### `php` or `composer` is not recognized

Install PHP 8.2+ and Composer, then restart the terminal. Confirm with `php -v` and `composer --version`.

### `could not find driver`

Use XAMPP's PHP executable or enable `pdo_mysql` in the active `php.ini`. Confirm with `php -m` and look for `pdo_mysql`.

### Images do not display

Run `php artisan storage:link` and confirm `APP_URL` matches the address used in the browser.

### Database/session table errors

Run `php artisan migrate:fresh --seed`. This rebuilds the local database and removes existing local records.

### Permission errors in `storage`

Ensure the PHP/web-server user can write to `storage` and `bootstrap/cache`.

## License

This educational project uses the MIT license inherited from the Laravel application template.
