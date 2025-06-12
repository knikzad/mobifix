#!/bin/sh

set -e

cd /var/www/html

# Ensure .env exists
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Set permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html

# Install composer dependencies if vendor doesn't exist
if [ ! -d "vendor" ]; then
  composer install --no-dev --prefer-dist --no-interaction
fi

# Generate key if not already set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Start PHP-FPM
exec php-fpm
