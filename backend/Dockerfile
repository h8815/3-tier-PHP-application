FROM php:8.2-apache

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

COPY /src .

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80