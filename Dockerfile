ARG PHP_VERSION=8.2
FROM php:${PHP_VERSION}-cli-alpine

COPY install-composer.sh install-composer.sh

RUN chmod +x install-composer.sh && ./install-composer.sh && rm install-composer.sh