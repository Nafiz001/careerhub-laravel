#!/usr/bin/env bash
set -e

cd /app

# Generate an app key if one was not supplied via the environment.
if [ -z "${APP_KEY}" ]; then
    php artisan key:generate --force
fi

# Cache config/routes/views for production performance.
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ensure the SQLite database file exists, then migrate + seed.
# NOTE: SQLite ships inside the image, so data RESETS on every redeploy.
# That is acceptable for a public demo.
touch database/database.sqlite
php artisan migrate --force --seed

# Boot Laravel's built-in server on the port Render provides.
php artisan serve --host 0.0.0.0 --port "${PORT:-8080}"
