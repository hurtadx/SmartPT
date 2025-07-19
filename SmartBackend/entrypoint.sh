#!/bin/bash

# Esperar a que PostgreSQL esté disponible
echo "Esperando PostgreSQL..."
while ! nc -z postgres 5432; do
  sleep 1
done
echo "PostgreSQL está listo!"

# Copiar archivo de entorno para Docker
if [ ! -f .env ]; then
    cp .env.docker .env
fi

# Generar clave de aplicación si no existe
php artisan key:generate --force

# Ejecutar migraciones
php artisan migrate --force

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Apache
apache2-foreground
