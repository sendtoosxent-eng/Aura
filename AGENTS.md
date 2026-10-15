# Repository Guidelines

## Project Structure & Module Organization

The application lives in `backend/`; run project commands from that directory. Laravel domain code is under `backend/app/`, with HTTP controllers and requests in `app/Http/`, models in `app/Models/`, and integrations in `app/Services/`. Routes are split between `routes/web.php` and `routes/api.php`. Blade templates, JavaScript, and CSS live in `resources/`; public images and compiled Vite assets are in `public/`. Database migrations, factories, and seeders are in `database/`. Pest tests are organized into `tests/Feature/` and `tests/Unit/`. General project notes belong in `docs/`.

## Build, Test, and Development Commands

From `backend/`, use:

- `composer run setup` — install PHP and JavaScript dependencies, create `.env`, generate the application key, migrate the database, and build assets.
- `composer run dev` — start the Laravel development services.
- `npm run dev` — run Vite in watch mode for frontend work.
- `npm run build` — create production frontend assets.
- `composer test` — clear cached configuration and run the complete test suite.
- `php artisan test --compact tests/Feature/RegistrationFlowTest.php` — run a focused test file.
- `vendor/bin/pint --dirty --format agent` — format changed PHP files.

## Coding Style & Naming Conventions

Follow PSR-4 and Laravel conventions. Use four spaces in PHP, explicit parameter and return types, braces for every control structure, and descriptive camelCase method and variable names. Classes use PascalCase; migrations and Blade files use descriptive snake_case or kebab-case names consistent with neighboring files. Reuse existing controllers, services, components, and factories before adding abstractions. Follow the more detailed rules in `backend/AGENTS.md` for all backend changes.

## Testing Guidelines

Tests use Pest 5 with Laravel's testing helpers and an in-memory SQLite database. Prefer feature tests for application behavior and unit tests for isolated logic. Name files after behavior or subject, ending in `Test.php`. Use model factories for test data. Run the narrowest relevant test while developing, then `composer test` before submitting. No numeric coverage threshold is configured; new and changed behavior should be covered.

## Commit & Pull Request Guidelines

Git history is unavailable in this workspace snapshot. Use concise, imperative commit subjects, optionally with a conventional prefix, such as `feat: add provider availability`. Keep commits focused. Pull requests should explain the change, note test commands run, link related issues, describe migrations or configuration changes, and include screenshots for visible UI updates. Never commit `.env`, credentials, uploaded patient documents, or runtime files from `storage/`.
