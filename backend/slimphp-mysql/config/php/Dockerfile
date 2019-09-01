FROM php:7.3-fpm

RUN apt-get update && apt-get install -y zip unzip git libmcrypt-dev mysql-client \
    && docker-php-ext-install pdo_mysql pdo

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www
