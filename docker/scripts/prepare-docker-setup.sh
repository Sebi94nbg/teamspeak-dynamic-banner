#!/usr/bin/env bash

# Print all executed commands
set -x

# Install composer dependencies
composer install

# APP_KEY
APP_KEY_VALUE=$(grep -E "^APP_KEY=" .env | cut -d "=" -f 2)

if [[ -z "$APP_KEY_VALUE" ]]; then
    php artisan key:generate --ansi
fi

# Adjust DotEnv for the Docker setup
sed -i "s/^APP_ENV=.*/APP_ENV=local/g" .env
sed -i "s/^APP_DEBUG=.*/APP_DEBUG=true/g" .env
sed -i "s/^LOG_DEPRECATIONS_CHANNEL=.*/LOG_DEPRECATIONS_CHANNEL=stack/g" .env
sed -i "s/^LOG_LEVEL=.*/LOG_LEVEL=debug/g" .env
sed -i "s/^DB_HOST=.*/DB_HOST=database/g" .env
sed -i "s/^REDIS_HOST=.*/REDIS_HOST=cache/g" .env

# Cache config and routes
php artisan optimize

# Cache views
php artisan view:cache

# Run migrations
php artisan migrate
