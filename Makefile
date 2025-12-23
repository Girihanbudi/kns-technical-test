SHELL := /bin/bash
COMPOSE ?= docker compose

.PHONY: dev dev-db dev-app build down down-db logs ps sh composer-install composer-dev migrate seed migrate-seed test

# Start both app + db
dev:
	$(COMPOSE) up -d

# Start only the database (for running the app locally)
dev-db:
	$(COMPOSE) up -d db

# Start only the app container (will auto-start db as dependency if not running)
dev-app:
	$(COMPOSE) up -d app

build:
	$(COMPOSE) up --build -d

down:
	$(COMPOSE) down

down-db:
	$(COMPOSE) stop db

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

sh:
	$(COMPOSE) exec app bash

composer-install:
	$(COMPOSE) run --rm app composer install

composer-dev:
	$(COMPOSE) run --rm app composer dev

migrate:
	$(COMPOSE) run --rm app php artisan migrate --force

seed:
	$(COMPOSE) run --rm app php artisan db:seed --force

migrate-seed:
	$(COMPOSE) run --rm app php artisan migrate --force --seed

test:
	$(COMPOSE) run --rm \
		-e APP_ENV=testing \
		-e DB_CONNECTION=sqlite \
		-e DB_DATABASE=:memory: \
		-e CACHE_STORE=array \
		-e SESSION_DRIVER=array \
		-e QUEUE_CONNECTION=sync \
		-e MAIL_MAILER=array \
		app php artisan test --env=testing
