#!/usr/bin/env sh
# One command to install, start or update the whole site in production.
#   ./start.sh invest-in-africa.org [contact@invest-in-africa.org]
# The first run generates every secret; later runs rebuild and update.
set -eu
cd "$(dirname "$0")"

DOMAIN="${1:-}"
ADMIN_EMAIL="${2:-}"
ENV_FILE=.env.docker

random() { head -c 48 /dev/urandom | base64 | tr -dc 'A-Za-z0-9' | head -c "${1:-32}"; }

if [ ! -f "$ENV_FILE" ]; then
    [ -n "$DOMAIN" ] || { echo "Usage: ./start.sh your-domain.org [admin-email]"; exit 1; }
    DB_PASSWORD="$(random 32)"
    cat > "$ENV_FILE" <<CONF
APP_NAME="The Invest In Africa Initiative"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://$DOMAIN
APP_KEY=base64:$(head -c 32 /dev/urandom | base64)
SERVER_NAME=$DOMAIN
SITE_DEFAULT_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=invest_in_africa
DB_USERNAME=invest_in_africa
DB_PASSWORD=$DB_PASSWORD
MYSQL_DATABASE=invest_in_africa
MYSQL_USER=invest_in_africa
MYSQL_PASSWORD=$DB_PASSWORD

REDIS_HOST=redis
REDIS_CLIENT=predis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
CLAMD_HOST=clamav
SEED_DEMO=false
LOG_CHANNEL=stderr

ADMIN_EMAIL=${ADMIN_EMAIL:-admin@$DOMAIN}
BACKUP_PASSWORD=$(random 40)

# Transactional emails — fill in the SMTP account of your sending service
MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=no-reply@$DOMAIN
MAIL_FROM_NAME="The Invest In Africa Initiative"
MAIL_TEAM_ADDRESS=${ADMIN_EMAIL:-contact@$DOMAIN}

GA4_MEASUREMENT_ID=
INDEXNOW_KEY=$(random 32)
ANTHROPIC_API_KEY=
CONF
    chmod 600 "$ENV_FILE"
    echo "Secrets generated in $ENV_FILE (keep a copy: it holds the backup password)."
fi

docker compose build
docker compose up -d --remove-orphans

echo "Waiting for the first start (migrations, first Super Admin)…"
i=0
until docker compose exec -T app test -f storage/app/private/first-admin.txt 2>/dev/null || [ $i -ge 90 ]; do i=$((i+1)); sleep 2; done
docker compose exec -T app cat storage/app/private/first-admin.txt 2>/dev/null || true
echo "Site: $(grep '^APP_URL=' "$ENV_FILE" | cut -d= -f2)"
