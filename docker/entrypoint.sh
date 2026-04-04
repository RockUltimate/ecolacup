#!/bin/sh
set -e

cd /var/www/html
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ ! -f .env ]; then
  cp .env.example .env
fi

if ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

php artisan storage:link || true
php artisan migrate --force --graceful

exec "$@"
