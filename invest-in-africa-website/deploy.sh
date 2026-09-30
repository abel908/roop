#!/usr/bin/env bash
# Zero-downtime-friendly deployment script (§9.4) — run from the project root on the server.
# Usage: ./deploy.sh [git ref]   e.g. ./deploy.sh origin/main  or  ./deploy.sh v1.0.3 (rollback to a tag)
set -euo pipefail

REF="${1:-origin/main}"

php artisan down --render="errors::503" --retry=30 || true
trap 'php artisan up' EXIT

git fetch --prune --tags origin
git reset --hard "$REF"

composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
npm ci && npm run build

php artisan migrate --force
php artisan content:sync
php artisan storage:link || true
php artisan optimize
php artisan filament:optimize
php artisan queue:restart

echo "Deployment of $REF completed."
