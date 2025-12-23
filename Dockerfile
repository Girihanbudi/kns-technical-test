FROM php:8.5.1-cli

# Install system dependencies and Postgres PHP extension
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.9.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

# Default dev command (migrate/start handled via docker-compose command override)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
