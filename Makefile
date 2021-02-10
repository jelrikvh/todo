DOCKER_COMPOSE = docker-compose --project-name todo --file infra/docker-compose.yml

include infra/xdebug.mk

.PHONY: shell
shell: infra/.built vendor/composer/installed.json
	$(DOCKER_COMPOSE) run php sh

.PHONY: test
test: infra/.built vendor/composer/installed.json
	$(DOCKER_COMPOSE) run php sh -c ' \
		php -dmemory_limit=-1 -derror_reporting=-1 -ddisplay_errors=On -dpcov.enabled=1 vendor/bin/phpunit \
			-vvv \
			--testdox \
			--coverage-html=var/cache/test-coverage/html \
			--coverage-clover=var/cache/test-coverage/clover.xml \
		&& php infra/coverage-checker.php var/cache/test-coverage/clover.xml 100 \
	'

.PHONY: clean
clean:
	git clean -fdX

infra/.built: infra/Dockerfile
	$(DOCKER_COMPOSE) build php
	touch $@

vendor/composer/installed.json: composer.lock
	$(DOCKER_COMPOSE) run php composer install
