# Production Dockerfile for Render.
# Differences from Dockerfile.dev:
#   - No volume mounts expected. Code is baked into the image.
#   - Uses $PORT (Render injects this). Dev hardcodes 8000 for docker-compose.
#   - Order optimized so composer install is cached when only app code changes.

FROM php:8.2-cli

# PDO MySQL extension — required for the Database class.
RUN docker-php-ext-install pdo pdo_mysql

# Grab composer binary from its official image. Avoids installing PHP + curl + verifying checksums ourselves.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy dependency manifests first so composer install layer is cached
# as long as composer.json / composer.lock don't change.
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Now copy the rest of the app.
COPY . /app

# Regenerate autoload after all source files are present (no-dev stays no-dev).
RUN composer dump-autoload --optimize --no-dev

# Render injects PORT as env var. Fall back to 8000 for local runs of this image.
# Shell form (not exec form) so $PORT expands at container start, not build time.
CMD php -S 0.0.0.0:${PORT:-8000} -t public
