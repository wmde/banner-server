current_user         := $(shell id -u)
current_group        := $(shell id -g)
BUILD_DIR            := $(PWD)
DOCKER_FLAGS         := --interactive --tty
UNIQUE_APP_CONTAINER := $(shell uuidgen)-app
COVERAGE_FLAGS       := --coverage-html coverage
DEFAULT_GOAL         := ci

install-php:
	docker run --rm $(DOCKER_FLAGS) --volume $(BUILD_DIR):/app -w /app --volume ~/.composer:/composer --user $(current_user):$(current_group) composer install $(COMPOSER_FLAGS)

update-php:
	docker run --rm $(DOCKER_FLAGS) --volume $(BUILD_DIR):/app -w /app --volume ~/.composer:/composer --user $(current_user):$(current_group) composer update $(COMPOSER_FLAGS)

clear:
	rm -rf var/cache/
	docker compose run --rm --no-deps app rm -rf var/cache/

# n alias to avoid frequent typo
clean: clear

phpunit:
	docker compose run --rm app ./vendor/bin/phpunit

phpunit-with-coverage:
	docker compose -f docker-compose.yml -f docker-compose.debug.yml run --rm app_debug ./vendor/bin/phpunit --configuration=phpunit.xml.dist $(COVERAGE_FLAGS)

docker-build:
	docker compose -f docker-compose.yml -f docker-compose.debug.yml build

ci: test cs phpcs stan

ci-with-coverage: phpunit-with-coverage cs stan

test: phpunit

cs: phpcs

fix-cs:
	docker compose run --rm app ./vendor/bin/phpcbf -p -s

phpcs:
	docker compose run --rm app ./vendor/bin/phpcs -p -s

stan:
	docker compose run --rm app ./vendor/bin/phpstan analyse --configuration=phpstan.neon.dist --memory-limit=1024M --level=7 --no-progress src/ tests/

.PHONY: install-php update-php clean clear phpunit ci ci-with-coverage ci ci-with-coverage test cs fix-cs phpcs stan
