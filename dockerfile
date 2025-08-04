FROM php:8.2-apache

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip \
    && docker-php-ext-install pdo pdo_pgsql zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working dir
WORKDIR /var/www/html

# Copy composer & source
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .

# Install PHP deps & cache config
RUN composer install --no-dev --optimize-autoloader \
    && cp .env.example .env \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

EXPOSE 80
