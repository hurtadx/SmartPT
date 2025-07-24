# SmartPT Backend

API REST desarrollada en Laravel para el sistema de gestión de encuestas SmartPT.

## Descripción

Backend que proporciona servicios de autenticación, gestión de usuarios y manejo de encuestas. Utiliza Laravel Sanctum para autenticación basada en tokens y PostgreSQL como base de datos.

## Tecnologías

- PHP 8.2
- Laravel 12.0
- Laravel Sanctum 4.0
- PostgreSQL 15
- Docker

## Estructura del Proyecto

```
SmartBackend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   └── SurveyController.php
│   │   └── Requests/
│   │       ├── LoginRequest.php
│   │       ├── RegisterRequest.php
│   │       └── SurveyRequest.php
│   └── Models/
│       ├── User.php
│       └── SurveyResponse.php
├── database/
│   └── migrations/
└── routes/
    └── api.php
```

## Instalación

### Con Docker (Recomendado)

1. Desde el directorio raíz del proyecto:
```bash
docker-compose up -d
```

2. Ejecutar migraciones:
```bash
docker-compose exec backend php artisan migrate
```

### Instalación Local

1. Clonar el repositorio:
```bash
git clone [URL_DEL_REPOSITORIO]
cd SmartPT-Backend
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

5. Configurar la base de datos en el archivo `.env`:
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

## Configuración

### Variables de Entorno

El archivo `.env.example` incluye todas las variables necesarias:

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

## API Endpoints

### Autenticación

#### POST /api/register
**Request Body:**
```json
{
    "name": "string",
    "email": "string",
    "password": "string",
    "password_confirmation": "string"
}
```

#### POST /api/login
**Request Body:**
```json
{
    "email": "string",
    "password": "string"
}
```

#### POST /api/logout
**Headers:** Authorization: Bearer {token}

#### GET /api/me
**Headers:** Authorization: Bearer {token}

### Encuestas

#### GET /api/survey/status
Verificar estado de la encuesta del usuario.

#### POST /api/survey/submit
Enviar respuestas de la encuesta.

#### GET /api/survey/results
Obtener resultados de la encuesta.

## Base de Datos

### Migraciones
- `create_users_table`
- `create_survey_responses_table`

### Modelos
- **User**: Autenticación con Sanctum
- **SurveyResponse**: Respuestas de encuesta

## Testing

```bash
php artisan test
```

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
