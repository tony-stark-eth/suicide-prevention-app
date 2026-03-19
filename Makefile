DATABASE_URL=postgresql://app:!ChangeMe!@database:5432/prevention

DOCKER_COMP = docker compose
PHP_CONT    = $(DOCKER_COMP) exec php
PHP         = $(PHP_CONT) php
COMPOSER    = $(PHP_CONT) composer
SYMFONY     = $(PHP) bin/console

.DEFAULT_GOAL = help
.PHONY: help build up start down logs sh bash composer vendor sf cc test sf-migrate sf-fixtures tw tw-watch

## —— Prevention Platform 🐳 ——————————————————————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Build Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start containers (detached)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start

down: ## Stop containers
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Follow logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Shell into php container
	@$(PHP_CONT) sh

bash: ## Bash into php container
	@$(PHP_CONT) bash

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer command, e.g.: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## Run bin/console command, e.g.: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: ## Clear cache
cc: c=c:c
cc: sf

sf-migrate: ## Run doctrine migrations
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction

sf-fixtures: ## Load fixtures (append)
	@$(SYMFONY) doctrine:fixtures:load --append --no-interaction

sf-followups: ## Process followup queue manually
	@$(SYMFONY) app:process-followups

## —— GeoIP 🌍 ——————————————————————————————————————————————————————————————————
geoip: ## Download DB-IP country lite MMDB (CC BY 4.0, no account required)
	@$(PHP_CONT) sh -c 'mkdir -p /var/data && curl -sL "https://download.db-ip.com/free/dbip-country-lite-$$(date +%Y-%m).mmdb.gz" | gunzip > /var/data/dbip-country-lite.mmdb && echo "DB-IP database downloaded."'

## —— Tailwind 🎨 —————————————————————————————————————————————————————————————
tw: ## Compile Tailwind CSS once
	@$(PHP_CONT) tailwindcss -i /app/assets/styles/app.source.css -o /app/assets/styles/app.css --minify

tw-watch: ## Watch and recompile Tailwind CSS on change
	@$(PHP_CONT) tailwindcss -i /app/assets/styles/app.source.css -o /app/assets/styles/app.css --watch

## —— Tests 🧪 —————————————————————————————————————————————————————————————————
test: ## Run phpunit, e.g.: make test c="tests/Service/SafetyOutputFilterTest.php"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)
