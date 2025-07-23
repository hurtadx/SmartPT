# Prueba tecnica Smart s.


# Construir y levantar todos los servicios
docker-compose up --build

# Para ejecutar en segundo plano
docker-compose up -d --build

# Para ver los logs
docker-compose logs -f

# Para detener los servicios
docker-compose down

# Para ejecutar migraciones
docker-compose exec backend php artisan migrate

# Para generar la clave de la aplicación
docker-compose exec backend php artisan key:generate

Backend (Laravel): http://localhost:8000
Frontend (React): http://localhost:3000
PostgreSQL: localhost:5432
