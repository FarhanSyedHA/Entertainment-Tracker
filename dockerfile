FROM php:8.2-cli
RUN docker-php-ext-install pdo pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts
COPY . .
RUN composer dump-autoload --optimize
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
