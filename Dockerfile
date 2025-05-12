FROM php:8.4-fpm-alpine

LABEL authors="ilyag"

WORKDIR /workout-assistant

# busybox (cp, mv, rm и т.д.)
RUN apk update && apk add --no-cache postgresql-dev postgresql-client busybox \
    && docker-php-ext-install pdo_pgsql

# official image of composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY . /workout-assistant

RUN composer install --optimize-autoloader --no-dev

RUN mkdir -p /workout-assistant/storage/logs

# change owner and mode
RUN chown -R www-data:www-data /workout-assistant/storage /workout-assistant/bootstrap/cache \
    && chmod -R 775 /workout-assistant/storage /workout-assistant/bootstrap/cache

RUN php artisan config:cache \
   && php artisan route:cache

COPY startapp.sh /usr/local/bin/startapp.sh
RUN chmod +x /usr/local/bin/startapp.sh

CMD ["/usr/local/bin/startapp.sh"]

EXPOSE 9000
