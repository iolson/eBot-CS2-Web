# Stage 1: Build frontend assets
FROM node:22-alpine AS node-builder
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
COPY resources/ resources/
RUN npm ci && npm run build

# Stage 2: PHP-FPM application
FROM php:8.4-fpm AS app

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    libxml2-dev libzip-dev zip unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip sockets \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . /var/www
COPY --from=node-builder /app/public/build public/build

RUN composer dump-autoload --optimize \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
