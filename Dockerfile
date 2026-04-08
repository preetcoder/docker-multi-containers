FROM php:7.4-apache

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y git unzip curl\
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80