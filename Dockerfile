FROM php:8.5.1-cli

ARG INSTALL_XDEBUG=false
ARG APP_ENV=production

# Install system dependencies, Postgres extension, and optionally Xdebug for dev
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends git unzip libpq-dev; \
    docker-php-ext-install pdo_pgsql; \
    if [ "$INSTALL_XDEBUG" = "true" ] || [ "$APP_ENV" = "local" ]; then \
        pecl install xdebug; \
        docker-php-ext-enable xdebug; \
        { \
            echo "xdebug.mode=off"; \
            echo "xdebug.start_with_request=yes"; \
            echo "xdebug.client_host=host.docker.internal"; \
            echo "xdebug.client_port=9003"; \
        } > /usr/local/etc/php/conf.d/xdebug-dev.ini; \
    fi; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2.9.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000

# Default dev command (migrate/start handled via docker-compose command override)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
