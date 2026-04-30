# CareerHub - single-image deploy for Render's free tier.
# Builds PHP deps + frontend assets, ships a self-contained SQLite app.
FROM php:8.2-cli

# --- System dependencies -------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libsqlite3-dev \
        curl ca-certificates gnupg \
    && docker-php-ext-install pdo pdo_sqlite zip intl \
    && rm -rf /var/lib/apt/lists/*

# --- Node 22 (for building Vite assets) ----------------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

# --- Composer ------------------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP deps first (better layer caching).
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Install JS deps.
COPY package.json package-lock.json ./
RUN npm ci

# Copy the rest of the application.
COPY . .

# Finalize PHP autoloader + build front-end assets.
RUN composer dump-autoload --optimize --no-dev \
    && npm run build

# Ensure storage/cache are writable and the SQLite file exists.
RUN mkdir -p storage/framework/{cache,sessions,views} database \
    && touch database/database.sqlite \
    && chmod -R 775 storage bootstrap/cache

# Render injects $PORT at runtime.
ENV PORT=8080
EXPOSE 8080

# Entrypoint: prepare env, run migrations + seed, then serve.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
