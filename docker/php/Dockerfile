ARG PHP_EXTENSIONS="imagick"

FROM thecodingmachine/php:8.2-v4-fpm as php

COPY --from=composer:2.5.8 --chown=docker:docker /usr/bin/composer /usr/bin/composer

WORKDIR /application

FROM nginx:1.23.2-alpine AS nginx
