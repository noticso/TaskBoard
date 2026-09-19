FROM php:8.5-cli-bookworm

# Estensioni di sistema necessarie a Laravel + driver Postgres
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libpq-dev libzip-dev curl ca-certificates gnupg \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && npm ci \
    && npm run build \
    && rm -rf node_modules

EXPOSE 10000

CMD php artisan config:cache \
    && php artisan route:cache \
    && php artisan migrate --force \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
