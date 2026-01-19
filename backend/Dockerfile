FROM php:8.2-apache

WORKDIR /var/www/html

# Install only required extensions
RUN docker-php-ext-install mysqli opcache

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy source
COPY src/ /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Uploads
RUN mkdir -p /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/uploads

EXPOSE 80
