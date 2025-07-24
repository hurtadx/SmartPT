#!/bin/bash
set -e

echo " Iniciando SmartPT Backend..."

# Verificar que las dependencias de Composer estén instaladas
echo "Verificando dependencias de Composer..."
if [ ! -f "/var/www/html/vendor/autoload.php" ]; then
    echo " Instalando dependencias de Composer..."
    composer install --no-dev --optimize-autoloader
fi

# Esperar a que PostgreSQL esté disponible
echo "Esperando PostgreSQL..."
while ! nc -z postgres 5432; do
  sleep 1
done
echo "PostgreSQL está listo!"


# Copiar .env si no existe
if [ ! -f .env ]; then
    if [ -f .env.docker ]; then
        echo "Configurando variables de entorno..."
        cp .env.docker .env
    else
        echo "No se encontró .env.docker, usando .env.example..."
        cp .env.example .env
    fi
fi

# Generar clave de aplicación si no existe o está vacía
if ! grep -q "^APP_KEY=" .env || grep -q "^APP_KEY=$" .env; then
    echo " Generando clave de aplicación..."
    php artisan key:generate --force
fi

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders para crear usuarios de prueba
echo "Creando usuarios de prueba..."
php artisan db:seed --force

# Limpiar cache antes de optimizar
echo "Limpiando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
echo "Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "SmartPT Backend listo para evaluadores!"
echo "Usuarios disponibles:"
echo "   - admin@smartpt.com / password123"
echo "   - user@smartpt.com / password123" 
echo "   - evaluador@smartpt.com / password123"

# Iniciar Apache
echo "Iniciando servidor web..."
apache2-foreground
