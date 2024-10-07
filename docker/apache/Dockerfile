FROM php:8.2-apache

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN apt-get update \
    && apt-get install -qq -y --no-install-recommends \
    cron \
    nano unzip \
	vim htop \
    git \
    locales coreutils apt-utils git libicu-dev g++ libpng-dev libxml2-dev libzip-dev libonig-dev libxslt-dev;

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

RUN curl -sS https://get.symfony.com/cli/installer | bash

RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql mysqli gd opcache intl zip calendar dom mbstring zip gd xsl && a2enmod rewrite
RUN pecl install apcu && docker-php-ext-enable apcu
    
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chown -R www-data:www-data /var/www/html

RUN cd /var/www/html/config/jwt \
    && openssl genpkey -algorithm RSA -out private.pem -pkeyopt rsa_keygen_bits:2048 \
    && openssl rsa -pubout -in private.pem -out public.pem

COPY php.ini /usr/local/etc/php/conf.d/php.ini

WORKDIR /var/www/html