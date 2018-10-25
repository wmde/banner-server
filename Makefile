current_user  := $(shell id -u)
current_group := $(shell id -g)
BUILD_DIR     := $(PWD)
DOCKER_FLAGS  := --interactive --tty
UNIQUE_APP_CONTAINER := $(shell uuidgen)-app

.PHONY: ci test phpunit cs stan covers composer

DEFAULT_GOAL := ci

ci: test cs

test: covers phpunit

cs: phpcs stan

fix-cs:
	docker-compose run --rm app ./vendor/bin/phpcbf -p -s

install-php:
	docker run --rm $(DOCKER_FLAGS) --volume $(BUILD_DIR):/app -w /app --volume ~/.composer:/composer --user $(current_user):$(current_group) composer install --ignore-platform-reqs $(COMPOSER_FLAGS)

phpunit:
	docker-compose run --rm app ./vendor/bin/phpunit

phpunit-with-coverage:
	docker-compose -f docker-compose.yml -f docker-compose.debug.yml run --rm --name $(UNIQUE_APP_CONTAINER)-$@ app_debug ./vendor/bin/phpunit --configuration=phpunit.xml.dist --coverage-clover coverage.clover --printer="PHPUnit\TextUI\ResultPrinter"

phpcs:
	docker-compose run --rm app ./vendor/bin/phpcs -p -s

stan:
	docker-compose run --rm app ./vendor/bin/phpstan analyse --configuration=phpstan.neon.dist --level=7 --no-progress src/ tests/

covers:
	docker-compose run --rm app ./vendor/bin/covers-validator

update-php:
	docker run --rm $(DOCKER_FLAGS) --volume $(BUILD_DIR):/app -w /app --volume ~/.composer:/composer --user $(current_user):$(current_group) composer update --ignore-platform-reqs $(COMPOSER_FLAGS)

ci-with-coverage: covers phpunit-with-coverage cs stan
