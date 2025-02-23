#!/bin/ash

if [ -f /app/backend/vendor/autoload.php ]; then
    echo "vendor/autoload.php already exists"
else
    echo "Installing composer dependencies"
    composer install --no-interaction --no-progress --optimize-autoloader
fi

if [ -f /app/backend/config.php ]; then
    echo "config.php already exists"
else
    echo "Creating config.php"
    cp config.php.example config.php
fi

php-fpm
