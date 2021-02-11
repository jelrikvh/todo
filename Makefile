DOCKER_COMPOSE = docker-compose --project-name todo --file infra/docker-compose.yml

include infra/xdebug.mk

.PHONY: shell
shell: infra/.built vendor/composer/installed.json
	$(DOCKER_COMPOSE) run php sh

.PHONY: test
test: infra/.built vendor/composer/installed.json
	$(DOCKER_COMPOSE) run --no-deps php sh -c ' \
		vendor/bin/phpstan \
			analyse \
			-c phpstan.neon \
			--ansi \
			--level=max \
			src \
		&& vendor/bin/phpcs \
			--colors \
			-s src \
		&& vendor/bin/phpmd \
			src \
			text \
			phpmd.xml \
	'
	$(MAKE) unit-test

.PHONY: clean
clean:
	git clean -fdX

unit-test:
	$(DOCKER_COMPOSE) run --no-deps php sh -c ' \
		php -dmemory_limit=-1 -derror_reporting=-1 -ddisplay_errors=On -dpcov.enabled=1 vendor/bin/phpunit \
			-vvv \
			--testdox \
			--coverage-html=var/cache/test-coverage/html \
			--coverage-xml=var/cache/test-coverage/xml \
			--coverage-clover=var/cache/test-coverage/clover.xml \
			--log-junit=var/cache/test-coverage/phpunit.junit.xml \
		&& php infra/coverage-checker.php var/cache/test-coverage/clover.xml 100 \
		&& php -derror_reporting -ddisplay_errors=On vendor/bin/infection \
			--coverage=var/cache/test-coverage/ \
			--only-covered \
	'

infra/.built: infra/Dockerfile
	$(DOCKER_COMPOSE) build php
	touch $@

vendor/composer/installed.json: composer.lock
	$(DOCKER_COMPOSE) run php composer install
