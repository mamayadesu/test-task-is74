FROM php:7.4-apache

RUN apt-get update -y && apt-get install -y libpng-dev libjpeg-dev libzip-dev

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev

RUN apt-get install -y zip unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip

