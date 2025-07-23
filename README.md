# SmartPT

Sistema de gestión de encuestas desarrollado con Laravel y React.

## Descripción

SmartPT es una aplicación web que permite a los usuarios registrarse, autenticarse y completar encuestas de desarrollo. El sistema incluye funcionalidades de autenticación, gestión de encuestas y visualización de resultados.

## Estructura del Proyecto

```
SmartPT/
├── SmartBackend/    # API REST desarrollada en Laravel
└── SmartFrontend/   # Aplicación web desarrollada en React
```

## Tecnologías Utilizadas

### Backend
- Laravel 12.0
- PHP 8.2
- Laravel Sanctum para autenticación
- PostgreSQL 15 como base de datos
- Docker para contenedorización

### Frontend
- React 19.1.0
- Vite 7.5.6 como build tool
- React Router DOM 7.7.0 para navegación
- Axios 1.10.0 para peticiones HTTP
- Font Awesome 7.0.0 para iconografía

## Funcionalidades

- Sistema de autenticación con registro y login
- Formularios de encuesta con diferentes tipos de preguntas
- Protección de rutas basada en autenticación
- Prevención de envíos duplicados de encuestas
- Visualización de resultados de encuestas
- Interfaz responsiva con diseño moderno

## Configuración del Entorno

### Requisitos Previos
- Docker y Docker Compose
- Git

### Instalación con Docker (Recomendado)

1. Clonar el repositorio:
```bash
git clone https://github.com/hurtadx/SmartPT.git
cd SmartPT
```

2. Configurar variables de entorno del backend:
```bash
cd SmartBackend
cp .env.example .env
```

3. Configurar variables de entorno del frontend:
```bash
cd ../SmartFrontend
cp .env.example .env
```

4. Levantar los servicios con Docker:
```bash
cd ..
docker-compose up -d
```

5. Ejecutar migraciones de la base de datos:
```bash
docker-compose exec backend php artisan migrate
```

6. Generar clave de la aplicación Laravel:
```bash
docker-compose exec backend php artisan key:generate
```

### Instalación Local

#### Backend (Laravel)

1. Navegar al directorio del backend:
```bash
cd SmartBackend
```

2. Instalar dependencias:
```bash
composer install
```

3. Configurar el archivo de entorno:
```bash
cp .env.example .env
```

4. Generar la clave de la aplicación:
```bash
php artisan key:generate
```

5. Configurar la base de datos en `.env`:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_pt
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

6. Ejecutar migraciones:
```bash
php artisan migrate
```

7. Iniciar el servidor:
```bash
php artisan serve
```

#### Frontend (React)

1. Navegar al directorio del frontend:
```bash
cd SmartFrontend
```

2. Instalar dependencias:
```bash
npm install
```

3. Configurar variables de entorno:
```bash
cp .env.example .env
```

4. Iniciar el servidor de desarrollo:
```bash
npm run dev
```

## Variables de Entorno

### Backend (.env)

```
APP_NAME=SmartPT
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=smart_pt
DB_USERNAME=postgres
DB_PASSWORD=

SANCTUM_STATEFUL_DOMAINS=localhost:5173
```

### Frontend (.env)

```
VITE_API_URL=http://localhost:8000/api
```

## Uso de la Aplicación

1. Acceder a la aplicación en: http://localhost:5173
2. Registrarse con nombre, email y contraseña
3. Iniciar sesión con las credenciales creadas
4. Completar la encuesta de desarrollo
5. Visualizar los resultados de la encuesta

## API Endpoints

### Autenticación
- `POST /api/register` - Registro de usuario
- `POST /api/login` - Inicio de sesión
- `POST /api/logout` - Cerrar sesión
- `GET /api/me` - Información del usuario autenticado

### Encuestas
- `GET /api/survey/status` - Verificar estado de la encuesta
- `POST /api/survey/submit` - Enviar respuestas de la encuesta
- `GET /api/survey/results` - Obtener resultados de la encuesta

## Estructura de la Base de Datos

### Tablas
- `users` - Información de usuarios registrados
- `survey_responses` - Respuestas de las encuestas

## Testing

### Backend
```bash
cd SmartBackend
php artisan test
```

### Variables de Entorno

Cada componente incluye archivos `.env.example` con las variables necesarias.

## Licencia

Este proyecto es un desarrollo para fines de evaluación técnica. Andres Hurtado Molina
