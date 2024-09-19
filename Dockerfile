FROM php:7.4-apache

RUN apt-get update -y && apt-get install -y libpng-dev libjpeg-dev

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev

RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd
