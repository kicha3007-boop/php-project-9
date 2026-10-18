PORT ?= 8000

setup: install build-css

install:
	composer install
	npm ci

build-css:
	npm run build:css

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public

lint:
	composer exec --verbose phpcs -- --standard=PSR12 --extensions=php public src tests

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 --extensions=php public src tests

test:
	composer exec --verbose phpunit tests

db-init:
	psql -a -d $(DATABASE_URL) -f database.sql

.PHONY: setup install build-css start lint lint-fix test db-init
