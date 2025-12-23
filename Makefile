SHELL := /bin/bash
COMPOSE ?= docker compose

.PHONY: dev build down logs ps sh composer-install composer-dev migrate test

dev:
	$(COMPOSE) up -d

build:
	$(COMPOSE) up --build -d

down:
	$(COMPOSE) down

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

test:
	$(COMPOSE) run --rm app php artisan test
