FROM php:8.0-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_CACHE_DIR /var/opt/.composer

RUN apk add --no-cache $PHPIZE_DEPS

RUN pecl install xdebug-3.0.2 pcov \
    && docker-php-ext-enable pcov

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN adduser -Ds /bin/sh app

RUN mkdir -p /var/opt && chown -R app:app /var/opt

USER app

WORKDIR /var/opt
