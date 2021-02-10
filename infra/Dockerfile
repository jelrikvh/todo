FROM php:8.0-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_CACHE_DIR /var/opt/.composer

WORKDIR /var/opt

RUN apk add --no-cache $PHPIZE_DEPS

RUN pecl install xdebug-3.0.2 pcov \
    && docker-php-ext-enable pcov

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
