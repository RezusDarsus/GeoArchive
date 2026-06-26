# GEOARCHIVE — Thesis Outline

**Author:** Rezo Darsavelidze
**Project:** GEOARCHIVE — Laravel-Based Web Archive Management System
**Course:** Web Development with PHP & Laravel (KIU)
**Presentation:** 27 June 2026, 10:00, Auditorium 1_028

This is the short outline. The full report is in [`thesis.md`](thesis.md).

| # | Section | What it covers |
|---|---------|----------------|
| — | Abstract | One-paragraph summary: problem, technologies, features, result. |
| 1 | Introduction | What GEOARCHIVE is, why digital archives matter, objectives, scope, limitations. |
| 2 | Problem Statement | Problems of manual archives; need for structured storage, CRUD, secure access. |
| 3 | Technology Review | PHP, Laravel 12, MVC, Blade, routing, controllers, models, migrations, Eloquent, middleware, auth, validation, CSRF, file upload, MySQL. |
| 4 | System Requirements | Functional + non-functional requirements taken from the real project. |
| 5 | System Design | Architecture, MVC, request flow, entities, relationships, routes/controllers/views. |
| 6 | Database Design | Each table, columns, keys, and the real relationships (1-1, 1-many, two many-to-many). |
| 7 | Implementation | Routes, controllers, models, Blade, forms, validation, CRUD, auth, uploads, seeding, API. |
| 8 | Security | CSRF, validation, authentication, middleware, password hashing, upload validation. |
| 9 | Testing & Evaluation | 33 automated tests (607 assertions) + manual test plan. |
| 10 | Results | What was built, main user flows, requirement satisfaction, limitations. |
| 11 | Conclusion & Future Work | Summary, lessons, achievements, future improvements. |
| — | References | Placeholders only. |
| A | Installation Guide | Requirements and step-by-step setup (MySQL). |
| B | Main Routes | Real web + API routes. |
| C | Main Files | Key controllers, models, migrations, views. |
| D | Instructor Requirements & Rubric | Official course requirements + project-to-rubric mapping. |

## Open TODO items (things not in the code yet)
- Screenshots for Chapter 10 (Results).
- Multilingual (Georgian/English) interface — future work.
- Map / geo-coordinate integration — future work.
- PDF / Excel export — future work.
- Write (POST/PUT/DELETE) API endpoints — only read endpoints exist today.
- Email verification — scaffolding is present but disabled.
