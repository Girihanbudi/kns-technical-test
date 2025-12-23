# Backend API (Laravel)

Backend-only Laravel 12 project focused on REST APIs (no frontend toolchain). Stack: PHP/Laravel, Postgres, PHPUnit, Docker Compose, Postman.

## Getting started (local)
1) `composer install`
2) Copy `.env.example` → `.env`; set DB/mail values (`DB_CONNECTION=pgsql`, host/port/db/user/password). Set `ROOT_ADMIN_*` for seeding the root admin.
3) `php artisan key:generate`
4) `php artisan migrate --graceful --ansi` (or `php artisan migrate --seed` to create the root admin)
5) `php artisan serve --host=127.0.0.1 --port=8000` (or use Docker/Make below)

## Docker
- Uses `.env` (set `DB_HOST=db` for Compose).
- Start everything: `docker compose up --build` (runs composer install + migrate --force).
- App: http://localhost:8000 | Postgres: localhost:5432.
- One-off: `docker compose run --rm app php artisan tinker` (etc.).

## Make targets
- `make dev` start app + db
- `make dev-db` start only db (run app locally)
- `make dev-app` start only app container
- `make build` build images; `make down` stop all; `make down-db` stop db only
- `make logs` tail logs; `make ps` status; `make sh` shell into app container
- `make composer-install` / `make composer-dev`
- `make migrate` | `make seed` | `make migrate-seed`
- `make test` runs `php artisan test --env=testing` with sqlite in-memory

## Folder structure (high level)
- `app/Controllers` core controllers (pure logic)
- `app/Http/Handlers` route handlers (validation + ApiResponse wrapping)
- `app/Http/Middleware` auth/token/role guards
- `app/Http/Requests` validation (FormRequests) + pagination helpers
- `app/Support/ApiResponse` unified JSON envelope
- `routes/api.php` includes `routes/api/*.php` (auth, users, ping)
- `database/migrations` schema (users/orders/tokens/indexes); `database/seeders` seeds root admin from env
- `resources/views/emails` mail templates
- `tests` Feature/Unit suites; `.env.testing` forces sqlite in-memory

## API reference
- Health: `GET /api/ping`
- Auth: `POST /api/auth/login` (Bearer token)
- Users: `POST /api/users` (admin/manager), `GET /api/users`, `PATCH /api/users/{id}` (role-based rules)
- Responses use envelope: `{success,message,errors,data,code}`. Use header `Authorization: Bearer <token>`.
- Postman collection: https://www.postman.com/blue-sunset-9254/workspace/kns-technical-test/collection/7070614-b1c82d0e-303a-4ddb-9a65-265f600df088?action=share&source=copy-link&creator=7070614

## Testing
- Local: `php artisan test --env=testing`
- Make: `make test` (uses sqlite in-memory; won’t touch Postgres)

## Notes
- Default mailer in `.env.example` is `array` for safe local/dev; switch to `smtp` when real delivery is needed.
- Root admin seeding pulls from `ROOT_ADMIN_EMAIL`, `ROOT_ADMIN_PASSWORD`, `ROOT_ADMIN_NAME`.
