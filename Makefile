QA_DOCKER_IMAGE=jakzal/phpqa:1.34.1-php7.4-alpine
QA_DOCKER_COMMAND=docker run --init -t --rm --user "$(shell id -u):$(shell id -g)" --volume /tmp/tmp-phpqa-$(shell id -u):/tmp --volume "$(shell pwd):/project" --workdir /project ${QA_DOCKER_IMAGE}

dist: install cs-full phpstan test-full
lint: install security-check cs-full phpstan

install:
	composer install --no-progress --no-interaction --no-suggest --optimize-autoloader --prefer-dist --ansi

test:
	./vendor/bin/simple-phpunit --verbose

# Linting tools
security-check: ensure
	sh -c "${QA_DOCKER_COMMAND} security-checker security:check ./composer.lock"

phpstan: ensure
	sh -c "${QA_DOCKER_COMMAND} phpstan analyse --configuration phpstan.neon"

cs: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix -vvv --diff"

cs-full: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix -vvv --using-cache=false --diff"

cs-full-check: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix -vvv --using-cache=false --diff --dry-run"

ensure:
	mkdir -p ${HOME}/.composer /tmp/tmp-phpqa-$(shell id -u)

.PHONY: install test phpstan cs cs-full cs-full-check
