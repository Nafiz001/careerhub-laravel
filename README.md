# CareerHub

A full-stack job board where **employers** post jobs and **seekers** apply, track their applications, and get status updates. Built with Laravel 12, Vue 3, and Inertia.js.

> Part of a portfolio of four small, deployable apps — each in a different stack (Laravel, FastAPI, Spring Boot, Angular).

## Features

- **Public job listing** — browse open jobs with full-text search and filters for type (full-time, part-time, remote, internship) and location, with pagination.
- **Role-based accounts** — register as a *seeker* or an *employer*; the dashboard and available actions adapt to the role.
- **Employer tools** — create, edit, and close job postings; view applicants per job and update their status (pending → reviewed → accepted/rejected).
- **Seeker tools** — apply to a job with a cover letter (one application per job), and track all submitted applications in one place.
- **Authentication** — registration, login, password reset, email verification, and profile management (scaffolded with Laravel Breeze + Inertia).
- **Authorization** — `JobPolicy` enforces that only owning employers manage their jobs/applicants and only seekers apply to open jobs.

## Tech stack

| Layer | Technology |
|-------|------------|
| Backend | PHP 8.2, Laravel 12 |
| Frontend | Vue 3, Inertia.js 2, Ziggy |
| Styling | Tailwind CSS 3 + `@tailwindcss/forms` |
| Build | Vite 7, `laravel-vite-plugin` |
| Auth | Laravel Breeze, Sanctum |
| Database | SQLite (default) |
| Tests | PHPUnit 11 |

## Prerequisites

- PHP **8.2+** with `pdo_sqlite`, `intl`, and `zip` extensions
- [Composer](https://getcomposer.org/) 2
- [Node.js](https://nodejs.org/) 18+ and npm

## Getting started

```bash
# 1. Install dependencies
composer install
npm install

# 2. Set up environment
cp .env.example .env
php artisan key:generate

# 3. Create the SQLite database and seed demo data
#    (DB_CONNECTION=sqlite is the default in .env)
touch database/database.sqlite
php artisan migrate --seed

# 4. Build front-end assets
npm run build
```

Or run the bundled one-shot setup script:

```bash
composer run setup
```

### Run the app (development)

The `dev` script runs the PHP server, queue worker, log viewer, and Vite dev server together:

```bash
composer run dev
```

Then open **http://localhost:8000**.

Prefer to run pieces separately? Use two terminals:

```bash
php artisan serve      # http://localhost:8000
npm run dev            # Vite HMR
```

## Demo accounts

After seeding, you can log in with:

| Role | Email | Password |
|------|-------|----------|
| Seeker | `nafiz@example.com` | `password` |
| Employer | `hr@brainstation-23.com` | `password` |

The seeder also creates a second employer (Pathao), a second seeker, 12 sample job postings, and a handful of applications.

## Key routes

| Method | Path | Access | Purpose |
|--------|------|--------|---------|
| GET | `/` | public | Landing page |
| GET | `/jobs` | public | Job listing with search/filters |
| GET | `/jobs/{job}` | public | Job detail |
| GET | `/dashboard` | auth | Role-aware dashboard |
| POST | `/jobs/{job}/apply` | seeker | Apply with cover letter |
| GET | `/my/applications` | seeker | Track applications |
| POST | `/manage/jobs` | employer | Create a posting |
| GET/PUT/DELETE | `/manage/jobs/{job}` | employer | Edit / close a posting |
| GET | `/manage/jobs/{job}/applicants` | employer | View applicants |
| PATCH | `/applications/{application}/status` | employer | Update applicant status |

## Tests

```bash
php artisan test
# or
composer run test
```

Tests run against an in-memory SQLite database. The suite covers the job board flow (listing visibility, search/filter, apply-once, authorization) plus the Breeze auth and profile flows — **32 tests** in total.

## Project structure

```
app/
  Http/Controllers/   # Job, Application, Dashboard, Auth controllers
  Models/             # User, Job (table: job_posts), Application
  Policies/           # JobPolicy (create/update/delete/apply/viewApplicants)
database/
  migrations/         # users + role, job_posts, applications
  seeders/            # DatabaseSeeder — demo employers, jobs, applications
resources/js/
  Pages/              # Inertia/Vue pages (Jobs, Applications, Dashboard, Auth, Profile)
  Layouts/ Components/
routes/web.php        # all application routes
tests/Feature/        # JobBoardTest, ProfileTest, Auth tests
```

## Deployment

A `Dockerfile`, `docker/entrypoint.sh`, and `render.yaml` are included for one-click deployment to [Render](https://render.com/) (Docker runtime, free tier). The entrypoint generates `APP_KEY` if absent, caches config/routes/views, creates the SQLite file, runs migrations + seed, then starts the server on `$PORT`.

```bash
docker build -t careerhub .
docker run -p 8080:8080 careerhub
```

> **Note:** the SQLite database lives inside the image, so data resets on every redeploy. Attach a persistent disk or switch `DB_CONNECTION` to a managed database for durable storage.

## License

MIT — sample/portfolio project.
