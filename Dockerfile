FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel source code (in dev, this is overwritten by mounted volume anyway)
COPY ../backend /var/www

# Permissions (important for Laravel storage/logs/cache)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# EXPOSE port used by php-fpm (if needed for debugging)
EXPOSE 9000


# -----------------------------------------------
# Install MongoDB extension for PHP (ext-mongodb)
# -----------------------------------------------
RUN apt-get update && apt-get install -y libssl-dev pkg-config && \
    pecl install mongodb && \
    docker-php-ext-enable mongodb


# Run PHP-FPM
CMD ["php-fpm"]
