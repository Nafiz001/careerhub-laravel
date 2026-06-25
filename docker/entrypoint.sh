#!/usr/bin/env bash
set -e

cd /app

# Generate an app key if one was not supplied via the environment.
# We export it directly rather than letting `artisan key:generate`
# rewrite /app/.env, because the image ships no .env file (and any
# change would be lost on the next deploy anyway).
if [ -z "${APP_KEY}" ]; then
    APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
    export APP_KEY
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
