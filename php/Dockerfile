# backend/Dockerfile
FROM php:8.4-fpm-alpine

WORKDIR /app/backend

# Install PHP extensions if needed
RUN docker-php-ext-install pdo_mysql

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

EXPOSE 9000

CMD ["php-fpm"]
