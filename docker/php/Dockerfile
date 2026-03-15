FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    curl \
    nano \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    intl \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN useradd -m laravel

WORKDIR /var/www

RUN chown -R laravel:laravel /var/www

USER laravel

