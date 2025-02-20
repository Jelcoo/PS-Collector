#!/bin/ash

composer install --no-interaction --no-progress --optimize-autoloader

php-fpm
