FROM php:7.4-fpm-alpine

RUN apk upgrade --update && apk add --no-cache \
    libpng-dev \
    libzip-dev \
    libwebp-dev \
    libjpeg-turbo-dev \
    libpng-dev libxpm-dev \
    freetype-dev \
    icu-dev \
    libpq \
    imagemagick \
    build-base \
    rabbitmq-c \
    rabbitmq-c-dev \
    zlib-dev \
    php7-dev \
    bash \
    curl

RUN docker-php-ext-configure gd && \
    docker-php-ext-install zip gd && \
    docker-php-ext-install mysqli pdo pdo_mysql && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl

#redis
RUN pecl install -o -f redis && docker-php-ext-enable redis && \
    rm -rf /tmp/pear

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN mkdir /.composer
RUN chown -R www-data /.composer
RUN chmod -R 777 /.composer
