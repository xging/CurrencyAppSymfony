# syntax=docker/dockerfile:1
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    build-essential \
    librabbitmq-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && pecl install redis amqp \
    && docker-php-ext-enable redis amqp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version  # Проверка установленной версии Composer

WORKDIR /var/www/html

COPY . .

USER www-data

