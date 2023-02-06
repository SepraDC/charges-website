.DEFAULT_GOAL := help
MAKEFILE_PATH := $(abspath $(lastword $(MAKEFILE_LIST)))
CWD := $(dir $(MAKEFILE_PATH))
UID := $(shell id -u)

setup: ## Setup the project from zero
	@make generate-ssl
	docker-compose up -d
	@echo "Configuring APP_UID"
	@sed -i "s/APP_UID=.*/APP_UID=`id -u`/g" .env
	@make instruct-ssl

setup-ssl: ## Setup SSL post-install
	@make generate-ssl
	docker-compose stop caddy
	docker-compose up -d --build caddy
	@make instruct-ssl

generate-ssl: ## Generate the SSL certificate and CA
	docker run --rm -v $(CWD)docker/caddy/certs:/root/.local/share/mkcert goodeggs/mkcert -cert-file /root/.local/share/mkcert/local-cert.pem -key-file /root/.local/share/mkcert/local-key.pem "idea.local" "*.idea.local"
	openssl x509 -in docker/caddy/certs/rootCA.pem -inform PEM -out docker/caddy/certs/rootCA.crt

instruct-ssl: ## Tell users to RTFM
	echo "\033[0;32mLes certificats ont été générés correctement\033[0m"
	echo "\033[0;33mVeuillez suivre les instructions présentes dans le fichier docs/10-ssl.md\033[0m"

up: ## Start project
	docker compose up -d --build

down: ## Stop project
	docker compose down --remove-orphans

migrations: ## Run migrations
	docker exec -it charge-website-php-fpm-1 bash -c "php bin/console doctrine:migrations:migrate"

fixtures: ## Run fixtures
	docker exec -it charge-website-php-fpm-1 bash -c "php bin/console hautelook:fixtures:load --purge-with-truncate -n"

api: ## Enter php container
	docker-compose exec api sh

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
