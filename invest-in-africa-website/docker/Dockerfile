# The Invest In Africa Initiative — production image (§9)
# FrankenPHP = Caddy + PHP: automatic HTTPS (Let's Encrypt), HTTP/2-3, Brotli / Zstd / Gzip.

# 1. Front-end assets
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci --no-audit --no-fund
COPY resources ./resources
COPY public ./public
RUN npm run build

# 2. PHP dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs

# 3. Runtime
FROM dunglas/frankenphp:1-php8.4-alpine

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    SERVER_ROOT=/app/public

# PHP extensions compiled from the bundled PHP sources (no PECL download).
# GD with JPEG, WebP and AVIF for automatic image conversion (§10.4).
RUN apk add --no-cache icu-libs libzip libpng libjpeg-turbo libwebp libavif freetype mysql-client tzdata \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libpng-dev libjpeg-turbo-dev libwebp-dev libavif-dev freetype-dev linux-headers \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-avif --with-freetype \
    && docker-php-ext-install -j"$(nproc)" intl gd pdo_mysql zip pcntl exif bcmath \
    && apk del .build-deps \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini $PHP_INI_DIR/conf.d/zz-site.ini
COPY docker/Caddyfile /etc/frankenphp/Caddyfile

WORKDIR /app
COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && APP_KEY=base64:$(head -c 32 /dev/urandom | base64) php artisan package:discover --ansi \
    && APP_KEY=base64:$(head -c 32 /dev/urandom | base64) php artisan filament:assets --ansi \
    && rm -f /usr/local/bin/composer \
    && rm -rf tests \
    && mkdir -p storage/app/private storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/backups bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/site-entrypoint
RUN chmod +x /usr/local/bin/site-entrypoint

ENTRYPOINT ["site-entrypoint"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
