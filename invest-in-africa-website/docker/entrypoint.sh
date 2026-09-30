#!/bin/sh
# Container start-up: no manual step is required.
set -e
cd /app

# 1. Application key — generated once and kept in the persistent storage volume.
KEY_FILE=storage/app/private/.app_key
if [ -z "$APP_KEY" ]; then
    if [ ! -s "$KEY_FILE" ]; then
        mkdir -p storage/app/private
        php -r 'echo "base64:".base64_encode(random_bytes(32));' > "$KEY_FILE"
        chmod 600 "$KEY_FILE"
    fi
    export APP_KEY="$(cat "$KEY_FILE")"
fi

# 2. Storage folders (fresh volumes)
mkdir -p storage/app/private storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/backups
chown -R www-data:www-data storage 2>/dev/null || true

# 3. Wait for the database
if [ "$DB_CONNECTION" = "mysql" ] || [ "$DB_CONNECTION" = "mariadb" ]; then
    echo "Waiting for the database at $DB_HOST…"
    i=0
    until php -r 'try { new PDO("mysql:host=".getenv("DB_HOST").";port=".(getenv("DB_PORT") ?: 3306), getenv("DB_USERNAME"), getenv("DB_PASSWORD")); } catch (Throwable $e) { exit(1); }'; do
        i=$((i+1)); [ $i -gt 60 ] && echo "Database unreachable" && exit 1
        sleep 2
    done
fi

# 4. Install / update (web container only): migrations, reference data,
#    first Super Admin, editable texts, image variants, caches.
if [ "${CONTAINER_ROLE:-app}" = "app" ]; then
    php artisan site:install --force --no-interaction
else
    # Worker and scheduler wait for the web container to finish migrations.
    sleep 20
    php artisan optimize >/dev/null 2>&1 || true
fi

exec "$@"
