FROM phalconphp/cphalcon:v5.9.2-php8.4

USER root

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libzip-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    zip \
    opcache

WORKDIR /var/www/html

USER phalcon
