FROM php:8.1-fpm-alpine

RUN apk add --no-cache git unzip netcat-openbsd

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --optimize

# Copia el script de inicio
COPY docker/php/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

EXPOSE 9000

CMD ["/usr/local/bin/start.sh"]
