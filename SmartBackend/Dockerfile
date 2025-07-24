# Multi-stage build para optimizar velocidad
FROM composer:latest AS dependencies
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-autoloader

# Imagen principal
FROM php:8.2-apache
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libpq-dev netcat-traditional \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


# Copiar solo archivos esenciales antes de instalar dependencias
COPY composer.json composer.lock ./
# Instalar dependencias de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copiar el resto del código fuente (excluyendo lo ignorado por .dockerignore)
COPY . .

# Finalizar instalación de Composer
RUN composer dump-autoload --optimize

# Configurar permisos solo en carpetas necesarias
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 755 storage \
    && chmod -R 755 bootstrap/cache

# Configurar Apache
RUN a2enmod rewrite
COPY .docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Script de inicio
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer puerto 80
EXPOSE 80

# Comando para iniciar Apache
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
