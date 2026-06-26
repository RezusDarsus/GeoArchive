# GeoArchive Project Report Notes

## 1. Project Overview

GeoArchive is a Georgian Historical Artifact and Event Management System built with Laravel. Its public side lets visitors browse artifacts, categories, and historical events. Authenticated users can maintain their profile, while administrators can manage all archive content. The project is fully server-rendered with Blade and intentionally uses straightforward code that can be explained during an exam defense.

The final technical version separates validation into Form Request classes (including login and registration), registers a dedicated policy for every managed model in addition to route middleware, centralizes transaction-safe image storage in a service, supports explicit image removal, enforces unique artifact and event titles in both validation and database indexes, and provides searchable and filterable public collections.

The seeded archive contains 22 artifacts, 37 chronological historical events, 7 categories, and 66 locally stored representative images. Every artifact and event includes a structured historical essay of at least 700 words, covering context, evidence, geography, interpretation, legacy, and research questions.

## 2. MVC Architecture in This Project

### Models

`User`, `Profile`, `Artifact`, `Category`, `Tag`, and `HistoricalEvent` represent database records and define Eloquent relationships. They are located in `app/Models`.

### Views

Blade files in `resources/views` render the interface. `layouts/app.blade.php` is the shared layout and contains the navigation, authentication-aware links, flash messages, and validation error list. Public and admin pages extend this layout.

### Controllers

Controllers are separated by audience and responsibility. `app/Http/Controllers/PublicArchive` contains read-only browsing controllers, while `app/Http/Controllers/Admin` contains protected resource controllers. Reusable search and chronology queries live in Eloquent model scopes. `ArtifactManager`, `CategoryManager`, and `HistoricalEventManager` coordinate transactions and image lifecycle operations, leaving controllers focused on HTTP authorization, requests, views, and redirects.

## 3. Database Tables

- `users`: default Laravel user data plus the `role` field (`admin` or `user`)
- `profiles`: biography and avatar for a user
- `categories`: artifact grouping, detailed historical description, and representative image
- `artifacts`: title, description, period, location, image, owner, and category
- `tags`: reusable artifact labels
- `artifact_tag`: pivot table joining artifacts and tags
- `historical_events`: title, description, period/date, location, and image
- Standard Laravel tables: password reset tokens, sessions, cache, jobs, job batches, and failed jobs

Foreign keys enforce valid records. Cascading deletion safely removes a user's profile/artifacts and an artifact's pivot rows. Category deletion is restricted at the database level and checked in the controller.

## 4. Relationships

### One-to-One: User and Profile

`User::profile()` uses `hasOne(Profile::class)`, while `Profile::user()` uses `belongsTo(User::class)`. Every seeded user has one profile, and a newly registered user receives a profile automatically.

### One-to-Many: Category/User and Artifacts

`Category::artifacts()` and `User::artifacts()` use `hasMany`. `Artifact::category()` and `Artifact::user()` use `belongsTo`. This lets Blade and controllers use readable expressions such as `$artifact->category->name` and `$artifact->user->name` without raw SQL.

### Many-to-Many: Artifacts and Tags

`Artifact::tags()` and `Tag::artifacts()` use `belongsToMany` through `artifact_tag`, including pivot timestamps. The artifact create/update methods call `sync()` to assign multiple selected tags.

## 5. CRUD Functionality

Administrators have complete Create, Read, Update, and Delete workflows for artifacts and historical events. Categories and tags can be created, listed, updated, and deleted, and both now have dedicated public detail pages that provide individual Read views with related artifacts. Artifacts support a category, any number of tags, and an uploaded image. Historical events and categories also support representative image uploads. Categories with artifacts cannot be deleted, preventing orphaned artifacts.

## 6. Authentication and Middleware

`AuthController` implements session-based registration, login, and logout using Laravel's authentication services. Passwords are hashed by the `User` model's `hashed` cast. Login regenerates the session to prevent session fixation, and logout invalidates it and regenerates the CSRF token.

Routes for profile editing use the `auth` middleware. The custom `AdminMiddleware` checks that a user is logged in and that `User::isAdmin()` returns true. Admin routes use both `auth` and `admin`; regular users receive HTTP 403. Admins are redirected to `/admin/dashboard` after login, and normal users are redirected home.

## 7. Validation

Dedicated Form Request classes validate and authorize every authentication, profile, and archive write before it reaches a controller. Required fields, maximum lengths, unique names and titles, existing foreign keys, arrays of distinct valid tag IDs, and image type, size, and dimensions are checked. Update validation uses `Rule::unique(...)->ignore(...)`. The shared layout displays all validation errors, and forms use `old()` so entered values survive validation failures.

## 8. File Uploads

Artifact images, category images, event images, and avatars use Laravel's `public` filesystem disk. `PublicImageStorage` and the manager services remove replaced/deleted files and clean up new uploads when a database transaction fails. The `php artisan storage:link` command exposes stored images to the web server. All seeded topics have local representative images, with sources listed in `IMAGE_CREDITS.md`.

## 9. Screenshots to Include in the Report

1. Home page showing the project title and latest records
2. Public artifact grid and category filter
3. Artifact detail with category, owner, tags, period, and location
4. Historical event grid and detail page
5. Login and registration pages
6. Admin dashboard with all five counters
7. Artifact creation form showing category, tags, image upload, and CSRF-enabled form
8. Artifact admin list with view/edit/delete actions
9. Category, tag, and historical event management pages
10. Profile page with bio and avatar upload
11. Validation error display
12. A 403 result when a regular user attempts an admin route
13. Database tables/relationships diagram or migration output
14. Passing `php artisan test` result

## 10. Conclusion

GeoArchive satisfies the course requirements with a coherent historical theme. It visibly demonstrates Laravel MVC, migrations, Eloquent ORM, all three required relationship types, Blade, CRUD, authentication, middleware, validation, CSRF protection, seeding, and file uploads. Its small controller methods, explicit relationships, conventional routes, and plain Blade/CSS interface keep it practical to demonstrate and explain.
