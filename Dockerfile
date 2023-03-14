FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libfreetype6-dev \
    libjpeg-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libssh2-1-dev \
    libssh2-1 \
    zip \
    unzip \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath -j$(nproc) gd opcache
RUN pecl install ssh2-1.3.1 xdebug
RUN docker-php-ext-enable ssh2 gd xdebug

# Configure PHP
COPY ./docker/php/conf.d/* /usr/local/etc/php/conf.d/

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy helper script
COPY ./docker/scripts/prepare-docker-setup.sh /tmp/

# Grant www-data permissions on Laravel installation
RUN chown -R www-data:www-data /var/www/

# Set working directory
WORKDIR /var/www

USER www-data
