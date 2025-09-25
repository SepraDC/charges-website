.DEFAULT_GOAL := help
MAKEFILE_PATH := $(abspath $(lastword $(MAKEFILE_LIST)))
CWD := $(dir $(MAKEFILE_PATH))
UID := $(shell id -u)
HOST := charge.local

PHPQA_IMAGE_VERSION := php8.4-alpine
PHPQA_IMAGE := jakzal/phpqa:$(PHPQA_IMAGE_VERSION)
DOCKER_QA := docker run --user $(shell id -u):$(shell id -u) -v "$(CWD)/api":/app -w /app --rm -it $(PHPQA_IMAGE) sh -c

setup: ## Setup the project from zero
	@make generate-ssl
	docker compose up -d
	@echo "Configuring APP_UID"
	@sed -i "s/APP_UID=.*/APP_UID=`id -u`/g" .env
	@make instruct-ssl

instruct-ssl: ## Tell users to RTFM
	echo "\033[0;32mLes certificats ont été générés correctement\033[0m"
	echo "\033[0;33mVeuillez suivre les instructions présentes dans le fichier docs/10-ssl.md\033[0m"

up: ## Start project
	docker compose up -d --build
	docker compose cp api:/data/caddy/pki/authorities/local/root.crt .

up-prod: ## Start prod project
	docker compose -f docker compose-prod.yml up -d --build

down: ## Stop project
	docker compose down --remove-orphans

.PHONY: db
db-dump: ## Dump database schema
	docker compose exec db /usr/bin/mysqldump -u root -p charges-website

.PHONY: api
api: ## Enter php container
	docker compose exec -u app api sh -l

api-lint al: # Fix error linter
	docker pull $(PHPQA_IMAGE)
	$(DOCKER_QA) "phpcs" || true
	$(DOCKER_QA) "rector process --dry-run" || true
	$(DOCKER_QA) "phpstan analyse" || true

migrations: ## Run migrations
	docker compose exec -u app -it api ash -c "php bin/console doctrine:migrations:migrate"

fixtures: ## Run fixtures
	docker compose exec -u app -it api ash -c "php bin/console doctrine:fixtures:load -n"

.PHONY: front
front: ## Enter front container
	docker compose exec -u node front sh

front\:lint fl: ## Run front linter
	docker compose exec -u node -it front ash -c 'pnpm run lintfix'

help: ## Display this help
	@grep -E '^[a-zA-Z_\\\:-]+:.*?## .*$$' Makefile | sed 's/\\//g' | awk 'BEGIN {FS = ": *?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
