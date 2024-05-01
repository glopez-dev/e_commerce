FROM php:8.3-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    git \
    zlib1g-dev \
    libxml2-dev \
    libpng-dev \
    libzip-dev \
    curl debconf apt-transport-https apt-utils \
    build-essential locales acl mailutils wget nodejs zip unzip \
    gnupg gnupg1 gnupg2 \
    sudo \
    ssh && \
    rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/* 

# PHP EXTENSIONS SETUP
COPY ./app/php_modules.txt /tmp/php_modules.txt

RUN docker-php-ext-install $(cat /tmp/php_modules.txt)

# COMPOSER SETUP
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN composer self-update 2.7.2

RUN usermod -u 1000 www-data
RUN usermod -a -G www-data root

RUN mkdir -p /var/www
RUN chown -R www-data:www-data /var/www

RUN mkdir -p /var/www/.composer
RUN chown -R www-data:www-data /var/www/.composer

WORKDIR /var/www/e_commerce/