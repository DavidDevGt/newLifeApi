#!/bin/sh
echo "Esperando a que MySQL esté disponible en db:3306..."
while ! nc -z db 3306; do
  sleep 1
done
echo "MySQL está disponible, iniciando PHP-FPM..."
exec php-fpm
