FROM php:8.2-apache

WORKDIR /var/www/html

# FIX: Changed "/src" to "src/"
COPY src/ .

# Frontend doesn't need mysqli, but it does need permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80