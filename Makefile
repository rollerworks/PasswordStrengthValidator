include vendor/rollerscapes/standards/Makefile

QA_DOCKER_IMAGE=jakzal/phpqa:1.115.0-php8.4-alpine

phpunit:
	./vendor/bin/phpunit

