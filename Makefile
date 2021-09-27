.DEFAULT_GOAL := help

up: ## Start project
	docker compose up -d --build

down: ## Stop project
	docker compose down --remove-orphans

fixtures: ## Run fixtures
	docker exec -it charge-website_php-fpm_1 bash -c "php bin/console hautelook:fixtures:load --purge-with-truncate -n"

php: ## Enter php container
	docker exec -it charge-website_php-fpm_1 bash

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
