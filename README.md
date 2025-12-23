# Backend API (Laravel)

This repository is trimmed to a backend-only Laravel 12 project. Vite/Tailwind scaffolding, Node toolchain, and built assets have been removed so you can focus on APIs and services.

## Quick start

- `composer install`
- Copy `.env.example` to `.env`, set Postgres credentials (`DB_CONNECTION=pgsql`, host/port/database/user/password`), and ensure the database exists.
- `php artisan key:generate`
- `php artisan migrate --graceful --ansi`
- `composer dev` (starts `php artisan serve` on port 8000)

## Docker setup

- Use `.env` for both local and Docker; set `DB_HOST=db` when using Compose.
- Build and start: `docker compose up --build`
- App: http://localhost:8000
- Postgres: localhost:5432 (uses credentials from `.env`)
- First-time setup runs `composer install` and `php artisan migrate --force` automatically inside the container.
- To run one-off commands: `docker compose run --rm app php artisan tinker` (or other artisan commands)
- Container toolchain: PHP 8.5.1 with Composer 2.9.2 (run Composer via Docker to avoid local PHP version mismatches).

## Make targets

- `make up` / `make build` / `make down` — manage the compose stack
- `make logs` — follow app/db logs
- `make sh` — shell into the app container
- `make composer-install` / `make composer-dev` — install deps or run the dev script inside the container
- `make migrate` — run migrations (force)
- `make test` — run the test suite

## Endpoints

- `GET /` -> `{"message":"Backend is running."}`
- `GET /api/ping` -> `{"message":"pong"}` (API prefix + `api` middleware group)
- `GET /up` -> framework health probe

## Testing

- `php artisan test`

## Notes

- Frontend build files (`package.json`, Vite config, resources/css/js, public/build, node_modules) were removed.
- `DatabaseSeeder` ships a default user (`test@example.com` / password `password`).
