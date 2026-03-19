DATABASE_URL=postgresql://app:!ChangeMe!@database:5432/prevention

DOCKER_COMP = docker compose
PHP_CONT    = $(DOCKER_COMP) exec php
PHP         = $(PHP_CONT) php
COMPOSER    = $(PHP_CONT) composer
SYMFONY     = $(PHP) bin/console

# Terraform / remote demo
TOFU        = ~/.local/bin/tofu -chdir=terraform
DEMO_IP    ?= $(shell ~/.local/bin/tofu -chdir=terraform output -raw server_ip 2>/dev/null)
DEMO_SSH    = ssh -o StrictHostKeyChecking=no root@$(DEMO_IP)

.DEFAULT_GOAL = help
.PHONY: help build up start down logs sh bash composer vendor sf cc test sf-migrate sf-fixtures bun-install tw tw-watch \
        tf-init tf-plan tf-apply tf-destroy tf-ip \
        demo-hash demo-build demo-up demo-down demo-logs demo-sh demo-init demo-deploy demo-redeploy demo-emails

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
bun-install: ## Install JS dependencies via Bun (run after make build)
	@$(PHP_CONT) bun install

tw: ## Compile Tailwind CSS + DaisyUI once (output tracked in git)
	@$(PHP_CONT) bunx tailwindcss -i /app/tailwind.source.css -o /app/assets/styles/app.compiled.css --minify

tw-watch: ## Watch and recompile Tailwind CSS + DaisyUI on change
	@$(PHP_CONT) bunx tailwindcss -i /app/tailwind.source.css -o /app/assets/styles/app.compiled.css --watch

## —— Demo 🔒 ——————————————————————————————————————————————————————————————————
DEMO_COMP = docker compose -f compose.demo.yaml --env-file .env.demo
DEMO_PHP  = $(DEMO_COMP) exec php

demo-hash: ## Generate bcrypt hash for demo password, e.g.: make demo-hash p="secret"
	@$(eval p ?=)
	@docker run --rm dunglas/frankenphp:1-php8.4 frankenphp hash-password --plaintext "$(p)"

demo-build: ## Build demo Docker image
	@$(DEMO_COMP) build --pull --no-cache

demo-up: ## Start demo stack (detached)
	@$(DEMO_COMP) up --detach

demo-down: ## Stop demo stack
	@$(DEMO_COMP) down --remove-orphans

demo-logs: ## Follow demo logs
	@$(DEMO_COMP) logs --tail=0 --follow

demo-sh: ## Shell into demo php container
	@$(DEMO_PHP) sh

demo-init: ## Initialise demo db: migrate + fixtures + geoip + cache warmup
	@$(DEMO_PHP) php bin/console doctrine:migrations:migrate --no-interaction
	@$(DEMO_PHP) php bin/console app:seed
	@$(DEMO_PHP) sh -c 'mkdir -p /var/data && curl -sL "https://download.db-ip.com/free/dbip-country-lite-$$(date +%Y-%m).mmdb.gz" | gunzip > /var/data/dbip-country-lite.mmdb && echo "GeoIP downloaded."'
	@$(DEMO_PHP) php bin/console cache:warmup

demo-deploy: demo-build demo-up demo-init ## Full first-time demo deploy (build + start + init)

demo-redeploy: ## Pull latest code + rebuild + restart on remote Hetzner server
	@echo "→ Deploying to $(DEMO_IP)"
	$(DEMO_SSH) "cd /app && git pull && \
	  docker compose -f compose.demo.yaml --env-file .env.demo build --no-cache php && \
	  docker compose -f compose.demo.yaml --env-file .env.demo up -d && \
	  docker compose -f compose.demo.yaml --env-file .env.demo exec -T php php bin/console cache:warmup"
	@echo "✓ Done — http://$(DEMO_IP)"

demo-provision: ## First-time remote setup: copy .env.demo + deploy (run after tf-apply)
	@test -f .env.demo || (echo "ERROR: .env.demo not found — copy from .env.demo.example" && exit 1)
	@echo "→ Uploading .env.demo to $(DEMO_IP)"
	@scp -o StrictHostKeyChecking=no .env.demo root@$(DEMO_IP):/app/.env.demo
	@echo "→ Running first-time deploy"
	$(DEMO_SSH) "cd /app && \
	  docker compose -f compose.demo.yaml --env-file .env.demo build && \
	  docker compose -f compose.demo.yaml --env-file .env.demo up -d && \
	  docker compose -f compose.demo.yaml --env-file .env.demo exec -T php php bin/console doctrine:migrations:migrate --no-interaction && \
	  docker compose -f compose.demo.yaml --env-file .env.demo exec -T php php bin/console app:seed && \
	  docker compose -f compose.demo.yaml --env-file .env.demo exec -T -u root php mkdir -p /var/data && \
	  docker compose -f compose.demo.yaml --env-file .env.demo exec -T -u root php chmod 777 /var/data && \
	  curl -sL \"https://download.db-ip.com/free/dbip-country-lite-\$$(date +%Y-%m).mmdb.gz\" | gunzip | docker compose -f compose.demo.yaml --env-file .env.demo exec -T php tee /var/data/dbip-country-lite.mmdb > /dev/null && \
	  docker compose -f compose.demo.yaml --env-file .env.demo exec -T php php bin/console cache:warmup"
	@echo "✓ Demo live — http://$(DEMO_IP)"

demo-emails: ## Show outreach email drafts from outreach/
	@for f in outreach/*.md; do \
	  echo ""; \
	  echo "══════════════════════════════════════════════════"; \
	  echo "  $$f"; \
	  echo "══════════════════════════════════════════════════"; \
	  cat "$$f"; \
	done | less -R

## —— Terraform / Hetzner ☁️ ——————————————————————————————————————————————————
tf-init: ## Initialise OpenTofu + download hcloud provider
	$(TOFU) init

tf-plan: ## Preview infrastructure changes
	$(TOFU) plan

tf-apply: ## Create / update Hetzner server (prompts for confirmation)
	$(TOFU) apply -auto-approve
	@echo ""
	@echo "Server IP: $$($(TOFU) output -raw server_ip)"
	@echo "Next step: make demo-provision DEMO_IP=$$($(TOFU) output -raw server_ip)"

tf-destroy: ## Destroy Hetzner server (stops billing)
	$(TOFU) destroy -auto-approve

tf-ip: ## Print current demo server IP
	@$(TOFU) output -raw server_ip 2>/dev/null || echo "No server provisioned yet"

demo-ssh: ## SSH into the remote demo server
	ssh -o StrictHostKeyChecking=no root@$(DEMO_IP)

## —— Tests 🧪 —————————————————————————————————————————————————————————————————
test: ## Run phpunit, e.g.: make test c="tests/Service/SafetyOutputFilterTest.php"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)
