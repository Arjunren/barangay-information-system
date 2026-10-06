# Barangay Information System

## Overview

A compact Laravel API for barangay households, resident profiles, certificate requests, incident reports, activity history, and operational summaries.

## Features

- Sanctum token registration, login, logout, and active-account enforcement
- Admin, staff, and resident roles with middleware, policies, and ownership checks
- Household and resident CRUD with search, filters, pagination, validation, and audit fields
- Resident-owned certificate requests with locked approval/rejection/release transitions
- Scoped incident reporting and staff status management
- Dashboard totals, activity logs, seed data, MySQL migrations, tests, Docker, and CI

## Technology Stack

PHP 8.5, Laravel 13, Laravel Sanctum, Eloquent ORM, MySQL 8.4, SQLite tests, PHPUnit 12, and Docker.

## Requirements

PHP 8.3+ with SQLite/MySQL extensions and Composer 2, or Docker with Compose.

## Installation

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

## Environment Variables

Configure `APP_KEY`, `APP_ENV`, `APP_DEBUG`, `APP_URL`, `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, and `CORS_ALLOWED_ORIGINS`. Never commit `.env`.

## Database Setup

```powershell
php artisan migrate --seed
```

For containers, replace the placeholder `APP_KEY` in `docker-compose.yml` with output from `php artisan key:generate --show`, then run `docker compose up --build`.

## Running the Application

```powershell
php artisan serve
```

The API is available at `http://localhost:8000/api`; health is `GET /up`.

## Running Tests

```powershell
composer test
composer audit
```

## Default Development Accounts

After seeding (development only):

- `admin@example.com` / `AdminPassword123!`
- `staff@example.com` / `StaffPassword123!`
- `resident@example.com` / `ResidentPassword123!`

## API Endpoints

- `POST /api/auth/register`, `/api/auth/login`, `/api/auth/logout`
- Staff: REST `/api/households`, REST `/api/residents`, `GET /api/dashboard`
- Resident: `GET /api/residents/me`
- `GET|POST /api/certificates`, `GET|PATCH /api/certificates/{id}`
- `GET|POST /api/incidents`, `GET|PATCH /api/incidents/{id}`

Collection endpoints accept `page` and a bounded `per_page` (maximum 100). Search uses `q`; supported filters are documented by the controller route (`zone`, `registered_voter`, and `status`). Responses use Laravel resource `data` envelopes and structured JSON errors.

## Folder Structure

`app/Http/Controllers/Api` contains thin HTTP controllers, `Requests` handles validation/authorization, `Resources` limits response fields, `Policies` enforces ownership, `Models` defines Eloquent relationships, `Services` contains activity logging, `database/migrations` defines the schema, and `tests/Feature` covers security and business rules.

## Security Notes

Sanctum stores hashed tokens. Passwords use Laravel's adaptive hashing. Login and registration are rate-limited. Authorization is server-side through role middleware and policies. Form Requests protect against mass assignment and invalid data; API resources prevent excessive exposure. Eloquent parameterizes queries. CORS is allow-listed, page sizes are bounded, errors omit stack traces when `APP_DEBUG=false`, and certificate transitions use a database transaction plus row lock.

## Known Limitations

This portfolio scope excludes document printing/signatures, SMS/email notifications, geographic mapping, attachments, household genealogy, national registry integration, token-expiration policy, MFA, and password recovery.

## License

MIT
