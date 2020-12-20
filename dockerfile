FROM php:7.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        librabbitmq-dev \
    && docker-php-ext-install \
        zip
# RUN pecl install xdebug

# Copy xdebug configuration for remote debugging
# COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/project
