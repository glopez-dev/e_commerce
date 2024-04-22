M=make
PHP=php bin/console

help: ## Affiche les commandes possibles du makefile
	@egrep -h '\s##\s' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m  %-30s\033[0m %s\n", $$1, $$2}'

entity: ## Créer une entité
	$(PHP) make:entity

migration: ## Faire la migration dans le projet
	$(PHP) make:migration

migrate: ## Faire la migration dans la base
	$(PHP) doctrine:migrations:migrate

controller: ## Créer un controller
	$(PHP) make:controller

form: ## Créer un formulaire
	$(PHP) make:form

start: ## Start le server sur le localhost
	symfony serve

cache: ## Nettoie le cache
	$(PHP) cache:clear

database: ## Créer la base de donnée
	$(PHP) doctrine:database:create

crud: ## Créer le crud pour une entité
	$(PHP) make:crud

jwt: ## Créer les clefs private et public pour JWT
	$(PHP) lexik:jwt:generate-keypair --overwrite