# If the first argument is "composer"...
ifeq (composer,$(firstword $(MAKECMDGOALS)))
  # use the rest as arguments for "composer"
  RUN_ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))
  # ...and turn them into do-nothing targets
  $(eval $(RUN_ARGS):;@:)
endif

.PHONY: ci test phpunit cs stan covers composer

DEFAULT_GOAL := ci

ci: test cs

test: covers phpunit

cs: phpcs stan

fix-cs:
	docker-compose run --rm app ./vendor/bin/phpcbf -p -s

phpunit:
	docker-compose run --rm app ./bin/phpunit

phpcs:
	docker-compose run --rm app ./vendor/bin/phpcs -p -s

stan:
	docker-compose run --rm app ./vendor/bin/phpstan analyse --level=7 --no-progress src/ tests/

covers:
	docker-compose run --rm app ./vendor/bin/covers-validator

composer:
	docker run --rm --interactive --tty --volume $(shell pwd):/app -w /app\
	 --volume ~/.composer:/composer --user $(shell id -u):$(shell id -g) composer composer --no-scripts $(filter-out $@,$(MAKECMDGOALS))

update-php:
	docker run --rm $(DOCKER_FLAGS) --volume $(BUILD_DIR):/app -w /app --volume ~/.composer:/composer --user $(current_user):$(current_group) composer update --ignore-platform-reqs $(COMPOSER_FLAGS)
